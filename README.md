# EcoQ Nội Bộ

EcoQ Nội Bộ là hệ thống quản lý phòng, chi nhánh và quy trình giữ phòng nội bộ cho Eco-Q House. Dự án được viết bằng PHP thuần theo mô hình controller/view đơn giản, sử dụng MySQL làm cơ sở dữ liệu và chạy tốt trên XAMPP hoặc hosting có Apache, PHP và MySQL.

## Tính năng chính

- Trang giới thiệu công ty/public site: trang chủ, giới thiệu, danh sách phòng/dự án, chi tiết phòng, tin tức, tuyển dụng và liên hệ.
- Khu nội bộ cho nhân sự tại `/app`, tối ưu cho giao diện mobile.
- Đăng nhập, đăng xuất, ghi nhớ đăng nhập và phân quyền theo vai trò.
- Quản lý hệ thống, phường, chi nhánh và phòng.
- Quản lý giá dịch vụ theo chi nhánh: điện, nước, dịch vụ, gửi xe.
- Upload nhiều hình ảnh/video cho phòng.
- Xem chi tiết chi nhánh, chọn phòng trong chi nhánh và tải ảnh phòng.
- Quy trình gửi yêu cầu lock phòng, duyệt/từ chối/hoàn tác lock.
- Quản lý khách hàng tiềm năng và phân công nhân viên xử lý.
- Quản lý người dùng, đổi mật khẩu, mở khóa tài khoản.
- Báo cáo tổng quan và nhật ký hoạt động.

## Công nghệ sử dụng

- PHP thuần, không bắt buộc Composer.
- MySQL/MariaDB.
- Apache với `mod_rewrite`.
- HTML, CSS, JavaScript thuần.
- Bootstrap Icons ở phần giao diện public.

## Cấu trúc thư mục

```text
app/
  Controllers/        Controller xử lý nghiệp vụ
  Core/               Lớp lõi: Auth, Database, Session, View
  Services/           Dịch vụ phụ trợ
config/
  app.php             Cấu hình app, base URL, database, bảo mật
  mail.php            Cấu hình gửi mail/liên hệ
database/
  schema.sql          Schema đầy đủ để tạo database mới
  *_patch.sql         Các patch nâng cấp database cũ
  sample_data_*.sql   Dữ liệu mẫu mở rộng
public/
  index.php           Front controller, định tuyến chính
  .htaccess           Rewrite route về public/index.php
  assets/             CSS, JS, hình ảnh
  company/            Các trang public
resources/views/      View cho khu nội bộ
storage/              File runtime, upload, cache, session, log
```

## Yêu cầu môi trường

- PHP 8.1 trở lên.
- MySQL hoặc MariaDB.
- Apache bật `mod_rewrite`.
- Extension PHP cần có: `pdo_mysql`, `mbstring`, `fileinfo`.
- Nếu chạy local bằng XAMPP: bật Apache và MySQL trong XAMPP Control Panel.

## Cài đặt local bằng XAMPP

1. Đặt source vào thư mục `htdocs`, ví dụ:

```text
D:\xampp\htdocs\EcoQnoibo
```

2. Tạo database MySQL:

```sql
CREATE DATABASE ecoq_noibo
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

3. Import schema:

```bash
mysql -u root ecoq_noibo < database/schema.sql
```

Hoặc vào phpMyAdmin, chọn database `ecoq_noibo`, rồi import file `database/schema.sql`.

4. Kiểm tra cấu hình trong `config/app.php`:

```php
'base_url' => 'http://localhost/EcoQnoibo',
'db' => [
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'ecoq_noibo',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
],
```

Nếu chạy trên domain thật, đổi `base_url` thành domain của bạn, ví dụ:

```php
'base_url' => 'https://ten-mien-cua-ban.com',
```

5. Mở trình duyệt:

- Public site: `http://localhost/EcoQnoibo/`
- Khu nội bộ: `http://localhost/EcoQnoibo/app`
- Đăng nhập: `http://localhost/EcoQnoibo/login`

## Tài khoản mẫu

Sau khi import `database/schema.sql`, có thể đăng nhập bằng các tài khoản sau. Mật khẩu mặc định là `123456`.

| Vai trò | Tài khoản |
| --- | --- |
| Giám đốc | `director` |
| Quản lý | `manager` |
| Nhân viên | `staff` |
| Cộng tác viên | `collaborator` |

Nên đổi mật khẩu ngay sau khi triển khai thật.

## Bật rewrite cho Apache

Nếu truy cập `/login`, `/app`, `/rooms` bị `404 Not Found`, hãy kiểm tra Apache.

Trong XAMPP, mở `httpd.conf` và chắc chắn dòng này không bị comment:

```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

Trong cấu hình `Directory` trỏ tới `htdocs`, cần cho phép `.htaccess` hoạt động:

```apache
AllowOverride All
```

Sau khi sửa cấu hình, restart Apache.

## Chạy bằng PHP built-in server

Cách này phù hợp để kiểm tra nhanh nếu không dùng Apache:

```bash
php -S localhost:8000 -t public
```

Sau đó mở:

```text
http://localhost:8000
```

Lưu ý: khi dùng built-in server, hãy chỉnh `base_url` trong `config/app.php` thành `http://localhost:8000` nếu cần test URL tuyệt đối.

