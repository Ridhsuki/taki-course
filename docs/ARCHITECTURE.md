# System Architecture Documentation

This document describes the **current implementation** and **confirmed business intent** of the `taki-course` Learning Management System (LMS) repository.

---

## 1. System Overview

`taki-course` is a monolith Learning Management System built on **Laravel 11.31** and **PHP 8.2+**. It manages online video-based courses, multi-tier user roles (Owner, Teacher, Student), manual proof-of-payment subscription processing, and restricted course content streaming.

```
                  ┌─────────────────────────────────────────┐
                  │              HTTP Request               │
                  └────────────────────┬────────────────────┘
                                       │
                                       ▼
                  ┌─────────────────────────────────────────┐
                  │             routes/web.php              │
                  └────────────────────┬────────────────────┘
                                       │
            ┌──────────────────────────┴──────────────────────────┐
            ▼                                                     ▼
┌───────────────────────┐                             ┌───────────────────────┐
│ Public / Front Routes │                             │      Admin Routes     │
└───────────┬───────────┘                             └───────────┬───────────┘
            │                                                     │
            ▼                                                     ▼
┌───────────────────────┐                             ┌───────────────────────┐
│    FrontController    │                             │ Admin Controllers     │
└───────────┬───────────┘                             └───────────┬───────────┘
            │                                                     │
            ▼                                                     ▼
┌───────────────────────┐                             ┌───────────────────────┐
│ Blade Views (Front)   │                             │ Blade Components      │
│ (Custom CSS/JS & CDN) │                             │ (Tailwind via Vite)   │
└───────────────────────┘                             └───────────────────────┘
```

---

## 2. Main Domain Entities & Relationships

The application domain consists of eight primary Eloquent models.

### Textual Relationship Diagram

```
[ User ] (role: student)
  │
  ├──► 1:N ──► [ SubscribeTransaction ] (is_paid, subscription_start_date)
  │
  └──► M:N ──► [ CourseStudent ] (participation) ◄── M:N ── [ Course ]
                                                               │
[ User ] (role: teacher)                                       ├──► N:1 ──► [ Category ]
  │                                                            │
  └──► 1:1 ──► [ Teacher ] ──► 1:N ────────────────────────────┤
                                                               ├──► 1:N ──► [ CourseVideo ]
                                                               │
                                                               └──► 1:N ──► [ CourseKeypoint ]
```

### Entity Specifications

1. **User** (`app/Models/User.php`)
   * **Database Table**: `users`
   * **Attributes**: `id`, `name`, `email`, `occupation`, `avatar`, `password`, `email_verified_at`, timestamps.
   * **Relationships**:
     * `courses()`: `belongsToMany(Course::class, 'course_students')`
     * `subscribe_transactions()`: `hasMany(SubscribeTransaction::class)`
     * `roles`: Spatie `HasRoles` trait (`owner`, `teacher`, `student`).
   * **Key Method**: `hasActiveSubscription()` returns boolean based on paid transaction within 1 month.
   * **Profile Management**: Profile editing (`name`, `email`, `password`, `occupation`, `avatar`) is supported for all authenticated roles via [`ProfileController.php`](/app/Http/Controllers/ProfileController.php).

2. **Teacher** (`app/Models/Teacher.php`)
   * **Database Table**: `teachers` (SoftDeletes enabled on model and migration)
   * **Attributes**: `id`, `user_id`, `is_active`, soft deletes, timestamps.
   * **Relationships**:
     * `user()`: `belongsTo(User::class)`
     * `courses()`: `hasMany(Course::class)`
   * *Note*: Model `User` does not currently define a reciprocal `teacher()` relationship.

3. **Category** (`app/Models/Category.php`)
   * **Database Table**: `categories` (SoftDeletes enabled on model and migration)
   * **Attributes**: `id`, `name`, `slug`, `icon`, soft deletes, timestamps.
   * **Route Key**: Overridden `getRouteKeyName()` returns `'slug'`.
   * **Relationships**:
     * `courses()`: `hasMany(Course::class)`

4. **Course** (`app/Models/Course.php`)
   * **Database Table**: `courses` (SoftDeletes enabled on model and migration)
   * **Attributes**: `id`, `name`, `slug`, `path_trailer`, `about`, `thumbnail`, `teacher_id`, `category_id`, soft deletes, timestamps.
   * **Relationships**:
     * `category()`: `belongsTo(Category::class)`
     * `teacher()`: `belongsTo(Teacher::class)`
     * `course_videos()`: `hasMany(CourseVideo::class)`
     * `course_keypoints()`: `hasMany(CourseKeypoint::class)`
     * `students()`: `belongsToMany(User::class, 'course_students')`

