<?php

require_once dirname(__DIR__, 3) . '/bootstrap.php';

use App\Core\Database;

function room_status_label(string $status): string
{
    return match ($status) {
        'chua_lock' => 'Còn trống',
        'dang_giu' => 'Đang giữ',
        'da_lock' => 'Đã thuê',
        default => $status,
    };
}

function room_type_label(string $type): string
{
    return match ($type) {
        'duplex' => 'Duplex',
        'studio' => 'Studio',
        'one_bedroom' => 'Studio',
        'two_bedroom' => 'Studio',
        'kiot' => 'Kiot',
        'co_gac' => 'Duplex',
        'khong_gac' => 'Studio',
        default => $type ?: 'Chưa cập nhật',
    };
}
function furniture_status_label(string $status): string
{
    return match ($status) {
        'co_noi_that' => 'Có nội thất',
        'khong_noi_that' => 'Không nội thất',
        default => $status ?: 'Chưa cập nhật',
    };
}

function window_type_label(string $type): string
{
    return match ($type) {
        'cua_so_troi' => 'Cửa sổ trời',
        'cua_so_hanh_lang' => 'Cửa sổ hành lang',
        'cua_so_gieng_troi' => 'Cửa sổ giếng trời',
        default => $type ?: 'Chưa cập nhật',
    };
}

function room_media_url(string $filePath): string
{
    return media_url($filePath);
}

function public_data_query(callable $callback, mixed $fallback): mixed
{
    $connection = Database::tryConnection();

    if (! $connection) {
        return $fallback;
    }

    try {
        return $callback($connection);
    } catch (Throwable) {
        return $fallback;
    }
}

function public_price_million(mixed $value): string
{
    $price = (float) $value;
    if ($price <= 0) {
        return 'Chưa cập nhật';
    }

    $million = $price / 1000000;
    $formatted = rtrim(rtrim(number_format($million, 1, '.', ''), '0'), '.');

    return $formatted . ' triệu';
}

function public_branch_price_label(array $branch): string
{
    $min = (float) ($branch['min_price'] ?? 0);
    $max = (float) ($branch['max_price'] ?? 0);
    $total = (int) ($branch['total_rooms'] ?? 0);

    if ($total <= 0 || ($min <= 0 && $max <= 0)) {
        return 'Liên hệ';
    }

    if ($max <= 0 || $min === $max || $total === 1) {
        return 'Giá: ' . public_price_million($min > 0 ? $min : $max);
    }

    return 'Giá từ: ' . public_price_million($min) . ' đến ' . public_price_million($max);
}

function public_room_price_label(array $room): string
{
    $price = (float) ($room['price'] ?? 0);

    return $price > 0 ? number_format($price, 0, ',', '.') . 'đ/tháng' : 'Liên hệ';
}

function fetch_public_branch_cards(): array
{
    return public_data_query(static function ($connection): array {
        $statement = $connection->query(
            "SELECT branches.id AS branch_id,
                    branches.name AS branch_name,
                    branches.manager_phone,
                    branches.electricity_price,
                    branches.water_price,
                    branches.service_price,
                    branches.parking_price,
                    systems.name AS system_name,
                    districts.name AS district_name,
                    COUNT(DISTINCT rooms.id) AS total_rooms,
                    SUM(CASE WHEN rooms.status = 'chua_lock' THEN 1 ELSE 0 END) AS available_rooms,
                    MIN(NULLIF(rooms.price, 0)) AS min_price,
                    MAX(NULLIF(rooms.price, 0)) AS max_price,
                    (
                        SELECT room_media.file_path
                        FROM rooms AS cover_rooms
                        INNER JOIN room_media ON room_media.room_id = cover_rooms.id
                        WHERE cover_rooms.branch_id = branches.id
                          AND cover_rooms.status = 'chua_lock'
                          AND cover_rooms.is_public_visible = 1
                          AND room_media.media_type = 'image'
                        ORDER BY cover_rooms.updated_at DESC, room_media.created_at DESC
                        LIMIT 1
                    ) AS cover_image_path
             FROM rooms
             INNER JOIN branches ON branches.id = rooms.branch_id
             INNER JOIN systems ON systems.id = branches.system_id
             INNER JOIN districts ON districts.id = branches.district_id
             WHERE rooms.status = 'chua_lock'
               AND rooms.is_public_visible = 1
             GROUP BY branches.id
             ORDER BY available_rooms DESC, branches.updated_at DESC"
        );

        return $statement->fetchAll();
    }, []);
}

