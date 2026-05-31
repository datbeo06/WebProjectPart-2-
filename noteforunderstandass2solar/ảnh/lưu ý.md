function có sẵn = màu vàng  


--------------------------------------------     
### `$page_title`
- **Giá trị** (chuỗi chữ): Khác nhau tùy trang.
- **Tên biến**: Phải luôn viết chính xác là `$page_title` (không được đặt thành `$title`, `$title_page`, v.v.), vì file template chung `header.inc` chỉ nhận diện đúng tên biến `$page_title`.

xuật hiện ở đầu trang các file như index about , job , aplly để đồng nhất tên biến để 

--- 
**Hàm `htmlspecialchars()` (Ngăn chặn XSS):**

- Đây là vũ khí chống lỗi bảo mật **Cross-Site Scripting (XSS)**. Hàm này chuyển đổi các ký tự HTML đặc biệt (như `<`, `>`, `&`, `"`, `'`) thành các thực thể HTML an toàn (ví dụ: `<` thành `&lt;`).

------ 

