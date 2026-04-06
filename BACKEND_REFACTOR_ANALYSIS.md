# Backend Refactoring Analysis - Mind Focus

**Date:** 6 April 2026  
**Project:** mind-focus (Laravel 13 + Inertia.js + Svelte 5)

---

## Executive Summary

The backend codebase is relatively small and well-structured, but several refactoring opportunities exist ranging from critical configuration issues to minor code style improvements. The most significant concerns are in `QwenMindFocusService`, which handles external AI CLI integration.

---

## Priority Matrix

| Priority | File | Issue | Effort | Status |
|----------|------|-------|--------|--------|
| **High** | `app/Services/QwenMindFocusService.php` | Move `env()` call to config; extract system prompt | Medium | ✅ Done |
| **High** | `app/Services/QwenMindFocusService.php` | Refactor `parseResponse()` -- reduce nesting, deduplicate logic | Medium | ✅ Done |
| **Medium** | `app/Http/Controllers/MindFocusController.php` | Add return type declarations | Low | ✅ Done |
| **Medium** | `app/Services/QwenMindFocusService.php` | Replace redundant icon map with enum or validation | Low | ✅ Done |
| **Medium** | `app/Http/Controllers/Settings/SecurityController.php` | Simplify nested ternary in `middleware()` | Low | ✅ Done |
| **Low** | `app/Http/Controllers/MindFocusController.php` | Extract validation to Form Request; use translations | Low | Pending |
| **Low** | `config/services.php` | Add `qwen` configuration section | Low | Pending |
| **Low** | `tests/Pest.php` | Uncomment `RefreshDatabase` or document why disabled | Low | ✅ Done |
| **Info** | `app/Services/QwenMindFocusService.php` | Consider queuing CLI call as background job | High | Pending |

---

## Detailed Findings

### 🔴 HIGH: QwenMindFocusService -- Too Many Responsibilities

**File:** `app/Services/QwenMindFocusService.php` (~350 lines)

#### 1. Hardcoded binary path (Configuration Anti-pattern) ✅ RESOLVED

**Was:**
```php
$this->binaryPath = env('QWEN_BIN_PATH', '/opt/homebrew/bin/qwen');
```

**Now:**
Moved to `config/services.php`:
```php
'qwen' => [
    'binary_path' => env('QWEN_BIN_PATH', '/opt/homebrew/bin/qwen'),
    'timeout' => (int) env('QWEN_TIMEOUT', 120),
],
```

And in `QwenMindFocusService`:
```php
$this->binaryPath = config('services.qwen.binary_path');
$this->timeout = config('services.qwen.timeout', 120);
```

---

#### 2. Massive hardcoded system prompt (~150 lines) ✅ RESOLVED

**Was:**
The `getSystemPrompt()` method returned a 150+ line Spanish-language prompt embedded as a heredoc.

**Now:**
Extracted to external resource file:
```
resources/prompts/mindfocus_system_prompt.md
```

Loaded in service:
```php
private function getSystemPrompt(): string
{
    $promptPath = resource_path('prompts/mindfocus_system_prompt.md');

    if (! file_exists($promptPath)) {
        throw new \RuntimeException("System prompt file not found: {$promptPath}");
    }

    $prompt = file_get_contents($promptPath);

    if ($prompt === false) {
        throw new \RuntimeException("Failed to read system prompt file: {$promptPath}");
    }

    return $prompt;
}
```

**Benefits:**
- Makes the service class more readable and maintainable.
- Easy to version control and A/B test different prompts.
- Separation of concerns: code logic vs. prompt content.

---

#### 3. Missing PHPDoc for array return type

**Current:**
```php
public function structure(string $text): array
```

**Recommended Fix:**
```php
/**
 * @return array{
 *     groups: array<int, array{
 *         id: int,
 *         name: string,
 *         icon: string,
 *         color: string,
 *         isPrimary: bool,
 *         subgroups: array<int, array{
 *             id: int,
 *             name: string,
 *             icon: string,
 *             color: string,
 *             items: array<int, array{
 *                 id: int,
 *                 title: string,
 *                 description?: string,
 *                 isPrimary: bool
 *             }>
 *         }>
 *     }>,
 *     markdown: string
 * }
 */
public function structure(string $text): array
```

---

#### 4. Complex nested loops in `parseResponse()`

