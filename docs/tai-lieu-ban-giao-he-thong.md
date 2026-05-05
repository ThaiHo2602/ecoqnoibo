# Tài liệu bàn giao hệ thống Eco-Q House

## 1. Tổng quan hệ thống

Eco-Q House là một website gồm 2 phần chính:

- Landing page công ty: giới thiệu thương hiệu, dự án/phòng nổi bật, tin tức, tuyển dụng, liên hệ.
- Hệ thống nội bộ: dùng cho giám đốc, quản lý, nhân viên, cộng tác viên để vận hành dữ liệu phòng, khách hàng, lock phòng và các nghiệp vụ liên quan.

Mục tiêu của hệ thống:

- Quản lý dữ liệu theo mô hình `Hệ thống -> Chi nhánh -> Phòng`.
- Quản lý khách hàng và phân khách cho nhân viên.
- Quản lý quy trình lock phòng.
- Hỗ trợ landing page giới thiệu dự án, tin tức, tuyển dụng.

Công nghệ sử dụng:

- PHP thuần
- HTML, CSS, JavaScript
- Bootstrap 5
- MySQL / MariaDB
- Apache `.htaccess`

## 2. Cấu trúc thư mục quan trọng

### Nền tảng và cấu hình

- [bootstrap.php](D:\xampp\htdocs\EcoQnoibo\bootstrap.php): khởi tạo app, helper, config.
- [config/app.php](D:\xampp\htdocs\EcoQnoibo\config\app.php): cấu hình app, domain, database.
- [config/mail.php](D:\xampp\htdocs\EcoQnoibo\config\mail.php): cấu hình SMTP gửi mail.
- [public/index.php](D:\xampp\htdocs\EcoQnoibo\public\index.php): front controller và router chính.
- [\.htaccess](D:\xampp\htdocs\EcoQnoibo\.htaccess): rewrite tầng root.
- [public/.htaccess](D:\xampp\htdocs\EcoQnoibo\public\.htaccess): rewrite trong thư mục `public`.

### Hệ thống nội bộ

- [app/Controllers](D:\xampp\htdocs\EcoQnoibo\app\Controllers): controller nghiệp vụ.
- [resources/views/layouts/app.php](D:\xampp\htdocs\EcoQnoibo\resources\views\layouts\app.php): layout nội bộ.
- [resources/views/home/index.php](D:\xampp\htdocs\EcoQnoibo\resources\views\home\index.php): trang chủ nội bộ dạng card phòng.
- [resources/views/rooms](D:\xampp\htdocs\EcoQnoibo\resources\views\rooms): quản lý phòng và chi tiết phòng.
- [resources/views/customers](D:\xampp\htdocs\EcoQnoibo\resources\views\customers): quản lý khách hàng.
- [public/assets/css/app.css](D:\xampp\htdocs\EcoQnoibo\public\assets\css\app.css): CSS chính của nội bộ.

### Landing page công ty

- [public/company/index.php](D:\xampp\htdocs\EcoQnoibo\public\company\index.php): trang chủ public.
- [public/company/tin-tuc.php](D:\xampp\htdocs\EcoQnoibo\public\company\tin-tuc.php): danh sách tin tức.
- [public/company/bai-viet.php](D:\xampp\htdocs\EcoQnoibo\public\company\bai-viet.php): trang chi tiết tin tức theo `slug`.
- [public/company/tuyen-dung.php](D:\xampp\htdocs\EcoQnoibo\public\company\tuyen-dung.php): danh sách tuyển dụng.
- [public/company/viec-lam.php](D:\xampp\htdocs\EcoQnoibo\public\company\viec-lam.php): chi tiết tuyển dụng theo `slug`.
- [public/company/assets/css/style.css](D:\xampp\htdocs\EcoQnoibo\public\company\assets\css\style.css): CSS landing page.

### Dữ liệu nội dung public

- [public/company/data/news-content.php](D:\xampp\htdocs\EcoQnoibo\public\company\data\news-content.php): dữ liệu bài viết tin tức.
- [public/company/data/jobs.php](D:\xampp\htdocs\EcoQnoibo\public\company\data\jobs.php): dữ liệu bài tuyển dụng.

### Database

