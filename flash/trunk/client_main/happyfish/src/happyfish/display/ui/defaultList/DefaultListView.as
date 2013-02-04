package happyfish.display.ui.defaultList 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.Sprite;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.GridPage;
	import happyfish.display.ui.SelectionGridPage;
	
	/**
	 * ...
	 * @author jj
	 */
	public class DefaultListView extends SelectionGridPage
	{
		public var autoAlginButton:Boolean;
		protected var itemClass:Class;
		protected var itemUiClass:Class;
		protected var blankItemClass:Class;
		protected var blankItemUiClass:Class;
		
		public function DefaultListView(uiview:Sprite,_container:DisplayObjectContainer,_pageLength:uint,_hideButton:Boolean=false,_autoAlginButton:Boolean=true) 
		{
			super(uiview as Sprite, _container);
			hideButtonFlag = _hideButton;
			autoAlginButton = _autoAlginButton;
			pageLength = _pageLength;
			
		}
		
		public function setButtonPosition(leftX:int,leftY:int,rightX:int,rightY:int):void {
			prevBtn.x = leftX;
			prevBtn.y = leftY;
			
			nextBtn.x = rightX;
			nextBtn.y = rightY;
		}
		
		override public function init(gridWidth:Number, gridHeight:Number, tileWidth:Number, tileHeight:Number, gridX:Number = 0, gridY:Number = 0, tileAlgin:String = "TL", algin:String = "TL"):void 
		{
			super.init(gridWidth, gridHeight, tileWidth, tileHeight, gridX, gridY, tileAlgin, algin);
			
			//设置右边按钮到列表最右侧
			if (autoAlginButton) 
			{
				if(nextBtn) nextBtn.x = gridX + gridWidth + 2;
			}
			
		}
		
		/**
		 * 设置列表内要创建的item的可视类
		 * @param	_itemClass			item可视对像类
		 * @param	_itemUiClass		item的ui素材类
		 * @param	_blankItemClass		空位占位item可视类
		 * @param	_blankItemUiClass	空位占位item的ui类
		 */
		public function setGridItem(_itemClass:Class,_itemUiClass:Class=null,_blankItemClass:Class=null,_blankItemUiClass:Class=null):void {
			itemClass = _itemClass;
			itemUiClass = _itemUiClass;
			blankItemClass = _blankItemClass;
			blankItemUiClass = _blankItemUiClass;
		}
		
		override protected function createItem(value:Object):GridItem 
		{
			var tmp:GridItem;
			
			if (itemUiClass==null) 
			{
				tmp = new itemClass() as GridItem;
			}else {
				tmp = new itemClass(new itemUiClass()) as GridItem;
			}
			
			tmp.setData(value);
			
			return tmp;
		}
		
		override public function createBlankItem():GridItem 
		{
			if (blankItemClass==null) 
			{
				return null;
			}
			var tmp:GridItem;
			
			if (blankItemUiClass==null) 
			{
				tmp = new blankItemClass() as GridItem;
			}else {
				tmp = new blankItemClass(new blankItemUiClass()) as GridItem;
			}
			
			return tmp;
		}
		
		//去第几页
		public function gopageLength(_num:uint):void
		{
			currentPage = _num;
			initPage();
		}
		
	}

}