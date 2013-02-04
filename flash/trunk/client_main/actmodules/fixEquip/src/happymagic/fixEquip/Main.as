package happymagic.fixEquip
{
	import flash.events.Event;
	import flash.utils.getQualifiedClassName;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happymagic.fixEquip.model.Data;
	import happymagic.fixEquip.view.FixEquipUISprite;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class Main extends ActModuleBase 
	{
		override public function init(actVo:ActVo, _type:uint = 1):void 
		{
			super.init(actVo, _type);
			if (!Data.instance.inited)
			{
				var o:Object = DataManager.getInstance().initStaticData.fixEquip;
				delete DataManager.getInstance().initStaticData.fixEquip;
				Data.instance.npcFace = o.npcFace;
				Data.instance.chatList = o.chat.split("&&");
				Data.instance.inited = true;
			}
			
			var ui:FixEquipUISprite = ModuleManager.getInstance().getModule("fixEquipUISprite") as FixEquipUISprite;
			if (!ui)
			{
				var module:ModuleVo = new ModuleVo();
				module.name = "fixEquipUISprite";
				module.className = getQualifiedClassName(FixEquipUISprite);
				ui = ModuleManager.getInstance().addModule(module) as FixEquipUISprite;
			}
			ModuleManager.getInstance().showModule("fixEquipUISprite");
			DisplayManager.uiSprite.setBg(ui);
			ui.addEventListener(Event.CLOSE, closeModule);
			init_complete();
		}
		
		private function closeModule(e:Event):void 
		{
			e.target.removeEventListener(Event.CLOSE, closeModule);
			close();
		}
		
	}
	
}