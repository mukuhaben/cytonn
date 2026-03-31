# Cytonn Task Manager 
 
A Task Management REST API built with Laravel and MySQL, with a minimal Vue 3 
frontend (no build tools). Built as a clean, well-structured backend project 
demonstrating strict business rule enforcement, layered validation, and a clear 
separation of concerns. 
 --- 

 
## Tech Stack 
 
| Layer | Technology | 
|---|---| 
| Language | PHP 8.2+ | 
| Framework | Laravel 11 | 
| Database | MySQL | 
| ORM | Eloquent | 
| Frontend | Vue 3 (CDN, no build step) | 
| API Format | JSON REST | 
 --- 
 
## Project Structure 
 
``` 
cytonn/ 
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
          index.php                 # Laravel entry point 
        routes/ 
          api.php                   # API route definitions 
        .env                        # Environment config (DB credentials) 
        artisan                     # Laravel CLI 
        composer.json               # PHP dependencies
    frontend/
        index.html                # Vue 3 frontend (single file) 
``` 
 --- 
 
## Business Rules 
 
These rules are enforced at multiple layers (application + database): 
 
| Rule | Enforcement | 
|---|---| 
| Title cannot duplicate for the same `due_date` | App validation + DB unique 
index | 
| `due_date` must be today or in the future | App validation 
(`after_or_equal:today`) | 
| Status must progress `pending → in_progress → done` only | Model method 
(`canTransitionTo()`) | 
| No skipping or reverting status | Transition map on the Model | 
| Only `done` tasks can be deleted | Model method (`canBeDeleted()`) | 
| Tasks listed by priority (high→low) then `due_date` (asc) | `FIELD()` SQL 
expression in query | 
 --- 
 
## Getting Started 
 
### Prerequisites 
 - PHP 8.2+ - Composer - MySQL - Laravel 11 
 
### Installation 
 
```bash 
# 1. Navigate into the backend folder 
cd cytonn/backend 
 
# 2. Install PHP dependencies 
composer install 
 
# 3. Copy the environment file 
copy .env.example .env 
 
# 4. Generate the application key 
php artisan key:generate 
``` 
 --- 
 
## Database Setup 
 
### 1. Configure your `.env` file 
 
```env 
DB_CONNECTION=mysql 
DB_HOST=127.0.0.1 
DB_PORT=3306 
DB_DATABASE=cytonn 
DB_USERNAME=root 
DB_PASSWORD=your_password
APP_DEBUG=*******
APP_ENV=*******
APP_KEY=*******
APP_URL=*******
CACHE_DRIVER=*******
FRONTEND_URL=*******
LOG_CHANNEL=*******
QUEUE_CONNECTION=*******
SESSION_DRIVER=*******
``` 
 
### 2. Create the database 
 
```sql 
CREATE DATABASE cytonn; 
``` 
 
### 3. Run migrations 
 
```bash 
php artisan migrate 
``` 
 
### 4. Verify 
 
```bash 
php artisan migrate:status 
``` 
 
### 5. Start the development server 
 
```bash 
php artisan serve (local development)
# API available at: http://127.0.0.1:8000/api/v1/
#API_BASE_URL = "https://cytonn-production.up.railway.app/api/v1";
# Frontend available at: http://127.0.0.1:8000/index.html (live server) or https://cytonn-ngatia2026.vercel.app/
``` 
 --- 
 
## API Reference 
 
All endpoints are prefixed with `/api/v1/`. Always include the header: 
``` 
Accept: application/json 
``` 
 --- 
 
### GET `/api/v1/tasks` 
 
List all tasks sorted by priority (high → low) then due date (ascending). 
 
**Response `200`:** 
```json 
[ 
  { 
    "id": 1, 
    "title": "Fix login bug", 
    "description": "Users cannot log in on mobile", 
    "status": "pending", 
    "priority": "high", 
    "due_date": "2026-04-01", 
    "created_at": "2026-03-29T10:00:00.000000Z", 
    "updated_at": "2026-03-29T10:00:00.000000Z" 
  } 
] 
``` 
 --- 
 
### POST `/api/v1/tasks` 
 
Create a new task. Status defaults to `pending` automatically. 
 
**Request body:** 
```json 
{ 
  "title": "Fix login bug", 
  "description": "Optional description", 
  "priority": "high", 
  "due_date": "2026-04-01" 
} 
``` 
 
**Response `201`:** 
```json 
{ 
  "id": 1, 
  "title": "Fix login bug", 
  "description": "Optional description", 
  "status": "pending", 
  "priority": "high", 
  "due_date": "2026-04-01", 
  "created_at": "2026-03-29T10:00:00.000000Z", 
  "updated_at": "2026-03-29T10:00:00.000000Z" 
} 
``` 
 
**Validation failure `422`:** 
```json 
{ 
  "message": "The title has already been taken.", 
  "errors": { 
    "title": ["The title has already been taken."], 
    "due_date": ["The due date must be a date after or equal to today."] 
  } 
} 
``` 
 --- 
 
### PATCH `/api/v1/tasks/{id}/status` 
 
