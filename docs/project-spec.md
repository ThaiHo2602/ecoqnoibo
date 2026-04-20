# EcoQ Noi Bo - Project Specification

## 1. Muc tieu

Xay dung mot trang web noi bo de quan ly tro cho cong ty moi gioi tro, su dung PHP thuan, HTML, CSS, JavaScript va cac thu vien giao dien phu hop de giao dien dep, chuyen nghiep, de mo rong.

He thong phuc vu 3 nhom nguoi dung:

- Giam doc
- Quan ly
- Nhan vien

## 2. Cau truc nghiep vu

Du lieu duoc quan ly theo cau truc:

- He thong
- Chi nhanh
- Phong

Quan he:

- Mot he thong co nhieu chi nhanh
- Mot chi nhanh thuoc duy nhat mot he thong
- Mot chi nhanh co nhieu phong
- Mot phong thuoc duy nhat mot chi nhanh

## 3. Doi tuong du lieu

### 3.1. He thong

Thong tin:

- Ten he thong
- Mo ta ngan (tuy chon)
- Trang thai hoat dong
- Thoi gian tao / cap nhat

### 3.2. Chi nhanh

Thong tin:

- Thuoc he thong nao
- Ten chi nhanh
- Dia chi chi nhanh
- Quan
- So dien thoai quan ly
- Thoi gian tao / cap nhat

### 3.3. Phong

Thong tin:

- Thuoc chi nhanh nao
- So phong
- Gia phong
- Hinh anh: nhieu hinh
- Video: cho phep upload video
- Loai phong: co gac / khong gac
- Tien dien
- Tien nuoc
- Phi dich vu
- Phi gui xe
- Trang thai: chua lock / dang giu / da lock
- Noi that: co noi that / khong noi that
- Ban cong: co / khong
- Loai cua so: cua so troi / cua so hanh lang / cua so gieng troi
- Ghi chu tong
- Thoi gian tao / cap nhat

Luu y:

- Trang thai phong giu dung theo mo ta nghiep vu da thong nhat.
- Khi quan ly tu choi lock, trang thai phong quay ve `chua lock`.

### 3.4. Nguoi dung

Thong tin:

- Ho ten
- Chuc vu
- So dien thoai (khong bat buoc)
- Email (khong bat buoc)
- Tai khoan
- Mat khau
- Vai tro
- Trang thai tai khoan: hoat dong / bi khoa
- Lan dang nhap cuoi
- Thoi gian tao / cap nhat

Rule:

- Nhan vien nao khong dang nhap trong 1 tuan se bi khoa tai khoan.

## 4. Vai tro va phan quyen

### 4.1. Giam doc

Toan quyen:

- Quan ly nhan su: tao / sua / xoa / khoa
- Quan ly he thong
- Quan ly chi nhanh
- Quan ly phong
- Duyet lock phong
- Xem bao cao thong ke
- Xem nhat ky hoat dong

### 4.2. Quan ly

Quyen:

- Them / sua / xoa he thong
- Them / sua / xoa chi nhanh
- Them / sua / xoa phong
- Duyet hoac tu choi yeu cau lock phong
- Xem nhat ky hoat dong
- Xem bao cao thong ke

### 4.3. Nhan vien

Quyen:

- Xem thong tin phong
- Tim kiem / loc phong
- Xem chi tiet phong
- Gui yeu cau lock phong
- Doi mat khau

Khong duoc:

- Duyet lock
- CRUD he thong / chi nhanh / phong / nhan su

## 5. Quy trinh lock phong

### 5.1. Nhan vien gui lock

- Nhan vien bam nut `Lock` tren phong
- He thong tao mot yeu cau lock moi
- Trang thai phong chuyen thanh `dang giu`
- Yeu cau duoc gui ve cho quan ly / giam doc de xet duyet

### 5.2. Quan ly / Giam doc duyet

Neu dong y:

- Yeu cau lock duoc danh dau da duyet
- Trang thai phong chuyen thanh `da lock`

Neu tu choi:

- Yeu cau lock duoc danh dau tu choi
- Trang thai phong quay ve `chua lock`

Thong tin can luu trong yeu cau lock:

- Phong nao
- Nhan vien nao gui
- Thoi gian gui
- Trang thai yeu cau
- Nguoi duyet
- Thoi gian duyet
- Ghi chu duyet / ly do tu choi (tuy chon)

## 6. Giao dien va man hinh

### 6.1. Header / dang nhap

