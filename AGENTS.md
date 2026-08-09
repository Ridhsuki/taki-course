# AGENTS.md

Operational contract and guardrails for AI coding agents working on `taki-course`.

---

## 1. Project Overview

* **Framework**: Laravel 11.31 (PHP 8.2+)
* **Architecture Pattern**: MVC with Blade templates, FormRequests, Eloquent Models, and Spatie Role-Based Access Control (RBAC).
* **Frontend Stack**:
  * **Admin & Auth**: Blade views powered by Tailwind CSS via Vite (`@vite(['resources/css/app.css', 'resources/js/app.js'])`) and Alpine.js.
  * **Public / Student Front**: Blade views using compiled Tailwind CSS (`public/css/output.css`), jQuery 3.7.1, Flickity 2 (carousels), Plyr (video player), Fancybox (lightboxes), and custom JavaScript (`public/js/main.js`).
* **Key Dependencies**: `spatie/laravel-permission` (v6.25), `laravel/breeze` (v2.4).
* **Database Assumptions**: SQLite for local testing (`:memory:`) or MySQL (production/dev environment). Soft deletes (`$table->softDeletes()`) exist in migrations for `Teacher`, `Category`, `Course`, `CourseKeypoint`, `CourseVideo`, `SubscribeTransaction`, and `CourseStudent`. The `SoftDeletes` trait is enabled on Eloquent models for `Teacher`, `Category`, `Course`, `CourseKeypoint`, `CourseVideo`, and `SubscribeTransaction`. *Note*: `CourseStudent` model does NOT currently import or use the `SoftDeletes` trait despite the database column existing in migration (alignment is DEFERRED technical debt).

---

## 2. Confirmed Business Rules & Intent

Future agents must preserve these business invariants when working on authorization, course management, subscription, checkout, or learning access:

* **Owner Scope**: Global platform administrator. Authorized to manage Students, Teachers, all Courses, Course Videos, and subscription transaction approvals. Owner can preview any course content without purchasing a Student subscription. Owner must NOT implicitly become a `Teacher`. In `CourseController@store`, requiring a matching `Teacher` record for Owner is an implementation gap.
* **Teacher Scope**: Authorized to manage only Courses and Course Videos assigned to that Teacher. Teacher can preview their own course content without a Student subscription. Teachers must never access another Teacher's course management resources (enforcing missing object-level authorization is a known security task).
* **Student & Global Subscription Scope**: Current subscription model is **platform-wide**. An approved, active `SubscribeTransaction` (`is_paid = true`, within 1 month) grants access to ALL platform courses. `SubscribeTransaction` is the sole entitlement source.
* **CourseStudent Semantics**: `CourseStudent` is NOT the source of global subscription authorization. `CourseStudent` records specific course participation when an entitled student accesses a valid course. Payment approval does not seed `CourseStudent` rows for every course.
* **Profile Editing**: All authenticated users (`owner`, `teacher`, `student`) are authorized to edit their own profile (`name`, `email`, `password`, `occupation`, `avatar`) via `ProfileController`.
* **CourseStudent Soft Delete**: `course_students` migration contains `deleted_at`, but `CourseStudent` model does not import `SoftDeletes`. Adding `SoftDeletes` is **DEFERRED** technical debt pending future access model redesign. Do NOT add `SoftDeletes` now.

---

## 3. Domain Terminology

| Term | Description |
| :--- | :--- |
| **User** | System account containing auth credentials, profile data (`name`, `email`, `occupation`, `avatar`), and Spatie roles (`owner`, `teacher`, `student`). |
| **Teacher** | Teacher profile entity linked to a `User` via `user_id`. Has an `is_active` status flag and owns `Course` records. |
| **Category** | Course category entity containing `name`, `slug`, and `icon`. Uses `slug` as Eloquent route key. |
| **Course** | Learning course created by a `Teacher` under a `Category`. Contains `name`, `slug`, `path_trailer`, `about`, and `thumbnail`. |
| **CourseVideo** | Individual video lesson tied to a `Course` via `course_id`. Stores video identifier/URL in `path_video`. |
| **CourseKeypoint** | Key learning outcome or bullet point associated with a `Course`. |
| **CourseStudent** | Pivot model tracking course participation between a `User` (student) and a `Course`. |
| **SubscribeTransaction** | Subscription invoice and proof submission. Contains `total_amount`, `is_paid` (boolean flag), `subscription_start_date`, `proof` image path, and `user_id`. |

---

## 4. Architecture & Routing Rules

1. **Route Model Binding**:
   * Public front routes use slug binding where available: `/details/{course:slug}` and `/category/{category:slug}`.
   * Admin routes use ID implicit model binding: `Route::resource('courses', CourseController::class)`.
   * Explicit parameter binding in `web.php` for video addition: `/admin/add/video/{course:id}`.
