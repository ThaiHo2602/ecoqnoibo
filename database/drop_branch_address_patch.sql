SET @branch_address_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'branches'
      AND COLUMN_NAME = 'address'
);

SET @drop_branch_address_sql := IF(
    @branch_address_exists > 0,
    'ALTER TABLE branches DROP COLUMN address',
    'SELECT ''branches.address already removed'' AS message'
);

PREPARE drop_branch_address_stmt FROM @drop_branch_address_sql;
EXECUTE drop_branch_address_stmt;
DEALLOCATE PREPARE drop_branch_address_stmt;
