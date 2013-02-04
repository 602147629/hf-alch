package 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.scene.world.FriendActionType;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class TigerMachineMain extends ActModuleBase
	{
		private var enemyuserId:int;
		private var obj:Object;
		private var type:int;
		
		public function TigerMachineMain():void 
		{
            EventManager.getInstance().addEventListener(TigerMachineEvent.TIGERMACHCLOSEMOUDLUE,closecomplete);
		}
		
		private function closecomplete(e:TigerMachineEvent):void 
		{
			super.close();
		}		
		
		override public function init(actVo:ActVo, _type:uint = 1):void 
		{
            super.init(actVo, 1);
			//new test();
			obj = DataManager.getInstance().getVar("tigerMachineData");
			
			switch(obj.type)
			{
				case FriendActionType.OCC:
					 type = 1;
					break;
					
				case FriendActionType.ASSISTANCE:
					  type = 3;
					break;					
					
				case FriendActionType.RESIST:
					  type = 2;
					break;					
					
					
			}			
			var command:TigerMachineInitCommond = new TigerMachineInitCommond();
			command.setData(obj.enemyUid, obj.buildId,type);
			command.addEventListener(Event.COMPLETE, commandComplete);
				
		}
		
		private function commandComplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, commandComplete);
			
			if (e.target.objdata.result.status < 0)
			{
				super.close();
			}
			else
			{
				showview();
			}
			
		}
		
		private function showview():void
		{
			 var view:TigerMachineView = DisplayManager.uiSprite.addModule("TigerMachineView", "TigerMachineView", false, AlginType.CENTER, 20, -45) as TigerMachineView;
			 view.setData(obj.enemyUid,obj.buildId,obj.enemyName,obj.enemyFace,obj.type);
			 DisplayManager.uiSprite.setBg(view);			
		}
		
	}
	
}