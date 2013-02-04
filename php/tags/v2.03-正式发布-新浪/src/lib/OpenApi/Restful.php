<?

require_once 'OpenApi/Exception.php';

class OpenApi_Restful
{
    public $app_key;
    public $secret;
    public $session_key;

    public $server_addr;
    public $v;
    public $sign_method;
    public $format;

    const TIME_OUT = 3;
    const CONNECT_TIME_OUT = 5;
    const RETRIES = 3;

    public function __construct($app_key, $secret, $session_key = null)
    {
        $this->app_key = $app_key;
        $this->secret = $secret;
        $this->session_key = $session_key;
    }

    public function getSessionKey()
    {
        return $this->session_key;
    }

    public function setSessionKey($session_key)
    {
        $this->session_key = $session_key;
    }

    public function setRestfulConfig($config)
    {
        $this->server_addr = $config['server_addr'];//'http://api.renren.com/restserver.do';
        $this->v = $config['v'];                    //'1.0'
        $this->sign_method = $config['sign_method'];//'md5';
        $this->format = $config['format'];          //'JSON';
    }

    public function call_method($method, $params)
    {
        $data = $this->post_request($method, $params);
        $result = $this->convert_result($data, $method, $params);

        if (is_array($result) && isset($result['error_code'])) {
            if (isset($result['error_msg'])) {
                $error_msg = $result['error_msg'];
            } else {
                $error_msg = '';
            }
            throw new OpenApi_Exception($error_msg, $result['error_code']);
        }

        return $result;
    }

    public function post_request($method, $params)
    {
        //$this->finalize_params($method, $params);
        $post_string = $this->create_post_string($params);
        //echo $post_string.'<br /><br />';
        //echo $this->server_addr . '?' . $post_string;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server_addr);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //max connect time
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIME_OUT);
        //max curl execute time
        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIME_OUT);
        //cache dns 1 hour
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 3600);
        //renren can get and send data encoding by gzip
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');

        $cURLVersion = curl_version();
        $ua = 'PHP-cURL/' . $cURLVersion['version'] . ' HapyFish-Rest/1.0';
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $retries = self::RETRIES;
        $result = false;
    	while (($result === false) && (--$retries > 0)) {
			$result = @curl_exec($ch);
		}

        $errno = @curl_errno($ch);
        $error = @curl_error($ch);
        curl_close($ch);

        if ($errno != CURLE_OK) {
            throw new OpenApi_Exception("HTTP Error: " . $error, $errno);
        }

        //echo $result;
        //debug_log($method . ': ' . $result);
        return $result;
    }

    protected function convert_result($data, $method, $params)
    {
        $is_xml = (empty($params['format']) || strtolower($params['format']) != 'json');
        return ($is_xml) ? $this->convert_xml_to_result($data, $method, $params) : json_decode($data, true);
    }

    protected function convert_xml_to_result($xml, $method, $params)
    {
        $sxml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        return self::convert_simplexml_to_array($sxml);
    }

    protected static function convert_simplexml_to_array($sxml)
    {
        $arr = array();
        if ($sxml) {
            $is_list = false;
            foreach ($sxml as $k => $v) {
                if ($sxml['list']) {
                    $arr[] = self::convert_simplexml_to_array($v);
                } else {
                    if (isset($arr[$k])) {
                        $is_list = true;
                        break;
                    }
                    $arr[$k] = self::convert_simplexml_to_array($v);
                }
            }

            if ($is_list) {
                $arr = array();
                foreach ($sxml as $k => $v) {
                    $arr[] = self::convert_simplexml_to_array($v);
                }
            }
        }
        if (sizeof($arr) > 0) {
            return $arr;
        } else {
            return (string)$sxml;
        }
    }

    private function xml_to_array($xml)
    {
        $array = (array)(simplexml_load_string($xml,'SimpleXMLElement', LIBXML_NOCDATA));
        foreach ($array as $key => $item){
            $array[$key]  = $this->struct_to_array((array)$item);
        }

        return $array;
    }

    private function struct_to_array($item)
    {
        if (!is_string($item)) {
            $item = (array)$item;
            foreach ($item as $key => $val) {
                $item[$key]  =  $this->struct_to_array($val);
            }
        }

        return $item;
    }

    public function convert_array_values_to_csv(&$params)
    {
        foreach ($params as $key => &$val) {
            if (is_array($val)) {
                $val = implode(',', $val);
            }
        }
    }

    private function create_post_string($params)
    {
        $post_params = array();
        foreach ($params as $key => &$val) {
            $post_params[] = $key.'='.urlencode($val);
        }
        return implode('&', $post_params);
    }

    //===========================================================================================================
}