# AI Coding Agent Instructions for HRMS System

## Project Overview
This is a **Laravel 12 HRMS (Human Resource Management System)** built with PHP 8.2+. It uses:
- **Framework**: Laravel 12 with Blade templating
- **Frontend**: Vite + Tailwind CSS 4.0
- **Database**: SQLite (default) or MySQL (configurable)
- **Testing**: PHPUnit with Feature/Unit test split
- **Build**: Composer (PHP), npm (JavaScript)

## Critical Setup Commands
Run these in order for first-time setup:
```bash
composer run setup    # Installs dependencies, generates .env, runs migrations, builds assets
composer run dev      # Starts all services: Laravel server, queue listener, logging, Vite dev server
composer run test     # Runs full test suite with config clear
```

## Architecture & Key Patterns

### Directory Structure
- **`app/Models/`** - Eloquent ORM models using Laravel conventions (User.php exists)
- **`app/Http/Controllers/`** - Request handlers; extend abstract `Controller` class
- **`routes/web.php`** - Web route definitions; currently minimal (welcome view only)
- **`database/migrations/`** - Schema versions; use Laravel's `Schema::create()` pattern
- **`database/seeders/`** - Database seeders (DatabaseSeeder.php provided)
- **`resources/views/`** - Blade templates (.blade.php); welcome.blade.php exists
- **`resources/css/app.css` & `resources/js/app.js`** - Vite entry points with Tailwind
- **`config/`** - Application configuration (app.php, database.php, auth.php, etc.)
- **`storage/logs/`** - Application logs

### Database & Models
- Default connection: **SQLite** at `database/database.sqlite` (env: `DB_CONNECTION=sqlite`)
- Models use **Eloquent ORM** with traits: `HasFactory` (testing), `Notifiable` (notifications)
- **Mass assignment**: Use `$fillable` array (e.g., User: name, email, password)
- **Casting**: Always cast sensitive data (e.g., `'password' => 'hashed'` auto-hashes in User)
- **Timestamps**: Automatically managed; migrations use `$table->timestamps()`

### Controllers & Routing
- All controllers inherit from `app/Http/Controllers/Controller`
- Routes defined in `routes/web.php`; use Route facades (Route::get(), Route::post(), etc.)
- No API routes currently; expand as needed in `routes/api.php` if building REST endpoints

### Frontend & Assets
- **Vite** serves assets in dev (`npm run dev`) and builds for production (`npm run build`)
- **Tailwind CSS 4.0** via `@tailwindcss/vite` plugin; configure in `vite.config.js`
- **axios** included for AJAX (imported in `resources/js/bootstrap.js`)
- Compiled assets go to `public/build/` (git-ignored); reference via Blade: `@vite(['resources/css/app.css', 'resources/js/app.js'])`

### Testing Structure
- **PHPUnit** with two test suites:
  - **`tests/Unit/`** - Unit tests (isolated logic)
  - **`tests/Feature/`** - Feature tests (HTTP requests, full stack)
- Test database: **in-memory SQLite** (configured in phpunit.xml)
- Base class: `Tests\TestCase` extends `Illuminate\Foundation\Testing\TestCase`
- Example files: `ExampleTest.php` in both suites

## Developer Workflows

### Adding a New Feature
1. **Create migration**: `php artisan make:migration create_[table]_table`
2. **Create model**: `php artisan make:model [Model]` (includes Model + migration option)
3. **Create controller**: `php artisan make:controller [ControllerName]`
4. **Define routes**: Add to `routes/web.php` or `routes/api.php`
5. **Create tests**: Add Feature tests in `tests/Feature/` for HTTP endpoints
6. **Build assets**: Run `npm run build` before deployment

### Running Tests
```bash
composer run test        # Full suite with config clear
php artisan test         # Quick run without config clear
php artisan test --filter=FeatureTestName  # Run specific test
```

### Database Operations
```bash
php artisan migrate              # Run pending migrations
php artisan migrate:fresh        # Reset and re-run all migrations
php artisan migrate:rollback     # Undo last batch
php artisan db:seed              # Run DatabaseSeeder
php artisan tinker               # Interactive shell (test code)
```

### Development Server
```bash
php artisan serve          # Start web server (port 8000)
php artisan queue:listen   # Process queued jobs
php artisan pail           # Stream logs in real-time
npm run dev                # Vite dev server (port 5173, auto-refresh)
```
Or use `composer run dev` to run all four concurrently.

## Project-Specific Conventions

### Naming & Organization
- **Model names**: Singular, PascalCase (User, Employee, Department)
- **Table names**: Plural, snake_case (users, employees, departments)
- **Migration names**: Descriptive, versioned: `0001_01_01_000000_create_users_table.php`
- **Controllers**: Singular resource name + "Controller" (UserController, EmployeeController)
- **Views**: Lowercase, dot-notation for nested folders (e.g., `employees.show` → `resources/views/employees/show.blade.php`)

### Environment Configuration
- **`.env` file required** (copy from `.env.example` during setup)
- Key env vars: `APP_NAME`, `APP_KEY`, `DB_CONNECTION`, `DB_DATABASE`, `MAIL_MAILER`, `QUEUE_CONNECTION`
- Use `config()` helper to access: `config('app.name')` not `env('APP_NAME')` directly in code

### Service Provider Pattern
- **AppServiceProvider** (`app/Providers/AppServiceProvider.php`) bootstraps application services
- Register custom bindings in `register()`, boot services in `boot()` method
- Providers auto-discovered in `bootstrap/providers.php`

## Integration Points & External Services

### Mail
- Configured in `config/mail.php`; currently uses array driver (logged, not sent)
- To use real mail: set `MAIL_MAILER` in `.env` (mailtrap, smtp, ses, etc.)

### Authentication
- **Guard**: Laravel's built-in session guard configured in `config/auth.php`
- **Middleware**: Use `auth` middleware to protect routes
- User model: `App\Models\User` with email uniqueness constraint

### Database Migrations
- Versioned format ensures consistent execution order
- Foreign key constraints enabled by default; manage in migrations with `->foreign('user_id')`

### Asset Pipeline
- **Vite HMR** enabled for hot-module replacement in dev
- Production build: `npm run build` outputs to `public/build/`
- Cache-busting automatic; always reference via Vite manifest in Blade

## Code Examples

### Creating a Resource Controller & Route
```php
// routes/web.php
use App\Http\Controllers\EmployeeController;
Route::resource('employees', EmployeeController::class);

// app/Http/Controllers/EmployeeController.php
namespace App\Http\Controllers;
use App\Models\Employee;
class EmployeeController extends Controller {
    public function index() { return view('employees.index', ['employees' => Employee::all()]); }
    public function show(Employee $employee) { return view('employees.show', compact('employee')); }
}
```

### Writing a Feature Test
```php
// tests/Feature/EmployeeTest.php
namespace Tests\Feature;
class EmployeeTest extends TestCase {
    public function test_can_list_employees() {
        $response = $this->get('/employees');
        $response->assertStatus(200);
    }
}
```

### Database Seeders
```php
// database/seeders/DatabaseSeeder.php
public function run(): void {
    User::factory(10)->create();
    User::factory()->create(['email' => 'test@example.com']);
}
```

## Git & CI/CD
- GitHub Actions workflows in `.github/workflows/`
- Branch strategy: Develop on feature branches, PR to main
- Tests run automatically on PR; ensure all pass before merge
