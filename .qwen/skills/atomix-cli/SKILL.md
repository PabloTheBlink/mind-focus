---
name: atomix-cli
description: "Use this skill when working with Atomix CLI — a structured design engine that maps visual HTML elements to a relational database. Activate when: creating Atomix projects or components (`atomix create`), importing HTML (`atomix import`), previewing rendered output (`atomix preview`), debugging slot/composition issues, converting CSS classes to inline styles for Atomix import, working with `data-at-link-to` navigation attributes, or troubleshooting Atomix API connectivity. Covers: style-first paradigm, atomic component design, slot-based composition, CLI commands, API endpoints, and HTML export workflows."
license: MIT
metadata:
  author: atomix
---

# Atomix CLI

Atomix is not a traditional HTML manager — it is a **structured design engine** that disintegrates HTML into atomic records in a relational database (nodes, styles, attributes). This skill covers the complete workflow for creating, importing, and composing visual components.

## Core Principles

### 1. Pure Layout Only — No JavaScript, No Global Structure

Atomix processes **visual fragments only**. It is a design structuring engine, not an application runtime.

**Strict Prohibitions:**

| ❌ Never Include                                             | Reason                                                |
| :---------------------------------------------------------- | :---------------------------------------------------- |
| `<script>` tags or event attributes (`onclick`, `onchange`) | Atomix strips all JS during import                    |
| `<html>`, `<head>`, `<body>`, `<meta>`, `<title>`, `<link>` | Importer only accepts content fragments               |
| `<style>` blocks                                            | Ignored during import — use inline `style="..."` only |

**What to send:** Only the HTML fragment you want rendered inside the layout.

```html
<!-- ✅ CORRECT: Pure fragment with inline styles -->
<div style="display: flex; flex-direction: column; gap: 1rem;">
    <h1 style="font-size: 2rem; font-weight: bold;">Welcome</h1>
    <p style="color: #6b7280;">This is a content section.</p>
</div>

<!-- ❌ WRONG: Full HTML document with scripts -->
<!DOCTYPE html>
<html>
<head><script>console.log("no!")</script></head>
<body><div onclick="alert('no!')">Bad</div></body>
</html>
```

### 2. Style-First Paradigm — Inline Styles Only

Atomix ignores external CSS classes. Every property must be declared inline so the visual editor can manage individual style records in the `node_styles` table.

```html
<!-- ❌ ANTI-PATTERN: Atomix doesn't know what 'bg-blue-500' or 'p-4' means -->
<div class="bg-blue-500 p-4 shadow-lg">Content</div>

<!-- ❌ ANTI-PATTERN: Internal <style> blocks are ignored -->
<style>.my-card { background: red; }</style>
<div class="my-card">Content</div>

<!-- ✅ ATOMIC PATTERN: Every property stored individually -->
<div style="background-color: #3b82f6; padding: 1rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);">
    Content
</div>
```

**Converting Tailwind/CSS to inline styles:**

When prototyping with Tailwind or other CSS frameworks, always convert the final output to inline `style` attributes before importing into Atomix.

### 3. Maximum Atomicity — Small, Reusable Components

Components should be as small and specific as possible. Even a button, icon, or divider deserves its own Atomix record.

**✅ Atomic Components (Recommended):**

```html
<!-- button.html -->
<button style="background: #007bff; color: white; padding: 12px 24px; border-radius: 6px;">
    <slot></slot>
</button>

<!-- card.html -->
<div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem;">
    <slot></slot>
</div>
```

**Composition on a page:**

```html
<mi-layout>
    <mi-boton>Guardar</mi-boton>
    <mi-boton>Cancelar</mi-boton>
    <mi-card>Contenido del card</mi-card>
</mi-layout>
```

**❌ Monolithic Component (Avoid):**

```html
<!-- DON'T: One component with the entire page -->
<div class="pagina-completa">
    <header>...</header>
    <nav>...</nav>
    <main><section>...</section></main>
    <footer>...</footer>
</div>
```

| Atomic Components        | Monolithic Components            |
| :----------------------- | :------------------------------- |
| ✅ Precise visual editing | ❌ Complex editing (entire block) |
| ✅ Maximum reusability    | ❌ Code duplication               |
| ✅ Optimal rendering      | ❌ Unnecessary render weight      |
| ✅ Simple maintenance     | ❌ Risky changes                  |

**Rule of Thumb:** If an element can exist independently and has visual meaning on its own, **it should be its own component**.

## Slot-Based Composition

Atomix uses a Web Components-style injection system.

### The Layout (Skeleton)

Layouts define the persistent structure. The `<slot></slot>` tag acts as the dynamic content marker.

```html
<!-- main-layout.html -->
<div style="min-height: 100vh; display: flex; flex-direction: column;">
    <header style="background: #1f2937; color: white; padding: 1rem;">
        Site Header
    </header>
    <main style="flex: 1; padding: 2rem;">
        <slot></slot>
    </main>
    <footer style="background: #f3f4f6; padding: 1rem; text-align: center;">
        Footer
    </footer>
</div>
```

