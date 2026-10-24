# 🍃 APU-EcoSpot — Campus Sustainability & Waste Reporting Platform

> **APU Sustainability Initiative | Real World Web Development (RWDD)**  
> *Spot the waste. Fix it in seconds. Save kWh and earn impact points.*

---

## 📌 Table of Contents
- [About The Project](#-about-the-project)
- [Key Features](#-key-features)
- [User Roles & Permissions](#-user-roles--permissions)
- [Ticket Lifecycle Workflow](#-ticket-lifecycle-workflow)
- [System Architecture & Tech Stack](#-system-architecture--tech-stack)
- [Project Directory Structure](#-project-directory-structure)
- [Database Setup & Schema](#-database-setup--schema)
- [Installation & Quick Start](#-installation--quick-start)
- [Test Accounts & Credentials](#-test-accounts--credentials)
- [Security & Implementation Highlights](#-security--implementation-highlights)

---

## 📖 About The Project

**APU-EcoSpot** is a web-based sustainability management system developed for **Asia Pacific University of Technology & Innovation (APU)**. It empowers students, faculty members, and eco-ambassadors to actively identify, report, and monitor campus energy waste in real time (e.g., lighting left on during broad daylight, empty air-conditioned rooms, or abandoned projector equipment).

By combining community reporting with an impact-point gamification model and direct integration with Campus Facilities Management, APU-EcoSpot closes the feedback loop between waste discovery and physical campus maintenance.

---

## ✨ Key Features

- **⚡ Fast Incident Reporting:** Submit incident tickets in under a minute with details such as building/block, floor, room number, waste category, and notes.
- **🏆 Gamification & Impact Points:**
  - Reporters earn **+75 impact points** per submitted report.
  - Points are dynamically factored into the real-time leaderboard.
  - Penalties/deductions apply if a report is marked as dismissed/invalid.
- **🏅 Individual & School Leaderboards:** Live rankings tracking both individual top contributors and school/department standings (Computing, Engineering, Business, Media, etc.).
- **📊 Real-Time Energy Analytics:** Dashboard tracking estimated kWh saved, campus ranks, average resolution time, and interactive energy waste charts.
- **📱 Responsive Component-Based UI:** Built using a modular component architecture supporting both desktop and mobile viewports with sticky navigation and role-aware links.
- **🔒 Role-Based Access Control (RBAC):** Distinct dashboards, navigation items, and action capabilities for Students, Eco-Ambassadors, and Facilities Managers.

---

## 👥 User Roles & Permissions

| Role | Access Level | Primary Responsibilities |
| :--- | :--- | :--- |
| **Student** | Reporter | Submit energy waste tickets, monitor own report history, check ranking on the campus leaderboard, view energy savings. |
| **Eco-Ambassador** | Validator / Auditor | Verify on-site reports, approve valid submissions (changing status to `Verified`), reject invalid/duplicate reports (`Dismissed`). |
| **Facilities Management** | Maintainer / Admin | Receive verified tickets, assign technicians to tasks (`In Progress`), resolve physical waste sources, and mark tickets as `Resolved`. |

---

## 🔄 Ticket Lifecycle Workflow

```
       [ Student ]
     Submits Report
   (+75 Impact Points)
           │
           ▼
     ┌───────────┐
     │  Pending  │
     └─────┬─────┘
           │
     [ Eco-Ambassador ]
      Audit / Validate
           │
     ┌─────┴───────────────┐
     ▼                     ▼
┌───────────┐        ┌───────────┐
│ Verified  │        │ Dismissed │ (Points Deducted)
└─────┬─────┘        └───────────┘
      │
[ Facilities Management ]
      │
      ▼
┌───────────────┐
│  In Progress  │  (Technician Dispatched)
└───────┬───────┘
        │
        ▼
┌───────────────┐
│   Resolved    │  (Energy Saved Logged)
└───────────────┘
```

---

## 🛠 System Architecture & Tech Stack

- **Backend:** PHP 8.x (Native / Procedural + Modular functions)
- **Database:** MySQL / MariaDB (Relational schema with foreign keys and transactions)
- **Frontend:** HTML5, CSS3 (Modern Flexbox, CSS Grid, Custom Variables), Vanilla JavaScript
- **Server Environment:** Apache HTTP Server via XAMPP / WampServer / LAMP stack
- **Authentication:** PHP Session management with `password_hash()` (bcrypt) / `password_verify()`

---

## 📂 Project Directory Structure

```text
APU-EcoSpot/
├── assets/                       # Static media, icons, and campus graphics
│   ├── apu.png                   # Campus banner image
│   ├── leaf.svg                  # Brand icon
│   └── *.png                     # Metric icons (clock, users, check, podium)
│
├── components/                   # Reusable UI component library
│   ├── layouts/
│   │   ├── layout.php            # Master layout wrapper and session loader
│   │   ├── desktop_layout.php    # Desktop container & layout framing
│   │   └── mobile_layout.php     # Mobile-optimized viewport layout
│   ├── navbar/
│   │   ├── desktop_navbar.php    # Role-adaptive desktop navigation bar
│   │   └── mobile_navbar.php     # Mobile-responsive slide-out navigation
│   ├── bar_chart.php             # Energy consumption/savings bar chart widget
│   ├── button.php                # Configurable themed button component
│   ├── eco_card.php              # Incident ticket display card
│   ├── eco_card_container.php    # Ticket container (grid / list modes)
│   ├── metric_card.php           # Metric counter & milestone cards
│   ├── logo.php                  # Brand logo component
│   ├── footer.php                # Site footer component
│   └── components.php            # Central component loader
│
├── data/                         # Database dumps and seed fixtures
│   ├── ecospot.sql               # Database schema definition
│   └── seed_test_data.sql        # Demo users, roles, and sample tickets
│
├── pages/                        # Page controllers and role views
│   ├── index.php                 # Public landing / home page
│   ├── ambassador/               # Eco-Ambassador validation dashboard
│   ├── dashboard/                # Main campus overview dashboard
│   ├── facilities/               # Facilities ticket dispatch & resolution portal
│   ├── leaderboard/              # Campus student & faculty leaderboard
│   ├── login/                    # Login and Registration portal
│   ├── profile/                  # User profile and account summary
│   ├── report/                   # Waste reporting form
│   └── student/                  # Student personal dashboard
│
├── util/                         # Backend utilities & helpers
│   ├── dbcon.php                 # MySQLi database connection handler
│   ├── dashboard_fetch.php       # Real-time analytics aggregation queries
│   └── logout.php                # Session destruction and sign-out redirect
│
└── README.md                     # Project documentation
```

---

## 🗄 Database Setup & Schema

The database consists of relational tables managing users, roles, permissions, and report tickets:

1. **`user`**: Stores user profiles, hashed passwords, APU Student IDs, departments, and accumulated `impact_pts`.
2. **`role`**: Stores the 3 operational system roles (`Student`, `Eco-Ambassador`, `Facilities`).
3. **`user_role`**: Many-to-many relationship mapping users to designated roles.
4. **`permission` & `role_permission`**: Granular capability keys mapped to roles.
5. **`ticket`**: Central repository for all reports:
   - Location: `location_type`, `block_name`, `floor_number`, `room_number`
   - Issue: `issue_type` (Lighting, Aircon, Projector, Equipment, etc.), `description`, `image_path`
   - Tracking: `status` (`Pending`, `Verified`, `In Progress`, `Resolved`, `Dismissed`), `created_at`

---

## 🚀 Installation & Quick Start

### 1. Prerequisites
- [XAMPP](https://www.apachefriends.org/) (recommended with PHP 8.0+ and MySQL 5.7+ / MariaDB 10.4+)
- Web Browser (Chrome, Firefox, Edge, Safari)

### 2. Clone / Copy Repository
Place the project inside your Apache web directory (e.g., `htdocs`):
```bash
# Path convention
C:/xampp/htdocs/APU-EcoSpot
```

### 3. Database Import
1. Start **Apache** and **MySQL** modules from the XAMPP Control Panel.
2. Open **phpMyAdmin** in your browser (`http://localhost/phpmyadmin`).
3. Create a new database named:
   ```sql
   ecospot
   ```
4. Click on the `ecospot` database and go to the **Import** tab:
   - First import: [`data/ecospot.sql`](data/ecospot.sql)
   - Then import: [`data/seed_test_data.sql`](data/seed_test_data.sql)

### 4. Database Configuration
Verify the credentials in [`util/dbcon.php`](util/dbcon.php):
```php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'ecospot';
```

### 5. Launch Application
Navigate to the root URL in your browser:
```
http://localhost/APU-EcoSpot/pages/index.php
```
*(or your configured virtual host path, e.g., `http://localhost/Assignment/src/pages/index.php`)*

---

## 🔑 Test Accounts & Credentials

The seed data provides pre-configured test accounts for each role:

| Role | Email | Password | Student / Staff ID |
| :--- | :--- | :--- | :--- |
| **Student** | `ahmad@mail.apu.edu.my` | `password123` | `TP060001` |
| **Student** | `aisyah@mail.apu.edu.my` | `password123` | `TP060002` |
| **Eco-Ambassador** | `ecoambassador@apu.edu.my` | `abc12345` | `ECO-AMB-01` |
| **Facilities Management** | `admin@apu.edu.my` | `admin123` | `APU-ADM-01` |

---

## 🛡 Security & Implementation Highlights

- **Prepared Statements:** Uses parameterized SQL (`mysqli_prepare`, `bind_param`) across login, registration, ticket creation, and status transitions to protect against SQL Injection.
- **Database Transactions:** Ticket creation and point recalculations are wrapped in `mysqli_begin_transaction()` and `mysqli_commit()` / `mysqli_rollback()` to ensure atomic data integrity.
- **Secure Password Hashing:** User passwords registered through the platform utilize PHP's `password_hash()` with `PASSWORD_DEFAULT` (bcrypt).
- **Session Protection:** Sensitive pages inspect `$_SESSION['user_id']` and role tokens before granting access to management actions.
