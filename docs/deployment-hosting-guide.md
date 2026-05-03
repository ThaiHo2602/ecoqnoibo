# Hướng Dẫn Đưa EcoQnoibo Lên Hosting

Tài liệu này dành cho dự án `EcoQnoibo` hiện tại, dùng PHP thuần, chạy bằng Apache + MySQL và đã được chỉnh để có thể truy cập domain gốc mà không cần gõ `/public`.

Mục tiêu sau khi deploy:

- Truy cập `https://tenmiencuaban.com` sẽ vào landing page
- Truy cập `https://tenmiencuaban.com/gioi-thieu.php`, `/du-an.php`, `/tin-tuc.php`, `/tuyen-dung.php`, `/lien-he.php` sẽ vào đúng trang công ty
- Truy cập `https://tenmiencuaban.com/login` sẽ vào trang đăng nhập nội bộ
- Truy cập `https://tenmiencuaban.com/dashboard`, `/app`, `/rooms`, `/customers`... sẽ chạy qua router nội bộ
- Ảnh/video trong `storage/uploads` hiển thị bình thường

## 1. Tổng Quan Cấu Trúc Hiện Tại

Các thư mục quan trọng:

- `public/`: router chính và file public
- `app/`: controller, core, service
- `resources/views/`: giao diện nội bộ
- `public/company/`: landing page công ty
- `storage/uploads/`: ảnh và video phòng
- `storage/logs/`: log mail và log phát sinh
- `database/schema.sql`: schema chính
- `database/customer_module_patch.sql`: patch phần khách hàng
- `config/app.php`: cấu hình app + database + base URL
- `config/mail.php`: cấu hình SMTP
- `.htaccess` ở thư mục gốc: rewrite để bỏ `/public`
- `public/.htaccess`: rewrite route nội bộ

## 2. Trước Khi Upload Lên Hosting

Bạn cần đổi ít nhất 2 file cấu hình:

### 2.1. Sửa `config/app.php`

Mở file [config/app.php](/D:/xampp/htdocs/EcoQnoibo/config/app.php) và đổi:

- `base_url`
- thông tin database

Ví dụ:

```php
<?php

return [
    'name' => 'EcoQ Noi Bo',
    'base_url' => 'https://tenmiencuaban.com',
    'timezone' => 'Asia/Ho_Chi_Minh',
    'db' => [
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'ten_database_hosting',
        'username' => 'ten_user_db',
        'password' => 'mat_khau_db',
        'charset' => 'utf8mb4',
    ],
    'security' => [
        'inactive_lock_days' => 7,
    ],
];
```

Lưu ý:

- Nếu website nằm trong thư mục con, ví dụ `https://tenmien.com/ecoqnoibo`, thì `base_url` phải là:
  `https://tenmien.com/ecoqnoibo`
- Nếu website chạy trực tiếp ở domain chính, không thêm thư mục:
  `https://tenmien.com`

### 2.2. Sửa `config/mail.php`

Mở file [config/mail.php](/D:/xampp/htdocs/EcoQnoibo/config/mail.php).

Nếu chưa dùng gửi mail, có thể để:

```php
'enabled' => false,
```

Nếu muốn dùng gửi mail thông báo:

```php
<?php

return [
    'enabled' => true,
    'driver' => 'smtp',
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'encryption' => 'tls',
    'username' => 'emailcuaban@gmail.com',
    'password' => 'app_password_16_ky_tu',
    'from_email' => 'emailcuaban@gmail.com',
    'from_name' => 'Eco-Q House',
    'timeout' => 15,
    'log_file' => base_path('storage/logs/mail.log'),
];
```

Lưu ý:

- Với Gmail phải dùng `App Password`, không dùng mật khẩu Gmail thường
- Server hosting phải cho phép kết nối SMTP ra ngoài

## 3. Chuẩn Bị Database

### 3.1. Tạo database mới trên hosting

Trong cPanel hoặc DirectAdmin:

