<?php
class Hapyfish2_Stat_Bll_Faq
{
	public static function  page($page,$total,$pagesize=10,$pagelen=10)
	{
		$pagecode = '';//定义变量，存放分页生成的HTML
		$page = intval($page);//避免非数字页码
		$total = intval($total);//保证总记录数值类型正确
		if(!$total) return array();//总记录数为零返回空数组
		$pages = ceil($total/$pagesize);//计算总分页
		//处理页码合法性
		if($page<1) $page = 1;
		if($page>$pages) $page = $pages;
		//计算查询偏移量
		$offset = $pagesize*($page-1);
		//页码范围计算
		$init = 1;//起始页码数
		$max = $pages;//结束页码数
		$pagelen = ($pagelen%2)?$pagelen:$pagelen+1;//页码个数
		$pageoffset = ($pagelen-1)/2;//页码个数左右偏移量
		
		//生成html
		$pagecode='<div class="page">';
		$pagecode.="<span>$page/$pages</span> ||&nbsp;&nbsp;";//第几页,共几页
		//如果是第一页，则不显示第一页和上一页的连接
		if($page!=1){
		$pagecode.='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="goPage(1);"><<</a>';//第一页
		$pagecode.='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="goPage('.($page-1).');"><</a>';//上一页
		}
		//分页数大于页码个数时可以偏移
		if($pages>$pagelen){
		//如果当前页小于等于左偏移
		if($page<=$pageoffset){
		$init=1;
		$max = $pagelen;
		}else{//如果当前页大于左偏移
		//如果当前页码右偏移超出最大分页数
		if($page+$pageoffset>=$pages+1){
		$init = $pages-$pagelen+1;
		}else{
		//左右偏移都存在时的计算
		$init = $page-$pageoffset;
		$max = $page+$pageoffset;
		}
		}
		}
		//生成html
		for($i=$init;$i<=$max;$i++){
		if($i==$page){
		$pagecode.='&nbsp;&nbsp;<span>'.$i.'</span>';
		} else {
			$pagecode.='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="goPage('.$i.')">'.$i.'</a>';
		}
		}
		if($page!=$pages){
			$pagecode.='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="goPage('.($page+1).');">></a>';//下一页
			$pagecode.='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="goPage('.$pages.');">>></a>';//最后一页
		}
		$pagecode.='</div>';
		return array('pagecode'=>$pagecode,'sqllimit'=>' limit '.$offset.','.$pagesize);
	}
	
	public static function getFaq($start, $end, $page, $type, $status, $id)
	{
		$dal = Hapyfish2_Stat_Dal_Faq::getDefaultInstance();
		if($id > 0){
			$count = 1;
		}else{
			$count = $dal->getCount($start, $end, $type, $status);
		}
		$page = self::page($page, $count);
		$data = $dal->getApiFaq($start, $end, $type, $status, $page['sqllimit'],$id);
		return array('page'=>$page['pagecode'], 'data'=>$data);
	}
	
	public static function getExportFaq($start, $end, $type, $status)
	{
		$dal = Hapyfish2_Stat_Dal_Faq::getDefaultInstance();
		$data = $dal->getEApiFaq($start, $end, $type, $status);
		return array('data'=>$data);
	}
}