5. **CourseVideo** (`app/Models/CourseVideo.php`)
   * **Database Table**: `course_videos` (SoftDeletes enabled on model and migration)
   * **Attributes**: `id`, `name`, `path_video`, `course_id`, soft deletes, timestamps.
   * **Relationships**:
     * `course()`: `belongsTo(Course::class)`

6. **CourseKeypoint** (`app/Models/CourseKeypoint.php`)
   * **Database Table**: `course_keypoints` (SoftDeletes enabled on model and migration)
   * **Attributes**: `id`, `name`, `course_id`, soft deletes, timestamps.
   * **Relationships**:
     * `course()`: `belongsTo(Course::class)`

7. **CourseStudent** (`app/Models/CourseStudent.php`)
   * **Database Table**: `course_students` (`deleted_at` column added in migration `$table->softDeletes()`)
   * **Attributes**: `id`, `user_id`, `course_id`, timestamps.
   * **Semantics**: Represents course participation/enrollment when a Student accesses a valid course. It is **NOT** the entitlement source for subscription authorization (`SubscribeTransaction` is the sole source of platform access).
   * *Soft Delete Note*: The `CourseStudent` Eloquent model does **NOT** import or use the `SoftDeletes` trait. Trait alignment is **DEFERRED** technical debt pending future access model redesign.

8. **SubscribeTransaction** (`app/Models/SubscribeTransaction.php`)
   * **Database Table**: `subscribe_transactions` (SoftDeletes enabled on model and migration)
   * **Attributes**: `id`, `total_amount`, `is_paid`, `subscription_start_date`, `proof`, `user_id`, soft deletes, timestamps.
   * **Relationships**:
     * `user()`: `belongsTo(User::class)`

---

## 3. Role Model & Access Intent

Roles are managed via Spatie Laravel Permission (`RolePermissionSeeder.php`).

| Role | Intended Business Scope | Implemented Route Guards | Current Implementation Gap |
| :--- | :--- | :--- | :--- |
| **owner** | System Administrator. Global platform management (Categories, Teachers, all Courses, Course Videos, Transaction approvals). Can preview any course content without a subscription. Edit own profile. | `middleware('role:owner')` on categories, teachers, subscribe_transactions. `role:owner\|teacher` on courses & videos. | `CourseController@store` fails if Owner lacks a `Teacher` record. `FrontController@learning` requires active subscription. |
| **teacher** | Instructor. Manage only assigned Courses & Videos. Preview own course content without a subscription. Edit own profile. Must never access other teachers' courses. | `middleware('role:owner\|teacher')` on courses and course_videos. | Missing object-level ownership check (`course.teacher.user_id === auth.id`). `FrontController@learning` requires active subscription. |
| **student** | Learner. Catalog browsing, manual payment submission, platform-wide course learning access upon approval. Edit own profile. | `middleware('role:student')` on checkout. `role:student\|teacher\|owner` on learning page. | No role-scope mismatch identified; learning-flow reliability gaps are documented in Section 9. |

---

## 4. Request Lifecycle

```
[ Client Request ]
       │
       ▼
[ routes/web.php ] ──► Route Matching & Parameter Binding (id / slug)
       │
       ▼
[ Middleware Pipeline ] ──► auth ──► role:{role_name}
       │
       ▼
[ FormRequest Validation ] ──► authorize() ──► rules()
       │
       ▼
[ Controller Action ] ──► Atomic Write / DB Transaction ──► Eloquent Operations
       │
       ▼
[ View Rendering ] ──► Blade Component / Page Layout
```

---

## 5. Key Application Flows & Invariants

### 5.1 Authentication & Profile Flow
* Handled by Laravel Breeze in `routes/auth.php` and `app/Http/Controllers/Auth/`.
* `RegisteredUserController@store`:
  * Validates `name`, `occupation`, `avatar`, `email`, `password`.
  * Uploads avatar to `storage/app/public/avatars`.
  * Creates `User` model.
  * Assigns default role `$user->assignRole('student')`.
  * Logs user in and redirects to `/dashboard`.
* Profile Editing: Handled by `ProfileController` (`edit`, `update`, `destroy`), accessible to all authenticated roles.

### 5.2 Authorization Flow & Findings

