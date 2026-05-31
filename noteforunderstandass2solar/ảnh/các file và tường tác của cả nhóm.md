## 2. Bảng Phân Công Code & Điểm Giao Thoa Giữa Các Thành Viên

|Tên Tệp (File Name)|Người Chịu Trách Nhiệm Chính|Vai Trò & Nhiệm Vụ Chi Tiết|Cách Tương Tác / Giao Thoa với Thành Viên Khác|
|---|---|---|---|
|**settings.php**|**DAT**|Tạo cấu trúc cấu hình và biến kết nối cơ sở dữ liệu (`$conn`).|**Tất cả thành viên** đều phải sử dụng file này để mở kết nối DB trên các trang của mình.|
|**header.inc**  <br>**nav.inc**  <br>**footer.inc**|**DAT**|Viết mã nguồn HTML chung cho Header, Thanh điều hướng (Navigation) và Footer của trang web.|**Tất cả thành viên** phải chèn (`include`) các file này vào các trang PHP giao diện của mình để đồng bộ toàn bộ website.|
|**style.css**|**JOSE**|Xây dựng hệ thống UI chung, CSS biến màu, bố cục Responsive cho toàn bộ trang web.|**Tất cả thành viên** phải tuân thủ các class, mã màu do Jose định nghĩa để giao diện không bị lệch tone.|
|**database.sql**|**DAT** (Hợp nhất)|Chứa toàn bộ câu lệnh `CREATE TABLE` và `INSERT` dữ liệu mẫu.|**Mọi người đều phải viết**: Mỗi thành viên tự soạn thảo SQL Schema cho phần của mình rồi gửi cho Dat để gộp vào file tổng.|
|**manage.php**|**DAT** & **JOSE**  <br>_(Đồng tác giả)_|**Dat**: Viết 6 truy vấn SQL xử lý bộ lọc (Job Ref, Name, Sort, Delete, Update Status).  <br>**Jose**: Thiết kế form lọc, bảng kết quả, nút bấm và gắn kết với biến PHP của Dat.|**Điểm giao thoa lớn nhất**: Dat và Jose phải thống nhất các tên biến POST/GET (ví dụ: `job_ref`, `first_name`, `sort_by`) để dữ liệu từ Form của Jose truyền chính xác vào câu lệnh SQL của Dat.|
|**apply.php**|**MARKSAMUEL**|Chỉnh sửa form đăng ký ứng tuyển: **Xóa bỏ toàn bộ HTML5 validation** (required, pattern, type="email" -> text).|Trường `Job Reference` phải được liên kết logic với bảng `jobs` mà **James** thiết lập. CSS phải theo chuẩn của **Jose**.|
|**process_eoi.php**|**MARKSAMUEL**|Nhận dữ liệu từ `apply.php`, thực hiện kiểm tra bảo mật (Direct URL block), tạo bảng tự động, thực hiện **Server-side Regex Validation** và insert vào bảng `eoi`.|Cần sử dụng kết nối từ `settings.php` (**Dat**) và đối chiếu mã công việc (`JobRef`) từ bảng của **James** để đảm bảo công việc tồn tại trước khi cho phép ứng tuyển.|
|**jobs.php**|**JAMES**|Thiết kế trang hiển thị danh sách công việc động từ DB. Viết công cụ tìm kiếm sử dụng `SQL WHERE LIKE` kết hợp prepared statements.|Lấy dữ liệu từ bảng `jobs` hiển thị lên màn hình. Có nút ứng tuyển dẫn link kèm tham số `JobRef` sang `apply.php` của **MarkSamuel**.|
|**login.php**  <br>**logout.php**|**JOSE**|Thiết kế form đăng nhập admin, lưu trạng thái đăng nhập vào `session` (`$_SESSION['user']`), và trang đăng xuất hủy session.|Session được tạo từ đây sẽ được **Dat** dùng ở đầu file `manage.php` để kiểm tra quyền truy cập: nếu chưa đăng nhập thì redirect ngược về `login.php`.|
|**about.php**|**JOSE**|Hiển thị thông tin nhóm và phần trăm đóng góp của các thành viên tải động từ cơ sở dữ liệu.|Dữ liệu đóng góp và tên thành viên phải khớp chính xác với bảng dữ liệu do cả nhóm thống nhất.|

---

## 3. Các Tệp Tin Mà "MỌI NGƯỜI ĐỀU PHẢI VIẾT" (Shared Files)

Đây là các file cốt lõi mang tính chất nền tảng hoặc tích hợp, yêu cầu sự đóng góp và phối hợp chặt chẽ từ **tất cả 4 thành viên**:

### A. `database.sql` (Tệp cấu trúc Cơ sở Dữ liệu Tổng hợp)

