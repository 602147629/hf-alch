package happyfish.utils.display 
{
	import flash.display.BitmapData;
	import flash.display.FrameLabel;
	import flash.display.MovieClip;
	import flash.events.Event;
	/**
	 * ...
	 * @author lite3
	 */
	public class MoviePlayer 
	{
		private var movie:MovieClip;
		
		public function MoviePlayer(movie:MovieClip = null) 
		{
			this.movie = movie;
		}
		
		public function setMovie(_movie:MovieClip, stopOld:Boolean = true):void
		{
			if (movie)
			{
				clearScript();
				if(stopOld) movie.stop();
			}
			movie = _movie;
		}
		
		public function dispose():void
		{
			if (!movie) return;
			clearScript();
			stop();
		}
		
		public function stop():void
		{
			if (movie) movie.stop();
		}
		
		public function playLoop(start:*, end:*):void
		{
			if (!movie) return;
			
			clearScript();
			start = getFrame(start, movie);
			end = getFrame(end, movie);
			if (start == end || start > end)
			{
				movie.gotoAndStop(start);
			}else
			{
				var _movie:MovieClip = movie;
				movie.gotoAndPlay(start);
				movie.addFrameScript(end - 1, function():void { _movie.gotoAndPlay(start); } );
			}
		}
		
		private function getFrame(frame:*, movie:MovieClip):int 
		{
			if (frame is Number)
			{
				frame = int(frame);
				if (frame <= 0) frame = 1;
				else if (frame > movie.totalFrames) frame = movie.totalFrames;
				return frame;
			}
			
			frame = String(frame);
			for each(var label:FrameLabel in movie.currentLabels)
			{
				if (label.name == frame) return label.frame;
			}
			
			return 1;
		}
		
		private function clearScript():void
		{
			for each(var label:FrameLabel in movie.currentLabels)
			{
				movie.addFrameScript(label.frame-1, null);
			}
		}
	}

}