<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Session;
use App\Core\View;

class RoomController
{
    public function manageData(int $roomId): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $room = $this->findRoom($roomId);
        if (! $room) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Không tìm thấy phòng cần chỉnh sửa.',
            ], 404);
        }

        $payload = $this->formatRoomPayload($room);
        $payload['media'] = $this->roomMedia($roomId);

        $this->jsonResponse([
            'success' => true,
            'room' => $payload,
        ]);
    }

    public function home(): void
    {
        Auth::requireLogin();

        $connection = Database::connection();
        $filters = $this->roomFilters();
        $rooms = $this->fetchRooms($filters);
        $roomIds = array_map(static fn (array $room): int => (int) $room['id'], $rooms);

        $roomMediaMap = $this->roomMediaMap($roomIds);
        $roomStats = [
            'total' => count($rooms),
            'chua_lock' => 0,
            'dang_giu' => 0,
            'da_lock' => 0,
        ];

        foreach ($rooms as $room) {
            if (isset($roomStats[$room['status']])) {
                $roomStats[$room['status']]++;
            }
        }

        View::render('home.index', [
            'pageTitle' => 'Trang chủ',
            'systems' => $connection->query('SELECT id, name FROM systems ORDER BY name ASC')->fetchAll(),
            'wards' => $connection->query(
                "SELECT wards.id, wards.name, wards.system_id, systems.name AS system_name
                 FROM wards
                 INNER JOIN systems ON systems.id = wards.system_id
                 ORDER BY systems.name ASC, wards.name ASC"
            )->fetchAll(),
            'districts' => $connection->query('SELECT id, name FROM districts ORDER BY name ASC')->fetchAll(),
            'branches' => $connection->query(
                "SELECT branches.id, branches.name, branches.ward_id, systems.name AS system_name, wards.name AS ward_name
                 FROM branches
                 INNER JOIN systems ON systems.id = branches.system_id
                 LEFT JOIN wards ON wards.id = branches.ward_id
                 ORDER BY systems.name ASC, branches.name ASC"
            )->fetchAll(),
            'rooms' => $rooms,
            'roomMediaMap' => $roomMediaMap,
            'filters' => $filters,
            'roomStats' => $roomStats,
            'canRequestLock' => Auth::can(['staff']),
        ]);
    }

    public function index(): void
    {
        Auth::requireLogin();

        $connection = Database::connection();
        $filters = $this->roomFilters();
        $rooms = $this->fetchRooms($filters);
        $roomIds = array_map(static fn (array $room): int => (int) $room['id'], $rooms);
        $roomMediaMap = $this->roomMediaMap($roomIds);
        $latestLockMap = $this->latestLockMap($roomIds);

        $roomStats = [
            'total' => count($rooms),
            'chua_lock' => 0,
            'dang_giu' => 0,
            'da_lock' => 0,
        ];
        foreach ($rooms as $room) {
            if (isset($roomStats[$room['status']])) {
                $roomStats[$room['status']]++;
            }
        }

        $editRoom = null;
        $editId = (int) query('edit', 0);
        if ($editId > 0) {
            $editRoom = $this->findRoom($editId);
        }

        $editRoomMedia = $editRoom ? $this->roomMedia((int) $editRoom['id']) : [];

        $systems = $connection->query('SELECT id, name FROM systems ORDER BY name ASC')->fetchAll();
        $wards = $connection->query(
            "SELECT wards.id, wards.name, wards.system_id, systems.name AS system_name
             FROM wards
             INNER JOIN systems ON systems.id = wards.system_id
             ORDER BY systems.name ASC, wards.name ASC"
        )->fetchAll();
        $districts = $connection->query('SELECT id, name FROM districts ORDER BY name ASC')->fetchAll();
        $branches = $connection->query(
            "SELECT branches.id, branches.name, branches.system_id, branches.ward_id, systems.name AS system_name, wards.name AS ward_name, districts.name AS district_name
             FROM branches
             INNER JOIN systems ON systems.id = branches.system_id
             LEFT JOIN wards ON wards.id = branches.ward_id
             INNER JOIN districts ON districts.id = branches.district_id
             ORDER BY systems.name ASC, branches.name ASC"
        )->fetchAll();

        View::render('rooms.index', [
            'pageTitle' => 'Quản lý phòng',
            'systems' => $systems,
            'wards' => $wards,
            'districts' => $districts,
            'branches' => $branches,
            'rooms' => $rooms,
            'filters' => $filters,
            'editRoom' => $editRoom,
            'editRoomMedia' => $editRoomMedia,
            'roomMediaMap' => $roomMediaMap,
            'latestLockMap' => $latestLockMap,
            'roomStats' => $roomStats,
            'canManage' => Auth::can(['director', 'manager']),
            'canFeaturePublic' => Auth::can(['director']),
            'canRequestLock' => Auth::can(['staff']),
        ]);
    }

    public function show(int $roomId): void
    {
        Auth::requireLogin();

        $room = $this->findRoom($roomId);
        if (! $room) {
            abort(404, 'Không tìm thấy phòng bạn đang xem.');
        }

        $media = $this->roomMedia($roomId);
        $latestLock = $this->latestLockMap([$roomId])[$roomId] ?? null;
        $relatedRooms = $this->relatedRooms($room);
        $relatedMediaMap = $this->roomMediaMap(array_map(static fn (array $item): int => (int) $item['id'], $relatedRooms));

        View::render('rooms.show', [
            'pageTitle' => 'Chi tiết phòng',
            'room' => $room,
            'media' => $media,
            'latestLock' => $latestLock,
            'relatedRooms' => $relatedRooms,
            'relatedMediaMap' => $relatedMediaMap,
            'canManage' => Auth::can(['director', 'manager']),
            'canRequestLock' => Auth::can(['staff']),
        ]);
    }

    public function downloadImages(int $roomId): void
    {
        Auth::requireLogin();

        $room = $this->findRoom($roomId);
        if (! $room) {
            abort(404, 'Không tìm thấy phòng để tải ảnh.');
        }

        $images = array_values(array_filter(
            $this->roomMedia($roomId),
            static fn (array $media): bool => ($media['media_type'] ?? '') === 'image'
        ));

        if ($images === []) {
            Session::flash('error', 'Phòng này chưa có ảnh để tải xuống.');
            redirect('/rooms/' . $roomId);
        }

        if (! class_exists(\ZipArchive::class)) {
            Session::flash('error', 'Máy chủ hiện chưa hỗ trợ nén file ZIP.');
            redirect('/rooms/' . $roomId);
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'room_images_');
        if ($tempFile === false) {
            Session::flash('error', 'Không thể tạo file tạm để nén ảnh.');
            redirect('/rooms/' . $roomId);
        }

        $zipPath = $tempFile . '.zip';
        @rename($tempFile, $zipPath);

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            @unlink($zipPath);
            Session::flash('error', 'Không thể tạo file ZIP.');
            redirect('/rooms/' . $roomId);
        }

        $addedCount = 0;
        foreach ($images as $index => $media) {
            $absolutePath = base_path($media['file_path']);
            if (! is_file($absolutePath)) {
                continue;
            }

            $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
            $safeBaseName = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) ($room['room_number'] ?? 'room'));
            $entryName = sprintf(
                '%s-%02d%s',
                trim($safeBaseName, '-'),
                $index + 1,
                $extension !== '' ? '.' . $extension : ''
            );

            $zip->addFile($absolutePath, $entryName);
            $addedCount++;
        }

        $zip->close();

        if ($addedCount === 0) {
            @unlink($zipPath);
            Session::flash('error', 'Không tìm thấy ảnh hợp lệ để nén.');
            redirect('/rooms/' . $roomId);
        }

        $downloadName = sprintf(
            'room-%s-images.zip',
            preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) ($room['room_number'] ?? $roomId))
        );

        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        header('Content-Length: ' . (string) filesize($zipPath));
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        readfile($zipPath);
        @unlink($zipPath);
        exit;
    }

    public function store(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $data = $this->validatedPayload();
        if ($data === null) {
            if ($this->isAjaxRequest()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => Session::getFlash('error', 'Dữ liệu phòng chưa hợp lệ.'),
                ], 422);
            }
            redirect('/rooms');
        }

        $connection = Database::connection();
        $connection->beginTransaction();

        try {
            $connection->prepare(
                'INSERT INTO rooms (
                    branch_id, room_number, price, room_type, electricity_fee, water_fee,
                    service_fee, parking_fee, status, is_public_visible, furniture_status, has_balcony, window_type, note
                 ) VALUES (
                    :branch_id, :room_number, :price, :room_type, :electricity_fee, :water_fee,
                    :service_fee, :parking_fee, :status, :is_public_visible, :furniture_status, :has_balcony, :window_type, :note
                 )'
            )->execute($data);

            $roomId = (int) $connection->lastInsertId();
            $uploaded = $this->storeUploadedMedia($roomId);
            $connection->commit();

            $user = Auth::user();
            activity_log((int) $user['id'], 'create', 'rooms', 'Tạo phòng #' . $roomId . ' - ' . $data['room_number'] . '. Upload ' . $uploaded . ' media.');

            $message = 'Đã thêm phòng thành công.';
            if ($this->isAjaxRequest()) {
                $freshRoom = $this->findRoom($roomId);
                $this->jsonResponse([
                    'success' => true,
                    'message' => $message,
                    'room' => $freshRoom ? $this->formatRoomPayload($freshRoom) : null,
                ]);
            }

            Session::flash('success', $message);
        } catch (\Throwable) {
            $connection->rollBack();
            $message = 'Thêm phòng thất bại. Vui lòng kiểm tra trùng số phòng trong chi nhánh.';

            if ($this->isAjaxRequest()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            Session::flash('error', $message);
        }

        redirect('/rooms');
    }

    public function update(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $roomId = (int) ($_POST['id'] ?? 0);
        if ($roomId <= 0) {
            $message = 'Phòng không hợp lệ.';
            if ($this->isAjaxRequest()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }
            Session::flash('error', $message);
            redirect('/rooms');
        }

        $existingRoom = $this->findRoom($roomId);
        if (! $existingRoom) {
            $message = 'Phòng không tồn tại.';
            if ($this->isAjaxRequest()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => $message,
                ], 404);
            }
            Session::flash('error', $message);
            redirect('/rooms');
        }

        $data = $this->validatedPayload($existingRoom);
        if ($data === null) {
            if ($this->isAjaxRequest()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => Session::getFlash('error', 'Dữ liệu phòng chưa hợp lệ.'),
                ], 422);
            }
            redirect('/rooms?edit=' . $roomId);
        }

        $deleteMediaIds = array_map('intval', $_POST['delete_media_ids'] ?? []);
        $connection = Database::connection();
        $connection->beginTransaction();

        try {
            $connection->prepare(
                'UPDATE rooms SET
                    branch_id = :branch_id,
                    room_number = :room_number,
                    price = :price,
                    room_type = :room_type,
                    electricity_fee = :electricity_fee,
                    water_fee = :water_fee,
                    service_fee = :service_fee,
                    parking_fee = :parking_fee,
                    status = :status,
                    is_public_visible = :is_public_visible,
                    furniture_status = :furniture_status,
                    has_balcony = :has_balcony,
                    window_type = :window_type,
                    note = :note
                 WHERE id = :id'
            )->execute($data + ['id' => $roomId]);

            $deleted = $this->deleteMediaByIds($roomId, $deleteMediaIds);
            $uploaded = $this->storeUploadedMedia($roomId);
            $connection->commit();

            $user = Auth::user();
            activity_log((int) $user['id'], 'update', 'rooms', 'Cập nhật phòng #' . $roomId . '. Xóa ' . $deleted . ' media, thêm ' . $uploaded . ' media.');

            $message = 'Đã cập nhật phòng thành công.';
            if ($this->isAjaxRequest()) {
                $freshRoom = $this->findRoom($roomId);
                $this->jsonResponse([
                    'success' => true,
                    'message' => $message,
                    'room' => $freshRoom ? $this->formatRoomPayload($freshRoom) : null,
                ]);
            }

            Session::flash('success', $message);
        } catch (\Throwable) {
            $connection->rollBack();
            $message = 'Cập nhật phòng thất bại. Vui lòng thử lại.';

            if ($this->isAjaxRequest()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            Session::flash('error', $message);
            redirect('/rooms?edit=' . $roomId);
        }

        redirect('/rooms');
    }

    public function delete(): void
    {
        Auth::requireLogin();
        Auth::authorize(['director', 'manager']);

        $roomId = (int) ($_POST['id'] ?? 0);
        if ($roomId <= 0) {
            $message = 'Phòng không hợp lệ.';
            if ($this->isAjaxRequest()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }
            Session::flash('error', $message);
            redirect('/rooms');
        }

        $room = $this->findRoom($roomId);
        if (! $room) {
            $message = 'Phòng không tồn tại.';
            if ($this->isAjaxRequest()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => $message,
                ], 404);
            }
            Session::flash('error', $message);
            redirect('/rooms');
        }

        $connection = Database::connection();
        $connection->beginTransaction();

        try {
            foreach ($this->roomMedia($roomId) as $media) {
                $absolutePath = base_path($media['file_path']);
                if (is_file($absolutePath)) {
                    @unlink($absolutePath);
                }
            }

            $connection->prepare('DELETE FROM rooms WHERE id = :id')->execute(['id' => $roomId]);
            $connection->commit();

            $user = Auth::user();
            activity_log((int) $user['id'], 'delete', 'rooms', 'Xóa phòng #' . $roomId . ' - ' . $room['room_number']);

            $message = 'Đã xóa phòng thành công.';
            if ($this->isAjaxRequest()) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => $message,
                    'room_id' => $roomId,
                ]);
            }

            Session::flash('success', $message);
        } catch (\Throwable) {
            $connection->rollBack();
            $message = 'Xóa phòng thất bại. Vui lòng thử lại.';

            if ($this->isAjaxRequest()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            Session::flash('error', $message);
        }

        redirect('/rooms');
    }

    private function roomFilters(): array
    {
        return [
            'system_id' => (int) query('system_id', 0),
            'ward_id' => (int) query('ward_id', 0),
            'branch_id' => (int) query('branch_id', 0),
            'district_id' => (int) query('district_id', 0),
            'status' => trim((string) query('status', '')),
            'furniture_status' => trim((string) query('furniture_status', '')),
            'price_min' => trim((string) query('price_min', '')),
            'price_max' => trim((string) query('price_max', '')),
            'keyword' => trim((string) query('keyword', '')),
        ];
    }

    private function fetchRooms(array $filters): array
    {
        $connection = Database::connection();
        $sql = "SELECT rooms.*,
                       branches.name AS branch_name,
                       branches.address AS branch_address,
                       branches.manager_phone,
                       branches.system_id,
                       branches.ward_id,
                       branches.id AS branch_id,
                       systems.name AS system_name,
                       wards.name AS ward_name,
                       districts.name AS district_name,
                       COUNT(room_media.id) AS media_count
                FROM rooms
                INNER JOIN branches ON branches.id = rooms.branch_id
                INNER JOIN systems ON systems.id = branches.system_id
                LEFT JOIN wards ON wards.id = branches.ward_id
                INNER JOIN districts ON districts.id = branches.district_id
                LEFT JOIN room_media ON room_media.room_id = rooms.id
                WHERE 1 = 1";

        $params = [];
        if ($filters['system_id'] > 0) {
            $sql .= ' AND branches.system_id = :system_id';
            $params['system_id'] = $filters['system_id'];
        }
        if ($filters['ward_id'] > 0) {
            $sql .= ' AND branches.ward_id = :ward_id';
            $params['ward_id'] = $filters['ward_id'];
        }
        if ($filters['branch_id'] > 0) {
            $sql .= ' AND branches.id = :branch_id';
            $params['branch_id'] = $filters['branch_id'];
        }
        if ($filters['district_id'] > 0) {
            $sql .= ' AND branches.district_id = :district_id';
            $params['district_id'] = $filters['district_id'];
        }
        if ($filters['status'] !== '') {
            $sql .= ' AND rooms.status = :status';
            $params['status'] = $filters['status'];
        }
        if ($filters['furniture_status'] !== '') {
            $sql .= ' AND rooms.furniture_status = :furniture_status';
            $params['furniture_status'] = $filters['furniture_status'];
        }
        if ($filters['price_min'] !== '' && is_numeric($filters['price_min'])) {
            $sql .= ' AND rooms.price >= :price_min';
            $params['price_min'] = (float) $filters['price_min'];
        }
        if ($filters['price_max'] !== '' && is_numeric($filters['price_max'])) {
            $sql .= ' AND rooms.price <= :price_max';
            $params['price_max'] = (float) $filters['price_max'];
        }
        if ($filters['keyword'] !== '') {
            $sql .= ' AND (rooms.room_number LIKE :keyword OR branches.name LIKE :keyword OR branches.address LIKE :keyword OR rooms.note LIKE :keyword)';
            $params['keyword'] = '%' . $filters['keyword'] . '%';
        }

        $sql .= ' GROUP BY rooms.id ORDER BY rooms.updated_at DESC';
        $statement = $connection->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    private function relatedRooms(array $room): array
    {
        $statement = Database::connection()->prepare(
            "SELECT rooms.*,
                    branches.name AS branch_name,
                    branches.address AS branch_address,
                    branches.manager_phone,
                    branches.system_id,
                    branches.ward_id,
                    branches.id AS branch_id,
                    systems.name AS system_name,
                    wards.name AS ward_name,
                    districts.name AS district_name,
                    COUNT(room_media.id) AS media_count
             FROM rooms
             INNER JOIN branches ON branches.id = rooms.branch_id
             INNER JOIN systems ON systems.id = branches.system_id
             LEFT JOIN wards ON wards.id = branches.ward_id
             INNER JOIN districts ON districts.id = branches.district_id
             LEFT JOIN room_media ON room_media.room_id = rooms.id
             WHERE rooms.id <> :room_id
               AND (branches.id = :branch_id OR districts.name = :district_name)
             GROUP BY rooms.id
             ORDER BY (rooms.status = 'chua_lock') DESC, rooms.updated_at DESC
             LIMIT 8"
        );
        $statement->execute([
            'room_id' => $room['id'],
            'branch_id' => $room['branch_id'],
            'district_name' => $room['district_name'],
        ]);

        return $statement->fetchAll();
    }

    private function validatedPayload(?array $existingRoom = null): ?array
    {
        $branchId = (int) ($_POST['branch_id'] ?? 0);
        $roomNumber = trim($_POST['room_number'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $roomType = trim($_POST['room_type'] ?? '');
        $electricityFee = trim($_POST['electricity_fee'] ?? '0');
        $waterFee = trim($_POST['water_fee'] ?? '0');
        $serviceFee = trim($_POST['service_fee'] ?? '0');
        $parkingFee = trim($_POST['parking_fee'] ?? '0');
        $status = trim($_POST['status'] ?? 'chua_lock');
        $isPublicVisible = Auth::can(['director']) && isset($_POST['is_public_visible']) ? 1 : 0;
        $furnitureStatus = trim($_POST['furniture_status'] ?? '');
        $hasBalcony = isset($_POST['has_balcony']) ? 1 : 0;
        $windowType = trim($_POST['window_type'] ?? '');
        $note = trim($_POST['note'] ?? '');

        $allowedRoomTypes = ['duplex', 'studio', 'one_bedroom', 'two_bedroom', 'kiot'];
        $allowedStatuses = ['chua_lock', 'dang_giu', 'da_lock'];
        $allowedFurnitureStatuses = ['co_noi_that', 'khong_noi_that'];
        $allowedWindowTypes = ['cua_so_troi', 'cua_so_hanh_lang', 'cua_so_gieng_troi'];

        if (
            $branchId <= 0 ||
            $roomNumber === '' ||
            ! is_numeric($price) ||
            ! in_array($roomType, $allowedRoomTypes, true) ||
            ! in_array($status, $allowedStatuses, true) ||
            ! in_array($furnitureStatus, $allowedFurnitureStatuses, true) ||
            ! in_array($windowType, $allowedWindowTypes, true)
        ) {
            Session::flash('error', 'Vui lòng nhập đầy đủ và đúng thông tin phòng.');
            return null;
        }

        foreach ([$electricityFee, $waterFee, $serviceFee, $parkingFee] as $amount) {
            if (! is_numeric($amount)) {
                Session::flash('error', 'Tiền điện, nước, dịch vụ và gửi xe phải là số hợp lệ.');
                return null;
            }
        }

        if (! $this->validateUploadedFiles()) {
            return null;
        }

        return [
            'branch_id' => $branchId,
            'room_number' => $roomNumber,
            'price' => (float) $price,
            'room_type' => $roomType,
            'electricity_fee' => (float) $electricityFee,
            'water_fee' => (float) $waterFee,
            'service_fee' => (float) $serviceFee,
            'parking_fee' => (float) $parkingFee,
            'status' => $status,
            'is_public_visible' => Auth::can(['director'])
                ? $isPublicVisible
                : (int) ($existingRoom['is_public_visible'] ?? 0),
            'furniture_status' => $furnitureStatus,
            'has_balcony' => $hasBalcony,
            'window_type' => $windowType,
            'note' => $note !== '' ? $note : null,
        ];
    }

    private function validateUploadedFiles(): bool
    {
        $files = $this->normalizeFiles($_FILES['media_files'] ?? []);
        $allowedImageMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $allowedVideoMimes = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'];

        foreach ($files as $file) {
            if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                Session::flash('error', 'Có file upload gặp lỗi. Vui lòng thử lại.');
                return false;
            }

            $mimeType = $this->detectMimeType($file['tmp_name']);
            if (! in_array($mimeType, array_merge($allowedImageMimes, $allowedVideoMimes), true)) {
                Session::flash('error', 'Chỉ chấp nhận ảnh JPG, PNG, WEBP, GIF hoặc video MP4, WEBM, OGG, MOV.');
                return false;
            }
        }

        return true;
    }

    private function storeUploadedMedia(int $roomId): int
    {
        $files = $this->normalizeFiles($_FILES['media_files'] ?? []);
        $allowedImageMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $allowedVideoMimes = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'];
        $storedCount = 0;

        foreach ($files as $file) {
            if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $mimeType = $this->detectMimeType($file['tmp_name']);
            if (! in_array($mimeType, array_merge($allowedImageMimes, $allowedVideoMimes), true)) {
                continue;
            }

            $mediaType = in_array($mimeType, $allowedImageMimes, true) ? 'image' : 'video';
            $directory = $mediaType === 'image' ? 'storage/uploads/images' : 'storage/uploads/videos';
            $absoluteDirectory = base_path($directory);

            if (! is_dir($absoluteDirectory)) {
                mkdir($absoluteDirectory, 0777, true);
            }

            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $generatedName = uniqid('room_' . $roomId . '_', true) . ($extension ? '.' . $extension : '');
            $relativePath = str_replace('\\', '/', $directory . '/' . $generatedName);
            $absolutePath = base_path($relativePath);

            if (! move_uploaded_file($file['tmp_name'], $absolutePath)) {
                continue;
            }

            Database::connection()->prepare(
                'INSERT INTO room_media (room_id, media_type, file_name, file_path, mime_type, file_size)
                 VALUES (:room_id, :media_type, :file_name, :file_path, :mime_type, :file_size)'
            )->execute([
                'room_id' => $roomId,
                'media_type' => $mediaType,
                'file_name' => $file['name'],
                'file_path' => $relativePath,
                'mime_type' => $mimeType,
                'file_size' => (int) ($file['size'] ?? 0),
            ]);

            $storedCount++;
        }

        return $storedCount;
    }

    private function deleteMediaByIds(int $roomId, array $mediaIds): int
    {
        if ($mediaIds === []) {
            return 0;
        }

        $deletedCount = 0;
        $connection = Database::connection();
        $statement = $connection->prepare('SELECT * FROM room_media WHERE room_id = :room_id AND id = :id');

        foreach ($mediaIds as $mediaId) {
            if ($mediaId <= 0) {
                continue;
            }

            $statement->execute(['room_id' => $roomId, 'id' => $mediaId]);
            $media = $statement->fetch();
            if (! $media) {
                continue;
            }

            $absolutePath = base_path($media['file_path']);
            if (is_file($absolutePath)) {
                @unlink($absolutePath);
            }

            $connection->prepare('DELETE FROM room_media WHERE id = :id')->execute(['id' => $mediaId]);
            $deletedCount++;
        }

        return $deletedCount;
    }

    private function roomMedia(int $roomId): array
    {
        $statement = Database::connection()->prepare('SELECT * FROM room_media WHERE room_id = :room_id ORDER BY created_at DESC');
        $statement->execute(['room_id' => $roomId]);
        return $statement->fetchAll();
    }

    private function roomMediaMap(array $roomIds): array
    {
        if ($roomIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($roomIds), '?'));
        $statement = Database::connection()->prepare(
            "SELECT * FROM room_media
             WHERE room_id IN ($placeholders)
             ORDER BY created_at DESC"
        );
        $statement->execute($roomIds);

        $map = [];
        foreach ($statement->fetchAll() as $media) {
            $map[$media['room_id']][] = $media;
        }

        return $map;
    }

    private function latestLockMap(array $roomIds): array
    {
        if ($roomIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($roomIds), '?'));
        $statement = Database::connection()->prepare(
            "SELECT lock_requests.*, users.full_name AS requester_name
             FROM lock_requests
             INNER JOIN users ON users.id = lock_requests.requested_by
             WHERE lock_requests.room_id IN ($placeholders)
             ORDER BY lock_requests.requested_at DESC"
        );
        $statement->execute($roomIds);

        $map = [];
        foreach ($statement->fetchAll() as $lockRequest) {
            if (! isset($map[$lockRequest['room_id']])) {
                $map[$lockRequest['room_id']] = $lockRequest;
            }
        }

        return $map;
    }

    private function findRoom(int $roomId): ?array
    {
        $statement = Database::connection()->prepare(
            "SELECT rooms.*,
                    branches.name AS branch_name,
                    branches.address AS branch_address,
                    branches.manager_phone,
                    branches.system_id,
                    branches.ward_id,
                    branches.id AS branch_id,
                    systems.name AS system_name,
                    wards.name AS ward_name,
                    districts.name AS district_name,
                    (
                        SELECT COUNT(*)
                        FROM room_media
                        WHERE room_media.room_id = rooms.id
                    ) AS media_count
             FROM rooms
             INNER JOIN branches ON branches.id = rooms.branch_id
             INNER JOIN systems ON systems.id = branches.system_id
             LEFT JOIN wards ON wards.id = branches.ward_id
             INNER JOIN districts ON districts.id = branches.district_id
             WHERE rooms.id = :id
             LIMIT 1"
        );
        $statement->execute(['id' => $roomId]);

        return $statement->fetch() ?: null;
    }

    private function normalizeFiles(array $files): array
    {
        if (! isset($files['name']) || ! is_array($files['name'])) {
            return [];
        }

        $normalized = [];
        foreach ($files['name'] as $index => $name) {
            $normalized[] = [
                'name' => $name,
                'type' => $files['type'][$index] ?? '',
                'tmp_name' => $files['tmp_name'][$index] ?? '',
                'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                'size' => $files['size'][$index] ?? 0,
            ];
        }

        return $normalized;
    }

    private function detectMimeType(string $tmpPath): string
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mimeType = finfo_file($finfo, $tmpPath) ?: '';
                finfo_close($finfo);
                if ($mimeType !== '') {
                    return $mimeType;
                }
            }
        }

        return mime_content_type($tmpPath) ?: '';
    }

    private function formatRoomPayload(array $room): array
    {
        return [
            'id' => (int) $room['id'],
            'branch_id' => (int) $room['branch_id'],
            'room_number' => $room['room_number'],
            'price' => (float) $room['price'],
            'room_type' => $room['room_type'],
            'electricity_fee' => (float) $room['electricity_fee'],
            'water_fee' => (float) $room['water_fee'],
            'service_fee' => (float) $room['service_fee'],
            'parking_fee' => (float) $room['parking_fee'],
            'status' => $room['status'],
            'is_public_visible' => (int) ($room['is_public_visible'] ?? 0),
            'furniture_status' => $room['furniture_status'],
            'has_balcony' => (int) $room['has_balcony'],
            'window_type' => $room['window_type'],
            'note' => $room['note'] ?? '',
            'branch_name' => $room['branch_name'] ?? '',
            'branch_address' => $room['branch_address'] ?? '',
            'system_name' => $room['system_name'] ?? '',
            'ward_name' => $room['ward_name'] ?? '',
            'district_name' => $room['district_name'] ?? '',
            'media_count' => (int) ($room['media_count'] ?? count($this->roomMedia((int) $room['id']))),
        ];
    }

    private function isAjaxRequest(): bool
    {
        $requestedWith = strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''));
        $accept = strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? ''));

        return $requestedWith === 'xmlhttprequest' || str_contains($accept, 'application/json');
    }

    private function jsonResponse(array $payload, int $statusCode = 200): never
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
