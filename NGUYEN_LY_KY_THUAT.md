# Nguyên Lý và Kiến Thức Kỹ Thuật - SQL Injection Demo

## 1. Tổng quan về SQL Injection

SQL Injection là một lỗ hổng bảo mật xảy ra khi ứng dụng web không kiểm tra và xử lý đúng cách dữ liệu đầu vào từ người dùng trước khi đưa vào câu truy vấn SQL. Kẻ tấn công có thể chèn các câu lệnh SQL độc hại vào input, khiến ứng dụng thực thi các câu lệnh này trên database.

## 2. Nguyên nhân gây ra SQL Injection trong demo

### 2.1 String Concatenation (Nối chuỗi trực tiếp)

Trong demo, ứng dụng sử dụng cách nối chuỗi trực tiếp để tạo câu SQL:

```php
$sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
```

Vấn đề ở đây là biến `$username` và `$password` được ghép trực tiếp vào câu SQL mà không qua bất kỳ kiểm tra hay mã hóa nào. Nếu người dùng nhập các ký tự đặc biệt như dấu nháy đơn (`'`), dấu chấm phẩy (`;`), hoặc các từ khóa SQL như `OR`, `UNION`, thì những ký tự này sẽ được thực thi như một phần của câu SQL.

### 2.2 Thiếu Input Validation

Ứng dụng không kiểm tra:
- Kiểu dữ liệu đầu vào
- Độ dài của input
- Sự tồn tại của ký tự đặc biệt
- Định dạng của input (ví dụ: username chỉ nên chứa chữ và số)

### 2.3 Thiếu Output Encoding

Ứng dụng không mã hóa dữ liệu đầu vào trước khi đưa vào câu SQL. Trong PHP, có thể sử dụng các hàm như `mysqli_real_escape_string()` hoặc tốt hơn là sử dụng Prepared Statements.

## 3. Các kỹ thuật SQL Injection được demo

### 3.1 Comment-based SQL Injection

**Payload:** `admin' #`

**Cách hoạt động:**

Câu SQL ban đầu:
```sql
SELECT * FROM users WHERE username='$username' AND password='$password'
```

Sau khi nhập payload:
```sql
SELECT * FROM users WHERE username='admin' #' AND password=''
```

Trong MySQL, dấu `#` được sử dụng để comment (ghi chú) toàn bộ phần sau nó cho đến cuối dòng. Do đó, phần `' AND password=''` bị comment và không được thực thi. Câu SQL chỉ còn kiểm tra username, và vì username 'admin' tồn tại trong database, nên đăng nhập thành công.

**Lưu ý:** Trong MySQL, có thể sử dụng `-- ` (dấu gạch ngang kép với khoảng trắng) thay cho `#` để comment, nhưng `#` đơn giản hơn và không cần khoảng trắng.

### 3.2 OR-based SQL Injection

**Payload:** `' OR '1'='1`

**Cách hoạt động:**

Câu SQL ban đầu:
```sql
SELECT * FROM users WHERE username='$username' AND password='$password'
```

Sau khi nhập payload vào cả hai trường:
```sql
SELECT * FROM users WHERE username='' OR '1'='1' AND password='' OR '1'='1'
```

Điều kiện `'1'='1'` luôn luôn đúng trong SQL. Do đó:
- `username='' OR '1'='1'` luôn trả về TRUE
- `password='' OR '1'='1'` luôn trả về TRUE

Kết quả là câu truy vấn sẽ trả về tất cả các bản ghi trong bảng users. Ứng dụng thường lấy bản ghi đầu tiên và cho phép đăng nhập.

**Lưu ý về thứ tự ưu tiên toán tử:** Trong SQL, toán tử `AND` có độ ưu tiên cao hơn `OR`. Do đó, câu SQL trên được hiểu là:
```sql
username='' OR ('1'='1' AND password='') OR '1'='1'
```

