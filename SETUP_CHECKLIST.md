# ✅ Hostinger + GitHub Setup Checklist

## 📋 Pre-Deployment Checklist

### Local Setup
- [ ] Project is working locally
- [ ] All assets are built (`npm run build`)
- [ ] Database is backed up
- [ ] Git repository is initialized
- [ ] Code is committed and pushed to GitHub

### Hostinger Setup
- [ ] Hosting plan purchased (PHP 8.1+)
- [ ] Domain pointed to Hostinger
- [ ] Database created
- [ ] SSH access enabled (if available)
- [ ] Database credentials noted down

### GitHub Setup
- [ ] Repository created on GitHub
- [ ] Code pushed to GitHub
- [ ] GitHub Actions workflow added (`.github/workflows/deploy.yml`)
- [ ] GitHub Secrets configured:
  - [ ] `HOST` (Hostinger server IP)
  - [ ] `USERNAME` (Hostinger username)
  - [ ] `PASSWORD` (Hostinger password)
  - [ ] `PORT` (SSH port, usually 22)

## 🚀 Deployment Steps

### Step 1: Initial Server Setup
```bash
# SSH into Hostinger
ssh username@your-server-ip

# Clone repository
cd public_html
git clone https://github.com/yourusername/your-repo-name.git .

# Set up environment
cp .env.production .env
nano .env  # Edit with your database credentials

# Generate app key
php artisan key:generate

# Run deployment
chmod +x deploy.sh
./deploy.sh
```

### Step 2: Database Import
```bash
# Export local database
mysqldump -u root -p your_database > backup.sql

# Import to Hostinger (via phpMyAdmin or SSH)
mysql -u username -p database_name < backup.sql
```

### Step 3: Test Deployment
- [ ] Website loads correctly
- [ ] Admin panel accessible
- [ ] Products display properly
- [ ] Images load correctly
- [ ] Forms work (contact, checkout)

## 🔄 Auto-Deployment Test

### Test GitHub Actions
1. Make a small change locally
2. Commit and push to GitHub
3. Check GitHub Actions tab
4. Verify deployment completes successfully
5. Check website for changes

## 🔧 Common Issues & Solutions

### Permission Issues
```bash
chmod -R 755 storage bootstrap/cache
chmod -R 755 public/storage
```

### Database Connection
- Verify credentials in `.env`
- Check database exists and is accessible
- Test connection manually

### Asset Loading
```bash
npm run build
cd packages/Webkul/Shop && npm run build
cd packages/Webkul/Admin && npm run build
```

### Cache Issues
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan responsecache:clear
```

## 📞 Support Contacts

- **Hostinger Support**: For hosting issues
- **GitHub Support**: For repository issues
- **Bagisto Community**: For framework issues

## 🔒 Security Checklist

- [ ] `.env` file not committed to Git
- [ ] Strong database passwords
- [ ] SSL/HTTPS enabled
- [ ] Admin passwords changed
- [ ] Regular backups scheduled

---

**🎉 Once all items are checked, your Bagisto store is live with auto-deployment!** 