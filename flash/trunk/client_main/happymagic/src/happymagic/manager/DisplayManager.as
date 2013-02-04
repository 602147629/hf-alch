package happymagic.manager 
{
	import flash.display.Sprite;
	import flash.geom.Point;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.module.vo.ModuleVo;
	import happymagic.display.view.dungeon.DungeonTip;
	import happymagic.display.view.mainMenu.MainMenuView;
	import happymagic.display.view.promptFrame.CoinNoEnoughView;
	import happymagic.display.view.promptFrame.NeedMoreItemView;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.events.SysMsgEvent;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.scene.world.MagicWorld;
	/**
	 * ...
	 * @author slam
	 */
	public class DisplayManager
	{
		//ui容器
		public static var uiSprite:UiManager;
		//场景容器
		public static var sceneSprite:Sprite;
		//幕布容器
		public static var storyUiSprite:Sprite;
		//手型容器
		public static var mouseIconSprite:Sprite;
		
		public static var dungeonTip:DungeonTip;
		
		public static var menuView:MainMenuView;
		
		public function DisplayManager() 
		{
			
		}
		
		public static function getCurrentMousePosition():Point {
			return new Point(uiSprite.mouseX, uiSprite.mouseY);
		}
		
		public static function getPlayerPosition():Point {
			var world:MagicWorld = DataManager.getInstance().worldState.world as MagicWorld;
			return world.player.view.container.parent.localToGlobal(new Point(world.player.view.container.screenX, world.player.view.container.screenY));
		}
		
		public static function showLoading():void {
			trace("showLoading");
			DisplayManager.uiSprite.showLoading();
		}
		
		public static function hideLoading():void {
			trace("hideLoading");
			DisplayManager.uiSprite.closeLoading();
		}
		
		/**
		 * 显示一个飘屏
		 * @param	piao_type
		 * @param	content
		 * @param	p
		 * @param	now
		 * @param	justShow	只是显示一下,不表现飞向信息面板动画
		 */
		public static function showPiaoStr(piao_type:uint, content:String, p:Point = null, now:Boolean = false, justShow:Boolean = false):void {
			var event_piao_msg:PiaoMsgEvent;
			
			var msgs_new:Array = [[piao_type, content]];
			if (!p) 
			{
				p = new Point(DisplayManager.uiSprite.stage.mouseX, DisplayManager.uiSprite.stage.mouseY);
			}
			event_piao_msg = new PiaoMsgEvent(PiaoMsgEvent.SHOW_PIAO_MSG, msgs_new, p.x, p.y, now);
			event_piao_msg.justShow = justShow;
			EventManager.getInstance().dispatchEvent(event_piao_msg);
		}
		
		/**
		 * 显示一个飘屏
		 * @param	piao_type
		 * @param	content
		 * @param	p
		 * @param	now
		 * @param	justShow	只是显示一下,不表现飞向信息面板动画
		 */
		public static function showPiaoItemStr(piao_type:uint, condvo:ConditionVo, p:Point = null, now:Boolean = false, justShow:Boolean = false):void {
			var event_piao_msg:PiaoMsgEvent;
			
			var msgs_new:Array = [[piao_type, condvo]];
			if (!p) 
			{
				p = new Point(DisplayManager.uiSprite.stage.mouseX, DisplayManager.uiSprite.stage.mouseY);
			}
			event_piao_msg = new PiaoMsgEvent(PiaoMsgEvent.SHOW_PIAO_MSG, msgs_new, p.x, p.y, now);
			event_piao_msg.justShow = justShow;
			EventManager.getInstance().dispatchEvent(event_piao_msg);
		}		
		
		/**
		 * 打开一条系统消息
		 * @param	str
		 * @param	type	消息类型 	1 确认框		0 普通消息框
		 * @param	time	消息在多少时间(毫秒)后自动关闭 -1为不自动关闭
		 */
		public static function showSysMsg(str:String,type:uint=0,time:int=-1,_callBack:Function=null):void
		{
			var event:SysMsgEvent = new SysMsgEvent(SysMsgEvent.SHOW_SYSMSG, str, type, time);
			event.callBack = _callBack;
			EventManager.getInstance().dispatchEvent(event);
		}
		
		
		//经营等级不足提示框
		public static function showBusinessLevelNoEnoughView(level:int ):void
		{
			var moduleVo:ModuleVo = DataManager.getInstance().getModuleVo("BusinessLevelNoEnoughView");
			DisplayManager.uiSprite.addModuleByVo(moduleVo)["setData"](level);
		}		
		
		//经营等级升级提示面板
		public static function showBusinessLevelUpView(level:int ):void
		{
			var moduleVo:ModuleVo = DataManager.getInstance().getModuleVo("BusinessLevelUpView");
			DisplayManager.uiSprite.addModuleByVo(moduleVo)["setData"](level);
		}			
		
		//金币不足的提示框
		public static function showCoinNoEnoughView():void
		{
			var moduleVo:ModuleVo = DataManager.getInstance().getModuleVo("CoinNoEnoughView");
			var view:CoinNoEnoughView =DisplayManager.uiSprite.addModuleByVo(moduleVo) as CoinNoEnoughView;
		}				
		
		//宝石不足的提示框
		public static function showGemNoEnoughView():void
		{
			var moduleVo:ModuleVo = DataManager.getInstance().getModuleVo("GemNoEnoughView");
			DisplayManager.uiSprite.addModuleByVo(moduleVo);
		}		
		
		//需要更多道具的提示框
		public static function showNeedMoreItemView(cid:int):void
		{			
			 //var view:NeedMoreItemView = DisplayManager.uiSprite.addModule("NeedMoreItemView", "happymagic.display.view.promptFrame.NeedMoreItemView", false, AlginType.CENTER, 20, -10) as NeedMoreItemView;
			 //view.setData(cid);
			 //DisplayManager.uiSprite.setBg(view);				
			var moduleVo:ModuleVo = DataManager.getInstance().getModuleVo("NeedMoreItemView");
			DisplayManager.uiSprite.addModuleByVo(moduleVo)["setData"](cid);
		}			
		
		//体力不足的提示框
		public static function showNeedMorePhysicalStrengthView():void
		{
			var moduleVo:ModuleVo = DataManager.getInstance().getModuleVo("NeedMorePhysicalStrengthView");
			DisplayManager.uiSprite.addModuleByVo(moduleVo);
		}
		
		static public function showNewItems(list:Array):void 
		{
			var getItemClass:Function = DataManager.getInstance().itemData.getItemClass;
			for (var i:int = list.length - 1; i >= 0; i--)
			{
				list[i] = new ConditionVo().setData( { id:list[i][0], type:ConditionType.ITEM } );
			}
			var moduleVo:ModuleVo = DataManager.getInstance().getModuleVo("GetNewItemsView");
			DisplayManager.uiSprite.addModuleByVo(moduleVo)["setData"](list);
		}
		
		public static function get camera():Sprite {
			return (DataManager.getInstance().worldState.view.isoView.camera);
		}
		
		static public function get stageMouseEnabled():Boolean 
		{
			return uiSprite.stage.mouseChildren;
		}
		
		static public function set stageMouseEnabled(value:Boolean):void 
		{
			uiSprite.stage.mouseChildren =  value;
		}
		
	}

}