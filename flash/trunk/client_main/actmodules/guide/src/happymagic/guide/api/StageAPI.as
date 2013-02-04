package happymagic.guide.api 
{
	import flash.display.Stage;
	/**
	 * ...
	 * @author lite3
	 */
	public class StageAPI 
	{
		private var stage:Stage
		public function StageAPI(stage:Stage) 
		{
			this.stage = stage;
		}
		
		public function get enabled():void { stage.mouseChildren = true; }
		public function get disabled():void { stage.mouseChildren = false; }
		
	}

}