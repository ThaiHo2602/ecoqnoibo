# EcoQ Noi Bo

Bo khung ban dau cho he thong quan ly tro noi bo bang PHP thuan.

## 1. Khoi tao database

1. Tao database va bang bang file [database/schema.sql](/D:/xampp/htdocs/EcoQnoibo/database/schema.sql:1)
2. Import file SQL vao MySQL trong XAMPP
3. Dieu chinh ket noi trong [config/app.php](/D:/xampp/htdocs/EcoQnoibo/config/app.php:1) neu can

Tai khoan mau sau khi import:

- `director` / `123456`
- `manager` / `123456`
- `staff` / `123456`

## 2. Chay local

Dat project trong `htdocs`, sau do truy cap:

- `http://localhost/EcoQnoibo/public`

Neu ban go thang vao URL nhu `http://localhost/EcoQnoibo/public/login` ma bi `Not Found`, nguyen nhan la Apache chua rewrite route dong ve [public/index.php](/D:/xampp/htdocs/EcoQnoibo/public/index.php:1).
Project nay da duoc bo sung file [public/.htaccess](/D:/xampp/htdocs/EcoQnoibo/public/.htaccess:1), nhung Apache can bat `mod_rewrite` va cho phep `.htaccess` hoat dong.

Voi XAMPP, hay kiem tra:

1. Trong `httpd.conf`, dong `LoadModule rewrite_module modules/mod_rewrite.so` khong bi comment.
2. Virtual host hoac `Directory` cho `htdocs` dang de `AllowOverride All`.
3. Sau khi sua, restart Apache.

## 3. Giai doan hien tai

Da co:

- Schema database nen
- Dang nhap / dang xuat
- Session va phan quyen co ban
- Dashboard tong quan
- Layout admin de mo rong
- CRUD he thong
- CRUD chi nhanh va bo loc chi nhanh co ban
- CRUD phong va bo loc phong co ban
- Upload nhieu anh va video cho phong
- Workflow gui lock va duyet lock co ban
- Popup chi tiet phong
- Quan ly nguoi dung cho giam doc
- Doi mat khau cho tai khoan dang dang nhap

Chua co:

- Bao cao va nhat ky giao dien
- Trang bao cao thong ke va nhat ky day du
