package happymagic.model.vo 
{
	/**
	 * ...
	 * @author jj
	 */
	public class ConditionType
	{
		public static const NONE:int = 0;
		public static const ITEM:uint = 1;
		public static const USER:uint = 2;
		public static const Mob:uint  = 3; //怪
		public static const UNKNOW:uint  = 4; //问号
		
		public static const USER_COIN:String = "coin";
		public static const USER_GEM:String = "gmoney";
		public static const USER_EXP:String = "exp";
		public static const USER_BATTLE_EXP:String = "battleExp";
		public static const USER_SP:String = "sp";
		public static const USER_SP_MAX:String = "spMax";
		
		
		public static const idArr:Object = {coin:1,gmoney:2,exp:3};
		public function ConditionType() 
		{
			
		}
		
		public static function StringToInt(str:String):uint {
			return idArr[str];
		}
		
	}

}