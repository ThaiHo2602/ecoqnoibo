ALTER TABLE customer_leads
  ADD COLUMN assignment_status ENUM('pending', 'accepted', 'rejected') NULL AFTER assigned_by,
  ADD COLUMN assignment_response_note TEXT NULL AFTER assignment_status,
  ADD COLUMN assignment_responded_at DATETIME NULL AFTER assignment_response_note;

UPDATE customer_leads
SET assignment_status = CASE
    WHEN assigned_to IS NULL THEN NULL
    WHEN status = 'assigned' THEN 'accepted'
    ELSE assignment_status
END
WHERE assignment_status IS NULL;
