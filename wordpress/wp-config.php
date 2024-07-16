<?php
{{ $config := . }}/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// wp-config-ddev.php not needed

/*
Dynamic site configuration depending on the environment type.

Requires/includes wp-config-{WP_ENVIRONMENT_TYPE}.php if we're not in production, to pre-emptively override the production defaults below

DDEV sets WP_ENVIRONMENT_TYPE to "development" by default and has created a wp-config-development.php file adjacent to wp-config.php.

If you would like to set other environment configs, you can set WP_ENVIRONMENT_TYPE to "local" or "staging" in your project's .ddev/config.yaml's web_environment parameter. Then create a commensurate wp-config-{WP_ENVIRONMENT_TYPE}.php file to be loaded in that environment.

*/

$env_type = getenv('WP_ENVIRONMENT_TYPE') ?: 'production';

if ($env_type != "production" ) {
    $ddev_settings = dirname(__FILE__) . "/wp-config-$env_type.php";
    if (is_readable($ddev_settings)){
        require_once($ddev_settings);
    } else {
        //TODO: this is probably not going to actually log anything, because logging gets set up later.
        error_log("No $ddev_settings file found. Using defaults. If the site is not working, look at wp-config.php and create a wp-config-$env_type.php file, and set the WP_ENVIRONMENT_TYPE.");
        
        // exit the request until the environment_type and config files are set up appropriately
        
        http_response_code(500);
        echo("No $ddev_settings file found. Using defaults. If the site is not working, look at wp-config.php and create a wp-config-$env_type.php file, and set the WP_ENVIRONMENT_TYPE.");
        exit();
    }
}


// ** Production site database settings - You can get this info from your web host ** //

/** The name of the database for WordPress */
defined('DB_NAME') || define('DB_NAME', '{{ $config.DatabaseName }}');

/** Database username */
defined('DB_USER') || define('DB_USER', '{{ $config.DatabaseUsername }}');

/** Database password */
defined('DB_PASSWORD') || define('DB_PASSWORD', '{{ $config.DatabasePassword }}');

/** Database hostname */
defined('DB_HOST') || define('DB_HOST', 'db');

/** Database charset to use in creating database tables. */
defined('DB_CHARSET') || define('DB_CHARSET', '{{ $config.DbCharset }}');

/** The database collate type. Don't change this if in doubt. */
defined('DB_COLLATE') || define('DB_COLLATE', '{{ $config.DbCollate }}');


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
define( 'AUTH_KEY', '{{ $config.AuthKey }}' );
define( 'SECURE_AUTH_KEY', '{{ $config.SecureAuthKey }}' );
define( 'LOGGED_IN_KEY', '{{ $config.LoggedInKey }}' );
define( 'NONCE_KEY', '{{ $config.NonceKey }}' );
define( 'AUTH_SALT', '{{ $config.AuthSalt }}' );
define( 'SECURE_AUTH_SALT', '{{ $config.SecureAuthSalt }}' );
define( 'LOGGED_IN_SALT', '{{ $config.LoggedInSalt }}' );
define( 'NONCE_SALT', '{{ $config.NonceSalt }}' );

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
defined('WP_DEBUG') || define('WP_DEBUG', false);
defined('WP_DEBUG_LOG') || define('WP_DEBUG_LOG', false);
defined('WP_DEBUG_DISPLAY') || define('WP_DEBUG_DISPLAY', false);

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '{{ $config.AbsPath }}');
}

/** Sets up WordPress vars and included files. */
if ( file_exists( ABSPATH . '/wp-settings.php' ) ) {
	require_once ABSPATH . '/wp-settings.php';
}