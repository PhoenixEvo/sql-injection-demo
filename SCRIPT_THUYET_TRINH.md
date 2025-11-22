# Script Thuyết Trình - Demo SQL Injection

## Slide 7: Demo - Dò tìm lỗ hổng SQL Injection
      
**Lời dẫn:**

Tiếp theo, chúng em xin minh họa demo tấn công SQL Injection thực tế. Đầu tiên, cần nhấn mạnh việc demo chỉ thực hiện trong môi trường lab an toàn - ở đây nhóm đã setup một ứng dụng web đơn giản chạy trên Docker để thực hành. Chúng ta tuyệt đối không thử các kỹ thuật này trên website thật.

Bây giờ, để tìm lỗ hổng SQLi, chúng ta sẽ thử tấn công thủ công trên giao diện đăng nhập. Đây là cách đơn giản nhất để minh họa SQL Injection.

**[Mở trình duyệt, truy cập http://localhost:8080]**

Đây là trang đăng nhập của ứng dụng demo. Ứng dụng này được thiết kế có lỗ hổng SQL Injection để chúng ta học tập.

**[Hiển thị code PHP trên slide hoặc màn hình]**

Như các bạn thấy trong code, ứng dụng sử dụng string concatenation trực tiếp để tạo câu SQL:

```php
$sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
```

Đây chính là nguyên nhân gây ra lỗ hổng. Dữ liệu từ người dùng được ghép trực tiếp vào câu SQL mà không qua bất kỳ kiểm tra nào.

Bây giờ, chúng ta sẽ thử đăng nhập bình thường trước:

**[Nhập username: admin, password: admin123]**

Đăng nhập thành công. Đây là cách hoạt động bình thường của ứng dụng.

Bây giờ, chúng ta sẽ thử tấn công SQL Injection. Có hai cách phổ biến:

**Cách 1: Sử dụng comment để bỏ qua phần password**

**[Nhập username: admin' #, password: để trống]**

Như các bạn thấy, chúng ta đã đăng nhập thành công mà không cần biết password. Hãy xem câu SQL được tạo ra:

```sql
SELECT * FROM users WHERE username='admin' #' AND password=''
```

Dấu `#` trong MySQL sẽ comment toàn bộ phần sau nó, nên phần kiểm tra password bị bỏ qua. Ứng dụng chỉ kiểm tra username, và vì username 'admin' tồn tại trong database, nên đăng nhập thành công.

**Cách 2: Sử dụng điều kiện OR luôn đúng**

**[Nhập username: ' OR '1'='1, password: ' OR '1'='1]**

Câu SQL được tạo ra sẽ là:

```sql
SELECT * FROM users WHERE username='' OR '1'='1' AND password='' OR '1'='1'
```

Vì điều kiện `'1'='1'` luôn luôn đúng, câu truy vấn sẽ trả về tất cả các user trong bảng. Ứng dụng sẽ lấy user đầu tiên và cho phép đăng nhập.

**[Hiển thị kết quả đăng nhập thành công]**

Như các bạn thấy, cả hai cách đều cho phép chúng ta bypass cơ chế xác thực mà không cần biết mật khẩu thực sự.

**Phần bổ sung: Demo với công cụ tự động sqlmap**

Ngoài cách tấn công thủ công, kẻ tấn công còn có thể sử dụng các công cụ tự động như sqlmap để phát hiện và khai thác lỗ hổng SQL Injection một cách nhanh chóng.

**[Mở terminal, chạy sqlmap]**

sqlmap là công cụ mã nguồn mở tự động phát hiện và khai thác lỗ hổng SQL Injection. Chúng ta sẽ demo sqlmap trên trang tìm kiếm sản phẩm:

**[Chạy lệnh: sqlmap -u "http://localhost:8080/search.php?keyword=test" --batch]**

Như các bạn thấy, sqlmap tự động:
- Gửi nhiều payload khác nhau để test
- Phân tích phản hồi từ server
- Xác định loại database và phiên bản
- Phát hiện lỗ hổng SQL Injection

**[Chờ sqlmap hoàn thành, hiển thị kết quả phát hiện lỗ hổng]**

sqlmap đã phát hiện lỗ hổng SQL Injection và xác định đây là MySQL database.

Bây giờ, chúng ta sẽ dùng sqlmap để liệt kê các databases:

**[Chạy lệnh: sqlmap -u "http://localhost:8080/search.php?keyword=test" --dbs --batch]**

sqlmap sẽ tự động khai thác lỗ hổng để liệt kê tất cả databases trên server. Như các bạn thấy, nó đã tìm thấy database `sqli_demo` và các database hệ thống khác.

Tiếp theo, chúng ta sẽ dump dữ liệu từ bảng users:

**[Chạy lệnh: sqlmap -u "http://localhost:8080/search.php?keyword=test" -D sqli_demo -T users --dump --batch]**

sqlmap sẽ tự động:
- Liệt kê các cột trong bảng users
- Trích xuất toàn bộ dữ liệu từ bảng
- Hiển thị username, password hash, email, và role của tất cả users

**[Hiển thị kết quả dump]**

Như các bạn thấy, chỉ với một vài lệnh đơn giản, sqlmap đã có thể:
- Tự động phát hiện lỗ hổng SQL Injection
- Liệt kê cấu trúc database
- Trích xuất toàn bộ dữ liệu nhạy cảm

Điều này cho thấy mức độ nguy hiểm của SQL Injection - không chỉ hacker chuyên nghiệp mà cả những người không có kiến thức sâu về SQL cũng có thể sử dụng công cụ tự động để khai thác lỗ hổng.

---

## Slide 8: Demo - Khai thác dữ liệu qua SQL Injection

**Lời dẫn:**

Ngoài việc bypass đăng nhập, SQL Injection còn cho phép kẻ tấn công đọc dữ liệu từ database. Hãy xem trang tìm kiếm sản phẩm:

**[Chuyển sang trang search.php]**

Trang này cho phép tìm kiếm sản phẩm theo tên. Code sử dụng LIKE để tìm kiếm:

```php
$sql = "SELECT * FROM products WHERE name LIKE '%$keyword%'";
```

Tương tự như trang login, code này cũng dễ bị SQL Injection.

**Tìm kiếm bình thường:**

**[Nhập keyword: Laptop]**

Ứng dụng trả về sản phẩm có tên chứa "Laptop". Đây là hành vi bình thường.

**Tấn công SQL Injection:**

**[Nhập keyword: ' OR '1'='1]**

Câu SQL được tạo ra:

```sql
SELECT * FROM products WHERE name LIKE '%' OR '1'='1'%'
```

Vì điều kiện `'1'='1'` luôn đúng, câu truy vấn sẽ trả về TẤT CẢ các sản phẩm trong bảng, không chỉ những sản phẩm khớp với từ khóa tìm kiếm.

**[Hiển thị tất cả sản phẩm]**

Như các bạn thấy, chúng ta đã có thể xem tất cả dữ liệu trong bảng products mà không cần biết tên sản phẩm cụ thể.

**Khai thác thông tin database:**

**[Nhập keyword: ' UNION SELECT 1, database(), 3, 4 #]**

Câu SQL sẽ thành:

```sql
SELECT * FROM products WHERE name LIKE '%' UNION SELECT 1, database(), 3, 4 #%'
```

Toán tử UNION cho phép kết hợp kết quả từ nhiều câu SELECT. Ở đây, chúng ta UNION với một câu SELECT khác để lấy tên database. Hàm `database()` trả về tên database hiện tại.

**[Hiển thị kết quả có tên database: sqli_demo]**

Với kỹ thuật này, kẻ tấn công có thể:
- Liệt kê tên các bảng trong database
- Đọc dữ liệu từ các bảng khác
- Thậm chí có thể đọc thông tin nhạy cảm như username, password hash từ bảng users

Điều này cho thấy mức độ nguy hiểm của SQL Injection - không chỉ bypass đăng nhập mà còn có thể đánh cắp toàn bộ dữ liệu.

---

## Slide 9: Demo - Payload 'OR 1=1 (Bypass xác thực)

**Lời dẫn:**

Như đã trình bày ở slide trước, payload `' OR '1'='1` là một trong những payload SQL Injection kinh điển nhất. Hãy xem lại cách nó hoạt động:

**[Quay lại trang login]**

Khi chúng ta nhập `' OR '1'='1` vào cả hai trường username và password, câu SQL ban đầu:

```sql
SELECT * FROM users WHERE username='$username' AND password='$password'
```

Sẽ trở thành:

```sql
SELECT * FROM users WHERE username='' OR '1'='1' AND password='' OR '1'='1'
```

Do điều kiện `'1'='1'` luôn luôn đúng, câu truy vấn sẽ trả về tất cả các bản ghi trong bảng users. Ứng dụng sẽ lấy user đầu tiên và cho phép đăng nhập.

**[Thực hiện đăng nhập với payload]**

**[Hiển thị kết quả đăng nhập thành công]**

Như các bạn thấy, chúng ta đã đăng nhập thành công mà không cần biết username hay password thực sự.

Điều kiện luôn đúng như `OR 1=1` còn có thể được sử dụng ở nhiều nơi khác trong ứng dụng:

- Trang tìm kiếm: Chèn `OR 1=1` vào keyword sẽ trả về tất cả kết quả, bỏ qua mọi điều kiện lọc
- Trang lọc dữ liệu: Có thể xem dữ liệu mà đáng lẽ không được phép xem
- Trang quản trị: Có thể truy cập các chức năng admin mà không có quyền

Trên thực tế đã có nhiều trường hợp hacker dùng cách này. Ví dụ: vụ tấn công vào công ty an ninh mạng Bkav năm 2021 - hacker đã login vào hệ thống VPN của Bkav chỉ bằng cách thêm `'OR 1=1` trong câu SQL và vượt qua bước đăng nhập admin trong 5 phút.

Đây là minh chứng rõ ràng cho sức mạnh của một payload SQLi đơn giản. Chỉ với một vài ký tự đặc biệt, hacker có thể vượt qua toàn bộ cơ chế bảo mật của ứng dụng.

---

## Kết thúc phần Demo

**Lời dẫn:**

Qua phần demo vừa rồi, chúng ta đã thấy rõ cách SQL Injection hoạt động và mức độ nguy hiểm của nó. Chỉ với một vài ký tự đặc biệt, kẻ tấn công có thể:

1. Bypass cơ chế đăng nhập mà không cần mật khẩu
2. Đọc dữ liệu từ database mà không có quyền
3. Khai thác thông tin về cấu trúc database

Tất cả những điều này xảy ra chỉ vì ứng dụng không kiểm tra và xử lý đúng cách dữ liệu đầu vào từ người dùng.

Trong phần tiếp theo, chúng em sẽ trình bày các biện pháp phòng chống SQL Injection để ngăn chặn những cuộc tấn công như vậy.

---

## Ghi chú cho người thuyết trình:

1. **Chuẩn bị trước:**
   - Đảm bảo Docker containers đã chạy: `docker-compose up -d`
   - Mở trình duyệt và truy cập http://localhost:8080
   - Test lại các payload trước khi thuyết trình
   - Cài đặt sqlmap và test các lệnh trước (xem file HUONG_DAN_SQLMAP.md)
   - Chuẩn bị terminal để chạy sqlmap

2. **Khi trình bày:**
   - Giải thích từng bước một cách rõ ràng
   - Chỉ vào câu SQL được hiển thị trên màn hình
   - Nhấn mạnh tại sao payload hoạt động
   - So sánh giữa hành vi bình thường và hành vi sau khi bị tấn công
   - Khi demo sqlmap, giải thích rằng công cụ tự động làm những gì hacker phải làm thủ công

3. **Xử lý sự cố:**
   - Nếu có lỗi, giải thích đó là một phần của demo (error-based SQL injection)
   - Nếu demo không hoạt động, có thể giải thích bằng cách chỉ vào code và câu SQL
   - Nếu sqlmap chạy chậm, có thể chuẩn bị screenshot kết quả trước
   - Nếu không có sqlmap, có thể bỏ qua phần này và chỉ demo thủ công

4. **Thời gian:**
   - Slide 7 (phần thủ công): 2-3 phút
   - Slide 7 (phần sqlmap): 2-3 phút
   - Slide 8: 3-4 phút  
   - Slide 9: 2-3 phút
   - Tổng cộng: khoảng 12-15 phút cho phần demo

5. **Lưu ý về sqlmap:**
   - sqlmap có thể mất 30 giây đến vài phút để chạy xong một lệnh
   - Có thể chuẩn bị screenshot kết quả để tiết kiệm thời gian
   - Hoặc chạy sqlmap trước và giải thích kết quả khi trình bày
   - Nhấn mạnh rằng sqlmap tự động hóa quá trình mà hacker phải làm thủ công

