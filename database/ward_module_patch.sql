SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS wards (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  description TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE wards
  CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

SET @has_branch_ward := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'branches'
    AND COLUMN_NAME = 'ward_id'
);
SET @sql := IF(
  @has_branch_ward = 0,
  'ALTER TABLE branches ADD COLUMN ward_id INT UNSIGNED NULL AFTER system_id',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT IGNORE INTO wards (name, description)
VALUES ('Phuong mac dinh', 'Du lieu tu dong tao de gan cac chi nhanh cu.');

UPDATE branches
SET ward_id = (SELECT id FROM wards WHERE name = 'Phuong mac dinh' LIMIT 1)
WHERE ward_id IS NULL;

ALTER TABLE branches
  MODIFY ward_id INT UNSIGNED NOT NULL;

SET @has_old_ward_fk := (
  SELECT COUNT(*)
  FROM information_schema.REFERENTIAL_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND CONSTRAINT_NAME = 'fk_wards_system'
);
SET @sql := IF(
  @has_old_ward_fk > 0,
  'ALTER TABLE wards DROP FOREIGN KEY fk_wards_system',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_ward_system := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'wards'
    AND COLUMN_NAME = 'system_id'
);
SET @sql := IF(
  @has_ward_system > 0,
  'ALTER TABLE wards DROP COLUMN system_id',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_old_ward_unique := (
  SELECT COUNT(*)
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'wards'
    AND INDEX_NAME = 'uq_ward_per_system'
);
SET @sql := IF(
  @has_old_ward_unique > 0,
  'ALTER TABLE wards DROP INDEX uq_ward_per_system',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_ward_name_unique := (
  SELECT COUNT(*)
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'wards'
    AND INDEX_NAME = 'uq_ward_name'
);
SET @sql := IF(
  @has_ward_name_unique > 0,
  'ALTER TABLE wards DROP INDEX uq_ward_name',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_branch_ward_fk := (
  SELECT COUNT(*)
  FROM information_schema.REFERENTIAL_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND CONSTRAINT_NAME = 'fk_branches_ward'
);
SET @sql := IF(
  @has_branch_ward_fk = 0,
  'ALTER TABLE branches ADD CONSTRAINT fk_branches_ward FOREIGN KEY (ward_id) REFERENCES wards(id)',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
