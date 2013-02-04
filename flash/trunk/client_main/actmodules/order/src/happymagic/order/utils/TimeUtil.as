package happymagic.order.utils 
{
	import happyfish.manager.local.LocaleWords;
	import happyfish.utils.DateTools;
	/**
	 * ...
	 * @author lite3
	 */
	public class TimeUtil 
	{
		/**
		 * 
		 * @param	t 剩余时间 单位:秒
		 * @return
		 */
		public static function getOrderRemainingTimeHtml(t:int, bold:Boolean = false):String
		{
			var color:String;
			var str:String;
			if (-1 == t)
			{
				color = "#391D0B";
				str = LocaleWords.getInstance().getWord("order-infiniteTime");
			}else if(0 == t)
			{
				color = "#5C1301";
				str = LocaleWords.getInstance().getWord("order-alreadyOverdue");
			}else
			{
				color = "#005E2F";
				str = DateTools.getRemainningTimeWithout0(t);
			}
			str = '<font color="' + color + '">' + str + '</font>';
			if (bold) str = "<b>" + str + "</b>";
			return str;
		}
		
	}

}