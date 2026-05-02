# Course Equivalency Requests — Laravel 10

## Requirements
- PHP 8.1+
- Composer
- MySQL / MariaDB
- Node.js + npm

## Setup

```bash
# 1. Install dependencies
composer install
npm install && npm run build

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Configure .env
DB_DATABASE=equivalency_db
DB_USERNAME=root
DB_PASSWORD=secret

# 4. Migrate & seed
php artisan migrate --seed

# 5. Storage link (for file uploads)
php artisan storage:link

# 6. Run
php artisan serve
```

## Default Accounts (from seeder)

| Role     | Email                   | Password |
|----------|-------------------------|----------|
| Admin    | admin@university.edu    | password |
| Academic | academic@university.edu | password |

## Public URLs (no login required)

| URL       | Description                              |
|-----------|------------------------------------------|
| /submit   | Public course equivalency request form   |
| /track    | Track a request by tracking code         |
| /login    | Staff login                              |

## Tracking Codes — Sample Data

| Code         | Student          | Status          |
|--------------|------------------|-----------------|
| EQ-SEED0001  | Ahmed Al-Rashidi | entered         |
| EQ-SEED0002  | Sara Hassan      | under_review    |
| EQ-SEED0003  | Mohammed Khalil  | new             |
| EQ-SEED0004  | Layla Nasser     | approved        |
| EQ-SEED0005  | Omar Zayed       | rejected        |
| EQ-SEED0006  | Rania Abu-Salem  | ready_for_entry |

## Status Color Legend

| Status          | Color  |
|-----------------|--------|
| new             | Gray   |
| under_review    | Blue   |
| ready_for_entry | Orange |
| entered         | Purple |
| approved        | Green  |
| rejected        | Red    |

## Feature Summary

### Public Features (no login)
- **Submit a request** — dynamic form with file uploads; generates a unique tracking code (format: `EQ-XXXXXXXX`)
- **Submission confirmation** — displays tracking code with one-click copy
- **Track a request** — enter tracking code to view full status, history timeline, notes, and attachments

### Admin Features (role: admin)
- Full request CRUD
- Filter by status and type, search by name/ID
- Status progression: New → Under Review → Ready for Entry → Entered
- View tracking code and student contact info on every request

### Academic Features (role: academic)
- View all "Entered" requests
- Approve or Reject with notes
- Full status history visible

## Database Schema

```
users                  — id, name, email, password, role (admin|academic)
requests               — id, tracking_code, name, student_id, email, phone,
                         type, major, old_student_id, new_student_id,
                         courses, university, attachments (JSON),
                         status, notes, created_by (nullable FK), timestamps
request_status_histories — id, request_id, old_status, new_status, notes, changed_by, timestamps
```
