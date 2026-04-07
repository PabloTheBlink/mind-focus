# Backend Refactoring Analysis

> Generated on: martes, 7 de abril de 2026
> Project: mind-focus (Laravel 13 + Inertia Svelte)

## Summary

The codebase is relatively small and well-structured for a Laravel application. It follows many Laravel best practices: FormRequest validation, service layer separation, traits for reusable validation rules, and proper middleware usage. However, there are several areas worth addressing — particularly around the `QwenMindFocusService` complexity, route exposure of the AI service, and a few architectural inconsistencies.

---

## Critical Issues (Fix Now)

| File | Issue | Severity | Recommendation |
|------|-------|----------|----------------|
| `app/Http/Controllers/MindFocusController.php` | **No auth middleware on structure endpoints** — `POST /app/structure` and `POST /api/structure` are publicly accessible, allowing anyone to trigger expensive Qwen CLI calls. | 🔴 Critical | Add `'auth'` or `'throttle'` middleware to these routes. At minimum, add rate limiting heavier than the default. |
| `app/Services/QwenMindFocusService.php:73-102` | **Process execution with user input** — While `escapeshellarg()` is used, the service executes a CLI binary with user-supplied text. If the binary path or behavior changes, this could be risky. | 🔴 Critical | Add input length validation (already in FormRequest — good), but also consider adding a whitelist of allowed characters or sanitization layer. Log all invocations for auditability. |
| `app/Http/Controllers/Settings/SecurityController.php:54` | **Password saved without hashing awareness** — `$request->user()->update(['password' => $request->password])` relies on the User model's `password` cast being `'hashed'`, which is correct but not immediately obvious to a reader. | 🔴 Critical | Add a comment or use `$request->user()->forceFill(['password' => $request->password])->save()` for clarity. The current approach works due to the `#[Hidden]` and cast, but could be more explicit. |

---

## High Priority

| File | Issue | Severity | Recommendation |
|------|-------|----------|----------------|
| `app/Services/QwenMindFocusService.php` | **God class — 280+ lines** doing CLI execution, JSON parsing, markdown generation, fallback logic, and data normalization. Single responsibility violation. | 🟡 High | Extract into: (1) `QwenClient` for CLI execution, (2) `ResponseParser` for JSON parsing/normalization, (3) `MarkdownGenerator` for markdown output. |
| `app/Services/QwenMindFocusService.php:105-187` | **`parseResponse()` is 80+ lines** with deep nesting (3-4 levels) handling JSON extraction, field normalization, icon validation, and primary item logic. | 🟡 High | Extract methods: `extractJson()`, `normalizeGroups()`, `validateIcon()`, `ensurePrimaryItems()`. Each should be its own private method. |
| `app/Services/QwenMindFocusService.php:192-230` | **`generateMarkdown()` duplicates logic** — the task vs note rendering branches are nearly identical (loop items, build line, append description). | 🟡 High | Extract a `renderItem(array $item, string $type): string` method to eliminate duplication between the task and note branches. |
| `routes/web.php:11-12` | **Two endpoints doing the same thing** — `/app/structure` returns Inertia response, `/api/structure` returns JSON. Both call the same service with the same validation. | 🟡 High | Consider if both are needed. If the API endpoint is for external consumers, move it to `routes/api.php` and apply API-specific middleware (token auth, stricter rate limits). |
| `app/Services/QwenMindFocusService.php:238-273` | **`fallbackStructure()` marks first item as `'urgente'`** arbitrarily. This is a magic business rule hardcoded in a service method. | 🟡 High | Extract the priority assignment logic into a constant or config value. Consider whether "first item = urgent" is the right heuristic — it may produce noise. |

---

## Medium Priority

| File | Issue | Severity | Recommendation |
|------|-------|----------|----------------|
| `app/Http/Controllers/MindFocusController.php:16-25` | **Method could be clearer** — the destructuring of `$result` keys is manual and repetitive. | 🟠 Medium | Use `['markdown' => $markdown, 'groups' => $groups] = $mindFocusService->structure($text);` for clarity, or rename the variable to `$structureResult`. ✅ Done — renamed to `$structureResult`. |
| `app/Http/Controllers/Settings/SecurityController.php:20-30` | **`middleware()` method has early returns** that make it unclear when password confirmation is actually required. The logic reads bottom-up. | 🟠 Medium | Flip the condition: `if (! Features::canManageTwoFactorAuthentication() || ! Features::optionEnabled(...)) { return []; }` and then `return [new Middleware(...)]`. |
| `app/Concerns/ProfileValidationRules.php:36-41` | **`emailRules()` has inline conditional** for the unique rule that's hard to read at a glance. | 🟠 Medium | Extract to `uniqueEmailRule(?int $userId): Rule` for clarity. |
| `app/Services/QwenMindFocusService.php:14-19` | **`ALLOWED_ICONS` array** is hardcoded. If icons are used elsewhere in the app (likely, in the Svelte frontend), this should be a config or constant shared between frontend/backend. | 🟠 Medium | Move to `config('mindfocus.allowed_icons')` or a dedicated `App\Enums\Icon` enum. |
| `app/Http/Middleware/HandleInertiaRequests.php:35` | **Sidebar state read from cookie** with magic string `'true'` and cookie name `'sidebar_state'`. | 🟠 Medium | Extract cookie name to a constant. Use `filter_var($value, FILTER_VALIDATE_BOOLEAN)` instead of string comparison. |
| `routes/settings.php:17` | **Password update route named `user-password.update`** — inconsistent with other route naming (`profile.edit`, `profile.update`, `security.edit`). | 🟠 Medium | Rename to `security.password.update` or `password.update` for consistency. |
| `app/Http/Controllers/MindFocusController.php` | **No tests exist** for the main application feature (text structuring via Qwen). Only scaffold tests exist. | 🟠 Medium | Add feature tests for `structure()` and `structureApi()` endpoints, and unit tests for `QwenMindFocusService::parseResponse()` with mocked CLI responses. |

