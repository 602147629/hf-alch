package happymagic.battle.controller 
{
	import flash.display.FrameLabel;
	import flash.display.MovieClip;
	import flash.events.Event;
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class MCController 
	{
		public var mc:MovieClip;
		private var fps:int;
		
		private var frameInterval:int; //跳帧间隔
		private var frameCount:int; //自上一次跳帧到现在的帧记数
		
		private var curLabelIndex:int; //当前标签是时间轴上的第几个标签
		private var curLabelEnd:int; //当前标签的最终帧
		
		private var _isPlaying:Boolean; //是否正在播放
		private var playType:int; //STOP_AT_END/LOOP/LET_IT_GO
		
		public function MCController(mc:MovieClip, fps:int = 18) 
		{
			this.mc = mc;
			mc.stop();
			mc.addEventListener(Event.ENTER_FRAME, onEnterFrame);
			
			this.fps = fps;
			
			frameInterval = Math.round(GAMEFPS / fps);
			frameCount = 0;
			
			curLabelEnd = getCurLabelEnd();
		}
		
		public function gotoAndPlay(frame:Object, playType:int = LET_IT_GO):void
		{
			goto(frame);
			_isPlaying = true;
			this.playType = playType;
		}
		
		public function gotoAndStop(frame:Object):void
		{
			goto(frame);
			_isPlaying = false;
		}
		
		public function stop():void
		{
			_isPlaying = false;
		}
		
		public function get isPlaying():Boolean
		{
			return _isPlaying;
		}
		
		private function goto(frame:Object):void
		{
			mc.gotoAndStop(frame);
			for (var i:int = 0; i < mc.currentLabels.length; i++)
			{
				if (mc.currentLabels[i].frame >= mc.currentFrame )
				{
					curLabelIndex = i;
					curLabelEnd = getCurLabelEnd();
					return;
				}
			}
		}
		
		private function onEnterFrame(event:Event):void
		{
			if (!_isPlaying) return;
			
			frameCount++;
			if (frameCount >= frameInterval)
			{
				frameCount = 0;
				
				if (mc.currentFrame == curLabelEnd) //已经播到头
				{
					if (playType == LET_IT_GO) //继续播放
					{
						curLabelIndex++;
						if (curLabelIndex >= mc.currentLabels.length) curLabelIndex = 0;
						curLabelEnd = getCurLabelEnd();
						
						if (mc.currentFrame == mc.totalFrames) mc.gotoAndStop(1);
						else mc.nextFrame();
					}
					else if (playType == STOP_AT_END) //停止
					{
						_isPlaying = false;
					}
					else if (playType == LOOP) //循环
					{
						mc.gotoAndStop(mc.currentLabels[curLabelIndex].frame);
					}
					mc.dispatchEvent(new Event(Event.COMPLETE));
				}
				else mc.nextFrame();
			}
		}
		
		private function getCurLabelEnd():int
		{
			var curLabels:Array = mc.currentLabels;
			if ((curLabelIndex + 1) >= curLabels.length) return mc.totalFrames;
			else return curLabels[curLabelIndex + 1].frame-1;
		}
		
		public function clear():void
		{
			mc.removeEventListener(Event.ENTER_FRAME, onEnterFrame);
		}
		
		//常量----------------------------------------
		private const GAMEFPS:int = 30;
		
		public static const LET_IT_GO:int = 0;
		public static const STOP_AT_END:int = 1;
		public static const LOOP:int = 2;
	}

}