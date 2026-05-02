# Exhaustive Application Blueprint

This document contains the *complete* and *exhaustive* rules, database schema, frontend logic, backend architecture, and specific mechanics required to clone or rebuild this application accurately.

---

## 1. Core Tech Stack & Infrastructure
### Backend
- **Framework:** Laravel 12.0
- **PHP Version:** ^8.2
- **Admin Panel Framework:** Filament ^4.0
- **Real-time Engine:** Laravel Reverb ^1.6
- **Cache Driver:** Custom `App\Services\CacheService` heavily utilizing Laravel's Cache, integrated deeply across models to clear caches logically on create/update/delete.
- **Queue/Jobs:** Database driver configured (`0001_01_01_000002_create_jobs_table.php`).

### Frontend
- **Framework:** Livewire v3 (using Volt ^1.7.0 and Flux ^2.1.1)
- **Styling ecosystem:** Tailwind CSS ^4.1.17 configured with dark mode support (`class`) and specific custom themes (Zinc palette and custom Blues).
- **Build tool:** Vite ^7.0.4 with `laravel-vite-plugin`.
- **PWA / Service Worker:** Present via `public/sw.js` storing essential assets (`/dashboard`, manifest, offline pages) and specifically excluding `/admin`, `/filament`, `/livewire/`, and `/api/` from cache-first mechanics.

---

## 2. Exhaustive Database Schema

### 2.1. Users Table
- Standard Auth: `name`, `email`, `password`, `created_at`, `updated_at`.
- Custom fields:
  - `role`: string (e.g., 'superadmin', 'admin', 'user').
  - `phone`: string (untuk kontak).
  - E-Wallet details: `ewallet_type` (OVO, DANA, GoPay), `ewallet_number`, `ewallet_name`.
  - `is_banned`: boolean.
  - `badge`: string (ranking user).

### 2.2. Categories Table (`2025_08_22_000001_create_categories_table`)
- `name` (string)
- `slug` (string)
- `description` (text)
- `is_active` (boolean)
- `created_by` (foreignID -> Users)

### 2.3. Tasks Table (`2025_08_22_000002_create_tasks_table`)
- `category_id` (foreignID)
- `admin_id` (foreignID -> Users)
- `created_by` (foreignID -> Users)
- `title` (string)
- `vcf_data` (longText - for downloadable contacts)
- `description` (text)
- `whatsapp_group_link` (string, nullable)
- `difficulty_level` (enum/string: 'easy', 'medium', 'hard')
- `expired_at` (datetime)
- `is_expired` (boolean)
- `priority_order` (integer)
- `estimated_amount` (decimal:2)

### 2.4. UserTasks Table (Pivot / Action Table)
*This is the most critical table in the app containing validation states.*
- `task_id`, `user_id`
- `status` (string: `taken`, `pending_verification_1`, `pending_verification_2`, `completed`, `cancelled`, `failed`, `banned`)
- `taken_at`, `deadline_at`, `cancelled_at`, `completed_at` (datetimes)
- `failed_count` (integer)
- Verification 1:
  - `verification_1_status` (string/text: heavily used to store logs ex: "Submitted at ... Description: ...")
  - `verification_1_files` (JSON array of file paths)
  - `verification_1_approved_by` (foreignID -> Users)
  - `verification_1_approved_at` (datetime)
- Verification 2:
  - `verification_2_status` (string/text)
  - `verification_2_files` (JSON array)
  - `verification_2_approved_by` (foreignID)
  - `verification_2_approved_at` (datetime)
- Payment:
  - `payment_status` (string: `pending`, `success`, `failed`)
  - `payment_amount` (decimal:2)
  - `amount_change_reason` (text)
  - `payment_verified_by_admin_id` (foreignID)
  - `payment_verified_at` (datetime)

### 2.5. TaskMessages Table (`2025_12_08_225220_create_task_messages_table`)
- `user_task_id` (foreignID)
- `sender_type` (string: 'user', 'admin')
- `message` (text)
- `file_path`, `file_type`, `file_name` (string, nullable)
- `is_read` (boolean, default 0)
- `read_at` (datetime, nullable)

