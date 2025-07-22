# 🚀 Bagisto Deployment Guide - Hostinger + GitHub

## 📋 Prerequisites

1. **Hostinger Account** with:
   - Web Hosting plan (PHP 8.1+ support)
   - MySQL database
   - SSH access (recommended)

2. **GitHub Account** with your project repository

3. **Domain name** pointing to Hostinger

## 🔧 Step-by-Step Setup

### Step 1: Prepare Your Local Project

1. **Initialize Git Repository** (if not already done):
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   ```

2. **Push to GitHub**:
   ```bash
   git remote add origin https://github.com/yourusername/your-repo-name.git
   git push -u origin main
   ```

### Step 2: Set Up Hostinger

1. **Create Database**:
   - Go to Hostinger Control Panel
   - Navigate to "Databases" → "MySQL Databases"
   - Create a new database
   - Note down: Database name, username, password, host

2. **Set Up Domain**:
   - Point your domain to Hostinger nameservers
   - Wait for DNS propagation (up to 24 hours)

3. **Enable SSH Access** (if available):
   - Go to "Advanced" → "SSH Access"
   - Generate SSH key or enable password access

### Step 3: Configure GitHub Actions (Auto-Deployment)

1. **Create GitHub Actions Workflow**:
   Create file: `.github/workflows/deploy.yml`

   ```yaml
   name: Deploy to Hostinger
   
   on:
     push:
       branches: [ main ]
   
   jobs:
     deploy:
       runs-on: ubuntu-latest
       
       steps:
       - uses: actions/checkout@v3
       
       - name: Deploy to Hostinger
         uses: appleboy/ssh-action@v0.1.5
         with:
           host: ${{ secrets.HOST }}
           username: ${{ secrets.USERNAME }}
           password: ${{ secrets.PASSWORD }}
           port: ${{ secrets.PORT }}
           script: |
             cd public_html
             git pull origin main
             chmod +x deploy.sh
             ./deploy.sh
   ```

2. **Add GitHub Secrets**:
   - Go to your GitHub repo → Settings → Secrets and variables → Actions
   - Add these secrets:
     - `HOST`: Your Hostinger server IP
     - `USERNAME`: Your Hostinger username
     - `PASSWORD`: Your Hostinger password
     - `PORT`: SSH port (usually 22)

### Step 4: Initial Server Setup

1. **SSH into Hostinger**:
   ```bash
   ssh username@your-server-ip
   ```

2. **Clone Your Repository**:
   ```bash
   cd public_html
   git clone https://github.com/yourusername/your-repo-name.git .
   ```

3. **Set Up Environment**:
   ```bash
   cp .env.production .env
   # Edit .env with your database credentials
   nano .env
   ```

4. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

5. **Run Initial Deployment**:
   ```bash
   chmod +x deploy.sh
   ./deploy.sh
   ```

### Step 5: Database Setup

1. **Import Your Database**:
   - Export your local database: `mysqldump -u root -p your_database > backup.sql`
   - Import to Hostinger via phpMyAdmin or SSH

2. **Update Database Configuration**:
   - Edit `.env` file with Hostinger database credentials
   - Run migrations: `php artisan migrate --force`

### Step 6: Configure Web Server

1. **Set Document Root**:
   - Ensure `public_html` points to the `public` folder
   - Or configure `.htaccess` for proper routing

2. **Set Permissions**:
   ```bash
   chmod -R 755 storage bootstrap/cache
   chmod -R 755 public/storage
   ```

## 🔄 Auto-Deployment Workflow

After setup, every time you push to GitHub:

1. **GitHub Actions** will automatically trigger
2. **SSH into Hostinger** and pull latest changes
3. **Run deployment script** to:
   - Install dependencies
   - Build assets
   - Clear caches
   - Run migrations
   - Optimize for production

## 🛠️ Manual Deployment Commands

If you need to deploy manually:

```bash
# SSH into Hostinger
ssh username@your-server-ip

# Navigate to project
cd public_html

# Pull latest changes
git pull origin main

# Run deployment script
./deploy.sh
```

## 🔧 Troubleshooting

### Common Issues:

1. **Permission Denied**:
   ```bash
   chmod -R 755 storage bootstrap/cache
   chmod -R 755 public/storage
   ```

2. **Database Connection**:
   - Verify database credentials in `.env`
   - Check if database exists and is accessible

3. **Asset Loading Issues**:
   ```bash
   npm run build
   cd packages/Webkul/Shop && npm run build
   cd packages/Webkul/Admin && npm run build
   ```

4. **Cache Issues**:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   php artisan responsecache:clear
   ```

## 📞 Support

- **Hostinger Support**: For hosting-related issues
- **GitHub Issues**: For code-related problems
- **Bagisto Documentation**: For framework-specific questions

## 🔒 Security Notes

1. **Never commit** `.env` files to Git
2. **Use strong passwords** for database and admin accounts
3. **Keep dependencies updated** regularly
4. **Enable SSL/HTTPS** for production
5. **Regular backups** of database and files

---

**🎉 Your Bagisto store is now live with auto-deployment!** 