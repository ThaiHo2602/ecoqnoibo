SET NAMES utf8mb4;

SET @current_database := DATABASE();
SET @sql := CONCAT(
  'ALTER DATABASE `',
  REPLACE(@current_database, '`', '``'),
  '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

ALTER TABLE wards
  CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE branches
  CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE systems
  CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE districts
  CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
