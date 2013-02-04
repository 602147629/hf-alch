<?php
define('ROOT_DIR', realpath('../'));
require(ROOT_DIR . '/bin/config.php');
try {
	Hapyfish2_Tool_Repair::repair();
}
catch (Exception $e) {
	info_log($e->getMessage(),'repair');
}