package happyfish.scene.fog
{
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.GradientType;
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.geom.Matrix;
	import flash.utils.ByteArray;
	import flash.utils.Timer;
	import happyfish.display.control.BitmapTool;

	/**
	 * 
	 * @author XiaJunJie
	 */
	public class FogView extends Bitmap
	{
		private var ellipse:BitmapData;
		private var nodeSize:int;
		public var offsetX:int;
		public var offsetY:int;
		private const SIZESCALE:int = 15;
		
		private var dissipateList:Vector.<FogNode>;
		private var dissipateTimer:Timer;
		
		public function FogView(scenePic:BitmapData, color:int, alpha:Number, nodeSize:int)
		{
			super(scenePic);
			var container:Sprite = new Sprite;
			//container.addChild(new Bitmap(scenePic));
			//var fogMask:Shape = new Shape;
			//with (fogMask.graphics)
			//{
				//beginFill(color, alpha);
				//drawRect(0, 0, scenePic.width, scenePic.height);
				//endFill();
			//}
			//container.addChild(fogMask);
			//bitmapData.draw(container);
			
			this.nodeSize = nodeSize;
			
			var cube:Shape = new Shape;
			var mat:Matrix = new Matrix;
			mat.createGradientBox(nodeSize*SIZESCALE,nodeSize*SIZESCALE,0,0,0);
			with(cube.graphics)
			{
				beginGradientFill(GradientType.RADIAL,[0xFFFFFF,0xFF0000],[0,1],[64,255],mat);
				drawRect(0,0,nodeSize*SIZESCALE,nodeSize*SIZESCALE);
				endFill();
			}
			cube.scaleY = 0.5;
			container = new Sprite;
			container.addChild(cube);
			ellipse = new BitmapData(nodeSize*SIZESCALE,nodeSize*SIZESCALE/2,true,0);
			ellipse.draw(container);
			
			this.addEventListener(Event.ADDED_TO_STAGE, onAddedToStage);
			FogManager.getInstance().addEventListener(FogEvent.FOG_CHANGE, onFogChange);
			
			dissipateList = new Vector.<FogNode>;
			dissipateTimer = new Timer(200);
			dissipateTimer.addEventListener(TimerEvent.TIMER, onDissipate);
			dissipateTimer.start();
		}
		
		private function onAddedToStage(event:Event):void
		{
			this.removeEventListener(Event.ADDED_TO_STAGE, onAddedToStage);
			this.parent.addEventListener(MouseEvent.MOUSE_OVER, onMouseEvent, true,1);
			this.parent.addEventListener(MouseEvent.ROLL_OVER, onMouseEvent, true,1);
			this.parent.addEventListener(MouseEvent.MOUSE_MOVE, onMouseEvent, true, 1);
		}
		
		private function onMouseEvent(event:MouseEvent):void
		{
			var pixel:uint = bitmapData.getPixel32(mouseX, mouseY);
			var alpha:uint = pixel >> 24 & 0xFF;
			if (alpha > 0x7f) //如果鼠标移动到不透明的地方
			{
				event.stopPropagation(); //阻止鼠标事件
			}
		}
		
		private function onFogChange(event:FogEvent):void
		{
			var list:Vector.<FogNode> = event.list;
			for (var i:int = 0; i < list.length; i++)
			{
				if (event.immediately) dissipate(list[i]);
				else if (dissipateList.indexOf(list[i]) == -1) dissipateList.push(list[i]);
			}
		}
		
		//以固定的速度擦除迷雾 每200毫秒擦两个
		private function onDissipate(event:TimerEvent):void
		{
			for (var i:int = 0; i < 2; i++)
			{
				var curNode:FogNode = dissipateList.shift();
				dissipate(curNode);
				
				//if (!curNode || curNode.explored == false) continue;
				//var screenX:Number = (curNode.x - curNode.z) * nodeSize - ellipse.width/2 + offsetX;
				//var screenY:Number = (curNode.x + curNode.z) * .5 * nodeSize - ellipse.height/2 + offsetY;
				//BitmapTool.blendAlpha(bitmapData, ellipse, screenX, screenY, BitmapTool.LIGHT);
			}
		}
		
		private function dissipate(node:FogNode):void
		{
			if (!node || node.explored == false) return;
			var screenX:Number = (node.x - node.z) * nodeSize - ellipse.width/2 + offsetX;
			var screenY:Number = (node.x + node.z) * .5 * nodeSize - ellipse.height/2 + offsetY;
			BitmapTool.blendAlpha(bitmapData, ellipse, screenX, screenY, BitmapTool.LIGHT);
		}
		
		public function clear():void
		{
			this.removeEventListener(Event.ADDED_TO_STAGE, onAddedToStage);
			this.parent.removeEventListener(MouseEvent.MOUSE_OVER, onMouseEvent, true);
			this.parent.removeEventListener(MouseEvent.ROLL_OVER, onMouseEvent, true);
			this.parent.removeEventListener(MouseEvent.MOUSE_MOVE, onMouseEvent, true);
			this.parent.removeChild(this);
			FogManager.getInstance().removeEventListener(FogEvent.FOG_CHANGE, onFogChange);
			
			dissipateList = null;
			dissipateTimer.stop();
			dissipateTimer.removeEventListener(TimerEvent.TIMER, onDissipate);
		}
	}
}