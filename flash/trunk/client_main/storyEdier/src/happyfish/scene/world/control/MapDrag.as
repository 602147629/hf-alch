package happyfish.scene.world.control 
{
	import flash.display.Sprite;
	import flash.display.Stage;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.WorldView;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.PublicDomain;
	/**
	 * ...
	 * @author slam Xu
	 */
	public class MapDrag
	{
		private static var single:MapDrag;
		private var drag_area:Sprite;
		private var mouseDown:Boolean;
		
		public function MapDrag($drag_area:Sprite) 
		{
			this.drag_area = $drag_area;
			this.init();
		}
		
		public static function getInstance($drag_area:Sprite = null):MapDrag
		{
			if (single === null) {
				single = new MapDrag($drag_area);
			}
			return single;
		}
		
		public function init():void
		{
			drag_area.addEventListener(MouseEvent.MOUSE_DOWN, beginDrag);
			drag_area.addEventListener(MouseEvent.MOUSE_MOVE, moveDrag);
			drag_area.stage.addEventListener(MouseEvent.MOUSE_UP, upDrag);
			drag_area.addEventListener(MouseEvent.MOUSE_OUT, overDrag);
		}
		
		private function upDrag(e:MouseEvent):void 
		{
			mouseDown = false;
			drag_area.stopDrag();
		}
	
		/**
		 * 主地图背景拖动开始
		 */
		public function beginDrag(e:MouseEvent):void
		{
			mouseDown = true;
			
			var offset:Point = new Point;
			offset = drag_area.parent.localToGlobal(offset);
			
			var rect:Rectangle = drag_area.getBounds(drag_area);
			
			var stage:Stage = drag_area.stage;
			var wAdd:Number = rect.width - stage.stageWidth;
			var hAdd:Number = rect.height - (stage.stageHeight - PublicDomain.UI_HEIGHT);
			
			var startPosX:Number = wAdd < 0 ? drag_area.x : stage.stageWidth - rect.right - offset.x;
			var startPosY:Number = hAdd < 0 ? drag_area.y : stage.stageHeight - PublicDomain.UI_HEIGHT - rect.bottom - offset.y + PublicDomain.TOP_UI_HEIGHT;
			
			drag_area.startDrag(
				false, 
				new Rectangle(startPosX, startPosY, Math.max(wAdd,0), Math.max(hAdd,0))
			);
		}
		
		/**
		 * 主地图背景拖动中
		 */
		public function moveDrag(e:MouseEvent):void
		{
			if (mouseDown) 
			{
				DataManager.getInstance().isDraging = true;
			}
		}

		/**
		 * 主地图背景拖动结束
		 */
		public function overDrag(e:MouseEvent):void
		{
			if (!mouseDown) 
			{
				mouseDown = false;
				drag_area.stopDrag();
			}
			
		}
		
	}

}