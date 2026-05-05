-- Compatible with older MySQL/MariaDB versions that do not support
-- ALTER TABLE ... ADD COLUMN IF NOT EXISTS.

SET @schema_name = DATABASE();

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE branches ADD COLUMN electricity_price DECIMAL(10, 2) NOT NULL DEFAULT 0 AFTER manager_phone',
    'SELECT 1'
  )
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @schema_name
    AND TABLE_NAME = 'branches'
    AND COLUMN_NAME = 'electricity_price'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE branches ADD COLUMN water_price DECIMAL(10, 2) NOT NULL DEFAULT 0 AFTER electricity_price',
    'SELECT 1'
  )
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @schema_name
    AND TABLE_NAME = 'branches'
    AND COLUMN_NAME = 'water_price'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE branches ADD COLUMN parking_price DECIMAL(10, 2) NOT NULL DEFAULT 0 AFTER water_price',
    'SELECT 1'
  )
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @schema_name
    AND TABLE_NAME = 'branches'
    AND COLUMN_NAME = 'parking_price'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE branches ADD COLUMN service_price DECIMAL(10, 2) NOT NULL DEFAULT 0 AFTER parking_price',
    'SELECT 1'
  )
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @schema_name
    AND TABLE_NAME = 'branches'
    AND COLUMN_NAME = 'service_price'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Move legacy room-level fees to branch-level fees.
-- If rooms in the same branch have different fees, this uses the earliest room
-- that has any non-zero fee as the source, then falls back to the earliest room.
UPDATE branches
INNER JOIN (
  SELECT source_rooms.branch_id,
         source_rooms.electricity_fee AS electricity_price,
         source_rooms.water_fee AS water_price,
         source_rooms.parking_fee AS parking_price,
         source_rooms.service_fee AS service_price
  FROM rooms AS source_rooms
  INNER JOIN (
    SELECT branch_id,
           COALESCE(
             MIN(CASE WHEN electricity_fee > 0 OR water_fee > 0 OR parking_fee > 0 OR service_fee > 0 THEN id END),
             MIN(id)
           ) AS source_room_id
    FROM rooms
    GROUP BY branch_id
  ) AS first_fee_room ON first_fee_room.source_room_id = source_rooms.id
) AS legacy_fees ON legacy_fees.branch_id = branches.id
SET branches.electricity_price = legacy_fees.electricity_price,
    branches.water_price = legacy_fees.water_price,
    branches.parking_price = legacy_fees.parking_price,
    branches.service_price = legacy_fees.service_price;

