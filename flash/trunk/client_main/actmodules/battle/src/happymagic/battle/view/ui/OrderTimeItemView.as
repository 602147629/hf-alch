package happymagic.battle.view.ui 
{
	import flash.geom.Rectangle;
	import happyfish.display.view.IconView;
	import happyfish.utils.display.FiltersDomain;
	import happymagic.model.vo.RoleVo;
	import happymagic.utils.AvatarUtil;
	/**
	 * ...
	 * @author 
	 */
	public class OrderTimeItemView extends orderTimeItemUi 
	{
		private var faceIcon:IconView;
		public var data:RoleVo;
		private var openFlag:Boolean;
		private var infoView:orderRoleInfoUi;
		
		public function OrderTimeItemView(role:RoleVo) 
		{
			data = role;
			buttonMode = true;
			mouseChildren = false;
			init();
		}
		
		private function init():void 
		{
			if (!faceIcon) 
			{
				faceIcon = new IconView();
				
			}
			
			faceIcon.setData(data.sFaceClass);
			addChild(faceIcon);
			
		}
		
		public function showInfo():void {
			if (!infoView) 
			{
				infoView = new orderRoleInfoUi();
				infoView.levelTxt.text = data.level.toString();
				infoView.nameTxt.text = data.name;
				infoView.hpTxt.text = data.hp.toString();
			}
			
			var rect:Rectangle = getRect(this);
			infoView.x = 10;
			infoView.y = rect.top - 5;
			addChild(infoView);
		}
		
		public function hideInfo():void {
			if (infoView) 
			{
				if (infoView.parent) infoView.parent.removeChild(infoView);
			}
		}
		
		public function setLight(flag:Boolean):void {
			if (openFlag == flag) return;
			openFlag = flag;
			
			if (openFlag) 
			{
				filters = [FiltersDomain.yellowGlow];
				showInfo();
			}else {
				filters = [];
				hideInfo();
			}
			
			
		}
	}

}