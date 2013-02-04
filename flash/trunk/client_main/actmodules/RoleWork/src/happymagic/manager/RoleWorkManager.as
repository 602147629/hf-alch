package happymagic.manager 
{
	import happymagic.model.vo.data.RoleWorkData;
	/**
	 * ...
	 * @author ZC
	 */
	public class RoleWorkManager 
	{
		private static var instance:RoleWorkManager;
		public const roleWorkData:RoleWorkData = new RoleWorkData();
		
		public function RoleWorkManager(access:Private) 
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
				throw new Error( "RoleWorkManager"+"单例" );
			}
		}
		
		public static function getInstance():RoleWorkManager
		{
			if (instance == null)
			{
				instance = new RoleWorkManager( new Private() );
			}
			return instance;
		}		
		
	}
}
class Private {}