1. Tạo database
2. Tạo user database
3. Gán user vào database với full quyền

### 3.2. Import SQL

Import lần lượt:

1. [database/schema.sql](/D:/xampp/htdocs/EcoQnoibo/database/schema.sql)
2. [database/customer_module_patch.sql](/D:/xampp/htdocs/EcoQnoibo/database/customer_module_patch.sql)

Nếu hosting chỉ cho import từng file:

- import `schema.sql` trước
- import `customer_module_patch.sql` sau

### 3.3. Kiểm tra dữ liệu mẫu

Sau khi import xong, thử đăng nhập bằng các tài khoản mẫu có sẵn nếu database đó đang chứa seed:

- `director / 123456`
- `manager / 123456`
- `staff / 123456`

Nếu host là bản production thật, nên:

- đổi mật khẩu ngay sau khi deploy
- tạo tài khoản mới riêng cho production

## 4. Upload Source Code Lên Hosting

Có 2 kiểu host phổ biến.

### Cách A: Host cho đổi `document root` vào `public/`

Đây là cách tốt nhất.

Bạn làm như sau:

1. Upload toàn bộ source code lên một thư mục, ví dụ:
   `/home/username/ecoqnoibo`
2. Trỏ `document root` của domain/subdomain vào:
   `/home/username/ecoqnoibo/public`

Ưu điểm:

- bảo mật hơn
- sạch hơn
- không cần phụ thuộc `.htaccess` ở thư mục gốc để giấu `/public`

Nếu làm theo cách này:

- vẫn giữ nguyên source code
- chỉ cần đảm bảo `base_url` đúng domain

### Cách B: Shared hosting không cho đổi `document root`

Nếu host bắt buộc web phải chạy từ `public_html/` hoặc thư mục gốc cố định, dự án hiện tại đã có sẵn giải pháp.

Bạn làm như sau:

1. Upload toàn bộ dự án vào thư mục public của host
2. Giữ nguyên file:
   - [index.php](/D:/xampp/htdocs/EcoQnoibo/index.php)
   - [\.htaccess](/D:/xampp/htdocs/EcoQnoibo/.htaccess)
3. Đảm bảo host hỗ trợ `mod_rewrite`

Hiện dự án đã có:

- `index.php` ở thư mục gốc để forward vào `public/index.php`
- `.htaccess` ở thư mục gốc để rewrite route về đúng router
- `public/.htaccess` để xử lý route nội bộ

Nếu deploy kiểu này, bạn vẫn vào được:

- `/`
- `/login`
- `/dashboard`
- `/app`
- `/company/...`

mà không cần gõ `/public`.

## 5. Quyền Thư Mục Trên Hosting

Tối thiểu cần đảm bảo:

- `storage/`
- `storage/uploads/`
- `storage/uploads/images/`
- `storage/uploads/videos/`
- `storage/logs/`

phải ghi được.

Thông thường:

- thư mục: `755`
- file: `644`

Nếu host báo không upload được ảnh/video hoặc không ghi log mail:

- tăng quyền `storage/` lên `775`
- nếu vẫn không được, kiểm tra user chạy PHP của hosting

Không nên dùng `777` trừ khi host quá đặc thù và bạn hiểu rõ rủi ro.

## 6. Bắt Buộc Bật Rewrite

Hệ thống route hiện tại phụ thuộc `.htaccess`.

Nếu sau deploy bạn thấy các lỗi kiểu:

- `404 Not Found`
- vào `/login` không được
- vào `/app` bị mở thư mục
- vào `/dashboard` bị sai route

thì gần như chắc chắn là do rewrite chưa chạy.

Bạn cần kiểm tra:

- Hosting có hỗ trợ `.htaccess`
- Apache có bật `mod_rewrite`
- Cho phép `AllowOverride All`

Nếu dùng shared hosting thông thường thì thường đã bật sẵn.

## 7. Ảnh Và Video Phòng

Hệ thống hiện lưu file thật trong:

