<?php
global $wpdb;

define('PUVC_ROOT_PATH', plugin_dir_path(__FILE__));
define('PUVC_DB_TABLE', $wpdb->prefix . 'puvc');
define('PUVC_DAYS', 30);
define('PUVC_ONE_DAY_IN_SECONDS', 86400);
define('PUVC_COOKIE_DURATION', time() + (PUVC_ONE_DAY_IN_SECONDS * PUVC_DAYS));
