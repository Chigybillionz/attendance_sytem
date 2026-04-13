# Attendance System - Supabase Cloud Deployment Guide

## Overview

This guide will help you deploy your attendance system to a cloud server with Supabase as the database.

## Prerequisites

- Supabase Project created (✓ Already done)
- Cloud server (Heroku, Railway, Vercel, DigitalOcean, etc.)
- Git repository with your code pushed to GitHub (✓ Already done)

## Step 1: Deployment Setup

### Option A: Deploy on Vercel (Recommended for quick deployment)

Vercel has your frontend code (Next.js/Vue) and can host the backend as well.

1. Go to [vercel.com](https://vercel.com)
2. Click "New Project" and import your GitHub repository
3. Configure environment variables:
   - `DB_CONNECTION=pgsql`
   - `DB_HOST=your-supabase-host.supabase.co`
   - `DB_PORT=5432`
   - `DB_DATABASE=your-database-name`
   - `DB_USERNAME=your-database-user`
   - `DB_PASSWORD=your-database-password`
   - `APP_KEY=your-app-key`
   - `APP_ENV=production`
   - `APP_DEBUG=false`

### Option B: Deploy on Railway.app (Simple Laravel deployment)

1. Go to [railway.app](https://railway.app)
2. Click "New Project" → "Deploy from GitHub"
3. Select your repository
4. Railway will detect it's a Laravel project
5. Add environment variables in the dashboard:
   ```
   DB_CONNECTION=pgsql
   DB_HOST=your-supabase-host.supabase.co
   DB_PORT=5432
   DB_DATABASE=your-database-name
   DB_USERNAME=your-database-user
   DB_PASSWORD=your-database-password
   ```

### Option C: Deploy on DigitalOcean App Platform

1. Create a DigitalOcean account and project
2. Go to App Platform
3. Create a new app → GitHub
4. Select your attendance_sytem repository
5. Manually configure the backend as a service:
   - Build command: `composer install && npm ci`
   - Run command: `php artisan serve`
   - Environment variables (same as above)

## Step 2: Run Database Migrations

Once deployed to your cloud server, SSH into it and run:

```bash
cd /path/to/your/project
php artisan migrate
```

Or use the migration script we created:

```bash
php migrate.php
```

## Step 3: Seed Initial Data (Optional)

To add sample data:

```bash
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=AttendanceSeeder
```

## Step 4: Configure CORS for Frontend

Your frontend needs to connect to your backend API. Update `backend/config/cors.php`:

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],

'allowed_origins' => [
    'https://your-frontend-domain.vercel.app',
    'https://your-frontend-domain.com',
],

'allow_credentials' => true,
```

## Step 5: Frontend Environment Configuration

Update your frontend `.env` file to point to your cloud API:

```
VITE_API_URL=https://your-backend-domain.com/api
```

## Verification Steps

1. **Check database connection:**

   ```bash
   php artisan tinker
   >>> DB::connection()->getPdo();
   ```

   Should return a PDO connection object without errors.

2. **Test an API endpoint:**
   - Login endpoint: `POST /api/login`
   - Should return a token

3. **Frontend connectivity:**
   - Open your frontend URL in the browser
   - Try logging in
   - Check browser console for any API errors

## Troubleshooting

### Connection Refused Error

- Check if Supabase host is correct
- Verify credentials are correct in .env
- Ensure your cloud server can reach Supabase (check firewall)

### SSL/TLS Certificate Error

- Add to `backend/config/database.php` pgsql connection:

```php
'sslmode' => 'require',
```

### Tables Not Found

- SSH into your server and run: `php artisan migrate`
- Check migration status: `php artisan migrate:status`

## After Deployment

1. **Monitor your application:**
   - Set up logging with Laravel Telescope or similar
   - Monitor Supabase dashboard for database usage

2. **Backup your database:**
   - Supabase provides automated backups
   - Enable backup retention in Supabase settings

3. **Update your DNS:**
   - Point your domain to your deployed application
   - Set up SSL certificates (free with most platforms)

## Local Testing Before Deployment

If you need to test locally with Supabase:

1. Your `.env` file is already configured with Supabase
2. Try using WSL2 or Docker to avoid Herd PHP issues:
   ```bash
   docker run --rm -v $(pwd):/app -w /app php:8.4 php artisan migrate
   ```

## Need Help?

- Supabase Docs: https://supabase.com/docs
- Laravel Docs: https://laravel.com/docs
- Contact your cloud provider's support

---

**Your Supabase Credentials:**

- Host: `your-supabase-host.supabase.co`
- Port: `5432`
- Database: `postgres`
- User: `postgres`
- (Password stored securely in your .env files)
