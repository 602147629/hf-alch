<?php
define('ROOT_DIR', realpath('../'));
require(ROOT_DIR . '/bin/config.php');
try {
//	Hapyfish2_Tool_Repair::repair();
//$num = Hapyfish2_Tool_Repair::sendCoin();
$num = Hapyfish2_Tool_Repair::sendPackage();
echo $num;
}
catch (Exception $e) {
	info_log($e->getMessage(),'repair');
}