- [database/schema.sql](D:\xampp\htdocs\EcoQnoibo\database\schema.sql): schema chính.
- [database/sample_data_large_seed.sql](D:\xampp\htdocs\EcoQnoibo\database\sample_data_large_seed.sql): dữ liệu mẫu lớn.
- [database/customer_module_patch.sql](D:\xampp\htdocs\EcoQnoibo\database\customer_module_patch.sql): patch khách hàng.
- [database/customer_assignment_confirmation_patch.sql](D:\xampp\htdocs\EcoQnoibo\database\customer_assignment_confirmation_patch.sql): patch xác nhận phân khách.
- [database/remember_login_patch.sql](D:\xampp\htdocs\EcoQnoibo\database\remember_login_patch.sql): patch ghi nhớ đăng nhập.
- [database/public_room_visibility_patch.sql](D:\xampp\htdocs\EcoQnoibo\database\public_room_visibility_patch.sql): patch hiển thị phòng ra landing.

## 3. Vai trò và quyền hiện tại

### Giám đốc

- Toàn quyền trong hệ thống nội bộ.
- Quản lý hệ thống, chi nhánh, phòng, người dùng.
- Xem báo cáo, nhật ký hoạt động.
- Thêm khách hàng, phân khách cho nhân viên.
- Chọn phòng nào được hiển thị ra landing page/public.

### Quản lý

- Quản lý hệ thống, chi nhánh, phòng.
- Duyệt hoặc từ chối lock phòng.
- Xem báo cáo.
- Theo dõi khách hàng và vận hành nội bộ.

### Nhân viên

- Xem danh sách phòng và trang chi tiết phòng.
- Gửi yêu cầu lock phòng.
- Nhận khách từ giám đốc.
- Xác nhận nhận khách hoặc từ chối nhận khách có lý do.
- Cập nhật tiến độ khách hàng: hoàn thành, hủy, dời lịch, cọc phòng.

### Cộng tác viên

- Chỉ dùng `Trang chủ`, `Khách hàng`, `Đổi mật khẩu`.
- Thêm khách hàng của mình vào hệ thống.
- Xem danh sách khách do mình tạo.
- Không được xóa khách sau khi khách đã được giám đốc phân cho nhân viên.

## 4. Route và màn hình chính

### Public / landing

- `/`: trang chủ công ty.
- `/gioi-thieu`
- `/du-an`
- `/chi-tiet-du-an`
- `/tin-tuc`
- `/bai-viet?slug=...`
- `/tuyen-dung`
- `/viec-lam?slug=...`
- `/lien-he`

### Nội bộ

- `/login`: đăng nhập.
- `/app`: trang chủ nội bộ.
- `/dashboard`: bảng điều khiển.
- `/systems`: quản lý hệ thống.
- `/branches`: quản lý chi nhánh.
- `/rooms`: quản lý phòng.
- `/rooms/{id}`: chi tiết phòng.
- `/customers`: khách hàng.
- `/lock-requests`: duyệt/yêu cầu lock.
- `/reports`: báo cáo.
- `/users`: người dùng.
- `/activity-logs`: nhật ký hoạt động.
- `/profile/password`: đổi mật khẩu.

## 5. Cách sử dụng khu nội bộ

### 5.1. Đăng nhập

- Truy cập `/login`.
- Sau khi đăng nhập thành công, hệ thống đưa vào `/app`.
- Đã có cơ chế ghi nhớ đăng nhập, nhưng cần database có đủ patch và hosting không chặn cookie.

### 5.2. Trang chủ nội bộ

Trang này hiển thị:

- thống kê nhanh số phòng
- bộ lọc phòng
- danh sách phòng dạng card
- bấm vào card để vào chi tiết phòng

Các bộ lọc hiện có:

- quận
- giá từ / giá đến
- trạng thái
- nội thất
- hệ thống
- chi nhánh
- từ khóa

### 5.3. Hệ thống và chi nhánh

Ở trang `Hệ thống`:

- quản lý danh sách hệ thống
- mỗi hệ thống có thể xổ dropdown để xem các chi nhánh bên trong
- có thể thêm, sửa, xóa hệ thống
- có thể thao tác chi nhánh từ trong từng hệ thống

Ở trang `Chi nhánh`:

- quản lý toàn bộ chi nhánh
- lọc theo hệ thống, quận, từ khóa
- xem và chuyển tiếp sang danh sách phòng liên quan

### 5.4. Quản lý phòng

Trang `Phòng` dùng để vận hành dữ liệu phòng:

- thêm phòng mới bằng nút `Thêm phòng`
- sửa phòng bằng nút `Sửa`
- form thêm/sửa hiện dưới dạng modal nổi
- xóa phòng
- lọc phòng theo nhiều tiêu chí

Thông tin phòng hiện lưu gồm:

- số phòng
- chi nhánh
- giá
- loại phòng
- điện, nước, dịch vụ, xe
- trạng thái
- nội thất
- ban công
- loại cửa sổ
- ghi chú tổng
- media ảnh/video
- cờ hiển thị public

