package happyfish.display.control
{
	import flash.display.BitmapData;
	import flash.geom.Rectangle;
	import flash.utils.ByteArray;

	/**
	 * XiaJunJie
	 * 位图工具
	 */ 
	public class BitmapTool
	{
		/**
		 * 将target中的像素的alpha应用到source中
		 * @param type: BitmapTool.LIGHT表示只淡不深 BitmapTool.STRONG表示只深不淡 null表示直接设置
		 */ 
		public static function blendAlpha(source:BitmapData,target:BitmapData,x:int=0,y:int=0,type:String=null):void
		{
			source.lock();
			for (var i:int = 0; i < target.width; i++)
			{
				for (var j:int = 0; j < target.height; j++)
				{
					var alpha:uint = target.getPixel32(i, j) >> 24 & 0xFF;
					var sourcePixel:uint = source.getPixel32(i+x,j+y);
					var sourceAlpha:uint = sourcePixel >> 24 & 0xFF;
					
					if(type==LIGHT && sourceAlpha <= alpha) continue;
					else if(type==STRONG && sourceAlpha >= alpha) continue;
					else
					{
						var newPixel:uint = alpha * 0x1000000 + (sourcePixel & 0xFFFFFF);
						source.setPixel32(i + x, j + y, newPixel);
					}
				}
			}
			source.unlock();
		}
		
		public static const LIGHT:String = "light";
		public static const STRONG:String = "strong";
	}
}