package happymagic.roleInfo.vo 
{
	import happyfish.model.vo.BasicVo;
	import happyfish.time.Time;
	import happymagic.model.vo.RoleVo;
	
	/**
	 * 雇用位
	 * @author lite3
	 */
	public class HireVo extends BasicVo 
	{
		private var totalTime:int;
		private var beginTime:int;
		
		public var id:int;
		public var price:int;
		public var content:String;
		public var roleVo:RoleVo
		
		public function get remainingTime():int { return Time.getRemainingTime(beginTime, totalTime); }
		public function set remainingTime(value:int):void
		{
			totalTime = value;
			beginTime = Time.getCurTime();
		}
		
		public function hasTime():Boolean { return remainingTime > 0; };
		
		override public function setData(obj:Object):BasicVo 
		{
			if (obj.roleVo)
			{
				roleVo = new RoleVo();
				roleVo.setData(obj.roleVo);
				delete obj.roleVo;
			}
			return super.setData(obj);
		}
	}

}