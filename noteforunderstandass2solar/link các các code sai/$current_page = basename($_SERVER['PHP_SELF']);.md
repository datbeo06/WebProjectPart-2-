

```
$current_page = basename($_SERVER['PHP_SELF']);
```


- **`$_SERVER['PHP_SELF']`** trả về đường dẫn file hiện tại đang chạy (ví dụ: `/project2/jobs.php`).
- **`basename(...)`** sẽ cắt bỏ phần thư mục, chỉ giữ lại tên file: `jobs.php`.
- **Tl;dr:** `PHP_SELF` là biến **built-in của PHP**, PHP tự động cung cấp nó, bạn chỉ cần gọi ra dùng, không cần định nghĩa.


## Ví dụ cụ thể:

**Giả sử bạn có website với menu:**

```
Home | Jobs | Apply | About Us
```

### Khi người dùng vào trang nào?

1. **Người dùng vào `index.php`:**
    
    - `$_SERVER['PHP_SELF']` = `/project2/index.php`
    - `basename()` cắt lấy = `index.php`
    - `$current_page` = `"index.php"` ✅
2. **Người dùng vào `jobs.php`:**
    
    - `$_SERVER['PHP_SELF']` = `/project2/jobs.php`
    - `basename()` cắt lấy = `jobs.php`
    - `$current_page` = `"jobs.php"` ✅

### Dùng để làm gì?

Code ở **dòng 19** kiểm tra:

```php
<?php if ($current_page === 'index.php') { 
    echo 'class="active"'; 
} ?>
```

**Nghĩa là:**

- Nếu người dùng đang ở trang `index.php` → Thêm `class="active"` vào link "Home"
- Nếu người dùng đang ở trang `jobs.php` → Thêm `class="active"` vào link "Jobs"

### Kết quả:

```html
<!-- Khi ở index.php -->
<a href="index.php" class="active">Home</a>  ← Màu khác (được highlight)
<a href="jobs.php">Jobs</a>                   ← Màu thường

<!-- Khi ở jobs.php -->
<a href="index.php">Home</a>                  ← Màu thường
<a href="jobs.php" class="active">Jobs</a>   ← Màu khác (được highlight)
```

**Tl;dr:** Dùng để **tô đậm link menu của trang mà bạn đang ở**, giống như hiệu ứng "bạn đang ở đây" 👈
## `$_SERVER['PHP_SELF']` là gì?

**`PHP_SELF`** là một **biến tự động của PHP** (superglobal variable) chứa **đường dẫn của file PHP hiện tại**.

### Ví dụ cụ thể:

|URL của bạn|`$_SERVER['PHP_SELF']` chứa|
|---|---|
|`http://example.com/index.php`|`/index.php`|
|`http://example.com/project2/jobs.php`|`/project2/jobs.php`|
|`http://example.com/folder/apply.php`|`/folder/apply.php`|

### Nó được định nghĩa từ đâu?

**PHP tự động tạo ra** khi server xử lý request. Bạn **không cần khai báo**, nó là sẵn có.

### So sánh với `basename()`

```php
$_SERVER['PHP_SELF'] = "/project2/index.php"
basename($_SERVER['PHP_SELF']) = "index.php"  ← Chỉ lấy tên file
```

---

**Tóm tắt:**

- `$_SERVER['PHP_SELF']` = Đường dẫn đầy đủ từ root webserver
- `basename()` = Cắt lấy chỉ tên file (bỏ phần đường dẫn)

Trong trường hợp của bạn, chỉ cần tên file (`index.php`, `jobs.php`, ...) nên dùng `basename()`.