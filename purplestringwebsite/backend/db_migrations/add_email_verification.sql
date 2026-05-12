-- Add email verification fields to users table
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS email VARCHAR(255) NULL UNIQUE,
  ADD COLUMN IF NOT EXISTS email_verified TINYINT(1) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS verification_token VARCHAR(255) NULL;

-- Note: run this SQL on your MySQL server to add email verification columns.
