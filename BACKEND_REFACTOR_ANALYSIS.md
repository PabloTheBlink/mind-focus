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
| **High** | `app/Services/QwenMindFocusService.php` | Move `env()` call to config; extract system prompt | Medium | Pending |
| **High** | `app/Services/QwenMindFocusService.php` | Refactor `parseResponse()` -- reduce nesting, deduplicate logic | Medium | Pending |
| **Medium** | `app/Http/Controllers/MindFocusController.php` | Add return type declarations | Low | ✅ Done |
| **Medium** | `app/Services/QwenMindFocusService.php` | Replace redundant icon map with enum or validation | Low | Pending |
| **Medium** | `app/Http/Controllers/Settings/SecurityController.php` | Simplify nested ternary in `middleware()` | Low | Pending |
| **Low** | `app/Http/Controllers/MindFocusController.php` | Extract validation to Form Request; use translations | Low | Pending |
| **Low** | `config/services.php` | Add `qwen` configuration section | Low | Pending |
| **Low** | `tests/Pest.php` | Uncomment `RefreshDatabase` or document why disabled | Low | ✅ Done |
| **Info** | `app/Services/QwenMindFocusService.php` | Consider queuing CLI call as background job | High | Pending |

---

## Detailed Findings

### 🔴 HIGH: QwenMindFocusService -- Too Many Responsibilities

**File:** `app/Services/QwenMindFocusService.php` (~350 lines)

#### 1. Hardcoded binary path (Configuration Anti-pattern)

**Current:**
```php
$this->binaryPath = env('QWEN_BIN_PATH', '/opt/homebrew/bin/qwen');
```

**Problems:**
- `env()` should **never** be called outside config files. In production with config caching (`php artisan config:cache`), this will return `null`.
- Default `/opt/homebrew/bin/qwen` is macOS/Apple Silicon specific and not portable.

**Recommended Fix:**

Move to `config/services.php`:
```php
'qwen' => [
    'binary_path' => env('QWEN_BIN_PATH', '/opt/homebrew/bin/qwen'),
    'timeout' => (int) env('QWEN_TIMEOUT', 120),
],
```

Then in `QwenMindFocusService`:
```php
$this->binaryPath = config('services.qwen.binary_path');
```

---

#### 2. Massive hardcoded system prompt (~150 lines)

**Current:**
The `getSystemPrompt()` method returns a 150+ line Spanish-language prompt embedded as a heredoc.

**Problems:**
- Makes the class harder to read and maintain.
- Not reusable across different contexts.
- Difficult to version or A/B test different prompts.

**Recommended Fix:**

Option A -- Config file:
```php
// config/mindfocus.php
return [
    'system_prompt' => <<<PROMPT
        Eres un asistente experto en organización de ideas...
        [... rest of prompt ...]
    PROMPT,
];
```

Option B -- Resource file (preferred):
```
resources/prompts/mindfocus_system_prompt.md
```

Load in service:
```php
private function getSystemPrompt(): string
{
    return file_get_contents(resource_path('prompts/mindfocus_system_prompt.md'));
}
```

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

#### 5. Icon map is redundant

**Current:**
```php
$iconMap = [
    'briefcase' => 'briefcase',
    'user' => 'user',
    'users' => 'users',
    // ... 10+ identical key-value pairs
];
```

**Problem:** This just validates that the icon is one of allowed values -- but returns the same value if valid or defaults to `briefcase`.

**Recommended Fix:**

Option A -- Simple validation:
```php
private const ALLOWED_ICONS = [
    'briefcase', 'user', 'users', 'heart', 'graduation-cap',
    'credit-card', 'home', 'rocket', 'book', 'star',
    'target', 'lightbulb', 'cog', 'folder', 'calendar',
];

$icon = in_array($groupData['icon'], self::ALLOWED_ICONS, true)
    ? $groupData['icon']
    : 'briefcase';
```

Option B -- Enum (PHP 8.1+):
```php
enum MindFocusIcon: string
{
    case Briefcase = 'briefcase';
    case User = 'user';
    // ... etc
    
    public static function tryFrom(string $value): self
    {
        return self::tryFrom($value) ?? self::Briefcase;
    }
}
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

### 🟡 MEDIUM: SecurityController nested ternary

**File:** `app/Http/Controllers/Settings/SecurityController.php`

**Current:**
```php
public static function middleware(): array
{
    return Features::canManageTwoFactorAuthentication()
        && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
            ? [new Middleware('password.confirm', only: ['edit'])]
            : [];
}
```

**Recommended Fix:**
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
3. **Fix configuration issues** (move `env()` calls to config files) -- prevents production bugs
4. **Extract system prompt** -- makes service more maintainable
5. **Refactor `parseResponse()`** -- reduces complexity and duplication
6. **Replace icon map** -- simplifies validation logic
7. **Add translations** -- prepares for internationalization
8. **Consider background job** -- for production scalability

---

## Estimated Effort

| Task | Effort | Risk | Status |
|------|--------|------|--------|
| Config fixes | 30 min | Low | Pending |
| ~~Return types~~ | ~~15 min~~ | Low | ✅ Done |
| Extract system prompt | 1 hour | Low | Pending |
| Refactor parseResponse | 2 hours | Medium | Pending |
| Icon map cleanup | 30 min | Low | Pending |
| Translation setup | 1 hour | Low | Pending |
| ~~Test fixes~~ | ~~30 min~~ | Low | ✅ Done |
| Background job (optional) | 3 hours | Medium | Pending |

**Total (core fixes):** ~5 hours
**Total (with optional):** ~8 hours
**Completed so far:** ~45 min

---

## Notes

- **No N+1 query issues detected:** The codebase is small with minimal database querying. The only model queries are straightforward single-model lookups via `$request->user()`.
- **Test coverage is adequate:** 14 test files cover authentication, registration, profile updates, security settings, and dashboard access.
- **No code duplication across controllers:** Controllers are small and follow standard Laravel conventions.