### 5.5. Chi tiết phòng

Ở trang chi tiết phòng:

- xem gallery ảnh/video
- xem toàn bộ thông tin phòng
- xem phòng liên quan
- nhân viên có thể gửi yêu cầu lock nếu phòng đang `Chưa lock`
- quản lý và giám đốc có thêm thao tác chỉnh sửa

Lưu ý:

- chức năng tải ảnh đã được đổi từ file zip sang tải từng ảnh
- có lightbox/phóng to ảnh và tải ảnh riêng lẻ
- phần này nên kiểm tra lại thực tế trên hosting sau khi deploy

### 5.6. Quy trình lock phòng

Luồng hiện tại:

1. Nhân viên mở chi tiết phòng.
2. Nếu phòng `Chưa lock`, nhân viên bấm gửi yêu cầu lock.
3. Hệ thống tạo `lock_request`, phòng chuyển `Đang giữ`.
4. Quản lý hoặc giám đốc vào `Duyệt lock`.
5. Nếu duyệt:
   - request thành `approved`
   - phòng thành `Đã lock`
6. Nếu từ chối:
   - request thành `rejected`
   - phòng quay về `Chưa lock`

### 5.7. Khách hàng

Module khách hàng hiện là phần nghiệp vụ lớn của hệ thống.

#### Cộng tác viên

- thêm khách mới
- xem khách do mình tạo
- sửa khách của mình
- chỉ được xóa nếu khách chưa bị phân cho nhân viên

#### Giám đốc

- xem toàn bộ khách
- thêm khách mới
- lọc theo tên, số điện thoại, nhân viên phụ trách, cộng tác viên, ngày, tháng
- ưu tiên hiển thị khách chưa phân lên đầu
- phân khách cho nhân viên
- nếu khách đang `chờ xác nhận`, vẫn có thể đổi sang nhân viên khác
- nếu nhân viên đã xác nhận nhận khách thì không được đổi nữa

#### Nhân viên

- nhận khách được phân
- xác nhận nhận khách hoặc từ chối và nhập lý do
- nếu xác nhận thì xử lý tiến độ khách
- có thể đánh dấu hoàn thành, hủy, dời lịch
- nếu khách cọc phòng, nhân viên chọn phòng và phát sinh quy trình lock

### 5.8. Người dùng

Trang `Người dùng` dành cho giám đốc:

- thêm tài khoản mới
- sửa tài khoản
- xóa tài khoản
- đổi vai trò
- đổi trạng thái tài khoản

Lưu ý:

- trường `Chức vụ` đã được bỏ khỏi form vì bị trùng với `Vai trò`
- hiển thị chức danh nên dùng `display_name` của role

### 5.9. Báo cáo và nhật ký hoạt động

Đã có route và màn hình:

- `Báo cáo`
- `Nhật ký hoạt động`

Tuy nhiên cần kiểm tra lại kỹ sau deploy vì đây là phần dễ bị ảnh hưởng bởi PJAX/Chart.js.

## 6. Cách đăng tin tức

Hiện tại phần tin tức public chưa dùng database. Dữ liệu được khai báo thủ công trong file:

- [public/company/data/news-content.php](D:\xampp\htdocs\EcoQnoibo\public\company\data\news-content.php)

### 6.1. Cấu trúc một bài viết

Mỗi bài viết là một phần tử trong mảng trả về từ `ecoq_news_items()`, gồm các trường chính:

- `slug`
- `aliases`
- `url`
- `title`
- `excerpt`
- `date`
- `category`
- `author`
- `reading_minutes`
- `image`
- `hero_image`
- `meta_description`
- `topics`
- `intro`
- `sections`
- `cta_title`
- `cta_text`

### 6.2. Cách thêm bài mới

1. Mở file [public/company/data/news-content.php](D:\xampp\htdocs\EcoQnoibo\public\company\data\news-content.php).
2. Thêm một phần tử mới trong mảng `return`.
3. Điền đầy đủ:
   - `slug`: khóa duy nhất, không dấu, dùng gạch ngang
   - `url`: thường là `bai-viet?slug=slug-cua-ban`
   - `title`, `excerpt`, `date`, `category`, `author`
   - `image`, `hero_image`
   - `intro` và `sections`
4. Lưu file.
5. Truy cập:
   - `/tin-tuc` để xem bài xuất hiện trong danh sách
   - `/bai-viet?slug=...` để xem chi tiết

### 6.3. Cách sửa bài cũ

