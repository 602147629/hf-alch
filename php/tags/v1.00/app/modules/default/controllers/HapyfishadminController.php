<?php

class HapyfishadminController extends Zend_Controller_Action
{

    //Admin Username & Password
    private $_admins = array('admin'=>'happyfish@2011',
    						 'jianghao'=>'1014@13052097027',
    						 'daqiang'=>'1011@alex.leung'
                       );

    private $_curAdmin;

	public function init()
	{
	    $loginU = $_SERVER['PHP_AUTH_USER'];
	    $loginP = $_SERVER['PHP_AUTH_PW'];
		if (!isset($loginU) || !isset($loginP)
            || !array_key_exists($loginU, $this->_admins) || $this->_admins[$loginU] != $loginP) {
			Header("WWW-Authenticate: Basic realm=Happy magic admin, Please Login");
			Header("HTTP/1.0 401 Unauthorized");

			echo <<<EOB
				<html><body>
				<h1>Rejected!</h1>
				<big>Wrong Username or Password!</big>
				</body></html>
EOB;
			exit;
		}

		$appInfo = Hapyfish2_Project_Bll_AppInfo::getAdvanceInfo();
	    $this->view->appTitle = $appInfo['app_title'];
		$this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
        $this->view->appId = APP_ID;
        $this->view->appKey = APP_KEY;
        $this->view->adminName = $loginU;
        $this->_curAdmin = $loginU;
	}

    protected function echoResult($data)
    {
        header("Cache-Control: no-store, no-cache, must-revalidate");
        echo json_encode($data);
        exit();
    }

	function vaild()
	{

	}

	function check()
	{

	}

	function indexAction()
	{
		$this->render();
	}

    function reloadbasicAction()
	{
	    $newVer = $this->_request->getParam('ver');
	    if ($newVer) {
	        $ok = Hapyfish2_Alchemy_Cache_Basic::setBasicVersion($newVer);
	    }

        $list = Hapyfish2_Tool_Server::getWebList();

		if (!empty($list)) {
			$host = str_replace('http://', '', HOST);
			foreach ($list as $server) {
				$url = 'http://' . $server['local_ip'] . '/zxtest/clearbascache';
				$result = Hapyfish2_Tool_Server::requestWeb($host, $url);
				echo $server['name']. '--' . $url . ':' . $result . '<br/>';
			}
		}
		echo "OK";
		exit;
	}

	function basicdataAction()
	{
	    $apiBasicVer = Hapyfish2_Alchemy_Cache_Basic::getBasicVersion();
	    $this->view->ver = $apiBasicVer;
        $this->view->tblist = Hapyfish2_Admin_Bll_Basic::getBasicTbList();
		$this->render();
	}

	function detaildataAction()
	{
        $tbName = $this->_request->getParam('table');
        $tbInfo = Hapyfish2_Admin_Bll_Basic::getBasicTbByName($tbName);
        if (!$tbInfo) {
            echo 'table not found,please check.';
            exit;
        }

        $cols = $tbInfo['column'];
        $colModel = $colNames = array();
        $colNames[] = '操作';
        $colModel[] = array('name'=>'actopt', 'index'=>'actopt', 'width'=>'80', 'sortable'=>false);

        //multikey table need
        $tbKeys = $tbInfo['key'];
        $aryTbKey = explode(',', $tbKeys);
	    if (count($aryTbKey) > 1) {
            $colNames[] = '键';
            $colModel[] = array('name'=>'mulKey', 'index'=>'mulKey', 'width'=>'1', 'sortable'=>true, 'key'=>true, 'sorttype'=>'text');
        }

        foreach ($cols as $key=>$col) {
            $colNames[] = $col;
            $colModel[] = array('name'=>$key, 'index'=>$key, 'width'=>'85', 'sortable'=>false, 'editable'=>true, 'edittype'=>'text');
        }


        $dal = Hapyfish2_Admin_Dal_Basic::getDefaultInstance();
        $lstData = $dal->getBasicList($tbName);
        if (count($aryTbKey) > 1) {
            foreach ($lstData as &$data) {
                $mulKey = '';
                foreach ($data as $col=>$val) {
                    if (in_array($col, $aryTbKey)) {
                        $mulKey .= '_'.$val;
                    }
                }
                $data['mulKey'] = substr($mulKey, 1);
            }
        }
        $this->view->lstData = json_encode($lstData);

        $canDel = isset($tbInfo['candel']) ? $tbInfo['candel'] : 0;
        $selDelList = array();
        if ($canDel) {
            foreach ($lstData as $key=>$data) {
                $selId = $data[$tbInfo['candel']];
                if (!array_key_exists($selId, $selDelList)) {
                    $selDelList[$selId] = $selId;
                }
            }
        }

        $this->view->tbShowName = $tbInfo['name'];
        $this->view->tbName = $tbName;
        $this->view->colNames = json_encode($colNames);
        $this->view->colModel = json_encode($colModel);
        $this->view->candel = $canDel;
        $this->view->selDelList = $selDelList;

		$this->render();
	}

