package happyfish.utils 
{
	/**
	 * ...
	 * @author lite3
	 */
	public class RemainingTimeFormat 
	{
		private static const formatString:String = "dHhIiSs";
		private static const pair:Array = [];
		private static var pairEnd:int = 0;
		private static var isSide:Boolean = true; // 是否第一层,外层
		
		private static var first:int = 0;
		private static var last:int = 0;
		
		/**
		 * 
		 * @param	t 时间,单位:秒
		 * @param	format 格式串
		 * @param	cutHead 是否弃头
		 * @param	cutTail 是否弃尾
		 * @param	showLastOnAllZero 当弃头弃尾并且全为0时是否要显示最后一位,例如 xxx[%i分][%s秒] ==> xxx0秒
		 * @return 格式化后的字串
		 */
		public static function parseFormat(t:int, format:String, cutHead:Boolean = false, cutTail:Boolean = false, showLastOnAllZero:Boolean = false):String
		{
			var day:int = t / (60 * 60 * 24);
			t %= 60 * 60 * 24;
			var hour:int = t / (60 * 60 );
			t %= 60 * 60;
			var mins:int = t / 60;
			var sec:int = t % 60;
			
			const tArr:Array = [day, hour, hour, mins, mins, sec, sec];
			const formatArr:Array = [];
			
			if (hour < 10) tArr[1] = "0" + hour;
			if (mins < 10) tArr[3] = "0" + mins;
			if (sec < 10) tArr[5] = "0" + sec;
			
			pair.length = 0;
			pairEnd = 0;
			isSide = true;
			
			var idx:int;
			var s:String = "";
			var len:int = format.length;
			
			for (var i:int = 0; i < len; i++)
			{
				var c:String = format.charAt(i);
				if ('%' == c)
				{
					var tc:String = format.charAt(i + 1);
					// 转义
					if ('%' == tc)
					{
						s += '%';
						i++;
					}
					// 时间格式
					else if ((idx = formatString.indexOf(tc)) != -1)
					{
						push(s);
						push(new T(tc, tArr[idx], idx));
						if (!isSide)
						{
							if (pair[pairEnd].first > idx) pair[pairEnd].first = idx;
							pair[pairEnd].last = idx;
							// 占位,表示该位存在于format字串
							formatArr[idx] ||= 0;
							if (tArr[idx] > 0)
							{
								// 表示该位非0
								formatArr[idx] ||= 1;
								pair[pairEnd].notZero = true;
							}
						}
						s = "";
						i++;
					}
					else if ('[' == tc || ']' == tc)
					{
						s += tc;
						i++;
					}
					// 普通的%
					else
					{
						s += '%' + tc;
						i++;
					}
				}else if ('[' == c)
				{
					push(s);
					s = "";
					isSide = false;
					pair[pairEnd] = [];
					pair[pairEnd].first = int.MAX_VALUE;
					pair[pairEnd].last = -1;
				}else if (']' == c)
				{
					push(s);
					s = "";
					isSide = true;
					pairEnd++;
				}else 
				{
					s += c;
				}
			}
			push(s);
			parseNotZeroPos(formatArr);
			return joinArr(pair, cutHead, cutTail, showLastOnAllZero);
		}
		
		private static function parseNotZeroPos(tArr:Array):void 
		{
			var len:int = tArr.length;
			first = len;
			last = -1;
			for (var i:int = 0; i < len; i++)
			{
				if (tArr[i] > 0)
				{
					if (len == first) first = i;
					last = i;
				}
			}
		}
		
		private static function joinArr(arr:Array, cutHead:Boolean, cutTail:Boolean, showLastOnAllZero:Boolean):String
		{
			var allZero:Boolean = -1 == last;
			var s:String = "";
			for (var i:int = arr.length - 1; i >= 0; i--)
			{
				var c:* = arr[i];
				if (c is Array)
				{
					if (allZero)
					{
						if (showLastOnAllZero && c.last == first - 1)
						{
							s = joinArr(c, cutHead, cutTail, showLastOnAllZero) + s;
						}
					}else
					{
						if (c.notZero){ s = joinArr(c, cutHead, cutTail, showLastOnAllZero) + s; }
						else if (cutHead && c.last < first) {}
						else if (cutTail && c.first > last) {}
						else { s = joinArr(c, cutHead, cutTail, showLastOnAllZero) + s; }
					}
					
				}else if (c is T)
				{
					s = T(c).n + s;
				}else
				{
					s = c + s;
				}
			}
			return s;
		}
		
		private static function push(s:*):void
		{
			if (!s) return;
			if (isSide)
			{
				pair[pairEnd++] = s;
			}else
			{
				pair[pairEnd].push(s);
			}
		}
		
	}

}

class T
{
	public var s:String = null;
	public var n:int = 0;
	public var idx:int = 0;
	
	public function T(s:String, n:int, idx:int)
	{
		this.s = s;
		this.n = n;
		this.idx = idx;
	}
}