- Sửa trực tiếp phần tử tương ứng trong [public/company/data/news-content.php](D:\xampp\htdocs\EcoQnoibo\public\company\data\news-content.php).
- Không đổi `slug` nếu đang muốn giữ link cũ.
- Nếu bắt buộc đổi `slug`, nên thêm slug cũ vào `aliases` để tránh gãy liên kết.

### 6.4. Cách xóa bài

- Xóa phần tử bài viết trong [public/company/data/news-content.php](D:\xampp\htdocs\EcoQnoibo\public\company\data\news-content.php).
- Kiểm tra lại trang `Tin tức` và các block tin trên trang chủ public.

## 7. Cách đăng tuyển dụng

Hiện tại tuyển dụng public cũng chưa dùng database. Dữ liệu được lưu trong:

- [public/company/data/jobs.php](D:\xampp\htdocs\EcoQnoibo\public\company\data\jobs.php)

### 7.1. Cấu trúc một bài tuyển dụng

Mỗi bài tuyển dụng là một phần tử trong mảng `$jobItems`, gồm:

- `slug`
- `title`
- `excerpt`
- `date`
- `location`
- `salary`
- `type`
- `deadline`
- `experience`
- `openings`
- `department`
- `image`
- `summary`
- `responsibilities`
- `requirements`
- `benefits`
- `schedule`

### 7.2. Cách thêm tin tuyển dụng

1. Mở [public/company/data/jobs.php](D:\xampp\htdocs\EcoQnoibo\public\company\data\jobs.php).
2. Thêm một phần tử mới vào mảng `$jobItems`.
3. Điền các trường bắt buộc.
4. Đảm bảo `slug` là duy nhất.
5. Kiểm tra lại:
   - `/tuyen-dung`
   - `/viec-lam?slug=...`

### 7.3. Cách sửa hoặc xóa tin tuyển dụng

- Sửa trực tiếp phần tử tương ứng trong [public/company/data/jobs.php](D:\xampp\htdocs\EcoQnoibo\public\company\data\jobs.php).
- Nếu xóa, xóa cả phần tử khỏi mảng.
- Nếu đổi `slug`, phải kiểm tra lại toàn bộ link cũ.

## 8. Dữ liệu và lưu ý database

### 8.1. File schema và patch

Khi dựng hệ thống mới:

1. import [database/schema.sql](D:\xampp\htdocs\EcoQnoibo\database\schema.sql)
2. import các patch cần thiết:
   - [database/customer_module_patch.sql](D:\xampp\htdocs\EcoQnoibo\database\customer_module_patch.sql)
   - [database/customer_assignment_confirmation_patch.sql](D:\xampp\htdocs\EcoQnoibo\database\customer_assignment_confirmation_patch.sql)
   - [database/remember_login_patch.sql](D:\xampp\htdocs\EcoQnoibo\database\remember_login_patch.sql)
   - [database/public_room_visibility_patch.sql](D:\xampp\htdocs\EcoQnoibo\database\public_room_visibility_patch.sql)

Nếu cần dữ liệu demo:

- import thêm [database/sample_data_large_seed.sql](D:\xampp\htdocs\EcoQnoibo\database\sample_data_large_seed.sql)

### 8.2. Quy ước bảng roles

Rất quan trọng:

- `roles.name` phải là khóa kỹ thuật không dấu:
  - `director`
  - `manager`
  - `staff`
  - `collaborator`
- `roles.display_name` mới là nơi chứa tiếng Việt:
  - `Giám đốc`
  - `Quản lý`
  - `Nhân viên`
  - `Cộng tác viên`

Không nên đổi `roles.name` sang tiếng Việt có dấu, vì dễ làm hỏng kiểm tra phân quyền và menu.

## 9. Tài liệu tham khảo kèm theo

- [docs/project-spec.md](D:\xampp\htdocs\EcoQnoibo\docs\project-spec.md): mô tả nghiệp vụ và scope.
- [docs/handoff-project-overview.md](D:\xampp\htdocs\EcoQnoibo\docs\handoff-project-overview.md): ghi chú bàn giao cũ.
- [docs/deployment-hosting-guide.md](D:\xampp\htdocs\EcoQnoibo\docs\deployment-hosting-guide.md): hướng dẫn đưa web lên hosting.
- [docs/mail-setup.md](D:\xampp\htdocs\EcoQnoibo\docs\mail-setup.md): cấu hình SMTP và email.

## 10. Vấn đề tồn đọng và rủi ro hiện tại

Đây là phần quan trọng nhất khi bàn giao.

### 10.1. CSS khu nội bộ trên hosting chưa ổn định

Hiện đã từng xảy ra tình trạng:

