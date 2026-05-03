USE ecoq_noibo;

SET NAMES utf8mb4;

INSERT IGNORE INTO districts (name) VALUES
('Quận 1'),
('Quận 3'),
('Quận 4'),
('Quận 5'),
('Quận 7'),
('Quận 10'),
('Quận 11'),
('Quận Bình Thạnh'),
('Quận Tân Bình'),
('Thủ Đức');

INSERT IGNORE INTO systems (name, description, is_active) VALUES
('Hệ thống mẫu 01', 'Dữ liệu mẫu hệ thống 01', 1),
('Hệ thống mẫu 02', 'Dữ liệu mẫu hệ thống 02', 1),
('Hệ thống mẫu 03', 'Dữ liệu mẫu hệ thống 03', 1),
('Hệ thống mẫu 04', 'Dữ liệu mẫu hệ thống 04', 1),
('Hệ thống mẫu 05', 'Dữ liệu mẫu hệ thống 05', 1),
('Hệ thống mẫu 06', 'Dữ liệu mẫu hệ thống 06', 1),
('Hệ thống mẫu 07', 'Dữ liệu mẫu hệ thống 07', 1),
('Hệ thống mẫu 08', 'Dữ liệu mẫu hệ thống 08', 1),
('Hệ thống mẫu 09', 'Dữ liệu mẫu hệ thống 09', 1),
('Hệ thống mẫu 10', 'Dữ liệu mẫu hệ thống 10', 1);

INSERT IGNORE INTO wards (name, description) VALUES
('Phuong mau 01', 'Phuong du lieu mau'),
('Phuong mau 02', 'Phuong du lieu mau'),
('Phuong mau 03', 'Phuong du lieu mau');

INSERT IGNORE INTO users (role_id, full_name, job_title, phone, email, username, password, account_status, last_login_at)
SELECT
    (SELECT id FROM roles WHERE name = 'staff'),
    'Nhân viên mẫu 01',
    'Nhân viên kinh doanh',
    '0901000001',
    'staff01@ecoq.local',
    'staff01',
    '$2y$10$w.MADegCeqpOhNq5KrMS4uxPduDY9GjPGb/rJGi6i.HMBSs9PbEv.',
    'active',
    NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'staff01');

INSERT IGNORE INTO users (role_id, full_name, job_title, phone, email, username, password, account_status, last_login_at)
SELECT
    (SELECT id FROM roles WHERE name = 'staff'),
    'Nhân viên mẫu 02',
    'Nhân viên kinh doanh',
    '0901000002',
    'staff02@ecoq.local',
    'staff02',
    '$2y$10$w.MADegCeqpOhNq5KrMS4uxPduDY9GjPGb/rJGi6i.HMBSs9PbEv.',
    'active',
    NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'staff02');

INSERT IGNORE INTO users (role_id, full_name, job_title, phone, email, username, password, account_status, last_login_at)
SELECT
    (SELECT id FROM roles WHERE name = 'staff'),
    'Nhân viên mẫu 03',
    'Nhân viên kinh doanh',
    '0901000003',
    'staff03@ecoq.local',
    'staff03',
    '$2y$10$w.MADegCeqpOhNq5KrMS4uxPduDY9GjPGb/rJGi6i.HMBSs9PbEv.',
    'active',
    NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'staff03');

INSERT IGNORE INTO users (role_id, full_name, job_title, phone, email, username, password, account_status, last_login_at)
SELECT
    (SELECT id FROM roles WHERE name = 'staff'),
    'Nhân viên mẫu 04',
    'Nhân viên kinh doanh',
    '0901000004',
    'staff04@ecoq.local',
    'staff04',
    '$2y$10$w.MADegCeqpOhNq5KrMS4uxPduDY9GjPGb/rJGi6i.HMBSs9PbEv.',
    'active',
    NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'staff04');