```
                            ┌─────────────────────────────────┐
                            │      Route Middleware Check     │
                            │      e.g. role:owner|teacher    │
                            └────────────────┬────────────────┘
                                             │ Passes
                                             ▼
                            ┌─────────────────────────────────┐
                            │     Controller Action Executed  │
                            └────────────────┬────────────────┘
                                             │
                       ┌─────────────────────┴─────────────────────┐
                       ▼                                           ▼
          ┌──────────────────────────┐                ┌──────────────────────────┐
          │  Category / Teacher      │                │  Course / Video CRUD     │
          │  (Role Check Sufficient) │                │  (MISSING Ownership      │
          └──────────────────────────┘                │   Check vs Auth User)    │
                                                      └──────────────────────────┘
```

* **Missing Resource-Level Ownership Enforcement**:
  * In `CourseController@edit`, `update`, `destroy`, and `show`, any user with role `teacher` can access and modify any `Course`, regardless of whether `course.teacher.user_id` matches the authenticated user's ID. Intended behavior is strict ownership isolation per teacher.
  * In `CourseVideoController@create`, `store`, `edit`, `update`, `destroy`, no check is made verifying that the video's parent course belongs to the authenticated teacher.

### 5.3 Course Management Flow
* Handled by `CourseController`.
* Index (`CourseController@index`):
  * Teachers see only courses matching their `teacher_id` (`whereHas('teacher', ...)`).
  * Owners see all courses.
* Store (`CourseController@store`):
  * Resolves `$teacher = Teacher::where('user_id', Auth::user()->id)->first()`.
  * *Implementation Gap*: Although `owner` has route-level access to `/admin/courses`, if an `owner` attempts to create a course without having a record in `teachers` table, creation fails with `"Unauthorized or invalid teacher."`. Intended behavior is that Owner can assign a Course to an existing Teacher or manage Courses directly.

### 5.4 Subscription & Checkout Flow
1. Student accesses `/checkout` (`FrontController@checkout`).
2. Student uploads payment proof image via `/checkout/store` (`FrontController@checkout_store`).
3. System creates `SubscribeTransaction` record with `total_amount = 429000` and `is_paid = false`.
4. Owner views transactions at `/admin/subscribe_transactions` (`SubscribeTransactionController@index`).
5. Owner approves payment (`SubscribeTransactionController@update`), setting `is_paid = true` and `subscription_start_date = Carbon::now()`.
6. **Entitlement Rule**: An approved paid `SubscribeTransaction` grants platform-wide entitlement to access all courses. Payment approval does NOT create `CourseStudent` rows for every course.

### 5.5 Learning Access Flow & Entitlement Semantics
* Accessing `/learning/{course}/{courseVideoId}` (`FrontController@learning`):
  * Middleware check: `role:student|teacher|owner`.
  * Access check: `if (!$user->hasActiveSubscription()) return redirect()->route('front.pricing');`.
  * *Implementation Gap*: Teachers and Owners without an active paid subscription record are blocked from viewing learning content. Intended behavior is that Teachers preview their own courses and Owners preview any course without a subscription.
  * **CourseStudent Participation Sync**: Executing `learning()` syncs the student to `course_students` via `$user->courses()->syncWithoutDetaching($course->id)`. This records course participation upon viewing a valid course.

---

## 6. Frontend Architecture

```text
Public frontend
    ↓
resources/views/layouts/front.blade.php
    ↓
shared Blade components
    ↓
resources/css/app.css
    ↓
Tailwind CSS + Laravel Vite
    ↓
public/build/assets/*
```

* **Public Frontend**: Public pages render through the shared Blade layout (`resources/views/layouts/front.blade.php`), using reusable navbar (`<x-front.navbar />`) and footer (`<x-front.footer />`) Blade components.
* **Styling & Asset Bundling**: Tailwind CSS is compiled through Vite via `@vite(['resources/css/app.css', 'resources/js/app.js'])` into `public/build/assets/*`.
* **Legacy & Page-Specific Assets**:
  * `public/js/main.js` remains a legacy/custom JavaScript asset intentionally loaded by the public layout (accordion, tab switcher, file upload label update).
  * Vendor libraries (jQuery 3.7.1, Flickity slider, Fancybox UI, Plyr video player) remain page-specific where applicable via CDN.
* **Admin / Dashboard Pipeline**: Admin views use `<x-app-layout>` (`resources/views/layouts/app.blade.php`), Alpine.js, and shared Vite assets.

---

## 7. Testing Architecture & Gaps