Tuy nhiên, vì `'1'='1'` luôn đúng, nên toàn bộ điều kiện WHERE sẽ luôn đúng.

### 3.3 UNION-based SQL Injection

**Payload:** `' UNION SELECT 1, database(), 3, 4 #`

**Cách hoạt động:**

Câu SQL ban đầu:
```sql
SELECT * FROM products WHERE name LIKE '%$keyword%'
```

Sau khi nhập payload:
```sql
SELECT * FROM products WHERE name LIKE '%' UNION SELECT 1, database(), 3, 4 #%'
```

Toán tử `UNION` trong SQL cho phép kết hợp kết quả từ nhiều câu SELECT. Để UNION hoạt động, số cột và kiểu dữ liệu của các câu SELECT phải khớp nhau.

Trong trường hợp này:
- Câu SELECT đầu tiên: `SELECT * FROM products WHERE name LIKE '%'` - không trả về kết quả nào
- Câu SELECT thứ hai: `SELECT 1, database(), 3, 4` - trả về một hàng với giá trị `1`, tên database, `3`, `4`

Hàm `database()` trong MySQL trả về tên của database hiện tại. Kết quả UNION sẽ hiển thị tên database trong bảng kết quả.

**Yêu cầu để UNION hoạt động:**
- Số cột của cả hai câu SELECT phải bằng nhau
- Kiểu dữ liệu của các cột tương ứng phải tương thích
- Cần biết số cột của bảng gốc (có thể dùng `ORDER BY` để tìm)

## 4. Cấu trúc của ứng dụng demo

### 4.1 Kiến trúc Docker

Demo sử dụng Docker Compose để chạy hai container:
- **Container web:** Chạy PHP 8.1 với Apache, phục vụ các file PHP
- **Container db:** Chạy MySQL 8.0, lưu trữ dữ liệu

Hai container giao tiếp với nhau qua Docker network. Container web kết nối đến container db qua hostname `db`.

### 4.2 Database Schema

**Bảng users:**
- `id`: INT AUTO_INCREMENT PRIMARY KEY
- `username`: VARCHAR(50) NOT NULL UNIQUE
- `password`: VARCHAR(100) NOT NULL
- `email`: VARCHAR(100)
- `role`: VARCHAR(20) DEFAULT 'user'
- `created_at`: TIMESTAMP

**Bảng products:**
- `id`: INT AUTO_INCREMENT PRIMARY KEY
- `name`: VARCHAR(100) NOT NULL
- `price`: DECIMAL(10, 2)
- `description`: TEXT

### 4.3 Code PHP

**Kết nối database:**
```php
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
```

Sử dụng MySQLi extension của PHP để kết nối đến MySQL database.

**Thực thi query:**
```php
$result = $conn->query($sql);
```

Hàm `query()` thực thi câu SQL và trả về kết quả. Nếu query thành công, `$result` sẽ chứa kết quả. Nếu có lỗi, `$result` sẽ là FALSE và có thể lấy thông báo lỗi qua `$conn->error`.

## 5. Tại sao các payload hoạt động

### 5.1 Vấn đề với String Interpolation

Khi PHP xử lý chuỗi trong dấu nháy kép (`"`), các biến bên trong sẽ được thay thế bằng giá trị của chúng. Ví dụ:

```php
$username = "admin' #";
$sql = "SELECT * FROM users WHERE username='$username'";
// Kết quả: SELECT * FROM users WHERE username='admin' #'
```

PHP không kiểm tra xem giá trị của biến có chứa ký tự đặc biệt SQL hay không. Nó chỉ đơn giản thay thế biến bằng giá trị của nó.

### 5.2 SQL Parser

