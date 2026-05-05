# 🚀 Deploy Guide — Shared Hosting (cPanel / DirectAdmin)

คู่มือ deploy ระบบ **Expense Memo Management System** ขึ้น Shared Hosting แบบ Step-by-Step

---

## ✅ สิ่งที่ต้องเตรียม

ก่อนเริ่ม ให้แน่ใจว่ามี:

- [ ] Hosting account (cPanel / DirectAdmin / Plesk) ที่รองรับ **PHP 8.0+**
- [ ] MySQL/MariaDB database
- [ ] Domain หรือ Subdomain (เช่น `memo.loveandaman.com`)
- [ ] FTP/SFTP credentials หรือเข้า cPanel File Manager ได้
- [ ] ZIP package: `expense-memo-system.zip`

---

## 📋 ขั้นตอนการ Deploy

### 🔵 STEP 1 — สร้าง Database ใน cPanel

1. Login เข้า **cPanel**
2. คลิก **MySQL® Databases** (อยู่ใต้ Databases)
3. **Create New Database**:
   - Name: `expense_memo` → cPanel จะเติม prefix อัตโนมัติเป็น `cpaneluser_expense_memo`
   - คลิก **Create Database**
4. **MySQL Users** → สร้าง user ใหม่:
   - Username: `emm_app`
   - Password: ใช้ **Password Generator** (ตั้งให้แข็งแรง 16+ ตัวอักษร)
   - **บันทึกรหัสผ่านไว้** (ต้องใช้ใน Step 5)
5. **Add User to Database**:
   - User: `cpaneluser_emm_app`
   - Database: `cpaneluser_expense_memo`
   - Privileges: เลือก **ALL PRIVILEGES** → Make Changes

---

### 🔵 STEP 2 — Upload Files ขึ้น Server

#### วิธี A: ผ่าน cPanel File Manager (แนะนำ)

1. cPanel → **File Manager**
2. ไปที่ `public_html/`
   - ถ้าใช้ subdomain: ไปที่ `public_html/memo/` (ตามที่สร้าง subdomain ไว้)
3. คลิก **Upload** → เลือกไฟล์ `expense-memo-system.zip`
4. รอจนอัพโหลดเสร็จ → กลับมาที่ File Manager
5. คลิกขวาที่ `.zip` → **Extract**
6. หากการแตกไฟล์สร้าง folder `expense-memo-system/` ให้ย้ายไฟล์ทั้งหมดข้างในออกมาที่ root โดย:
   - เข้า folder `expense-memo-system/`
   - Select All → Cut → ออกมา root → Paste

#### วิธี B: ผ่าน FTP (FileZilla / WinSCP)

```
host: ftp.yourdomain.com
user: cpaneluser
pass: ********
port: 21 (หรือ 22 สำหรับ SFTP)
```

Upload ทั้ง folder `expense-memo-system/*` ขึ้นไปที่ `public_html/` (หรือ subfolder)

---

### 🔵 STEP 3 — เลือก Layout (สำคัญ — เลือกอันใดอันหนึ่ง)

#### 🅰️ Layout A: ตั้ง Document Root ที่ `public/` folder (แนะนำ — ปลอดภัยสุด)

ถ้า hosting ของคุณรองรับการตั้ง document root (เช่น addon domain หรือ subdomain):

1. cPanel → **Subdomains** → สร้าง `memo`
2. **Document Root**: ใส่ `/public_html/expense-memo-system/public`
3. **เสร็จ!** ไม่ต้องทำอะไรเพิ่ม

> ✅ ข้อดี: source code อยู่ใน `expense-memo-system/` ซึ่งอยู่นอก document root → ไม่มีทาง access โดยตรงผ่าน URL

#### 🅱️ Layout B: Flat Layout (ทุกไฟล์ใน public_html — สำหรับ shared host ที่ไม่ให้เปลี่ยน document root)

1. ใน File Manager → เข้า `expense-memo-system/`
2. **Cut** ทุกไฟล์ออกมา **Paste** ที่ `public_html/`
3. ลบ `expense-memo-system/` (folder ว่าง)
4. ลบ `public_html/public/index.php` (ตัวเก่า)
5. **คัดลอก** `public_html/deploy/index.flat.php` → **เปลี่ยนชื่อเป็น** `index.php` ที่ root ของ public_html
6. **คัดลอก** `public_html/deploy/htaccess.root` → **เปลี่ยนชื่อเป็น** `.htaccess` ที่ root
7. **วาง deny .htaccess** ใน 4 folder ที่ห้าม web เข้า:
   - `public_html/app/.htaccess`           = `Require all denied`
   - `public_html/config/.htaccess`        = `Require all denied`
   - `public_html/database/.htaccess`      = `Require all denied`
   - `public_html/storage/.htaccess`       = `Require all denied`
   - `public_html/deploy/.htaccess`        = `Require all denied`

   (ใช้ไฟล์ `deploy/htaccess.deny-all` ก็ได้ — copy ไปวางและเปลี่ยนชื่อเป็น `.htaccess`)

