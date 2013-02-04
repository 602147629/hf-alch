package happymagic.scene.world.control 
{
	import happymagic.manager.DataManager;
	import happymagic.manager.PublicDomain;
	import happymagic.model.vo.SceneState;
	import happymagic.model.vo.SceneClassVo;
	import happymagic.scene.world.MagicWorld;
	/**
	 * 移动场景到新的地图(如渔人海湾之类)
	 * 
	 * 修改
	 * 不论是程序初始化还是通过大地图、传送门进入家、野外或地下城 都可使用此Command
	 * 修改者 XiaJunJie 修改于 2011.11.11
	 * 
	 * @author slamjj
	 */
	public class ChangeSceneCommand 
	{
		
		/**
		 * 
		 * @param	data	场景VO
		 */
		public function ChangeSceneCommand() 
		{
			var currentSceneId:int = DataManager.getInstance().currentUser.currentSceneId;
			var sceneClassVo:SceneClassVo = DataManager.getInstance().getSceneClassById(currentSceneId);
			
			var tmpworld:MagicWorld = (DataManager.getInstance().worldState.world as MagicWorld);
			//清除此场景
			tmpworld.clear();
			
			var world_data:Object = new Object();
			world_data['type'] = sceneClassVo.type;
			if (sceneClassVo.type==SceneClassVo.HOME || sceneClassVo.type==0) //家
			{
				world_data['decorList'] = DataManager.getInstance().decorList;
				world_data['floorList'] = DataManager.getInstance().floorList;
				world_data['floorList2'] = DataManager.getInstance().floorList2 ? DataManager.getInstance().floorList2 : [];
				world_data['wallList'] = DataManager.getInstance().wallList;
				world_data['userInfo'] = DataManager.getInstance().curSceneUser;
			}
			else
			{
				world_data['decorList'] = DataManager.getInstance().decorList;
				world_data['floorList'] = DataManager.getInstance().floorList;
				world_data['floorList2'] = DataManager.getInstance().floorList2 ? DataManager.getInstance().floorList2 : [];
				world_data['wallList'] = [];
				world_data['userInfo'] = DataManager.getInstance().curSceneUser;
			}
			
			DataManager.getInstance().worldState.world.create(world_data, true);
			
		}
	}

}