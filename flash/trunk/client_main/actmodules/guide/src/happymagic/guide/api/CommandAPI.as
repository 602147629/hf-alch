package happymagic.guide.api 
{
	import happymagic.guide.commands.FinishGuideCommand;
	import happymagic.guide.commands.UpdateGuideCommand;
	/**
	 * ...
	 * @author lite3
	 */
	public class CommandAPI 
	{
		
		public function finishGuide():void 
		{
			new FinishGuideCommand().finishGuide();
		}
		
		public function updateStep(step:int):void
		{
			new UpdateGuideCommand().update(step + 1);
		}
		
	}

}