โครงสร้างสุดท้ายของ Layout B:

```
public_html/
├── index.php              ← จาก deploy/index.flat.php
├── .htaccess              ← จาก deploy/htaccess.root
├── setup.php              (ลบหลังติดตั้ง!)
├── app/         + .htaccess (deny)
├── config/      + .htaccess (deny)
├── database/    + .htaccess (deny)
├── storage/     + .htaccess (deny)
│   └── uploads/  ← chmod 775
├── deploy/      + .htaccess (deny)
└── public/      ← (เลือกใช้ Layout A หรือ Layout B อันใดอันหนึ่ง — ไม่ใช้พร้อมกัน)
```

---

### 🔵 STEP 4 — Set File Permission

ใน cPanel File Manager (หรือ FTP):
- คลิกขวาที่ `storage/uploads/` → **Change Permissions** → ใส่ `775`
- คลิกขวาที่ `config/` → **Change Permissions** → ใส่ `755` (ต้อง writable เพื่อ setup.php เขียน config ได้)
- ไฟล์ทั่วไป: `644`
- ไฟล์ที่ execute ได้ (.sh): `755`

หรือผ่าน SSH:
```bash
chmod -R 755 .
chmod -R 775 storage/uploads
chmod 755 config
find . -type f -name "*.php" -exec chmod 644 {} \;
chmod 644 .htaccess
```

---

### 🔵 STEP 5 — รัน Web Installer

1. เปิด browser ไปที่:
   ```
   https://yourdomain.com/setup.php
   ```
   (หรือ `https://yourdomain.com/memo/setup.php` ถ้าอยู่ใน subfolder)

2. **Step 1 — Pre-flight Check**: ตรวจ PHP version, extensions, file permissions
   - ถ้ามี ❌ ให้กลับไปแก้ไขก่อน → กด **Re-check**

3. **Step 2 — Database Connection**: กรอก DB ที่สร้างใน Step 1
   - Host: `localhost`
   - Database: `cpaneluser_expense_memo`
   - User: `cpaneluser_emm_app`
   - Password: ที่ตั้งไว้

4. **Step 3 — Run Migration**: กด **Run Migration** → ระบบจะ import schema + seed อัตโนมัติ

5. **Step 4 — Create Admin**:
   - Base URL:
     - ถ้า domain ชี้ตรงไปที่ระบบ → เว้นว่าง
     - ถ้าอยู่ใน subfolder เช่น `/memo` → ใส่ `/memo`
   - Admin Name / Email / Password (อย่างน้อย 6 ตัว)

6. **Step 5 — สำเร็จ!** ระบบจะแจ้งให้ลบไฟล์ที่ไม่จำเป็น

---

### 🔵 STEP 6 — ลบไฟล์ที่ไม่จำเป็น (สำคัญต่อความปลอดภัย!)

**ลบทันทีหลังติดตั้ง**:
- ❌ `setup.php`
- ❌ `database/01_schema.sql`
- ❌ `database/02_seed.sql`
- ❌ `deploy/` (ทั้ง folder — เก็บแค่ตอน deploy)
- ❌ `.installed` (ลบหรือคงไว้ก็ได้ แต่ห้าม access ผ่าน web)
- ❌ `DEPLOY.md`, `README.md` (optional — ไม่จำเป็นบน production)

**Tip**: เก็บสำเนา project ไว้ที่ local เผื่อ deploy รอบถัดไป

---

### 🔵 STEP 7 — เปิดใช้งาน HTTPS (SSL)

1. cPanel → **SSL/TLS Status** → เปิด **AutoSSL**
2. รอ 5-10 นาที จนได้ certificate
3. เปิด `.htaccess` แก้ uncomment 3 บรรทัดบนสุด:
   ```apache
   RewriteEngine On
   RewriteCond %{HTTPS} !=on
   RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```
4. ทุก request จะถูก redirect ไป `https://` อัตโนมัติ

---

### 🔵 STEP 8 — Test ระบบ

