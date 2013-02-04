package happymagic.roleInfo
{
	import flash.utils.getQualifiedClassName;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.roleInfo.events.RoleInfoActEvent;
	import happymagic.roleInfo.view.MySoldierListUISprite;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class Main extends ActModuleBase 
	{
		
		override public function init(actVo:ActVo, _type:uint = 1):void 
		{
			super.init(actVo, _type);
			EventManager.addEventListener(RoleInfoActEvent.ROLE_INFO_CLOSE, closeHandler);
			
			var uiSprite:MySoldierListUISprite = ModuleManager.getInstance().getModule("MySoldierListUISprite") as MySoldierListUISprite;
			if (!uiSprite)
			{
				var module:ModuleVo = new ModuleVo();
				module.className = getQualifiedClassName(MySoldierListUISprite);
				module.name = "MySoldierListUISprite";
				uiSprite = ModuleManager.getInstance().addModule(module) as MySoldierListUISprite;
			}
			ModuleManager.getInstance().showModule("MySoldierListUISprite");
			DisplayManager.uiSprite.setBg(uiSprite);
			uiSprite.show();
			
			init_complete();
			
			//ActModuleManager.getInstance().addActModule(DataManager.getInstance().getActByName("StrengThen"));
		}
		
		private function closeHandler(e:RoleInfoActEvent):void 
		{
			EventManager.removeEventListener(RoleInfoActEvent.ROLE_INFO_CLOSE, closeHandler);
			close();
		}
		
		
	}
	
}