### 2.6. Notifications Table (`2026_02_08_034904_create_notifications_table`)
- Basic broadcast notification struct for users.

---

## 3. Detailed Logic & Workflows

### 3.1. Frontend Task Execution Wizard (`TaskWorkWizard.php`)
The core user mechanic is a 4-step wizard:
- **Step 1: Instructions.** The user accepts the task rules. Cannot proceed until explicitly accepting via toggle (`understoodInstructions = true`). If timeout occurs (10m strict), task fails auto.
- **Step 2: Proof 1 (V1).** Upload files or input text. Files stored in `task-proofs/{task_id}/verification-1`. System saves to `verification_1_files` as JSON array. Status shifts to `pending_verification_1`.
- **Step 3: Proof 2 (V2).** Accessible *only* after Admin approves V1. Same file logic, saves to `verification-2`. Shifts to `pending_verification_2`.
- **Step 4: Completion.** User waits for final admin approval and payment assignment.
*Strict security:* Methods contain `verifyUserTaskOwnership()` to prevent manipulation. Heavy caching resets upon any state changes.

### 3.2. Admin Panel (Filament `UserTaskResource.php`)
- **Isolation of Data:** Superadmins see everything. Regular Admins *only* see `UserTask` records corresponding to `task.created_by == auth()->id()`.
- **Review UI (Lightbox):** Custom HTML injection utilizing JS lightbox logic within Filament Modals (`view_proofs` action) to display V1/V2 images without leaving the dashboard table.
- **Approval Flow Actions:**
  - `approve_verification_1`: Sets V1 to approved, triggers NotificationService to User, advances task to allow V2.
  - `reject_verification_1`: Prompts for reason, marks task as Failed, releases task back to the general pool.
  - `approve_verification_2`: Triggers a form demanding `payment_amount` (defaulting to task's `estimated_amount`). Marks task fully `completed`.
- **Payments:** Payment status operates independently from Task status. Task `completed` does not mean `payment_status = success`. There is an explicit `completed_unpaid` filter in the Filament table.

### 3.3. Real-Time Chat System
- Uses standard Laravel Pusher/Echo over Reverb.
- Connected specifically to a `UserTask` context (`/admin/chat/{record}` and user equivalent).
- Enables live dispute resolution or instruction giving directly per task attempt.

### 3.4. VCF Generation Route
- Uniquely serves text stored under `vcf_data` in the database dynamically as `text/vcard` format via `user/task/{task}/vcf` route. The frontend prompts a file download named `task-{id}-{slug}.vcf`.

### 3.5. Middlewares Required
- `auth`: General web auth.
- `not-banned`: Blocks `User->is_banned() == true` from accessing any Livewire dash parts.
- `can-take-task`: Checks against user's active concurrent tasks limit before allowing entry to task taking UI.

---

## 4. Caching Rules
- The application implements hyper-aggressive caching via a custom `CacheService`.
- **Rule:** `UserTask` `created`, `updated`, or `deleted` triggers a flush of:
  - Global `available_tasks_count` badge.
  - `user_active_tasks_{id}`
  - `dashboard_stats_{id}`
  - Specific task keys.
This means *no* direct database updates should bypass Eloquent events, otherwise the frontend will show stale task pools.

---

## 5. Development Strategy for Replication
1. Configure Laravel 12 + Reverb + Livewire + Filament stack identically.
2. Port the exhaustive database schema *exactly* as specified to ensure model casts (`verification_1_files` JSON array, `taken_at` datetime) match.
3. Build the core Eloquent Models with the specific relationships (`Task` -> `UserTasks` <- `User`).
4. Recreate the Filament Resources (`CategoryResource`, `TaskResource`, `UserResource`, and specifically `UserTaskResource` with the custom HTML Lightbox review modal and conditional Action Buttons).
5. Build the `TaskWorkWizard` Volt/Livewire component replicating the 4-step state machine logic strictly, along with `TaskDashboard` and `MyTasks` views.
6. Verify Service Worker routing bypasses (`/admin`, `/livewire`) to avoid breaking dynamic state.
