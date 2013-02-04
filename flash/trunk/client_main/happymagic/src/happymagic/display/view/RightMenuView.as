package happymagic.display.view 
{
	import com.greensock.TweenMax;
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.utils.Dictionary;
	import happyfish.display.view.UISprite;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happymagic.display.view.menuControl.MenuIconControl;
	import happymagic.events.FunctionFilterEvent;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.MoveSceneCommand;
	import happymagic.model.vo.SceneClassVo;
	import happymagic.scene.world.SceneType;
	import xrope.HLineLayout;
	
	/**
	 * ...
	 * @author 
	 */
	public class RightMenuView extends UISprite 
	{
		public var _iview:rightMenuUi;
		private var layer:HLineLayout;
		
		private var leftBtns_selfHome:Array;//自己家
		private var leftBtns_selfViliage:Array;//自村
		private var leftBtns_friendViliage:Array;//好友村
		private var leftBtns_friendHome:Array;//好友家
		private var leftBtns_explore:Array;//进入副本后应该显示的状态栏
		private var leftBtns_explore_out:Array;//副本第一层
		public var worldMapIcon:MenuIconControl;
		public var followIconMap:Dictionary;
		public function RightMenuView() 
		{
			super();
			_view = new rightMenuUi() as MovieClip;
			_iview = _view as rightMenuUi;
			
			layer = new HLineLayout(_view, -135, -70, 150, 75, "L", 5, true);
			layer.useBounds = true;
			
			worldMapIcon = new MenuIconControl(_iview.worldMapBtn);
			worldMapIcon.setBg(new newIconUi(), 20, -25,1);
			worldMapIcon.enabled = false;
			
			followIconMap = new Dictionary();
			followIconMap[_iview.worldMapBtn] = [worldMapIcon];
			
			leftBtns_selfHome = [_iview.goVillageBtn,_iview.worldMapBtn];
			leftBtns_friendHome = [_iview.goHomeBtn, _iview.worldMapBtn];
			leftBtns_selfViliage = [_iview.goHomeBtn, _iview.worldMapBtn];
			leftBtns_friendViliage = [_iview.goHomeBtn, _iview.worldMapBtn];
			leftBtns_explore_out = [_iview.goHomeBtn, _iview.worldMapBtn];
			leftBtns_explore = [_iview.goHomeBtn, _iview.goDoorBtn];
			
			_view.addEventListener(MouseEvent.CLICK, clickFun);
			
			EventManager.getInstance().addEventListener(SceneEvent.SCENE_COMPLETE, sceneChange);
			
			EventManager.getInstance().addEventListener("isNewWorldMap", initFollowIcon);
			EventManager.addEventListener(FunctionFilterEvent.UNLOCK, unlockFuncHandler);
			
			setType();
		}
		
		private function unlockFuncHandler(e:FunctionFilterEvent):void 
		{
			setType();
		}
		
		private function initFollowIcon(e:Event=null):void 
		{
			var type:int = DataManager.getInstance().curSceneType;
			
			if (type != SceneType.TYPE_EXPLORE ) 
			{
				worldMapIcon.enabled = Boolean(DataManager.getInstance().isNewWorldMap);
			}
		}
		
		private function sceneChange(e:SceneEvent):void 
		{
			setType();
		}
		
		public function setType():void {
			
			var type:int = DataManager.getInstance().curSceneType;
			
			switch (type) 
			{
				case SceneType.TYPE_HOME:
					sortBtn(leftBtns_selfHome);
				break;
				
				case SceneType.TYPE_FRIEND_HOME:
					sortBtn(leftBtns_friendHome);
				break;
				
				case SceneType.TYPE_SELF_VILIAGE:
					sortBtn(leftBtns_selfViliage);
				break;
				
				case SceneType.TYPE_FRIEND_VILIAGE:
					sortBtn(leftBtns_friendViliage);
				break;
				
			case SceneType.TYPE_EXPLORE:
				var check:int = checkExploreIsDoor();
					
					if (check==-1)
					{
						sortBtn(leftBtns_explore_out);
					}else {
						sortBtn(leftBtns_explore);
					}
					
				break;
			}
			
			initFollowIcon();
		}
		
		/**
		 * 返回当前场景的入口场景号，如果就是入口，返回-1
		 * @return
		 */
		private function checkExploreIsDoor():int {
			var str:String = DataManager.getInstance().currentUser.currentSceneId.toString();
			var endstr:String = str.slice(str.length - 2, str.length);
			if (endstr=="01")
			{
				return -1;
			}else {
				str = str.slice(0, str.length-2);
				return int(str.concat("01"));
			}
		}
		
		private function sortBtn(arr:Array):void {
			var i:int;
			for (i = 0; i < _iview.numChildren; i++) 
			{
				if (_view.getChildAt(i) != _iview.bg) 
				{
					_view.getChildAt(i).visible = false;
				}
				
			}
			
			for (var name:Object in followIconMap) 
			{
				for (var j:int = 0; j < followIconMap[name].length; j++) 
				{
					followIconMap[name][j].visible = false;
				}
				
			}
			var isLock:Function = DataManager.getInstance().functionFilterData.isLock;
			var len:int = 0;
			layer.removeAll();
			var lastBtn:DisplayObject;
			for (i = 0; i < arr.length; i++) 
			{
				var btn:DisplayObject = arr[i];
				if (btn && !isLock(btn.name.slice(0, -3))) 
				{
					len++;
					arr[i].visible = true;
					layer.add(arr[i]);
					layer.layout();
					TweenMax.to(arr[i], .5, { x:arr[i].x,onComplete:tweenIcon_complete,onCompleteParams:[arr[i]] } );
				}
			}
			
			//TweenMax.to(_iview.bg, .5, { width:arr.length * 60 + 30 } );
			TweenMax.to(_iview.bg, .5, { width:len * 60 + 30 } );
		}
		
		private function tweenIcon_complete(icon:DisplayObject):void 
		{
			var followIcon:Array = followIconMap[icon] as Array;
			if (followIcon) 
			{
				for (var i:int = 0; i < followIcon.length; i++) 
				{
					if (followIcon[i].enabled) 
					{
						followIcon[i].visible = true;
						followIcon[i].algin();
						TweenMax.from(followIcon[i], .2, { x:"-10", alpha:0 } );
					}
				}
				
			}
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case _iview.goHomeBtn:
					new MoveSceneCommand().moveScene(DataManager.getInstance().gameSetting.homeSceneId);
				break;
				
				case _iview.goVillageBtn:
					new MoveSceneCommand().moveScene(DataManager.getInstance().gameSetting.viliageSceneId);
				break;
				
			case _iview.goDoorBtn:
				var check:int = checkExploreIsDoor();
					
					if (check!=-1) 
					{
						new MoveSceneCommand().moveScene(check);
					}else {
						DisplayManager.showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, "已经是最外面了");
					}
				break;
				
				case _iview.worldMapBtn:
					ActModuleManager.getInstance().addActModule(DataManager.getInstance().getActByName("WorldMap"));
				break;
			}
		}
		
	}

}