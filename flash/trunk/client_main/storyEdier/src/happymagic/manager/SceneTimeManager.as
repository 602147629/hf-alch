package happymagic.manager 
{
	import happyfish.time.TimeActionCommand;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.SceneType;
	
	/**
	 * ...
	 * @author 
	 */
	public class SceneTimeManager 
	{
		
		public function SceneTimeManager(access:Private) 
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
				throw new Error( "SceneTimeManager"+"单例" );
			}
		}
		
		/**
		 * 初始化场景内各种时间倒计时
		 */
		public function initSceneTime():void {
			if (!timeChecker) 
			{
				timeChecker = new TimeActionCommand();
			}else {
				timeChecker.clearAll();
			}
			
			var user:UserVo = DataManager.getInstance().curSceneUser;
			switch (DataManager.getInstance().curSceneType) 
			{
				case SceneType.TYPE_FRIEND_VILIAGE:
					//if(user.safeTime) timeChecker.setTimeAction(user.safeTime, safeTimeout);
					//if(user.atkSafeTime) timeChecker.setTimeAction(user.atkSafeTime, atkSafeTimeout);
					//if(user.ownerAwardTime) timeChecker.setTimeAction(user.ownerAwardTime, taxesTimeout);
					//if(user.ownerEndTime) timeChecker.setTimeAction(user.ownerEndTime, occTimeout);
				break;
				
				
				case SceneType.TYPE_SELF_VILIAGE:
					//if(user.safeTime) timeChecker.setTimeAction(user.safeTime, safeTimeout);
					//if(user.ownerEndTime) timeChecker.setTimeAction(user.ownerEndTime, occTimeout);
					//if(user.nextSafeTime) timeChecker.setTimeAction(user.nextSafeTime, nextSafeTimeout);
				break;
			}
		}
		
		private function nextSafeTimeout():void 
		{
			
		}
		
		private function occTimeout():void 
		{
			
		}
		
		private function taxesTimeout():void 
		{
			
		}
		
		private function atkSafeTimeout():void 
		{
			
		}
		
		private function safeTimeout():void 
		{
			
		}
		
		public static function getInstance():SceneTimeManager
		{
			if (instance == null)
			{
				instance = new SceneTimeManager( new Private() );
			}
			return instance;
		}
		
		
		private static var instance:SceneTimeManager;
		private var timeChecker:TimeActionCommand;
		
	}
	
}
class Private {}