package happymagic.recoverHpMp
{
	import flash.events.Event;
	import flash.utils.getQualifiedClassName;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.recoverHpMp.commands.InitRecoverCommand;
	import happymagic.recoverHpMp.model.Data;
	import happymagic.recoverHpMp.model.RoleUpgaradeStarVo;
	import happymagic.recoverHpMp.view.RoleUpgradeQualityUISprite;
	import happymagic.recoverHpMp.view.ui.OrderLevel;
	import happymagic.recoverHpMp.view.ui.RoleLevel;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class Main extends ActModuleBase 
	{
		
		override public function init(actVo:ActVo, _type:uint = 1):void 
		{
			OrderLevel;
			RoleLevel;
			super.init(actVo, _type);
			
			if (!Data.instance.inited)
			{
				loadData();
			}else
			{
				start();
			}
		}
		
		private function loadData():void 
		{
			new InitRecoverCommand().init(start);
		}
		
		private function start():void 
		{
			var moduleName:String = getQualifiedClassName(RoleUpgradeQualityUISprite);
			var ui:RoleUpgradeQualityUISprite = ModuleManager.getInstance().getModule(moduleName) as RoleUpgradeQualityUISprite;
			if (!ui)
			{
				var moduleVo:ModuleVo = new ModuleVo();
				moduleVo.name = moduleName;
				moduleVo.className = moduleName;
				ui = ModuleManager.getInstance().addModule(moduleVo) as RoleUpgradeQualityUISprite;
			}
			ModuleManager.getInstance().showModule(moduleName);
			DisplayManager.uiSprite.setBg(ui);
			ui.show();
			ui.addEventListener(Event.CLOSE, closeActModule);
		}
		
		private function closeActModule(e:Event):void 
		{
			e.target.removeEventListener(Event.CLOSE, closeActModule);
			close();
		}
		
		
		
	}
	
}