package happymagic.manager 
{
	import adobe.utils.CustomActions;
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.filters.BlurFilter;
	import flash.filters.ColorMatrixFilter;
	import flash.geom.Matrix;
	import flash.geom.Point;
	import happyfish.cacher.SwfClassCache;
	import happyfish.display.ui.Tooltips;
	import happyfish.display.view.LoadingStateView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.module.interfaces.IModule;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.ModuleMvType;
	import happyfish.manager.module.vo.ModuleVo;
	import happyfish.manager.SoundEffectManager;
	import happyfish.utils.display.FiltersDomain;
	import happyfish.utils.display.ScaleControl;
	import happymagic.display.control.PiaoMsgControl;
	import happymagic.display.ui.TipsBgSmall;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.RightCenterMenuView;
	import happymagic.display.view.SysMsgView;
	import happymagic.events.MagicBookEvent;
	import happymagic.events.MagicEvent;
	import happymagic.events.SceneEvent;
	import happymagic.events.SysMsgEvent;
	/**
	 * ...
	 * @author jj
	 */
	public class UiManager extends Sprite
	{
		private var loadingMc:LoadingStateView;
		public var loadingLayer:Sprite;
		public var upModuleLayer:Sprite;
		public var moduleLayer:Sprite;
		private var moduleManager:ModuleManager;
		private var sysMsgList:Array;
		private var sysMsgView:SysMsgView;
		
		
		public static const MODULE_MAINMENU:String = "menu";
		public static const MODULE_MAGICBOOK:String = "magicBook";
		public static const MODULE_FRIENDS:String = "friends";
		public static const MODULE_ITEMBOX:String = "itemBoxView";
		public static const MODULE_GUIDES:String = "guides";
		private var sceneChangeMc:MovieClip;
		private var sceneOutCallback:Function;
		private var sceneEndCallback:Function;
		private var sceneOutCallPlaying:Boolean;
		private var sceneEndCallPlaying:Boolean;
		private var needSceneOut:Boolean;
		private var storyTmpCloseModules:Array;
		public function UiManager() 
		{
			
			//PublicDomain.getInstance().setVar("uiManager", this);
			loadingLayer = new Sprite();
			upModuleLayer = new Sprite();
			upModuleLayer.mouseEnabled = false;
			moduleLayer = new Sprite();
			addChild(moduleLayer);
			addChild(upModuleLayer);
			addChild(loadingLayer);
			
			addEventListener(Event.ADDED_TO_STAGE, addToStage);
		}
		
		public function init():void {
			
			
			//弹出消息队列
			sysMsgList = new Array();
			
			//模块事件
			//EventManager.getInstance().addEventListener(MagicBookEvent.SHOW_MAGICBOOK, showMagicBook);
			//EventManager.getInstance().addEventListener(ItemBoxEvent.SHOW_ITEMBOX, showOrHideItemBox);
			//EventManager.getInstance().addEventListener(ItemBoxEvent.HIDE_ITEMBOX, showOrHideItemBox);
			
			//系统消息框
			EventManager.getInstance().addEventListener(SysMsgEvent.SHOW_SYSMSG, showSysMsgEvent);
			
			
			//初始化tips
			
			Tooltips.getInstance().setContainer(upModuleLayer);
			//创建常用TIPS BG
			Tooltips.getInstance().saveBg(Tooltips.DEFAULT_BG, new TipsBgSmall());
			Tooltips.getInstance().saveBg("itemBoxTipsUi", new itemBoxTipsUi());
			
			//初始化飘屏control
			PiaoMsgControl.getInstance().setContainer(stage, EventManager.getInstance());
			
			//外挂模块初始化
			ActModuleManager.getInstance().init(moduleLayer);
			
			//广播UI准备完成
			EventManager.getInstance().dispatchEvent(new MagicEvent(MagicEvent.UI_INIT));
			
			EventManager.getInstance().addEventListener(MagicEvent.RIGHT_CENTER_INIT, rightCenterInited);
		}
		
		private function rightCenterInited(e:MagicEvent):void 
		{
			var rMenu:RightCenterMenuView = ModuleManager.getInstance().getModule("rightCenterMenu") as RightCenterMenuView;
			var diaryBtnMc:diaryBtn = new diaryBtn();
			rMenu.addMc("diaryBtn",diaryBtnMc,10,showDiary);
		}
		
		private function showDiary():void {
			var act:ActVo = DataManager.getInstance().getActByName("Daily");
			ActModuleManager.getInstance().addActModule(act);
		}
		
		private function addToStage(e:Event):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, addToStage);
			
			//初始化loading
			loadingMc = new LoadingStateView(loadingLayer, stage.stageWidth, stage.stageHeight);
			loadingMc.body = new loadingMv();
			
			//初始化模块
			moduleManager = ModuleManager.getInstance();
			moduleManager.initManager([loadingLayer, upModuleLayer, moduleLayer], SwfClassCache.getInstance(), SoundEffectManager.getInstance());
			
			
		}
		
		/**
		 * state 1代表LOADING1  2代表LOADING2 
		 */
		 
		public function showSceneOutMv(callback:Function = null, state:int = 1 ):void {
			if (sceneOutCallPlaying) 
			{
				return;
			}
			switch(state )
			{
				case 1:
					if (!sceneChangeMc) 
					{
					     sceneChangeMc = new sceneChangeMv();
					}else {
						callback.apply();
						return;
					}
				  break;
				  
				case 2:
				
				  break;
			}

			sceneChangeMc.gotoAndStop(1);
			
			ScaleControl.size(sceneChangeMc, stage.stageWidth+100, stage.stageHeight+100);
			
			sceneChangeMc.x = stage.stageWidth / 2;
			sceneChangeMc.y = stage.stageHeight / 2-30;
				
			sceneOutCallback = callback;
			sceneOutCallPlaying = true;
			loadingLayer.addChild(sceneChangeMc);
			sceneChangeMc.gotoAndPlay("startOut");
			sceneChangeMc.addEventListener(Event.ENTER_FRAME,checkSceneOutMv);
		}
		
		private function checkSceneOutMv(e:Event):void
		{
			if (sceneChangeMc.currentFrameLabel=="endOut") 
			{
				sceneChangeMc.stop();
				sceneChangeMc.removeEventListener(Event.ENTER_FRAME, checkSceneOutMv);
				if (sceneOutCallback!=null) 
				{
					sceneOutCallback.call();
				}
				sceneOutCallPlaying = false;
				if (needSceneOut) 
				{
					showSceneEndMv(sceneEndCallback);
				}
			}
		}
		
		public function showSceneEndMv(callback:Function = null):void {
			
			sceneEndCallback = callback;
			if (sceneOutCallPlaying) 
			{
				needSceneOut = true;
				return;
			}else {
				needSceneOut = false;
				sceneEndCallPlaying = true;
				if (sceneChangeMc) 
				{
					sceneChangeMc.gotoAndPlay("startEnd");
					sceneChangeMc.addEventListener(Event.ENTER_FRAME, checkSceneEndMv);
				}
				
			}
			
		}
		
		private function checkSceneEndMv(e:Event):void 
		{
			if (sceneChangeMc.currentFrameLabel=="endEnd") 
			{
				sceneChangeMc.removeEventListener(Event.ENTER_FRAME, checkSceneEndMv);
				if (sceneEndCallback!=null) 
				{
					sceneEndCallback.call();
				}
				if (sceneChangeMc.parent) loadingLayer.removeChild(sceneChangeMc);
				sceneChangeMc = null;
				sceneEndCallPlaying = false;
				
				
			}
			
		}
		
		public function showLoading():void
		{
			if(loadingMc) loadingMc.showMe();
		}
		
		public function closeLoading():void {
			if(loadingMc)loadingMc.closeMe();
		}
		
		public function initModules():void {
			moduleManager.setData(PublicDomain.getInstance().getVar("moduleData"));
			
			//记录其它modulevo
			var arr:Array = PublicDomain.getInstance().getVar("moduleData");
			arr = arr.concat(PublicDomain.getInstance().getVar("otherModules"));
			var tmp:ModuleVo;
			var outarr:Array = new Array();
			for (var i:int = 0; i < arr.length; i++) 
			{
				tmp = new ModuleVo().setValue(arr[i]);
				outarr.push(tmp);
			}
			DataManager.getInstance().modules = outarr;
		}
		
		private function showSysMsgEvent(e:SysMsgEvent):void 
		{
			showSysMsg(e.content,e.msgType,e.showTime,e.callBack);
		}
		
		/**
		 * 显示一条消息
		 * @param	str		消息内容
		 * @param	type	类型 1 确认框  2消息框
		 * @param	time	自动关闭时间 -1为不自动关闭
		 * @param	callBack	回调
		 */
		public function showSysMsg(str:String, type:uint,time:int=-1,callBack:Function=null):void {
			
			if (sysMsgList.length!=0) 
			{
				sysMsgList.push({content:str,type:type,time:time,callBack:callBack});
				return;
			}
			sysMsgList.push({content:str,type:type,time:time,callBack:callBack});
			createSysMsg(sysMsgList[0]);
			
		}
		
		private function createSysMsg(obj:Object):void {
			sysMsgView = addModule(ModuleDict.MODULE_SYSMSG, ModuleDict.MODULE_SYSMSG_CLASS,false, AlginType.CENTER) as SysMsgView;
			DisplayManager.uiSprite.setBg(sysMsgView);
			sysMsgView.setData(obj.content,obj.type,obj.time,obj.callBack);
		}
		
		public function closeSysMsg():void {
			closeModule(ModuleDict.MODULE_SYSMSG);
			sysMsgList.splice(0, 1);
			sysMsgView = null;
			if (sysMsgList.length>0) 
			{
				createSysMsg(sysMsgList[0]);
			}
		}
		
		public function addModule(mname:String,className:String,single:Boolean=false,algin:String=AlginType.CENTER,mx:Number=0,my:Number=0,fx:Number=0,fy:Number=0,mvType:String=ModuleMvType.CNETER,mvTime:Number=.5):IModule {
			var module:ModuleVo = new ModuleVo();
			module.setValue( { "name":mname, "className":className, "single":single, "algin":algin, "x":mx, "y":my, "fx":fx, "fy":fy, "mvType":mvType, "mvTime":mvTime } );
			var tmp:IModule=moduleManager.addModule(module);
			moduleManager.showModule(mname);
			
			return tmp;
		}
		
		public function addModuleByVo(vo:ModuleVo):IModule {
			var tmp:IModule=moduleManager.addModule(vo);
			moduleManager.showModule(vo.name);
			
			return tmp;
		}
		
		public function showModule(mname:String):IModule {
			return moduleManager.showModule(mname);
		}
		
		public function getModule(mname:String):IModule {
			return moduleManager.getModule(mname);
		}
		
		public function closeModule(mname:String,del:Boolean=false):void {
			moduleManager.closeModule(mname, del);
		}
		
		public function setBg(target:IModule):void {
			if (target) 
			{
				moduleManager.setModuleBg(target.name,createMaskBg());
			}
		}
		
		public function hideBg(target:IModule):void {
			moduleManager.closeModuleBg(target.name);
		}
		
		public function createMaskBg():Sprite {
			var bd:BitmapData = new BitmapData(stage.stageWidth, stage.stageHeight, false, 0x000000);
			
			//bd.draw(stage);
			var bt:Bitmap = new Bitmap(bd);
			bt.alpha = .5;
			var mat:Array = [  1, 0, 0, 0, -50, 
							   0, 1, 0, 0, -50, 
							   0, 0, 1, 0, -50, 
							   0, 0, 0, 1, 0 ];
			//bt.filters = [new BlurFilter(8, 8, 2), new ColorMatrixFilter(mat)];
			//bt.filters = [new ColorMatrixFilter(mat)];
			var bg:Sprite = new Sprite();
			bg.addChild(bt);
			return bg;
		}
		
		
	}

}