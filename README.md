# UCLM GearLoop: The Academic Resource Exchange

## Project Overview
**UCLM GearLoop** is a secure, campus-exclusive marketplace designed for students of the University of Cebu Lapu-Lapu and Mandaue. 

### SDG Alignment
**SDG 12: Responsible Consumption and Production**
By facilitating the reuse and exchange of academic resources like uniforms and books, GearLoop reduces waste and promotes a circular economy within the campus.

### The Problem
Students currently rely on fragmented and risky Facebook groups or personal posts to buy/sell/swap items. This leads to:
- Disorganized searching.
- Safety risks from non-UC outsiders.
- High waste when items are discarded instead of reused.

### The Solution
A unified, verified web platform that integrates:
- **Secure Marketplace:** Only verified students can enter.
- **Trading Engine:** Options for "Sale," "Swap," or "Both."
- **Smart Filtering:** Search by department (Nursing, Maritime, etc.) to find relevant items fast.

---

## Tech Stack
- **Frontend:** HTML5, Vanilla CSS3, JavaScript.
- **Backend:** PHP 7.4+ (Compatible with XAMPP).
- **Database:** MySQL (XAMPP).
- **Architecture:** Monolithic PHP.

---

## How to Run & Install

### Prerequisites
- Install [XAMPP](https://www.apachefriends.org/index.html) or any LAMP/WAMP stack.
- A web browser (Chrome/Edge/Firefox).

### Step 1: Clone/Download
Place the project folder inside your XAMPP `htdocs` directory:
`C:\xampp\htdocs\Gearloop`

### Step 2: Database Setup
1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Create a new database named `gearloop_db`.
3. Import the SQL file located at: `/docs/database_schema.sql`.
   - *This will create the `users` and `items` tables and add a sample student account.*

### Step 3: Configure Database Connection
1. Open `/src/config/db.php`.
2. Ensure the `user` and `pass` match your XAMPP MySQL credentials (default is `root` and no password).

### Step 4: Run the Prototype
1. Start Apache and MySQL in your XAMPP Control Panel.
2. Open your browser and go to: `http://localhost/Gearloop/src/index.php`.

---

## Sample Credentials (No real passwords)
| Role    | Username    | Password |
|---------|-------------|----------|
| Student | student123  | password |

---

## Prototype Walkthrough
1. **Login:** Use the credentials above to enter the secure portal.
2. **Dashboard:** Browse available academic resources listed by other students.
3. **List Item:** 
   - Click "List an Item".
   - Fill out the form (Name, Category, Condition A-D, Sale/Swap).
   - Submit the listing.
4. **Verification:** The item will immediately appear on the Marketplace dashboard for all verified users to see.

---

## Architecture Diagram
*Refer to `/docs/architecture.md` for the system diagram.*
