package happymagic.recoverHpMp.view 
{
	import flash.display.SimpleButton;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import flash.utils.getQualifiedClassName;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happyfish.utils.display.FiltersDomain;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.RoleVo;
	import happymagic.recoverHpMp.commands.RecoverHpMpCommand;
	import happymagic.recoverHpMp.model.Data;
	import happymagic.recoverHpMp.model.RoleUpgaradeStarVo;
	import happymagic.recoverHpMp.view.ui.RoleUpgradeQualityUI;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class RoleUpgradeQualityUISprite extends UISprite 
	{
		private var npcIcon:IconView;
		
		private var iview:RoleUpgradeQualityUI;
		
		private var info:RoleUpgaradeStarVo;
		
		public function RoleUpgradeQualityUISprite() 
		{
			iview = new RoleUpgradeQualityUI();
			_view = iview;
			
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			
			var rect:Rectangle = new Rectangle(iview.border.x, iview.border.y, iview.border.width, iview.border.height);
			npcIcon = new IconView(rect.width, rect.height, rect);
			iview.addChildAt(npcIcon, iview.getChildIndex(iview.border));
			iview.removeChild(iview.border);
			
			iview.addEventListener(MouseEvent.CLICK, clickHandler);
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			switch(e.target)
			{
				case iview.upgradeBtn :
					showUpgardePanel();
					closeMe();
					break;
					
				case iview.recoverBtn :
					new RecoverHpMpCommand().recover(show);
					iview.recoverBtn.mouseEnabled = false;
					break;
					
				case iview.closeBtn :
					closeMe();
					break;
			}
		}
		
		public function show(success:Boolean = false):void
		{
			iview.recoverBtn.mouseEnabled = true;
			
			var datam:DataManager = DataManager.getInstance();
			var arr:Array = datam.roleData.getMyRoles();
			var hp:int = 0;
			var mp:int = 0;
			for (var i:int = arr.length - 1; i >= 0; i--)
			{
				var role:RoleVo = arr[i];
				if (RoleVo.TEMP == role.label) continue;
				hp += role.maxHp - role.hp;
				mp += role.maxMp - role.mp;
			}
			var coin:int = Math.round(datam.gameSetting.hpPrice * hp + datam.gameSetting.mpPrice * mp);
			iview.coinTxt.text = coin + "";
			
			var quality:int = datam.roleData.getRole(0).quality;
			
			var curInfo:RoleUpgaradeStarVo = Data.instance.getInfo(quality);
			if (curInfo)
			{
				npcIcon.setData(curInfo.npcClass);
				iview.chatTxt.text = curInfo.npcChat;
			}
			info = Data.instance.getInfo(quality + 1);
			if (!curInfo)
			{
				npcIcon.setData(info.npcClass);
				iview.chatTxt.text = info.npcChat;
			}
			iview.upgradeBtn.mouseEnabled = info != null;
			iview.upgradeBtn.filters = info ? [] : [FiltersDomain.grayFilter];
		}
		
		private function showUpgardePanel():void 
		{
			var moduleName:String = getQualifiedClassName(UpgradeStarUISprite);
			var ui:UpgradeStarUISprite = ModuleManager.getInstance().getModule(moduleName) as UpgradeStarUISprite;
			if (!ui)
			{
				var moduleVo:ModuleVo = new ModuleVo();
				moduleVo.name = moduleName;
				moduleVo.className = moduleName;
				ui = ModuleManager.getInstance().addModule(moduleVo) as UpgradeStarUISprite;
			}
			ModuleManager.getInstance().showModule(moduleName);
			DisplayManager.uiSprite.setBg(ui);
			ui.setData(info);
		}
	}
}