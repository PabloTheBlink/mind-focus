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

---

## Production Setup (Apache)

In production with Apache, you need to keep the SSR server running persistently using a process manager.

### 1. Install Supervisor

Install Supervisor on your production server:

```bash
# Ubuntu/Debian
sudo apt-get install supervisor

# CentOS/RHEL
sudo yum install supervisor
```

### 2. Create Supervisor Configuration

Create a Supervisor config file for the SSR server:

```bash
sudo nano /etc/supervisor/conf.d/mindfocus-ssr.conf
```

Add the following content (replace `/ruta/a/tu/proyecto` with your actual project path):

```ini
[program:mindfocus-ssr]
process_name=%(program_name)s_%(process_num)02d
command=php /ruta/a/tu/proyecto/artisan inertia:start-ssr
directory=/ruta/a/tu/proyecto
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/ruta/a/tu/proyecto/storage/logs/ssr-worker.log
stopwaitsecs=3600
```

**Important**: Change `user=www-data` to the user that runs your web server (could be `apache`, `nginx`, or your deploy user).

### 3. Enable and Start Supervisor

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start mindfocus-ssr:*
```

### 4. Configure Environment Variables

Make sure your production `.env` file has:

```env
INERTIA_SSR_URL=http://127.0.0.1:13714
INERTIA_SSR_ENABLED=true
```

### 5. Apache Configuration (Optional)

If you need Apache to proxy SSR requests (usually not needed - Laravel connects directly to the SSR server), add this to your VirtualHost:

```apache
<VirtualHost *:443>
    ServerName tu-dominio.com
    
    # Your normal Laravel configuration...
    DocumentRoot /ruta/a/tu/proyecto/public
    
    <Directory /ruta/a/tu/proyecto/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    # SSL configuration...
    SSLEngine on
    SSLCertificateFile /path/to/cert.pem
    SSLCertificateKeyFile /path/to/key.pem
</VirtualHost>
```

**Note**: Laravel's Inertia connects directly to the SSR server via the URL in `config/inertia.php`, so Apache proxy is usually not needed.

### 6. Deploy Script

Add these steps to your deployment script:

```bash
# 1. Install Node dependencies
npm ci

# 2. Build the SSR bundle
npm run build:ssr

# 3. Restart the SSR server via Supervisor
sudo supervisorctl restart mindfocus-ssr:*

# 4. Verify SSR is running
curl http://127.0.0.1:13714
```

Example deploy script snippet:

```bash
#!/bin/bash
cd /ruta/a/tu/proyecto

# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci

# Build assets
npm run build
npm run build:ssr

# Run migrations
php artisan migrate --force

# Restart SSR
sudo supervisorctl restart mindfocus-ssr:*

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7. Verification

Verify SSR is working in production:

```bash
# From your local machine
curl -s -L https://tu-dominio.com/ | grep -o '<h1[^>]*>[^<]*</h1>'

# Or from the server itself
curl -s http://127.0.0.1:13714

# Check Supervisor status
sudo supervisorctl status mindfocus-ssr:*

# Check SSR logs
tail -f /ruta/a/tu/proyecto/storage/logs/ssr-worker.log

# Check Laravel logs for SSR errors
tail -f /ruta/a/tu/proyecto/storage/logs/laravel.log | grep -i ssr
```

### 8. Troubleshooting

**SSR server not starting:**

```bash
# Check if the port is in use
sudo lsof -i :13714

# Check Supervisor logs
sudo tail -50 /var/log/supervisor/supervisord.log

# Check worker logs
sudo tail -50 /ruta/a/tu/proyecto/storage/logs/ssr-worker.log
```

**SSR returns errors:**

```bash
# Rebuild the SSR bundle
npm run build:ssr

# Restart the worker
sudo supervisorctl restart mindfocus-ssr:*

# Check for missing Node packages
npm install
```

**Port 13714 is blocked:**

Make sure your server's firewall allows localhost connections on port 13714:

```bash
# Usually not needed as it's localhost only, but if needed:
sudo ufw allow 13714/tcp
```

### 9. Monitoring

Monitor the SSR process:

```bash
# Check status
sudo supervisorctl status

# View live logs
sudo tail -f /ruta/a/tu/proyecto/storage/logs/ssr-worker.log

# Restart if needed
sudo supervisorctl restart mindfocus-ssr:*

# Stop
sudo supervisorctl stop mindfocus-ssr:*

# Start
sudo supervisorctl start mindfocus-ssr:*
```
