# Cytonn Task Manager

A Task Management REST API built with Laravel and MySQL, with a minimal Vue 3 frontend (no build tools).
This project demonstrates strong backend architecture with strict business rule enforcement, layered validation, and clear separation of concerns.

---

## Live Demo

* **API Base URL:** https://cytonn-production.up.railway.app/api/v1
* **Frontend:** https://cytonn-ngatia2026.vercel.app/

---

## Tech Stack

| Layer      | Technology                 |
| ---------- | -------------------------- |
| Language   | PHP 8.2+                   |
| Framework  | Laravel 11                 |
| Database   | MySQL                      |
| ORM        | Eloquent                   |
| Frontend   | Vue 3 (CDN, no build step) |
| API Format | JSON REST                  |

---

## Project Structure

```
cytonn/
  backend/
    app/
      Http/Controllers/
        TaskController.php
      Models/
        Task.php
    database/migrations/
    public/
      index.php
    routes/
      api.php
    .env
    artisan
    composer.json

  frontend/
    index.html
```

---

## Business Rules

| Rule                                | Enforcement                      |
| ----------------------------------- | -------------------------------- |
| Title must be unique per `due_date` | App validation + DB unique index |
| `due_date` must be today or future  | App validation                   |
| Status progression enforced         | Model (`canTransitionTo()`)      |
| No skipping/reverting status        | Transition map                   |
| Only `done` tasks deletable         | Model (`canBeDeleted()`)         |
| Sorted by priority then due date    | SQL `FIELD()`                    |

---

## Getting Started

### Prerequisites

* PHP 8.2+
* Composer
* MySQL

---

### Installation

```bash
cd cytonn/backend
composer install
copy .env.example .env
php artisan key:generate
```

---

## Database Setup

### Configure `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cytonn
DB_USERNAME=root
DB_PASSWORD=your_password
```

> Note: `.env` is not included for security reasons. Use `.env.example`.
i.e
APP_DEBUG=
APP_ENV=
APP_KEY=
APP_URL=
CACHE_DRIVER=
DB_CONNECTION=
DB_DATABASE=
DB_HOST=
DB_PASSWORD=
DB_PORT=
DB_USER=
FRONTEND_URL=
LOG_CHANNEL=
QUEUE_CONNECTION=
SESSION_DRIVER=

### Option 1: Run Migrations

```bash
php artisan migrate
```

---

### Option 2: Import SQL Dump

```bash
mysql -u root -p < cytonn.sql
```

---

## Running the Application

```bash
php artisan serve
```

* API: http://127.0.0.1:8000/api/v1
* Frontend: http://127.0.0.1:8000/index.html

---

## Deployment (Railway)

1. Create a Railway project and connect your repository
2. Add a MySQL database
3. Set environment variables in Railway using `.env.example`
4. Run migrations:

   ```bash
   php artisan migrate
   ```
5. Deploy

---

## API Reference

Base path: `/api/v1`
Header:

```
Accept: application/json
```

---

### GET `/tasks`

Returns all tasks sorted by priority and due date.

---

### POST `/tasks`

Create a task.

```json
{
  "title": "Fix login bug",
  "priority": "high",
  "due_date": "2026-04-01"
}
```

---

### PATCH `/tasks/{id}/status`

Advance task status.

```json
{
  "status": "in_progress"
}
```

---

### DELETE `/tasks/{id}`

Deletes task (only if `done`).

---

### GET `/tasks/report`

Returns daily summary of tasks.

---

## Frontend

Single-file Vue 3 app (no build tools).

**Features:**

* Create tasks
* View tasks
* Advance status
* Conditional delete
* Validation feedback
* Daily report

```html
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
```

---

## Status State Machine

```
pending → in_progress → done
```

Invalid:

* skipping steps
* reverting
* modifying `done`

---

## Validation Logic

### Unique Title per Date

* App: validation rule
* DB: unique index

### Due Date

* Frontend: input restriction
* Backend: enforced rule

### Enum Validation

* App: `Rule::in`
* DB: ENUM constraint

---

## Design Decisions

* **Model-based business logic:** reusable and centralized
* **Dedicated status endpoint:** prevents rule bypass
* **Custom priority sorting:** ensures correct ordering
* **Dual validation layers:** safety + usability
* **Restricted deletion:** protects workflow integrity

---

## Author

Benson Ngatia
Software Engineering Internship Challenge (2026)
