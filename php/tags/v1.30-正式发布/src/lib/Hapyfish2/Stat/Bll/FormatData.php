<?php

class Hapyfish2_Stat_Bll_FormatData
{
    /**
     * 格式化本文数据
     * 
     * @param array $list
     * @param array $params
     * 字段名，列位置，结果参数：分列、求和、平均、去重
     * $params[] = array('userLev', 3, array('group'=>1,'sum'=>1,'avg'=>1,'diff'=>1));
     */
    public static function formatData($list, $params = null)
    {
    	$info = array();
    	foreach ( $params as $p ) {
    		if (!isset($p[2])) {
    			$p[] = array('group'=>1,'sum'=>1,'avg'=>1,'diff'=>1);
    		}
    		
    		$groupAry = array();
    		$diffAry = array();
    		$diffCnt = 0;
    		$allCnt = 0;
    		$sum = 0;
    		foreach ( $list as $i ) {
                if (empty($i) || $i == '-100') {
    				continue;
    			}
    			$v = explode("\t", $i);
    			
    			//分类
    			if( $p[2]['group'] == 1 ) {
	    			if ( isset($groupAry[$v[$p[1]]]) ) {
	    				$groupAry[$v[$p[1]]]++;
	    			}
	    			else {
	    				$groupAry[$v[$p[1]]] = 1;
	    			}
    			}
    			
    			//求和
    			if ( $p[2]['sum'] == 1 ) {
    				$sum += $v[$p[1]];
    			}
    			
    			//不重复行数
    			if ( $p[2]['diff'] == 1 ) {
    				if ( !isset($diffAry[$v[$p[1]]]) ) {
    					$diffCnt++;
    					$diffAry[$v[$p[1]]] = 1;
    				}
    			}
    			
    			//文件数据总行数
    			$allCnt++;
    		}
    		if ( $allCnt < 1 ) {
    			$avg = 0;
    		}
    		else {
	    		//平均数
	    		$avg = round($sum/$allCnt);
    		}
    		
    		$result = array();
    		if ( $p[2]['group'] == 1 ) {
    			$result['group'] = $groupAry;
    		}
    		if ( $p[2]['sum'] == 1 ) {
    			$result['sum'] = $sum;
    			$result['avg'] = $avg;
    		}
    		if ( $p[2]['diff'] == 1 ) {
    			$result['diff'] = $diffCnt;
    		}
    		$info[$p[0]] = $result;
    	}
    	
    	$info['allCnt'] = $allCnt;
    	return $info;
    }
    
}