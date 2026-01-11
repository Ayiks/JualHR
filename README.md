# HRMS – Employee Management System(ongoing)

A comprehensive **Human Resource Management System (HRMS)** built with **Laravel 10.x**, designed to streamline HR operations including employee management, leave tracking, attendance, complaints, policies, surveys, and more.

---

## 📋 Table of Contents

- [Features](#-features)
- [Technology Stack](#-technology-stack)
- [Prerequisites](#-prerequisites)
- [Installation](#-installation)
- [Environment Configuration](#️-environment-configuration)
- [Database Setup](#️-database-setup)
- [Running the Application](#-running-the-application)

---

## ✨ Features

### 🏢 Core Modules

- **Employee Management** – Complete employee profiles, departments, and reporting hierarchy  
- **Leave Management** – Request, approve, reject leaves with balance tracking  
- **Time & Attendance** – Daily check-in/out and attendance reports  
- **Policy Management** – Company policies with version control  
- **Query / Warning System** – Formal queries, warnings, and disciplinary records  
- **Complaint Management** – Complaint submission, tracking, and resolution  
- **Survey System** – Employee feedback, exit interviews, satisfaction surveys  
- **Onboarding** – New employee document submission and verification  
- **Document Management** – Secure storage of employee documents  

### 👥 User Roles

- **Super Admin** – Full system access and configuration  
- **HR Admin** – Manage employees, leaves, policies, and queries  
- **Line Manager** – Approve team leaves and view team reports  
- **Employee** – Self-service portal for leaves, complaints, and surveys  

---

## 🛠 Technology Stack

### Backend
- **PHP**: 8.2+  
- **Laravel**: 10.x  
- **MySQL**: 8.0+  

### Frontend
- **Tailwind CSS**: 3.x  
- **Alpine.js**: 3.x  
- **Chart.js**: Analytics and dashboards  
- **Laravel Blade**: Templating engine  

### Packages & Dependencies
- Laravel Breeze – Authentication scaffolding  
- Spatie Laravel Permission – Role-based access control  
- Laravel Excel – Data import/export  
- DomPDF / TCPDF – PDF generation  
- Redis – Queue & caching (optional)  

---

## 📦 Prerequisites

Ensure the following are installed:

- **PHP 8.2+** with extensions:
  - BCMath, Ctype, Fileinfo, JSON, Mbstring  
  - OpenSSL, PDO, Tokenizer, XML
- **Composer**
- **Node.js 16+ & npm**
- **MySQL 8.0+**
- **Git**

---

## 🚀 Installation

### 1. Clone the Repositor
git clone https://github.com/Ayiks/JualHR.git
- cd hrms-system

### 2. Install PHP Dependencies
`composer install`

### 3. Install JavaScript Dependencies
`npm install`

---

## ⚙️ Environment Configuration

### 1. Copy Environment File
`cp .env.example .env`

### 2. Generate Application Key
`php artisan key:generate`

### 3. Configure Environment Variables
- APP_NAME="HRMS"
- APP_ENV=local
- APP_DEBUG=true
- APP_URL=http://localhost:8000

- DB_CONNECTION=mysql
- DB_HOST=127.0.0.1
- DB_PORT=3306
- DB_DATABASE=hrms_db
- DB_USERNAME=root
- DB_PASSWORD=

- Mail Configuration
- MAIL_MAILER=smtp
- MAIL_HOST=smtp.mailtrap.io
- MAIL_PORT=2525
- MAIL_USERNAME=null
- MAIL_PASSWORD=null
- MAIL_ENCRYPTION=null
- MAIL_FROM_ADDRESS="noreply@hrms.com"
- MAIL_FROM_NAME="HRMS"

File Storage
- FILESYSTEM_DISK=local

--

## 🗃️ Database Setup
### 1. Create Database
in your phpMyAdmin run `CREATE DATABASE hrms_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;` to create database.

### 2. Run Migrations
`php artisan migrate`

### 3. Seed Database with Initial Data
`php artisan db:seed`
This will create:
- Default user roles (Super Admin, HR Admin, Line Manager, Employee)
- Leave types (Annual, Sick, Casual, Maternity, Paternity)
- Sample departments
- Initial admin user

### 4. Create Storage Link
`php artisan storage:link`

--
## 🏃 Running the Application

### Development Server
`php artisan serve`
Visit: http://localhost:8000

Default Login Credentials
After seeding:

- Email: admin@hrms.com
- Password: password
- Role: Super Admin