- `storage/uploads/images`
- `storage/uploads/videos`

Các URL media hiện đã được chuẩn hóa theo `base_url`, ví dụ:

- `https://tenmien.com/storage/uploads/images/xxx.jpg`

Vì vậy sau khi deploy bạn cần đảm bảo:

1. thư mục `storage/uploads` đã được upload đầy đủ
2. web server cho phép đọc file trong `storage/uploads`
3. `base_url` trỏ đúng domain hiện tại

Nếu ảnh không hiện:

1. mở DevTools của trình duyệt
2. kiểm tra URL ảnh đang gọi
3. thử mở trực tiếp URL đó
4. nếu 404 thì kiểm tra:
   - file có tồn tại trên host không
   - `base_url` có đúng không
   - file có nằm đúng `storage/uploads/...` không

## 8. Luồng Truy Cập Sau Khi Deploy

Luồng hiện tại:

- `/` -> landing page công ty
- `/gioi-thieu.php`, `/du-an.php`, `/tin-tuc.php`, `/tuyen-dung.php`, `/lien-he.php` -> các trang công ty ở domain gốc
- `/company/` -> vẫn có thể truy cập trực tiếp nếu host đang phục vụ thư mục `public/company`, nhưng đây không còn là URL chuẩn nữa
- `/login?force_login=1` -> luôn mở form đăng nhập
- `/login` -> mở login, nếu đã có session có thể redirect
- đăng nhập thành công -> `/dashboard`
- `/app` -> trang chủ nội bộ dạng listing phòng

Lưu ý:

- `dashboard` và `app` là hai màn hình khác nhau
- `dashboard` là bảng điều khiển
- `app` là trang nội bộ chính dạng danh sách/phòng

## 9. Checklist Sau Deploy

Sau khi upload xong, test theo đúng thứ tự này:

### 9.1. Trang public

Mở và kiểm tra:

- `/`
- `/gioi-thieu.php`
- `/du-an.php`
- `/chi-tiet-du-an.php?id=...`
- `/tin-tuc.php`
- `/tuyen-dung.php`
- `/lien-he.php`

Kiểm tra:

- CSS có tải không
- logo có hiện không
- ảnh phòng có hiện không
- nút đăng nhập nhân viên có mở đúng form login không

### 9.2. Trang nội bộ

Mở và kiểm tra:

- `/login`
- đăng nhập bằng tài khoản mẫu
- sau đăng nhập có vào `/dashboard` không
- `/app`
- `/rooms`
- `/rooms/{id}`
- `/reports`
- `/customers`

Kiểm tra:

- ảnh phòng có hiện không
- lightbox ảnh có mở không
- tải `.zip` ảnh phòng có chạy không
- upload ảnh/video mới có thành công không

### 9.3. Database

Kiểm tra:

- thêm phòng mới
- sửa phòng
- tạo khách hàng
- gửi lock
- duyệt lock
- tạo user

### 9.4. Mail

Nếu đã bật SMTP:

- phân khách cho nhân viên
- xác nhận mail có được gửi không
- nếu không gửi được thì xem log:
  `storage/logs/mail.log`

## 10. Lỗi Thường Gặp Và Cách Xử Lý

### Lỗi 0: Vừa mở domain đã báo HTTP 500

Nguyên nhân thường gặp nhất trên shared hosting:

- host đang dùng PHP 8.0 hoặc thấp hơn, trong khi dự án này cần PHP 8.1 trở lên
- file `.htaccess` chứa directive host không cho phép
- `RewriteBase` vẫn đang để kiểu local như `/EcoQnoibo/`
- lỗi kết nối database hoặc thiếu bảng dữ liệu
- thiếu extension PHP như `pdo_mysql` hoặc `zip`

Cách xử lý theo đúng thứ tự:

1. Kiểm tra phiên bản PHP trên host, tối thiểu phải là `PHP 8.1+`
2. Kiểm tra `error_log` trong cPanel hoặc File Manager
3. Nếu chưa xem được log, tạm thêm vào đầu file `index.php`:

