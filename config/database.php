<?php
/**
 * Database Configuration
 * Market Place OutFit
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'marketplace_outfit');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Application settings
define('APP_NAME', 'Market Place OutFit');
define('APP_URL', 'http://localhost/web');

// Session settings
define('SESSION_LIFETIME', 3600); // 1 hour

// Upload settings
define('UPLOAD_PATH', __DIR__ . '/../assets/images/products/');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
