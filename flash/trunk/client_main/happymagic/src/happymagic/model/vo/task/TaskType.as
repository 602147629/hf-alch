package happymagic.model.vo.task 
{
	/**
	 * 任务类型, 主线,支线,日常
	 * @author lite3
	 */
	public class TaskType 
	{
		
		public static const MAIN:int	= 1; // 主线任务
		public static const LATERAL:int	= 2; // 支线任务
		public static const DAILY:int	= 3; // 日常任务
		
		public static function getType(id:int):int
		{
			return id % 10;
		}
		
	}

}