---

## Low Priority / Nice to Have

| File | Issue | Severity | Recommendation |
|------|-------|----------|----------------|
| `app/Services/QwenMindFocusService.php:14-19` | **`ALLOWED_ICONS` not exhaustive** — missing common icons like `flag`, `target`, `calendar`, `tag`. | 🟢 Low | Review if the icon list matches what the frontend actually uses. |
| `app/Http/Controllers/Controller.php` | **Empty base controller** — no shared behavior. Could be removed in favor of direct controller classes. | 🟢 Low | This is a Laravel convention, so it's fine to keep, but note that it adds no value currently. |
| `config/services.php` | **Qwen config has timeout cast** `(int) env('QWEN_TIMEOUT', 120)` — the cast is redundant since `env()` returns string and `(int)` on a non-numeric string returns `0`. | 🟢 Low | Use `env('QWEN_TIMEOUT', 120)` and cast in the service constructor, or use `config('services.qwen.timeout', 120)` with a default. |
| `lang/es/mindfocus.php` | **Validation messages in Spanish** but the app's Qwen service responses (system prompt, fallback text) are also in Spanish. Consider if the service should be locale-aware. | 🟢 Low | Inject `app()->getLocale()` into the service and load locale-specific prompts/fallbacks. |
| `app/Models/User.php` | ~~**Commented-out `MustVerifyEmail`**~~ | 🟢 Low | ✅ Done — removed the commented import. |

---

## Architecture Observations

1. **Service Layer is Thin but Growing**: The `QwenMindFocusService` is the only custom service, and it's doing too much. As features grow, consider a proper service layer pattern with interfaces for testability.

2. **No Repository or Data Mapper Layer**: Currently, the app uses Eloquent directly, which is fine for this scale. If complex queries emerge, consider repository pattern.

3. **Fortify Integration is Clean**: The Fortify setup follows Laravel conventions with custom Actions, proper middleware, and Inertia views. No issues here.

4. **Missing API Versioning**: If `/api/structure` is intended for external consumption, consider API versioning (`/api/v1/structure`) and token-based authentication (Laravel Sanctum).

5. **No Event/Listener Pattern**: The app doesn't use Laravel's event system. If features like "send notification when structuring completes" or "log usage analytics" are added, events would be the right approach.

6. **Cookie-Based State**: Sidebar state and appearance are stored in cookies. For a logged-in app, consider storing user preferences in the database instead.

---

## Quick Wins

These take <5 minutes each:

1. **Add auth middleware to structure routes** in `routes/web.php`:
   ```php
   Route::middleware(['auth'])->group(function () {
       Route::post('app/structure', [MindFocusController::class, 'structure'])->name('app.structure');
       Route::post('api/structure', [MindFocusController::class, 'structureApi'])->name('api.structure');
   });
   ```

2. **Fix route naming consistency**: Rename `user-password.update` to `password.update`.

3. ~~**Remove commented `MustVerifyEmail`** from `User.php`.~~ ✅ Done

4. **Add PHPDoc to `SecurityController::update()`** clarifying that the password cast handles hashing via the model attribute.

5. **Run `vendor/bin/pint`** to ensure all PHP files match the project's code style.

---

## Stats

- **Files analyzed**: 20 PHP files (excluding config, migrations, and seeders)
- **Issues found**: 21 (Critical: 3, High: 6, Medium: 7, Low: 5)
- **Top files to refactor**:
  1. `app/Services/QwenMindFocusService.php` — 10 issues (complexity, duplication, security)
  2. `app/Http/Controllers/MindFocusController.php` — 3 issues (auth, naming, tests)
  3. `app/Http/Controllers/Settings/SecurityController.php` — 2 issues (middleware clarity, password update)
  4. `routes/web.php` — 2 issues (auth middleware, route organization)
  5. `app/Concerns/ProfileValidationRules.php` — 1 issue (readability)
