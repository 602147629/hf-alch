<?php
define('SERVER_ID', '99');

define('APP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'app');
define('CONFIG_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'config');
define('MODULES_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'modules');
define('LIB_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'lib');
define('DOC_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'www');
define('LOG_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'logs');
define('TEMP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'temp');
define('STAT_LOG_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'stat');
define('SMARTY_TEMPLATES_C', ROOT_DIR . DIRECTORY_SEPARATOR . 'templates_c');

define('APP_ID', '100036458');
define('APP_KEY', '230783855027d754161bcbe6fe1288f8');
define('APP_SECRET', 'fdc87f1c2e75a4a8ca58886216411245');
define('APP_NAME', 'testalchemy');

define('PLATFORM', 'renren');
define('APP_SERVER_TYPE', 3); //1 正服  2 测服 3 开发服
define('PREX_APC', 'alchemy:rr');
define('DATABASE_NODE_NUM', 1);
define('MEMCACHED_NODE_NUM', 1);
define('ENABLE_STATIC_GZ', false);
define('ENABLE_HFC', false);
define('ENABLE_DEBUG', true);
define('ENABLE_FIGHT_DB_LOG', true);//战斗记录是否入db

define('HOST', 'http://devalchemyrenren.happyfish001.com');
define('STATIC_HOST', 'http://devstaticalchemy.happyfish001.com/renren');

define('SEND_ACTIVITY', true);
define('SEND_MESSAGE', false);

define('APP_STATUS', 1);
define('APP_STATUS_DEV', 0);

define('ECODE_NUM', 4);