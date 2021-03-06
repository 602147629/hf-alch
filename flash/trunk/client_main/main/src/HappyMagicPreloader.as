package 
{
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.display.StageAlign;
	import flash.display.StageScaleMode;
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.events.ProgressEvent;
	import flash.net.registerClassAlias;
	import flash.utils.getDefinitionByName;
	
	/**
	 * ...
	 * @author jj
	 */
	public class HappyMagicPreloader extends MovieClip 
	{
		
		public function HappyMagicPreloader() 
		{
			registerClassAlias("HappyMagicMain2", HappyMagicMain);
			if (stage) {
				stage.scaleMode = StageScaleMode.NO_SCALE;
				stage.align = StageAlign.TOP_LEFT;
			}
			
			
			
			//addEventListener(Event.ENTER_FRAME, checkFrame);
			//loaderInfo.addEventListener(ProgressEvent.PROGRESS, progress);
			//loaderInfo.addEventListener(IOErrorEvent.IO_ERROR, ioError);
			//loadingFinished();
			// TODO show loader
		}
		
		private function ioError(e:IOErrorEvent):void 
		{
			trace(e.text);
		}
		
		private function progress(e:ProgressEvent):void 
		{
			// TODO update loader
		}
		
		private function checkFrame(e:Event):void 
		{
			if (currentFrame == totalFrames) 
			{
				removeEventListener(Event.ENTER_FRAME, checkFrame);
				stop();
			}
		}
		
		private function loadingFinished():void 
		{
			removeEventListener(Event.ENTER_FRAME, checkFrame);
			loaderInfo.removeEventListener(ProgressEvent.PROGRESS, progress);
			loaderInfo.removeEventListener(IOErrorEvent.IO_ERROR, ioError);
			
			// TODO hide loader
			
			startup();
		}
		
		private function startup():void 
		{
			//var mainClass:Class = getDefinitionByName("HappyMagicMain") as Class;
			//addChild(new mainClass() as DisplayObject);
			//
		}
		
	}
	
}