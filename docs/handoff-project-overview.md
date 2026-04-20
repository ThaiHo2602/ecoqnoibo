# EcoQ Noi Bo - Handoff Tong Quan Du An

## 1. Tong quan du an

Day la web noi bo quan ly tro cho cong ty moi gioi tro, xay dung bang:

- PHP thuan
- HTML / CSS / JavaScript
- Bootstrap 5
- MySQL / MariaDB tren XAMPP

Muc tieu cua he thong:

- Quan ly du lieu theo cau truc `He thong -> Chi nhanh -> Phong`
- Phan quyen theo 3 vai tro:
  - Giam doc
  - Quan ly
  - Nhan vien
- Ho tro quy trinh `lock phong`
- Ho tro tim phong nhanh cho nhan vien
- Ho tro quan tri noi bo cho quan ly va giam doc

## 2. Trang thai hien tai cua du an

Du an da co bo khung chay duoc va da co nhieu module nen.

### 2.1. Da hoan thanh

#### Nen tang he thong

- Front controller tai `public/index.php`
- Router tu viet cho cac route GET / POST
- Session, auth, phan quyen co ban
- Ket noi DB qua PDO
- Layout admin chung
- File rewrite Apache tai `public/.htaccess`

#### Database

Da co file schema tai:

- `database/schema.sql`

Bang hien co:

- `roles`
- `users`
- `districts`
- `systems`
- `branches`
- `rooms`
- `room_media`
- `lock_requests`
- `activity_logs`

Da co du lieu mau va 3 tai khoan test:

- `director / 123456`
- `manager / 123456`
- `staff / 123456`

#### Xac thuc va phan quyen

- Dang nhap / dang xuat
- Tu dong cap nhat `last_login_at`
- Khoa tai khoan neu khong dang nhap qua 1 tuan khi login
- Phan quyen:
  - `director`: toan quyen quan tri
  - `manager`: quan tri du lieu phong, he thong, chi nhanh, duyet lock
  - `staff`: xem phong, gui lock, doi mat khau

#### Module He thong

- Danh sach he thong
- Them / sua / xoa he thong
- Hien so chi nhanh / so phong moi he thong
- Them nhanh chi nhanh trong trang he thong

#### Module Chi nhanh

- CRUD chi nhanh
- Loc theo he thong / quan / tu khoa
- Link sang danh sach phong theo chi nhanh

#### Module Phong

Da ton tai 2 huong su dung:

1. `GET /`
   - Trang chu sau dang nhap
   - Hien thi danh sach phong dang card, theo huong gan voi mau nguoi dung yeu cau
   - Co bo loc
   - Bam vao tung card se vao trang chi tiet phong

2. `GET /rooms`
   - Trang quan tri phong
   - Dung cho quan ly / giam doc de CRUD
   - Van con dang theo kieu admin management

Da hoan thanh:

- CRUD phong
- Upload nhieu anh va video
- Luu metadata vao `room_media`
- Hien media hien co khi sua phong
- Xoa media cu khi can

#### Trang chi tiet phong

Da co route:

- `GET /rooms/{id}`

Da co:

- Gallery anh / video
- Tieu de, gia, trang thai, tag thong tin
- Mo ta / ghi chu tong
- Thong tin chi tiet phong
- Khoi vi tri placeholder
- Khoi lien he chi nhanh
- Khoi phong lien quan
- Nut `Gui yeu cau lock` cho nhan vien neu phong dang `chua_lock`
- Nut `Chinh sua phong` cho quan ly / giam doc

Luu y:

- Da bo popup chi tiet lam luong chinh
- Trang chi tiet rieng la huong moi duoc uu tien

#### Workflow lock phong

Da co:

- Nhan vien gui yeu cau lock
- He thong tao `lock_request`
- Phong chuyen sang `dang_giu`
- Quan ly / giam doc vao trang `GET /lock-requests`
- Duyet hoac tu choi yeu cau
- Neu duyet:
  - request -> `approved`
  - room -> `da_lock`
- Neu tu choi:
  - request -> `rejected`
  - room -> `chua_lock`

#### Module nguoi dung

Da co:

- `GET /users` cho giam doc
- Them / sua / xoa nguoi dung
- Gan vai tro
- Khoa / mo khoa bang account_status

