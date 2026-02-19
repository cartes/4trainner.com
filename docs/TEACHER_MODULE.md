# Teacher Student Module

This module allows users with the `profesor` (Teacher) role to manage their assigned students, view their details, and track their progress.

## Overview

The module consists of:
- **Backend**: Laravel Controllers, Models, and Migrations.
- **Frontend**: Vue.js components integrated into Blade views.
- **Database**: Pivot table for teacher-student relationships and a table for tracking progress.

## Database Schema

### `professor_student` (Pivot Table)
Links professors (teachers) to students. Both are instances of the `User` model.
- `id`: Primary Key
- `professor_id`: Foreign Key to `users.id`
- `student_id`: Foreign Key to `users.id`
- `timestamps`

### `student_progress`
Tracks various metrics and activities for a student.
- `id`: Primary Key
- `student_id`: Foreign Key to `users.id`
- `professor_id`: Foreign Key to `users.id` (Author of the record)
- `type`: String (e.g., 'attendance', 'weight', 'height', 'routine_completion')
- `value`: JSON (Flexible data structure for different metric types)
- `notes`: Text (Optional)
- `date`: Date
- `timestamps`

## Models

### `User`
Updated with the following relationships:
- `students()`: BelongsToMany (as a professor)
- `professors()`: BelongsToMany (as a student)
- `progress()`: HasMany (as a student)

### `StudentProgress`
Represents a single progress record.
- `student()`: BelongsTo `User`
- `professor()`: BelongsTo `User`

## API Endpoints

All endpoints are prefixed with `/api/v1` and require authentication + `profesor` or `super-admin` role.

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/teacher/students` | List all students assigned to the authenticated teacher. |
| GET | `/teacher/students/{id}` | Get details for a specific student. Returns 404 if the student is not assigned to the teacher. |

## Frontend

The frontend uses Vue.js components mounted within Blade templates.

### Components
- **`resources/js/Components/Teacher/Students/Index.vue`**:
  - Lists students in a responsive grid.
  - Includes search functionality.
  - Displays basic info (Photo, Name, Email, Phone, Status).

- **`resources/js/Components/Teacher/Students/Show.vue`**:
  - Detailed dashboard for a single student.
  - Displays contact info.
  - Shows metrics (Weight, Height, Body Fat).
  - Displays progress bars for Attendance and Routine Completion.
  - Lists recent history.

### Views
- `resources/views/teacher/students/index.blade.php`: Mounts `StudentIndex`.
- `resources/views/teacher/students/show.blade.php`: Mounts `StudentShow`.

## Routes (Web)

Prefix: `/teacher`
Middleware: `auth`, `role:profesor|super-admin`

- `GET /teacher/students` -> `Teacher\StudentController@index`
- `GET /teacher/students/{id}` -> `Teacher\StudentController@show`
