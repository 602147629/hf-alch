package happymagic.order.flow 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import happyfish.utils.display.McUtil;
	import happymagic.order.view.ui.SubmitFinishOrderMovie;
	/**
	 * ...
	 * @author lite3
	 */
	public class SubmitFinishOrderMovieFlow 
	{
		
		//function (flow:SubmitFinishOrderMovieFlow):void;
		private var callback:Function;
		
		public function showAt(x:int, y:int, container:DisplayObjectContainer, callback:Function):SubmitFinishOrderMovieFlow
		{
			this.callback = callback;
			var movie:MovieClip = new SubmitFinishOrderMovie();
			movie.mouseChildren = false;
			movie.mouseEnabled = false;
			movie.x = x;
			movie.y = y;
			container.addChild(movie);
			var flow:SubmitFinishOrderMovieFlow = this;
			movie.addFrameScript(movie.totalFrames - 1, function():void
			{
				movie.stop();
				if (movie.parent) movie.parent.removeChild(movie);
				movie.addFrameScript(movie.totalFrames - 1, null);
				
				if (callback != null) callback(flow);
				callback = null;
			});
			
			return this;
		}
		
	}

}