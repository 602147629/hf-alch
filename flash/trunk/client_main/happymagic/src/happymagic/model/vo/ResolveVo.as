package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author zc
	 */
	//分解VO
	public class ResolveVo extends BasicVo
	{
		public var id:int; //分解ID
		public var gem:uint;//分解物品得到的钻石
		public var item:Array; //[[数组ID1,type]，[数组ID2,type],[数组ID3,type]]
		public var coin:uint;//分解物品得到的金币
		public var decorId:uint;//被分解的装饰物ID
		
		public function ResolveVo() 
		{
			
		}
		
	}

}