- trang nội bộ bị vỡ layout
- CSS của `/app` không áp dụng đúng
- DevTools báo stylesheet bị lỗi URL
- có lúc URL `app.css` trả về HTML thay vì CSS

Khả năng cao liên quan đến:

- rewrite asset trên hosting
- đường dẫn `/assets/...` bị server xử lý sai
- cache hosting / OPcache

Những file đã động đến trong quá trình vá:

- [resources/views/layouts/app.php](D:\xampp\htdocs\EcoQnoibo\resources\views\layouts\app.php)
- [resources/views/layouts/app-inline-css.php](D:\xampp\htdocs\EcoQnoibo\resources\views\layouts\app-inline-css.php)
- [public/assets/css/app.css](D:\xampp\htdocs\EcoQnoibo\public\assets\css\app.css)

Khuyến nghị người tiếp nhận:

- kiểm tra trực tiếp URL CSS trên host
- xác nhận response header là `text/css`
- kiểm tra asset nội bộ có nên dùng `/public/assets/...` cố định hay không
- kiểm tra `.htaccess` để tránh rewrite nhầm asset sang route HTML

### 10.2. Tin tức public đang phụ thuộc file dữ liệu tĩnh

Hiện:

- chưa có CMS
- chưa có giao diện admin để đăng tin
- mọi bài viết đều sửa tay trong [public/company/data/news-content.php](D:\xampp\htdocs\EcoQnoibo\public\company\data\news-content.php)

Ngoài ra đã từng phát sinh lỗi:

- `/bai-viet?slug=...` báo không tồn tại
- hoặc trang trắng / lỗi trên hosting

Nguyên nhân nghi ngờ:

- file cũ bị cache
- slug cũ và slug mới không đồng bộ
- hosting chưa nhận file PHP mới

### 10.3. Tuyển dụng cũng đang là dữ liệu tĩnh

- chưa có backend quản lý bài tuyển dụng
- sửa/xóa/thêm đều phải thao tác file [public/company/data/jobs.php](D:\xampp\htdocs\EcoQnoibo\public\company\data\jobs.php)

### 10.4. Encoding tiếng Việt ở nhiều file chưa sạch

Trong terminal có thể nhìn thấy ký tự lỗi như:

- `Tuyển dụng`
- `Khách hàng`

Điều này cho thấy một số file đã bị lưu với encoding không đồng nhất hoặc đang được đọc sai trong terminal.

Khuyến nghị:

- chuẩn hóa toàn bộ file view/data về UTF-8
- kiểm tra trên browser thật, không chỉ nhìn PowerShell

### 10.5. Báo cáo dùng Chart.js cần kiểm tra lại sau deploy

Đã từng có lỗi:

- `Chart is not defined`

Đã có chỉnh sửa ở layout để chờ external script tải xong trước khi chạy inline script, nhưng vẫn nên test lại riêng màn `Báo cáo` sau mỗi lần deploy.

### 10.6. Nhiều phần public vẫn là dữ liệu code cứng

Hiện chưa có panel admin để quản lý:

- tin tức
- tuyển dụng
- nội dung landing page

Nếu dự án tiếp tục phát triển, nên cân nhắc:

- đưa tin tức vào database
- đưa tuyển dụng vào database
- hoặc làm một module CMS mini trong nội bộ

## 11. Đề xuất việc nên làm tiếp

Thứ tự ưu tiên khuyến nghị:

1. Ổn định hoàn toàn CSS và asset của khu nội bộ trên hosting.
2. Chuẩn hóa encoding UTF-8 cho các file public và nội bộ.
3. Kiểm tra lại luồng tin tức `bai-viet?slug=...` trên host.
4. Cân nhắc chuyển `tin tức` và `tuyển dụng` sang dữ liệu database hoặc module admin.
5. Rà soát lại toàn bộ responsive trên mobile/tablet.
6. Viết thêm checklist test sau deploy cho cả public và nội bộ.

## 12. Kết luận ngắn

Hệ thống hiện đã có nền tảng vận hành khá đầy đủ:

- auth
- phân quyền
- CRUD hệ thống, chi nhánh, phòng
- quản lý khách hàng
- quy trình lock phòng
- quản lý người dùng
- landing page công ty
- tin tức và tuyển dụng public

Điểm còn thiếu lớn nhất hiện tại không phải nghiệp vụ cốt lõi, mà là:

- độ ổn định khi deploy hosting
- quản lý nội dung public còn thủ công
- encoding tiếng Việt chưa sạch

Người tiếp nhận nếu cần làm tiếp nên ưu tiên xử lý phần ổn định hosting trước, sau đó mới mở rộng tính năng mới.
