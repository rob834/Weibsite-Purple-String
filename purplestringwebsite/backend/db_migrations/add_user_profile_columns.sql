-- Add profile fields to users table
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS display_name VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS bio TEXT NULL,
  ADD COLUMN IF NOT EXISTS phone VARCHAR(50) NULL,
  ADD COLUMN IF NOT EXISTS address TEXT NULL,
  ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) NULL;

-- Note: run this SQL on your MySQL server to add profile columns.
