package happymagic.shop.view 
{
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.manager.EventManager;
	import happymagic.model.vo.ItemType;
	import happymagic.shop.controller.ShopItem;
	import happymagic.shop.model.event.ShopEvent;
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class ShopView extends ShopPanelUi
	{
		private var listController:DefaultListView;
		
		private var itemList:Array; //物品列表
		private var matList:Array; //材料列表
		
		private var currentList:Array;
		
		public function ShopView() 
		{
			listController = new DefaultListView(this, this, 9, false, false);
			listController.init(510, 320, 166, 100, -249, -122);
			listController.setGridItem(ShopItem, ShopItemUi);
			
			itemTabBtn.buttonMode = matTabBtn.buttonMode = true;
			for (var i:int = 0; i < 5; i++) this["subTabBtn" + i].buttonMode = true;
			
			subTabBtnWord.mouseEnabled = subTabBtnWord.mouseChildren = false;
			
			addEventListener(MouseEvent.CLICK, onClick);
		}
		
		public function setData(itemList:Array, matList:Array):void
		{
			this.itemList = itemList;
			this.matList = matList;
			tab();
		}
		
		private function onClick(event:MouseEvent):void
		{
			var mat:Boolean = currentList == matList;
			
			switch(event.target.name)
			{
				case "itemTabBtn":
					tab();
				break;
				
				case "matTabBtn":
					tab(true);
				break;
				
				case "subTabBtn0":
					subTab(0);
				break;
				
				case "subTabBtn1":
					subTab(1, mat ? ItemType.Plant : ItemType.Merchandise);
				break;
				
				case "subTabBtn2":
					subTab(2, mat ? ItemType.Animal : ItemType.Food);
				break;
				
				case "subTabBtn3":
					subTab(3, mat ? ItemType.Ore : ItemType.Tool);
				break;
				
				case "subTabBtn4":
					subTab(4, mat ? ItemType.SpecialStuff : ItemType.Atk);
				break;
				
				case "closeBtn":
					removeEventListener(MouseEvent.CLICK, onClick);
					var shopEvent:ShopEvent = new ShopEvent(ShopEvent.CLOSE);
					EventManager.getInstance().dispatchEvent(shopEvent);
				break;
			}
		}
		
		//切换大分类
		private function tab(mat:Boolean = false):void
		{
			if (mat)
			{
				itemTabBtn.gotoAndStop(2);
				matTabBtn.gotoAndStop(1);
				subTabBtnWord.gotoAndStop(2);
				currentList = matList;
			}
			else
			{
				itemTabBtn.gotoAndStop(1);
				matTabBtn.gotoAndStop(2);
				subTabBtnWord.gotoAndStop(1);
				currentList = itemList;
			}
			
			subTabBtn0.gotoAndStop(1);
			for (var i:int = 1; i < 5; i++) this["subTabBtn" + i].gotoAndStop(2);
			listController.setData(currentList);
		}
		
		//切换子分类
		private function subTab(index:int,subType:int = -1):void
		{
			for (var i:int = 0; i < 5; i++)
			{
				if (i == index) this["subTabBtn" + i].gotoAndStop(1);
				else this["subTabBtn" + i].gotoAndStop(2);
			}
			if (subType == -1) listController.setData(currentList);
			else listController.setData(currentList, "type2", subType);
		}
		
	}

}