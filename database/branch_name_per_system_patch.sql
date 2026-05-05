SET NAMES utf8mb4;

-- Allow the same branch name in different systems.
-- Old rule was unique by ward + name, which blocked duplicate branch names across systems.

-- MySQL may be using uq_branch_per_ward as the supporting index for fk_branches_ward.
-- Add a normal ward_id index first so the foreign key remains valid after dropping the unique index.
SET @has_branch_ward_index := (
  SELECT COUNT(*)
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'branches'
    AND INDEX_NAME = 'idx_branches_ward'
);
SET @sql := IF(
  @has_branch_ward_index = 0,
  'ALTER TABLE branches ADD KEY idx_branches_ward (ward_id)',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_old_branch_ward_unique := (
  SELECT COUNT(*)
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'branches'
    AND INDEX_NAME = 'uq_branch_per_ward'
);
SET @sql := IF(
  @has_old_branch_ward_unique > 0,
  'ALTER TABLE branches DROP INDEX uq_branch_per_ward',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_branch_system_unique := (
  SELECT COUNT(*)
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'branches'
    AND INDEX_NAME = 'uq_branch_per_system'
);

SET @duplicate_branch_names_in_system := (
  SELECT COUNT(*)
  FROM (
    SELECT system_id, name
    FROM branches
    GROUP BY system_id, name
    HAVING COUNT(*) > 1
  ) AS duplicates
);

SET @sql := IF(
  @has_branch_system_unique = 0 AND @duplicate_branch_names_in_system = 0,
  'ALTER TABLE branches ADD UNIQUE KEY uq_branch_per_system (system_id, name)',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
