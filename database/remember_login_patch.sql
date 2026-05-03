ALTER TABLE users
  ADD COLUMN remember_token_hash VARCHAR(255) NULL AFTER last_login_at,
  ADD COLUMN remember_token_expires_at DATETIME NULL AFTER remember_token_hash;
