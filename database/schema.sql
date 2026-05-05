CREATE DATABASE IF NOT EXISTS ecoq_noibo
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE ecoq_noibo;

SET NAMES utf8mb4;

DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS customer_leads;
DROP TABLE IF EXISTS lock_requests;
DROP TABLE IF EXISTS room_media;
DROP TABLE IF EXISTS rooms;
DROP TABLE IF EXISTS branches;
DROP TABLE IF EXISTS wards;
DROP TABLE IF EXISTS systems;
DROP TABLE IF EXISTS districts;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;

CREATE TABLE roles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE,
  display_name VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role_id INT UNSIGNED NOT NULL,
  full_name VARCHAR(150) NOT NULL,
  job_title VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NULL,
  email VARCHAR(150) NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  account_status ENUM('active', 'locked') NOT NULL DEFAULT 'active',
  last_login_at DATETIME NULL,
  remember_token_hash VARCHAR(255) NULL,
  remember_token_expires_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

CREATE TABLE districts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE systems (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL UNIQUE,
  description TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE wards (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  description TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE branches (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  system_id INT UNSIGNED NOT NULL,
  ward_id INT UNSIGNED NOT NULL,
  district_id INT UNSIGNED NOT NULL,
  name VARCHAR(150) NOT NULL,
  manager_phone VARCHAR(20) NULL,
  electricity_price DECIMAL(10, 2) NOT NULL DEFAULT 0,
  water_price DECIMAL(10, 2) NOT NULL DEFAULT 0,
  parking_price DECIMAL(10, 2) NOT NULL DEFAULT 0,
  service_price DECIMAL(10, 2) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_branches_system FOREIGN KEY (system_id) REFERENCES systems(id) ON DELETE CASCADE,
  CONSTRAINT fk_branches_ward FOREIGN KEY (ward_id) REFERENCES wards(id),
  CONSTRAINT fk_branches_district FOREIGN KEY (district_id) REFERENCES districts(id),
  KEY idx_branches_system (system_id),
  UNIQUE KEY uq_branch_per_system (system_id, name)
) ENGINE=InnoDB;

CREATE TABLE rooms (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  branch_id INT UNSIGNED NOT NULL,
  room_number VARCHAR(50) NOT NULL,
  price DECIMAL(12, 2) NOT NULL DEFAULT 0,
  room_type ENUM('duplex', 'studio', 'one_bedroom', 'two_bedroom', 'kiot') NOT NULL,
  electricity_fee DECIMAL(10, 2) NOT NULL DEFAULT 0,
  water_fee DECIMAL(10, 2) NOT NULL DEFAULT 0,
  service_fee DECIMAL(10, 2) NOT NULL DEFAULT 0,
  parking_fee DECIMAL(10, 2) NOT NULL DEFAULT 0,
  status ENUM('chua_lock', 'dang_giu', 'da_lock') NOT NULL DEFAULT 'chua_lock',
  is_public_visible TINYINT(1) NOT NULL DEFAULT 0,
  furniture_status ENUM('co_noi_that', 'khong_noi_that') NOT NULL,
  has_balcony TINYINT(1) NOT NULL DEFAULT 0,
  window_type ENUM('cua_so_troi', 'cua_so_hanh_lang', 'cua_so_gieng_troi') NOT NULL,
  note TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_rooms_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
  UNIQUE KEY uq_room_per_branch (branch_id, room_number)
) ENGINE=InnoDB;

CREATE TABLE room_media (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  room_id INT UNSIGNED NOT NULL,
  media_type ENUM('image', 'video') NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  mime_type VARCHAR(100) NOT NULL,
  file_size BIGINT UNSIGNED NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_room_media_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE lock_requests (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  room_id INT UNSIGNED NOT NULL,
  requested_by INT UNSIGNED NOT NULL,
  approved_by INT UNSIGNED NULL,
  request_status ENUM('pending', 'approved', 'rejected', 'undone') NOT NULL DEFAULT 'pending',
  request_note TEXT NULL,
  decision_note TEXT NULL,
  requested_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  decided_at DATETIME NULL,
  CONSTRAINT fk_lock_requests_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
  CONSTRAINT fk_lock_requests_requested_by FOREIGN KEY (requested_by) REFERENCES users(id),
  CONSTRAINT fk_lock_requests_approved_by FOREIGN KEY (approved_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE customer_leads (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  created_by INT UNSIGNED NOT NULL,
  assigned_to INT UNSIGNED NULL,
  assigned_by INT UNSIGNED NULL,
  assignment_status ENUM('pending', 'accepted', 'rejected') NULL,
  assignment_response_note TEXT NULL,
  assignment_responded_at DATETIME NULL,
  customer_name VARCHAR(150) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  note TEXT NULL,
  planning_scope ENUM('week', 'month') NOT NULL DEFAULT 'week',
  appointment_at DATETIME NOT NULL,
  status ENUM('new', 'assigned', 'completed', 'canceled', 'rescheduled', 'deposited') NOT NULL DEFAULT 'new',
  selected_room_id INT UNSIGNED NULL,
  completed_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_customer_leads_created_by FOREIGN KEY (created_by) REFERENCES users(id),
  CONSTRAINT fk_customer_leads_assigned_to FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_customer_leads_assigned_by FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_customer_leads_selected_room FOREIGN KEY (selected_room_id) REFERENCES rooms(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE activity_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  action VARCHAR(100) NOT NULL,
  module VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  ip_address VARCHAR(45) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_activity_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO roles (name, display_name) VALUES
('director', 'Giam doc'),
('manager', 'Quan ly'),
('staff', 'Nhan vien'),
('collaborator', 'Cong tac vien');

INSERT INTO districts (name) VALUES
('Quan 1'),
('Quan 3'),
('Quan 7'),
('Quan Binh Thanh'),
('Quan Tan Binh');

INSERT INTO users (role_id, full_name, job_title, phone, email, username, password, account_status, last_login_at) VALUES
((SELECT id FROM roles WHERE name = 'director'), 'Nguyen Van Giam Doc', 'Giam doc', '0900000001', 'director@ecoq.local', 'director', '$2y$10$w.MADegCeqpOhNq5KrMS4uxPduDY9GjPGb/rJGi6i.HMBSs9PbEv.', 'active', NOW()),
((SELECT id FROM roles WHERE name = 'manager'), 'Tran Thi Quan Ly', 'Quan ly', '0900000002', 'manager@ecoq.local', 'manager', '$2y$10$w.MADegCeqpOhNq5KrMS4uxPduDY9GjPGb/rJGi6i.HMBSs9PbEv.', 'active', NOW()),
((SELECT id FROM roles WHERE name = 'staff'), 'Le Van Nhan Vien', 'Nhan vien', '0900000003', 'staff@ecoq.local', 'staff', '$2y$10$w.MADegCeqpOhNq5KrMS4uxPduDY9GjPGb/rJGi6i.HMBSs9PbEv.', 'active', NOW()),
((SELECT id FROM roles WHERE name = 'collaborator'), 'Pham Thi Cong Tac', 'Cong tac vien', '0900000004', 'collaborator@ecoq.local', 'collaborator', '$2y$10$w.MADegCeqpOhNq5KrMS4uxPduDY9GjPGb/rJGi6i.HMBSs9PbEv.', 'active', NOW());

INSERT INTO systems (name, description, is_active) VALUES
('Long Thinh', 'He thong tro Long Thinh', 1),
('An Phu', 'He thong tro An Phu', 1);

INSERT INTO wards (name, description) VALUES
('Phuong Tan Phong', 'Khu vuc Quan 7'),
('Phuong 25', 'Khu vuc Binh Thanh'),
('Phuong 13', 'Khu vuc Tan Binh');

INSERT INTO branches (system_id, ward_id, district_id, name, manager_phone) VALUES
((SELECT id FROM systems WHERE name = 'Long Thinh'), (SELECT id FROM wards WHERE name = 'Phuong Tan Phong'), (SELECT id FROM districts WHERE name = 'Quan 7'), 'Long Thinh 1', '0911111111'),
((SELECT id FROM systems WHERE name = 'Long Thinh'), (SELECT id FROM wards WHERE name = 'Phuong 25'), (SELECT id FROM districts WHERE name = 'Quan Binh Thanh'), 'Long Thinh 2', '0922222222'),
((SELECT id FROM systems WHERE name = 'An Phu'), (SELECT id FROM wards WHERE name = 'Phuong 13'), (SELECT id FROM districts WHERE name = 'Quan Tan Binh'), 'An Phu 1', '0933333333');

INSERT INTO rooms (
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
) VALUES
((SELECT id FROM branches WHERE name = 'Long Thinh 1'), 'P101', 3500000, 'duplex', 3500, 120000, 150000, 100000, 'chua_lock', 1, 'co_noi_that', 1, 'cua_so_troi', 'Phong gan cau thang, phu hop 1-2 nguoi.'),
((SELECT id FROM branches WHERE name = 'Long Thinh 1'), 'P102', 2800000, 'studio', 3500, 100000, 150000, 100000, 'dang_giu', 0, 'khong_noi_that', 0, 'cua_so_hanh_lang', 'Dang co khach giu cho den het ngay.'),
((SELECT id FROM branches WHERE name = 'An Phu 1'), 'A201', 4200000, 'one_bedroom', 4000, 150000, 200000, 120000, 'da_lock', 0, 'co_noi_that', 1, 'cua_so_gieng_troi', 'Da lock thanh cong cho khach moi.');

INSERT INTO lock_requests (room_id, requested_by, approved_by, request_status, request_note, decision_note, requested_at, decided_at) VALUES
((SELECT id FROM rooms WHERE room_number = 'P102'), (SELECT id FROM users WHERE username = 'staff'), NULL, 'pending', 'Khach hen chuyen vao cuoi tuan nay.', NULL, NOW(), NULL),
((SELECT id FROM rooms WHERE room_number = 'A201'), (SELECT id FROM users WHERE username = 'staff'), (SELECT id FROM users WHERE username = 'manager'), 'approved', 'Khach dat coc giu phong.', 'Da xac nhan lock.', NOW(), NOW());

INSERT INTO activity_logs (user_id, action, module, description, ip_address) VALUES
((SELECT id FROM users WHERE username = 'director'), 'seed_create', 'system', 'Khoi tao du lieu mau he thong va chi nhanh.', '127.0.0.1'),
((SELECT id FROM users WHERE username = 'manager'), 'approve_lock', 'lock_requests', 'Duyet lock phong A201.', '127.0.0.1'),
((SELECT id FROM users WHERE username = 'staff'), 'request_lock', 'lock_requests', 'Gui yeu cau lock phong P102.', '127.0.0.1');

INSERT INTO customer_leads (
  created_by,
  assigned_to,
  assigned_by,
  assignment_status,
  customer_name,
  phone,
  note,
  planning_scope,
  appointment_at,
  status,
  selected_room_id
) VALUES
((SELECT id FROM users WHERE username = 'collaborator'), NULL, NULL, NULL, 'Khach Hang Mau 1', '0988000001', 'Hen gap vao thu hai de xem phong.', 'week', DATE_ADD(NOW(), INTERVAL 2 DAY), 'new', NULL),
((SELECT id FROM users WHERE username = 'collaborator'), (SELECT id FROM users WHERE username = 'staff'), (SELECT id FROM users WHERE username = 'director'), 'accepted', 'Khach Hang Mau 2', '0988000002', 'Da duoc phan cho nhan vien theo doi.', 'month', DATE_ADD(NOW(), INTERVAL 5 DAY), 'assigned', NULL);
