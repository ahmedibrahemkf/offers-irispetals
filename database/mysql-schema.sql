-- Iris Petals schema for MySQL 8+
-- Execute if you prefer manual SQL instead of php artisan migrate.

CREATE TABLE IF NOT EXISTS site_settings (
  id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  payload JSON NOT NULL
);

INSERT INTO site_settings (id, updated_at, payload)
VALUES (1, NOW(), JSON_OBJECT())
ON DUPLICATE KEY UPDATE id = id;

CREATE TABLE IF NOT EXISTS orders (
  id VARCHAR(80) NOT NULL PRIMARY KEY,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  payload JSON NOT NULL,
  INDEX idx_orders_created_at (created_at)
);

CREATE TABLE IF NOT EXISTS expenses (
  id VARCHAR(80) NOT NULL PRIMARY KEY,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  payload JSON NOT NULL,
  INDEX idx_expenses_created_at (created_at)
);
