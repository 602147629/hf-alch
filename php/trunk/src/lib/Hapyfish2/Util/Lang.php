<?php

class Hapyfish2_Util_Lang
{
    protected static $_instance;

	protected $_data;

    public function __construct()
    {
		$this->_data = array();
    }

    /**
     * single instance of Hapyfish2_Util_Lang
     *
     * @return Hapyfish2_Util_Lang
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
    public function getData($category)
    {
        if (!isset($this->_data[$category])) {
            include_once CONFIG_DIR . '/lang/' . $category . '.php';
            $lang_category = 'lang_' . $category;
            $this->_data[$category] = $$lang_category;
        }
        return $this->_data[$category];
    }
    
    public function getText($category, $key)
    {
        $data = $this->getData($category);
        if (isset($data[$key])) {
            $text = $data[$key];
            $args_num = func_num_args();
            if ($args_num > 2) {
                $args = func_get_args();
                for($i = 2; $i < $args_num; $i++) {
                    $text = preg_replace('/[%s]/', $args[$i], $text, 1);
                }
            }
            return $text;
        }
        return '';
    }

}