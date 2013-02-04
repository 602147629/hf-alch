package happyfish.time 
{
	import flash.utils.getTimer;
	import flash.utils.setInterval;
	/**
	 * 系统时间 所有时间单位都为秒
	 * @author lite3
	 */
	public class Time 
	{
		
		private static var _fpBeginTime:int;
		private static var _curTime:int;
		private static var running:Boolean = false;
		
		/**
		 * 设置系统时间
		 * @param	time
		 */
		public static function setCurrTime(time:int):void
		{
			_fpBeginTime = time - getTimer() / 1000;
			_curTime = time;
			if (!running)
			{
				running = true;
				setInterval(refreshTime, 500);
			}
		}
		
		/**
		 * 获取剩余时间
		 * @param	begin 开始时间
		 * @param	total 总时间
		 * @return
		 */
		public static function getRemainingTime(begin:int, total:int):int
		{
			var t:int = begin + total - _curTime;
			if (t < 0) t = 0;
			return t;
		}
		
		/**
		 * 获取离END的剩余时间
		 * @param	end		到期时间戳
		 * @return
		 */
		public static function getRemainingTimeByEnd(end:int):int {
			var t:int = end - _curTime;
			if (t < 0) t = 0;
			return t;
		}
		
		/**
		 * 已经经过的时间
		 * @param	begin
		 * @return
		 */
		public static function getDuration(begin:int):int { return _curTime - begin; }
		
		/**
		 * 当前时间
		 * @return
		 */
		public static function getCurTime():int { return _curTime; }
		
		/**
		 * 当前日期
		 * @return
		 */
		public static function getCurDate():Date { return new Date(_curTime * 1000); }
		
		/**
		 * fp时间 同 flash.utils.getTimer()/1000
		 * @return
		 */
		public static function getFpTime():int { return _curTime - _fpBeginTime; }
		
		// 刷新时间
		private static function refreshTime():void
		{
			_curTime = getTimer() / 1000 + _fpBeginTime;
		}
	}
}