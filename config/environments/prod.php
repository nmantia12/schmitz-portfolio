<?php
/* Production */
define('DISABLE_WP_CRON', false);
ini_set('display_errors', 0);
define('WP_DEBUG', false);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', false);
define('DISALLOW_FILE_MODS', true); // this disables all file modifications including updates and update notifications
define('WP_CACHE', true);
