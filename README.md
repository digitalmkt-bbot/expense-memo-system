# Expense Memo Management System

[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?logo=php&logoColor=white)](https://www.php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![Deploy on Railway](https://railway.com/button.svg)](https://railway.com/new/template)

ระบบจัดการ Memo ค่าใช้จ่ายสำหรับองค์กร — สร้าง Memo ขออนุมัติ, แนบเอกสาร, Approval Workflow, จ่ายเงิน Supplier, ออกรายงาน

**สำหรับบริษัท**:
- 🏝️ **LO** — LOVE ISLAND CO., LTD.
- 🌅 **AS** — ANDAMAN SUNDAY CO., LTD.

---

## ✨ Features

- 🔐 Role-based Login (Requester / Manager / Accounting / Director / Admin)
- 📝 Multi-item Expense Memo + Attachment upload
- 🔢 Auto-generate Memo No. — `[DEPT]-[YYYY]-[MM]-[RUN3]` (เช่น `LO-MKT-2026-05-001`)
- ✅ 3-tier Approval: Manager → Accounting → Director (ตาม amount tier)
- 💵 Payment Recording, Slip Upload, Partial Payment, Auto-close
- 📊 Reports: by Company / Department / Category / Monthly / Pending
- 🖨 Export PDF (browser print-ready)
- ⚙️ Full Master Data Management
- 🛡 CSRF Protection, Audit Log, Foreign Keys, Production-hardened

---

## 🚀 Quick Deploy

### 🚂 Deploy to Railway (1-click)

1. Push repo ขึ้น GitHub (public/private OK)
2. Railway → **New Project → Deploy from GitHub**
3. เพิ่ม **MySQL Plugin** → ตั้ง `ADMIN_EMAIL`, `ADMIN_PASSWORD` ใน Variables
4. Done! → ดู [RAILWAY.md](RAILWAY.md) คู่มือเต็ม

### 🐘 Deploy to cPanel / DirectAdmin

ดู [DEPLOY.md](DEPLOY.md) — Step-by-step + Web Installer

### 🐳 Run with Docker

```bash
docker build -t expense-memo .
docker run -p 8080:8080 \
  -e MYSQLHOST=mysql_host \
  -e MYSQLUSER=user \
  -e MYSQLPASSWORD=pass \
  -e MYSQLDATABASE=expense_memo \
  -e ADMIN_EMAIL=admin@example.com \
  -e ADMIN_PASSWORD=ChangeMe123 \
  expense-memo
```

### 💻 Run Locally

```bash
git clone https://github.com/YOUR_USERNAME/expense-memo-system.git
cd expense-memo-system

# Setup database
mysql -u root -p < database/01_schema.sql
mysql -u root -p < database/02_seed.sql

# Configure
cp .env.example .env
# แก้ .env ตาม local DB

# Start
cd public && php -S localhost:8080
# เปิด http://localhost:8080
```

Demo password = `password123` (ทุก demo user)

---

## 🏗 Architecture

```
expense-memo-system/
├── app/
│   ├── controllers/     ← MVC Controllers (Auth, Memo, Approval, Payment, Report, MasterData)
│   ├── core/            ← Framework: Database, Router, Auth, Model, helpers
│   ├── models/          ← Domain Models
│   └── views/           ← PHP templates (Bootstrap 5)
├── config/              ← Environment-aware config
├── database/            ← SQL schema + seed data
├── deploy/              ← Dockerfile, scripts, htaccess templates
├── public/              ← Web entry point
├── storage/uploads/     ← User uploads (use Volume on Railway)
├── Dockerfile           ← PHP 8.2 + Apache (Railway-ready)
├── railway.json         ← Railway build config
├── nixpacks.toml        ← Nixpacks fallback
└── setup.php            ← Web installer (delete after first run)
```

---

## 📋 Database Tables (16)

`companies` · `departments` · `users` · `projects` · `expense_categories` · `suppliers` · `memos` · `memo_items` · `memo_payment_requests` · `payments` · `attachments` · `memo_running_numbers` · `approval_rules` · `approval_logs` · `memo_comments` · `audit_logs`

---

## 🔄 Workflow

```
[Requester]                  [Manager]           [Accounting]         [Director]            [Accounting]
    │                            │                    │                    │                     │
Create Memo → Add Items     Review + Approve    Verify VAT/WHT       (if amount > 10K)     Record Payment
    │  status: draft               ↓                  ↓                Approve              Upload Slip
    ▼                                                                                            │
Submit ──► Generate Memo No.                                                                     ▼
    │   (LO-MKT-2026-05-001)                                                                Paid → Closed
    ▼
[End → Export PDF]
```

### Approval Tiers
| Amount | Required Approvers |
|--------|-------------------|
| 0 – 10,000 | Manager + Accounting |
| 10,001 – 50,000 | Manager + Accounting + Director |
| 50,001+ | Manager + Accounting + Director |

---

## 🌐 Routes

ดู [public/index.php](public/index.php) สำหรับ route ทั้ง 45 routes — รวมถึง:

- `GET /login`, `POST /login`, `GET /logout`
- `GET /memos`, `POST /memos`, `GET /memos/{id}`, `POST /memos/{id}/submit`, `GET /memos/{id}/pdf`
- `GET /approvals`, `POST /memos/{id}/approve|reject|revision`
- `GET /payments`, `POST /memos/{id}/payment`, `POST /memos/{id}/close`
- `GET /reports/*` (6 reports)
- `GET /master/*` (admin master data)

---

## ⚙️ Environment Variables

ดู [.env.example](.env.example) สำหรับ list เต็ม

| Var | Default | คำอธิบาย |
|-----|---------|---------|
| `APP_ENV` | `production` | `local` หรือ `production` |
| `APP_DEBUG` | `false` | เปิด error display |
| `APP_BASE_URL` | `''` | Subfolder prefix |
| `MYSQLHOST` | `127.0.0.1` | DB host (Railway auto-inject) |
| `MYSQLPORT` | `3306` | |
| `MYSQLUSER` | `root` | |
| `MYSQLPASSWORD` | `''` | |
| `MYSQLDATABASE` | `expense_memo` | |
| `AUTO_INSTALL` | `true` | Run schema+seed on first boot |
| `ADMIN_EMAIL` | — | First admin email |
| `ADMIN_PASSWORD` | — | First admin password |
| `ADMIN_NAME` | `System Administrator` | First admin name |

---

## 🔒 Security

- ✅ PDO Prepared Statements (no SQL injection)
- ✅ CSRF tokens on all POST forms
- ✅ Bcrypt password hashing (cost 10)
- ✅ Session: HttpOnly, Secure (HTTPS), SameSite=Strict
- ✅ Role-based authorization
- ✅ File upload validation (extension + size)
- ✅ Security headers (X-Frame-Options, CSP-friendly)
- ✅ Source folders denied via .htaccess (`/app`, `/config`, `/database`)

---

## 🧪 Testing the Workflow

1. Login as `mkt.staff@loveandaman.com` (Requester) — password `password123`
2. **Create Memo** → Subject: "ค่าโฆษณา Facebook Q2"
3. **Add Item**: Marketing category, qty=1, unit_price=5000, VAT exclude 7%
4. **Upload** ใบเสนอราคา
5. **Submit** → ระบบ generate `LO-MKT-2026-05-001`
6. Login `mkt.manager@loveandaman.com` → `/approvals` → Approve
7. Login `acc.lo@loveandaman.com` → `/approvals` → Approve
8. (ถ้า > 10,000) Login `director.lo@loveandaman.com` → Approve
9. Login `acc.lo@loveandaman.com` → `/payments` → Record Payment + Slip
10. Memo → `paid` → Click Close → `closed`
11. Export PDF — `/memos/{id}/pdf`

---

## 🤝 Contributing

1. Fork the repo
2. Create branch: `git checkout -b feature/amazing`
3. Commit: `git commit -am 'feat: add amazing'`
4. Push: `git push origin feature/amazing`
5. Open Pull Request

---

## 📄 License

[MIT License](LICENSE) © 2026 LOVE ISLAND CO., LTD. / ANDAMAN SUNDAY CO., LTD.

---

## 📚 Documentation

- 📘 [DEPLOY.md](DEPLOY.md) — cPanel / Shared Hosting deployment
- 🚂 [RAILWAY.md](RAILWAY.md) — Railway deployment with MySQL plugin
- 📐 [database/01_schema.sql](database/01_schema.sql) — Database schema
- 🌱 [database/02_seed.sql](database/02_seed.sql) — Seed data

---

Built with ♥ for **LOVE ISLAND** & **ANDAMAN SUNDAY**
