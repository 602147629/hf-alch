package happymagic.view 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import happyfish.display.ui.GridItem;
	import happymagic.model.vo.StoragItemVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class StorageItemItemView extends GridItem
	{
		private var iview:StorageItemItemViewUi;
		private var data:StoragItemVo;
		public function StorageItemItemView(_uiview:MovieClip) 
		{
			super(_uiview);
			
			iview = _uiview as StorageItemItemViewUi;	
			
		}
		
		override public function setData(_data:Object):void
		{
			data = _data as StoragItemVo;
			
			iview.icon.gotoAndStop(data.type);
			iview.num.text = String(data.num);
		}
		
	}

}