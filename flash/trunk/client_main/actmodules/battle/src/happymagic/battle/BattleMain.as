package happymagic.battle
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.net.registerClassAlias;
	import happyfish.display.view.UISprite;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.interfaces.IModule;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happymagic.battle.Battle;
	import happymagic.battle.view.ui.BattleFaultView;
	import happymagic.manager.DisplayManager;
	
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class BattleMain extends ActModuleBase
	{
		
		private var uiSprite:BattleUISprite;
		
		public function BattleMain()
		{
			registerClassAlias("BattleUISprite2", BattleUISprite);
			registerClassAlias("BattleFaultView2", BattleFaultView);
		}
		
		override public function init(actVo:ActVo, _type:uint = 1):void
		{
			var moduleVo:ModuleVo = new ModuleVo;
			moduleVo.name = "battle";
			moduleVo.className = "happymagic.battle.BattleUISprite";
			
			var tmpModule:IModule = ModuleManager.getInstance().addModule(moduleVo);
			ModuleManager.getInstance().showModule("battle");
			DisplayManager.uiSprite.setBg(tmpModule);
			
			super.init(actVo, _type);
			
			EventManager.getInstance().addEventListener(Battle.BATTLE_END, onBattleEnd);
		}
		
		private function onBattleEnd(event:Event):void
		{
			EventManager.getInstance().removeEventListener(Battle.BATTLE_END, onBattleEnd);
			close();
			EventManager.getInstance().dispatchEvent(new Event("battleFinish"));
		}
		
	}
	
}