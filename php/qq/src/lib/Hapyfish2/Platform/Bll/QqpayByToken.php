<?php

class Hapyfish2_Platform_Bll_QqpayByToken
{
   
    public static function getToken($uid,$cid,$itemInfo=array())
    {
    	$goodsInfo = self::createItemInfo($cid,$itemInfo=array());
    	//return true;
        try {
            $context = Hapyfish2_Util_Context::getDefaultInstance();
            $openkey = $context->get('session_key');
            $openid = $context->get('puid');
            $pfkey = $context->get('pfkey');
            $rest = OpenApi_QQ_Client::getInstance();
            $rest->setUser($openid,$openkey);
            $platform = PLATFORM;
            $rest->setPlatform($platform);
            $rest->setPfkey($pfkey);
            $data = $rest->getToken($goodsInfo);
            if($data['ret'] != 0){
    			info_log('errCode:'.$data['msg'],'getTokenErr');
	    		return null;
    		}
	    	$token = $data['token'];
	    	$logData = array();
	    	$logData['uid'] = $uid;
	    	$logData['token'] = $token;
	    	$logData['payitem'] = $logData;
			$logData['pl'] = PLATFORM;
        	try {
                //register qpoint buy token
                $dal = Hapyfish2_Platform_Dal_QqpayByToken::getDefaultInstance();
                $dal->insertToken($uid, $logData);
            } catch (Exception $e) {
    			info_log('getToken:'.$e->getMessage(), 'Platform_Bll_QqpayByToken');
                return null;
		    }
        }
        catch (Exception $e) {
        	info_log('getTokenErr:'.$e->getMessage(), 'getTokenErr');
            return null;
        }

        return $data;
    }
    
    public static function createItemInfo($cid,$itemInfo=array())
    {
    	$data = array();
    	$itemType =	substr($cid, -2, 1);
    	if(empty($itemInfo)){
	    	if ( $itemType == 1	) {
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getGoodsInfo($cid);
			}
			else if	( $itemType	== 2 ) {
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getScrollInfo($cid);
			}
			else if	( $itemType	== 3 ) {
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getStuffInfo($cid);
			}
			else if	( $itemType	== 5 ) {
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getDecorInfo($cid);
			}
			else if	( $itemType	== 6 ) {
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getWeaponInfo($cid);
			}
    	}
    	$goodsmeta = $itemInfo['name'].'*'.$itemInfo['content'];
    	$goodsurl = STATIC_HOST.'/alchemy/image/item/'.$itemInfo['cid'].'jpg';
    	$payitem = $itemInfo['cid'].'*'.$item['buy_gem'].'*1';
    	$data['goodsmeta'] = $goodsmeta;
    	$data['goodsurl'] = $goodsurl;
    	$data['payitem'] = $payitem;
    	return $data;
    }
    
    //0: 成功 1: 系统繁忙 2: token已过期 3: token不存在
    public static function completeBuy($uid, $params)
    {
    	try {
    	    $dal = Hapyfish2_Platform_Dal_QqpayByToken::getDefaultInstance();
            $row = $dal->getToken($uid, $params['token']);
    	    if (!$row) {
                return 3;
            }
        } catch (Exception $e) {
			info_log('completeBuy:'.$e->getMessage(), 'Platform_Bll_QqpayByToken');
			return 1;
		}
    	
    	try {
            $dalLog = Hapyfish2_Platform_Dal_QqpayByToken::getDefaultInstance();
            $rowLog = $dalLog->getPayDone($uid, $params['billno']);
        } catch (Exception $e) {
			info_log('completeBuy:'.$e->getMessage(), 'Platform_Bll_QqpayByToken');
			return 1;
		}
        //had already done
        if ($rowLog) {
            return 0;
        }
        
         $aryItem = explode(';', $params['payitem']);
         foreach($aryItem as $k=>$itemList){
         	$itemInfo = explode('*', $itemList);
         	Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $itemInfo[0],$itemInfo[2]);
         }
         $logData = array();
         $logData['billno'] = $params['billno'];
         $logData['payItme'] = $params['payitem'];
         
    	try {
           $dalLog->insertPayDone($uid, $params['billno']);
        } catch (Exception $e) {
			info_log('completeBuy:'.$e->getMessage(), 'Platform_Bll_QqpayByToken');
			return 1;
		}
         return 0;
    }
}