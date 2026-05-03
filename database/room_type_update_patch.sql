ALTER TABLE rooms
  MODIFY room_type ENUM('co_gac', 'khong_gac', 'duplet', 'studio', 'one_bedroom', 'two_bedroom', 'kiot') NOT NULL;

UPDATE rooms
SET room_type = CASE room_type
  WHEN 'co_gac' THEN 'duplet'
  WHEN 'khong_gac' THEN 'studio'
  ELSE room_type
END;

ALTER TABLE rooms
  MODIFY room_type ENUM('duplet', 'studio', 'one_bedroom', 'two_bedroom', 'kiot') NOT NULL;
