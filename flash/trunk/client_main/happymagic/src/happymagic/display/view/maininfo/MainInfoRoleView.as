package happymagic.display.view.maininfo 
{
	import flash.display.Sprite;
	import flash.geom.Rectangle;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.RoleVo;
	
	/**
	 * ...
	 * @author 
	 */
	public class MainInfoRoleView extends Sprite 
	{
		
		public var playerInfo:MainInfoPlayerRoleView;
		private var otherInfos:Object;
		public function MainInfoRoleView() 
		{
			init();
		}
		
		public function init():void {
			while (numChildren>0) 
			{
				removeChildAt(0);
			}
			
			otherInfos = new Object();
			var tmp:MainInfoOtherRoleView;
			var roles:Array = DataManager.getInstance().roleData.getMyRoles();
			var otherCount:int=0;
			for (var i:int = 0; i < roles.length; i++) 
			{
				if ((roles[i] as RoleVo).pos<0) 
				{
					continue;
				}
				if ((roles[i] as RoleVo).label==RoleVo.MAIN_ROLE) 
				{
					playerInfo = new MainInfoPlayerRoleView();
					playerInfo.x = 0;
					playerInfo.y = 0;
					playerInfo.setData(roles[i]);
					addChild(playerInfo);
				}else {
					tmp = new MainInfoOtherRoleView();
					tmp.x = 1;
					tmp.y = 56+42*otherCount;
					tmp.setData(roles[i]);
					otherInfos[roles[i].id] = tmp;
					addChild(tmp);
					otherCount++;
				}
				
			}
		}
		
		public function initInfo():void {
			var tmp:MainInfoOtherRoleView;
			var roles:Array = DataManager.getInstance().roleData.getMyRoles();
			
			for (var i:int = 0; i < roles.length; i++) 
			{
				if ((roles[i] as RoleVo).label==RoleVo.MAIN_ROLE) 
				{
					playerInfo.setData(roles[i]);
				}else {
					tmp = otherInfos[roles[i].id];
					if (tmp) 
					{
						tmp.setData(roles[i]);
					}
				}
			}
		}
		
		public function show():void {
			visible = true;
		}
		
		public function hide():void {
			visible = false;
		}
		
	}

}