2. **Transactional Protection**:
   * Use database transactions (`DB::transaction(...)`) when an operation contains multiple related writes that must succeed or fail atomically, or when data consistency requires transactional protection.
   * Do NOT require `DB::transaction()` for trivial single-record writes where Laravel/Eloquent already performs an atomic statement and there is no multi-step consistency requirement.
3. **File Storage**:
   * Uploaded media (thumbnails, icons, avatars, proofs) must be stored using Laravel's `Storage` facade on the `public` disk.
4. **Form Request Validation**:
   * Input validation logic belongs in dedicated `FormRequest` classes in `app/Http/Requests`.
   * Form requests perform role-level authorization checks in their `authorize()` method using `$this->user()->hasAnyRole(...)`.

---

## 5. Role and Authorization Invariants

The application defines three Spatie roles: `owner`, `teacher`, `student`.

### Verified Permission Rules
* **`owner`**:
  * **Route-level access**: Authorized to access Categories (`admin.categories.*`), Teachers (`admin.teachers.*`), Subscribe Transactions (`admin.subscribe_transactions.*`), as well as Courses (`admin.courses.*`) and Course Videos (`admin.course_videos.*`) alongside `teacher`.
* **`teacher`**:
  * **Route-level access**: Authorized to access Courses (`admin.courses.*`) and Course Videos (`admin.course_videos.*`).
* **`student`**:
  * Default role assigned upon registration via `RegisteredUserController`.
  * Authorized to access pricing, checkout (`/checkout`, `/checkout/store`), and course learning pages.

### Documented Implementation Gaps (CRITICAL)
* **Owner Course Creation Inconsistency**: Although `owner` has route-level access to `/admin/courses`, `CourseController@store` attempts to fetch `$teacher = Teacher::where('user_id', Auth::user()->id)->first()`. If an `owner` user who is not explicitly listed in the `teachers` table attempts to create a course, the action fails with `"Unauthorized or invalid teacher."`. Intended behavior is that Owner can assign a Course to an existing Teacher or manage Courses without being a Teacher.
* **Missing Object-Level Authorization for Teachers**:
  * `CourseController` (`show`, `edit`, `update`, `destroy`) does NOT check if the authenticated teacher owns the course (`course.teacher.user_id === auth.id`). Currently, any user with the `teacher` role can view, edit, update, or delete courses belonging to other teachers.
  * `CourseVideoController` (`create`, `store`, `edit`, `update`, `destroy`) does NOT check if the authenticated user owns the parent course.
* **Teacher/Owner Learning Access Trap**: `FrontController@learning` requires `$user->hasActiveSubscription()`. Teachers and Owners who lack an active paid `SubscribeTransaction` record are redirected to `/pricing` when attempting to preview learning pages. Intended behavior is that Teachers preview their own courses and Owners preview any course without a subscription.

---

## 6. Subscription and Learning Access Rules

* **Student Entitlement**: A Student has active learning access to platform courses if `User::hasActiveSubscription()` returns `true`. An active subscription grants platform-wide learning access to all courses.
* **Teacher Entitlement**: A Teacher may preview their own course content without requiring a Student subscription.
* **Owner Entitlement**: An Owner may preview all course content without requiring a Student subscription.
* **Subscription Mechanics**: `hasActiveSubscription()` checks for a `SubscribeTransaction` where `is_paid = true`, taking the latest by `updated_at`, and verifying that `subscription_start_date` + 1 month is greater than or equal to `Carbon::now()`.
* **Current Implementation Gap**: `FrontController@learning` currently applies `hasActiveSubscription()` universally across all roles, incorrectly blocking Teachers and Owners who lack a paid Student subscription.
* **Course Participation Sync**: Accessing a learning video (`FrontController@learning`) attaches the student to the course in `course_students` via `$user->courses()->syncWithoutDetaching($course->id)`.

---

## 7. Coding Conventions

* **Controller Pattern**: Standard RESTful resource controllers returning Blade views or redirects with flash keys (`'success'`, `'error'`).
* **Naming Conventions**:
  * Database tables and foreign keys: lower snake_case singular for foreign keys (`teacher_id`, `category_id`, `user_id`, `course_id`).
  * Routes: dot notation with `front.` prefix for public pages and `admin.` prefix for back-office pages.
* **Blade Organization**:
  * Admin layout: `<x-app-layout>` wrapping Blade components.
  * Front templates: Standalone HTML layout files in `resources/views/front/` referencing `asset('css/output.css')` and `asset('js/main.js')`.

---

## 8. Required Workflow Before Coding

Before executing any coding task, future agents **MUST**:

1. Read `AGENTS.md` (this file).
2. Read relevant sections of `docs/ARCHITECTURE.md`.
3. Inspect all files directly related to the task.
4. Inspect existing automated tests relevant to the feature.
5. Inspect related routes (`routes/web.php`), models (`app/Models`), requests (`app/Http/Requests`), and authorization logic.
6. Identify possible side effects on model relationships, cascade deletes, or role permissions.
7. Produce a concise implementation plan and present it to the user before initiating multi-file modifications.

