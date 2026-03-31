# Cytonn Task Manager API

A RESTful Task Management API built with Laravel and MySQL.
This project demonstrates clean API design, strict business rule enforcement, and production-ready backend practices.

---

## Live API

Base URL:

```
https://your-render-url/api/v1
```

---

##  Features

* Create tasks
* List tasks (with sorting & optional filtering)
* Update task status with strict progression rules
* Delete tasks (only when completed)
* Daily task report (summary by priority & status)

---

##  Tech Stack

* PHP 8.2+
* Laravel 11
* MySQL
* Eloquent ORM
* Vue 3 (CDN, minimal frontend)

---



##  Folder structure

Cytonn/ 
  backend/ 
    app/ 
      Http/ 
        Controllers/ 
          TaskController.php    # All API endpoint handlers 
      Models/ 
        Task.php                # Business rules + Eloquent model 
    database/ 
      migrations/ 
        xxxx_create_tasks_table.php  # Schema definition 
    public/ 
      index.html                # Vue 3 frontend (single file) 
      index.php                 # Laravel entry point (do not edit) 
    routes/ 
      api.php                   # API route definitions 
    .env                        # Environment config (DB credentials) 
    artisan                     # Laravel CLI 
    composer.json               # PHP dependencies 


---


##  Setup Instructions

```bash
git clone https://github.com/mukuhaben/task-manager-.git
cd cytonn/backend

composer install

cp .env.example .env
php artisan key:generate
```

### Configure Database

Update `.env`:

```
DB_DATABASE=cytonn
DB_USERNAME=root
DB_PASSWORD=your_password
```

Run migrations:

```bash
php artisan migrate
```

Start server:

```bash
php artisan serve
```

API runs at:

```
http://127.0.0.1:8000/api/v1
```

---

##  API Endpoints

### 1. Create Task

```
POST /api/v1/tasks
```

**Body:**

```json
{
  "title": "Finish report",
  "priority": "high",
  "due_date": "2026-04-01"
}
```

**Response (201):**

```json
{
  "message": "Task created successfully",
  "data": { ... }
}
```

---

### 2. List Tasks

```
GET /api/v1/tasks
GET /api/v1/tasks?status=pending
```

* Sorted by priority (high → low), then due date (ascending)
* Optional `status` filter

**Empty state (404):**

```json
{
  "message": "No tasks found."
}
```

---

### 3. Update Task Status

```
PATCH /api/v1/tasks/{id}/status
```

**Body:**

```json
{
  "status": "in_progress"
}
```

✔ Allowed:

* pending → in_progress → done

❌ Not allowed:

* skipping or reverting

**Invalid transition (400):**

```json
{
  "error": "Invalid status transition"
}
```

---

### 4. Delete Task

```
DELETE /api/v1/tasks/{id}
```

✔ Only allowed if task is `done`

**If not done (403):**

```json
{
  "error": "Only completed tasks can be deleted"
}
```

---

### 5. Daily Report

```
GET /api/v1/tasks/report?date=YYYY-MM-DD
```

**Response (200):**

```json
{
  "date": "2026-04-01",
  "summary": {
    "high": {"pending": 1, "in_progress": 1, "done": 0},
    "medium": {"pending": 0, "in_progress": 0, "done": 2},
    "low": {"pending": 0, "in_progress": 0, "done": 1}
  }
}
```

---

##  Testing

You can test the API using:

* Postman
* curl

Example:

```bash
curl -X POST http://127.0.0.1:8000/api/v1/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"title":"Test Task","priority":"high","due_date":"2026-04-01"}'
```

---

##  Business Rules Enforced

* Task title must be unique per `due_date`
* `due_date` must be today or in the future
* Status progression is strictly enforced:

  * `pending → in_progress → done`
* Tasks cannot skip or revert status
* Only completed (`done`) tasks can be deleted
* Tasks are sorted by priority, then due date

---

##  Author

Ngatia
Software Engineering Internship Candidate — 2026
