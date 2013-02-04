package happyfish.scene.fog
{
	import flash.geom.Rectangle;

	public class FogExplorer
	{
		public var id:String;
		
		public var x:int;
		public var z:int;
		
		public var range:int; //探索范围 0代表一格 1代表9格
		
		public var changed:Boolean; //变化标记
		
		public function FogExplorer(id:String)
		{
			this.id = id;
		}
		
		public function getRangeRect():Rectangle
		{
			//由于FogManager中的map是向外扩一圈的 因此0位置是从-1而非0开始 所以要偏移一个单位
			return new Rectangle(x-range+1,z-range+1,range*2+1,range*2+1);
		}
	}
}