# BCA Department Portal (PHP + MySQL + Bootstrap)

A simple college department portal where BCA students log in with their **enrollment number + password** to access **study materials** and **previous-year question papers**, and an **admin panel** to manage everything.

## Tech Stack
- HTML5, CSS3, JavaScript
- Bootstrap 5 (CDN)
- PHP 7.4+ / 8.x
- MySQL / MariaDB
- Runs on **XAMPP / WAMP / LAMP**

---

## How to Run (XAMPP)

1. **Install XAMPP** and start **Apache** + **MySQL**.
2. Copy the entire `bca_portal` folder into `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (macOS) or `/opt/lampp/htdocs/` (Linux).
3. Open phpMyAdmin: <http://localhost/phpmyadmin>
4. Create a database named **`bca_portal`** and import the file `database/bca_portal.sql`.
5. Open the app: <http://localhost/bca_portal/>

### Default Logins

**Admin**
- URL: <http://localhost/bca_portal/admin/login.php>
- Username: `admin`
- Password: `admin123`

**Sample Students**
| Enrollment No | Password   | Semester |
|---------------|-----------|----------|
| BCA2023001    | student123 | 1 |
| BCA2023002    | student123 | 3 |
| BCA2022015    | student123 | 5 |

> Change all default passwords after first login.

---

## Folder Structure
```
bca_portal/
├── index.php              # Landing page
├── login.php              # Student login
├── logout.php
├── dashboard.php          # Student dashboard
├── materials.php          # Browse study materials
├── papers.php             # Browse question papers
├── download.php           # Secure file download
├── config/
│   └── db.php             # DB connection
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── auth.php
├── admin/
│   ├── login.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── students.php       # CRUD students
│   ├── subjects.php       # CRUD subjects
│   ├── materials.php      # Upload study material
│   └── papers.php         # Upload question papers
├── assets/
│   ├── css/style.css
│   └── js/script.js
├── uploads/
│   ├── materials/
│   └── papers/
└── database/
    └── bca_portal.sql
```

---

## Features

### Student
- Login with enrollment number + password
- Dashboard with profile, semester info, quick links
- Filter study materials by semester & subject
- Download lecture notes / PPTs / PDFs
- Browse previous-year question papers by year & subject

### Admin
- Secure admin login
- Manage students (add, edit, delete, reset password)
- Manage subjects per semester
- Upload study materials (PDF/PPT/DOC)
- Upload question papers (PDF) with year & exam type
- Dashboard stats

### Security
- Passwords hashed with `password_hash()` (bcrypt)
- Prepared statements (PDO) — SQL injection safe
- Session-based auth with separate student/admin guards
- File uploads restricted by extension & size
- Direct file access blocked — downloads served via `download.php` (auth-checked)

---

## Notes
- Max upload size: 20 MB (adjust in `php.ini` if needed)
- Allowed file types: pdf, doc, docx, ppt, pptx
- All 6 semesters preloaded with sample BCA subjects.
