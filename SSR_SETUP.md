# SSR Setup for MindLandingPage.svelte

## What was done

### 1. Created SSR Entry Point
- Created `resources/js/ssr.ts` - the server-side rendering entry point for Inertia
- Simplified configuration (removed layout and setup functions that don't work in SSR)

### 2. Updated Vite Configuration
- Added `ssr: 'resources/js/ssr.ts'` to `vite.config.ts` in the Laravel plugin configuration
- This enables Vite to build the SSR bundle

### 3. Updated Inertia Configuration  
- Uncommented the SSR bundle path in `config/inertia.php`: `'bundle' => base_path('bootstrap/ssr/ssr.mjs')`
- SSR was already enabled (`'enabled' => true`)

### 4. Fixed SSR Compatibility Issues
- Fixed `InputArea.svelte` component that was using `DOMPurify.sanitize()` (browser-only API)
- Added browser environment check: `if (typeof window !== 'undefined')` before calling DOMPurify
- This allows the component to render during SSR without errors

### 5. Built and Started SSR Server
- Ran `npm run build:ssr` to generate the SSR bundle
- Started the SSR server with `php artisan inertia:start-ssr`
- SSR server runs on `http://127.0.0.1:13714`

## How it Works

Now when users visit `https://mind-focus.test/`, the server:
1. Renders the `MindLandingPage.svelte` component on the server
2. Returns fully rendered HTML with all content (headings, text, structure)
3. Search engines can crawl and index all the content immediately
4. The client-side JavaScript hydrates the page for interactivity

## Verification

You can verify SSR is working by:
```bash
curl -s -L https://mind-focus.test/ | grep -o '<h1[^>]*>[^<]*</h1>'
```

You should see all the headings rendered in the HTML response.

## Commands

- **Build SSR bundle**: `npm run build:ssr`
- **Start SSR server**: `php artisan inertia:start-ssr`
- **Stop SSR server**: `php artisan inertia:stop-ssr`
- **Development**: Keep Vite dev server running (`npm run dev`) - SSR works automatically

## Notes

- SSR only runs on the initial page load for SEO benefits
- Client-side navigation after that works as normal SPA
- The SSR server must be running in production for SSR to work
- In development, Vite handles SSR automatically