	function getdataAction()
	{
	    $tbName = $this->_request->getParam('table');
	    $dal = Hapyfish2_Admin_Dal_Basic::getDefaultInstance();
        $lstData = $dal->getBasicList($tbName);
        echo json_encode($lstData);
        exit;
	}

    function savedataAction()
	{
	    $tbName = $this->_request->getParam('table');
	    $tbInfo = Hapyfish2_Admin_Bll_Basic::getBasicTbByName($tbName);
	    if (!$tbInfo) {
            echo 'table not found,please check.';
            exit;
        }

	    $cols = $tbInfo['column'];
	    $info = array();

        foreach ($cols as $key=>$col) {
            $info[$key] = $this->_request->getPost($key);
        }

   	    //multikey table need
        $tbKeys = $tbInfo['key'];
        $aryTbKey = explode(',', $tbKeys);
        if (count($aryTbKey) > 1) {
            $mulKey = $this->_request->getPost('id');
            if (strpos($mulKey, '_') > 0) {
                $aryTmp = explode('_', $mulKey);
                foreach ($aryTbKey as $idx=>$col) {
                    $info[$col] = $aryTmp[$idx];
                }
            }
        }

        try {
            $dal = Hapyfish2_Admin_Dal_Basic::getDefaultInstance();
            $rst = $dal->addInfo($tbName, $info);
        }
        catch(Exception $e) {
            echo 'Failed:'.$e->getMessage();
            exit;
        }
        //echo json_encode($rst);
        echo 'complete';
        exit;
	}

	function deldataAction()
	{
	    $tbName = $this->_request->getParam('table');
	    $field = $this->_request->getParam('key');
	    $selVal = $this->_request->getParam('selVal');
	    if (empty($tbName) || empty($field) || empty($selVal)) {
	        echo '删除失败，参数不正';
	        exit;
	    }

	    $dal = Hapyfish2_Admin_Dal_Basic::getDefaultInstance();
        $rst = $dal->deleteInfo($tbName, $field, $selVal);
        echo '副本地图' . $selVal . '数据删除成功！<br />';
        echo '<a href="#" onclick="parent.showDetail(\''. $tbName .'\');">》返回《</a>';
		exit;
	}

	function exportdataAction()
	{
	    $tbName = $this->_request->getParam('table');
        $fileName = LOG_DIR . '/admin/export_'.$tbName.'.txt';
        $rst = Hapyfish2_Admin_Bll_Basic::generateBasicDataFile($tbName, $fileName);
        if ($rst) {
            echo $rst;
            exit;
        }

        if (!file_exists($fileName)) {
            echo "export failed: $fileName";
            exit;
        }

        header('Content-Description: File Transfer');
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename='.basename($fileName));
        //header('Content-Transfer-Encoding: binary');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        //header('Expires: 0');
        header('Content-Length: ' . filesize($fileName));
        ob_clean();
        flush();
        readfile($fileName);
        exit;
	}

