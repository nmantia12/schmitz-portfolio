<?php
/* STAGING */
define('DISABLE_WP_CRON', false);
ini_set('display_errors', 0);
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', true);
define('SCRIPT_DEBUG', false);
define('DISALLOW_FILE_MODS', true); // this disables all file modifications including updates and update notifications
define('WP_CACHE', false); // Disable W3 Total Cache / WP Super Cache
define('TWO_FACTOR_DISABLE', true);
