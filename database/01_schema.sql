-- =====================================================================
-- Expense Memo Management System
-- Companies: LOVE ISLAND CO., LTD. (LO) / ANDAMAN SUNDAY CO., LTD. (AS)
-- Database: MySQL 8.0+ / MariaDB 10.5+
-- Charset : utf8mb4 (รองรับภาษาไทย + Emoji)
-- =====================================================================

CREATE DATABASE IF NOT EXISTS expense_memo
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE expense_memo;

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- 1. companies
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS companies;
CREATE TABLE companies (
    id           BIGINT PRIMARY KEY AUTO_INCREMENT,
    company_code VARCHAR(50)  UNIQUE NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    tax_id       VARCHAR(50),
    address      TEXT,
    phone        VARCHAR(50),
    email        VARCHAR(255),
    is_active    BOOLEAN DEFAULT TRUE,
    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2. departments
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS departments;
CREATE TABLE departments (
    id              BIGINT PRIMARY KEY AUTO_INCREMENT,
    company_id      BIGINT NOT NULL,
    department_code VARCHAR(50)  NOT NULL,
    department_name VARCHAR(255) NOT NULL,
    is_active       BOOLEAN DEFAULT TRUE,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_dept_code (company_id, department_code),
    CONSTRAINT fk_dept_company FOREIGN KEY (company_id) REFERENCES companies(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 3. users
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id            BIGINT PRIMARY KEY AUTO_INCREMENT,
    full_name     VARCHAR(255) NOT NULL,
    email         VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone         VARCHAR(50),
    position      VARCHAR(255),
    company_id    BIGINT,
    department_id BIGINT,
    role          ENUM('requester','manager','accounting','director','admin') NOT NULL,
    is_active     BOOLEAN DEFAULT TRUE,
    last_login_at DATETIME,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_users_company (company_id),
    KEY idx_users_dept    (department_id),
    KEY idx_users_role    (role),
    CONSTRAINT fk_user_company FOREIGN KEY (company_id)    REFERENCES companies(id),
    CONSTRAINT fk_user_dept    FOREIGN KEY (department_id) REFERENCES departments(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 4. projects
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS projects;
CREATE TABLE projects (
    id            BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_code  VARCHAR(100) UNIQUE NOT NULL,
    project_name  VARCHAR(255) NOT NULL,
    company_id    BIGINT,
    department_id BIGINT,
    start_date    DATE,
    end_date      DATE,
    status        ENUM('active','inactive','completed') DEFAULT 'active',
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_proj_company FOREIGN KEY (company_id)    REFERENCES companies(id),
    CONSTRAINT fk_proj_dept    FOREIGN KEY (department_id) REFERENCES departments(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 5. expense_categories
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS expense_categories;
CREATE TABLE expense_categories (
    id              BIGINT PRIMARY KEY AUTO_INCREMENT,
    category_code   VARCHAR(50)  UNIQUE NOT NULL,
    category_name   VARCHAR(255) NOT NULL,
    accounting_code VARCHAR(100),
    description     TEXT,
    is_active       BOOLEAN DEFAULT TRUE,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 6. suppliers
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS suppliers;
CREATE TABLE suppliers (
    id                  BIGINT PRIMARY KEY AUTO_INCREMENT,
    supplier_code       VARCHAR(50) UNIQUE,
    supplier_name       VARCHAR(255) NOT NULL,
    contact_name        VARCHAR(255),
    phone               VARCHAR(50),
    email               VARCHAR(255),
    tax_id              VARCHAR(50),
    address             TEXT,
    bank_name           VARCHAR(255),
    bank_account_name   VARCHAR(255),
    bank_account_number VARCHAR(100),
    is_active           BOOLEAN DEFAULT TRUE,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 7. memos (ตารางหลัก)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS memos;
CREATE TABLE memos (
    id                     BIGINT PRIMARY KEY AUTO_INCREMENT,
    memo_no                VARCHAR(100) UNIQUE,
    company_id             BIGINT NOT NULL,
    department_id          BIGINT NOT NULL,
    project_id             BIGINT,
    requester_id           BIGINT NOT NULL,
    memo_date              DATE   NOT NULL,
    required_payment_date  DATE,
    memo_type              ENUM('advance_payment','reimbursement','supplier_payment',
                                'general_expense','petty_cash','other') NOT NULL,
    subject                VARCHAR(255) NOT NULL,
    objective              TEXT,
    description            TEXT,
    total_amount           DECIMAL(12,2) DEFAULT 0.00,
    vat_amount             DECIMAL(12,2) DEFAULT 0.00,
    wht_amount             DECIMAL(12,2) DEFAULT 0.00,
    net_amount             DECIMAL(12,2) DEFAULT 0.00,
    currency               VARCHAR(10)   DEFAULT 'THB',
    status                 ENUM('draft','submitted','manager_approved','accounting_checked',
                                'director_approved','approved','revision_required','rejected',
                                'pending_payment','partially_paid','paid','closed','cancelled')
                                DEFAULT 'draft',
    payment_status         ENUM('unpaid','pending_payment','partially_paid','paid','cancelled')
                                DEFAULT 'unpaid',
    current_approval_step  INT DEFAULT 0,
    submitted_at           DATETIME,
    approved_at            DATETIME,
    rejected_at            DATETIME,
    paid_at                DATETIME,
    closed_at              DATETIME,
    created_by             BIGINT,
    updated_by             BIGINT,
    created_at             DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at             DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_memo_company   (company_id),
    KEY idx_memo_dept      (department_id),
    KEY idx_memo_requester (requester_id),
    KEY idx_memo_status    (status),
    KEY idx_memo_pay_status(payment_status),
    KEY idx_memo_date      (memo_date),
    CONSTRAINT fk_memo_company   FOREIGN KEY (company_id)    REFERENCES companies(id),
    CONSTRAINT fk_memo_dept      FOREIGN KEY (department_id) REFERENCES departments(id),
    CONSTRAINT fk_memo_project   FOREIGN KEY (project_id)    REFERENCES projects(id),
    CONSTRAINT fk_memo_requester FOREIGN KEY (requester_id)  REFERENCES users(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 8. memo_items (รายการค่าใช้จ่ายย่อย)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS memo_items;
CREATE TABLE memo_items (
    id              BIGINT PRIMARY KEY AUTO_INCREMENT,
    memo_id         BIGINT NOT NULL,
    expense_date    DATE,
    category_id     BIGINT,
    supplier_id     BIGINT,
    item_name       VARCHAR(255) NOT NULL,
    description     TEXT,
    quantity        DECIMAL(10,2) DEFAULT 1.00,
    unit            VARCHAR(50),
    unit_price      DECIMAL(12,2) DEFAULT 0.00,
    amount          DECIMAL(12,2) DEFAULT 0.00,
    vat_type        ENUM('none','include_vat','exclude_vat') DEFAULT 'none',
    vat_rate        DECIMAL(5,2)  DEFAULT 0.00,
    vat_amount      DECIMAL(12,2) DEFAULT 0.00,
    wht_type        ENUM('none','wht_1','wht_3','wht_5','custom') DEFAULT 'none',
    wht_rate        DECIMAL(5,2)  DEFAULT 0.00,
    wht_amount      DECIMAL(12,2) DEFAULT 0.00,
    net_amount      DECIMAL(12,2) DEFAULT 0.00,
    payment_method  ENUM('cash','bank_transfer','company_paid','reimbursement',
                         'payroll','credit_card','other') DEFAULT 'bank_transfer',
    note            TEXT,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_item_memo (memo_id),
    CONSTRAINT fk_item_memo     FOREIGN KEY (memo_id)     REFERENCES memos(id) ON DELETE CASCADE,
    CONSTRAINT fk_item_category FOREIGN KEY (category_id) REFERENCES expense_categories(id),
    CONSTRAINT fk_item_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 9. memo_payment_requests (ข้อมูลผู้รับเงิน)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS memo_payment_requests;
CREATE TABLE memo_payment_requests (
    id                  BIGINT PRIMARY KEY AUTO_INCREMENT,
    memo_id             BIGINT NOT NULL,
    payee_type          ENUM('employee','supplier','other') NOT NULL,
    payee_user_id       BIGINT,
    supplier_id         BIGINT,
    payee_name          VARCHAR(255) NOT NULL,
    bank_name           VARCHAR(255),
    bank_account_name   VARCHAR(255),
    bank_account_number VARCHAR(100),
    requested_amount    DECIMAL(12,2) NOT NULL,
    payment_due_date    DATE,
    note                TEXT,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_pr_memo (memo_id),
    CONSTRAINT fk_pr_memo     FOREIGN KEY (memo_id)       REFERENCES memos(id) ON DELETE CASCADE,
    CONSTRAINT fk_pr_user     FOREIGN KEY (payee_user_id) REFERENCES users(id),
    CONSTRAINT fk_pr_supplier FOREIGN KEY (supplier_id)   REFERENCES suppliers(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 10. payments (ประวัติการจ่ายเงินจริง)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS payments;
CREATE TABLE payments (
    id                  BIGINT PRIMARY KEY AUTO_INCREMENT,
    memo_id             BIGINT NOT NULL,
    payment_no          VARCHAR(100) UNIQUE,
    payment_date        DATE NOT NULL,
    paid_amount         DECIMAL(12,2) NOT NULL,
    payment_method      ENUM('cash','bank_transfer','company_bank',
                             'credit_card','payroll','other') NOT NULL,
    paid_by             BIGINT,
    paid_to_name        VARCHAR(255),
    bank_name           VARCHAR(255),
    bank_account_name   VARCHAR(255),
    bank_account_number VARCHAR(100),
    slip_file_id        BIGINT,
    payment_note        TEXT,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_pay_memo (memo_id),
    KEY idx_pay_date (payment_date),
    CONSTRAINT fk_pay_memo FOREIGN KEY (memo_id) REFERENCES memos(id) ON DELETE CASCADE,
    CONSTRAINT fk_pay_user FOREIGN KEY (paid_by) REFERENCES users(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 11. attachments (ไฟล์แนบทั้งหมด)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS attachments;
CREATE TABLE attachments (
    id           BIGINT PRIMARY KEY AUTO_INCREMENT,
    related_type ENUM('memo','memo_item','payment') NOT NULL,
    related_id   BIGINT NOT NULL,
    file_type    ENUM('quotation','invoice','receipt','tax_invoice','payment_slip',
                      'booking_confirmation','photo','other') DEFAULT 'other',
    file_name    VARCHAR(255) NOT NULL,
    file_url     TEXT NOT NULL,
    mime_type    VARCHAR(100),
    file_size    BIGINT,
    uploaded_by  BIGINT,
    uploaded_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY idx_att_related (related_type, related_id),
    CONSTRAINT fk_att_user FOREIGN KEY (uploaded_by) REFERENCES users(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 12. memo_running_numbers (running number ต่อ Department/ปี/เดือน)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS memo_running_numbers;
CREATE TABLE memo_running_numbers (
    id              BIGINT PRIMARY KEY AUTO_INCREMENT,
    company_id      BIGINT NOT NULL,
    department_id   BIGINT NOT NULL,
    department_code VARCHAR(50) NOT NULL,
    year            INT NOT NULL,
    month           INT NOT NULL,
    current_number  INT DEFAULT 0,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_run (department_id, year, month),
    CONSTRAINT fk_run_company FOREIGN KEY (company_id)    REFERENCES companies(id),
    CONSTRAINT fk_run_dept    FOREIGN KEY (department_id) REFERENCES departments(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 13. approval_rules (กฎการอนุมัติ)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS approval_rules;
CREATE TABLE approval_rules (
    id               BIGINT PRIMARY KEY AUTO_INCREMENT,
    company_id       BIGINT,
    department_id    BIGINT,
    memo_type        VARCHAR(100),
    min_amount       DECIMAL(12,2) DEFAULT 0.00,
    max_amount       DECIMAL(12,2),
    approval_level   INT NOT NULL,
    approver_role    ENUM('manager','accounting','director','admin') NOT NULL,
    approver_user_id BIGINT,
    is_active        BOOLEAN DEFAULT TRUE,
    created_at       DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_rule_company FOREIGN KEY (company_id)       REFERENCES companies(id),
    CONSTRAINT fk_rule_dept    FOREIGN KEY (department_id)    REFERENCES departments(id),
    CONSTRAINT fk_rule_user    FOREIGN KEY (approver_user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 14. approval_logs (ประวัติการอนุมัติ)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS approval_logs;
CREATE TABLE approval_logs (
    id             BIGINT PRIMARY KEY AUTO_INCREMENT,
    memo_id        BIGINT NOT NULL,
    approval_level INT NOT NULL,
    approver_id    BIGINT NOT NULL,
    approver_role  VARCHAR(100),
    action         ENUM('submitted','approved','rejected','revision_required','cancelled') NOT NULL,
    comment        TEXT,
    action_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY idx_log_memo (memo_id),
    CONSTRAINT fk_log_memo FOREIGN KEY (memo_id)     REFERENCES memos(id) ON DELETE CASCADE,
    CONSTRAINT fk_log_user FOREIGN KEY (approver_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 15. memo_comments
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS memo_comments;
CREATE TABLE memo_comments (
    id         BIGINT PRIMARY KEY AUTO_INCREMENT,
    memo_id    BIGINT NOT NULL,
    user_id    BIGINT NOT NULL,
    comment    TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_cmt_memo (memo_id),
    CONSTRAINT fk_cmt_memo FOREIGN KEY (memo_id) REFERENCES memos(id) ON DELETE CASCADE,
    CONSTRAINT fk_cmt_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 16. audit_logs
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS audit_logs;
CREATE TABLE audit_logs (
    id          BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id     BIGINT,
    action      VARCHAR(100) NOT NULL,
    table_name  VARCHAR(100),
    record_id   BIGINT,
    old_value   JSON,
    new_value   JSON,
    ip_address  VARCHAR(100),
    user_agent  TEXT,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY idx_audit_user (user_id),
    KEY idx_audit_table (table_name, record_id),
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;