INSERT IGNORE INTO users (role_id, full_name, job_title, phone, email, username, password, account_status, last_login_at)
SELECT
    (SELECT id FROM roles WHERE name = 'staff'),
    'Nhân viên mẫu 05',
    'Nhân viên kinh doanh',
    '0901000005',
    'staff05@ecoq.local',
    'staff05',
    '$2y$10$w.MADegCeqpOhNq5KrMS4uxPduDY9GjPGb/rJGi6i.HMBSs9PbEv.',
    'active',
    NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'staff05');

INSERT IGNORE INTO users (role_id, full_name, job_title, phone, email, username, password, account_status, last_login_at)
SELECT
    (SELECT id FROM roles WHERE name = 'staff'),
    'Nhân viên mẫu 06',
    'Nhân viên kinh doanh',
    '0901000006',
    'staff06@ecoq.local',
    'staff06',
    '$2y$10$w.MADegCeqpOhNq5KrMS4uxPduDY9GjPGb/rJGi6i.HMBSs9PbEv.',
    'active',
    NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'staff06');

INSERT IGNORE INTO users (role_id, full_name, job_title, phone, email, username, password, account_status, last_login_at)
SELECT
    (SELECT id FROM roles WHERE name = 'staff'),
    'Nhân viên mẫu 07',
    'Nhân viên kinh doanh',
    '0901000007',
    'staff07@ecoq.local',
    'staff07',
    '$2y$10$w.MADegCeqpOhNq5KrMS4uxPduDY9GjPGb/rJGi6i.HMBSs9PbEv.',
    'active',
    NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'staff07');

INSERT IGNORE INTO users (role_id, full_name, job_title, phone, email, username, password, account_status, last_login_at)
SELECT
    (SELECT id FROM roles WHERE name = 'staff'),
    'Nhân viên mẫu 08',
    'Nhân viên kinh doanh',
    '0901000008',
    'staff08@ecoq.local',
    'staff08',
    '$2y$10$w.MADegCeqpOhNq5KrMS4uxPduDY9GjPGb/rJGi6i.HMBSs9PbEv.',
    'active',
    NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'staff08');

INSERT IGNORE INTO users (role_id, full_name, job_title, phone, email, username, password, account_status, last_login_at)
SELECT
    (SELECT id FROM roles WHERE name = 'staff'),
    'Nhân viên mẫu 09',
    'Nhân viên kinh doanh',
    '0901000009',
    'staff09@ecoq.local',
    'staff09',
    '$2y$10$w.MADegCeqpOhNq5KrMS4uxPduDY9GjPGb/rJGi6i.HMBSs9PbEv.',
    'active',
    NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'staff09');

INSERT IGNORE INTO users (role_id, full_name, job_title, phone, email, username, password, account_status, last_login_at)
SELECT
    (SELECT id FROM roles WHERE name = 'staff'),
    'Nhân viên mẫu 10',
    'Nhân viên kinh doanh',
    '0901000010',
    'staff10@ecoq.local',
    'staff10',
    '$2y$10$w.MADegCeqpOhNq5KrMS4uxPduDY9GjPGb/rJGi6i.HMBSs9PbEv.',
    'active',
    NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'staff10');

