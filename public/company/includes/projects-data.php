<?php

require_once dirname(__DIR__, 3) . '/bootstrap.php';

use App\Core\Database;

function room_status_label(string $status): string
{
    return match ($status) {
        'chua_lock' => 'Chưa lock',
        'dang_giu' => 'Đang giữ',
        'da_lock' => 'Đã lock',
        default => $status,
    };
}

function room_type_label(string $type): string
{
    return match ($type) {
        'co_gac' => 'Có gác',
        'khong_gac' => 'Không gác',
        default => $type,
    };
}

function furniture_status_label(string $status): string
{
    return match ($status) {
        'co_noi_that' => 'Có nội thất',
        'khong_noi_that' => 'Không nội thất',
        default => $status,
    };
}

function window_type_label(string $type): string
{
    return match ($type) {
        'cua_so_troi' => 'Cửa sổ trời',
        'cua_so_hanh_lang' => 'Cửa sổ hành lang',
        'cua_so_gieng_troi' => 'Cửa sổ giếng trời',
        default => $type,
    };
}

function room_media_url(string $filePath): string
{
    return media_url($filePath);
}

function fetch_public_rooms(): array
{
    $statement = Database::connection()->query(
        "SELECT rooms.*,
                branches.name AS branch_name,
                branches.address AS branch_address,
                branches.manager_phone,
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
}

function fetch_room_media_map(array $roomIds): array
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

function fetch_public_room_detail(int $roomId): ?array
{
    if ($roomId <= 0) {
        return null;
    }

    $statement = Database::connection()->prepare(
        "SELECT rooms.*,
                branches.name AS branch_name,
                branches.address AS branch_address,
                branches.manager_phone,
                branches.id AS branch_id,
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
}

function fetch_public_room_media(int $roomId): array
{
    $statement = Database::connection()->prepare(
        'SELECT * FROM room_media WHERE room_id = :room_id ORDER BY created_at DESC'
    );
    $statement->execute(['room_id' => $roomId]);

    return $statement->fetchAll();
}

function fetch_related_public_rooms(array $room, int $limit = 6): array
{
    $statement = Database::connection()->prepare(
        "SELECT rooms.*,
                branches.name AS branch_name,
                branches.address AS branch_address,
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
}
