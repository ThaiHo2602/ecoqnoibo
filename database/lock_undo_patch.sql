SET NAMES utf8mb4;

ALTER TABLE lock_requests
  MODIFY request_status ENUM('pending', 'approved', 'rejected', 'undone') NOT NULL DEFAULT 'pending';
