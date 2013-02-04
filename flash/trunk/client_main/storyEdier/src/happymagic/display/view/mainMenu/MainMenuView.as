package happymagic.display.view.mainMenu 
{
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	import com.greensock.TweenMax;
	import flash.display.DisplayObject;
	import flash.display.SimpleButton;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import flash.utils.Dictionary;
	import happyfish.display.view.UISprite;
	import happyfish.events.ModuleEvent;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.module.interfaces.IModule;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.ModuleMvType;
	import happyfish.manager.module.vo.ModuleVo;
	import happyfish.manager.mouse.MouseManager;
	import happyfish.manager.SoundEffectManager;
	import happyfish.scene.world.WorldState;
	import happymagic.display.view.menuControl.MenuIconControl;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.SysMenuView;
	import happymagic.events.DiyEvent;
	import happymagic.manager.PublicDomain;
	import happymagic.model.command.MoveSceneCommand;
	import happymagic.model.command.SwitchSceneCommand;
	import happymagic.model.vo.SceneClassVo;
	import happymagic.scene.world.control.ChangeSceneCommand;
	import happymagic.events.ActionStepEvent;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.mouse.MagicMouseIconType;
	import happymagic.model.control.TakeResultVoControl;
	import happymagic.scene.world.control.MouseDefaultAction;
	import happymagic.scene.world.SceneType;
	import xrope.HLineLayout;
	/**
	 * ...
	 * @author jj
	 */
	public class MainMenuView extends UISprite
	{
		
		private var btw:Number=5;
		public var _iview:mainMenuUi;
		private var layer:HLineLayout;
		private var leftBtns_selfHome:Array;//自己家
		private var leftBtns_selfViliage:Array;//自村
		private var leftBtns_friendViliage:Array;//友村
		private var leftBtns_friend:Array;//好友家
		private var leftBtns_explore:Array;//进入副本后应该显示的状态栏
		private var _hasNewMagic:Boolean;
		private var diyMenu:diyMenuUi;
		private var diyMenuFlag:Boolean;
		private var sysMenu:SysMenuView;
		private var sysMenuFlag:Boolean;
		public var illusIcon:MenuIconControl;
		private var followIconMap:Dictionary;
		
		public function MainMenuView() 
		{
			super();
			DisplayManager.menuView = this;
			
			_view = new mainMenuUi();
			_iview = _view as mainMenuUi;
			_view.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			layer = new HLineLayout(_view, 15, -57, 600, 55, "L", 5, true);
			layer.useBounds = true;
			
			illusIcon = new MenuIconControl(_iview.illusBtn);
			illusIcon.setBg(new newIconUi(), 20, -25,1);
			DataManager.getInstance().isNewIllus = int(DataManager.getInstance().illustratedData.newIllustratedArr.length > 0);
			illusIcon.enabled = Boolean(DataManager.getInstance().isNewIllus);
			
			followIconMap = new Dictionary();
			followIconMap[_iview.illusBtn] = [illusIcon];
			
			leftBtns_selfHome = [_iview.roleInfoBtn,_iview.illusBtn, _iview.itemboxBtn, _iview.shopBtn, _iview.friendBtn,_iview.diyBtn];
			leftBtns_friend = [_iview.roleInfoBtn, _iview.itemboxBtn, _iview.shopBtn, _iview.friendBtn];
			leftBtns_selfViliage = [_iview.roleInfoBtn, _iview.illusBtn, _iview.itemboxBtn, _iview.shopBtn, _iview.friendBtn, _iview.hireBtn];
			leftBtns_friendViliage = [_iview.roleInfoBtn, _iview.illusBtn, _iview.itemboxBtn, _iview.shopBtn, _iview.friendBtn, _iview.hireBtn];
			leftBtns_explore = [_iview.roleInfoBtn, _iview.itemboxBtn,_iview.shopBtn];
			
			ModuleManager.getInstance().addEventListener(ModuleEvent.MODULE_CLOSE, moduleClose);
			
			EventManager.getInstance().addEventListener(SceneEvent.SCENE_DATA_COMPLETE, sceneChange);
			
			EventManager.getInstance().addEventListener("isNewIllus", initFollowIcon);
			
			setType();
		}
		
		private function initFollowIcon(e:Event):void 
		{
			var type:int = DataManager.getInstance().curSceneType;
			
			if (type == SceneType.TYPE_SELF_VILIAGE || type ==  SceneType.TYPE_HOME || type ==  SceneType.TYPE_FRIEND_VILIAGE ) 
			{
				illusIcon.enabled = Boolean(DataManager.getInstance().isNewIllus);
			}
		}
		
		public function addIconMc():void {
			
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
					sortBtn(leftBtns_friend);
				break;
				
				case SceneType.TYPE_SELF_VILIAGE:
					sortBtn(leftBtns_selfViliage);
				break;
				
				case SceneType.TYPE_FRIEND_VILIAGE:
					sortBtn(leftBtns_friendViliage);
				break;
				
				case SceneType.TYPE_EXPLORE:
					sortBtn(leftBtns_explore);
				break;
			}
		}
		
		/**
		 * 增加自己家中时的主菜单按钮
		 * @param	btn
		 */
		public function addBtnLeftSelf(btn:DisplayObject):void {
			btn.x = 0;
			btn.y = 0;
			leftBtns_selfHome.push(btn);
			setType();
		}
		
		/**
		 * 移除自己家中时的主菜单按钮
		 * @param	btnName	按钮名字
		 */
		public function removeBtnLeftSelf(btnName:String):void {
			for (var i:int = 0; i < leftBtns_selfHome.length; i++) 
			{
				var item:DisplayObject = leftBtns_selfHome[i];
				if (item.name==btnName) 
				{
					leftBtns_selfHome.splice(i, 1);
					setType();
					return;
				}
			}
		}
		
		private function sortBtn(arr:Array):void {
			var i:int;
			var tmp:*;
			for (i = 0; i < _iview.numChildren; i++) 
			{
				tmp = _view.getChildAt(i);
				if (tmp != _iview.bg && tmp!=_iview.configBtn ) 
				{
					_view.getChildAt(i).visible = false;
				}
				if (tmp is Sprite) 
				{
					tmp.mouseChildren = false;
				}
			}
			
			for (var name:Object in followIconMap) 
			{
				for (var j:int = 0; j < followIconMap[name].length; j++) 
				{
					followIconMap[name][j].visible = false;
				}
				
			}
			
			layer.removeAll();
			var lastBtn:DisplayObject;
			for (i = 0; i < arr.length; i++) 
			{
				if (arr[i]) 
				{
					arr[i].visible = true;
					layer.add(arr[i]);
					layer.layout();
					TweenMax.to(arr[i], .5, { x:arr[i].x,onComplete:tweenIcon_complete,onCompleteParams:[arr[i]] } );
				}
			}
			
			TweenMax.to(_iview.bg, .5, { width:arr.length * 56 + 10 } );
			TweenMax.to(_iview.configBtn, .5, { x:arr.length * 57 + 25 } );
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
		
		/**
		 * 返回指定按钮的全局坐标
		 * @return
		 */
		public function getBtnPoint(btnName:String):Point {
			if (_iview[btnName]) 
			{
				var p:Point = new Point(_iview[btnName].x, _iview[btnName].y);
				p = _iview.localToGlobal(p);
				return p;
			}
			return null;
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case _iview.diyBtn:
					if (diyMenuFlag) 
					{
						hideDiyMenu();
					}else {
						showDiyMenu();
					}
					e.stopImmediatePropagation();
				break;
				
				case _iview.configBtn:
					if (diyMenuFlag) 
					{
						hideDiyMenu();
					}else {
						showSysMenu();
					}
					e.stopImmediatePropagation();
				break;
				
				case _iview.roleInfoBtn:
					ActModuleManager.getInstance().addActModule(DataManager.getInstance().getActByName("roleInfo"));
					
				break;
				
				case _iview.illusBtn:
					var signAwardact:ActVo = DataManager.getInstance().getActByName("IllustratedHandbook");
					var signAwardVoloadingitem2:LoadingItem = ActModuleManager.getInstance().addActModule(signAwardact);
				break;
				
				case _iview.itemboxBtn:
					var signAwardact1:ActVo = DataManager.getInstance().getActByName("storage");
					var signAwardVoloadingitem:LoadingItem = ActModuleManager.getInstance().addActModule(signAwardact1);
				break;
				
				case _iview.shopBtn:
					ActModuleManager.getInstance().addActModule(DataManager.getInstance().getActByName("shop"));
				break;
				
				case _iview.hireBtn:
					ActModuleManager.getInstance().addActModule(DataManager.getInstance().getActByName("hire"));
				break;
				
				case _iview.friendBtn:
					DisplayManager.uiSprite.addModuleByVo(DataManager.getInstance().getModuleVo("friend"));
				break;
				
				/*case _iview.FBicon:
				
					var command:MoveSceneCommand = new MoveSceneCommand;
					command.addEventListener(Event.COMPLETE, onSceneChanged);
					command.moveScene(((DataManager.getInstance().getSceneClassById(DataManager.getInstance().currentUser.currentSceneId)) as SceneClassVo).parentSceneId);
				   
				 break;*/
			}
		}
		
		private function showDiyMenu():void 
		{
			//_iview.diyBtn.gotoAndStop(2);
			
			if (!diyMenu) 
			{
				diyMenu = new diyMenuUi();
				diyMenu.addEventListener(MouseEvent.CLICK, diyClickFun);
			}
			diyMenu.mouseEnabled = 
			diyMenu.mouseChildren = false;
			_iview.addChild(diyMenu);
			diyMenu.x = _iview.diyBtn.x;
			diyMenu.y = _iview.diyBtn.y - 35;
			diyMenu.alpha = 0;
			TweenMax.to(diyMenu, .3, { alpha:1, y:"+10", onComplete:showDiyMenu_complete } );
			
			_iview.stage.addEventListener(MouseEvent.CLICK, stageClickFun);
			
		}
		
		private function showSysMenu():void {
			if (!sysMenu) 
			{
				sysMenu = new SysMenuView();
				_iview.addChild(sysMenu);
				sysMenu.init();
			}
			sysMenu.mouseEnabled = 
			sysMenu.mouseChildren = false;
			_iview.addChild(sysMenu);
			sysMenu.x = _iview.configBtn.x;
			sysMenu.y = _iview.configBtn.y - 35;
			sysMenu.alpha = 0;
			TweenMax.to(sysMenu, .3, { alpha:1, y:"+10", onComplete:showSysMenu_complete } );
			
			_iview.stage.addEventListener(MouseEvent.CLICK, stageClickFun);
		}
		
		private function showSysMenu_complete():void 
		{
			sysMenuFlag = true;
			sysMenu.mouseChildren = true;
			sysMenu.addEventListener(MouseEvent.MOUSE_OUT, hideDiyMenu);
		}
		
		private function hideSysMenu(e:MouseEvent=null):void 
		{
			if (!sysMenuFlag) 
			{
				return;
			}
			if (e) 
			{
				if (e.target.parent==diyMenu) 
				{
					return;
				}
				
			}
			sysMenu.removeEventListener(MouseEvent.MOUSE_OUT, hideSysMenu);
			//_iview.diyBtn.gotoAndStop(1);
			sysMenuFlag = false;
			_iview.stage.removeEventListener(MouseEvent.CLICK, stageClickFun);
			
			sysMenu.mouseEnabled = 
			sysMenu.mouseChildren = false;
			TweenMax.to(sysMenu, .3, { alpha:0, y:"-10",onComplete:hideSysMenu_complete } );
		}
		
		private function hideSysMenu_complete():void 
		{
			sysMenu.parent.removeChild(sysMenu);
		}
		
		private function stageClickFun(e:MouseEvent):void 
		{
			if (diyMenuFlag) 
			{
				if (e.target.parent == diyMenu) 
				{
					return;
				}
				hideDiyMenu();
			}
			
			if (sysMenuFlag) 
			{
				if (e.target.parent == sysMenu) 
				{
					return;
				}
				hideSysMenu();
			}
			
		}
		
		private function showDiyMenu_complete():void 
		{
			diyMenuFlag = true;
			//diyMenu.mouseEnabled = 
			diyMenu.mouseChildren = true;
			diyMenu.addEventListener(MouseEvent.MOUSE_OUT, hideDiyMenu);
		}
		
		private function diyClickFun(e:MouseEvent):void 
		{
			var worldState:WorldState = DataManager.getInstance().worldState;
			var event:DiyEvent;
			switch (e.target) 
			{
				case diyMenu.diyStopBtn:
					new MouseDefaultAction(worldState);
					hideDiyMenu();
				break;
				
				case diyMenu.diyMoveBtn:
					EventManager.getInstance().dispatchEvent(new DiyEvent(DiyEvent.MOVE_ITEM));
					hideDiyMenu();
				break;
				
				case diyMenu.diyMirrorBtn:
					EventManager.getInstance().dispatchEvent(new DiyEvent(DiyEvent.MIRROR_ITEM));
					hideDiyMenu();
				break;
				
				case diyMenu.diyRemoveBtn:
					EventManager.getInstance().dispatchEvent(new DiyEvent(DiyEvent.REMOVE_ITEM));
					hideDiyMenu();
				break;
			}
		}
		
		private function hideDiyMenu(e:MouseEvent=null):void 
		{
			if (!diyMenuFlag) 
			{
				return;
			}
			if (e) 
			{
				if (e.target.parent==diyMenu) 
				{
					return;
				}
				
			}
			diyMenu.removeEventListener(MouseEvent.MOUSE_OUT, hideDiyMenu);
			//_iview.diyBtn.gotoAndStop(1);
			diyMenuFlag = false;
			_iview.stage.removeEventListener(MouseEvent.CLICK, stageClickFun);
			
			diyMenu.mouseEnabled = 
			diyMenu.mouseChildren = false;
			TweenMax.to(diyMenu, .3, { alpha:0, y:"-10",onComplete:hideDiyMenu_complete } );
		}
		
		private function hideDiyMenu_complete():void 
		{
			diyMenu.parent.removeChild(diyMenu);
		}
		
		private function onSceneChanged(event:Event):void
		{
			event.target.removeEventListener(Event.COMPLETE, onSceneChanged);
			new ChangeSceneCommand();
		}
		
		private function moduleClose(e:ModuleEvent):void 
		{
			switch (e.moduleName) 
			{
				case "magicbox":
				//ModuleManager.getInstance().showModule(name);
				break;
			}
		}
	}

}