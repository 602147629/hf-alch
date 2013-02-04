package happymagic.mix.view.ui 
{
	/**
	 * ...
	 * @author lite3
	 */
	public class MixListTab extends MixListTabUI 
	{
		public var index:int;
		
		public function MixListTab() 
		{
			select.mouseChildren = false;
			unselect.mouseChildren = false;
		}
		
		public function setType(type:int):void
		{
			select.gotoAndStop("type" + type);
			unselect.gotoAndStop("type" + type);
		}
		
	}

}