- **Tại sao mọi người phải viết?** Mỗi thành viên phụ trách một luồng nghiệp vụ riêng đi kèm với các bảng cơ sở dữ liệu riêng.
- **Ai viết phần nào?**
    - **Dat**: Thiết lập hạ tầng DB chung.
    - **James**: Soạn thảo bảng `jobs` và chèn 2-5 bản ghi công việc mẫu cực kỳ thực tế của SolarCore Energy.
    - **MarkSamuel**: Soạn thảo bảng `eoi` (chứa các trường thông tin ứng tuyển như họ tên, địa chỉ, kỹ năng, trạng thái EOI).
    - **Jose**: Soạn thảo bảng `users` (tài khoản admin) kèm mật khẩu đã được mã hóa (MD5/Bcrypt/Sha256) và bảng `about` (dữ liệu đóng góp nhóm).
- **Quy trình hợp nhất**: Mọi người gửi đoạn mã SQL của mình cho **Dat** để Dat tổng hợp thành một file `database.sql` duy nhất chạy một lần là lên toàn bộ hệ thống.

### B. `settings.php` (Tệp cấu hình kết nối DB)

- **Tại sao mọi người phải viết/dùng chung?** File này chứa thông tin máy chủ DB (`$host`, `$user`, `$pwd`, `$sql_db`).
- **Cách phối hợp**: Cả nhóm phải thống nhất tên các biến cấu hình kết nối DB. Khi deploy lên máy chủ local (như XAMPP) hoặc server trường, cả nhóm chỉ cần chỉnh sửa duy nhất file `settings.php` này là tất cả các trang web khác đều chạy bình thường.

### C. `header.inc`, `nav.inc`, `footer.inc` (Các tệp giao diện chung)

- **Tại sao mọi người phải viết?**
    - **Dat** dựng khung ban đầu.
    - Tuy nhiên, các thành viên khác khi phát triển trang của mình (ví dụ: James làm `jobs.php`, MarkSamuel làm `apply.php`, Jose làm `about.php`) cần bổ sung các thẻ liên kết điều hướng vào `nav.inc` hoặc các file css/meta vào `header.inc`.
- **Cách phối hợp**: Mọi thay đổi về cấu trúc menu (Navigation Bar) hoặc thông tin bản quyền ở chân trang (Footer - bao gồm link Jira do James quản lý) đều phải được thực hiện trên các file `.inc` này để cập nhật tự động cho toàn hệ thống.

### D. `styles/style.css` (Tệp định dạng CSS chung)

- **Tại sao mọi người phải dùng chung?** Đảm bảo tính nhất quán về mặt mỹ thuật của SolarCore Energy.
- **Cách phối hợp**: Mặc dù **Jose** là người chịu trách nhiệm chính về UI/UX và CSS, nhưng khi **James** viết code hiển thị danh sách công việc hay **MarkSamuel** tạo form ứng tuyển, họ đều phải sử dụng chung các class màu sắc, font chữ và kiểu nút bấm (Button) có sẵn trong `style.css`. Nếu cần thêm CSS mới, họ phải thảo luận với Jose để Jose chuẩn hóa mã nguồn CSS, tránh viết CSS đè chồng chéo gây lỗi hiển thị.

---

## 4. Các Điểm Tương Tác Quan Trọng Cần Lưu Ý Để Tránh Xung Đột Code

IMPORTANT

**1. Đồng bộ hóa Biến và Form trong `manage.php`**

- **Jose** thiết kế UI form tìm kiếm/lọc và bảng kết quả hiển thị EOI.
- **Dat** viết các câu lệnh truy vấn dữ liệu theo các tiêu chí đó.
- _Cần làm:_ Hai người phải chốt trước danh sách tên của các input field (ví dụ: `name="jobref"`, `name="status"`, `name="sort"`) để Dat viết câu lệnh `$_GET['jobref']` hoặc `$_POST['status']` khớp hoàn toàn với thiết kế form của Jose.

WARNING

**2. Ràng buộc mã công nghiệp (`JobRef`) giữa Apply Flow và Jobs Flow**

- **James** quyết định mã công việc trong bảng `jobs` (ví dụ: `SC001`, `SC002`).
- **MarkSamuel** viết code kiểm tra ở trang `process_eoi.php`.
- _Cần làm:_ Khi thực hiện validation cho đơn ứng tuyển, MarkSamuel phải viết một truy vấn nhỏ kiểm tra xem mã `JobRef` người dùng nhập vào Form có thực sự tồn tại trong bảng `jobs` của James hay không. Nếu không tồn tại, phải báo lỗi ngay lập tức.

TIP

**3. Quản lý Session và Bảo mật trang quản lý (`manage.php`)**

- **Jose** tạo session đăng nhập thành công bằng cách đặt `$_SESSION['user_admin'] = true` trong `login.php`.
- **Dat** phải viết đoạn code kiểm tra ở ngay đầu file `manage.php`:
    
    php
    
    session_start();
    
    if (!isset($_SESSION['user_admin'])) {
    
        header("Location: login.php");
    
        exit();
    
    }
    
- _Cần làm:_ Thống nhất tên biến Session dùng chung để hệ thống phân quyền hoạt động đồng nhất.