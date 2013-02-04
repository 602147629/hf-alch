package happyfish.scene.iso 
{
	import com.friendsofed.isometric.Point3D;
	import flash.display.BitmapData;
	import flash.display.Sprite;
	import flash.geom.Matrix;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.net.SharedObject;
	import flash.utils.ByteArray;
	import happyfish.scene.world.WorldView;
	import happymagic.manager.PublicDomain;
	import happymagic.model.vo.SceneClassVo;
	import happymagic.model.vo.SceneVo;
	
	/**
	 * 负责显示,这个类最终将被实际加入主显示对象
	 * @author slam
	 */
	public class IsoView extends Sprite
	{
		public var backgroundContainer:Sprite;
		public var big_backgroundContainer:Sprite;
		public var layers:Array = [];
		public var camera:Sprite;
		
		public static var NUM_LAYERS:int = 4;
		public var sceneY:int = 0;
		public function IsoView() 
		{
            this.camera = new Sprite();
			backgroundContainer = new Sprite();
			big_backgroundContainer = new Sprite();
            addChild(camera);
			initialize();
		}
		
		public function getLayer(index:uint):Sprite {
			return layers[index];
		}
		
		/**
		 * 将IsoSprite放入相应的layer里
		 * @param	isoSprite
		 */
        public function addIsoChild(isoSprite:IsoSprite) : void
        {
            this.layers[isoSprite.layer].addIsoChild(isoSprite);
            return;
        }
		
		public function removeIsoChild(isoSprite:IsoSprite) : void
		{
            this.layers[isoSprite.layer].removeIsoChild(isoSprite);
            return;
		}
		
        private function initialize() : void
        {
			camera.addChild(big_backgroundContainer);
			camera.addChild(backgroundContainer);
			
			var layer:IsoLayer = null;
			
            this.layers = [];
            var layerCount:int = 0;
            while (layerCount < NUM_LAYERS)
            {
                if (layerCount==WorldView.LAYER_REALTIME_SORT) 
				{
					 layer = new IsoLayer(this,true);
				}else {
					 layer = new IsoLayer(this,false);
				}
               
				layer.name = "isoLayer_" + layerCount.toString();
                camera.addChild(layer);
                layers.push(layer);
                layerCount++;
            }
			
            return;
        }
		
		public function resize(numCols:int):void
		{
			sceneY = -IsoUtil.TILE_SIZE * numCols / 2;
			backgroundContainer.y = sceneY;
			for (var i:int = 0; i < layers.length; i++)
			{
				layers[i].y = sceneY;
			}
		}
		
		public function center():void
		{
			//var offset:Point = new Point;
			//offset = camera.parent.localToGlobal(offset);
			var rect:Rectangle = camera.getBounds(null);
			var centerOffsetY:Number = (rect.top + rect.bottom) / 2;
			camera.x = stage.stageWidth / 2;
			camera.y = ( stage.stageHeight - PublicDomain.UI_HEIGHT) / 2 + PublicDomain.TOP_UI_HEIGHT - centerOffsetY;
		}
		
		public function fullCenter():void
		{
			center();
		}
		
	}

}