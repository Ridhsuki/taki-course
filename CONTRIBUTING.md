# Contributing to Taki Course

Thank you for contributing to Taki Course! This guide outlines recommended practices for both **human contributors** and **AI-assisted contributors** to ensure safe, consistent, and maintainable changes.

---

## 1. Local Development Setup

To set up the project locally:

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/Ridhsuki/taki-course.git
   cd taki-course
   ```

2. **Install Dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database & Storage Setup**:
   * Configure database connection parameters in `.env`.
   * Run migrations and seeders:
     ```bash
     php artisan migrate --seed
     ```
   * Create storage symlink for uploaded media:
     ```bash
     php artisan storage:link
     ```

5. **Start Local Development**:
   ```bash
   # Option A: Run services concurrently
   composer dev

   # Option B: Run in separate terminal sessions
   php artisan serve
   npm run dev
   ```

---

## 2. Branch & Commit Conventions

### Branch Expectations
Based on repository history, recommended branch naming patterns include:
* `feature/<short-description>` or `feat/<short-description>` (e.g., `feature/dynamic-front-categories`)
* `fix/<short-description>`
* `chore/<short-description>`

### Commit Conventions
Repository history largely follows **Conventional Commits-style** prefixes:
* `feat(scope)`: New feature implementation (e.g., `feat(checkout): Fitur Checkout Subscription`)
* `fix(scope)`: Bug fixes (e.g., `fix(categories): Correct category slug binding`)
* `chore(scope)`: Maintenance, dependencies, or configuration (e.g., `chore: custom login/register pages`)
* `refactor(scope)`: Code refactoring without behavioral changes (e.g., `refactor(navbar): Merapihkan Navbar Routing`)
* `docs(scope)`: Documentation updates

---

## 3. Issue & PR Workflow

1. **Check Existing Issues & PRs**: Before working on a task, check open GitHub issues and recently merged PRs to avoid duplicating efforts. Note that some open issues may describe features already merged in recent commits.
2. **Pull Request Submissions**: Create a pull request targeting `main`.
3. **PR Description Checklist**: Every pull request should include a completed checklist:

```markdown
## Implementation Summary
- [ ] Clear description of changes made

## Task Reference
- Related issue: #<issue_number> (or N/A, or Closes #<issue_number>)

## Quality Checklist
- [ ] Verified object-level and role-level authorization impacts
- [ ] Added or updated automated tests where applicable
- [ ] Executed `./vendor/bin/phpunit` or `php artisan test` and verified passing tests
- [ ] Executed `./vendor/bin/pint` for PHP code formatting
- [ ] Tested UI changes across desktop and mobile breakpoints (attached screenshots/recordings)
- [ ] Verified backward compatibility and absence of unintended side effects
- [ ] Preserved existing database schema unless explicitly required
```

---

## 4. Scope Discipline & AI Contributor Rules

To keep pull requests clean, predictable, and easy to review:

* **Minimal Diffs**: Touch only files necessary for the assigned task.
* **No Unrelated Refactorings**: Avoid reformatting surrounding code, altering whitespace, or updating unrelated functions in files you touch.
* **Preserve Public Contracts**: Do not change public route URIs, controller action parameters, or model method signatures unless requested.
* **Dependency Stability**: Avoid adding new packages or updating `composer.lock` / `package-lock.json` unless mandatory for the task.
* **Database Caution**: Always inspect foreign key cascades, soft deletes, and nullability constraints in `database/migrations/` before modifying persistence operations.
* **UI & Restyling Guardrails**: Pull requests that modify or restyle UI/UX must adhere to project direction in `DESIGN.md` and quality standards in `ANTISLOP.md`.

---

## 5. Security & Privacy Guidelines

* **Never Commit Secrets**: Ensure `.env`, secret keys, AWS credentials, or tokens are never committed to git.
* **Object-Level Authorization**: Always enforce object ownership checks (e.g., verifying `$course->teacher->user_id === Auth::id()`) inside controllers or policies, in addition to middleware role checks.
* **Validation**: Validate all incoming input parameters through dedicated `FormRequest` classes in `app/Http/Requests`.

---

## 6. Testing & Formatting Requirements

* **Code Formatting**: Format PHP code using Laravel Pint before opening a PR:
  ```bash
  ./vendor/bin/pint
  ```
* **Running Tests**: Execute PHPUnit tests using:
  ```bash
  php artisan test
  # or
  ./vendor/bin/phpunit
  ```
* **Testing Note**: If writing factory-based tests, ensure `occupation` is supplied when creating `User` instances (or update `UserFactory`) to comply with database constraints.
