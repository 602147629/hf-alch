package manager 
{
	import model.vo.StrengThenVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class StrengThenManager 
	{
		private static var instance:StrengThenManager;	
		public var strengThenArr:Array;
		public function StrengThenManager(access:Private) 
		{
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
				}
			}
			else
			{	
				throw new Error( "DataManager"+"单例" );
			}			
		}
		
		public static function getInstance():StrengThenManager
		{
			if (instance == null)
			{
				instance = new StrengThenManager( new Private() );
			}
			return instance;
		}
		
		public function getStrengThenVo(_id:int):StrengThenVo
		{
			for (var i:int = 0; i < strengThenArr.length; i++) 
			{
				if (strengThenArr[i].id == _id)
				{
					return strengThenArr[i];
				}
			}
			return null;
		}
		
	}

}
class Private {}