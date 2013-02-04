<?php

require_once 'OpenApi/Restful.php';

/**
 * Renren平台Restful API调用封装类 实现openApi的接口
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/03    zx
 */
class OpenApi_Renren_Renren implements OpenApi_Interface
{
    public $api_key;
    public $secret;
    public $app_id;
    public $app_name;
    public $user_id;

    //"renren.com"
    public $domain;

    //"rr"
    public $user_src;

    const REST_SERVER_ADDR = 'http://api.renren.com/restserver.do';//api 服务器地址
    const REST_VER = '1.0';                                        //版本号
    const REST_SIGN_METHOD = 'md5';                                //验证方式
    const REST_FORMAT = 'JSON';                                    //返回格式

    /**
     * api rest object （curl request method）
     *
     * @var OpenApi_Restful
     */
    public $restful;

    protected static $_instance;

    /**
     * get renren object singleton instance
     *
     * @return OpenApi_Renren_Renren
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self(APP_KEY, APP_SECRET, APP_ID, APP_NAME);
        }

        return self::$_instance;
    }

    public function __construct($api_key, $secret, $app_id, $app_name)
    {
        $this->api_key = $api_key;
        $this->secret = $secret;
        $this->app_id = $app_id;
        $this->app_name = $app_name;
        $this->_initRest();
    }

    /**
     * 初始化 restful请求配置参数
     *
     * @return void
     */
    private function _initRest()
    {
        $this->restful = new OpenApi_Restful($this->api_key, $this->secret);
        $config = array(
            'server_addr'=>self::REST_SERVER_ADDR,
            'v'=>self::REST_VER,
            'sign_method'=>self::REST_SIGN_METHOD,
            'format'=>self::REST_FORMAT
        );
        $this->restful->setRestfulConfig($config);
    }

    /**
     * call api and get api return data
     * @param  $method  -API method name
     * @param  $params  -API method required parameters
     *
     * @return array
     */
    private function _callApi($method, $params)
    {
        try {
            $this->_finalizeParams($method, $params);
            return $this->restful->call_method($method, $params);
        }
        catch (OpenApi_Exception $e1) {
            err_log("[OpenApi_Renren::$method]1: " . $e1->getCode() . ':' . $e1->getMessage());
        }
        catch (Exception $e) {
            err_log("[OpenApi_Renren::$method]: " . $e->getMessage());
        }
        return null;
    }

    public function setLocation($domain, $user_src)
    {
        $this->domain = $domain;
        $this->user_src = $user_src;
        $this->app_url = 'http://app.' . $domain;
    }

    /**
     * 调用实际api方法前，需要先调用该方法设置用户的sessionkey
     * @param  $user_id      -platformid
     * @param  $session_key  -sessionkey for platform
     *
     * @return void
     */
    public function setUser($user_id, $session_key)
    {
        $this->user_id = $user_id;
        $this->restful->setSessionKey($session_key);
    }

    /**
     * 与平台之间交互时验证 数据的合法性
     * @param  $xn_params      -parameters for valid
     * @param  $expected_sig   -valid sig
     *
     * @return boolean
     */
    public function verifySignature($xn_params, $expected_sig)
    {
        return $this->generateSignature($xn_params, $this->secret) == $expected_sig;
    }

    /**
     * 根据参数生成与平台交互时使用的 sig
     * @param  $params_array   -parameters for valid
     * @param  $secret   	   -app secret
     *
     * @return string
     */
    public function generateSignature($params_array, $secret)
    {
        $str = '';
        ksort($params_array);
        foreach ($params_array as $k => $v) {
            $str .= "$k=$v";
        }
        $str .= $secret;

        return md5($str);
    }

    /**
     * 格式化平台返回的用户信息（统一成db格式）
     * @param  $data   -user data from platform
     *
     * @return array
     */
    protected function _formatUser($data)
    {
        $user = array();
        $user['puid'] = $data['uid'];
        $user['name'] = $data['name'];
        $user['figureurl'] = $data['headurl'];
        $sex = isset($data['sex']) ? $data['sex'] : '';
        if ($sex == '1') {
            $gender = 1;
        }
        else if ($sex == '0') {
            $gender = 0;
        }
        else {
            $gender = - 1;
        }
        $user['gender'] = $gender;
        return $user;
    }

