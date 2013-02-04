package happyfish.storyEdit.display 
{
	import fl.controls.listClasses.CellRenderer;
	
	/**
	 * ...
	 * @author jj
	 */
	public class ActionRender extends CellRenderer 
	{
		
		public var columnIndex:int;
		public function ActionRender() 
		{
			
		}
		
		override protected function draw():void 
		{
			columnIndex = listData.column;
			//setSize( data.actions.length*30, 30);
			super.draw();
		}
		
	}

}