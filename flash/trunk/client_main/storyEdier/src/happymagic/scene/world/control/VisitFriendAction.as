package happymagic.scene.world.control 
{
	import com.brokenfunction.json.decodeJson;
	import flash.events.Event;
	import flash.geom.Rectangle;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.manager.module.interfaces.IModule;
	import happyfish.scene.astar.Node;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.WorldState;
	import happyfish.utils.display.McShower;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.AddWineCommand;
	import happymagic.model.command.MoveSceneCommand;
	import happymagic.model.command.ShowStoryCommand;
	import happymagic.model.vo.NpcClassVo;
	import happymagic.model.vo.NpcVo;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.award.AwardItemManager;
	import happymagic.scene.world.bigScene.NpcView;
	import happymagic.scene.world.grid.item.SpecialBuild;
	/**
	 * ...
	 * @author 
	 */
	public class VisitFriendAction extends MouseDefaultAction 
	{
		private var curItem:IsoItem;
		private var tmpItem:SpecialBuild;
		
		public function VisitFriendAction($state:WorldState, $stack_flg:Boolean = false) 
		{
			super($state, false);
		}
		
		override public function onNpcOver(e:GameMouseEvent):void 
		{
			if ((e.item as NpcView).npcvo.clickType) {
				e.item.showGlow();
				(e.item as NpcView).showName(e.item.data.name);
			}
		}
		
		override public function onNpcOut(e:GameMouseEvent):void 
		{
			if ((e.item as NpcView).npcvo.clickType ) {
				e.item.hideGlow();
				(e.item as NpcView).hideName();
			}
		}
		
		override public function onNpcClick(e:GameMouseEvent):void 
		{
			//屏蔽地板点击事件
			skipBackgroundClick = true;
			var tmpcommand:AvatarCommand;
			var rect:Rectangle;
			var targetNode:Node;
			var npcvo:NpcVo = (e.item as NpcView).npcvo;
			
			var obj:Object;
			var curSceneType:int = DataManager.getInstance().curSceneType;
			switch (npcvo.clickType) 
			{
				case NpcClassVo.CLICKTYPE_MODULE:
					obj = decodeJson(npcvo.clickValue);
					if (obj.sceneType) 
					{
						if (obj.sceneType.indexOf(curSceneType)<=-1) 
						{
							break;
						}
					}
					DataManager.getInstance().setVar("npcClickValue", obj);
					var tmp:IModule = DisplayManager.uiSprite.addModuleByVo(DataManager.getInstance().getModuleVo(obj.name));
					if (obj.initFunc) {
						
						tmp[obj.initFunc].call(null,obj);
					}
				break;
				
				case NpcClassVo.CLICKTYPE_ACTMODULE:
					obj = decodeJson(npcvo.clickValue);
					if (obj.sceneType) 
					{
						if (obj.sceneType.indexOf(curSceneType)<=-1) 
						{
							break;
						}
					}
					DataManager.getInstance().setVar("npcClickValue", obj);
					ActModuleManager.getInstance().addActModule(DataManager.getInstance().getActByName(obj.actName));
				break;
			}
		}
		
		override public function onSpecialBuildClick(event:GameMouseEvent):void {
			skipBackgroundClick = true;
			
			var sceneUser:UserVo = DataManager.getInstance().curSceneUser;
			
			if ( DataManager.getInstance().gameSetting.barBuildIds.indexOf(int(event.item.data.id))>-1) //酒馆
			{
				if (!sceneUser.hireHelpUsed && sceneUser.hireHelp < DataManager.getInstance().gameSetting.maxHireHelp) //可以加酒
				{
					tmpItem = event.item as SpecialBuild;
					event.item.mouseEnabled = false;
					curItem = event.item;
					
					var mc:McShower = new McShower(timeMovie, curItem.view.container,null,null,addWine);
					var rect:Rectangle = curItem.view.container.getRect(curItem.view.container);
					mc.y = rect.top - 10;
				}
			}else if (int(event.item.data.id) == DataManager.getInstance().gameSetting.homeBuildId) 
			{
				new MoveSceneCommand().moveScene(DataManager.getInstance().gameSetting.homeSceneId,0,0,1,sceneUser.uid);
			}
		}
		
		private function addWine():void {
			var sceneUser:UserVo = DataManager.getInstance().curSceneUser;
			var command:AddWineCommand = new AddWineCommand(int(sceneUser.uid));
			command.addEventListener(Event.COMPLETE, onAddWineComplete);
			
		}
		
		override public function onSpecialBuildOver(event:GameMouseEvent):void 
		{
			var sceneUser:UserVo = DataManager.getInstance().curSceneUser;
			
			if ( DataManager.getInstance().gameSetting.barBuildIds.indexOf(int(event.item.data.id))>-1) //酒馆
			{
				if (!sceneUser.hireHelpUsed && sceneUser.hireHelp < DataManager.getInstance().gameSetting.maxHireHelp) //可以加酒
				{
					event.item.showGlow();
				}
			}else if (int(event.item.data.id) == DataManager.getInstance().gameSetting.homeBuildId) 
			{
				event.item.showGlow();
			}
		}
		
		private function onAddWineComplete(event:Event):void
		{
			event.target.removeEventListener(Event.COMPLETE, onAddWineComplete);
			
			if (event.target.result.isSuccess) 
			{
				//掉落
				if (event.target.data.addItems) 
				{
					AwardItemManager.getInstance().addAwardsByResultVo(event.target.data.result, event.target.data.addItems,tmpItem.gridPos);
				}else {
					AwardItemManager.getInstance().addAwardsByResultVo(event.target.data.result, [],tmpItem.gridPos);
				}
				
			}
			tmpItem.mouseEnabled = true;
			tmpItem = null;
			DataManager.getInstance().curSceneUser.hireHelp++;
			DataManager.getInstance().curSceneUser.hireHelpUsed=1;
			(curItem as SpecialBuild).clearTip();
		}
		
	}

}