* **Framework**: PHPUnit 11 configured in `phpunit.xml`.
* **Database Connection**: Configured to run in-memory SQLite (`<env name="DB_CONNECTION" value="sqlite"/>`, `<env name="DB_DATABASE" value=":memory:"/>`).
* **Test Infrastructure Status**: **Healthy / Green Baseline**. Test infrastructure foundation defects (missing `occupation` in `UserFactory`, missing `RefreshDatabase` trait in `ExampleTest`, and stale inputs in `RegistrationTest`) have been resolved.

### Test Coverage Gaps

* **Domain Feature Coverage Gap**: While existing Breeze authentication and profile tests pass cleanly, minimal or zero automated feature test coverage currently exists for core business functionality:
  * `CategoryController` (CRUD operations and slug key binding)
  * `CourseController` (course management and teacher assignment)
  * `CourseVideoController` (video management per course)
  * `TeacherController` (teacher status management)
  * `SubscribeTransactionController` (transaction review and approval)
  * `FrontController` (public catalog, pricing, checkout proof submission, and course learning access)

---

## 8. Infrastructure & CI/CD

* **Workflows**: No GitHub Actions workflows exist (`.github/workflows` does not exist in repository).
* **Local Development Execution**: Application is run locally via `php artisan serve` and `npm run dev` (or `composer dev` using `concurrently`).

---

## 9. Known Technical Debt

### P0 (Security & Data Integrity Concerns)
* **P0-1: Missing Resource Ownership Enforcement in Course Controller**: `CourseController@edit`, `@update`, and `@destroy` do not check if `$course->teacher->user_id === Auth::id()`. Any teacher can edit or delete another teacher's course.
* **P0-2: Missing Resource Ownership Enforcement in Course Video Controller**: `CourseVideoController@create`, `@store`, `@edit`, `@update`, and `@destroy` do not verify course ownership.
* **P0-3: Non-existent Video Handling**: `FrontController@learning` searches for video via `$course->course_videos->firstWhere('id', $courseVideoId)`. If not found, it passes `$video = null` to the view without throwing a 404 HTTP exception.

### P1 (Reliability & Maintainability Debt)
* **P1-1: Core Domain Automated Test Coverage Gap**: Automated test suite baseline is green, but core domain controllers (`CategoryController`, `CourseController`, `CourseVideoController`, `TeacherController`, `SubscribeTransactionController`, `FrontController`) lack automated regression test coverage.
* **P1-2: Owner Cannot Create Courses**: `CourseController@store` assumes every user creating a course has a record in `teachers` table. Owners without a `Teacher` record are rejected with a session error.
* **P1-3: Teacher/Owner Learning Block**: Teachers and Owners cannot preview learning pages unless they purchase a student subscription.
* **P1-4: Hardcoded Subscription Price**: `FrontController@checkout_store` hardcodes `$validated['total_amount'] = 429000` while `StudentSubscriptionSeeder` seeds `150000`.
* **P1-5: Unconditional Student Pivot Syncing**: Accessing `/learning/{course}/{courseVideoId}` triggers pivot insertion even if the requested video ID is invalid.
* **P1-6: CourseStudent SoftDeletes Trait Alignment (DEFERRED)**: `course_students` migration defines `deleted_at`, but `CourseStudent` model does not use `SoftDeletes`. Alignment is deferred until future access model refactoring.

### P2 (Developer Experience & Cleanup)
* **P2-1: CDN Dependency Inconsistency**: Front views rely on external CDNs (jQuery, Flickity) while admin views use Vite bundler.
* **P2-2: Missing GitHub CI Workflows**: No automated linting or test runs configured on pull requests.
* **P2-3: Misleading Empty Controllers**: `CourseKeypointController` and `CourseStudentController` contain empty resource methods.

---

## 10. Future Product Direction (Non-Binding)

> [!NOTE]
> **THIS IS NOT CURRENT APPLICATION BEHAVIOR.**
>
> The information in this section describes a planned future product iteration. Future coding agents MUST NOT implement, pre-optimize, alter database schemas, or add abstractions for this model unless a task explicitly requests it. Follow YAGNI (You Aren't Gonna Need It).

A future product iteration is planned to transition the current platform-wide subscription model into **Course-specific purchasing/subscriptions**.

Potential future flow:
1. Student selects a specific Course.
2. Student submits payment proof for that specific Course.
3. Owner reviews and approves the Course-specific payment.
4. Student receives access only to the purchased Course.

This future model would likely require updates to payment schema records, course access relationships, authorization policies, and learning access checks.

---

## 11. Open Questions

1. **Canonical Subscription Pricing**: What is the single source of truth for the subscription price (429,000 IDR in `FrontController@checkout_store` vs 150,000 IDR in `StudentSubscriptionSeeder`)?
