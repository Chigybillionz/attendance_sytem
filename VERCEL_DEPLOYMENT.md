# Vercel Deployment Guide - Attendance System

## Overview

This guide walks you through deploying your full-stack attendance system (Vue.js frontend + Laravel backend) to Vercel with Supabase database.

## Prerequisites

✅ Supabase project created and configured  
✅ GitHub repository with latest code pushed  
✅ Vercel account created

## Step 1: Deploy to Vercel

### 1.1 Connect Your Repository

1. Go to [https://vercel.com](https://vercel.com)
2. Click **"Add New"** → **"Project"**
3. Select **"Import Git Repository"**
4. Search for and select your `attendance_sytem` repository
5. Click **"Import"**

### 1.2 Configure Project Settings

When importing, Vercel will ask for configuration:

**Framework Preset**: Select **"Other"** (monorepo setup)

**Project Name**: `attendance-system`

**Root Directory**: Leave blank (or select `./`)

**Build Command**: Leave default or keep empty

**Output Directory**: Leave blank

Click **"Deploy"** (this will initially fail - that's normal, we'll fix it)

---

## Step 2: Configure Environment Variables

After the initial setup, go to your project settings:

1. Click **"Settings"** tab
2. Navigate to **"Environment Variables"**
3. Add each of these variables:

```
DB_CONNECTION         = pgsql
DB_HOST              = db.kwmyunvwjfdrvrfibezh.supabase.co
DB_PORT              = 5432
DB_DATABASE          = postgres
DB_USERNAME          = postgres
DB_PASSWORD          = Youngchapel@2024
APP_KEY              = base64:zI/7ezkCdtqG1G/GB80djq0aH1eNecuMwSlu/7ArOYg=
APP_ENV              = production
APP_DEBUG            = false
VITE_API_URL         = https://your-vercel-domain.vercel.app/api
```

**IMPORTANT**: Replace `https://your-vercel-domain.vercel.app` with your actual Vercel domain!

### To find your Vercel domain:

- Go to your project overview
- Look for the URL like: `https://attendance-system-xyz.vercel.app`

---

## Step 3: Manual Deployment Configuration

Since this is a monorepo, we need to manually configure the deployment.

### 3.1 Update `vercel.json`

The root `vercel.json` file is already created, but we need to adjust it for your specific setup.

### 3.2 Create Backend Serverless Function

Create this file: `api/index.js`

```javascript
// api/index.js
import { createRequire } from "module";
const require = createRequire(import.meta.url);
const app = require("../backend/public/index.php");

export default app;
```

---

## Step 4: Deploy

### Option A: Deploy from Git (Recommended)

1. Go to your project on Vercel
2. Click **"Deployments"** tab
3. Click **"Redeploy"** next to the latest deployment
4. Or make a commit and push to GitHub - automatic deployment will trigger!

### Option B: Manual Deploy

```bash
npm i -g vercel
cd c:\attendance_sytem
vercel --prod
```

---

## Step 5: Verify Deployment

### 5.1 Check Frontend

Visit `https://your-vercel-domain.vercel.app` in your browser

You should see your attendance system UI loading.

### 5.2 Check API

```bash
curl https://your-vercel-domain.vercel.app/api/health

# Or test login endpoint
curl -X POST https://your-vercel-domain.vercel.app/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

### 5.3 Check Logs

On Vercel dashboard:

1. Click **"Deployments"**
2. Select the latest deployment
3. Click **"Logs"** tab to see build and runtime logs

---

## Step 6: Run Database Migrations

Once deployed, you need to run migrations on your Supabase database.

### Option A: Run remotely via SSH

If your Laravel app supports Vercels SSH (Pro plan):

```bash
vercel ssh
php artisan migrate
php artisan db:seed --class=AdminSeeder
exit
```

### Option B: Run locally then verify

```bash
# On your local machine
cd backend
php artisan migrate:fresh --seed
```

Or if you set up the local environment correctly, use:

```bash
php migrate.php
```

### Option C: Use a deployment hook

Create a file `api/migrate.php`:

```php
<?php
// Only allow from Vercel
if ($_SERVER['HTTP_X_VERCEL_REQUEST_ID'] ?? null) {
    require_once __DIR__.'/../backend/artisan';
    // Run migrations on first deployment
}
?>
```

---

## Step 7: Configure Domain (Optional)

To use a custom domain:

1. On Vercel dashboard, click **"Settings"** → **"Domains"**
2. Click **"Add"**
3. Enter your domain (e.g., `attendance.yourcompany.com`)
4. Follow DNS configuration instructions
5. Update environment variables to use your custom domain

---

## Step 8: Update Frontend API URL

After deployment, update your frontend to use the correct API URL.

### If using custom domain:

```bash
VITE_API_URL=https://attendance.yourcompany.com/api
```

### If using Vercel subdomain:

```bash
VITE_API_URL=https://attendance-system-xyz.vercel.app/api
```

Then redeploy frontend:

```bash
vercel --prod
```

---

## Troubleshooting

### Error: "502 Bad Gateway"

- Frontend may be working but backend API is failing
- Check Vercel logs: **Deployments** → **Logs**
- Verify all environment variables are set
- Run migrations on your Supabase database

### Error: "Cannot find module 'vendor/autoload.php'"

- This means PHP dependencies aren't installed
- Make sure `composer install` runs during build
- Check if `vendor/` directory is in `.vercelignore`

### Error: "Connection refused to Supabase"

- Verify database credentials in environment variables
- Check Supabase is running and accessible
- Tests from local machine: `php artisan tinker` → `DB::connection()->getPdo();`

### Frontend showing "Cannot connect to API"

- Check `VITE_API_URL` environment variable
- Verify API is accessible: curl the endpoint
- Check browser console for CORS errors
- Update `backend/config/cors.php` with your domain

### Migrations not running

- Vercel has limited PHP support for artisan commands
- Recommended: Run migrations locally or via SSH
- Alternative: Create a migration endpoint that accepts a secret token

---

## File Structure After Deployment

```
attendance_sytem/
├── api/
│   └── index.js                    ← Vercel serverless function
├── backend/
│   ├── .env.production             ← Production env template
│   ├── public/
│   │   └── index.php               ← Laravel entry point
│   └── ...
├── frontend/
│   ├── .env.production             ← Frontend production env
│   ├── dist/                       ← Built frontend
│   └── ...
├── .vercelignore                   ← Files to ignore
├── vercel.json                     ← Vercel config
└── VERCEL_DEPLOYMENT.md            ← This file
```

---

## Environment Variables Summary

| Variable        | Value                                 | Notes                |
| --------------- | ------------------------------------- | -------------------- |
| `DB_CONNECTION` | `pgsql`                               | PostgrSQL (Supabase) |
| `DB_HOST`       | `db.kwmyunvwjfdrvrfibezh.supabase.co` | Supabase host        |
| `DB_PORT`       | `5432`                                | PostgreSQL port      |
| `DB_DATABASE`   | `postgres`                            | Database name        |
| `DB_USERNAME`   | `postgres`                            | Database user        |
| `DB_PASSWORD`   | `Youngchapel@2024`                    | ⚠️ Keep secure!      |
| `APP_KEY`       | `base64:zI/7...`                      | Application key      |
| `APP_ENV`       | `production`                          | Environment          |
| `VITE_API_URL`  | `https://your-domain.vercel.app/api`  | Frontend API URL     |

---

## Post-Deployment Checklist

- [ ] Frontend loads at your Vercel domain
- [ ] API endpoints respond (test with curl)
- [ ] Database migrations ran successfully
- [ ] Login functionality works
- [ ] SSL certificate is valid
- [ ] Custom domain configured (if applicable)
- [ ] Error logs monitored
- [ ] Supabase backups enabled

---

## Support & Resources

- **Vercel Docs**: https://vercel.com/docs
- **Laravel on Vercel**: https://vercel.com/docs/frameworks/laravel
- **Supabase**: https://supabase.com/docs
- **Your Repo**: https://github.com/Chigybillionz/attendance_sytem
- **Vercel CLI**: `npm i -g vercel`

---

## Quick Reference

```bash
# Deploy to Vercel
vercel --prod

# View logs
vercel logs [project-name] --tail

# Rebuild
vercel rebuild

# Check status
vercel status

# Environment variables
vercel env ls
vercel env add DB_PASSWORD
```

---

**Your deployment is ready! 🚀**

Start with deploying to Vercel, then we can work on any additional configuration needed.
