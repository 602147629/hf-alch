package happymagic.specialUpgrade
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.utils.getQualifiedClassName;
	import happyfish.display.view.UISprite;
	import happyfish.events.MainEvent;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.specialUpgrade.commands.InitUpgradesCommand;
	import happymagic.specialUpgrade.model.Data;
	import happymagic.specialUpgrade.model.SpecialUpgaradeVo;
	import happymagic.specialUpgrade.view.NotEnoughLevelUISprite;
	import happymagic.specialUpgrade.view.UpgradeUISprite;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class MainSprite extends Sprite 
	{
		
		
		
		public function MainSprite():void 
		{
			EventManager.addEventListener(MainEvent.MAIN_DATA_COMPELTE, showMainIcon);
		}
		
		private function showMainIcon(e:Event):void 
		{
			var icon:Sprite = new BuildingUpgradeIcon();
			icon.x = 10;
			icon.y = 65
			DisplayManager.uiSprite.stage.addChild(icon);
			icon.addEventListener(MouseEvent.CLICK, clickHandler);
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			DataManager.getInstance().setVar("npcClickValue", { id:95 } );
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
			new InitUpgradesCommand().init(start);
		}
		
		private function start():void 
		{
			var cid:int = DataManager.getInstance().getVar("npcClickValue").id;
			var level:int = DataManager.getInstance().currentUser.viliageInfo[cid] + 1;
			
			var info:SpecialUpgaradeVo = Data.instance.getSpecialUpgradeVo(cid, level);
			if (!info)
			{
				upgardeAlreadyMaxLevel();
			}else if (info.needLevel > DataManager.getInstance().currentUser.level)
			{
				showNotEnoughLevel(info.needLevel);
			}else
			{
				var moduleName:String = getQualifiedClassName(UpgradeUISprite);
				var ui:UpgradeUISprite = ModuleManager.getInstance().getModule(moduleName) as UpgradeUISprite;
				if (!ui)
				{
					var moduleVo:ModuleVo = new ModuleVo();
					moduleVo.name = moduleName;
					moduleVo.className = moduleName;
					ui = ModuleManager.getInstance().addModule(moduleVo) as UpgradeUISprite;
				}
				ModuleManager.getInstance().showModule(moduleName);
				DisplayManager.uiSprite.setBg(ui);
				ui.setData(info);
			}
		}
		
		private function upgardeAlreadyMaxLevel():void 
		{
			
		}
		
		private function showNotEnoughLevel(needLevel:int):void 
		{
			var moduleName:String = getQualifiedClassName(NotEnoughLevelUISprite);
			var ui:NotEnoughLevelUISprite = ModuleManager.getInstance().getModule(moduleName) as NotEnoughLevelUISprite;
			if (!ui)
			{
				var moduleVo:ModuleVo = new ModuleVo();
				moduleVo.name = moduleName;
				moduleVo.className = moduleName;
				ui = ModuleManager.getInstance().addModule(moduleVo) as NotEnoughLevelUISprite;
			}
			ModuleManager.getInstance().showModule(moduleName);
			DisplayManager.uiSprite.setBg(ui);
			ui.setData(needLevel);
		}
	}
}
