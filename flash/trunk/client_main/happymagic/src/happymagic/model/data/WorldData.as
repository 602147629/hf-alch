package happymagic.model.data 
{
	import happymagic.model.vo.classVo.WorldMapClassVo;
	import happymagic.model.vo.WorldMapVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class WorldData 
	{
		//世界地图的静态数据		
		private var _worldmapStaticData:Array = new Array();
		
		//世界地图的动态数据
		private var _worldmapInitData:WorldMapVo;		
		
		
		public function WorldData() 
		{
			
		}

		//根据sceneId获取世界地图的静态数据
		public function getWorldMapClassVo(_sceneId:int):WorldMapClassVo
		{
			for (var i:int = 0; i < worldmapStaticData.length; i++)
			{
				if (worldmapStaticData[i].sceneId == _sceneId)
				{
					return worldmapStaticData[i];
				}
			}
			return null;
		}
		
		//根据cid获取世界地图的静态数据
		public function getWorldMapClassCidVo(_cid:int):WorldMapClassVo
		{
			for (var i:int = 0; i < worldmapStaticData.length; i++)
			{
				if (worldmapStaticData[i].cid == _cid)
				{
					return worldmapStaticData[i];
				}
			}
			return null;
		}		
		
		//是否场景解锁
		public function isworldMapLock(_cid:int):int
		{
			for (var i:int = 0; i < worldmapInitData.curOpenScene.length; i++)
			{
				if (worldmapInitData.curOpenScene[i].cid == _cid)
				{
					return 1;
				}
			}
			return 0;			
		}		
			
		public function get worldmapStaticData():Array 
		{
			return _worldmapStaticData;
		}
		
		public function set worldmapStaticData(value:Array):void 
		{
			_worldmapStaticData = value;
		}
		
		public function get worldmapInitData():WorldMapVo 
		{
			return _worldmapInitData;
		}
		
		public function set worldmapInitData(value:WorldMapVo):void 
		{
			_worldmapInitData = value;
		}
		
	}

}