1. เข้า `https://yourdomain.com/login`
2. Login ด้วย admin ที่สร้างไว้ใน Step 5
3. Test workflow ครบ:
   - [ ] สร้าง Memo → Submit → ตรวจว่า memo_no generate ถูก format
   - [ ] Login ด้วย role อื่น → Approve
   - [ ] Record Payment + Upload Slip
   - [ ] Export PDF
   - [ ] Reports ทำงานถูก

---

## 🔧 Configuration ที่ควรปรับหลัง Deploy

### `config/config.php`

```php
'app' => [
    'debug'    => false,                // ✅ Production
    'base_url' => '',                   // ✅ ให้ตรงกับ domain
    'timezone' => 'Asia/Bangkok',
],
'session' => [
    'lifetime' => 60 * 60 * 8,
    'secure'   => true,                  // ✅ HTTPS only
    'samesite' => 'Strict',
],
```

### `config/database.php`
- DB credentials จะถูก setup.php เขียนให้อัตโนมัติแล้ว
- ตรวจดูอีกครั้งว่าไม่ใช่ default password

---

## 🛡 Security Checklist หลัง Deploy

- [ ] ลบ `setup.php` แล้ว
- [ ] ลบ `database/*.sql` แล้ว
- [ ] ลบ `deploy/` folder แล้ว
- [ ] ตั้ง `'debug' => false`
- [ ] เปิด HTTPS + AutoSSL
- [ ] เปิด force HTTPS ใน `.htaccess`
- [ ] เปลี่ยน password admin จาก default
- [ ] ลบ demo user ที่ไม่ใช้ (`mkt.staff@...`, ฯลฯ) หรือ disable
- [ ] Backup database ตั้ง cron วันละครั้ง (ดู `deploy/backup.sh`)
- [ ] ตรวจ `chmod 644` ของไฟล์ `.htaccess` ทั้งหมด
- [ ] ตรวจว่า `https://yourdomain.com/app/`, `/config/`, `/database/` คืน **403 Forbidden**

---

## 🐛 Troubleshooting

### 500 Internal Server Error
- เช็ค error log: cPanel → **Error Pages** → **Error Log**
- บ่อย: `mod_rewrite` ไม่ enabled → ติดต่อ host
- เช็ค PHP version ใน cPanel → **MultiPHP Manager** → ตั้งเป็น 8.0+

### 404 ทุกหน้า ยกเว้น `/`
- `.htaccess` ไม่ทำงาน → check `AllowOverride All` (ติดต่อ host)
- หรือ `mod_rewrite` ไม่ enabled

### Login ผ่านแต่ refresh แล้วหลุด
- Session save path ไม่ writable → เช็ค `php.ini` → `session.save_path`
- หรือ `'secure' => true` แต่ยังไม่ได้ตั้ง SSL → ปิดชั่วคราว

### Upload ไฟล์ไม่ได้
- เช็ค `storage/uploads/` permission = 775
- เช็ค `php.ini` → `upload_max_filesize` ≥ 10M, `post_max_size` ≥ 12M
- เช็คว่า `deploy/.htaccess` (deny) ไม่ได้บัง storage

### "Class not found" หลัง upload
- ส่วนใหญ่เป็น file casing — Linux is case-sensitive!
- ตรวจชื่อไฟล์ใน `app/models/User.php` ตรงกับ `class User`

### Database connection failed
- เช็ค DB user ผูกกับ DB หรือยัง (Step 1.5)
- บางโฮสต์ใช้ host = `127.0.0.1` แทน `localhost`

---

## 📅 Maintenance ประจำ

### Backup Database (cron)

cPanel → **Cron Jobs** → Add new:
```bash
# ทุกคืน 02:00 — backup DB
0 2 * * * /home/cpaneluser/expense-memo-system/deploy/backup.sh
```

### Backup Files (manually)
- cPanel → **Backup** → Download backup
- หรือ FTP download ทั้ง folder ทุกเดือน

### Update Code
ใช้ `deploy/deploy.sh` (ดูในไฟล์)

---

## 🎯 Tips

- **เร็ว/เสถียรกว่า**: ใช้ subdomain แยก (`memo.yourdomain.com`) แทน subfolder
- **Caching**: เปิด **OPcache** ใน cPanel → MultiPHP INI Editor
- **PHP version**: 8.1 หรือ 8.2 จะเร็วกว่า 8.0 ประมาณ 10-15%
- **CDN**: หากใช้ Cloudflare ให้ปิด "Always Online" สำหรับหน้า dynamic

---

## 📞 Need Help?

- Email: digital.mkt@loveandaman.com
- หากติด step ไหน ส่ง screenshot ของ error message มาได้

Built with ♥ for LOVE ISLAND & ANDAMAN SUNDAY
