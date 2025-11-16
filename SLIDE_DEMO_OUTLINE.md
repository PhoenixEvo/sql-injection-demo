# Outline Slide - Phần Demo SQL Injection

## Slide 7: Demo - Dò tìm lỗ hổng SQL Injection

### Nội dung slide:

**Tiêu đề:** Demo - Tấn công SQL Injection

**Nội dung chính:**
- Môi trường demo: Ứng dụng web đơn giản chạy trên Docker
- Code dễ bị tấn công: String concatenation trực tiếp
- Demo đăng nhập bình thường
- Demo SQL Injection với comment (`admin' #`)
- Demo SQL Injection với OR condition (`' OR '1'='1`)

**Hình ảnh/Minh họa:**
- Screenshot code PHP dễ bị tấn công
- Screenshot trang đăng nhập
- Screenshot câu SQL được tạo ra
- Screenshot kết quả đăng nhập thành công

**Ghi chú:**
- Nhấn mạnh đây là môi trường lab an toàn
- Giải thích tại sao code dễ bị tấn công
- So sánh giữa hành vi bình thường và sau khi bị tấn công

---

## Slide 8: Demo - Khai thác dữ liệu qua SQL Injection

### Nội dung slide:

**Tiêu đề:** Demo - Khai thác dữ liệu

**Nội dung chính:**
- Trang tìm kiếm sản phẩm dễ bị SQL Injection
- Demo tìm kiếm bình thường
- Demo SQL Injection để hiển thị tất cả sản phẩm (`' OR '1'='1`)
- Demo UNION-based SQL Injection để lấy tên database (`' UNION SELECT 1, database(), 3, 4 #`)

**Hình ảnh/Minh họa:**
- Screenshot trang tìm kiếm
- Screenshot câu SQL với UNION
- Screenshot kết quả hiển thị tên database
- Bảng so sánh: Tìm kiếm bình thường vs SQL Injection

**Ghi chú:**
- Giải thích cách UNION hoạt động
- Nhấn mạnh khả năng đọc dữ liệu không được phép
- Liên hệ với hậu quả thực tế (đánh cắp dữ liệu)

---

## Slide 9: Demo - Payload 'OR 1=1 (Bypass xác thực)

### Nội dung slide:

**Tiêu đề:** Payload 'OR 1=1 - Bypass Authentication

**Nội dung chính:**
- Payload `' OR '1'='1` là một trong những payload SQL Injection kinh điển nhất
- Giải thích cách payload hoạt động
- Demo bypass đăng nhập
- Ứng dụng của payload này ở các chức năng khác (tìm kiếm, lọc dữ liệu)
- Case study: Vụ tấn công Bkav 2021

**Hình ảnh/Minh họa:**
- Diagram giải thích logic của câu SQL
- Screenshot demo bypass đăng nhập
- Screenshot case study Bkav (nếu có)

**Ghi chú:**
- Nhấn mạnh tính đơn giản nhưng nguy hiểm của payload
- Giải thích tại sao payload hoạt động (logic SQL)
- Liên hệ với thực tế để thấy mức độ nguy hiểm

---

## Slide tổng hợp (nếu cần)

### Slide: Tổng kết phần Demo

**Nội dung:**
- Các kỹ thuật SQL Injection đã demo:
  - Comment-based (`#`, `--`)
  - OR-based (`' OR '1'='1`)
  - UNION-based (`UNION SELECT`)
- Hậu quả đã minh họa:
  - Bypass authentication
  - Data disclosure
  - Database information disclosure
- Nguyên nhân: String concatenation, thiếu input validation
- Chuyển tiếp: Phần phòng chống SQL Injection

---

## Gợi ý thiết kế slide

### Màu sắc:
- Màu đỏ cho phần code dễ bị tấn công
- Màu xanh lá cho phần giải thích an toàn
- Màu vàng/cam cho cảnh báo

### Font:
- Code: Monospace font (Courier New, Consolas)
- Nội dung: Sans-serif font (Arial, Calibri)

### Layout:
- Slide 7: 2 cột - Code bên trái, Demo bên phải
- Slide 8: 3 phần - Tìm kiếm bình thường, SQL Injection, UNION
- Slide 9: Focus vào payload và logic

### Animation (nếu dùng PowerPoint):
- Hiển thị từng bước của demo
- Highlight phần code dễ bị tấn công
- Hiển thị câu SQL được tạo ra từng phần

---

## Checklist trước khi thuyết trình

- [ ] Docker containers đã chạy
- [ ] Ứng dụng web đã sẵn sàng (http://localhost:8080)
- [ ] Đã test tất cả các payload
- [ ] Đã chuẩn bị screenshot backup (nếu demo không hoạt động)
- [ ] Đã chuẩn bị giải thích cho các câu hỏi về:
  - Tại sao code dễ bị tấn công
  - Cách payload hoạt động
  - Cách phòng chống
- [ ] Đã kiểm tra thời gian demo (khoảng 10 phút)

