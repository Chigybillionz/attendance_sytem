# Vercel Quick Start - 5 Minutes to Deploy

## 📋 Pre-Deployment Checklist

Before you start, make sure you have:

- ✅ Supabase project created with database credentials
- ✅ GitHub repository with latest code pushed
- ✅ Vercel account created (free at vercel.com)
- ✅ Node.js installed locally (for testing)

---

## 🚀 Step-by-Step Deployment

### Step 1: Connect to Vercel (2 minutes)

1. Go to **[https://vercel.com/dashboard](https://vercel.com/dashboard)**
2. Click **"Add New"** → **"Project"**
3. Click **"Import Git Repository"**
4. Search and select `attendance_sytem`
5. Click **"Import"**

### Step 2: Configure Environment Variables (2 minutes)

In the "Environment Variables" section, add these 9 variables:

```
DB_CONNECTION      = pgsql
DB_HOST            = your-supabase-host.supabase.co
DB_PORT            = 5432
DB_DATABASE        = your-database-name
DB_USERNAME        = your-database-user
DB_PASSWORD        = your-database-password
APP_KEY            = your-app-key
APP_ENV            = production
VITE_API_URL       = https://your-vercel-project.vercel.app/api
```

**Replace** `your-vercel-project` with your actual Vercel project name (shown after deployment).

### Step 3: Deploy (1 minute)

Click **"Deploy"** button and wait for build to complete.

Your app is now live! 🎉

---

## ✅ Verify Deployment Works

### Test 1: Frontend loads

```
Open: https://your-vercel-project.vercel.app
```

You should see your login page.

### Test 2: API works

```bash
# From terminal, test the API:
curl https://your-vercel-project.vercel.app/api/health

# Or test login:
curl -X POST https://your-vercel-project.vercel.app/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

Should return a JSON response.

---

## 🔄 Database Migrations

After first deployment, you need to run migrations:

### Option 1: Local Run (Easiest)

Run on your local machine in the `backend` folder:

```bash
php artisan migrate
php artisan db:seed --class=AdminSeeder
```

### Option 2: Via Vercel CLI

```bash
npx vercel ssh --cwd backend
php artisan migrate
exit
```

---

## 📱 Configure Frontend API URL

When you know your final Vercel URL:

1. Update `frontend/.env.production`:

   ```
   VITE_API_URL=https://your-final-domain.vercel.app/api
   ```

2. Commit and push:

   ```bash
   git add frontend/.env.production
   git commit -m "Update API URL for production"
   git push origin master
   ```

3. Vercel auto-redeploys automatically!

---

## 🎯 What Happens After Click "Deploy"

1. ✅ Vercel clones your GitHub repo
2. ✅ Reads `vercel.json` configuration
3. ✅ Installs frontend dependencies (`npm ci`)
4. ✅ Builds frontend with Vite (`npm run build`)
5. ✅ Generates frontend static files
6. ✅ Creates serverless function for Laravel API
7. ✅ Routes `/api/*` to Laravel, other routes to frontend
8. ✅ Application goes live!

---

## 🆘 Troubleshooting

### "502 Bad Gateway"

- Frontend loads but API has issues
- Check environment variables are set
- Verify Supabase database is accessible
- View Vercel logs: Dashboard → Deployments → Latest → Logs

### "Cannot POST /api/login"

- Frontend API URL might be wrong
- Update `VITE_API_URL` environment variable
- Redeploy

### "Connect to database failed"

- Verify `DB_PASSWORD` is correct
- Check Supabase project is running
- Ensure all DB\_\* variables are set

### "Module not found: axios"

- Frontend dependencies issue
- Clear Vercel build cache: Settings → Git → Clear cache → Redeploy

---

## 📊 Monitoring

### View Live Logs

```bash
npm i -g vercel
vercel logs YOUR_PROJECT_NAME --follow
```

### Check Deployments

Dashboard → Deployments tab shows all versions

### Enable Preview URLs

Each GitHub PR automatically gets a preview URL for testing!

---

## 🔐 Custom Domain (Optional)

1. Dashboard → Settings → Domains
2. Add your domain
3. Update DNS records (Vercel shows exact steps)
4. Update `VITE_API_URL` to use your domain

---

## 📚 Full Guides

- **Detailed Setup**: See `VERCEL_DEPLOYMENT.md` in project root
- **General Deployment**: See `DEPLOYMENT.md` in project root
- **Supabase Setup**: See `SETUP_SUMMARY.md` in project root

---

## 🎊 Success! You're Deployed!

Your attendance system is now live on Vercel with:

- ✅ Vue.js frontend with Vite
- ✅ Laravel API backend
- ✅ Supabase PostgreSQL database
- ✅ Automatic deployments from GitHub
- ✅ Free SSL certificate

**Next steps:**

1. Test login functionality
2. Run database migrations
3. Configure custom domain (optional)
4. Monitor logs and performance
5. Set up database backups

---

## 💬 Need Support?

- Vercel Docs: https://vercel.com/docs
- Laravel Docs: https://laravel.com/docs
- Supabase Docs: https://supabase.com/docs
- GitHub Project: https://github.com/Chigybillionz/attendance_sytem

**You're all set to deploy! 🚀**
