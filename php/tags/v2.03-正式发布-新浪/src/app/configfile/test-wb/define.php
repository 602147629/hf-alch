<?php
define('SERVER_ID', '99');

define('APP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'app');
define('CONFIG_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'config');
define('MODULES_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'modules');
define('LIB_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'lib');
define('DOC_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'www');
define('LOG_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'logs');
define('STAT_LOG_DIR', '/home/admin/logs/alchemy/weibo/stat');
define('TEMP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'temp');
define('SMARTY_TEMPLATES_C', ROOT_DIR . DIRECTORY_SEPARATOR . 'templates_c');

define('APP_ID', '3507232293');
define('APP_KEY', '3507232293');
define('APP_SECRET', 'e58f123a534962319be88c38afb5feda');
define('APP_NAME', 'alchemy_dev');

define('PLATFORM', 'sinaweibo');
define('WB_RANK_ARENA', 1);
define('APP_SERVER_TYPE', 2); //1 正服  2 测服 3 开发服
define('PREX_APC', 'alchemy:wb');
define('DATABASE_NODE_NUM', 4);
define('MEMCACHED_NODE_NUM', 10);
define('ENABLE_STATIC_GZ', false);
define('ENABLE_HFC', true);
define('ENABLE_DEBUG', true);
define('ENABLE_FIGHT_DB_LOG', true);//战斗记录是否入db
define('BOTFRIEND', '10081,10052');//公共好友

define('HOST', 'http://testalchemy-wb.happyfish001.com');
define('STATIC_HOST', 'http://testalchemystatic.happyfish001.com/weibo');

define('SEND_ACTIVITY', true);
define('SEND_MESSAGE', false);

define('APP_STATUS', 1);
define('APP_STATUS_DEV', 0);

define('ECODE_NUM', 4);