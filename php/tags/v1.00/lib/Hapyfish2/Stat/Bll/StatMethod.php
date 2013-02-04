<?php
class Hapyfish2_Alchemy_Bll_Fight_StatMethod
{
	protected $_data;
	/**
	 *数据
	 *
	 */
	protected $_col;
	/**
	 * 列
	 */
	protected $_type;
	/**
	 * 计算的类型 0为和 1为 平均
	 */
	protected $_repeat;
	/**
	 * 是否去重 0为否  1为是
	 */
	
	protected $_additive;
	/**
	 * 是否相加 最后尾列 0为否  1为是
	 */
	public function setData($data)
	{
		$this->_data = $data;
	}
	
	public function setCol($col)
	{
		$this->_col = $col;
	}
	
	public function setType($type)
	{
		$this->_type = $type;
	}
	
	public function setRepeat($repeat)
	{
		$this->_repeat = $repeat;
	}
	
	public function setAdditive($additive)
	{
		$this->_additive = $additive;
	}
	public function run()
	{
		$data = array();
		foreach($this->_data as $k => $v){

		}
  	}
  	
  	public function creatArray()
  	{
  		
  	}
	
}