    /**
     * 拼接准备调用API前 必要的参数
     * @param  $method   -api method name
     * @param  $params   -parameters to prepare
     *
     * @return array
     */
    protected function _finalizeParams($method, &$params)
    {
        $params['method'] = $method;
        $params['session_key'] = $this->restful->getSessionKey();
        $params['api_key'] = $this->api_key;
        /*$params['call_id'] = microtime(true);
        if ($params['call_id'] <= $this->last_call_id) {
            $params['call_id'] = $this->last_call_id + 0.001;
        }
        $this->last_call_id = $params['call_id'];
        */
        $params['v'] = self::REST_VER;
        $params['format'] = self::REST_FORMAT;

        //we need to do this before signing the params
        $this->restful->convert_array_values_to_csv($params);

        //generate signature
        $str = '';
        $params_array = $params;
        ksort($params_array);
        foreach ($params_array as $k => $v) {
            $str .= "$k=$v";
        }
        $str .= $this->secret;

        $params['sig'] = md5($str);
    }

	/**
     * Returns the requested info fields for the requested set of users.
     *
     * @param array $uids    An array of user ids
     * @param array $fields  An array of info field names desired
     *
     * @return array  An array of user objects
     */
    public function usersGetInfo()
    {
        $params = array(
    		'uids' => $this->user_id,
            'fields' => array('uid','name','sex','headurl')
        );
        $data = $this->_callApi('users.getInfo', $params);
        if(isset($data[0])) {
            return $this->_formatUser($data[0]);
        }

        return null;
    }

	/**
     * Returns the friends of the session user, who are also users
     * of the calling application.
     *
     * @param array $fields  An array of info field names desired
     *
     * @return array  An array of friends also using the app
     */
    public function friendGetAppFriends()
    {
        //friends_getAppUsers will become invalid
        //$data = $this->restful->friends_getAppUsers();
        $params = array();
        $data = $this->_callApi('friends.getAppFriends', $params);
        if (isset($data['uid'])) {
            return array($data['uid']);
        }
        else if (is_array($data)) {
            return $data;
        }

        return null;
    }

	/**
     * Returns the friends of the current session user.
     *
     * @param int $page  (Optional).
     * @param int $count   (Optional)
     *
     * @return array  An array of friends
     */
    public function friendGetFriends()
    {

        $params = array('page' => 0, 'count' => 1000);
        $data = $this->_callApi('friends.getFriends', $params);
        if ($data && is_array($data)) {
            $friends = array();
            foreach ($data as $v) {
               $friends[$v['id']] = array('uid' => $v['id'], 'name' => $v['name'], 'figureurl' => $v['tinyurl']);
            }
            return $friends;
        }

        return null;
    }

	/**
     * check is fans
     *
     * @return boolean
     */
    public function pagesIsFan()
    {
        $params = array();
        $data = $this->_callApi('pages.isFan', $params);
        return (int)$data;
    }

	/**
     * prepare pay order
     * @param $param  -array(amount 校内豆消费数额, 取值范围为[0,1000], desc 用户使用校内豆购买的虚拟物品的名称)
     *
     * @return array
     */
    public function payRegOrder($param)
    {
        $amount = $param['amount'];
        $desc = $param['desc'];
        $order_id = $this->createPayOrderId();
        $params = array('order_id' => $order_id, 'amount' => $amount, 'desc' => $desc, 'type' => 0);

        if (defined(APP_SERVER_TYPE) && APP_SERVER_TYPE == 1) {
            $data = $this->_callApi('pay.regOrder', $params);
        }
        else {
            $data = $this->_callApi('pay4Test.regOrder', $params);
        }

        if(isset($data['token'])) {
            return array('orderid' => $order_id, 'token' => $data['token']);
        }

        return null;
    }

	/**
     * check pay order is completed
	 * @param $order_id  -支付订单号
     *
     * @return boolean
     */
    public function payIsOrderComplete($order_id)
    {

        $params = array('order_id' => $order_id);
        if (defined(APP_SERVER_TYPE) && APP_SERVER_TYPE == 1) {
            $data = $this->_callApi('pay.isCompleted', $params);
        }
        else {
            $data = $this->_callApi('pay4Test.isCompleted', $params);
        }

        if(isset($data[0])) {
            return (bool)$data[0];
        }

        return false;
    }

	/**
     * check pay order current status
	 * @param $order_numbers   -支付订单号，多个订单用逗号隔开
     *
     * @return array
     */
    public function payQueryOrders($order_numbers)
    {
        $params = array('order_numbers' => $order_numbers);
        $data = $this->_callApi('pay.queryOrders', $params);
        if(isset($data['order'])) {
            return $data['order'];
        }

        return false;
    }

	/**
     * create order id
     *
     * @return string
     */
    public function createPayOrderId()
    {
        //seconds 10 lens
        $time = time();
        //2010-01-01 00:00:00 1262275200
        $ticks = $time - 1262275200;

        //server id, 1 lens 0~9
        if (defined('SERVER_ID')) {
            $serverid = substr(SERVER_ID, -1, 1);
        } else {
            $serverid = '0';
        }

        //max 9 lens
        //$this->user_id
        return $ticks . $serverid . $this->user_id;
    }

}