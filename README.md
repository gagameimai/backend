# Meimai
## ubuntu部署

### 安裝mysql 8.0
```bash
apt install mysql-server
```
本地DB使用者root可不設密碼登入，但若要設定密碼，可以將密碼規則設最低
查看密碼策略
```sql
SHOW VARIABLES LIKE 'validate_password%';
```
若validate_password.policy顯示是LOW以上，建議更改
```sql
set global validate_password.policy=LOW;
```
設定新密碼
```sql
ALTER USER 'root'@'localhost' IDENTIFIED BY 'ilovemei';
```
未來在linux登入，不能只輸入mysql
```bash
mysql -u root -p
```

### 安裝php
```bash
apt install php 8.1;
apt install php8.1 php8.1-cli php8.1-fpm php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip php8.1-gd;
```
修改預設限制
vi /etc/php/8.1/fpm/php.ini
```bash
upload_max_filesize = 512M
post_max_size = 512M
```
### 安裝nginx
```bash
apt install nginx
```
移除預設檔案的軟連結
cd /etc/nginx/sites-enable
```bash
unlink default
```
新增mei檔案軟連結
```bash
ln -s /etc/nginx/sites-enable/mei
```
本地建立localhost憑證，可在根目錄產生，正式機用certbot
```bash
# 1. 產生私鑰 (private key)
openssl genrsa -out localhost.key 2048

# 2. 產生憑證簽署請求 (CSR) 與自簽憑證 (CRT)
# -days 365 代表憑證有效天數為 365 天
openssl req -new -x509 -key localhost.key -out localhost.crt -days 365 -subj "/CN=localhost"
```
檔案放置到 Nginx 指定目錄
```bash
mkdir -p /etc/nginx/ssl
cp localhost.crt /etc/nginx/ssl/
cp localhost.key /etc/nginx/ssl/
```
做完以上，重啟nginx
```bash
systemctl restart nginx
```

## laravel部屬
### 程式碼的位子
正式機
/var/wwww/mei/admin
本地放在
/var/www/mei/backend
### 安裝composer套件
cd /var/www/mei/backend
```bash
composer install
```
### 複製環境檔
```bash
cp .env.example .env
```
### 產生 APP_KEY（若 .env 還沒有）
```bash
php artisan key:generate
```
### 資料庫建置
```bash
php artisan migrate
```
### storage 的軟連結
```bash
php artisan storage:link
```
### filemanager 的前端資源搬到 public/vendor/
```bash
php artisan vendor:publish --tag=lfm_public
```
### 建立後台管理者
```bash
php artisan tools:admin-user
```

## 套件使用

- [UniSharp/laravel-filemanager](https://github.com/UniSharp/laravel-filemanager)

## windows wsl設定
### laravel-filemanager功能在wsl不支援POSIX的chmod
vi /etc/wsl.conf
```bash
[automount]
options = "metadata"
```
