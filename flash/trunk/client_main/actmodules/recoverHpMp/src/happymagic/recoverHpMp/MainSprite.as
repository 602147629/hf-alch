package happymagic.recoverHpMp
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.utils.getQualifiedClassName;
	import happyfish.events.MainEvent;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.recoverHpMp.commands.InitRecoverCommand;
	import happymagic.recoverHpMp.model.Data;
	import happymagic.recoverHpMp.model.RoleUpgaradeStarVo;
	import happymagic.recoverHpMp.view.RoleUpgradeQualityUISprite;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class MainSprite extends Sprite 
	{
		
		public function MainSprite()
		{
			EventManager.addEventListener(MainEvent.MAIN_DATA_COMPELTE, showMainIcon);
		}
		
		private function showMainIcon(e:Event):void 
		{
			var icon:Sprite = new RecoverHpMpIcon();
			icon.x = 10;
			icon.y = 120;
			DisplayManager.uiSprite.stage.addChild(icon);
			icon.addEventListener(MouseEvent.CLICK, clickHandler);
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			//DataManager.getInstance().setVar("npcClickValue", { id:95 } );
			init();
		}
		
		public function init(actVo:ActVo = null, _type:uint = 1):void 
		{
			//super.init(actVo, _type);
			
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
		}
		
	}
	
}