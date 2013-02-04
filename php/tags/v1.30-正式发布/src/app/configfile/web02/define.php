<?php
define('SERVER_ID', '1002');

define('APP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'app');
define('CONFIG_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'config');
define('MODULES_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'modules');
define('LIB_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'lib');
define('DOC_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'www');
define('LOG_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'logs');
define('STAT_LOG_DIR', '/home/admin/logs/alchemy/kaixin/stat');
define('TEMP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'temp');
define('SMARTY_TEMPLATES_C', ROOT_DIR . DIRECTORY_SEPARATOR . 'templates_c');

define('APP_ID', '100036352');
define('APP_KEY', '33721651799924a3fea5d3d9b7b46fc9');
define('APP_SECRET', 'e64cc747857a0b699e0b0eff5f6f77c9');
define('APP_NAME', 'alchemy');

define('PLATFORM', 'kaixin');
define('APP_SERVER_TYPE', 1); //1 正服  2 测服 3 开发服
define('PREX_APC', 'alchemy:kx');
define('DATABASE_NODE_NUM', 4);
define('MEMCACHED_NODE_NUM', 10);
define('ENABLE_STATIC_GZ', false);
define('ENABLE_HFC', true);
define('ENABLE_DEBUG', true);
define('ENABLE_FIGHT_DB_LOG', false);//战斗记录是否入db

define('HOST', 'http://ackx.happyfishgame.com.cn');
define('STATIC_HOST', 'http://accdn.playwhale.com/kaixin');

define('SEND_ACTIVITY', true);
define('SEND_MESSAGE', false);

define('APP_STATUS', 1);
define('APP_STATUS_DEV', 0);

define('ECODE_NUM', 4);