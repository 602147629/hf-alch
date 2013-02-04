package happymagic.battle.view.ui 
{
	import com.greensock.TweenMax;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happymagic.manager.DataManager;
	import happymagic.model.data.ItemData;
	import happymagic.model.vo.ItemType;
	/**
	 * ...
	 * @author 
	 */
	public class BattleItemListView extends battleItemListUi 
	{
		private var hideId:Number;
		private var items:Array;
		private var itemList:DefaultListView;
		private var inited:Boolean;
		
		public function BattleItemListView() 
		{
			visible = false;
			alpha = 0;
			mouseChildren = mouseEnabled = false;
			
			x = -152;
			y = 132;
			
			itemList = new DefaultListView(new Sprite(), this, 12);
			itemList.init(190, 169, 42, 43, -91, -156);
			itemList.setGridItem(BattleItemListItemView, battleItemListItemUi);
		}
		
		public function init():void 
		{
			inited = true;
			var itemData:ItemData = DataManager.getInstance().itemData;
			items = itemData.getItemListByType2(ItemType.Atk);
			items = items.concat(itemData.getItemListByType2(ItemType.Drink));
			items = items.concat(itemData.getItemListByType2(ItemType.Food));
			
			items = itemData.getItemListByLabel(items, ["inBattle"]);
			itemList.setData(items);
		}
		
		public function show():void {
			TweenMax.killTweensOf(this);
			if (hideId) 
			{
				clearTimeout(hideId);
			}
			
			//if (!inited) 
			//{
				//init();
			//}
			
			init();
			
			mouseChildren = mouseEnabled = true;
			TweenMax.to(this, .3, { y:162,autoAlpha:1,onComplete:show_complete } );
		}
		
		private function show_complete():void 
		{
			
		}
		
		public function hide(now:Boolean=true):void {
			if (hideId) 
			{
				clearTimeout(hideId);
			}
			if (now) 
			{
				hideMv();
			}else {
				hideId = setTimeout(hideMv, 300);
			}
			
		}
		
		private function hideMv():void {
			if (hideId) 
			{
				clearTimeout(hideId);
			}
			hideId = 0;
			TweenMax.killTweensOf(this);
			TweenMax.to(this, .3, { y:132,autoAlpha:0,onComplete:hide_complete } );
		}
		
		private function hide_complete():void 
		{
			mouseChildren = mouseEnabled = false;
		}
	}

}