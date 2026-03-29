# Architecture Overview - UCLM GearLoop

## Introduction
UCLM GearLoop is a monolithic web application designed for the University of Cebu Lapu-Lapu and Mandaue (UCLM) community. It leverages a standard LAMP-like stack (Linux/Apache/MySQL/PHP) adapted for local development via XAMPP.

## System Components

### 1. Presentation Layer (Frontend)
- **Technologies:** HTML5, Vanilla CSS3, Vanilla JavaScript.
- **Key Features:**
  - Responsive design for mobile, tablet, and desktop.
  - Interactive elements like the GEPO AI Chatbot and profile image cropping (Cropper.js).
  - Consistent UCLM branding (Blue & Yellow).

### 2. Application Layer (Backend)
- **Technologies:** PHP 7.4+.
- **Architecture:** Monolithic architecture where logic and UI are managed within standalone `.php` files.
- **Key Processes:**
  - **Authentication:** Session-based login using Username or Student ID.
  - **Item Management:** CRUD operations for marketplace listings.
  - **Transaction Flow:** Request-Accept-Coordinate system for campus meetups.
  - **AI Integration:** `process-chat.php` handles communication with the Google Gemini API.

### 3. Data Layer (Persistence)
- **Database:** MySQL.
- **Access Method:** PHP Data Objects (PDO) with prepared statements for security against SQL injection.
- **Schema:**
  - `users`: Stores student/staff profiles and credentials.
  - `items`: Manages marketplace listings and their status.
  - `transactions`: Tracks swap/sale requests and meetup details.

### 4. External Integrations
- **Google Gemini API:** Powering the GEPO AI Chatbot for intelligent item recommendations and marketplace assistance.
- **FontAwesome:** For consistent iconography across the platform.

## Request Lifecycle & Data Flow

1.  **User Interaction:** A user performs an action in the browser (e.g., searches for an item).
2.  **Server Request:** The browser sends an HTTPS request to the corresponding PHP file (e.g., `dashboard.php`).
3.  **Authentication & Config:** The application initializes the session and loads database credentials from the `.env` file via `config/db.php`.
4.  **Database Query:** PHP executes a PDO query to fetch or update data in the `gearloop_db`.
5.  **AI Processing (Optional):** If the chatbot is engaged, `process-chat.php` sends a cURL request to the external Gemini API, providing it with the current marketplace context.
6.  **Response Delivery:** The server returns the final HTML/JSON to the browser, and the UI is updated dynamically.

## Security Considerations
- **Environment Variables:** Sensitive credentials (API keys, DB passwords) are stored in a `.env` file and never hard-coded.
- **SQL Injection Prevention:** Universal use of PDO prepared statements.
- **Session Security:** All protected routes verify active user sessions before processing requests.

---
*Refer to `docs/architecture_diagram.xml` for a visual representation in draw.io.*
