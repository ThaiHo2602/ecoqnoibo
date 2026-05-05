ALTER TABLE rooms
  MODIFY room_type ENUM('co_gac', 'khong_gac', 'duplex', 'studio', 'one_bedroom', 'two_bedroom', 'kiot') NOT NULL;

UPDATE rooms
SET room_type = CASE room_type
  WHEN 'co_gac' THEN 'duplex'
  WHEN 'khong_gac' THEN 'studio'
  ELSE room_type
END;

ALTER TABLE rooms
  MODIFY room_type ENUM('duplex', 'studio', 'one_bedroom', 'two_bedroom', 'kiot') NOT NULL;
