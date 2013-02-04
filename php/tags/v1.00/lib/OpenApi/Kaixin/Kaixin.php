<?php

/**
 * Kaixin平台
 *
 */
class OpenApi_Kaixin_Kaixin
{
    public $api_key;
    public $secret;
    public $app_id;
    public $app_name;
    public $user_id;
	public $access_token;

    //"rr"
    public $user_src;

    const REST_SERVER_ADDR = 'https://api.kaixin001.com/';						//api 服务器地址
	const AUTHORIZE_URL	= 'http://api.kaixin001.com/oauth2/authorize';			//
	const ACCESS_TOKEN_URL = 'http://api.kaixin001.com/oauth2/access_token';
	const ACCESS_TOKEN_URL_SSL = 'https://api.kaixin001.com/oauth2/access_token';
    const REST_VER = '2.0';														//版本号
    const REST_SIGN_METHOD = 'oauth2';											//验证方式
    const REST_FORMAT = 'json';													//返回格式

	//回调地址，所在域名必须与开发者注册应用时所提供的网站根域名列表或应用的站点地址（如果根域名列表没填写）的域名相匹配
	public $redirect_uri = ".../redirect.php";
  
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
        $this->restful = new OpenApi_Kaixin_Restful();
        $config = array(
            'format' => self::REST_FORMAT
        );
        $this->restful->setRestfulConfig($config);
    }

    public function setUser($user_id, $access_token)
    {
        $this->user_id = $user_id;
        $this->access_token = $access_token;
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
        $user['figureurl'] = $data['logo50'];
        $user['gender'] = $data['gender'];
        return $user;
    }
    
    public function usersGetInfo()
    {
        $data = $this->users_me();
        if ($data['httpcode'] == 200) {
            return $this->_formatUser($data['response']);
        }

        return null;
    }
    
    public function friendGetAppFriends()
    {
        $fids = array();
        $pageNum = 50;
        $index = 0;
        while($index <= 20) {
        	$data = $this->app_friends($index, $pageNum);
        	if ($data['httpcode'] != 200) {
        		return null;
        	}
        	$fids = array_merge($fids, $data['response']['uids']);
        	if ($data['response']['next'] > 0) {
        		$index++;
        	} else {
        		break;
        	}
        }

        return $fids;
    }

	/**
	* Get the authorize URL
	*
	* @returns a string
	*/
	public function getAuthorizeURL($response_type, $scope = null, $state = null, $display = null)
	{
		$params = array(
			'client_id' => $this->api_key,
			'response_type' => $response_type,
			'redirect_uri' => $this->redirect_uri,
		);
		if(!empty($scope))	$params['scope'] = $scope;
		if(!empty($state))	$params['state'] = $state;
		if(!empty($display))	$params['display'] = $display;
		$query = OAuthUtil::build_http_query($params);
		return self::AUTHORIZE_URL . "?{$query}";  
	}

	/**
	*
	*/
	public function getAccessTokenFromCode($code)
	{
		$params = array(
			'grant_type' => 'authorization_code',
			'code' => $code,
			'client_id' => $this->api_key,
			'client_secret' => $this->secret,
			'redirect_uri' => $this->redirect_uri,
		);
		$request = $this->restful->get(self::ACCESS_TOKEN_URL, $params);
		return $request;
	}
  
	public function getAccessTokenFromPassword($username, $password, $scope)
	{
		$params = array(
			'grant_type' => 'password',
			'username' => $username,
			'password' => $password,
			'client_id' => $this->api_key,
			'client_secret' => $this->secret,
			'scope' => $scope,
		);
		$request = $this->restful->get(self::ACCESS_TOKEN_URL_SSL, $params);
		return $request;
	}
  
	public function getAccessTokenFromRefreshToken($refresh_token, $scope)
	{
		$params = array(
			'grant_type' => 'refresh_token',
			'refresh_token' => $refresh_token,
			'client_id' => $this->api_key,
			'client_secret' => $this->secret,
			'scope' => $scope,
		);
		$request = $this->restful->get(self::ACCESS_TOKEN_URL, $params);
		return $request;
	}

    /**
     * 按用户ID返回多个用户的资料
     */
    public function users_show($uids, $fields = null, $start = 0, $num = 20)
    {
        $url = 'users/show';
        $param = array(
            'uids' => $uids,
            'fields' => $fields,
            'start' => $start,
            'num' => $num,
        );
        $response = $this->get($url, $param);
        return array(
            'httpcode' => $this->restful->http_code,
            'response' => $response,
        );
    }
    
    /**
     * 获取当前登录用户的资料
     */
    function users_me( $fields = null )
    {
        $url = 'users/me';
        $param = array(
            'fields' => $fields,
        );       
        $response = $this->get($url, $param);
        return array(
            'httpcode' => $this->restful->http_code,
            'response' => $response,
        );
    }
    
    /**
     * 取出当前登录用户的好友资料
     */
    function friends_me( $fields = null, $start = 0, $num = 20 )
    {
        $url = 'friends/me';
        $param = array(
            'fields' => $fields,
            'start' => $start,
            'num' => $num,
        );
        $response = $this->get($url, $param);
        return array(
            'httpcode' => $this->restful->http_code,
            'response' => $response,
        );
    }
    
    /**
     * 返回两个用户之间好友关系
     */
    function friends_relationship( $uid1, $uid2 )
    {
        $url = 'friends/relationship';
        $param = array(
            'uid1' => $uid1,
            'uid2' => $uid2,
        );
        $response = $this->get($url, $param);
        return array(
            'httpcode' => $this->restful->http_code,
            'response' => $response,
        );
    }
    
    /**
     * 获取本人安装本组件的好友的uid列表
     */
    function app_friends( $start = 0, $num = 20 )
    {
        $url = 'app/friends';
        $param = array(
            'start' => $start,
            'num' => $num,
        );
        $response = $this->get($url, $param);
        return array(
            'httpcode' => $this->restful->http_code,
            'response' => $response,
        );
    }
    
    /**
     * 批量获取用户安装组件的状态
     */
    function app_status( $uids, $start = 0, $num = 20 )
    {
        $url = 'app/status';
        $param = array(
            'uids' => $uids,
            'start' => $start,
            'num' => $num,
        );
        $response = $this->get($url, $param);
        return array(
            'httpcode' => $this->restful->http_code,
            'response' => $response,
        ); 
    }
    
    /**
     * 获取某人邀请成功的好友uid列表
     */
    function app_invited( $uid, $start = 0, $num = 20 )
    {
       $url = 'app/invited';
        $param = array(
            'uid' => $uid,
            'start' => $start,
            'num' => $num,
        );
        $response = $this->get($url, $param);
        return array(
            'httpcode' => $this->restful->http_code,
            'response' => $response,
        ); 
    }
    
    /**
     * 创建照片专辑
     * privacy	照片专辑隐私设置(0:任何人可见, 1:仅好友可见, 2:凭密码, 3:隐藏)，默认为0任何人可见
     * category	照片专辑分类(0:空, 1:美女, 2:帅哥, 3:宠物, 4:旅游, 5:美食, 6:家居, 7:街拍, 8:时尚,9:风景, 10:奇趣)
     * allow_repaste	是否允许转贴，仅在privacy为1时有效，默认为允许转帖。其他情况下该参数无效
     * 
     */
    function album_create($title,$privacy=0,$password='',$category=0,$allow_repaste=0,$location='',$description='')
    {
    	$url = 'album/create';
        $param = array(
            'title' => $title,
            'privacy' => $privacy ,
            'password' => $password ,
            'category' => $category ,
            'allow_repaste' => $allow_repaste ,
            'location' => $location ,
            'description' => $description ,
            
        );
        $response = $this->post($url, $param);
        return array(
            'httpcode' => $this->restful->http_code,
            'response' => $response,
        ); 
    }
    
	/**
	 * 发布一条记录(可以带一张图片)
	 * content	true	发记录的内容
	 * save_to_album	flase	是否存到记录相册中，0/1-不保存/保存，默认为0不保存
	 * location	flase	记录的地理位置(目前仅在“我的记录”列表中显示)
	 * lat	flase	纬度 -90.0到+90.0，+表示北纬(目前暂不能显示)
	 * lon	flase	经度 -180.0到+180.0，+表示东经(目前暂不能显示)
	 * sync_status	flase	是否同步签名 0/1/2-无任何操作/同步/不同步，默认为0无任何操作
	 * spri	flase	权限设置，0/1/2/3-任何人可见/好友可见/仅自己可见/好友及好友的好友可见,默认为0任何人可见
	 * pic	flase	发记录上传的图片，图片在10M以内，格式支持jpg/jpeg/gif/png/bmp
	 * pic和picurl只能选择其一，两个同时提交时，只取pic
	 * oauth1.0，pic参数不需要参加签名
	 * picurl	flase	外部图片链接，图片在10M以内，格式支持jpg/jpeg/gif/png/bmp
	 * pic和picurl只能选择其一，两个同时提交时，只取pic
	 */
    function records_add($content,$pic="",$picurl="",$save_to_album="",$location="",$sync_status="",$spri="")
    {	
    	$url = 'records/add';
        $param = array(
        	'content' => $content,
            'save_to_album' => $save_to_album,
            'location' => $location ,
            'sync_status' => $sync_status,
        	'spri' => $spri,
        	'lat','lon',
                        
        );
        $multi = false;
        if(strlen($pic) > 0)
        {
        	$param['pic'] = $pic;
        	$multi = true;
        }
        else if(strlen($picurl) > 0)
        {
        	$param['picurl'] = $picurl;
        }
        $response = $this->post($url, $param, $multi);
        return array(
            'httpcode' => $this->restful->http_code,
            'response' => $response,
        ); 
    }

    /**
	 * 返回某个用户的照片专辑列表
	 * uid: 用户UID。若参数uid缺失, 默认使用当前登录用户的UID。
	 * start: 起始值。
	 * num: 返回数量。
     */
    function album_show($start=0,$num=20,$uid='')
    {
    	$url = 'album/show';
        $param = array(
            'uid' => $uid,
            'start' => $start,
            'num' => $num,
        );
        $response = $this->get($url, $param);
        return array(
            'httpcode' => $this->restful->http_code,
            'response' => $response,
        ); 
    }

    /**
	 * 上传照片到指定的照片专辑
	 * albumid	照片专辑ID 若参数albumid缺失, 返回错误提示album_not_select。
	 * title	照片标题
	 * size	返回照片的大小尺寸. 可选值:(mid, small, cover)
	 * send_news	是否发送动态. 可选值:(0:不发送动态, 1:发送动态)
	 * pic	要上传的照片文件 若参数pic缺失,返回错误提示file_not_select。如果照片上传失败,则返回错误提示: file_upload_failed。
     */
    function photo_upload($albumid,$pic,$title = "",$size = 'mid',$send_news = 0)
    {	
    	$url = 'photo/upload';
        $param = array(
        	'albumid' => $albumid,
            'title' => $title,
            'size' => $size ,
            'send_news' => $send_news,
            'pic' => $pic ,
                        
        );
        $response = $this->post($url, $param, true);
        return array(
            'httpcode' => $this->restful->http_code,
            'response' => $response,
        ); 
    }

    /**
	 * 获取某个用户的某张照片或某照片专辑下的所有照片，如果是非当前用户好友，只能取到“任何人可见”的照片
	 * uid	用户uid 若参数uid缺失, 默认使用当前登录用户的UID。
	 * albumid	照片专辑ID 若参数albumid和pid都缺失, 返回错误提示Photo_Parameter_Absent。
	 * pid	照片ID  若参数pid存在, 则返回指定的照片。若参数albumid存在并且pid不存在, 则返回该相册下的照片列表。
	 * password	相册或照片的密码
	 * start	起始位置(0~n-1)
	 * num	返回数量
     */
    function photo_show($albumid='',$start=0,$num=20,$password='',$uid='',$pid='')
    {
    	
    	$url = 'photo/show';
        $param = array(
            'uid' => $uid,
            'albumid' => $albumid,
            'pid' => $pid,
            'password' => $password,       	
            'start' => $start,
            'num' => $num,
            
        );
        $response = $this->post($url, $param);      
        return array(
            'httpcode' => $this->restful->http_code,
            'response' => $response,
        ); 
    }

    function get($api, $params = array())
    {
    	$url = self::REST_SERVER_ADDR . $api . '.' . $this->restful->format;
        $params['access_token'] = $this->access_token;
        return $this->restful->get($url, $params);
    }

    function post($api, $params = array(), $multi = false)
    {
    	$url = self::REST_SERVER_ADDR . $api . '.' . $this->restful->format;
        $params['access_token'] = $this->access_token;
        return $this->restful->post($url, $params, $multi);
    }
}