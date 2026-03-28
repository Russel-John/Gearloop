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

## Verification & Testing
Before committing changes:
- Ensure database interactions are tested against the `gearloop_db` schema.
- Verify that authentication middleware (session checks) correctly redirects unauthenticated users to `index.php`.
- Test the end-to-end "List an Item" flow to ensure listings persist in the database and appear on the dashboard.