Advance a task's status. Only valid transitions are accepted: 
`pending → in_progress → done` 
 
**Request body:** 
```json 
{ 
  "status": "in_progress" 
} 
``` 
 
**Response `200`:** 
```json 
{ 
  "id": 1, 
  "title": "Fix login bug", 
  "status": "in_progress", 
  ... 
} 
``` 
 
**Invalid transition `422`:** 
```json 
{ 
  "message": "Invalid status transition.", 
  "current_status": "pending", 
  "requested_status": "done", 
  "allowed_next": "in_progress" 
} 
``` 
 --- 
 
### DELETE `/api/v1/tasks/{id}` 
 
Delete a task. Only tasks with status `done` can be deleted. 
 
**Response `200`:** 
```json 
{ 
  "message": "Task deleted successfully." 
} 
``` 
 
**Task not done `422`:** 
```json 
{ 
  "message": "Only tasks with status \"done\" can be deleted.", 
  "current_status": "in_progress" 
} 
``` 
 --- 
 
### GET `/api/v1/tasks/report` 
 
Returns a daily summary of task counts grouped by priority and status. 
 
**Response `200`:** 
```json 
{ 
  "date": "2026-03-29", 
  "totals": { 
    "pending": 3, 
    "in_progress": 2, 
    "done": 5, 
    "total": 10 
  }, 
  "breakdown": [ 
    { "priority": "high",   "status": "pending",     "count": 2 }, 
    { "priority": "high",   "status": "done",        "count": 1 }, 
    { "priority": "medium", "status": "in_progress", "count": 2 }, 
    { "priority": "low",    "status": "pending",     "count": 1 } 
  ] 
} 
``` 
 --- 
 
## Frontend 
 
The frontend is a single HTML file served by Laravel's static file server. 
 
**Access it at:** 
``` 
http://127.0.0.1:8000/index.html
https://cytonn-ngatia2026.vercel.app/
``` 
 
**Features:** - Create tasks with title, priority, due date, and optional description - View all tasks in a sortable table with priority and status badges - Advance task status with a single button click (shows only valid next step) 
- Delete button appears only when task status is `done` - Field-level error messages from API validation - Daily report panel with totals and breakdown by priority and status 
 
**No build tools required.** Vue 3 is loaded via CDN: 
```html 
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script> 
``` 
 --- 
 
## Status State Machine 
 
``` 
  [pending] ──→ [in_progress] ──→ [done] 
 
  Blocked transitions: 
    pending     → done         (skipping not allowed) 
    in_progress → pending      (reverting not allowed) 
    done        → anything     (terminal state) 
``` 
 
The transition map is defined once on the `Task` model and referenced by both the 
backend (enforcement) and the frontend (UI hint): 
 
```php 
public static array $validTransitions = [ 
    'pending'     => 'in_progress', 
    'in_progress' => 'done', 
]; 
``` 
 --- 
 
## Validation Logic 
 
### Title + Due Date Uniqueness 
 
The same title is allowed on different dates. The same title on the same date is 
rejected. 
 - **App layer:** `Rule::unique('tasks')->where('due_date', $request->due_date)` — 
returns a clear 422 error message - **Database layer:** `UNIQUE INDEX (title, due_date)` — hard rejection if app 
layer is bypassed 
 
### Due Date in the Future 
 - **Frontend:** `min` attribute on the date input prevents past date selection 
(UX only) - **App layer:** `after_or_equal:today` validation rule — enforces the real rule 
server-side 
 
### Priority and Status Values 
 - **App layer:** `Rule::in([...])` — validates against allowed values - **Database layer:** `ENUM` column type — MySQL rejects invalid values at query 
level 
 --- 
 
## Design Decisions 
 
**Why is business logic on the Model, not the Controller?** 
Controllers handle HTTP only — parsing requests and returning responses. Business 
rules on the model apply everywhere: API, CLI commands, background jobs, and 
tests. `canTransitionTo()` and `canBeDeleted()` are domain rules, not HTTP rules. 
 
**Why a separate PATCH `/status` endpoint instead of a general PUT?** 
Status progression is a business operation with strict rules, not a simple data 
edit. A dedicated endpoint makes the intent explicit, keeps validation focused, 
and prevents a general update from accidentally bypassing transition rules. 
 
**Why `FIELD()` for priority sorting instead of `ORDER BY priority DESC`?** 
MySQL sorts ENUMs alphabetically — `high < low < medium` — which is wrong for 
semantic priority. `FIELD(priority, 'high', 'medium', 'low')` defines the exact 
sort order needed. 
 
**Why two layers of validation (app + database)?** 
The app layer gives friendly, user-readable error messages. The database layer is 
a hard guarantee — if a race condition or bug bypasses the app layer, the 
database rejects the invalid data at the query level. Defense in depth. 
 
**Why restrict deletion to `done` tasks?** 
Deleting pending or in-progress tasks destroys work history. Restricting deletion 
forces intentional workflow completion before cleanup, preventing accidental loss 
of active work records. 
 --- 
 
 
## Author 
 
Benson Ngatia — Cytonn Task Management API(software engineering internship) 
 
 