**Current:**
- 4+ levels of nested `foreach` loops.
- Logic for setting default `isPrimary` item is **duplicated** across two nearly identical code blocks (lines ~185-205 for groups and subgroups).

**Recommended Fix:**

Extract primary-fallback logic into dedicated method:
```php
private function ensurePrimaryItem(array &$items): void
{
    if (empty($items)) {
        return;
    }

    $hasPrimary = collect($items)->contains('isPrimary', true);
    
    if (! $hasPrimary) {
        $items[0]['isPrimary'] = true;
    }
}
```

Then in `parseResponse()`:
```php
// After parsing groups
$this->ensurePrimaryItem($groups);

// After parsing subgroups
foreach ($groups as &$group) {
    $this->ensurePrimaryItem($group['subgroups']);
}
```

---

#### 5. Icon map is redundant ✅ RESOLVED

**Was:**
```php
$iconMap = [
    'briefcase' => 'briefcase',
    'user' => 'user',
    'users' => 'users',
    // ... 10+ identical key-value pairs
];
```

**Now:**
```php
private const ALLOWED_ICONS = [
    'briefcase', 'user', 'heart', 'lightbulb',
    'dollar-sign', 'book', 'home', 'users',
];

$icon = in_array($group['icon'] ?? null, self::ALLOWED_ICONS, true)
    ? $group['icon']
    : 'briefcase';
```

---

#### 6. Uses `\Log` facade directly

**Current:**
```php
\Log::error('Qwen CLI error (MindFocus)', [...]);
```

**Recommended Fix:**
```php
logger()->error('Qwen CLI error (MindFocus)', [...]);
```

Or inject `Psr\Log\LoggerInterface` via constructor for better testability.

---

#### 7. Synchronous CLI call blocks request

**Current:**
```php
$process = new Process([
    $this->binaryPath,
    'structure',
    $text,
]);

$process->setTimeout(120);
$process->run();
```

**Problem:** 120-second synchronous execution will timeout HTTP requests in production.

**Recommended Fix:**

For production use, queue this as a background job:
```php
// Create job class
php artisan make:job StructureMindFocusText

// In job:
public function handle(QwenMindFocusService $service): void
{
    $result = $service->structure($this->text);
    // Store result, notify user, etc.
}
```

Controller then dispatches:
```php
StructureMindFocusText::dispatch($text, $request->user());
```

---

#### 8. `generateMarkdown()` duplicates logic

**Problem:** Markdown generation for tasks vs notes shares nearly identical structure (loop items, append title, append description as blockquote).

**Recommended Fix:**

Extract common pattern:
```php
private function formatItemAsMarkdown(array $item): string
{
    $markdown = "### {$item['title']}\n";
    
    if (!empty($item['description'])) {
        $markdown .= "> {$item['description']}\n";
    }
    
    return $markdown;
}
```

---

### 🟡 MEDIUM: MindFocusController -- Missing Return Types

**File:** `app/Http/Controllers/MindFocusController.php`

**Current:**
```php
public function structure(Request $request, QwenMindFocusService $mindFocusService)
public function structureApi(Request $request, QwenMindFocusService $mindFocusService)
```

**Recommended Fix:**
```php
use Inertia\Response;
use Illuminate\Http\JsonResponse;

public function structure(Request $request, QwenMindFocusService $mindFocusService): Response
public function structureApi(Request $request, QwenMindFocusService $mindFocusService): JsonResponse
```

**Additional:** Both methods duplicate empty-text validation. Extract to Form Request:
```php
php artisan make:request StructureTextRequest
```

```php
// app/Http/Requests/StructureTextRequest.php
public function rules(): array
{
    return [
        'text' => ['required', 'string', 'min:10'],
    ];
}

public function messages(): array
{
    return [
        'text.required' => 'El texto no puede estar vacío.',
        'text.min' => 'El texto debe tener al menos 10 caracteres.',
    ];
}
```

---

### 🟡 MEDIUM: SecurityController nested ternary ✅ RESOLVED

**File:** `app/Http/Controllers/Settings/SecurityController.php`

**Was:**
```php
public static function middleware(): array
{
    return Features::canManageTwoFactorAuthentication()
        && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
            ? [new Middleware('password.confirm', only: ['edit'])]
            : [];
}
```

