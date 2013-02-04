package happymagic.model.vo.order 
{
	/**
	 * ...
	 * @author lite3
	 */
	public class OrderType 
	{
		public static const REQUEST:int	= 1; // 未接
		public static const WORKING:int = 2; // 已接,正在进行中
		public static const COMPLETED:int = 3; // 已完成
		public static const FAILED:int	= 4; // 已失败
	}

}