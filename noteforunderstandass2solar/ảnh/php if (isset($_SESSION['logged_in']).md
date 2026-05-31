#### B. Phân quyền hiển thị (Security & Session)

php

**<?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>**

    **<li><a href="manage.php" ...>Manage</a></li>**

    **<li><a href="logout.php" ...>Logout</a></li>**

**<?php endif; ?>**




- **Logic**: Chỉ khi ứng dụng xác nhận người dùng đã đăng nhập thành công (`$_SESSION['logged_in'] === true`), menu mới xuất hiện nút **Manage** và **Logout**. Khách vãng lai sẽ không bao giờ nhìn thấy 2 nút này.


`isset` là một hàm PHP dùng để **kiểm tra xem một biến có được định nghĩa (set) và không phải là `NULL` hay không**.


