# GEMINI.md - Instructional Context for UCLM GearLoop

## Project Overview
**UCLM GearLoop** is a campus-exclusive marketplace for the University of Cebu Lapu-Lapu and Mandaue (UCLM), focused on **SDG 12: Responsible Consumption and Production**. It enables students to buy, sell, or swap academic resources (uniforms, books, equipment) in a secure, verified environment.

### Tech Stack
- **Backend:** PHP 7.4+ (Monolithic architecture)
- **Database:** MySQL (managed via PDO)
- **Frontend:** HTML5, Vanilla CSS3 (UCLM Blue/Yellow branding)
- **Environment:** Designed for XAMPP (Apache/MySQL)

---

## Directory Structure
- `/src`: Application source code.
    - `/config`: Database connection (`db.php`).
    - `/public`: Static assets (CSS, JS, images).
- `/docs`: Project documentation, architecture diagrams, and SQL schemas.

---

## Building and Running
The project is designed to run in a local LAMP/WAMP/XAMPP environment.

1.  **Database Setup:**
    - Create a database named `gearloop_db` in MySQL.
    - Import `/docs/database_schema.sql` to initialize tables and sample data.
2.  **Web Server:**
    - Place the project in the web root (e.g., `htdocs/Gearloop`).
    - Start Apache and MySQL via XAMPP Control Panel.
3.  **Access:**
    - Navigate to `http://localhost/Gearloop/src/index.php`.
    - Login with `student123` / `password`.

---

## Development Conventions

### PHP & Backend
- **Session Management:** All protected pages must call `session_start()` and verify `$_SESSION['user_id']`.
- **Database Access:** Use the `$pdo` instance from `src/config/db.php`. Always use prepared statements for security.
- **Form Processing:** Logic for processing forms (e.g., `process-list-item.php`) is separated from the UI files.

### Styling & UI
- **Branding:** Adhere to UCLM colors defined in `src/public/css/styles.css`:
    - `--primary-color: #003366` (Blue)
    - `--secondary-color: #FFCC00` (Yellow)
- **Layout:** Use the `.container` and `.form-card` classes for consistent spacing and styling.
- **Responsiveness:** Ensure marketplace grids (`.item-grid`) are responsive across device sizes.

### Data Model
- **Users:** Verified student/staff accounts.
- **Items:** Listings categorized by Department, Category (Uniform/Book), and Condition (Grade A to D).
- **Listing Types:** Support for "For Sale", "For Swap", and "Both".
---

## Current State & Progress (As of March 29, 2026)

### ✅ Completed Features
- **UI/UX Overhaul:** Modernized dashboard with 16px rounded corners, soft shadows, and FontAwesome icons.
- **Responsiveness:** Grid system optimized for mobile (1 col), tablet (2 col), and desktop.
- **Profile System:** 1:1 image cropping (Cropper.js), custom bio, and department selection.
- **Transaction System:** Replaced direct "Buy" with a Request-Accept-Coordinate flow.
- **Meetup Coordination:** Integrated campus building selection (Basic Ed, CBE, Annex, Old Annex, Maritime).
- **GEPO AI Chatbot:** Integrated Google Gemini (gemini-flash-latest) with a custom system identity.
- **Environment Security:** Added `.env` support for DB credentials and API keys.
- **Navigation:** Standardized headers across all pages with a real-time notification badge for incoming requests.

### 🛠️ Technical Decisions
- **File Organization:** Strictly separated CSS (`styles.css`) and JS (`profile.js`) from PHP files.
- **Monolithic Structure:** Logic and HTML are kept within standalone `.php` files (no more `includes/` folder per user preference).
- **Authentication:** Users can login using either their **Username** or **Student ID**.

### 📋 Next Steps / Pending
- Implement the actual message/chat database for "Coordinate Meetup".
- Finalize the chatbot logic once the user adds their live API key.
- Add "Delete Listing" functionality to My Listings.

