package 
{
	import event.WorldMapEvent;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.utils.getDefinitionByName;
	import happyfish.cacher.SwfClassCache;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.classVo.WorldMapClassVo;
	import happymagic.model.vo.WorldMapVo;
	import model.view.WorldMapView;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class WorldMapMain extends ActModuleBase 
	{
		
		public function WorldMapMain():void 
		{
           EventManager.getInstance().addEventListener(WorldMapEvent.WORLDMAPCLOSE,closecomplete);			
		}
		
		private function closecomplete(e:WorldMapEvent):void 
		{
			super.close();
		}
		
		override public function init(actVo:ActVo, _type:uint = 1):void 
		{
			super.init(actVo, 1);
			
			//世界地图动态数据				
			if (actVo.moduleData)
			{			
			   DataManager.getInstance().worldData.worldmapInitData = new WorldMapVo().setVaule(actVo.moduleData)
			}
				 
			var arr:Array = DataManager.getInstance().worldData.worldmapStaticData;
			
			//for (var i:int = 0; i < arr.length; i++) 
			//{
				//if (!isworldMapLock((arr[i] as WorldMapClassVo).cid))
				//{
					//var obj:Object = new Object();
					//obj.cid = (arr[i] as WorldMapClassVo).sceneId;
					//obj.newUnlock = 0;
					//obj.newOpen = 0;
					//
					//DataManager.getInstance().worldmapInitData.curOpenScene.push(obj);
				//}
			//}
			
			 var worldMapView:WorldMapView = DisplayManager.uiSprite.addModule("worldMapView", "model.view.WorldMapView", false, AlginType.CENTER, 20, 0) as WorldMapView;
			 worldMapView.setData();
			 DisplayManager.uiSprite.setBg(worldMapView);				
			//var mainClass:Class = SwfClassCache.getInstance().getClass(className);
			//
			//var worldMapView2:test = new mainClass();
			
		}
		
		public function isworldMapLock(_cid:int):int
		{
			for (var i:int = 0; i < DataManager.getInstance().worldData.worldmapInitData.curOpenScene.length; i++)
			{
				if (DataManager.getInstance().worldData.worldmapInitData.curOpenScene[i].cid == _cid)
				{
					return 1;
				}
			}
			return 0;			
		}		
		
	}
	
}