#### Doi mat khau

Da co:

- `GET /profile/password`
- `POST /profile/password`
- Cho phep tai khoan dang dang nhap tu doi mat khau

## 3. Van de da gap va da xu ly

### 3.1. Loi `Not Found` khi vao `/public/login`

Nguyen nhan:

- Apache tim file vat ly `public/login`
- Chua rewrite route dong ve `public/index.php`

Da xu ly:

- Them `public/.htaccess`

Nhung de chay duoc tren may can:

- Bat `mod_rewrite`
- `AllowOverride All`
- Restart Apache

Neu AI khac tiep quan va user van bao loi 404:

- Kiem tra lai `httpd.conf`
- Kiem tra virtual host / Directory cua XAMPP
- Kiem tra `RewriteBase /EcoQnoibo/public/`

### 3.2. Van de encoding tieng Viet

Mot so file moi da duoc dua sang tieng Viet co dau.
Tuy nhien khi doc bang terminal PowerShell co the thay ky tu bi vo.

Danh gia hien tai:

- Browser co the van hien dung neu file duoc doc voi UTF-8
- Nhung can rat can than voi encoding file khi chinh sua tiep

Khuyen nghi cho AI / dev tiep theo:

- Chuan hoa tat ca file view / controller ve UTF-8 without BOM neu co the
- Kiem tra truc tiep tren browser, khong chi dua vao `Get-Content` terminal
- Neu thay vo chu tren browser, uu tien sua encoding cua file truoc khi sua noi dung

## 4. Kien truc route hien tai

### Auth

- `GET /login`
- `POST /login`
- `POST /logout`

### Home / Dashboard

- `GET /`
  - Listing phong dang card
- `GET /dashboard`
  - Bang dieu khien quan tri

### Systems / Branches

- `GET /systems`
- `POST /systems/store`
- `POST /systems/update`
- `POST /systems/delete`
- `GET /branches`
- `POST /branches/store`
- `POST /branches/update`
- `POST /branches/delete`

### Rooms

- `GET /rooms`
- `GET /rooms/{id}`
- `POST /rooms/store`
- `POST /rooms/update`
- `POST /rooms/delete`

### Lock requests

- `GET /lock-requests`
- `POST /lock-requests/store`
- `POST /lock-requests/approve`
- `POST /lock-requests/reject`

### Users / Profile

- `GET /users`
- `POST /users/store`
- `POST /users/update`
- `POST /users/delete`
- `GET /profile/password`
- `POST /profile/password`

## 5. Cac file quan trong nen doc truoc khi lam tiep

### Nen tang

- `bootstrap.php`
- `config/app.php`
- `public/index.php`
- `public/.htaccess`

### Controller

- `app/Controllers/RoomController.php`
- `app/Controllers/LockRequestController.php`
- `app/Controllers/UserController.php`
- `app/Controllers/ProfileController.php`
- `app/Controllers/DashboardController.php`

### View

- `resources/views/layouts/app.php`
- `resources/views/home/index.php`
- `resources/views/rooms/index.php`
- `resources/views/rooms/show.php`
- `resources/views/locks/index.php`
- `resources/views/users/index.php`
- `resources/views/profile/password.php`

### Style

- `public/assets/css/app.css`

### Tai lieu

- `docs/project-spec.md`
- `README.md`

## 6. Phan da lam theo yeu cau giao dien moi

Theo yeu cau user, da chot:

- Toan bo web can chuyen dan sang tieng Viet co dau
- Trang chu sau dang nhap phai la listing phong dang card
- Trang chi tiet phong la trang rieng, khong lay popup lam luong chinh
- Giao dien listing / detail can gan voi mau card bat dong san / phong tro
- Khong lam khung danh gia khach hang o trang chi tiet

Da trien khai mot phan lon cua huong nay:

- `/` da tro thanh listing phong
- `/dashboard` da tach rieng
- `/rooms/{id}` da ton tai
- CSS da co them block listing / detail

## 7. Van de ton dong quan trong

### 7.1. Chua Viet hoa 100%

Van con kha nhieu man hinh cu / text cu co the con:

- khong dau
- hoac da co dau nhung chua chac dong bo

Can ra soat:

- `systems`
- `branches`
- `locks`
- `users`
- `profile`
- `errors 403/500`
- thong bao flash con sot

