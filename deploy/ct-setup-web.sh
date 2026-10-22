#!/usr/bin/env bash
# Dijalankan DI DALAM kontainer pkbm-web (Debian 12). Dipanggil oleh setup-proxmox-pkbm-web.sh.
set -euo pipefail
export DEBIAN_FRONTEND=noninteractive
# pct exec jalan tanpa HOME/PATH lengkap; composer butuh HOME.
export HOME=/root COMPOSER_HOME=/root/.composer COMPOSER_ALLOW_SUPERUSER=1
export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
export LC_ALL=C.UTF-8 LANG=C.UTF-8
APP_DIR=/var/www/pkbm
APP_URL="${APP_URL:?}"
REPO="${REPO:?}"
BRANCH="${BRANCH:-main}"
REF="${REF:-$BRANCH}"   # commit/branch yang dipasang sekarang
log() { echo -e "\n\033[1;34m==> $*\033[0m"; }

log "Install paket dasar"
apt-get update -q
apt-get install -y -q curl ca-certificates gnupg lsb-release unzip git nginx mariadb-server cron >/dev/null

log "Install PHP 8.3 (repo packages.sury.org)"
if [ ! -f /etc/apt/sources.list.d/php.list ]; then
  curl -fsSL https://packages.sury.org/php/apt.gpg -o /usr/share/keyrings/sury-php.gpg
  echo "deb [signed-by=/usr/share/keyrings/sury-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list
  apt-get update -q
fi
apt-get install -y -q php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl \
  php8.3-zip php8.3-gd php8.3-intl php8.3-bcmath php8.3-sqlite3 >/dev/null
for ini in /etc/php/8.3/fpm/php.ini /etc/php/8.3/cli/php.ini; do
  sed -i 's/^upload_max_filesize.*/upload_max_filesize = 64M/; s/^post_max_size.*/post_max_size = 70M/; s/^memory_limit.*/memory_limit = 256M/' "$ini"
  sed -i 's#^;\?date.timezone.*#date.timezone = Asia/Jakarta#' "$ini"
done
timedatectl set-timezone Asia/Jakarta 2>/dev/null || ln -sf /usr/share/zoneinfo/Asia/Jakarta /etc/localtime

log "Install Composer"
if [ ! -x /usr/local/bin/composer ]; then
  curl -fsSL https://getcomposer.org/installer -o /tmp/composer-setup.php
  php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
fi
/usr/local/bin/composer --version

log "Siapkan database MariaDB"
systemctl enable --now mariadb >/dev/null
CRED=/root/pkbm-web-credentials.txt
if [ ! -f "$CRED" ]; then
  DB_PASS="$(openssl rand -hex 16)"
  ADMIN_PASS="$(openssl rand -base64 12 | tr -d '/+=' | cut -c1-12)"
  mysql -e "CREATE DATABASE IF NOT EXISTS pkbm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    CREATE USER IF NOT EXISTS 'pkbm'@'localhost' IDENTIFIED BY '$DB_PASS';
    GRANT ALL PRIVILEGES ON pkbm.* TO 'pkbm'@'localhost'; FLUSH PRIVILEGES;"
  printf 'DB_PASS=%s\nADMIN_PASS=%s\n' "$DB_PASS" "$ADMIN_PASS" > "$CRED"
  chmod 600 "$CRED"
fi
. "$CRED"

log "Pasang aplikasi Laravel"
mkdir -p "$APP_DIR"
echo "REPO=$REPO" > /etc/pkbm-web.conf
echo "BRANCH=$BRANCH" >> /etc/pkbm-web.conf
curl -fsSL "https://codeload.github.com/$REPO/tar.gz/$REF" | tar -xz -C "$APP_DIR" --strip-components=2 --wildcards '*/web/*'
cd "$APP_DIR"
if [ ! -f .env ]; then
  cp .env.example .env
  sed -i "s#^APP_ENV=.*#APP_ENV=production#; s#^APP_DEBUG=.*#APP_DEBUG=false#; s#^APP_URL=.*#APP_URL=$APP_URL#" .env
  sed -i "s#^DB_CONNECTION=.*#DB_CONNECTION=mariadb#; s/^# DB_/DB_/" .env
  sed -i "s#^DB_DATABASE=.*#DB_DATABASE=pkbm#; s#^DB_USERNAME=.*#DB_USERNAME=pkbm#; s#^DB_PASSWORD=.*#DB_PASSWORD=$DB_PASS#" .env
  sed -i "s#^SESSION_DRIVER=.*#SESSION_DRIVER=database#; s#^LOG_LEVEL=.*#LOG_LEVEL=warning#" .env
  echo "ADMIN_PASSWORD=$ADMIN_PASS" >> .env
  echo "SEED_DEMO=${SEED_DEMO:-false}" >> .env
fi
/usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction
grep -q '^APP_KEY=base64' .env || php artisan key:generate --force
php artisan migrate --force
if [ ! -f /root/.pkbm-seeded ]; then
  php artisan db:seed --force
  touch /root/.pkbm-seeded
fi
php artisan storage:link 2>/dev/null || true
php artisan optimize
php artisan filament:optimize
chown -R www-data:www-data storage bootstrap/cache
mkdir -p storage/app/public && chown -R www-data:www-data storage/app

log "Konfigurasi nginx"
cat > /etc/nginx/sites-available/pkbm <<'NGINX'
server {
    listen 80 default_server;
    root /var/www/pkbm/public;
    index index.php;
    client_max_body_size 70M;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 120;
    }
    location ~ /\.(?!well-known) { deny all; }
}
NGINX
ln -sf /etc/nginx/sites-available/pkbm /etc/nginx/sites-enabled/pkbm
rm -f /etc/nginx/sites-enabled/default
nginx -t -q && systemctl reload nginx
systemctl restart php8.3-fpm

log "Backup database harian (simpan 14 hari di /var/backups/pkbm)"
cat > /usr/local/bin/backup-pkbm <<'BK'
#!/usr/bin/env bash
set -e
mkdir -p /var/backups/pkbm
STAMP=$(date +%Y%m%d-%H%M)
mysqldump --single-transaction pkbm | gzip > /var/backups/pkbm/db-$STAMP.sql.gz
tar -czf /var/backups/pkbm/files-$STAMP.tar.gz -C /var/www/pkbm/storage app
find /var/backups/pkbm -type f -mtime +14 -delete
BK
chmod +x /usr/local/bin/backup-pkbm
echo "30 2 * * * root /usr/local/bin/backup-pkbm" > /etc/cron.d/pkbm-backup

log "Perintah update aplikasi: update-pkbm-web"
cat > /usr/local/bin/update-pkbm-web <<'UP'
#!/usr/bin/env bash
# Ambil kode terbaru dari GitHub lalu pasang. Pakai: update-pkbm-web
set -euo pipefail
. /etc/pkbm-web.conf
cd /var/www/pkbm
/usr/local/bin/backup-pkbm
php artisan down || true
curl -fsSL "https://codeload.github.com/$REPO/tar.gz/$BRANCH" | tar -xz -C /var/www/pkbm --strip-components=2 --wildcards '*/web/*'
export HOME=/root COMPOSER_HOME=/root/.composer COMPOSER_ALLOW_SUPERUSER=1
/usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction --quiet
php artisan migrate --force
php artisan optimize
php artisan filament:optimize
chown -R www-data:www-data storage bootstrap/cache
systemctl reload php8.3-fpm
php artisan up
echo "Update selesai."
UP
chmod +x /usr/local/bin/update-pkbm-web
echo "SELESAI"
