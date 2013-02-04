package happyfish.scene.world 
{
	/**
	 * ...
	 * @author ...
	 */
	public class WorldLayerDict 
	{
		public static const LAYER_BOTTOM:int = 0; //最低层,不排序
		public static const LAYER_REALTIME_SORT:int = 1; //墙壁装饰,桌子,人,几乎所有需要排序的
		public static const LAYER_FLYING:int = 2; //不明飞行物
		public static const LAYER_MV:int = 3; //特效层
		public function WorldLayerDict() 
		{
			
		}
		
	}

}