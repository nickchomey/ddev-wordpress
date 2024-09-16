<?php

// wp-config-ddev.php not needed

/*
Dynamic site configuration depending on the environment type.

Requires/includes wp-config-{WP_ENVIRONMENT_TYPE}.php if we're not in production, to pre-emptively override the production defaults below

DDEV sets WP_ENVIRONMENT_TYPE to "development" by default and has created a wp-config-development.php file adjacent to wp-config.php.

If you would like to set other environment configs, you can set WP_ENVIRONMENT_TYPE to "local" or "staging" in your project's .ddev/config.yaml's web_environment parameter. Then create a commensurate wp-config-{WP_ENVIRONMENT_TYPE}.php file to be loaded in that environment.

*/

$env_type = getenv("WP_ENVIRONMENT_TYPE") ?: "production";

$environment_settings = dirname(__FILE__) . "/wp-config-$env_type.php";
if (is_readable($environment_settings)) {
    require_once($environment_settings);
} else {
    // exit the request until the environment_type and config files are set up appropriately

    http_response_code(500);
    echo ("No $environment_settings file found. Look at wp-config.php and create a wp-config-$env_type.php file, and set the WP_ENVIRONMENT_TYPE.");
    exit();
}


/* That's all, stop editing! Happy publishing. */
/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}
/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
