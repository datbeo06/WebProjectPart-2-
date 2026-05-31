Với vai trò **Tech Lead - Infrastructure & Management Logic**, bạn cần tiếp cận việc viết code theo **trình tự từ dưới lên (Bottom-Up Approach)**: Đi từ nền móng hạ tầng (Database & Connection) -> Xây dựng bộ khung dùng chung (Inclusions) -> Tích hợp trang chủ -> và cuối cùng mới là phát triển Logic Quản trị phức tạp.

Dưới đây là **Trình tự Logic 5 Giai đoạn** tối ưu nhất để bạn trình bày khi code thực tế cũng như khi trả lời phỏng vấn:

---

### GIAI ĐOẠN 1: THIẾT LẬP NỀN MÓNG (DATABASE & INFRASTRUCTURE)

_Đây là bước đầu tiên vì toàn bộ các trang PHP sau này đều không thể chạy nếu thiếu Database và Kết nối._

1. **Bước 1: Tạo và chạy file `database.sql`**
    - Tạo cơ sở dữ liệu `solarcore_db` và các bảng `eoi`, `jobs`, `users`, `about`.
    - Chèn dữ liệu mẫu (Seed data) cho các công việc (`jobs`), thông tin thành viên (`about`) và đặc biệt là tài khoản admin (`admin/admin` đã hash) vào bảng `users`.
2. **Bước 2: Tạo file cấu hình `settings.php`**
    - Viết mã nguồn kết nối MySQLi. Đây là file "xương sống", chỉ cần viết chuẩn một lần, tất cả các file khác khi cần tương tác dữ liệu chỉ cần gọi `require_once 'settings.php'`.

---

### GIAI ĐOẠN 2: XÂY DỰNG BỘ KHUNG GIAO DIỆN DÙNG CHUNG (COMMON TEMPLATES)

_Thiết lập các khối giao diện dùng chung để đảm bảo tính đồng nhất (Consistency) và dễ bảo trì._

3. **Bước 3: Tạo `header.inc` & `footer.inc`**
    - Định nghĩa thẻ mở HTML, meta tags, liên kết file `styles/style.css` chung trong `header.inc`.
    - Định nghĩa phần chân trang chứa các link bắt buộc (Jira, GitHub, email) và các thẻ đóng trong `footer.inc`.
4. **Bước 4: Tạo `nav.inc` (Tích hợp kiểm tra trạng thái đăng nhập)**
    - Viết menu điều hướng.
    - Tích hợp logic sử dụng `basename($_SERVER['PHP_SELF'])` để tự động highlight trang hiện tại (Active Link).
    - Thêm logic kiểm tra Session: Nếu `$_SESSION['logged_in']` bằng true thì mới hiển thị thêm link **"Manage"** và **"Logout"**.

---

### GIAI ĐOẠN 3: CHUYỂN ĐỔI & KIỂM THỬ TRANG CHỦ (CONVERT TO PHP)

_Kiểm tra xem bộ khung hạ tầng vừa xây dựng ở Giai đoạn 2 hoạt động có tốt không trên giao diện thực tế._

5. **Bước 5: Tạo `index.php`**
    - Lấy file `index.html` của Part 1, cắt bỏ các phần tĩnh `<head>`, `<nav>`, `<footer>` và thay bằng các câu lệnh `include` tương ứng.
    - **Kiểm thử nhanh:** Mở `index.php` trên trình duyệt (XAMPP). Nếu trang chủ hiển thị đẹp mắt, CSS tải đầy đủ, menu hoạt động trơn tru nghĩa là hạ tầng dùng chung của bạn đã thành công 100%.

---

### GIAI ĐOẠN 4: TRIỂN KHAI LOGIC QUẢN TRỊ CHÍNH (`manage.php`)

_Đây là phần việc nặng nhất và đòi hỏi tư duy logic cao nhất của bạn._

