# 🚂 Deploy to Railway — Step-by-Step

คู่มือ deploy ระบบขึ้น **Railway** (https://railway.com) พร้อม MySQL plugin

---

## 🎯 Overview

```
GitHub Repo → Railway Service → Auto-build (Docker) → Live URL
                  ↓
            Railway MySQL plugin (auto-inject ENV)
```

**เวลา deploy ครั้งแรก: ~5 นาที**

---

## 📋 Prerequisites

- [ ] GitHub account
- [ ] Railway account (https://railway.com — free tier ใช้งานได้)
- [ ] Local machine มี `git` ติดตั้งแล้ว

---

## STEP 1 — Push to GitHub

### 1.1 สร้าง Repository บน GitHub

1. เปิด https://github.com/new
2. Repository name: `expense-memo-system`
3. **Public** (Railway deploy ได้ทั้ง public/private)
4. ❌ **อย่าเช็ค** "Add README", "Add .gitignore", "Add license" (เรามีแล้ว)
5. **Create repository**

### 1.2 Push code

```bash
cd expense-memo-system

# (ถ้ายังไม่มี git repo)
git init
git branch -M main
git add .
git commit -m "feat: initial commit — Expense Memo Management System"

# Push (แทนที่ YOUR_USERNAME)
git remote add origin https://github.com/YOUR_USERNAME/expense-memo-system.git
git push -u origin main
```

> ✅ ตรวจสอบบน GitHub ว่าไฟล์อัพขึ้นครบ — ดูว่า `Dockerfile`, `railway.json`, `app/`, `database/` ถูก push แล้ว

---

## STEP 2 — Create Railway Project

1. เปิด https://railway.com → **Login with GitHub**
2. **New Project** → **Deploy from GitHub repo**
3. ครั้งแรกอาจต้อง **Configure GitHub App** → เลือก repo ที่ต้องการให้ Railway เข้าถึง
4. เลือก `expense-memo-system`
5. Railway จะเริ่ม build ทันที (รออย่าเพิ่งกังวลเรื่อง DB)

---

## STEP 3 — Add MySQL Plugin

1. ในหน้าโปรเจค Railway → คลิก **+ New** (ขวาบน)
2. เลือก **Database** → **Add MySQL**
3. รอ ~30 วินาที จน MySQL provisioning เสร็จ
4. คลิกที่ MySQL service → **Variables tab** → จะเห็น:
   - `MYSQLHOST`
   - `MYSQLPORT`
   - `MYSQLUSER`
   - `MYSQLPASSWORD`
   - `MYSQLDATABASE`

---

## STEP 4 — Connect MySQL to App Service

1. กลับมาที่ App service (ไม่ใช่ MySQL)
2. **Variables tab** → คลิก **Add Reference**
3. เลือก MySQL service → ติ๊ก variables ทั้งหมด:
   - `MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`
4. **Add Reference** — Railway จะ inject เป็น ENV vars ของ App ให้อัตโนมัติ

   > 💡 หรือใช้ private network: **MYSQL_URL** (full DSN) ก็ได้ — ระบบรองรับทั้ง 2 แบบ

---

## STEP 5 — Set Application Variables

ยังอยู่ที่ **App service → Variables** → **Add Variable**:

| Variable | Value | คำอธิบาย |
|----------|-------|---------|
| `APP_ENV` | `production` | Production mode |
| `APP_DEBUG` | `false` | ปิด debug |
| `AUTO_INSTALL` | `true` | สร้าง schema + seed อัตโนมัติครั้งแรก |
| `ADMIN_EMAIL` | `admin@yourdomain.com` | Email ของ admin คนแรก |
| `ADMIN_PASSWORD` | `ChangeThis_Strong!` | Password admin (อย่างน้อย 6 ตัว) |
| `ADMIN_NAME` | `System Administrator` | ชื่อ admin |
| `APP_TIMEZONE` | `Asia/Bangkok` | Timezone (default ไว้แล้ว) |

> ⚠️ **อย่าลืมเปลี่ยน ADMIN_PASSWORD** เป็น password ที่แข็งแรง — ถ้าไม่ตั้ง ระบบจะ generate random + log ออก

หลังเพิ่ม variable แล้ว Railway จะ **redeploy อัตโนมัติ**

---

## STEP 6 — Generate Public Domain

1. App service → **Settings tab** → **Networking** → **Generate Domain**
2. Railway สร้าง URL ให้ เช่น `expense-memo-production.up.railway.app`
3. เปิด URL — ควรเห็นหน้า Login

   > หากเห็น error: รอ 1-2 นาที auto-installer กำลัง run schema

---

## STEP 7 — First Login

1. เปิด `https://your-app.up.railway.app/login`
2. Login ด้วย:
   - Email: ค่าจาก `ADMIN_EMAIL`
   - Password: ค่าจาก `ADMIN_PASSWORD`
3. ไปที่ **Master Data → Users** → ลบหรือ disable demo users (`mkt.staff@...`, ฯลฯ)

---

## STEP 8 — Custom Domain (Optional)

หากมี domain ของตัวเอง (เช่น `memo.loveandaman.com`):

1. App → **Settings → Networking → Custom Domain**
2. ใส่ domain → Railway แสดง CNAME record
3. ไปที่ DNS provider (Cloudflare/GoDaddy):
   - Type: `CNAME`
   - Name: `memo`
   - Value: `your-app.up.railway.app`
4. รอ 5-10 นาที → ใช้งาน HTTPS ได้ทันที (Let's Encrypt auto)

---

## 🔄 Continuous Deployment

ตั้งแต่นี้ทุก `git push` ไปยัง `main` branch:

```bash
git add .
git commit -m "feat: add new feature"
git push
```

Railway จะ build + deploy ใหม่อัตโนมัติ (~2 นาที)

---

## 📊 Monitoring

| Tab | ใช้ทำอะไร |
|-----|-----------|
| **Deployments** | ดู build/deploy history, rollback ได้ |
| **Logs** | Real-time application logs |
| **Metrics** | CPU, RAM, Network usage |
| **Variables** | Environment variables |
| **Settings** | Domain, restart policy, replicas |

---

## 💰 Costs

Railway free tier:
- $5 free credit/month
- เหมาะสำหรับ MVP / dev
- App + MySQL ใช้รวมประมาณ $4-7/month

Production พิจารณา:
- Hobby plan: $5/month (single dev)
- Pro plan: $20/month (team)

---

## 🐛 Troubleshooting

### Build Failed: "PHP not found"
→ Railway ใช้ Dockerfile (เห็น `Dockerfile` ที่ root) — ตรวจไฟล์อัพขึ้น GitHub ครบ

### "Database connection failed" ใน logs
→ ตรวจ Variables tab ของ App service ว่ามี `MYSQLHOST`, `MYSQLPASSWORD` ครบหรือยัง  
→ บางครั้งต้อง click **Redeploy** หลังเพิ่ม reference

### "Can't write to storage/uploads"
→ ใช้ Railway **Volumes**:
1. App → **Settings → Volumes → New Volume**
2. Mount path: `/var/www/html/storage/uploads`
3. Size: 1 GB เริ่มต้น
4. Redeploy

### หน้าเว็บ 502 Bad Gateway
→ Apache ยังไม่ start เสร็จ — รอ 30 วินาที
→ ดู Logs ว่า auto-installer มี error ไหม

### ไม่อยากให้ AUTO_INSTALL ทำงานทุกครั้ง
→ หลัง deploy ครั้งแรก ตั้ง `AUTO_INSTALL=false` (script เป็น idempotent อยู่แล้ว แต่จะข้ามได้เร็วกว่า)

---

## 🛡 Security Checklist

- [ ] เปลี่ยน `ADMIN_PASSWORD` แล้ว (ไม่ใช้ default)
- [ ] ลบหรือ disable demo users (`mkt.staff@loveandaman.com`, ฯลฯ)
- [ ] ตั้ง `APP_DEBUG=false`
- [ ] HTTPS enabled (Railway default ON)
- [ ] Volume mount สำหรับ uploads
- [ ] Backup MySQL → Railway → MySQL service → **Backups tab**
- [ ] เก็บ ADMIN_PASSWORD ใน password manager (อย่า commit ลง git)

---

## 📦 Volume Setup (สำคัญสำหรับ uploads)

Railway containers ใช้ **ephemeral filesystem** — ไฟล์ใน `storage/uploads/` จะหายเมื่อ redeploy หากไม่ใช้ volume

1. App service → **Settings → Volumes**
2. **+ New Volume**
3. Mount Path: `/var/www/html/storage/uploads`
4. Size: 1 GB (ขยายได้ภายหลัง)
5. **Redeploy**

ตั้งแต่นี้ uploads จะถาวร แม้ container restart

---

## 🎯 Quick Commands

```bash
# Railway CLI (optional)
npm i -g @railway/cli
railway login
railway link        # link to existing project
railway logs        # tail logs
railway run php deploy/db-migrate.php --check   # test DB connection
railway shell       # SSH-like shell into container
```

---

## 📞 Help

- Railway Discord: https://discord.gg/railway
- Railway Docs: https://docs.railway.com
- GitHub Issues: https://github.com/YOUR_USERNAME/expense-memo-system/issues

---

Built with ♥ for LOVE ISLAND & ANDAMAN SUNDAY  
Deployed on 🚂 Railway
