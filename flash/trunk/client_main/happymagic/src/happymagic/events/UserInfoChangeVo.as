package happymagic.events 
{
	import flash.geom.Point;
	import happymagic.model.vo.ResultVo;
	/**
	 * ...
	 * @author jj
	 */
	public class UserInfoChangeVo
	{
		public var levelUp:Boolean;
		public var coin:int;
		public var gem:int;
		public var exp:int;
		public var sp:int;
		
		public var piao:Boolean;
		public var showPoint:Point;
		
		public var maxSp:int;
		public function UserInfoChangeVo() 
		{
			
		}
		
		public function turnFromResultVo(result:ResultVo):void {
			coin = result.coin;
			gem = result.gem;
			exp = result.exp;
			sp = result.sp;
			piao = true;
			levelUp = result.levelUP;
			
		}
		
		public function get isEmpty():Boolean {
			if (coin || gem || exp || sp || maxSp || levelUp) 
			{
				return false;
			}else {
				return true;
			}
		}
		
	}

}