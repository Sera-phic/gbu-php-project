# GBU Portal System – Event Management & Placement Cell

This project is a full-stack PHP-based web system designed to support college operations by providing two integrated portals:
1. **Event Management & Registration Portal**
2. **Placement Cell Student Portal**

The system is modular, scalable, and built with real-world deployment in mind. It allows students to interact with college services efficiently while enabling administrators to manage data securely and effectively.

---

## 📌 Project Objectives

- Improve student engagement through centralized digital services.
- Simplify event registration and placement management.
- Provide a real-world deployable system that can be integrated into the college website.
- Ensure equal learning and contribution for both team members.

---

## 🧱 Project Architecture

The project consists of a **common landing page** that directs users to two independent portals:

- `/event/` → Event Management Portal  
- `/placement/` → Placement Cell Portal  

Both portals share common configurations (database connection, authentication logic, assets) but function independently.

---

## 🧭 Landing Page Features

- Acts as the homepage of the system.
- Displays two main options:
  - 🎉 Event Management Portal
  - 💼 Placement Cell Portal
- Routes users to the respective modules.

---

## 🎉 Event Management Portal (Module 1)

### Features:
**Student Side**
- View upcoming and past college events.
- Register for events.
- View registration status.
- Receive event details and updates.

**Admin Side**
- Add, update, and delete events.
- View registered participants.
- Manage event categories and dates.
- Export participant lists.

### Purpose:
To digitalize college event management, reduce manual registration work, and increase student participation.

---

## 💼 Placement Cell Portal (Module 2)

### Features:
**Student Side**
- Register and login.
- Create and update profile.
- Upload resume.
- View job/internship postings.
- Apply for jobs.
- Track application status.

**Admin (Placement Cell) Side**
- Post job and internship opportunities.
- View and filter applicants.
- Download resumes.
- Manage placement drives.
- View student statistics.

### Purpose:
To streamline placement operations and maintain a centralized student recruitment platform.

---

## 🛠️ Technologies Used

- **Backend:** PHP (Core PHP)
- **Frontend:** HTML, CSS, JavaScript, Bootstrap
- **Database:** MySQL
- **Server:** Apache (via XAMPP)
- **Version Control:** Git & GitHub
- **Deployment Ready:** Yes (can be hosted on any PHP-supported server)

---

## 🗂️ Folder Structure

```bash
college-portal/
│
├── index.php              # Main landing page
├── assets/               # Common CSS, JS, images
│
├── event/                # Event Management Portal
│   ├── index.php         # Event portal home
│   ├── student/          # Student-facing pages
│   ├── admin/            # Admin dashboard
│   ├── includes/         # Header, footer, auth
│   └── db/               # Event-related queries
│
├── placement/            # Placement Cell Portal
│   ├── index.php         # Placement portal home
│   ├── student/          # Student-facing pages
│   ├── admin/            # Admin dashboard
│   ├── includes/         # Header, footer, auth
│   └── db/               # Placement-related queries
│
├── config/
│   └── database.php      # Shared database connection
│
├── auth/
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│
└── README.md