Để viết `manage.php` không bị rối, bạn hãy viết theo thứ tự tăng dần về độ khó sau đây:

6. **Bước 6: Khởi dựng & Bảo mật trang (`manage.php` - Phần 1)**
    - Viết logic kiểm tra Session ở đầu trang. Nếu chưa đăng nhập -> chuyển hướng về `login.php` ngay lập tức để chặn truy cập trái phép.
    - Include `header.inc`, `nav.inc` và `footer.inc` để có giao diện admin hoàn chỉnh.
7. **Bước 7: Viết câu lệnh hiển thị mặc định (Query 1 - List All)**
    - Gọi `require_once 'settings.php'`.
    - Viết câu lệnh SQL đơn giản: `SELECT * FROM eoi` để lấy toàn bộ danh sách hồ sơ và hiển thị lên bảng HTML.
    - _Mục đích:_ Xác nhận dữ liệu từ DB đã được hiển thị lên màn hình chính xác thông qua vòng lặp `while(mysqli_fetch_assoc(...))`. Bọc tất cả đầu ra bằng `htmlspecialchars()` để chống XSS.
8. **Bước 8: Tích hợp bộ lọc tìm kiếm nâng cao (Query 2 & 3 - Filtering)**
    - Xây dựng Form tìm kiếm (Job Reference, First Name, Last Name).
    - Viết logic nhận dữ liệu từ Form (sử dụng phương thức `GET` để giữ lại bộ lọc trên URL khi nhấn sắp xếp).
    - Dùng mệnh đề điều kiện `if` để xây dựng câu truy vấn động bằng **Prepared Statements** (mysqli_prepare, bind_param).
9. **Bước 9: Tích hợp sắp xếp động (Query 6 - Sorting)**
    - Thêm Dropdown sắp xếp trong Form (Sắp xếp theo mã EOI, Họ ứng viên, Trạng thái, Ngày nộp).
    - Áp dụng **Whitelist Validation** để kiểm tra tính an toàn của cột được yêu cầu sắp xếp trước khi nối vào chuỗi SQL `ORDER BY $sort_by`.
10. **Bước 10: Xử lý hành động cập nhật trạng thái (Query 5 - Update Status)**
    - Thêm Form nhỏ chứa Dropdown (`New`, `Current`, `Final`) và nút "Update" vào cột cuối cùng của từng hàng trong bảng.
    - Viết logic xử lý phương thức `POST` ở đầu trang `manage.php` để cập nhật trạng thái hồ sơ vào database qua Prepared Statements.
11. **Bước 11: Xử lý hành động xóa hàng loạt (Query 4 - Delete records)**
    - Thêm nút "Delete by Job Ref" cạnh nút tìm kiếm.
    - Viết logic xử lý hành động xóa theo `job_ref` (Prepared Statements). Thêm cảnh báo xác nhận `confirm()` phía Frontend để tránh mất mát dữ liệu do vô tình nhấn nhầm.

---

### GIAI ĐOẠN 5: KIỂM THỬ TÍCH HỢP CUỐI CÙNG (INTEGRATION TESTING)

_Đóng vai trò Tech Lead để chạy thử nghiệm đầu cuối hệ thống (End-to-End)._

12. **Bước 12: Chạy thử toàn bộ luồng nghiệp vụ**
    - Vào trang ứng tuyển `apply.php` -> Gửi một hồ sơ mẫu.
    - Vào trang `login.php` -> Đăng nhập tài khoản `admin` / `admin`.
    - Vào trang `manage.php` -> Kiểm tra xem hồ sơ mẫu vừa gửi có xuất hiện ở đầu danh sách không.
    - Thử nghiệm tính năng lọc theo mã job, lọc theo tên, thử sắp xếp bảng kết quả.
    - Thử cập nhật trạng thái hồ sơ của ứng viên đó và cuối cùng thử xóa hồ sơ theo mã job để đảm bảo database cập nhật hoàn hảo.