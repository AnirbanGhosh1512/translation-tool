# Translation Management Tool

A web-based translation management tool built with **PHP**, **Docker**, and **Keycloak authentication**, integrated with a **Translation API** built in **.NET 7** and **PostgreSQL**. The application allows users to view and edit translations dynamically with modal-based editing and real-time table updates.

---

## 🚀 Features

* 🔐 Keycloak authentication (JWT-based)  
* 🌍 Multi-language translation filtering  
* ✏️ Inline editing via modal popup  
* ⚡ Instant table refresh without full page reload  
* 🐳 Docker-based environment  
* 🔄 REST API integration for translation services  
* 🗑 Delete SID with all translations  
* 🎯 Event delegation for dynamic UI updates  
* ✅ Backend API supports create, update, delete, and upsert operations  
* 📊 Debug endpoints for user claims and authentication  

---

## 🏗 Architecture

Frontend:  
* PHP (Dashboard + Modals)  
* JavaScript (Fetch API, Event Delegation)  

Backend:  
* Translation API (REST service in .NET 7)  
* PostgreSQL (Translations table with composite primary key SID + LangId)  
* Entity Framework Core for database access  

Authentication:  
* Keycloak (OIDC / JWT)  

Infrastructure:  
* Docker & Docker Compose  

---

## 📂 Project Structure

project-root/  
├── Frontend/  
│   ├── dashboard.php              # Main UI page  
│   ├── save_translation.php       # Handles update requests  
│   ├── create.php                 # Create translations (optional)  
│   ├── login.php                  # Authentication redirect  
│   └── callback.php               # Keycloak callback handler  
├── Backend/TranslationApi/  
│   ├── Controllers/  
│   │   └── TranslationsController.cs  
│   ├── Data/  
│   │   ├── AppDbContext.cs  
│   │   └── AppDbContextFactory.cs  
│   ├── Models/  
│   │   └── Translation.cs  
│   ├── Services/  
│   │   └── TranslationService.cs  
│   ├── Program.cs  
│   └── appsettings.json  
├── docker-compose.yml  
└── README.md  

---

## ⚙️ Requirements

* Docker  
* Docker Compose  
* PHP 8+  
* .NET 7 SDK  
* PostgreSQL  
* Web browser  

---

## 🐳 Running with Docker

Start all services:  
docker-compose up -d --build  

Stop services:  
docker-compose down  

Check running containers:  
docker ps  

---

## 🔐 Authentication Flow

1. User opens the application.  
2. Redirected to Keycloak login.  
3. After login, JWT token is stored in session.  
4. PHP frontend uses the token to call the Translation API.  
5. Translation API validates JWT and enforces `translator` role.  

---

## ✏️ Editing Translations (Frontend)

1. Double-click a table row.  
2. Edit text in modal popup.  
3. Click **Save**.  
4. Data is updated via API.  
5. Table refreshes automatically without page reload.  

---

## 🌍 Language Filtering

Dropdown selection filters translations by language:  
dashboard.php?lang=en  
dashboard.php?lang=de  

The selected language persists during updates.  

---

## 🔄 API Endpoints (Backend)

Translations:  
GET /api/translations           - Get all translations  
GET /api/translations/{sid}/{langId} - Get a translation by SID and language  
POST /api/translations           - Create or update a translation (upsert)  
PUT /api/translations/{sid}/{langId} - Update a translation  
DELETE /api/translations/{sid}/{langId} - Delete a translation  
DELETE /api/translations/{sid}    - Delete all translations for a SID  

Debug & Claims:  
GET /api/translations/claims    - Get all claims for current user  
GET /api/translations/debug     - Debug endpoint to see claims  
GET /api/translations/debug-auth - Check authentication status and roles  

---

### Update Translation Payload Example

{  
  "sid": "ExampleSID",  
  "langId": "de",  
  "text": "Example Text"  
}  

---

## 🛠 Troubleshooting (Frontend)

500 Error on Save:  
Check Docker container names and API URL inside PHP (http://translation-api:8080). Ensure containers can communicate.  

Modal Opens Top-Left:  
Ensure modal CSS uses:  
top: 50%;  
left: 50%;  
transform: translate(-50%, -50%);  

Table Not Updating After Save:  
Make sure `<tbody>` has ID:  
<tbody id="translationsTableBody">  

---

## 📌 Development Notes

* Event delegation is used to keep rows editable after dynamic reload.  
* AJAX reload only updates `<tbody>` for performance.  
* No additional PHP reload endpoints required.  
* Translation API uses EF Core with PostgreSQL and composite key `(SID, LangId)`.  
* Upsert behavior: POST `/api/translations` automatically updates existing translations if SID+LangId exists.  

---

## 📤 Git Workflow

git add .  
git commit -m "Update dashboard editing flow"  
git push origin main  

---

## 👨‍💻 Author

Anirban Ghosh  

---

## 📄 License

This project is for internal / educational use.