---

## 9. Scope Discipline

Future agents must strictly adhere to the following scope boundaries:

* **File Targeting**: Modify only files strictly required to accomplish the stated task.
* **Refactoring Limits**: Avoid unrequested opportunistic refactoring, code reformatting, or style changes.
* **Behavior Preservation**: Preserve existing public routing, HTTP status codes, and user-facing behavior unless explicitly instructed to modify them.
* **Dependency Constraints**: Do not add, update, or remove packages in `composer.json` or `package.json` unless explicitly requested.
* **Schema Protection**: Do not create or alter database migrations unless task requirements specifically mandate schema changes.

---

## 10. Bug-Fix & Security Rules

* **Bug-Fix Rule**: Every bug fix must include a corresponding regression test in `tests/Feature` or `tests/Unit` verifying the fix.
* **Security Rule**: Never rely solely on role-level middleware (`middleware('role:...')`). Always enforce object-level / resource-level ownership authorization inside controllers or policies when handling protected resources.
* **Database Rule**: Before modifying model persistence logic, verify schema constraints in `database/migrations/`, including foreign keys, nullability, unique constraints, and soft-delete behaviors.

---

## 11. Engineering Principles

Future contributors and coding agents should optimize for:

1. **Best Practices**: Prefer framework-native solutions (FormRequests, Eloquent relationships, route model binding, Laravel Storage) before introducing custom abstractions or third-party packages.
2. **Security by Default**: Evaluate authentication, role authorization, resource-level ownership, mass assignment, CSRF, input validation, and file upload safety for all changes. Never weaken existing protections for convenience.
3. **Performance**: Avoid N+1 queries, unindexed queries on large tables, and unbounded result sets. Use eager loading and pagination where datasets grow. Do not add caching or queues without clear justification.
4. **Reusability**: Extract shared logic into Blade components, FormRequests, or model scopes only when logic is genuinely repeated or shared across components. Avoid premature abstraction for single-use code.
5. **Scalability**: Design query patterns, model relationships, and file storage to support reasonable project growth without designing for hypothetical unneeded scale.
6. **Clean Code**: Write small, focused methods with clear descriptive names and explicit control flow. Code readability takes precedence over clever tricks. Comments should explain *why*, not restate obvious code.
7. **UI / UX**: Maintain visual consistency with existing Blade layouts. Provide clear validation messages, empty states, and responsive behavior without redesigning unrelated screens.

---

## 12. Avoid Over-Engineering

Future agents must choose the simplest implementation that correctly satisfies current acceptance criteria, security requirements, and maintainability.

Use this decision order:
1. Can the requirement be solved cleanly with existing Laravel functionality?
2. Can existing project patterns solve it safely?
3. Is a small reusable abstraction genuinely useful?
4. Only then consider introducing a new architectural layer or dependency.

Do **NOT** introduce without explicit requirement or clear justification:
* Repository pattern on top of Eloquent
* Custom service classes for trivial CRUD operations
* DTO layers for simple internal forms
* Event-driven architecture or message queues for synchronous local workflows
* Caching or Redis infrastructure without measurable need
* Microservices, CQRS, or Event Sourcing
* Unnecessary third-party packages for native Laravel functionality

---

## 13. Architectural Decision Principle

When multiple valid implementations exist, prefer the solution that:
1. Matches existing repository patterns
2. Uses Laravel-native functionality
3. Minimizes new dependencies
4. Minimizes changed files
5. Is easiest to test
6. Preserves security boundaries
7. Is understandable by the next contributor
8. Leaves room for future extension without prematurely implementing it

---

## 14. Verified Environment Commands

The following commands are verified for local development:

```bash
# Installation
composer install
npm install
cp .env.example .env
php artisan key:generate

# Storage Link
php artisan storage:link

# Database Migration & Seed
php artisan migrate --seed

# Development Server (Run concurrently or in separate terminals)
php artisan serve
npm run dev

# Frontend Assets Build
npm run build

# Code Formatting (Laravel Pint)
./vendor/bin/pint

# Automated Testing
php artisan test
./vendor/bin/phpunit
```

---

## 15. Definition of Done

A task assigned to a coding agent is complete ONLY when:

1. Requested behavior is fully implemented according to acceptance criteria.
2. Object-level and role-level authorization implications have been evaluated and enforced.
3. FormRequest validation covers all new or modified input fields.
4. Relevant automated tests pass using `./vendor/bin/phpunit` or `php artisan test`.
5. Regression tests are added for any resolved bugs.
6. PHP code passes formatting via `./vendor/bin/pint`.
7. Unrelated workspace files remain unmodified.
8. Git diff is inspected to confirm clean changes.
9. Remaining risks, limitations, or open questions are reported to the user.
