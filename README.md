# Translation Management Tool

A web-based translation management tool built with **PHP**, **Docker**, and **Keycloak authentication**.
The application allows users to view and edit translations dynamically with modal-based editing and real-time table updates.

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

---

## 🏗 Architecture

Frontend:

* PHP (Dashboard + Modals)
* JavaScript (Fetch API, Event Delegation)

Backend:

* Translation API (REST service)

Authentication:

* Keycloak (OIDC / JWT)

Infrastructure:

* Docker & Docker Compose

---

## 📂 Project Structure

```
project-root/
│
├── dashboard.php              # Main UI page
├── save_translation.php       # Handles update requests
├── create.php                 # Create translations (optional)
├── login.php                  # Authentication redirect
├── callback.php               # Keycloak callback handler
│
├── docker-compose.yml
└── README.md
```

---

## ⚙️ Requirements

* Docker
* Docker Compose
* PHP 8+
* Web browser

---

## 🐳 Running with Docker

Start all services:

```bash
docker-compose up -d --build
```

Stop services:

```bash
docker-compose down
```

Check running containers:

```bash
docker ps
```

---

## 🔐 Authentication Flow

1. User opens the application.
2. Redirected to Keycloak login.
3. After login, JWT token stored in session.
4. PHP uses token to call Translation API.

---

## ✏️ Editing Translations

1. Double-click a table row.
2. Edit text in modal popup.
3. Click **Save**.
4. Data is updated via API.
5. Table refreshes automatically without page reload.

---

## 🌍 Language Filtering

Dropdown selection filters translations by language:

```
dashboard.php?lang=en
dashboard.php?lang=de
```

The selected language persists during updates.

---

## 🔄 API Endpoints Used

### Get Translations

```
GET /api/translations
```

### Update Translation

```
PUT /api/translations/{sid}/{langId}
```

Payload:

```json
{
  "sid": "ExampleSID",
  "langId": "de",
  "text": "Example Text"
}
```

---

## 🛠 Troubleshooting

### 500 Error on Save

Check:

* Docker container names
* API URL inside PHP:

```
http://translation-api:8080
```

Make sure containers can communicate.

---

### Modal Opens Top-Left

Ensure modal CSS uses:

```css
top: 50%;
left: 50%;
transform: translate(-50%, -50%);
```

---

### Table Not Updating After Save

Make sure `<tbody>` has ID:

```html
<tbody id="translationsTableBody">
```

---

## 📌 Development Notes

* Event delegation is used to keep rows editable after dynamic reload.
* AJAX reload only updates `<tbody>` for performance.
* No additional PHP reload endpoints required.

---

## 📤 Git Workflow

```bash
git add .
git commit -m "Update dashboard editing flow"
git push origin main
```

---

## 👨‍💻 Author

Anirban Ghosh

---

## 📄 License

This project is for internal / educational use.
