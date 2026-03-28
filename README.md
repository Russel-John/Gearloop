# UCLM GearLoop
A campus-exclusive marketplace for the University of Cebu Lapu-Lapu and Mandaue (UCLM), focused on **SDG 12: Responsible Consumption and Production**. It enables students to buy, sell, or swap academic resources in a secure, verified environment.

![Homepage](src\public\images\Screenshot 2026-03-29 014042.png)


## 🌟 Key Features
- **Academic Marketplace:** Browse and search for academic resources (uniforms, books, equipment) categorized by department.
- **Transaction Request System:** Instead of direct buying, users send "Buy" or "Trade" requests to initiate negotiations.
- **Trade Offers:** Buyers can specify exactly what they are offering in exchange during a trade request.
- **Campus Meetup Coordination:** Once a request is accepted, parties can coordinate meetups at specific UCLM buildings (Basic Ed, CBE, Annex, Old Annex, or Maritime).
- **Notification System:** Real-time notification badges in the navigation bar for incoming transaction requests.
- **User Profiles:** Manage your profile with custom bio, department info, and a 1:1 square-cropped profile picture.
- **GEPO AI Chatbot:** An integrated AI assistant powered by Google Gemini to help students navigate the marketplace and understand campus trade rules.
- **Modern Responsive UI:** A polished, "app-like" interface featuring soft shadows, rounded corners (16px), and full mobile responsiveness.

## 🛠️ Tech Stack
- **Backend:** PHP 7.4+ (Monolithic)
- **Database:** MySQL (Managed via PDO)
- **Frontend:** HTML5, CSS3 (Vanilla), JavaScript
- **AI Integration:** Google Gemini API (gemini-flash-latest)
- **Environment:** Designed for XAMPP (Apache/MySQL)

## 🚀 Setup Instructions

### 1. Database Setup
1. Start **Apache** and **MySQL** via the XAMPP Control Panel.
2. Open `phpMyAdmin` (`http://localhost/phpmyadmin`).
3. Create a new database named `gearloop_db`.
4. Import the SQL schema located at `docs/database_schema.sql`.

### 2. Environment Configuration
1. In the root directory, locate or create a `.env` file.
2. Configure your credentials as follows:
```env
DB_HOST=localhost
DB_NAME=gearloop_db
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4
GEMINI_API_KEY=your_google_ai_studio_key_here
```

### 3. Application Access
Navigate to the following URL in your web browser:
`http://localhost/Gearloop/src/index.php`

## 📂 Directory Organization
- `src/`: Contains all PHP logic and page structures.
- `src/public/css/`: Centralized `styles.css` containing the entire design system.
- `src/public/js/`: Modular JavaScript files (e.g., `profile.js` for image cropping).
- `src/public/images/`: Static branding assets like the GEPO AI logo.
- `src/public/uploads/`: Secure storage for profile pictures and item images.
- `docs/`: Project documentation and database schemas.

## 🌿 Sustainable Development Goals
UCLM GearLoop is committed to **SDG 12: Responsible Consumption and Production**. By facilitating the reuse of uniforms and books, we help reduce the environmental footprint of our campus community.

---
*Developed for the UCLM Community.*
