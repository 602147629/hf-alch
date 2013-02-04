package happyfish.editer.view
{
	import br.com.stimuli.loading.BulkLoader;
	import com.as3xls.xls.ExcelFile;
	import flash.display.Sprite;
	import flash.display.StageAlign;
	import flash.display.StageScaleMode;
	import flash.events.Event;
	import flash.net.FileReference;
	import flash.system.ApplicationDomain;
	import flash.system.LoaderContext;
	import happyfish.editer.model.command.LoadInitDataCommand;
	import happyfish.editer.model.EditDataManager;
	import happyfish.editer.scene.view.EditSceneView;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.manager.SwfURLManager;
	import happyfish.model.SwfLoader;
	
	/**
	 * ...
	 * @author jj
	 */
	public class Main extends Sprite 
	{
		public var gridView:GridWorld;
		public var data:Object;
		public var editer:EditerView;
		public var mapSprite:EditSceneView;
		public var mapview:MapView;
		private var fileLoader:FileReference;
		private var xls:ExcelFile;
		private var staticLoaders:Array;
		private var needLoadStaticDatas:Array;
		private var needLoadDatas:Array;
		
		private const CLASSDATA_PATH:String = "classdata/";
		private const DATA_PATH:String = "data/";
		private const SWF_PATH:String = "swf/";
		
		public function Main():void 
		{
			stage.align = StageAlign.TOP_LEFT;
			stage.scaleMode = StageScaleMode.NO_SCALE;
			
			SwfURLManager.getInstance().setValue([]);
			InterfaceURLManager.getInstance().staticHost = "swf/";
			
			mapSprite = new EditSceneView();
			addChild(mapSprite);
			
			editer = new EditerView(this);
			addChild(editer);
			
			SwfLoader.getInstance().loaderContext = new LoaderContext(false, ApplicationDomain.currentDomain);
			
			
			loadData();
		}
		
		private function loadData():void
		{
			var loader:LoadInitDataCommand = new LoadInitDataCommand();
			loader.addEventListener(Event.COMPLETE, loadData_complete);
			loader.load("data.txt" + "?=" + new Date().getTime() );
		}
		
		private function loadData_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, loadData_complete);
			
			data = e.target.objdata;
			
			if (data.bgswfs.length>0) {
				loadBgSwf(data.bgswfs);
			}else {
				startLoadClassData();
			}
			
		}
		
		public function loadBgSwf(swfs:Array):void
		{
			var tmpstr:String="?=" + new Date().getTime();
			for (var i:int = 0; i < swfs.length; i++) 
			{
				swfs[i] = swfs[i] + tmpstr;
			}
			var loader:BulkLoader = SwfLoader.getInstance().addGroup("loadSwf", swfs);
			loader.addEventListener(Event.COMPLETE, loadBgSwf_complete);
		}
		
		private function loadBgSwf_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, loadBgSwf_complete);
			startLoadClassData();
		}
		
		private function startLoadClassData():void {
			editer.addStr("文件加载完成");
			
			//loadMapClass();
			needLoadStaticDatas = ["mapClass", "decorClass", "avatarClass", "mineClass", "monsterClass",
			"portalClass", "floorClass", "npcClass"];
			
			needLoadDatas = ["mineList","monsterList","portalList","decorList","floorList","floorList2","npcList"
			];
			
			loadStaticData(needLoadStaticDatas.shift(),CLASSDATA_PATH);
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
			
			editer.addStr("数据加载完成 " + loader.name);
			if (needLoadStaticDatas.length>0) 
			{
				loadStaticData(needLoadStaticDatas.shift(),CLASSDATA_PATH);
			}else if (needLoadDatas.length>0) {
				loadStaticData(needLoadDatas.shift(),DATA_PATH);
			}else {
				editer.init();
			}
			
		}
		
		//**************************************************************************
		
		
	}
	
}