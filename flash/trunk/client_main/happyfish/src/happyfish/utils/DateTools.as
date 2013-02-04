package happyfish.utils
{
	import com.adobe.utils.IntUtil;
	/**
	 * ...
	 * @author slamjj
	 */
	public class DateTools
	{
		
		public function DateTools() 
		{
			
		}
		
		public static function unix2ASDate(val:uint):String {
			var d:Date = new Date(val * 1000);
			return d.getFullYear()+"-"+ (d.getMonth()+1) + "-" + d.getDate();
			//return d.toLocaleString();
		}
		
		public static function getDateString(time:Date,sp:String="/"):String {
			var str:String = "";
			
			str += time.getFullYear().toString()+sp;
			str += String(time.getMonth() + 1)+sp;
			str += time.getDate().toString();
			
			return str;
		}
		
		public static function getTimeString(time:Date, sp:String = ":"):String {
			var str:String = "";
			
			str += time.getHours().toString()+sp;
			str += time.getMinutes().toString()+sp;
			str += time.getSeconds().toString();
			
			return str;
		}
		
		public static function getTimeStringMin(time:Date, sp:String = ":"):String {
			var str:String = "";
			
			str += time.getHours().toString()+sp;
			str += time.getMinutes().toString();
			
			return str;
		}
		
		public static function getHoursTimeStr(time:Date):String {
			var str:String = "";
			
			str += (time.getDay()+time.getHours()).toString()+"小时 ";
			str += time.getMinutes().toString()+"分钟";
			
			return str;
		}
		
		/**
		 * 格式化剩余时间 由http://www.php.net/manual/zh/dateinterval.format.php演变而来
		 * @param	t 总时间,单位:秒
		 * @param	formart %转义字符,d:日,Hh:时,Ii:分,Ss:秒 大写为两为(01,12),小写为1位或两位(1,12)
		 * 			时间的每个字符必须以%开头.例如 "还剩%y年%m月%d天%h小时%i分%s秒" ==> "还剩5年6月10天8小时9分7秒"
		 * @return
		 */
		public static function getRemainingTime(t:int, formart:String = "%d天%h时%i分%s秒"):String
		{
			
			var day:int = t / (60 * 60 * 24);
			t %= 60 * 60 * 24;
			var hour:int = t / (60 * 60 );
			t %= 60 * 60;
			var mins:int = t / 60;
			var sec:int = t % 60;
			
			const formatString:String = "dHhIiSs";
			const tArr:Array = [day, hour, hour, mins, mins, sec, sec];
			if (hour < 10) tArr[1] = "0" + hour;
			if (mins < 10) tArr[3] = "0" + mins;
			if (sec < 10) tArr[5] = "0" + sec;
			
			
			
			var len:int = formart.length;
			var s:String = "";
			var idx:int;
			var firstIdx:int = tArr.length - 1;
			const outArr:Array = [];
			for (var i:int = 0; i < len-1; i++)
			{
				var c:String = formart.charAt(i);
				if ('%' == c)
				{
					var tc:String = formart.charAt(i + 1);
					// 转义
					if ('%' == tc)
					{
						s += '%';
						i++;
					}
					// 时间格式
					else if ((idx = formatString.indexOf(tc)) != -1)
					{
						outArr.push(s);
						outArr.push(idx);
						if (firstIdx > idx) firstIdx = idx;
						s = "";
						i++;
					}
					// 普通的%
					else
					{
						s += '%' + tc;
						i++;
					}
				}else 
				{
					s += c;
				}
			}
			if (i == len - 1)
			{
				s += formart.charAt(len - 1);
				outArr.push(s);
			}
			switch(firstIdx)
			{
				case 1 :
				case 2 :
					hour += day * 24;
					if (hour >= 10) tArr[1] = hour;
					tArr[2] = hour;
					break;
					
				case 3 :
				case 4 :
					mins += (day * 24 + hour) * 60;
					if (mins >= 10) tArr[3] = mins;
					tArr[4] = mins;
					break;
					
				case 5 :
				case 6 :
					sec += ((day * 24 + hour) * 60 + mins) * 60;
					if (mins >= 10) tArr[5] = sec;
					tArr[6] = mins;
					break;
			}
			s = "";
			len = outArr.length;
			for (i = 0; i < len; i++)
			{
				s += outArr[i] is String ? outArr[i] : tArr[outArr[i]];
			}
			
			return s;
		}
		
		/**
		 * 格式化剩余时间 由http://www.php.net/manual/zh/dateinterval.format.php演变而来
		 * @param	t 总时间,单位:秒
		 * @param	formart %转义字符,[]分组字符,d:日,Hh:时,Ii:分,Ss:秒 大写为两为(01,12),小写为1位或两位(1,12)
		 * 				[]分组符号用于舍弃0日期时使用,要显示[]字符时用%转义, 如%[ %]
		 * 				时间的每个字符必须以%开头.例如 "还剩[%y年][%m月][%d天][%h小时][%i分][%s秒]" ==> "还剩5年6月10天8小时9分7秒"
		 * @return
		 */
		public static function getRemainningTimeWithout0(t:int, formart:String = "[%d天][%h时][%i分][%s秒]", cutHead:Boolean = true, cutTail:Boolean = false, showLastOnAllZero:Boolean = true):String
		{
			return RemainingTimeFormat.parseFormat(t, formart, cutHead, cutTail, showLastOnAllZero);
		}
		
		/**
		 * 获得还剩多少时间文字
		 * @param	value	毫秒
		 * @param	showSec
		 * @param	dayStr
		 * @param	hourStr
		 * @param	minStr
		 * @param	secStr
		 * @return
		 */
		public static function getLostTime(value:uint, showSec:Boolean = true, dayStr:String="天", hourStr:String="时", minStr:String="分", secStr:String="秒",allShow:Boolean=false):String {
			
			var day:uint;
			var hour:uint;
			var mins:uint;
			var sec:uint;
			
			
			day = Math.floor(value / (1000 * 60 * 60 * 24));
			value = value % (1000 * 60 * 60 * 24);
			hour = Math.floor(value / (1000 * 60 * 60 ));
			value = value % (1000 * 60 * 60 );
			mins = Math.floor(value / (1000 * 60 ));
			value = value % (1000 * 60);
			sec = Math.floor(value / (1000 ));
			//value = value % (1000);
			
			var str:String="";
			if (day>0) 
			{
				str += day.toString() + dayStr;
			}
			
			if (hour>0) 
			{
				str += hour.toString() + hourStr;
			}
			
			if (mins>0) 
			{
				if ((str.length>0 || allShow )&& hour==0 ) 
				{
					str += "00" + hourStr;
				}
				str += mins.toString() + minStr;
			}
			
			if (!showSec) 
			{
				if ((str.length>0 || allShow ) && mins==0) 
				{
					str += "00" + minStr;
				}
				return str;
			}
			
			if (sec>0) 
			{
				if ((str.length>0 || allShow) && mins==0) 
				{
					str += "00" + minStr;
				}
				str += sec.toString() + secStr;
			}else {
				if ((str.length>0 || allShow) && sec==0) 
				{
					str += "00" + secStr;
				}
			}

			return  str;
		}
	}

}