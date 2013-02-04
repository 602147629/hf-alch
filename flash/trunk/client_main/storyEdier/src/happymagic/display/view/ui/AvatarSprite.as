package happymagic.display.view.ui 
{
	import flash.display.FrameLabel;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.geom.Rectangle;
	import happyfish.cacher.SwfClassCache;
	import happyfish.events.SwfClassCacheEvent;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class AvatarSprite extends Sprite 
	{
		private var mc:MovieClip;
		private var className:String;
		public var data:Object;
		
		private var _borderW:Number;
		private var _borderH:Number;
		
		private var label:String;
		private var isPlaying:Boolean;
		private var startFrame:int;
		private var endFrame:int;
		
		private var frameCount:int = 5;
		private var currFrameCount:int = 0;
		
		public function AvatarSprite(x:Number = 0, y:Number = 0, borderW:Number = Number.NaN, borderH:Number = Number.NaN) 
		{
			this.x = x;
			this.y = y;
			_borderW = borderW;
			_borderH = borderH;
			mouseEnabled = false;
			mouseChildren = false;
		}
		
		
		
		public function load(className:String):void
		{
			if (this.className == className) return;
			
			clear();
			this.className = className; 
			SwfClassCache.getInstance().addEventListener(SwfClassCacheEvent.COMPLETE, loadCompleteHandler);
			SwfClassCache.getInstance().loadClass(className);
		}
		
		public function playLoop(label:String = null):void
		{
			isPlaying = true;
			this.label = label;
			if (!mc) return;
			
			startFrame = getKeyFrame(label);
			endFrame = getKeyFrameEnd(label);
			if (startFrame != 0) mc.gotoAndStop(startFrame);
			if (0 == startFrame || 0 == endFrame || startFrame == endFrame)
			{
				stop();
				return;
			}
			mc.gotoAndStop(startFrame);
			mc.addEventListener(Event.ENTER_FRAME, enterFrameHandler);
			
		}
		
		private function enterFrameHandler(e:Event):void 
		{
			currFrameCount = ++currFrameCount % frameCount;
			if (0 == currFrameCount)
			{
				if (mc.currentFrame >= endFrame)
				{
					mc.gotoAndStop(startFrame);
				}else
				{
					mc.nextFrame();
				}
			}
		}
		
		private function getKeyFrame(label:String):int 
		{
			for (var i:int = mc.currentLabels.length - 1; i >= 0; i--)
			{
				var tmp:FrameLabel = FrameLabel(mc.currentLabels[i]);
				if (tmp.name == label) return tmp.frame;
			}
			return 0;
		}
		
		private function getKeyFrameEnd(label:String):int 
		{
			var len:int = mc.currentLabels.length;
			for (var i:int = 0; i < len; i++)
			{
				var tmp:FrameLabel = FrameLabel(mc.currentLabels[i]);
				if (tmp.name != label) continue;
				if (tmp.frame == mc.totalFrames || i == len - 1) return mc.totalFrames;
				return FrameLabel(mc.currentLabels[i + 1]).frame - 1;
			}
			return 0;
		}
		
		public function stop():void
		{
			if (!isPlaying) return;
			isPlaying = false;
			if (mc)
			{
				mc.stop();
				mc.removeEventListener(Event.ENTER_FRAME, enterFrameHandler);
			}
		}
		
		private function clear():void 
		{
			this.className = null;
			SwfClassCache.getInstance().removeEventListener(SwfClassCacheEvent.COMPLETE, loadCompleteHandler);
			stop();
			if (mc && mc.parent == this) removeChild(mc);
			mc = null;
			label = null;
		}
		
		private function loadCompleteHandler(e:SwfClassCacheEvent):void 
		{
			if (e.className != className) return;
			SwfClassCache.getInstance().removeEventListener(SwfClassCacheEvent.COMPLETE, loadCompleteHandler);
			
			var Ref:Class = SwfClassCache.getInstance().getClass(className);
			mc = new Ref() as MovieClip;
			if (isPlaying) playLoop(label);
			else mc.gotoAndStop(label);
			addChild(mc);
			setBorder(_borderW, _borderH);
		}
		
		public function get borderH():Number { return _borderH; }
		public function get borderW():Number { return _borderW; }
		
		public function setBorder(w:Number, h:Number):void
		{
			_borderW = w;
			_borderH = h;
			if (!mc) return;
			if (isNaN(w) || isNaN(h))
			{
				mc.scaleX = 1.0;
				mc.scaleY = 1.0;
			}else
			{
				var rateX:Number = w / (mc.width / mc.scaleX);
				var rateY:Number = h / (mc.height / mc.scaleY);
				var rate:Number = Math.min(rateX, rateY);
				mc.scaleX = rate;
				mc.scaleY = rate;
				var rect:Rectangle = mc.getRect(null);
				mc.x = (w - mc.width) * 0.5 - rect.x * rate;
				mc.y = (h - mc.height) * 0.5 - rect.y * rate;
			}
		}
	}
}