```php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
```

4. Kiểm tra lại file `.htaccess`:
   - không dùng `RewriteBase /EcoQnoibo/` khi chạy ở domain gốc
   - một số host không cho `Options -Indexes`
   - một số host không cho `DirectorySlash Off`
5. Kiểm tra `config/app.php`:
   - `base_url` đúng domain thật
   - DB host, DB name, DB user, DB password đúng
6. Đảm bảo đã import đủ:
   - `database/schema.sql`
   - `database/customer_module_patch.sql`

### Lỗi 1: Vào `/login` hoặc `/dashboard` bị 404

Nguyên nhân:

- rewrite chưa chạy

Cách xử lý:

- kiểm tra `.htaccess`
- kiểm tra `mod_rewrite`
- kiểm tra `AllowOverride All`

### Lỗi 2: Vào `/app` bị hiện danh sách thư mục

Nguyên nhân:

- Apache đang mở thư mục thật `app/`

Cách xử lý:

- giữ nguyên `.htaccess` gốc hiện tại
- không xóa rule rewrite gốc
- không xóa `DirectorySlash Off`
- không xóa `Options -Indexes`

### Lỗi 3: Ảnh phòng không hiện

Nguyên nhân:

- upload thiếu thư mục `storage/uploads`
- `base_url` sai
- server chặn đọc file trong `storage`

Cách xử lý:

- kiểm tra file thật trên host
- kiểm tra URL ảnh đang sinh ra
- kiểm tra quyền thư mục

### Lỗi 4: Không đăng nhập được dù tài khoản đúng

Nguyên nhân có thể:

- database chưa import đúng
- DB config sai
- tài khoản đang bị khóa
- password hash khác dữ liệu local

Cách xử lý:

- kiểm tra `config/app.php`
- kiểm tra bảng `users`
- reset lại mật khẩu bằng SQL nếu cần

### Lỗi 5: Gửi mail không hoạt động

Nguyên nhân:

- `mail.enabled = false`
- SMTP chưa điền đủ
- host chặn SMTP
- Gmail chưa tạo app password

Cách xử lý:

- kiểm tra [config/mail.php](/D:/xampp/htdocs/EcoQnoibo/config/mail.php)
- xem `storage/logs/mail.log`

## 11. Khuyến Nghị Trước Khi Chạy Production Thật

Nên làm thêm:

- đổi tất cả tài khoản mẫu mặc định
- backup database trước khi nhập dữ liệu thật
- cấu hình HTTPS
- kiểm tra dung lượng upload tối đa của host
- nếu video nhiều, nên xác nhận host còn đủ dung lượng
- tắt directory listing toàn server
- định kỳ backup:
  - database
  - `storage/uploads`
  - `config/app.php`
  - `config/mail.php`

## 12. Checklist Siêu Nhanh

Nếu bạn cần bản rút gọn cực nhanh:

1. Upload toàn bộ source code lên host
2. Import:
   - `database/schema.sql`
   - `database/customer_module_patch.sql`
3. Sửa [config/app.php](/D:/xampp/htdocs/EcoQnoibo/config/app.php)
4. Sửa [config/mail.php](/D:/xampp/htdocs/EcoQnoibo/config/mail.php) nếu dùng mail
5. Đảm bảo `storage/uploads` và `storage/logs` ghi được
6. Đảm bảo `.htaccess` hoạt động
7. Test:
   - `/`
   - `/login`
   - đăng nhập
   - `/dashboard`
   - `/app`
   - upload ảnh
   - xem ảnh
   - tải zip ảnh

## 13. Gợi Ý Sau Này

Nếu bạn muốn deploy ổn định và chuyên nghiệp hơn nữa, nên làm ở đợt tiếp theo:

- tách cấu hình local và production
- chuyển config nhạy cảm sang biến môi trường
- thêm script backup
- thêm file `health-check`
- thêm log lỗi rõ ràng hơn cho production
