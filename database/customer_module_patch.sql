USE ecoq_noibo;

SET NAMES utf8mb4;

INSERT INTO roles (name, display_name)
SELECT 'collaborator', 'Cong tac vien'
WHERE NOT EXISTS (
  SELECT 1 FROM roles WHERE name = 'collaborator'
);

CREATE TABLE IF NOT EXISTS customer_leads (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  created_by INT UNSIGNED NOT NULL,
  assigned_to INT UNSIGNED NULL,
  assigned_by INT UNSIGNED NULL,
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

INSERT INTO users (role_id, full_name, job_title, phone, email, username, password, account_status, last_login_at)
SELECT
  (SELECT id FROM roles WHERE name = 'collaborator'),
  'Pham Thi Cong Tac',
  'Cong tac vien',
  '0900000004',
  'collaborator@ecoq.local',
  'collaborator',
  '$2y$10$w.MADegCeqpOhNq5KrMS4uxPduDY9GjPGb/rJGi6i.HMBSs9PbEv.',
  'active',
  NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM users WHERE username = 'collaborator'
);

INSERT INTO customer_leads (
  created_by,
  assigned_to,
  assigned_by,
  customer_name,
  phone,
  note,
  planning_scope,
  appointment_at,
  status,
  selected_room_id
)
SELECT
  (SELECT id FROM users WHERE username = 'collaborator'),
  NULL,
  NULL,
  'Khach Hang Mau 1',
  '0988000001',
  'Hen gap vao thu hai de xem phong.',
  'week',
  DATE_ADD(NOW(), INTERVAL 2 DAY),
  'new',
  NULL
WHERE NOT EXISTS (
  SELECT 1 FROM customer_leads WHERE customer_name = 'Khach Hang Mau 1' AND phone = '0988000001'
);
