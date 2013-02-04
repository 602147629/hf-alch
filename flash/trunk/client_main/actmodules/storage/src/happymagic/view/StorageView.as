package happymagic.view 
{
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.Pagination;
	import happyfish.display.ui.TabelView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happymagic.event.StorageEvent;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.ItemType;
	import happymagic.model.vo.ItemVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class StorageView extends UISprite
	{
		private var iview:StorageViewUI;
		private var topTab:TabelView; 	
		private var list:StorageListView;
		private var first:Boolean = true;
		private var centreTab:TabelView;
		private var data:Array = new Array();
		private var type:int;
		
		public function StorageView() 
		{
			_view = new StorageViewUI();
			iview = _view as StorageViewUI;
			iview.addEventListener(MouseEvent.CLICK, clickrun);	
			
			init2();
			
		    list = new StorageListView(new StorageListViewUi(), iview, 8, false, false);
		    var pag:Pagination = new Pagination(100,5);
			pag.x = 265;
			pag.y = 330;
			list.pagination = pag;			
			list.init(580, 680, 125, 155, 0,0);
			list.setGridItem(StorageItemView, StorageItemViewUi);
			list.x = -252;
			list.y = -120;
			list.tweenTime = 0;	
			list.setButtonPosition( -30, 135, 537, 135);
			

			topTab = new TabelView();
			
			iview.addChild(topTab);
			topTab.btwX = 5;
			topTab.x = -226;
			topTab.y = -157;
			topTab.setTabs([iview.Furnace, ItemType.Furnace],[iview.Stuff, ItemType.Stuff], [iview.Goods, ItemType.Goods], [iview.Equipment, ItemType.Equipment], [iview.Scroll, ItemType.Scroll],[iview.Decor,ItemType.Decor]);
			topTab.addEventListener(Event.SELECT, tab_select);    
			topTab.select(0);	
			
			EventManager.getInstance().addEventListener(StorageEvent.USEITEMCOMPLETE,useitemcomplete);

		}
		
		public function getItemView(cid:int):MovieClip
		{
			var item:GridItem = list.getItemByKey("cid", cid);
			return item ? item.view : null;
		}
		
		private function useitemcomplete(e:StorageEvent):void 
		{
			list.initPage();
		}
		
		private function init2():void 
		{
			//iview.all.visible = false;
			iview.Skill.visible = false;			
			iview.Weapon.visible = false;
			iview.Armor.visible = false;
			iview.Ornament.visible = false;			
			iview.Other.visible = false;
			iview.Task.visible = false;
			iview.Drink.visible = false;
			iview.Food.visible = false;			
			iview.Tool.visible = false;
			iview.Atk.visible = false;
			iview.Merchandise.visible = false;
			iview.Plant.visible = false;			
			iview.Ore.visible = false;
			iview.Animal.visible = false;
			iview.SpecialStuff.visible = false;
			iview.Floor.visible = false;			
			iview.Wall.visible = false;
			iview.Decoration.visible = false;
			iview.DecorOnWall.visible = false;
			iview.Door.visible = false;			
			
		}
		
		private function tab_select(e:Event):void 
		{		
			setData((e.target as TabelView).selectValue);
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
		    {
				case "closebtn":
					close();
					break;
			}
		}

		public function setData(_type:int):void
		{
			init2();
			var i:int = 0;
			
			type = _type;
			
            if (centreTab)
			{
				iview.removeChild(centreTab);
			}
            switch(type)	
			{
				case ItemType.Furnace:
						centreTab = new TabelView();
						iview.addChild(centreTab);
						centreTab.btwX = 4;
						centreTab.x = -238;
						centreTab.y = -128;
						centreTab.setTabs([iview.all, 1000]);
						centreTab.addEventListener(Event.SELECT, centretab_select);    
						centreTab.select(0);						
					break;
					
				case ItemType.Stuff:					
					    iview.Plant.visible = true;
					    iview.Ore.visible = true;											
					    iview.Animal.visible = true;						
					    iview.SpecialStuff.visible = true;	
						
						centreTab = new TabelView();
						iview.addChild(centreTab);
						centreTab.btwX = 4;
						centreTab.x = -238;
						centreTab.y = -128;
						centreTab.setTabs([iview.all, 1000],[iview.Plant,ItemType.Plant],[iview.Ore,ItemType.Ore],[iview.Animal,ItemType.Animal],[iview.SpecialStuff,ItemType.SpecialStuff]);
						centreTab.addEventListener(Event.SELECT, centretab_select);    
						centreTab.select(0);					
						
					break;
					
				case ItemType.Goods:
					    iview.Drink.visible = true;
					    iview.Food.visible = true;						
					    iview.Tool.visible = true;						
					    iview.Atk.visible = true;						
					    iview.Merchandise.visible = true;					
					    iview.Task.visible = true;
						centreTab = new TabelView();
						iview.addChild(centreTab);
						centreTab.btwX = 4;
						centreTab.x = -238;
						centreTab.y = -128;
						centreTab.setTabs([iview.all, 1000],[iview.Merchandise,ItemType.Merchandise],[iview.Tool,ItemType.Tool],[iview.Food,ItemType.Food],[iview.Drink,ItemType.Drink],[iview.Atk,ItemType.Atk],[iview.Task,ItemType.Task]);						
						centreTab.addEventListener(Event.SELECT, centretab_select);    
						centreTab.select(0);					
					
					break;
					
				case ItemType.Equipment:
					
					    iview.Weapon.visible = true;
					    iview.Armor.visible = true;						
					    iview.Other.visible = true;						
					    iview.Ornament.visible = true;						
					
						centreTab = new TabelView();
						iview.addChild(centreTab);
						centreTab.btwX = 4;
						centreTab.x = -238;
						centreTab.y = -128;
						centreTab.setTabs([iview.all, 1000],[iview.Weapon,ItemType.Weapon],[iview.Armor,ItemType.Armor],[iview.Other,ItemType.Other],[iview.Ornament,ItemType.Ornament]);						
						centreTab.addEventListener(Event.SELECT, centretab_select);    
						centreTab.select(0);					
					break;
					
				case ItemType.Scroll:					
					    iview.Skill.visible = true;
						
						centreTab = new TabelView();
						iview.addChild(centreTab);
						centreTab.btwX = 4;
						centreTab.x = -238;
						centreTab.y = -128;
						centreTab.setTabs([iview.all, 1000],[iview.Skill, ItemType.Skill]);
						centreTab.addEventListener(Event.SELECT, centretab_select);    
						centreTab.select(0);					
					break;
					
				case ItemType.Decor:
					    iview.Floor.visible = true;
					    iview.Wall.visible = true;						
					    iview.Decoration.visible = true;						
					    iview.DecorOnWall.visible = true;						
					    iview.Door.visible = true;						
					
						centreTab = new TabelView();
						iview.addChild(centreTab);
						centreTab.btwX = 4;
						centreTab.x = -238;
						centreTab.y = -128;
						centreTab.setTabs([iview.all, 1000],[iview.Floor,ItemType.Floor],[iview.Wall,ItemType.Wall],[iview.Decoration,ItemType.Decoration],[iview.DecorOnWall,ItemType.DecorOnWall],[iview.Door,ItemType.Door]);	
						centreTab.addEventListener(Event.SELECT, centretab_select);    
						centreTab.select(0);					

					break;						
			}
			
		}
		
		private function centretab_select(e:Event = null):void 
		{
            data = DataManager.getInstance().itemData.getItemListByType(type);			
			
            switch((e.target as TabelView).selectValue)	
			{
			    case 1000:
					list.setData(data);
					return;
					break;							
			}
			var data2:Array = new Array();
			
			for (var i:int = 0; i < data.length; i++ )
			{
				if ((data[i] as ItemVo).base.type2 == (e.target as TabelView).selectValue )
				{
					data2.push(data[i])
				}
			}
			
			list.setData(data2);
		}
		
		private function close():void
		{
			closeMe(true);
			EventManager.getInstance().dispatchEvent(new StorageEvent(StorageEvent.CLOSEMOUDLE));			
		}	
		
		
		
	}

}