function fetch_public_rooms(): array
{
    return public_data_query(static function ($connection): array {
        $statement = $connection->query(
            "SELECT rooms.*,
                    branches.name AS branch_name,
                    branches.manager_phone,
                    branches.electricity_price AS electricity_fee,
                    branches.water_price AS water_fee,
                    branches.service_price AS service_fee,
                    branches.parking_price AS parking_fee,
                    systems.name AS system_name,
                    districts.name AS district_name
             FROM rooms
             INNER JOIN branches ON branches.id = rooms.branch_id
             INNER JOIN systems ON systems.id = branches.system_id
             INNER JOIN districts ON districts.id = branches.district_id
             WHERE rooms.status = 'chua_lock'
               AND rooms.is_public_visible = 1
             ORDER BY rooms.updated_at DESC"
        );

        return $statement->fetchAll();
    }, []);
}

function fetch_room_media_map(array $roomIds): array
{
    if ($roomIds === []) {
        return [];
    }

    return public_data_query(static function ($connection) use ($roomIds): array {
        $placeholders = implode(',', array_fill(0, count($roomIds), '?'));
        $statement = $connection->prepare(
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
    }, []);
}

function fetch_public_room_detail(int $roomId): ?array
{
    if ($roomId <= 0) {
        return null;
    }

    return public_data_query(static function ($connection) use ($roomId): ?array {
        $statement = $connection->prepare(
            "SELECT rooms.*,
                    branches.name AS branch_name,
                    branches.manager_phone,
                    branches.id AS branch_id,
                    branches.electricity_price AS electricity_fee,
                    branches.water_price AS water_fee,
                    branches.service_price AS service_fee,
                    branches.parking_price AS parking_fee,
                    systems.name AS system_name,
                    districts.name AS district_name
             FROM rooms
             INNER JOIN branches ON branches.id = rooms.branch_id
             INNER JOIN systems ON systems.id = branches.system_id
             INNER JOIN districts ON districts.id = branches.district_id
             WHERE rooms.id = :id
               AND rooms.status = 'chua_lock'
               AND rooms.is_public_visible = 1
             LIMIT 1"
        );
        $statement->execute(['id' => $roomId]);

        return $statement->fetch() ?: null;
    }, null);
}

function fetch_public_branch_detail(int $branchId): ?array
{
    if ($branchId <= 0) {
        return null;
    }

    return public_data_query(static function ($connection) use ($branchId): ?array {
        $statement = $connection->prepare(
            "SELECT branches.*,
                    systems.name AS system_name,
                    districts.name AS district_name
             FROM branches
             INNER JOIN systems ON systems.id = branches.system_id
             INNER JOIN districts ON districts.id = branches.district_id
             WHERE branches.id = :id
             LIMIT 1"
        );
        $statement->execute(['id' => $branchId]);

        return $statement->fetch() ?: null;
    }, null);
}

function fetch_public_branch_rooms(int $branchId): array
{
    return public_data_query(static function ($connection) use ($branchId): array {
        $statement = $connection->prepare(
            "SELECT rooms.*
             FROM rooms
             WHERE rooms.branch_id = :branch_id
               AND rooms.status = 'chua_lock'
               AND rooms.is_public_visible = 1
             ORDER BY rooms.price ASC, rooms.room_number ASC"
        );
        $statement->execute(['branch_id' => $branchId]);

        return $statement->fetchAll();
    }, []);
}

function fetch_public_room_media(int $roomId): array
{
    return public_data_query(static function ($connection) use ($roomId): array {
        $statement = $connection->prepare(
            'SELECT * FROM room_media WHERE room_id = :room_id ORDER BY created_at DESC'
        );
        $statement->execute(['room_id' => $roomId]);

        return $statement->fetchAll();
    }, []);
}

function fetch_related_public_rooms(array $room, int $limit = 6): array
{
    return public_data_query(static function ($connection) use ($room, $limit): array {
        $statement = $connection->prepare(
            "SELECT rooms.*,
                    branches.name AS branch_name,
                    branches.electricity_price AS electricity_fee,
                    branches.water_price AS water_fee,
                    branches.service_price AS service_fee,
                    branches.parking_price AS parking_fee,
                    systems.name AS system_name,
                    districts.name AS district_name
             FROM rooms
             INNER JOIN branches ON branches.id = rooms.branch_id
             INNER JOIN systems ON systems.id = branches.system_id
             INNER JOIN districts ON districts.id = branches.district_id
             WHERE rooms.status = 'chua_lock'
               AND rooms.is_public_visible = 1
               AND rooms.id <> :room_id
               AND (branches.id = :branch_id OR districts.name = :district_name)
             ORDER BY rooms.updated_at DESC
             LIMIT {$limit}"
        );
        $statement->execute([
            'room_id' => $room['id'],
            'branch_id' => $room['branch_id'],
            'district_name' => $room['district_name'],
        ]);

        return $statement->fetchAll();
    }, []);
}
