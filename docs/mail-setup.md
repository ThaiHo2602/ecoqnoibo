# Hướng dẫn cấu hình email thông báo phân khách

Hệ thống hiện đã được code sẵn để gửi email cho nhân viên khi giám đốc phân khách hàng.

## 1. File cần điền thông số

Mở file:

- [config/mail.php](/D:/xampp/htdocs/EcoQnoibo/config/mail.php)

Bạn sẽ thấy cấu hình dạng:

```php
return [
    'enabled' => false,
    'driver' => 'smtp',
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'encryption' => 'tls',
    'username' => '',
    'password' => '',
    'from_email' => '',
    'from_name' => 'Eco-Q House',
    'timeout' => 15,
    'log_file' => base_path('storage/logs/mail.log'),
];
```

## 2. Cách điền

### Nếu dùng Gmail

Điền như sau:

```php
'enabled' => true,
'host' => 'smtp.gmail.com',
'port' => 587,
'encryption' => 'tls',
'username' => 'youremail@gmail.com',
'password' => 'APP_PASSWORD_16_KY_TU',
'from_email' => 'youremail@gmail.com',
'from_name' => 'Eco-Q House',
```

Lưu ý:

- `password` không phải mật khẩu Gmail thường.
- Bạn cần bật xác thực 2 lớp cho Gmail.
- Sau đó tạo `App Password` và dùng chuỗi 16 ký tự đó.

### Nếu dùng Outlook / Hotmail

```php
'enabled' => true,
'host' => 'smtp.office365.com',
'port' => 587,
'encryption' => 'tls',
'username' => 'yourmail@outlook.com',
'password' => 'mat_khau_email',
'from_email' => 'yourmail@outlook.com',
'from_name' => 'Eco-Q House',
```

### Nếu dùng Zoho Mail

```php
'enabled' => true,
'host' => 'smtp.zoho.com',
'port' => 587,
'encryption' => 'tls',
'username' => 'yourmail@domain.com',
'password' => 'mat_khau_hoac_app_password',
'from_email' => 'yourmail@domain.com',
'from_name' => 'Eco-Q House',
```

## 3. Khi nào email được gửi

Email sẽ được gửi khi:

- Giám đốc vào màn hình `Khách hàng`
- Chọn một khách hàng
- Phân khách đó cho một nhân viên

Sau khi phân:

- Nếu SMTP đã cấu hình đúng: hệ thống sẽ gửi email cho nhân viên
- Nếu SMTP chưa cấu hình: hệ thống vẫn phân khách bình thường, chỉ không gửi mail

## 4. Điều kiện để nhân viên nhận được mail

Tài khoản nhân viên phải có email hợp lệ trong phần `Người dùng`.

Nếu email trống hoặc sai định dạng:

- hệ thống vẫn phân khách
- nhưng sẽ không gửi email

## 5. File log kiểm tra lỗi gửi mail

Nếu cần kiểm tra mail có gửi được không, mở file:

- [storage/logs/mail.log](/D:/xampp/htdocs/EcoQnoibo/storage/logs/mail.log)

Tại đây hệ thống sẽ ghi:

- chưa cấu hình SMTP
- thiếu email người nhận
- gửi thành công
- hoặc lỗi kết nối SMTP

## 6. Cách test nhanh

1. Vào `Người dùng`
2. Đảm bảo tài khoản nhân viên có email thật
3. Mở [config/mail.php](/D:/xampp/htdocs/EcoQnoibo/config/mail.php) và điền SMTP
4. Đổi `'enabled' => false` thành `'enabled' => true`
5. Vào màn `Khách hàng`
6. Giám đốc phân một khách cho nhân viên
7. Kiểm tra hộp thư nhân viên
8. Nếu chưa thấy mail, kiểm tra file log

## 7. Gợi ý an toàn

Khi deploy thật:

- nên dùng email domain công ty thay vì Gmail cá nhân
- nên chuyển mật khẩu SMTP sang biến môi trường hoặc file cấu hình riêng không commit git
- nếu gửi mail nhiều hơn sau này, có thể nâng cấp sang PHPMailer hoặc dịch vụ như Resend / Brevo / Mailgun
