package happyfish.editer.view 
{
	import com.adobe.serialization.json.JSON;
	import fl.controls.dataGridClasses.DataGridColumn;
	import fl.data.DataProvider;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import happyfish.editer.scene.view.EditIsoItem;
	/**
	 * ...
	 * @author ...
	 */
	public class ItemDataInfoView 
	{
		private var iview:dataInfoUi;
		public function ItemDataInfoView(ui:dataInfoUi) 
		{
			iview = ui;
			iview.closeBtn.addEventListener(MouseEvent.CLICK, closeMe);
			
			iview.grid.editable = true;
			iview.grid.sortableColumns = false;
		}
		
		private function closeMe(e:MouseEvent):void 
		{
			visible = false;
		}
		
		public function setData(isoItem:EditIsoItem, labelNames:Array):void {
			
			iview.grid.removeAll();
			iview.grid.removeAllColumns();
			
			iview.nameTxt.text = isoItem.data.name;
			
			for (var i:int = 0; i < labelNames.length; i++) 
			{
				if (isoItem.data.hasOwnProperty(labelNames[i])) 
				{
					iview.grid.addColumn(new DataGridColumn(labelNames[i]));
				}
			}
			
			iview.grid.dataProvider = new DataProvider([isoItem.data]);
		}
		
		public function get visible():Boolean 
		{
			return iview.visible;
		}
		
		public function set visible(value:Boolean):void 
		{
			iview.visible = value;
		}
		
	}

}