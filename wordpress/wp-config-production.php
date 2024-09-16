<?php

// ** Production site database settings - You can get this info from your web host ** //

/** The name of the database for WordPress */
define('DB_NAME', '{{ $config.DatabaseName }}');

/** Database username */
define('DB_USER', '{{ $config.DatabaseUsername }}');

/** Database password */
define('DB_PASSWORD', '{{ $config.DatabasePassword }}');

/** Database hostname */
define('DB_HOST', 'db');

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', '{{ $config.AuthKey }}');
define('SECURE_AUTH_KEY', '{{ $config.SecureAuthKey }}');
define('LOGGED_IN_KEY', '{{ $config.LoggedInKey }}');
define('NONCE_KEY', '{{ $config.NonceKey }}');
define('AUTH_SALT', '{{ $config.AuthSalt }}');
define('SECURE_AUTH_SALT', '{{ $config.SecureAuthSalt }}');
define('LOGGED_IN_SALT', '{{ $config.LoggedInSalt }}');
define('NONCE_SALT', '{{ $config.NonceSalt }}');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
if (!isset($table_prefix) || empty($table_prefix)) {
    // phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
    $table_prefix = 'wp_';
    // phpcs:enable
}

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY', false);