Khi MySQL nhận được câu SQL, nó sẽ parse (phân tích cú pháp) câu SQL để hiểu cần thực thi gì. Trong quá trình parse, MySQL sẽ:
1. Nhận diện các từ khóa SQL (SELECT, FROM, WHERE, etc.)
2. Nhận diện các toán tử (AND, OR, UNION, etc.)
3. Nhận diện các giá trị (chuỗi trong dấu nháy đơn, số, etc.)
4. Nhận diện các comment (--, #)

Nếu kẻ tấn công chèn các ký tự đặc biệt vào input, MySQL parser sẽ nhận diện chúng như một phần của câu SQL, không phải như dữ liệu thông thường.

### 5.3 Logic của câu SQL

Câu SQL ban đầu có logic:
```
IF username matches AND password matches THEN return user
```

Sau khi bị SQL Injection, logic có thể trở thành:
```
IF (username matches OR always true) AND (password matches OR always true) THEN return user
```

Vì "always true" luôn đúng, nên điều kiện tổng thể luôn đúng, dẫn đến trả về tất cả users.

## 6. Hậu quả của SQL Injection

### 6.1 Bypass Authentication

Như đã demo, SQL Injection cho phép kẻ tấn công đăng nhập mà không cần biết mật khẩu. Điều này có thể dẫn đến:
- Truy cập trái phép vào tài khoản người dùng
- Truy cập vào tài khoản admin với quyền cao
- Đánh cắp thông tin cá nhân của người dùng

### 6.2 Data Disclosure (Tiết lộ dữ liệu)

SQL Injection cho phép kẻ tấn công đọc dữ liệu từ database:
- Đọc dữ liệu từ các bảng không được phép truy cập
- Đọc thông tin nhạy cảm như mật khẩu (dù đã hash), email, số thẻ tín dụng
- Khám phá cấu trúc database (tên bảng, tên cột)

### 6.3 Data Manipulation (Thao tác dữ liệu)

Nếu ứng dụng có quyền ghi, kẻ tấn công có thể:
- Sửa đổi dữ liệu (UPDATE)
- Xóa dữ liệu (DELETE)
- Thêm dữ liệu giả mạo (INSERT)

### 6.4 Database Schema Manipulation

Nếu ứng dụng có quyền DDL (Data Definition Language), kẻ tấn công có thể:
- Tạo bảng mới
- Xóa bảng
- Sửa đổi cấu trúc bảng

### 6.5 Command Execution

Trong một số trường hợp đặc biệt, SQL Injection có thể dẫn đến thực thi lệnh trên hệ điều hành:
- MySQL: Sử dụng `INTO OUTFILE` để ghi file, sau đó thực thi file
- MS SQL Server: Sử dụng `xp_cmdshell` để thực thi lệnh hệ thống

Tuy nhiên, điều này yêu cầu quyền đặc biệt và thường bị tắt trong môi trường production.

## 7. Các loại SQL Injection

### 7.1 In-band SQL Injection (Classic SQLi)

Kẻ tấn công sử dụng cùng một kênh giao tiếp (HTTP response) để tấn công và nhận kết quả. Đây là loại phổ biến nhất.

**Demo này thuộc loại In-band SQL Injection**, cụ thể bao gồm cả hai kỹ thuật sau:

**Error-based SQL Injection:**
- Ứng dụng hiển thị thông báo lỗi SQL chi tiết
- Kẻ tấn công có thể khai thác thông tin từ thông báo lỗi
- Trong demo: Ứng dụng hiển thị câu SQL được tạo ra và thông báo lỗi SQL (nếu có) trực tiếp trên trang web
- Ví dụ: Khi nhập payload sai, ứng dụng hiển thị lỗi SQL syntax, từ đó kẻ tấn công có thể biết cấu trúc database

**UNION-based SQL Injection:**
- Sử dụng toán tử UNION để kết hợp kết quả từ nhiều câu SELECT
- Cho phép đọc dữ liệu từ các bảng khác hoặc các cột khác
- Trong demo: Phần search sử dụng UNION để lấy tên database (`' UNION SELECT 1, database(), 3, 4 #`)
- Kết quả được hiển thị trực tiếp trên trang web, không cần suy luận

**Đặc điểm của In-band SQL Injection trong demo:**
- Kết quả được hiển thị trực tiếp trên trang web (đăng nhập thành công, danh sách sản phẩm, tên database)
- Câu SQL được hiển thị để minh họa (trong thực tế, ứng dụng không nên hiển thị điều này)
- Lỗi SQL (nếu có) được hiển thị chi tiết
- Không cần suy luận từ phản hồi gián tiếp như Blind SQL Injection

### 7.2 Blind SQL Injection (Inferential SQLi)

Ứng dụng không hiển thị lỗi hoặc dữ liệu trực tiếp. Kẻ tấn công phải suy luận thông tin dựa trên phản hồi gián tiếp.

**Boolean-based Blind SQLi:**
- Quan sát sự khác biệt trong phản hồi khi điều kiện đúng/sai
- Ví dụ: Nếu điều kiện đúng, trang hiển thị "User exists", nếu sai thì "User not found"

**Time-based Blind SQLi:**
- Sử dụng hàm làm trễ thời gian (như `SLEEP()` trong MySQL)
- Quan sát độ trễ trong phản hồi để suy luận kết quả
- Ví dụ: `' AND SLEEP(5) --` sẽ làm trễ 5 giây nếu điều kiện đúng

### 7.3 Out-of-band SQL Injection

Kẻ tấn công sử dụng kênh khác để nhận dữ liệu (DNS, HTTP request). Loại này ít phổ biến và yêu cầu tính năng đặc biệt trên database server.

## 8. Cách phòng chống SQL Injection

### 8.1 Prepared Statements (Parameterized Queries)

Đây là biện pháp phòng chống hiệu quả nhất. Thay vì nối chuỗi, sử dụng placeholder:

**Code không an toàn (như trong demo):**
```php
$sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
$result = $conn->query($sql);
```

**Code an toàn với Prepared Statements:**
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();
```

Với Prepared Statements:
- Câu SQL được compile trước, cấu trúc câu SQL không thể thay đổi
- Dữ liệu được truyền vào như tham số, không phải như một phần của câu SQL
- Database tự động escape các ký tự đặc biệt trong dữ liệu

### 8.2 Input Validation

Kiểm tra và lọc dữ liệu đầu vào:
- Kiểm tra kiểu dữ liệu (số, chuỗi, email, etc.)
- Kiểm tra độ dài
- Sử dụng whitelist (chỉ cho phép các ký tự cụ thể) thay vì blacklist
- Validate định dạng (ví dụ: username chỉ chứa chữ và số)

### 8.3 Least Privilege Principle

Ứng dụng chỉ nên có quyền tối thiểu cần thiết trên database:
- Chỉ có quyền SELECT, INSERT, UPDATE trên các bảng cần thiết
- Không có quyền DROP, DELETE (trừ khi thực sự cần)
- Không sử dụng tài khoản root hoặc admin

### 8.4 Error Handling

Không hiển thị thông báo lỗi SQL chi tiết cho người dùng:
- Log lỗi vào file nội bộ
- Hiển thị thông báo chung chung cho người dùng
- Tắt display_errors trong production

### 8.5 Web Application Firewall (WAF)

WAF có thể chặn các request chứa payload SQL Injection phổ biến. Tuy nhiên, WAF không phải giải pháp hoàn hảo và có thể bị bypass.

## 9. Kết luận

SQL Injection là một lỗ hổng nghiêm trọng nhưng hoàn toàn có thể phòng chống được. Nguyên nhân chính là do lập trình viên không xử lý đúng cách dữ liệu đầu vào. Biện pháp phòng chống hiệu quả nhất là sử dụng Prepared Statements, kết hợp với input validation và nguyên tắc least privilege.

Demo này minh họa cách SQL Injection hoạt động trong môi trường thực tế, giúp hiểu rõ hơn về mức độ nguy hiểm và tầm quan trọng của việc viết code an toàn.

