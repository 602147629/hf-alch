package happymagic.illustratedHandbook.model.view
{
	import adobe.utils.CustomActions;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import flash.text.TextFormat;
	import happyfish.display.ui.TabelView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happyfish.utils.display.McShower;
	import happymagic.illustratedHandbook.event.IllustratedHandbookEvent;
	import happymagic.illustratedHandbook.model.control.QuickJoinControl;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.classVo.IllustrationsClassVo;
	import happymagic.model.vo.ItemType;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class IllustratedHandbookView extends UISprite
	{
		private var iview:IllustratedHandbookViewUI;
		private var topTab:TabelView; 
		private var list:IllustratedHandbookListView;
		private var fisrt:Boolean = true;
		private var data:Array = new Array();
		private var thisSelectValue:int;
		public function IllustratedHandbookView()
		{
			_view = new IllustratedHandbookViewUI();
			iview = _view as IllustratedHandbookViewUI;
			iview.addEventListener(MouseEvent.CLICK, clickrun);

			topTab = new TabelView();
			iview.addChild(topTab);
			topTab.btwY = 5;
			topTab.x = -140;
			topTab.y = -110;
			topTab.setTabs([iview.Furnace, ItemType.Furnace],[iview.Stuff, ItemType.Stuff], [iview.Goods, ItemType.Goods], [iview.Equipment, ItemType.Equipment], [iview.Scroll, ItemType.Scroll], [iview.Mob, ItemType.Mob],[iview.Decor,ItemType.Decor]);
			topTab.addEventListener(Event.SELECT, tab_select);    
			topTab.select(0);
			
		    list = new IllustratedHandbookListView(new IllustratedHandbookListViewUi1(), iview, 5, true, false);
			list.init(245, 200, 236.65, 40, 0,0);
			list.setGridItem(IllustratedHandbookItemView, IllustratedHandbookItemViewUi);
			list.x = 20;
			list.y = -85;
			list.tweenTime = 0;
			
			EventManager.getInstance().addEventListener(IllustratedHandbookEvent.ILLUSTRATEDHANDBOOKVIEWCHANGECOMPLETE, illustratedhandbookviewchangecomplete); 
			EventManager.getInstance().addEventListener(IllustratedHandbookEvent.ILLUSTRATEDHANDBOOKVIEWCHANGE, illustratedhandbookviewchange); 			
		}
		
		private function illustratedhandbookviewchange(e:IllustratedHandbookEvent):void 
		{
			
		}
		
		private function illustratedhandbookviewchangecomplete(e:IllustratedHandbookEvent):void 
		{
			
		}
		
		private function tab_select(e:Event):void 
		{
			var illustratedHandbookHomepageView:IllustratedHandbookHomepageView;
			var temp:Array = new Array();
			
			thisSelectValue = (e.target as TabelView).selectValue;
			
            switch((e.target as TabelView).selectValue)	
			{
				case ItemType.Furnace:
					     if (fisrt)
						 {
							 fisrt = false;
							 return
						 }
			             iview.mouseChildren = false;
			             iview.mouseEnabled = false;						 
						 pagingPlayer();
					break;
					
				case ItemType.Stuff:
                    list.setData(data, "type",(e.target as TabelView).selectValue);
					break;
					
				case ItemType.Goods:
                    list.setData(data, "type",(e.target as TabelView).selectValue);
					break;
					
				case ItemType.Equipment:
                    list.setData(data, "type",(e.target as TabelView).selectValue);
					break;
					
				case ItemType.Scroll:
                    list.setData(data, "type",(e.target as TabelView).selectValue);
					break;
					
				case ItemType.Mob:
					list.setData(temp);
			        iview.mouseChildren = false;
			        iview.mouseEnabled = false;					
                    pagingPlayer();
					
					break;	
					
				case ItemType.Decor:
                    list.setData(data, "type",(e.target as TabelView).selectValue);
					break;						
			}
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "closebtn":
			           close();
					break;
					
			    case "look":
					  new QuickJoinControl(iview.inout.text);
					break;
				
				
			}
		}
		
		public function setData():void
		{
			var i:int = 0;
			var temp:int;
            var vo:IllustrationsClassVo;
			var textmat:TextFormat;
			data = new IllustratedHandbookUpdata().getdata();
			var arrtempinit1:Array = new Array();
			var arrtempinit2:Array = DataManager.getInstance().illustratedData.illustratedHandbookInit;
			
			for (i  = 0; i < arrtempinit2.length; i++ )
			{
				vo = DataManager.getInstance().illustratedData.getIllustrationsClassVo(arrtempinit2[i].cid);
				if (vo.type == ItemType.Furnace)
				{
					arrtempinit1.push(vo);
				}
			}
			
			var arrtempstatic1:Array = new Array();
			var arrtempstatic2:Array = DataManager.getInstance().illustratedData.illustratedHandbookStatic;			
			
			for (i  = 0; i < arrtempstatic2.length; i++ )
			{
				if (arrtempstatic2[i].type == ItemType.Furnace)
				{
					arrtempstatic1.push(vo);
				}
			}			
			
			iview.number1.text = arrtempinit1.length.toString();
            iview.number11.text = arrtempstatic1.length.toString();
			
			arrtempinit1 = new Array();
			arrtempstatic1 = new Array();	
			
			for (i  = 0; i < arrtempinit2.length; i++ )
			{
				vo = DataManager.getInstance().illustratedData.getIllustrationsClassVo(arrtempinit2[i].cid);
				if (vo.type == ItemType.Stuff)
				{
					arrtempinit1.push(vo);
				}
			}

			for (i = 0; i < arrtempstatic2.length; i++ )
			{
				if (arrtempstatic2[i].type == ItemType.Stuff)
				{
					arrtempstatic1.push(vo);
				}
			}			
			
			iview.number2.text = arrtempinit1.length.toString();
            iview.number22.text = arrtempstatic1.length.toString();
			arrtempinit1 = new Array();
			arrtempstatic1 = new Array();	
			
			for (i = 0; i < arrtempinit2.length; i++ )
			{
				vo = DataManager.getInstance().illustratedData.getIllustrationsClassVo(arrtempinit2[i].cid);
				if (vo.type == ItemType.Goods)
				{
					arrtempinit1.push(vo);
				}
			}

			for (i = 0; i < arrtempstatic2.length; i++ )
			{
				if (arrtempstatic2[i].type == ItemType.Goods)
				{
					arrtempstatic1.push(vo);
				}
			}			
			
			iview.number3.text = arrtempinit1.length.toString();
            iview.number33.text = arrtempstatic1.length.toString();			
			
			arrtempinit1 = new Array();
			arrtempstatic1 = new Array();	
			
			for (i = 0; i < arrtempinit2.length; i++ )
			{
				vo = DataManager.getInstance().illustratedData.getIllustrationsClassVo(arrtempinit2[i].cid);
				if (vo.type == ItemType.Equipment)
				{
					arrtempinit1.push(vo);
				}
			}

			for (i = 0; i < arrtempstatic2.length; i++ )
			{
				if (arrtempstatic2[i].type == ItemType.Equipment)
				{
					arrtempstatic1.push(vo);
				}
			}			
			
			iview.number4.text = arrtempinit1.length.toString();
            iview.number44.text = arrtempstatic1.length.toString();			
			
			arrtempinit1 = new Array();
			arrtempstatic1 = new Array();	
			
			for (i = 0; i < arrtempinit2.length; i++ )
			{
				vo = DataManager.getInstance().illustratedData.getIllustrationsClassVo(arrtempinit2[i].cid);
				if (vo.type == ItemType.Scroll)
				{
					arrtempinit1.push(vo);
				}
			}

			for (i = 0; i < arrtempstatic2.length; i++ )
			{
				if (arrtempstatic2[i].type == ItemType.Scroll)
				{
					arrtempstatic1.push(vo);
				}
			}			
			
			iview.number5.text = arrtempinit1.length.toString();
            iview.number55.text = arrtempstatic1.length.toString();		
			
			arrtempinit1 = new Array();
			arrtempstatic1 = new Array();	
			
			for (i = 0; i < arrtempinit2.length; i++ )
			{
				vo = DataManager.getInstance().illustratedData.getIllustrationsClassVo(arrtempinit2[i].cid);
				if (vo.type == ItemType.Mob)
				{
					arrtempinit1.push(vo);
				}
			}

			for (i = 0; i < arrtempstatic2.length; i++ )
			{
				if (arrtempstatic2[i].type == ItemType.Mob)
				{
					arrtempstatic1.push(vo);
				}
			}			
			
			iview.number6.text = arrtempinit1.length.toString();
            iview.number66.text = arrtempstatic1.length.toString();
			
			arrtempinit1 = new Array();
			arrtempstatic1 = new Array();	
			
			for (i = 0; i < arrtempinit2.length; i++ )
			{
				vo = DataManager.getInstance().illustratedData.getIllustrationsClassVo(arrtempinit2[i].cid);
				if (vo.type == ItemType.Decor)
				{
					arrtempinit1.push(vo);
				}
			}

			for (i = 0; i < arrtempstatic2.length; i++ )
			{
				if (arrtempstatic2[i].type == ItemType.Decor)
				{
					arrtempstatic1.push(vo);
				}
			}			
			
			iview.number7.text = arrtempinit1.length.toString();
            iview.number77.text = arrtempstatic1.length.toString();	
			
			
			
			
		}
		
		public function close():void
		{
			EventManager.getInstance().addEventListener(IllustratedHandbookEvent.ILLUSTRATEDHANDBOOKVIEWCHANGECOMPLETE, illustratedhandbookviewchangecomplete); 
			EventManager.getInstance().addEventListener(IllustratedHandbookEvent.ILLUSTRATEDHANDBOOKVIEWCHANGE, illustratedhandbookviewchange); 			
			closeMe(true);
			EventManager.getInstance().dispatchEvent(new IllustratedHandbookEvent(IllustratedHandbookEvent.ILLUSTRATEMODULECLOSE));			
		}
		
		private function pagingPlayer():void
		{
			var flashMv:McShower = new McShower(Illustratedhandbookleft, iview, null, null,mcComplete);
			flashMv.setMcScaleXY(1.0, 1.0);
			flashMv.x = -277.05;
			flashMv.y = -376.35;			
						
		}
		
		private function mcComplete():void 
		{
			iview.mouseChildren = true;
			iview.mouseEnabled = true;
			var illustratedHandbookHomepageView:IllustratedHandbookHomepageView;
			var temp:Array = new Array();
            switch(thisSelectValue)	
			{
				case ItemType.Furnace:
					     if (fisrt)
						 {
							 fisrt = false;
							 return
						 }
					     list.setData(temp);
			             illustratedHandbookHomepageView = DisplayManager.uiSprite.addModule(IllustratedHandbookDictionary.MODULE_ILLUSTRATEDHANDBOOKHOMEPAGEVIEW, 
			                                        IllustratedHandbookDictionary.MODULE_ILLUSTRATEDHANDBOOKHOMEPAGEVIEW_CLASS, false, AlginType.CENTER, 0, -5,0,0,"fromCenter",0) as IllustratedHandbookHomepageView;
			             illustratedHandbookHomepageView.setData(41);
			             //DisplayManager.uiSprite.setBg(illustratedHandbookHomepageView);
					break;
					
				case ItemType.Mob:
			        illustratedHandbookHomepageView = DisplayManager.uiSprite.addModule(IllustratedHandbookDictionary.MODULE_ILLUSTRATEDHANDBOOKHOMEPAGEVIEW, 
			                                        IllustratedHandbookDictionary.MODULE_ILLUSTRATEDHANDBOOKHOMEPAGEVIEW_CLASS, false, AlginType.CENTER, 0, -5,0,0,"fromCenter",0) as IllustratedHandbookHomepageView;
			        illustratedHandbookHomepageView.setData(71);
			        //DisplayManager.uiSprite.setBg(illustratedHandbookHomepageView);
					
					break;
			}		
		}
	}

}