	function importdataAction()
	{
		$tbName = $this->_request->getParam('table');
		$chkDel = $this->_request->getParam('chkDel');

	    $tbInfo = Hapyfish2_Admin_Bll_Basic::getBasicTbByName($tbName);
	    if (!$tbInfo) {
            echo 'table not found,please check.';
            exit;
        }

		if (!isset($_POST['btnImport'])){
            echo 'not post please retry.';
            exit;
		}

		$file = $_FILES['filename'];
        $file_type = substr(strstr($file['name'],'.'), 1);

        if (!$file) {
            echo '请指定需导入的数据文件!';
            exit;
        }

        // 检查文件格式
        if ($file_type != 'txt'){
            echo '文件格式不对,请指定数据文件或重新上传!';
            exit;
        }

        //先备份原有数据
        $bakFile = LOG_DIR . '/admin/bak/'.$tbName.'_'.date('Ymd-His').'.txt';
        Hapyfish2_Admin_Bll_Basic::generateBasicDataFile($tbName, $bakFile);

        //是否需要先清除原有数据
        if ($chkDel) {
            Hapyfish2_Admin_Bll_Basic::clearTableData($tbName);
        }

        //再导入新数据
        $aryFailed = array();
        $cntSuccess = Hapyfish2_Admin_Bll_Basic::importBasicDataFromFile($tbName, $file['tmp_name'], $aryFailed);

		echo "<br />";
		if ($cntSuccess) {
		    echo $cntSuccess . ' 行数据导入成功！<br />';
		}
		else {
		    echo '数据导入失败！<br />';
		}
		if ($aryFailed) {
		    echo '第'. implode(',', $aryFailed) . '行数据有问题，请检查后重新导入！<br />';
		}
		echo '<a href="#" onclick="parent.showDetail(\''. $tbName .'\');">》返回《</a>';
		exit;
	}

    function grantitemAction()
	{

		$this->render();
	}

	function easytoolAction()
	{
		$this->render();
	}