**Now:**
```php
public static function middleware(): array
{
    if (! Features::canManageTwoFactorAuthentication()) {
        return [];
    }

    if (! Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
        return [];
    }

    return [new Middleware('password.confirm', only: ['edit'])];
}
```

**Benefits:**
- Improved readability with early returns.
- Easier to understand and maintain.
- Follows common Laravel conventions.

---

### 🟢 LOW: Spanish error messages hardcoded

**File:** `app/Http/Controllers/MindFocusController.php`

**Current:**
```php
return back()->with('error', 'El texto no puede estar vacío.');
```

**Recommended Fix:**

Use Laravel's translation system:
```php
return back()->with('error', __('mindfocus.text_required'));
```

Create `lang/es/mindfocus.php`:
```php
return [
    'text_required' => 'El texto no puede estar vacío.',
];
```

---

### 🟢 LOW: Missing config entry for Qwen service

**File:** `config/services.php`

**Recommended Addition:**
```php
'qwen' => [
    'binary_path' => env('QWEN_BIN_PATH', '/opt/homebrew/bin/qwen'),
    'timeout' => (int) env('QWEN_TIMEOUT', 120),
],
```

---

### 🟢 LOW: Test configuration issues

**File:** `tests/Pest.php`

#### 1. RefreshDatabase commented out

**Current:**
```php
pest()->extend(TestCase::class)
 // ->use(RefreshDatabase::class)
    ->in('Feature');
```

**Action Required:**
- If tests modify database, uncomment `RefreshDatabase` to prevent data leakage between test runs.
- If tests are truly stateless, add comment explaining why disabled.

#### 2. Dead helper function

**Current:**
```php
function something(): string
{
    return 'something';
}
```

**Action:** Remove this function -- it serves no purpose.

---

## Architecture Assessment

### ✅ Strengths
- Clear separation of concerns (controllers, services, actions).
- Form Request classes for validation.
- Comprehensive test coverage (14 test files).
- Proper use of Fortify for authentication.
- No N+1 query issues detected (minimal Eloquent usage).
- No significant code duplication across controllers.

### ⚠️ Areas for Improvement
- Single service class handling too many responsibilities.
- Configuration not following Laravel best practices (`env()` outside config files).
- Missing type declarations in some public methods.
- Synchronous external process execution may not scale.

---

## Recommended Refactoring Order

1. ~~**Add return type declarations**~~ -- ✅ improves code quality and IDE support
2. ~~**Fix test configuration**~~ -- ✅ ensures reliable test suite
3. ~~**Replace icon map**~~ -- ✅ simplifies validation logic
4. ~~**Fix configuration issues** (move `env()` calls to config files)~~ -- ✅ prevents production bugs
5. ~~**Extract system prompt**~~ -- ✅ makes service more maintainable
6. ~~**Refactor `parseResponse()`**~~ -- ✅ reduces complexity and duplication
7. ~~**Simplify SecurityController ternary**~~ -- ✅ improves readability
8. **Add translations** -- prepares for internationalization
9. **Consider background job** -- for production scalability

---

## Estimated Effort

| Task | Effort | Risk | Status |
|------|--------|------|--------|
| ~~Config fixes~~ | ~~30 min~~ | Low | ✅ Done |
| ~~Return types~~ | ~~15 min~~ | Low | ✅ Done |
| ~~Extract system prompt~~ | ~~1 hour~~ | Low | ✅ Done |
| ~~Refactor parseResponse~~ | ~~2 hours~~ | Medium | ✅ Done |
| ~~Icon map cleanup~~ | ~~30 min~~ | Low | ✅ Done |
| ~~SecurityController ternary~~ | ~~15 min~~ | Low | ✅ Done |
| Translation setup | 1 hour | Low | Pending |
| Background job (optional) | 3 hours | Medium | Pending |

**Total (core fixes):** ~1 hour remaining
**Total (with optional):** ~4 hours remaining
**Completed so far:** ~2 hours 30 min

---

## Notes

- **No N+1 query issues detected:** The codebase is small with minimal database querying. The only model queries are straightforward single-model lookups via `$request->user()`.
- **Test coverage is adequate:** 14 test files cover authentication, registration, profile updates, security settings, and dashboard access.
- **No code duplication across controllers:** Controllers are small and follow standard Laravel conventions.
