package happymagic.hire
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.utils.getQualifiedClassName;
	import happyfish.events.GameMouseEvent;
	import happyfish.events.MainEvent;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happymagic.hire.commands.InitHireCommand;
	import happymagic.hire.data.HireData;
	import happymagic.hire.events.HireActEvent;
	import happymagic.hire.view.HireUISprite;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.UiManager;
	import happymagic.scene.world.MagicWorld;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class Main extends ActModuleBase 
	{
		override public function init(actVo:ActVo, _type:uint = 1):void 
		{
			super.init(actVo, _type);
			if (!HireData.instance.inited)
			{
				HireData.instance.inited = true;
				new InitHireCommand().initHire(initHireComplete);
			}else
			{
				initHireComplete();
			}
			init_complete();
		}
		
		private function closeHandler(e:HireActEvent):void 
		{
			EventManager.removeEventListener(HireActEvent.HIRE_CLOSE, closeHandler);
			close();
		}
		
		private function initHireComplete():void
		{
			var getItemById:Function = MagicWorld(DataManager.getInstance().worldState.world).getItemById;
			var cidList:Array = DataManager.getInstance().gameSetting.barBuildIds;
			for (var i:int = cidList.length - 1; i >= 0; i--)
			{
				if (getItemById(cidList[i]) != null) break;
			}
			
			if (i < 0)
			{
				close();
			}else
			{
				EventManager.addEventListener(HireActEvent.HIRE_CLOSE, closeHandler);
				showHireUI(i + 1);
			}
		}
		
		private function showHireUI(npcId:int):void 
		{
			var hireSprite:HireUISprite = ModuleManager.getInstance().getModule("HireUISprite") as HireUISprite;
			if (!hireSprite)
			{
				var module:ModuleVo = new ModuleVo();
				module.className = getQualifiedClassName(HireUISprite);
				module.name = "HireUISprite";
				hireSprite = ModuleManager.getInstance().addModule(module) as HireUISprite;
			}
			ModuleManager.getInstance().showModule("HireUISprite");
			DisplayManager.uiSprite.setBg(hireSprite);
			hireSprite.show(npcId);
		}
	}
}