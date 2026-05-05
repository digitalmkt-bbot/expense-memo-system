-- =====================================================================
-- Seed Data
-- Default password ของ user ทุกคน = "password123"
-- (hash: $2y$10$8K1VqK4hMBrUQz9kLjPBaO5VG6kGRr.L1z6XyZJxVrL4Wf7TL2QGm)
-- ปรับ hash ด้วย: php -r "echo password_hash('password123', PASSWORD_BCRYPT);"
-- =====================================================================

USE expense_memo;

-- ---------------------------------------------------------------------
-- Companies
-- ---------------------------------------------------------------------
INSERT INTO companies (id, company_code, company_name, is_active) VALUES
(1, 'LO', 'LOVE ISLAND CO., LTD.',     TRUE),
(2, 'AS', 'ANDAMAN SUNDAY CO., LTD.',  TRUE);

-- ---------------------------------------------------------------------
-- Departments — LOVE ISLAND (LO)
-- ---------------------------------------------------------------------
INSERT INTO departments (company_id, department_code, department_name, is_active) VALUES
(1, 'LO-SEC',      'SECRETARY',                                  TRUE),
(1, 'LO-HR',       'HUMAN RESOURCES',                            TRUE),
(1, 'LO-ACC-FIN',  'ACCOUNTING & FINANCE',                       TRUE),
(1, 'LO-BD-PR',    'Business Development / Public Relations',    TRUE),
(1, 'LO-MKT',      'MARKETING',                                  TRUE),
(1, 'LO-SA',       'SALES AGENT',                                TRUE),
(1, 'LO-RES',      'RESERVATION',                                TRUE),
(1, 'LO-ONL',      'ONLINE',                                     TRUE),
(1, 'LO-SVC',      'SERVICE',                                    TRUE),
(1, 'LO-MEC',      'MECHANIC',                                   TRUE),
(1, 'LO-PKT-PORT', 'PHUKET PORT',                                TRUE),
(1, 'LO-TL-PORT',  'TAP LAMU PORT',                              TRUE),
(1, 'LO-RNG-PORT', 'RANONG PORT',                                TRUE),
(1, 'LO-TL-SHOP',  'TAP LAMU PORT SHOP',                         TRUE);

-- ---------------------------------------------------------------------
-- Departments — ANDAMAN SUNDAY (AS)
-- ---------------------------------------------------------------------
INSERT INTO departments (company_id, department_code, department_name, is_active) VALUES
(2, 'AS-SA',       'SALES AGENT',           TRUE),
(2, 'AS-HR',       'HUMAN RESOURCES',       TRUE),
(2, 'AS-ACC-FIN',  'ACCOUNTING & FINANCE',  TRUE),
(2, 'AS-SVC',      'SERVICE',               TRUE);

-- ---------------------------------------------------------------------
-- Expense Categories
-- ---------------------------------------------------------------------
INSERT INTO expense_categories (category_code, category_name, is_active) VALUES
('ACCOMMODATION', 'Accommodation',     TRUE),
('FOOD',          'Food & Beverage',   TRUE),
('TRANSPORT',     'Transportation',    TRUE),
('MARKETING',     'Marketing Expense', TRUE),
('OPERATION',     'Operation Expense', TRUE),
('OFFICE',        'Office Expense',    TRUE),
('SUPPLIER',      'Supplier Payment',  TRUE),
('ALLOWANCE',     'Staff Allowance',   TRUE),
('MAINTENANCE',   'Maintenance',       TRUE),
('FUEL',          'Fuel',              TRUE),
('BOAT',          'Boat Expense',      TRUE),
('PORT',          'Port Expense',      TRUE),
('IT',            'IT / Software',     TRUE),
('OTHER',         'Other Expense',     TRUE);

-- ---------------------------------------------------------------------
-- Default Users (password = password123)
-- bcrypt cost 10
-- ---------------------------------------------------------------------
INSERT INTO users (full_name, email, password_hash, role, company_id, department_id, position, is_active) VALUES
('System Administrator',  'admin@loveandaman.com',
 '$2y$10$wH8N7MZ8PFR7Y1Q9J3LqWuLh8u8B7v2xq9r6Y4W3a8cKj9P0M1bDe',
 'admin',      1, 3,  'IT Admin',         TRUE),

('Director — Love Island','director.lo@loveandaman.com',
 '$2y$10$wH8N7MZ8PFR7Y1Q9J3LqWuLh8u8B7v2xq9r6Y4W3a8cKj9P0M1bDe',
 'director',   1, 1,  'Managing Director',TRUE),