## Database và patch nâng cấp

Nếu tạo database mới, chỉ cần import `database/schema.sql`.

Nếu đang nâng cấp từ database cũ, hãy xem và chạy các patch trong thư mục `database/` theo nhu cầu, ví dụ:

- `remember_login_patch.sql`: thêm bảng/token ghi nhớ đăng nhập.
- `customer_module_patch.sql`: thêm module khách hàng tiềm năng.
- `ward_module_patch.sql` và `ward_utf8_fix_patch.sql`: thêm/sửa dữ liệu phường.
- `branch_fee_migration_patch.sql`: chuyển phí điện/nước/dịch vụ/gửi xe lên chi nhánh.
- `drop_branch_address_patch.sql`: xóa cột địa chỉ chi nhánh cũ.
- `room_type_update_patch.sql`: chuẩn hóa loại phòng.
- `public_room_visibility_patch.sql`: bật/tắt hiển thị phòng public.
- `lock_undo_patch.sql`: hỗ trợ hoàn tác lock.

Nên backup database trước khi chạy patch:

```bash
mysqldump -u root ecoq_noibo > backup_ecoq_noibo.sql
```

## Ghi chú về tiếng Việt và mã hóa

Toàn bộ file nên lưu bằng UTF-8. Database cũng nên dùng `utf8mb4_unicode_ci`.

Không nên dùng lệnh ghi file mặc định của PowerShell như `Set-Content` nếu chưa chỉ rõ encoding, vì dễ gây lỗi mojibake. Khi chỉnh file thủ công, hãy dùng editor lưu UTF-8.

## Upload và thư mục runtime

Các thư mục runtime không nên commit lên Git:

- `storage/tmp/`
- `storage/logs/`
- `storage/sessions/`
- `storage/cache/`
- `storage/uploads/`

Trên hosting thật, cần đảm bảo các thư mục này có quyền ghi cho PHP/Apache, đặc biệt là `storage/uploads/` nếu dùng upload ảnh/video phòng.

## Triển khai lên hosting

### Hosting PHP/cPanel/VPS

1. Upload source lên hosting.
2. Trỏ document root về thư mục `public/` nếu hosting cho phép.
3. Nếu document root không trỏ được vào `public/`, giữ file `.htaccess` ở root và `public/.htaccess` để rewrite hoạt động.
4. Tạo database MySQL và import `database/schema.sql`.
5. Sửa `config/app.php` theo thông tin database và domain thật.
6. Đảm bảo các thư mục trong `storage/` có quyền ghi.
7. Mở domain và kiểm tra `/login`, `/app`.

### GitHub Pages

GitHub Pages chỉ chạy HTML/CSS/JS tĩnh, không chạy PHP và không kết nối MySQL. Vì vậy dự án này không thể chạy đầy đủ trên GitHub Pages.

Nếu chỉ muốn xem giao diện tĩnh, bạn có thể mở một số file HTML/PHP như trang tĩnh, nhưng các chức năng đăng nhập, quản lý phòng, database, upload và lock phòng sẽ không hoạt động.

## Các URL quan trọng

| URL | Mục đích |
| --- | --- |
| `/` | Trang chủ public |
| `/du-an` | Danh sách phòng/dự án public |
| `/chi-tiet-du-an?id=...` | Chi tiết phòng public |
| `/login` | Đăng nhập nội bộ |
| `/app` | Trang chủ nội bộ/mobile |
| `/dashboard` | Dashboard tổng quan |
| `/systems` | Quản lý hệ thống |
| `/wards` | Quản lý phường |
| `/branches` | Quản lý chi nhánh |
| `/rooms` | Quản lý phòng |
| `/customers` | Quản lý khách hàng |
| `/lock-requests` | Duyệt yêu cầu lock |
| `/reports` | Báo cáo |
| `/users` | Quản lý người dùng |
| `/activity-logs` | Nhật ký hoạt động |

## Kiểm tra nhanh sau khi cài

Chạy kiểm tra cú pháp PHP:

```bash
php -l public/index.php
php -l bootstrap.php
php -l app/Core/Database.php
```

Kiểm tra database trong trình duyệt:

1. Mở `/login`.
2. Đăng nhập bằng `director` / `123456`.
3. Mở `/app` để xem danh sách chi nhánh/phòng.
4. Mở `/rooms` để kiểm tra CRUD phòng.
5. Upload thử ảnh phòng và kiểm tra hiển thị ở chi tiết phòng.

## Lưu ý bảo mật khi lên production

- Đổi toàn bộ mật khẩu tài khoản mẫu.
- Không commit `.env`, file backup database, file `.zip`, file trong `storage/tmp` hoặc `storage/uploads`.
- Bật HTTPS cho domain thật.
- Kiểm tra quyền ghi thư mục `storage/`, không cấp quyền quá rộng nếu không cần.
- Backup database định kỳ.
- Chỉ cấp tài khoản quản trị cho người thật sự cần quyền.

## Trạng thái dự án

Dự án hiện đã có đủ các module vận hành chính cho nội bộ Eco-Q House: quản lý hệ thống, chi nhánh, phường, phòng, media, khách hàng, lock phòng, người dùng, báo cáo và public site. Các thay đổi database đang được lưu trong thư mục `database/` để hỗ trợ cả cài mới lẫn nâng cấp từ bản cũ.
