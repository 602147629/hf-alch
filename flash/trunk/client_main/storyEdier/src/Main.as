package 
{
	import com.brokenfunction.json.decodeJson;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.system.ApplicationDomain;
	import flash.system.LoaderContext;
	import happyfish.display.ui.Tooltips;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happyfish.manager.SwfURLManager;
	import happyfish.model.SwfLoader;
	import happyfish.scene.world.control.IsoPhysicsControl;
	import happyfish.storyEdit.display.MainUiView;
	import happyfish.storyEdit.model.EditDataManager;
	import happymagic.display.ui.TipsBgSmall;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.UiManager;
	import happymagic.model.command.initCommand;
	import happymagic.model.command.initStaticCommand;
	import happymagic.model.command.MoveSceneCommand;
	import happymagic.scene.world.MagicState;
	import happymagic.scene.world.MagicView;
	import happymagic.scene.world.MagicWorld;
	
	/**
	 * ...
	 * @author jj
	 */
	public class Main extends Sprite 
	{
		private var sceneSprite:Sprite;
		private var editer:MainUiView;
		
		private const DATA_PATH:String = "data/";
		private const SWF_PATH:String = "swf/";
		private var needLoadStaticDatas:Array;
		
		public function Main():void 
		{
			SwfURLManager.getInstance().setValue([]);
			SwfLoader.getInstance().loaderContext = new LoaderContext(false, ApplicationDomain.currentDomain);
			
			Tooltips.getInstance().setContainer(stage);
			Tooltips.getInstance().saveBg(Tooltips.DEFAULT_BG, new TipsBgSmall());
			
			sceneSprite = new Sprite();
			addChild(sceneSprite);
			DisplayManager.sceneSprite = sceneSprite;
			DisplayManager.uiSprite = new UiManager;
			addChild(DisplayManager.uiSprite);
			
			//创建存储世界对象的类
			var magicState:MagicState = new MagicState();
			
			//世界view
			var magicView:MagicView = new MagicView(magicState);
			this.sceneSprite.addChild(magicView); //将世界view加入显示列表
			
			//准备创建世界
			var world:MagicWorld = new MagicWorld(magicState);
			DataManager.getInstance().setVar("magicWorld", world);
			
			//初始化worldState
			magicState.init(magicView, world, null);
			magicState.physicsControl = new IsoPhysicsControl;
			magicState.physicsControl.initPhysics(magicState);
			
			DataManager.getInstance().worldState = magicState;
			
			editer = new MainUiView();
			addChild(editer);
			
			loadInit();
		}
		
		private function loadInit():void 
		{
			needLoadStaticDatas = ["storyList", "actorList"];
			
			loadStaticData(needLoadStaticDatas.shift(),DATA_PATH);
		}
		
		private function loadStaticData(dataName:String,path:String):void 
		{
			trace("loadStaticData",dataName);
			var loader:CsvLoader = new CsvLoader(dataName);
			//staticLoaders.push(loader);
			loader.addEventListener(Event.COMPLETE, loadStaticData_complete);
			loader.loadURL(path+dataName+".csv"+"?=" + new Date().getTime());
		}
		
		private function loadStaticData_complete(e:Event):void 
		{
			var loader:CsvLoader = e.target as CsvLoader;
			loader.removeEventListener(Event.COMPLETE, loadStaticData_complete);
			
			var tmpData:Array = loader.getRecords();
			
			EditDataManager.getInstance().setVar(loader.name, tmpData);
			
			//trace("数据加载完成 " + loader.name);
			if (needLoadStaticDatas.length>0) 
			{
				loadStaticData(needLoadStaticDatas.shift(),DATA_PATH);
			}else {
				loadConfig();
			}
		}
		
		private function loadConfig():void
		{
			var loader:URLLoader = new URLLoader(new URLRequest("data/initData.txt"));
			loader.addEventListener(Event.COMPLETE, onLoadConfigComplete);
		}
		private function onLoadConfigComplete(event:Event):void
		{
			event.target.removeEventListener(Event.COMPLETE, onLoadConfigComplete);
			var dataStr:String = event.target.data;
			var data:Object = decodeJson(dataStr);
			
			InterfaceURLManager.getInstance().urls = data["interfaces"];
			InterfaceURLManager.getInstance().interfaceHost = "http://devalchemyrenren.happyfish001.com/";
			InterfaceURLManager.getInstance().staticHost = "http://devstaticalchemy.happyfish001.com/renren/swf/";
			
			DataManager.getInstance().modules = new Array;
			var modules:Array = data["modules"];
			for (var i:int = 0; i < modules.length; i++){
				var moduleVo:ModuleVo = new ModuleVo;
				moduleVo.setValue(modules[i]);
				DataManager.getInstance().modules.push(moduleVo);
			}
			modules = data["otherModules"];
			for (i = 0; i < modules.length; i++){
				moduleVo = new ModuleVo;
				moduleVo.setValue(modules[i]);
				DataManager.getInstance().modules.push(moduleVo);
			}
			
			loadStatic();
		}
		/**
		 * 静态数据读取
		 */
		private function loadStatic():void
		{
			//请求
			var init_static_command:initStaticCommand = new initStaticCommand();
			init_static_command.addEventListener(Event.COMPLETE, loadInitUser);
			init_static_command.load();
			
		}
		
		/**
		 * 静态数据读取完毕与动态数据加载
		 * @param	e
		 */
		private function loadInitUser(e:Event):void
		{
			//读取玩家初始信息,包括场景\屋中的学生\玩家魔法等
			var init_command:initCommand = new initCommand();
			init_command.addEventListener(Event.COMPLETE, allInitWorkDone);
			init_command.load();
		}
		
		private function allInitWorkDone(e:Event):void
		{
			e.target.removeEventListener(Event.COMPLETE, allInitWorkDone);
			
			DataManager.getInstance().currentUser.currentSceneId = -1;
			
			editer.init();
		}
		
		//private function createWorld(e:Event):void
		//{
			//var sceneId:uint = DataManager.getInstance().currentUser.currentSceneId;
			//var command:MoveSceneCommand = new MoveSceneCommand();
			//command.addEventListener(Event.COMPLETE, onMoveSceneComplete);
			//command.moveScene(2);
		//}
		
		
	}
	
}