DROP PROCEDURE IF EXISTS seed_sample_branches;
DELIMITER $$
CREATE PROCEDURE seed_sample_branches()
BEGIN
    DECLARE idx INT DEFAULT 1;
    DECLARE system_idx INT;
    DECLARE district_name_value VARCHAR(100);

    WHILE idx <= 30 DO
        SET system_idx = ((idx - 1) MOD 10) + 1;
        SET district_name_value = CASE ((idx - 1) MOD 10) + 1
            WHEN 1 THEN 'Quận 1'
            WHEN 2 THEN 'Quận 3'
            WHEN 3 THEN 'Quận 4'
            WHEN 4 THEN 'Quận 5'
            WHEN 5 THEN 'Quận 7'
            WHEN 6 THEN 'Quận 10'
            WHEN 7 THEN 'Quận 11'
            WHEN 8 THEN 'Quận Bình Thạnh'
            WHEN 9 THEN 'Quận Tân Bình'
            ELSE 'Thủ Đức'
        END;

        INSERT IGNORE INTO branches (system_id, ward_id, district_id, name, address, manager_phone)
        VALUES (
            (SELECT id FROM systems WHERE name = CONCAT('Hệ thống mẫu ', LPAD(system_idx, 2, '0')) LIMIT 1),
            (SELECT id FROM wards WHERE name = CONCAT('Phuong mau ', LPAD(((idx - 1) MOD 3) + 1, 2, '0')) LIMIT 1),
            (SELECT id FROM districts WHERE name = district_name_value LIMIT 1),
            CONCAT('Chi nhánh mẫu ', LPAD(idx, 2, '0')),
            CONCAT(100 + idx, ' Đường nội bộ ', LPAD(idx, 2, '0'), ', ', district_name_value),
            CONCAT('09', LPAD(idx, 8, '0'))
        );

        SET idx = idx + 1;
    END WHILE;
END$$
DELIMITER ;
CALL seed_sample_branches();
DROP PROCEDURE IF EXISTS seed_sample_branches;

DROP PROCEDURE IF EXISTS seed_sample_rooms;
DELIMITER $$
CREATE PROCEDURE seed_sample_rooms()
BEGIN
    DECLARE idx INT DEFAULT 1;
    DECLARE branch_idx INT;

    WHILE idx <= 100 DO
        SET branch_idx = ((idx - 1) MOD 30) + 1;

        INSERT IGNORE INTO rooms (
            branch_id,
            room_number,
            price,
            room_type,
            electricity_fee,
            water_fee,
            service_fee,
            parking_fee,
            status,
            is_public_visible,
            furniture_status,
            has_balcony,
            window_type,
            note
        ) VALUES (
            (SELECT id FROM branches WHERE name = CONCAT('Chi nhánh mẫu ', LPAD(branch_idx, 2, '0')) LIMIT 1),
            CONCAT('P', LPAD(idx, 3, '0')),
            2500000 + (idx * 55000),
            CASE
                WHEN MOD(idx, 5) = 0 THEN 'duplet'
                WHEN MOD(idx, 5) = 1 THEN 'studio'
                WHEN MOD(idx, 5) = 2 THEN 'one_bedroom'
                WHEN MOD(idx, 5) = 3 THEN 'two_bedroom'
                ELSE 'kiot'
            END,
            3500 + (MOD(idx, 4) * 500),
            90000 + (MOD(idx, 5) * 10000),
            120000 + (MOD(idx, 4) * 15000),
            80000 + (MOD(idx, 3) * 10000),
            CASE
                WHEN MOD(idx, 9) = 0 THEN 'da_lock'
                WHEN MOD(idx, 4) = 0 THEN 'dang_giu'
                ELSE 'chua_lock'
            END,
            IF(MOD(idx, 5) = 0 AND MOD(idx, 9) <> 0 AND MOD(idx, 4) <> 0, 1, 0),
            IF(MOD(idx, 3) = 0, 'co_noi_that', 'khong_noi_that'),
            IF(MOD(idx, 2) = 0, 1, 0),
            CASE MOD(idx, 3)
                WHEN 0 THEN 'cua_so_troi'
                WHEN 1 THEN 'cua_so_hanh_lang'
                ELSE 'cua_so_gieng_troi'
            END,
            CONCAT('Phòng mẫu số ', idx, ', phù hợp cho khách cần dữ liệu demo để test lọc và lock phòng.')
        );

        SET idx = idx + 1;
    END WHILE;
END$$
DELIMITER ;
CALL seed_sample_rooms();
DROP PROCEDURE IF EXISTS seed_sample_rooms;
