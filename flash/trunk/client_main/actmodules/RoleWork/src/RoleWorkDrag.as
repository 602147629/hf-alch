package  
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
	 * @author ZC
	 */
	public class RoleWorkDrag
	{
		private static var single:RoleWorkDrag;
		private var drag_area:Sprite;
		private var follow_area:Sprite;
		private var mouseDown:Boolean;
		private var startPosX:Number;
	    private var startPosY:Number
		private var wAdd:Number;
		private	var hAdd:Number;
		//sp1 需要一起跟随的
		public function RoleWorkDrag($drag_area:Sprite,follow_area:Sprite = null) 
		{
			this.drag_area = $drag_area;
			this.follow_area = follow_area;
			this.init();
			
		}
		
		//public static function getInstance($drag_area:Sprite = null):RoleWorkDrag
		//{
			//if (single === null) {
				//single = new RoleWorkDrag($drag_area,sp);
			//}
			//return single;
		//}
		
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
			//follow_area.startDrag(
		    //false, 
		    //new Rectangle(startPosX, startPosY, Math.max(wAdd, 0), Math.max(hAdd, 0)));
				//
		    //follow_area.stopDrag();			
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
			wAdd = rect.width - stage.stageWidth;
			hAdd = rect.height - (stage.stageHeight);
			
			startPosX = wAdd < 0 ? drag_area.x : stage.stageWidth - rect.right - offset.x;
			startPosY = hAdd < 0 ? drag_area.y : stage.stageHeight - rect.bottom - offset.y;
			
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
			//if (mouseDown) 
			//{
				//DataManager.getInstance().isDraging = true;
			//}
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
				
			    //follow_area.startDrag(
				//false, 
				//new Rectangle(startPosX, startPosY, Math.max(wAdd, 0), Math.max(hAdd, 0)));
				//
				//follow_area.stopDrag();
			}
			
		}
		
	}

}