('Accounting Officer LO', 'acc.lo@loveandaman.com',
 '$2y$10$wH8N7MZ8PFR7Y1Q9J3LqWuLh8u8B7v2xq9r6Y4W3a8cKj9P0M1bDe',
 'accounting', 1, 3,  'Accounting Lead',  TRUE),

('Marketing Manager LO',  'mkt.manager@loveandaman.com',
 '$2y$10$wH8N7MZ8PFR7Y1Q9J3LqWuLh8u8B7v2xq9r6Y4W3a8cKj9P0M1bDe',
 'manager',    1, 5,  'Marketing Manager',TRUE),

('Marketing Staff LO',    'mkt.staff@loveandaman.com',
 '$2y$10$wH8N7MZ8PFR7Y1Q9J3LqWuLh8u8B7v2xq9r6Y4W3a8cKj9P0M1bDe',
 'requester',  1, 5,  'Marketing Staff',  TRUE),

('Director — Andaman',    'director.as@loveandaman.com',
 '$2y$10$wH8N7MZ8PFR7Y1Q9J3LqWuLh8u8B7v2xq9r6Y4W3a8cKj9P0M1bDe',
 'director',   2, 17, 'Managing Director',TRUE),

('Accounting Officer AS', 'acc.as@loveandaman.com',
 '$2y$10$wH8N7MZ8PFR7Y1Q9J3LqWuLh8u8B7v2xq9r6Y4W3a8cKj9P0M1bDe',
 'accounting', 2, 17, 'Accounting Lead',  TRUE),

('Sales Agent Manager AS','sa.manager@loveandaman.com',
 '$2y$10$wH8N7MZ8PFR7Y1Q9J3LqWuLh8u8B7v2xq9r6Y4W3a8cKj9P0M1bDe',
 'manager',    2, 15, 'Sales Manager',    TRUE),

('Sales Agent Staff AS',  'sa.staff@loveandaman.com',
 '$2y$10$wH8N7MZ8PFR7Y1Q9J3LqWuLh8u8B7v2xq9r6Y4W3a8cKj9P0M1bDe',
 'requester',  2, 15, 'Sales Agent',      TRUE);

-- ---------------------------------------------------------------------
-- Approval Rules (Default)
-- 0 - 10,000      → Manager + Accounting
-- 10,001 - 50,000 → Manager + Accounting + Director
-- 50,001+         → Manager + Accounting + Director
-- ---------------------------------------------------------------------
INSERT INTO approval_rules (company_id, min_amount, max_amount, approval_level, approver_role, is_active) VALUES
(1, 0.00,        10000.00,    1, 'manager',    TRUE),
(1, 0.00,        10000.00,    2, 'accounting', TRUE),
(1, 10000.01,    50000.00,    1, 'manager',    TRUE),
(1, 10000.01,    50000.00,    2, 'accounting', TRUE),
(1, 10000.01,    50000.00,    3, 'director',   TRUE),
(1, 50000.01,    99999999.99, 1, 'manager',    TRUE),
(1, 50000.01,    99999999.99, 2, 'accounting', TRUE),
(1, 50000.01,    99999999.99, 3, 'director',   TRUE),
(2, 0.00,        10000.00,    1, 'manager',    TRUE),
(2, 0.00,        10000.00,    2, 'accounting', TRUE),
(2, 10000.01,    50000.00,    1, 'manager',    TRUE),
(2, 10000.01,    50000.00,    2, 'accounting', TRUE),
(2, 10000.01,    50000.00,    3, 'director',   TRUE),
(2, 50000.01,    99999999.99, 1, 'manager',    TRUE),
(2, 50000.01,    99999999.99, 2, 'accounting', TRUE),
(2, 50000.01,    99999999.99, 3, 'director',   TRUE);

-- ---------------------------------------------------------------------
-- Sample Suppliers
-- ---------------------------------------------------------------------
INSERT INTO suppliers (supplier_code, supplier_name, contact_name, phone, tax_id, is_active) VALUES
('SUP-001', 'Phuket Hotel Co., Ltd.', 'Khun A', '076-123456', '0105550000001', TRUE),
('SUP-002', 'Andaman Catering',       'Khun B', '076-234567', '0105550000002', TRUE),
('SUP-003', 'Marine Fuel Supply',     'Khun C', '076-345678', '0105550000003', TRUE);

-- ---------------------------------------------------------------------
-- Sample Project
-- ---------------------------------------------------------------------
INSERT INTO projects (project_code, project_name, company_id, department_id, status) VALUES
('LO-CAMPAIGN-2026-Q2', 'Q2 Marketing Campaign 2026', 1, 5, 'active'),
('LO-TRIP-PHIPHI-2026', 'Phi Phi Trip Operations 2026', 1, 9, 'active'),
('AS-AGENT-COMM-2026',  'Agent Commission 2026',       2, 15, 'active');
