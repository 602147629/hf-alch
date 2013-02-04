<?php
define('SERVER_ID', '99');

define('APP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'app');
define('CONFIG_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'config');
define('MODULES_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'modules');
define('LIB_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'lib');
define('DOC_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'www');
define('LOG_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'logs');
define('TEMP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'temp');
define('SMARTY_TEMPLATES_C', ROOT_DIR . DIRECTORY_SEPARATOR . 'templates_c');

define('APP_ID', '139593');
define('APP_KEY', '2eee456824fd441f8ce2424689572c37');
define('APP_SECRET', '6ec4f1bc038c4ca9a772c7dfa59c3d37');
define('APP_NAME', 'devalchemy');

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