    function easytoolimportAction()
	{
		$tb = $this->_request->getParam('tb');
		//地图编辑器导入数据
		if ($tb == 'mapcopy') {
    		if (!isset($_POST['btnImport'])){
                echo 'not post please retry.';
                exit;
    		}

    		$file1 = $_FILES['filename1'];
            $file_type1 = substr(strstr($file1['name'],'.'), 1);
            $file2 = $_FILES['filename2'];
            $file_type2 = substr(strstr($file2['name'],'.'), 1);
            $file3 = $_FILES['filename3'];
            $file_type3 = substr(strstr($file3['name'],'.'), 1);

            if (!$file1 && !$file2 && !$file3) {
                echo '请指定需导入的数据文件!';
                exit;
            }

            // 检查文件格式
            /*if ($file_type1 != 'csv' || $file_type2 != 'csv' || $file_type3 != 'csv'){
                echo '文件格式不对,请指定数据文件或重新上传!';
                exit;
            }

            //先备份原有数据
            $bakFile = LOG_DIR . '/admin/bak/'.$tbName.'_'.date('Ymd-His').'.txt';
            Hapyfish2_Admin_Bll_Basic::generateBasicDataFile($tbName, $bakFile);

            //是否需要先清除原有数据
            if ($chkDel) {
                Hapyfish2_Admin_Bll_Basic::clearTableData($tbName);
            }*/

            //再导入新数据
            $aryFailed1 = $aryFailed2 = $aryFailed3 = array();
            if ($file1['tmp_name']) {
                $cntSuccess1 = Hapyfish2_Admin_Bll_Basic::importMapCopyDataFromFile('monster', $file1['tmp_name'], $aryFailed1);
            }

		    if ($file2['tmp_name']) {
                $cntSuccess2 = Hapyfish2_Admin_Bll_Basic::importMapCopyDataFromFile('mine', $file2['tmp_name'], $aryFailed2);
            }
		    if ($file3['tmp_name']) {
                $cntSuccess3 = Hapyfish2_Admin_Bll_Basic::importMapCopyDataFromFile('portal', $file3['tmp_name'], $aryFailed3);
            }

            $updMapIds = array();
    		echo "<br />";
    		if ($cntSuccess1) {
    		    foreach ($cntSuccess1 as $mapId=>$data) {
    		        if (!array_key_exists($mapId, $updMapIds)) {
                        $updMapIds[$mapId] = 1;
    		        }
    		    }
    		    echo count($cntSuccess1) . ' 个地图副本数据导入成功！（monsterList.csv）<br />';
    		}
    		else {
    		    echo '没有数据导入！（monsterList.csv）<br />';
    		}
    		if ($aryFailed1) {
    		    echo '第'. implode(',', $aryFailed1) . '行数据有问题，请检查后重新导入！-1<br />';
    		}

		    echo "<br />";
    		if ($cntSuccess2) {
    		    foreach ($cntSuccess2 as $mapId=>$data) {
    		        if (!array_key_exists($mapId, $updMapIds)) {
                        $updMapIds[$mapId] = 1;
    		        }
    		    }
    		    echo count($cntSuccess2) . ' 个地图副本数据导入成功！（mineList.csv）<br />';
    		}
    		else {
    		    echo '没有数据导入！（mineList.csv）<br />';
    		}
    		if ($aryFailed2) {
    		    echo '第'. implode(',', $aryFailed2) . '行数据有问题，请检查后重新导入！-2<br />';
    		}

		    echo "<br />";
    		if ($cntSuccess3) {
    		    foreach ($cntSuccess3 as $mapId=>$data) {
    		        if (!array_key_exists($mapId, $updMapIds)) {
                        $updMapIds[$mapId] = 1;
    		        }
    		    }
    		    echo count($cntSuccess3) . ' 个地图副本数据导入成功！（portalList.csv）<br />';
    		}
    		else {
    		    echo '没有数据导入！（portalList.csv）<br />';
    		}
    		if ($aryFailed3) {
    		    echo '第'. implode(',', $aryFailed3) . '行数据有问题，请检查后重新导入！-3<br />';
    		}

    		if ($updMapIds) {
    		    $newVer = time();
    		    try {
    		        //update map version
    		        $ids = '';
    		        $dal = Hapyfish2_Admin_Dal_Basic::getDefaultInstance();
        		    foreach ($updMapIds as $mapId=>$val) {
        		        $info = array('map_id'=>$mapId, 'fname'=>$mapId, 'ver'=>$newVer);
                        $dal->addInfo('alchemy_map_copy_version', $info);
                        $ids .= $mapId . ',';
        		    }

        		    //clear map copy cache
                	$list = Hapyfish2_Tool_Server::getWebList();
            		if (!empty($list) && $ids) {
            		    $ids = substr($ids,0,-1);
            			$host = str_replace('http://', '', HOST);
            			foreach ($list as $server) {
            				$url = 'http://' . $server['local_ip'] . '/zxtest/clearbasmapcopy?ids='.$ids;
            				$result = Hapyfish2_Tool_Server::requestWeb($host, $url);
            				echo $server['name']. '--' . $url . ':' . $result . '<br/>';
            			}
            		}
    		    }
    		    catch (Exception $e) {
                    echo $e->getMessage();
    	        }
    		}
		}
		else if ($tb == 'mapcopystatic') {
		    if (!isset($_POST['btnImport'])){
                echo 'not post please retry.';
                exit;
    		}
            $file = $_FILES['filename'];
		    if (!$file) {
                echo '请指定地图文件!';
                exit;
            }

		    $data = file_get_contents($file['tmp_name']);
		    //$data = mb_convert_encoding($data, "UTF-8");
		    //$data = mb_convert_encoding($data,"UTF-8","gb2312");
		    $aryInfo = json_decode($data, true);
		    $mapId = $aryInfo['sceneClass']['sceneId'];

		    if ($mapId) {
    		    if (!is_dir(TEMP_DIR . '/mapcopy')) {
                    mkdir(TEMP_DIR . '/mapcopy');
    		    }
                $file = TEMP_DIR . '/mapcopy/'. $mapId . '.cache';
                file_put_contents($file, $data);

                //update map version
                $newVer = time();
		        $dal = Hapyfish2_Admin_Dal_Basic::getDefaultInstance();
		        $info = array('map_id'=>$mapId, 'fname'=>$mapId, 'ver'=>$newVer);
                $dal->addInfo('alchemy_map_copy_version', $info);

                //update map copy info
                $info = array('map_id'=>$mapId, 'jump'=>(int)$aryInfo['sceneClass']['jump']);
                //portal data
                if ($aryInfo['sceneClass']['portalList']) {
                    $info['portal_data'] = array();
                    foreach ($aryInfo['sceneClass']['portalList'] as $pdata) {
                        $tmpData = array(
                        	'id'=>(int)$pdata['id'],
                            'cid'=>(int)$pdata['cid'],
                            'x'=>(int)$pdata['x'],
                            'z'=>(int)$pdata['z'],
                            'mirror'=>(int)$pdata['mirror'],
                            'tar'=>(int)$pdata['targetSceneId'],
                            'vpath'=>''
                        );
                        $info['portal_data'][(int)$pdata['id']] = $tmpData;
                    }
                    $info['portal_data'] = json_encode($info['portal_data']);
                }
                $dal->addInfo('alchemy_map_copy', $info);
                echo $mapId.'-mapid import ok <br/>';

		        $host = str_replace('http://', '', HOST);
    			foreach ($list as $server) {
    				$url = 'http://' . $server['local_ip'] . '/zxtest/clearbasmapcopy?ids='.$mapId;
    				$result = Hapyfish2_Tool_Server::requestWeb($host, $url);
    				echo $server['name']. '--' . $url . ':' . $result . '<br/>';
    			}
		    }
		}
		exit;
	}

