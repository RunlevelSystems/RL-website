# Database Configuration

## Overview
The WDS website uses a shared database with the GameServer Panel for staff authentication. This ensures a single source of truth for user credentials and admin access.

## Setup Instructions

### 1. Configure Database Connection

1. Copy the example configuration file:
   ```bash
   cp includes/db-config.example.php includes/db-config.php
   ```

2. Edit `includes/db-config.php` with your actual database credentials:
   ```php
   define('DB_HOST', 'your_mysql_host');      // MySQL server hostname
   define('DB_NAME', 'panel');                // Database name
   define('DB_USER', 'your_db_user');         // Database username
   define('DB_PASS', 'your_secure_password'); // Database password
   define('DB_CHARSET', 'utf8mb4');           // Character set
   ```

3. **IMPORTANT**: Never commit `db-config.php` to version control. It contains sensitive credentials.

### 2. Database Schema

The website uses the existing OpenGamePanel database schema. Required table:

```sql
ogp_users (
    user_id INT PRIMARY KEY,
    users_login VARCHAR(255),
    users_password VARCHAR(255),  -- MD5 hashed
    users_role VARCHAR(50)         -- 'admin' for staff access
)
```

### 3. User Access Levels

The authentication system checks the `users_role` field:
- **admin**: Full access to staff areas (login, staff-info page)
- Other roles: No access to staff sections

### 4. Security Considerations

#### Password Hashing
- Currently uses MD5 (legacy compatibility with OGP)
- **SECURITY NOTE**: MD5 is not secure for new applications
- Consider migrating to bcrypt/Argon2 for new user accounts

#### Database User Permissions
Create a dedicated database user with minimal permissions:

```sql
-- Create read-only user for authentication
CREATE USER 'wds_auth'@'localhost' IDENTIFIED BY 'secure_password';

-- Grant only SELECT on ogp_users table
GRANT SELECT ON panel.ogp_users TO 'wds_auth'@'localhost';

-- Apply permissions
FLUSH PRIVILEGES;
```

#### Connection Security
- Use SSL/TLS for database connections in production
- Restrict database access by IP address
- Use firewall rules to limit MySQL port (3306) access

### 5. Environment Variables (Recommended)

For enhanced security, consider using environment variables instead of hardcoded credentials:

```php
// In db-config.php
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'panel');
define('DB_USER', getenv('DB_USER') ?: 'dbuser');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');
```

Set environment variables in your server configuration (Apache .htaccess or Nginx config):

```apache
# Apache .htaccess
SetEnv DB_HOST "mysql.example.com"
SetEnv DB_NAME "panel"
SetEnv DB_USER "wds_auth"
SetEnv DB_PASS "secure_password"
```

```nginx
# Nginx server block
location ~ \.php$ {
    fastcgi_param DB_HOST "mysql.example.com";
    fastcgi_param DB_NAME "panel";
    fastcgi_param DB_USER "wds_auth";
    fastcgi_param DB_PASS "secure_password";
}
```

### 6. Testing the Connection

Test your database configuration:

```php
<?php
define('WDS_SYSTEM', true);
require_once 'includes/db-config.php';

$db = getDatabaseConnection();
if ($db) {
    echo "✓ Database connection successful!\n";
    
    // Test admin user query
    $stmt = $db->query("SELECT COUNT(*) as count FROM ogp_users WHERE users_role = 'admin'");
    $result = $stmt->fetch();
    echo "✓ Found {$result['count']} admin user(s)\n";
} else {
    echo "✗ Database connection failed!\n";
}
?>
```

### 7. Troubleshooting

#### Connection Refused
- Check MySQL server is running
- Verify hostname and port (default 3306)
- Check firewall rules

#### Access Denied
- Verify username and password
- Check user has correct permissions
- Ensure user can connect from your web server's IP

#### Table Not Found
- Verify database name is correct
- Check that OpenGamePanel is installed and configured
- Ensure user has SELECT permission on ogp_users table

### 8. Production Checklist

- [ ] Database credentials are in `db-config.php` (not the example file)
- [ ] `db-config.php` is NOT in version control
- [ ] Database user has minimal required permissions
- [ ] SSL/TLS enabled for database connections
- [ ] Error logging configured (not displaying to users)
- [ ] Connection pooling configured if high traffic expected
- [ ] Regular database backups scheduled
- [ ] Monitoring set up for connection failures

---

## Support

For issues with database configuration:
1. Check server error logs
2. Review MySQL error logs
3. Verify OpenGamePanel installation
4. Contact system administrator

**Note**: This authentication system is designed for staff/admin access only, not public user registration.
