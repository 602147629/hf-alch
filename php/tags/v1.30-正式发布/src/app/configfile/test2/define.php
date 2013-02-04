<?php
define('SERVER_ID', '992');

define('APP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'app');
define('CONFIG_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'config');
define('MODULES_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'modules');
define('LIB_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'lib');
define('DOC_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'www');
define('LOG_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'logs');
define('TEMP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'temp');
define('SMARTY_TEMPLATES_C', ROOT_DIR . DIRECTORY_SEPARATOR . 'templates_c');

define('APP_ID', '196608');
define('APP_KEY', '1bc7f77afc8146dcbfd0a919f5bd0513');
define('APP_SECRET', 'ce66a768647c4121b0ed2616e0b6f6c4');
define('APP_NAME', 'testalchemyto');

define('PLATFORM', 'renren');
define('APP_SERVER_TYPE', 2); //1 正服  2 测服 3 开发服
define('PREX_APC', 'alchemy:rr');
define('DATABASE_NODE_NUM', 1);
define('MEMCACHED_NODE_NUM', 1);
define('ENABLE_STATIC_GZ', false);
define('ENABLE_HFC', true);
define('ENABLE_DEBUG', true);
define('ENABLE_FIGHT_DB_LOG', true);//战斗记录是否入db

define('HOST', 'http://testalchemy-rr2.happyfish001.com');
define('STATIC_HOST', 'http://testalchemystatic.happyfish001.com/renren2');

define('SEND_ACTIVITY', true);
define('SEND_MESSAGE', false);

define('APP_STATUS', 1);
define('APP_STATUS_DEV', 0);

define('ECODE_NUM', 4);