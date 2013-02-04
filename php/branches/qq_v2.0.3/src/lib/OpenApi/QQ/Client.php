<?php

require_once 'OpenApi/QQ/V3/OpenApiV3.php';

class OpenApi_QQ_Client
{
    public $api_key;
    public $app_id;
    public $app_name;
    public $user_id;
	public $session_key;
	public $platform;
	public $pfkey;

    public $sdk;

    protected static $_instance;

    public function __construct($app_id, $app_key, $app_name)
    {
        $this->app_id = $app_id;
    	$this->api_key = $app_key;
    	$this->app_name = $app_name;
        $this->sdk = new OpenApiV3($app_id, $app_key);
        if ( APP_SERVER_TYPE == 1 ) {
//			$this->sdk->setServerName('119.147.19.43');
			$this->sdk->setServerName('openapi.tencentyun.com');
        }
        else {
			$this->sdk->setServerName('119.147.19.43');
        }
    }

    public function setUser($user_id, $session_key)
    {
        $this->user_id = $user_id;
        $this->session_key = $session_key;
    }

	/**
	 *
	 */
	public function setPlatform($pf)
	{
		$this->platform = $pf;
	}
	
	public function setPfkey($pfkey)
	{
		$this->pfkey = $pfkey;
	}

    /**
     * single instance of OpenApi_QQ_Client
     *
     * @return OpenApi_QQ_Client
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self(APP_ID, APP_SECRET, APP_NAME);
        }

        return self::$_instance;
    }

    /**
     * check user whether installed app
     *
     * @return boolean true|false
     */
    public function isAppUser()
    {
		$api_name = '/v3/user/is_setup';
		$params = array(
			'openid' => $this->user_id,
			'openkey' => $this->session_key,
			'pf' => $this->platform,
		);
		$result = $this->sdk->api($api_name, $params, 'post');
		if(isset($result['setuped'])) {
			return $result['setuped'];
		}
		return null;
    }

    public function getUser()
    {
		$api_name = '/v3/user/get_info';
		$params = array(
			'openid' => $this->user_id,
			'openkey' => $this->session_key,
			'pf' => $this->platform,
		);
		$data = $this->sdk->api($api_name, $params);
		return $data;
    }

    public function getAppFriendIds()
    {
		$api_name = '/v3/relation/get_app_friends';
		$params = array(
			'openid' => $this->user_id,
			'openkey' => $this->session_key,
			'pf' => $this->platform,
		);
		$data = $this->sdk->api($api_name, $params);

		$fids = array();
		if (!empty($data['items'])) {
			foreach ($data['items'] as $item) {
				//filter
				if ($item['openid'] != $this->user_id) {
					$fids[] = $item['openid'];
				}
			}
		}

		return $fids;
    }
    
    public function getToken($data)
    {
    	$api_name = '/v3/pay/buy_goods';
    	$params = array(
    		'openid' => $this->user_id,
			'openkey' => $this->session_key,
			'pf' => $this->platform,
    		'pfkey'=>$this->pfkey,
    		'ts'=>time(),
    		'appmode'=>1,
    		'payitem'=>$data['payitem'],
    		'goodsmeta'=>$data['goodsmeta'],
    		'goodsurl'=>$data['goodsurl'],
    		'zoneid'=>0,
    	);
    	$data = $this->sdk->api($api_name, $params,'post','https');
//    	info_log('errCode:'.json_encode($data),'getToken');
    	if($data['ret'] == 0){
    		return $data;
    	}
    	info_log('errCode:'.$data['msg'],'getTokenErr');
    	return null;
    }
    
    public function callConfirm($data)
    {
    	$api_name = '/v3/pay/confirm_delivery';
    	$params = array(
    		'openid' => $this->user_id,
			'openkey' => $this->session_key,
    		'ts'=>time(),
    		'pf' => $this->platform,
    		'payitem'=>$data['payitem'],
    		'token_id'=>$data['token_id'],
    		'billno'=>$data['billno'],
    		'zoneid'=>0,
    		'provide_errno'=>0,
    		'amt'=>$data['amt'],
    		'payamt_coins'=>$data['payamt_coins'],
    	);
    	$data = $this->sdk->api($api_name, $params,'post','https');
    	return $data;
    }

}