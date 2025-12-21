# HRMS – Employee Management System

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
- [User Roles & Permissions](#-user-roles--permissions)
- [Project Structure](#-project-structure)
- [Development Workflow](#-development-workflow)
- [Testing](#-testing)
- [Deployment](#-deployment)
- [Troubleshooting](#-troubleshooting)
- [API Documentation](#-api-documentation)
- [Contributing](#-contributing)
- [License](#-license)
- [Support](#-support)

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

### 1. Clone the Repository
```bash
git clone <repository-url>
cd hrms-system
