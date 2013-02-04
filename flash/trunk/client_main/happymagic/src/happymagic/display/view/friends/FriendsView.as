package happymagic.display.view.friends 
{
	import flash.display.StageDisplayState;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.view.UISprite;
	import happyfish.events.ModuleEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happymagic.events.FriendsEvent;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.PublicDomain;
	import happymagic.model.command.LoadFriendsCommand;
	import happymagic.model.MagicJSManager;
	/**
	 * ...
	 * @author jj
	 */
	public class FriendsView extends UISprite
	{
		private var data:Array;
		private var friendList:DefaultListView;
		private var iview:friendUi;
		public function FriendsView() 
		{
			
			super();
			_view = new friendUi();
			iview = _view as friendUi;
			_view.addEventListener(Event.ADDED_TO_STAGE, bodyAddToStage);
			
			friendList = new DefaultListView(new friendListUi(), _view, 6, false, false);
			friendList.tweenTime = 0;
			friendList.setGridItem(FriendsItemView, friendItemUi,FriendBlankItemView,friendBlankItemUi);
			friendList.init(620, 120, 97, 115, 46, -58);
			friendList.x = 17;
			friendList.y = -69;
			
			
			_view.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			EventManager.getInstance().addEventListener(FriendsEvent.FRIENDS_DATA_COMPLETE, data_complete);
			EventManager.getInstance().addEventListener(FriendsEvent.SHOW_FRIENDS_VIEW, showEvent);
			EventManager.getInstance().addEventListener(FriendsEvent.HIDE_FRIENDS_VIEW, hideEvent);
		}
		
		
		private function clickFun(e:MouseEvent):void 
		{
			
			switch (e.target) 
			{
				case iview.closeBtn:
					closeMe(true);
				break;
			}
		}
		
		private function loadData():void {
			var loader:LoadFriendsCommand = new LoadFriendsCommand();
			loader.loadFriend();
		}
		
		
		private function data_complete(e:FriendsEvent):void 
		{
			setData(DataManager.getInstance().friends);
		}
		
		private function setData(value:Array):void
		{
			
			data = value;
			
			friendList.setData(data);
		}
		
		private function bodyAddToStage(e:Event):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, bodyAddToStage);
			
			loadData();
		}
		
		private function hideEvent(e:FriendsEvent):void 
		{
			ModuleManager.getInstance().closeModule(name);
		}
		
		private function showEvent(e:FriendsEvent):void 
		{
			ModuleManager.getInstance().showModule(name);
		}
	}

}