- Header co muc dang nhap
- Sau khi dang nhap thanh cong, nguoi dung vao trang noi bo theo vai tro

### 6.2. Trang chu sau dang nhap

- Hien danh sach tro / phong
- Phan trang khoang 30 item / trang
- Moi item hien:
  - Ten chi nhanh
  - Dia chi
  - So phong
  - Gia
- Bam vao item se hien popup chi tiet phong
- Popup hien day du thong tin phong
- Quan ly va giam doc co the sua truc tiep tren popup neu bam `Chinh sua`
- Nhan vien co the bam `Lock` de gui yeu cau

### 6.3. Bo loc / tim kiem

Can tap trung lam ky bo loc:

- Theo quan
- Theo gia
- Theo trang thai
- Theo noi that

Nen mo rong them:

- Theo he thong
- Theo chi nhanh
- Theo loai phong
- Theo ban cong
- Theo loai cua so

### 6.4. Trang He thong

- Hien danh sach he thong
- Bam vao mot he thong se dropdown danh sach chi nhanh thuoc he thong do
- Co the them / sua / xoa chi nhanh truc tiep tai day

### 6.5. Trang Chi nhanh

- Hien danh sach chi nhanh
- Bam vao mot chi nhanh se hien danh sach phong thuoc chi nhanh
- Danh sach phong co the hien dang popup / modal
- Co the them / sua / xoa phong truc tiep

### 6.6. Trang Phong

- Hien danh sach toan bo phong
- Dang list / bang
- Khong can hien hinh anh o che do danh sach
- Bam vao phong se mo form chi tiet de sua
- Quan ly va giam doc duoc them / sua / xoa

### 6.7. Trang Duyet lock

Chi cho:

- Quan ly
- Giam doc

Chuc nang:

- Xem danh sach yeu cau lock
- Loc theo trang thai / nhan vien / thoi gian
- Duyet hoac tu choi tung yeu cau

### 6.8. Trang Quan ly nguoi dung

Chi cho:

- Giam doc

Chuc nang:

- Tao / sua / xoa nhan vien
- Khoa / mo khoa tai khoan
- Xem vai tro va trang thai tai khoan

### 6.9. Trang Nhat ky hoat dong

Chi cho:

- Giam doc

Quan ly co the duoc xem theo nghiep vu da thong nhat.

Can hien:

- Moi thao tac CRUD
- Ai gui lock phong
- Ai duyet / tu choi
- Ai cap nhat phong, chi nhanh, he thong, nguoi dung
- Moc thoi gian

### 6.10. Trang Bao cao thong ke

Danh cho:

- Quan ly
- Giam doc

Bao cao:

- So phong duoc lock theo ngay / tuan / thang / quy
- So luong lock theo nhan vien
- Top 10 nhan vien lock duoc nhieu phong
- Top he thong duoc lock nhieu nhat
- Top he thong duoc lock it nhat

## 7. Cong nghe de xuat

Backend:

- PHP thuan
- Kien truc theo huong tach lop MVC nhe

Frontend:

- HTML5
- CSS3
- JavaScript
- Bootstrap 5
- DataTables
- SweetAlert2

Database:

- MySQL / MariaDB

Luu tru media:

- Upload file vao thu muc noi bo
- Anh va video duoc luu metadata trong database

Bao mat:

- Hash password bang `password_hash()`
- Xac thuc bang session
- Kiem tra phan quyen theo role
- Validate upload file

## 8. Database du kien

Bang toi thieu:

- `roles`
- `users`
- `systems`
- `branches`
- `districts`
- `rooms`
- `room_media`
- `lock_requests`
- `activity_logs`

## 9. Giai doan trien khai

### Giai doan 1

- Khoi tao project PHP thuan
- Tao cau truc thu muc
- Ket noi database
- Dang nhap / dang xuat / session / phan quyen
- Layout admin chung

### Giai doan 2

- CRUD he thong
- CRUD chi nhanh
- CRUD phong
- Upload anh / video cho phong

### Giai doan 3

- Trang chu noi bo va popup chi tiet phong
- Bo loc nang cao
- Gui yeu cau lock
- Duyet lock

### Giai doan 4

- Quan ly nguoi dung
- Doi mat khau
- Tu dong khoa tai khoan neu khong dang nhap 1 tuan
- Nhat ky hoat dong

### Giai doan 5

- Dashboard
- Bao cao thong ke
- Toi uu giao dien va trai nghiem su dung
- Kiem thu phan quyen va nghiep vu