### 7.2. Chua co trang bao cao thong ke

Theo nghiep vu ban dau, chua lam:

- so phong lock theo ngay / tuan / thang / quy
- top nhan vien lock
- top he thong lock nhieu / it

### 7.3. Chua co trang nhat ky hoat dong giao dien

Database da co `activity_logs`, nhung chua co:

- giao dien xem log
- bo loc log
- hien ai lam gi, luc nao

### 7.4. Chua toi uu trang home theo muc pixel-perfect voi mau

Trang home listing da co huong card, nhung AI tiep theo co the can:

- tinh chinh spacing
- tinh chinh typography
- badge / tag / card shadow
- sap xep bo loc gon hon
- cai thien mobile

### 7.5. Chi tiet phong chua co ban do that

Hien moi la placeholder.
Neu can thi buoc sau co the:

- nhung Google Maps / iframe
- hoac de placeholder dep hon

### 7.6. Chua co phan trang that

Hien tai danh sach phong dang lay het theo filter.
Can them:

- phan trang 30 item / trang o listing chinh
- co the ap dung cho rooms admin

## 8. Thu tu uu tien de lam tiep

AI khac nen di theo thu tu nay de it vo flow nhat:

### Uu tien 1 - Don dep va dong bo giao dien moi

- Ra soat va doi toan bo giao dien sang tieng Viet co dau
- Fix encoding neu browser hien sai chu
- Tinh chinh `home/index.php` va `rooms/show.php` cho gan mau hon

### Uu tien 2 - Bao cao thong ke

Them route + controller + view cho:

- bao cao theo ngay / tuan / thang / quy
- top nhan vien
- top he thong

Nen dung du lieu tu:

- `lock_requests`
- `rooms`
- `branches`
- `systems`
- `users`

### Uu tien 3 - Nhat ky hoat dong

Them:

- route `GET /activity-logs` hoac tuong duong
- man hinh cho giam doc
- loc theo module / action / user / thoi gian

### Uu tien 4 - Toi uu listing / detail

- phan trang that cho home listing
- sort theo gia / moi cap nhat
- card dep hon
- room related thong minh hon

### Uu tien 5 - Nang cap admin

- cai thien trang `/rooms` de de dung hon
- bo sung quick actions tu trang detail
- co the tach biet ro hon giua giao dien nhan vien va giao dien admin

## 9. Tieu chi test AI tiep theo nen kiem tra

Sau moi dot sua lon, nen test toi thieu:

1. `GET /login`
2. Dang nhap voi `staff`
3. Vao `/`
4. Bam card vao `/rooms/{id}`
5. Gui lock phong `chua_lock`
6. Dang nhap `manager`
7. Vao `/lock-requests`
8. Duyet hoac tu choi
9. Vao lai chi tiet phong xem status da doi dung
10. Dang nhap `director`
11. Vao `/users`
12. Tao / sua / xoa user
13. Doi mat khau tai `/profile/password`

Neu user bao loi 404:

- check `.htaccess`
- check mod_rewrite
- check AllowOverride

Neu user bao loi vo dau:

- check encoding file
- check browser render truoc, khong chi dua vao terminal

## 10. Ghi chu quan trong cho AI tiep theo

- Khong nen quay lai popup chi tiet lam luong chinh; huong moi la trang detail rieng.
- Khong nen doi schema DB neu chua that su can, vi user da chap nhan ban hien tai khong co field nhu dien tich / favorite / rating.
- Neu can them route dong khac, co the tiep tuc mo rong co che trong `public/index.php`.
- Nho giu phan quyen:
  - staff: xem, loc, gui lock, doi mat khau
  - manager: CRUD he thong / chi nhanh / phong, duyet lock
  - director: them quyen users, logs, reports

## 11. Ket luan ngan

Du an da qua giai doan "bo khung".
Hien tai no da co:

- auth
- phan quyen
- CRUD he thong / chi nhanh / phong
- upload media
- listing phong dang card
- trang chi tiet phong rieng
- workflow lock
- quan ly nguoi dung
- doi mat khau

Do do, AI tiep theo nen xem day la giai doan:

- polish UI
- hoan tat Vietnamese UI
- them reports
- them logs
- toi uu trai nghiem van hanh
