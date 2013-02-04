package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	import happyfish.time.Time;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.classVo.MixClassVo;
	
	/**
	 * 合成术
	 * @author lite3
	 */
	public class MixVo extends BasicVo 
	{
		// cid
		public var cid:int;
		// 合成的数量
		public var num:int;
		// 工作台id
		public var furnaceId:String;
		// 剩余时间
		private var _beginTime:int;
		// 总时间
		private var _totalTime:int;
		// 当前几率
		public var curProbability:int;
		
		// 剩余时间
		public function get remainingTime():int { return Time.getRemainingTime(_beginTime, _totalTime); }
		// 剩余时间
		public function set remainingTime(value:int):void 
		{
			_beginTime = Time.getCurTime();
			_totalTime = value;
		}
		
		// 基类的引用
		public var base:MixClassVo;
		
		// 基类的引用,弱类型
		public function get _base():* { return base; }
		
		
		
		override public function setData(obj:Object):BasicVo 
		{
			super.setData(obj);
			base = DataManager.getInstance().mixData.getMixClass(cid);
			return this;
		}
		
	}

}