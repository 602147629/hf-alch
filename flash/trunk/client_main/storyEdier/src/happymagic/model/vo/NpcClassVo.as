package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author 
	 */
	public class NpcClassVo extends BasicVo
	{
		public var id:uint;
		public var cid:uint;
		public var className:String;
		public var faceClass:String;
		public var name:String;
		public var x:String;
		public var z:String;
		public var clickType:uint;	//NPC的点击处理
		public var clickValue:String;
		public var faceX:uint;
		public var faceZ:uint;
		public var fiddleRangeX:uint;
		public var fiddleRangeZ:uint;
		
		public static const CLICKTYPE_NONE:int = 0;
		public static const CLICKTYPE_MODULE:int = 1;
		public static const CLICKTYPE_ACTMODULE:int = 2;
		public static const CLICKTYPE_BUYITEM:int = 3;
		public static const CLICKTYPE_MOVIE:int = 4;
		public static const CLICKTYPE_URL:int = 5;
		public function NpcClassVo() 
		{
			
		}
		
	}

}