    function ajaxgrantitemAction()
	{
	    $coin = (int)$this->_request->getParam('coin');
	    $gold = (int)$this->_request->getParam('gold');
	    $item = (int)$this->_request->getParam('item');
	    $decor = (int)$this->_request->getParam('decor');
	    $cntItem = (int)$this->_request->getParam('cntItem');
	    $cntDecor = (int)$this->_request->getParam('cntDecor');
	    $uids = $this->_request->getParam('uids');

	    if (empty($uids)) {
            $this->echoResult(array('status'=>0,'msg'=>'uid is empty!'));
	    }
	    if (empty($coin) && empty($gold) && empty($item) && empty($decor) && empty($cntItem) && empty($cntDecor) && empty($uids)) {
            $this->echoResult(array('status'=>0,'msg'=>'nothing to send!'));
	    }

	    $aryUid = explode(',', $uids);
	    $msg = '';
	    $sendUids = array();
	    foreach ($aryUid as $uid) {
	        $uid = (int)$uid;
	        $rowUser = Hapyfish2_Platform_Bll_User::getUser($uid);
	        if (!$rowUser) {
	            continue;
	        }

            $robot = new Hapyfish2_Magic_Bll_Award();
            $strGain = '';
            if ($coin) {
                $robot->setCoin($coin);
                $strGain .= '--金币+' . $coin;
            }
	        if ($gold) {
                $robot->setGold($gold, 98);
                $strGain .= '--宝石+' . $gold;
            }
	        if ($item && $cntItem) {
                $robot->setItem($item, $cntItem);
                $strGain .= '--物品'.$item.'*' . $cntItem;
            }
	        if ($decor && $cntDecor) {
                $robot->setDecor($decor, $cntDecor);
                $strGain .= '--装饰'.$decor.'*' . $cntDecor;
            }
            $robot->sendOne($uid);

	        $msg .= "\n" .$uid . ' -> ' . $strGain;
	        $sendUids[]= $uid;
	    }

	    //$log = Hapyfish2_Util_Log::getInstance();
	    $sendItem = 'coin:'.$coin.'  gold:'.$gold.'  item:'.$item.'*'.$cntItem.'  decor:'.$decor.'*'.$cntDecor;
	    //$log->report('adminGrantitem', array($this->_curAdmin, implode(',', $sendUids), $sendItem));
	    info_log($this->_curAdmin.':'.implode(',', $sendUids).'->'.$sendItem, 'adminGrantitem');

	    $rst = array('status'=>1,'msg'=>$msg,'tm'=>date('Y/m/d H:i:s'));
		$this->echoResult($rst);
	}


}