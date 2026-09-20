# Deploy Laravel Backend to Render - Step by Step

## 🚀 Quick Start (5 Minutes)

### Step 1: Create Render Account

1. Go to https://render.com
2. Sign up (use GitHub account - easier!)
3. Click **"New +"** → **"Web Service"**

---

### Step 2: Connect Your GitHub Repository

1. Click **"Connect account"** to authorize GitHub
2. Search for: `attendance_sytem`
3. Select the repository
4. Click **"Connect"**

---

### Step 3: Configure the Backend Service

**Name:**

```
attendance-backend
```

**Environment:**

```
Docker
```

**Root Directory:** (Click "Specify root directory")

```
backend
```

**Build Command:** (Keep default or leave empty)

**Start Command:**

```
composer install && php artisan migrate && php artisan serve --host=0.0.0.0 --port=10000
```

---

### Step 4: Add Environment Variables

Click **"Add Environment Variable"** and add each one:

```
DB_CONNECTION      = pgsql
DB_HOST            = your-supabase-host.supabase.co
DB_PORT            = 5432
DB_DATABASE        = your-database-name
DB_USERNAME        = your-database-user
DB_PASSWORD        = your-database-password
APP_KEY            = your-app-key
APP_ENV            = production
APP_DEBUG          = false
APP_URL            = https://your-render-backend.onrender.com
CORS_ALLOWED_ORIGINS = https://your-frontend-domain.vercel.app
```

⚠️ Replace `https://your-render-backend.onrender.com` with your actual Render URL (you'll get it after deployment)

---

### Step 5: Deploy

1. Click **"Create Web Service"**
2. Wait for deployment (2-5 minutes)
3. Render will show your live URL: `https://attendance-backend-xxx.onrender.com`

---

## ✅ After Deployment

### Get Your Backend URL

Your backend will be available at:

```
https://attendance-backend-xxx.onrender.com/api
```

(Render shows the exact URL after deployment)

---

### Update Frontend API URL

1. Go to Vercel dashboard
2. Go to **Settings** → **Environment Variables**
3. Update `VITE_API_URL` to:
   ```
   https://attendance-backend-xxx.onrender.app/api
   ```
4. Vercel auto-redeploys with new URL

---

### Test Your Backend API

```bash
# Test health endpoint
curl https://your-render-backend.onrender.com/api/health

# Or test login
curl -X POST https://your-render-backend.onrender.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

---

## 🎯 Complete Architecture Now

```
└── Your Attendance System
    ├── Frontend
    │   └── Vercel (https://attendance-sytem-9bee-...vercel.app)
    ├── Backend API
    │   └── Render (https://attendance-backend-xxx.onrender.com/api)
    └── Database
        └── Supabase (PostgreSQL)
```

---

## Troubleshooting

### Error: "Failed to connect to database"

- Check all DB\_\* environment variables are correct
- Verify Supabase database is running
- Check port 5432 is accessible

### Error: "Module not found"

- Build failed during Composer install
- Render shows logs - check what's missing
- Usually means `composer.json` is missing required packages

### Deployment stuck

- Check Render logs: Dashboard → Service → Logs
- Look for error messages
- Might need to rebuild

### API returns 404

- Make sure routes are properly configured in Laravel
- Check `backend/routes/api.php` exists
- Verify build command ran successfully

---

## Monitor Your Backend

**View Logs:**

- Render Dashboard → Service → **Logs** tab
- Shows real-time activity

**View Metrics:**

- Render Dashboard → Service → **Metrics** tab
- CPU, Memory, Response time

**Restart Backend:**

- Manual redeploy: Dashboard → **Redeploy** button
- Auto redeploy on git push: Already enabled!

---

## What Happens After You Click "Create"

1. ✅ Render clones your GitHub repo
2. ✅ Checkouts `backend` folder
3. ✅ Installs PHP and Composer dependencies
4. ✅ Runs `composer install`
5. ✅ Runs `php artisan migrate` (creates database tables!)
6. ✅ Starts Laravel server on port 10000
7. ✅ Exposes via: `https://your-service.onrender.com`
8. ✅ Frontend can now connect via `/api` routes!

---

## Next Steps After Deployment

1. ✅ Get your Render backend URL
2. ✅ Update `VITE_API_URL` on Vercel
3. ✅ Test frontend login
4. ✅ Check all features work
5. ✅ Monitor logs for errors

---

**Ready? Go to [render.com](https://render.com) and start!** 🚀

When you finish, tell me your backend URL and I'll verify everything works!
