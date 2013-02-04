package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author jj
	 */
	public class LevelInfoVo extends BasicVo
	{
		public var level:uint;
		public var maxExp:uint;
		public var maxSp:uint;
		public var items:Array;
		public var gem:uint;
		public var coin:uint;
		public var assistCnt:int;
		
		public function LevelInfoVo() 
		{
			
		}
		
	}

}