> [!IMPORTANT]
> **Single Slot Rule:** Only **ONE `<slot></slot>`** is allowed per component or layout. Atomix does not support named slots or multiple slot tags. If you need multiple injection points, split your design into smaller atomic components.

### Slot Syntax Requirements

- Must be exactly `<slot></slot>` (lowercase, no self-closing `<slot />`)
- Only one per component
- No named slots (`<slot name="header">` is not supported)

## Navigation Between Pages

Atomix supports page linking via the `data-at-link-to` attribute during HTML import.

```html
<!-- Button navigating to 'checkout' page -->
<button data-at-link-to="checkout" style="background: blue; color: white;">
    Ir al Checkout
</button>

<!-- Link navigating to 'dashboard' page -->
<a data-at-link-to="dashboard" style="text-decoration: none;">
    Volver al Panel
</a>
```

**Import Behavior:**
- The importer stores `data-at-link-to` as an atomic node property
- If `<a>` has `data-at-link-to` but no `href`, Atomix auto-assigns `href="javascript:void(0)"` for proper cursor behavior

## Development Lifecycle

Follow this exact flow for 100% success:

### Step 1: Design

Prototype your component in HTML/CSS. Tools like Tailwind are fine for prototyping, but **convert to inline styles** before importing.

### Step 2: Register

```bash
# Create a component
atomix mi-proyecto create mi-componente component

# Create a layout
atomix mi-proyecto create main-layout layout

# Create a project (if needed)
atomix create mi-proyecto project
```

### Step 3: Import

```bash
# Import HTML file into a component
atomix mi-proyecto/mi-componente import archivo.html
```

### Step 4: Compose

Create a page and reference the component by its slug:

```html
<mi-layout>
    <mi-componente>Content here</mi-componente>
</mi-layout>
```

### Step 5: Local Workspace & Sync

For a more efficient workflow, set up a local workspace:

```bash
# Link the current folder to a project
atomix use mi-proyecto

# Synchronize local and remote changes based on timestamps
atomix sync
```

This downloads all project components into `.atomix/` subdirectories and allows you to use your preferred local editor.

### Step 6: Preview

```bash
# Preview the rendered output
atomix mi-proyecto/page preview

# Export to file
atomix mi-proyecto/page preview > output.html
```

## CLI Commands Reference

| Command   | Description                   | Example                              |
| :-------- | :---------------------------- | :----------------------------------- |
| `config`  | Link terminal with API        | `atomix config key_123...`           |
| `doctor`  | System health diagnosis       | `atomix doctor`                      |
| `use`     | Initialize local workspace    | `atomix use mi-proyecto`             |
| `sync`    | Sync local <-> remote         | `atomix sync`                        |
| `create`  | Create projects or components | `atomix proy create header layout`   |
| `import`  | Upload HTML code to system    | `atomix proy/comp import index.html` |
| `preview` | Render final output           | `atomix proy/page preview`           |

## API Endpoints

For programmatic integration:

| Method | Endpoint                                             | Description             |
| :----- | :--------------------------------------------------- | :---------------------- |
| GET    | `/api/projects`                                      | List all projects       |
| POST   | `/api/projects/{slug}/components`                    | Create a component      |
| POST   | `/api/projects/{slug}/components/{comp}/import-html` | Parse `{"html": "..."}` |
| GET    | `/api/projects/{slug}/components/{comp}/preview`     | Get final rendered HTML |

### Export HTML to File

```bash
# Option A: Preview redirect
atomix proy/page preview > output.html

# Option B: Direct API call
curl https://atomix.test/api/projects/proy/components/comp/preview -o output.html

# Option C: Output flag (if available)
atomix proy/page preview --output output.html
```

## Troubleshooting

### Component Not Appearing on Page

- **Cause:** The slug in the HTML tag doesn't exactly match the Atomix slug
- **Fix:** Run `atomix <proyecto>` to see available slugs. Verify exact match (case-sensitive, kebab-case)

### Layout Not Rendering Content

- **Cause:** Missing or malformed `<slot></slot>` tag
- **Fix:** Must be exactly `<slot></slot>` in lowercase. Not `<Slot>`, not `<slot />`, not `<slot name="main">`

### Connection Error (HTTP 000 or Timeout)

- **Cause:** `https://atomix.test` is not reachable from your network
- **Fix:** Run `atomix doctor` to verify API visibility. Ensure Laravel Herd or local server is running

### Styles Not Applied After Import

- **Cause:** Used `class` attribute instead of inline `style`
- **Fix:** Convert all CSS classes to inline `style="property: value;"` attributes before importing

## Best Practices Checklist

- [ ] No `<script>`, `<html>`, `<head>`, `<body>`, `<style>` tags in imports
- [ ] All styles use inline `style="..."` attributes
- [ ] Components are atomic (one visual element per component)
- [ ] Each component/layout has exactly one `<slot></slot>`
- [ ] Slot tags are lowercase and not self-closing
- [ ] Navigation uses `data-at-link-to` with correct page slugs
- [ ] Component slugs match exactly when composing pages
- [ ] Tailwind/CSS prototypes are converted to inline styles before import
