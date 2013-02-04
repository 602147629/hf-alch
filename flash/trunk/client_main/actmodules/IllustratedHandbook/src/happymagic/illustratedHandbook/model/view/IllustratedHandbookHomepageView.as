package happymagic.illustratedHandbook.model.view 
{
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import flash.text.TextFormat;
	import happyfish.display.ui.Pagination;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.ModuleManager;
	import happyfish.utils.display.BtnStateControl;
	import happyfish.utils.display.McShower;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.display.view.ui.ItemIconView;
	import happymagic.illustratedHandbook.event.IllustratedHandbookEvent;
	import happymagic.manager.DataManager;
	import happymagic.mix.events.MixEvent;
	import happymagic.model.vo.classVo.IllustrationsClassVo;
	import happymagic.model.vo.ItemType;
	import happymagic.scene.world.grid.item.FurnaceDecor;
	import happymagic.scene.world.MagicWorld;
	/**
	 * ...
	 * @author ZC
	 */
	public class IllustratedHandbookHomepageView extends UISprite
	{
		private var iview:IllustratedHandbookHomepageViewUi;
		private var list:IllustratedHandbookListView;
		private var data:IllustrationsClassVo;
		private var furnace:FurnaceDecor;
		private var itemicon:IconView;
		private var selectbtn:String;
		
		public function IllustratedHandbookHomepageView() 
		{
			_view = new IllustratedHandbookHomepageViewUi();
			iview =  _view as IllustratedHandbookHomepageViewUi;
			
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			
		    list = new IllustratedHandbookListView(new IllustratedHandbookListViewUi1(), iview, 9, true, false);
			var pag:Pagination = new Pagination(50,3);
			pag.x = 75;
			pag.y = 18;
			list.pagination = pag;
			list.init(300, 340, 80, 75, -60, -250);
			list.setGridItem(IllustratedHandbookHomepageItemView, IllustratedHandbookHomepageItemViewUi);
			list.x = -205;
			list.y = 126;
			list.tweenTime = 0;	
			list.setButtonPosition( -10, 15, 150, 15);
	
            EventManager.getInstance().addEventListener(IllustratedHandbookEvent.ILLUSTRATESELECT, illustrateselecet);
 			
			iview.make2.visible = false
			iview.coin.visible = false;
		}
		
		private function illustrateselecet(e:IllustratedHandbookEvent):void 
		{
			data = e.obj as IllustrationsClassVo;
			update();
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			selectbtn = e.target.name;
			switch(e.target.name)
			{
				case "closebtn":					   
					   iview.mouseChildren = false;
					   iview.mouseEnabled = false;
					   pagingPlayer();
					break;
					
				case "closebtn2":
					   iview.mouseChildren = false;
					   iview.mouseEnabled = false;	
					   closeMe(true);
					   if (ModuleManager.getInstance().getModule(IllustratedHandbookDictionary.MODULE_ILLUSTRATEDHANDBOOKVIEW))
					   {
						  (ModuleManager.getInstance().getModule("IllustratedHandbookView") as IllustratedHandbookView).close();
                          EventManager.getInstance().dispatchEvent(new IllustratedHandbookEvent(IllustratedHandbookEvent.ILLUSTRATEMODULECLOSE));						   
					   }	
					   else
					   {
						   EventManager.getInstance().dispatchEvent(new IllustratedHandbookEvent(IllustratedHandbookEvent.ILLUSTRATEMODULECLOSE));	
					   }
					break;					
				
				case "make":
					   EventManager.getInstance().dispatchEvent(new MixEvent(MixEvent.MIX_ITEM, false, false, data.mixCid, furnace.decorVo.id));
					   if (ModuleManager.getInstance().getModule(IllustratedHandbookDictionary.MODULE_ILLUSTRATEDHANDBOOKVIEW))
					   {
					       ModuleManager.getInstance().closeModule(IllustratedHandbookDictionary.MODULE_ILLUSTRATEDHANDBOOKVIEW);						   
					   }


					   closeMe(true);
					break;
			}
		}
		
		
		public function setData(_type2:int,selectcid:int = 0):void
		{			
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			var arr:Array = new Array();
			for (var i:int = 0; i < DataManager.getInstance().illustratedData.illustratedHandbookInit.length; i++ )
			{
				if (DataManager.getInstance().illustratedData.illustratedHandbookInit[i].base.type2 == _type2)
				{
					arr.push(DataManager.getInstance().illustratedData.illustratedHandbookInit[i].base);
				}
			}
			
			arr.sortOn("cid", Array.NUMERIC);
			
			data = arr[0];
			
			if (data)
			{

			   if (selectcid)
			   {
				   data = DataManager.getInstance().illustratedData.getIllustrationsClassVo(DataManager.getInstance().getVar("HandBookSelect"));
			   }
			   else
			   {
			       DataManager.getInstance().setVar("HandBookSelect", data.cid);				   
			   }

			   	update();
			}
			else
			{
			    iview.nametxt.text = "";
			    iview.cid.text = "";
				iview.fromtxt.text = "";
			    iview.content.text = "";
			    iview.source.text = "";				
				iview.type.text = "";
				iview.price.text = "";
				iview.make.visible = false;
			}
			
			switch(_type2)
			{
				case ItemType.Drink:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword1");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword28");
					break
					
				case ItemType.Food:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword2");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword28");
					break					
					
				case ItemType.Tool:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword3");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword28");
					break					
					
				case ItemType.Atk:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword4");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword28");
					break					
					
				case ItemType.Merchandise:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword5");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword28");
					break					
					
				case ItemType.Mix:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword6");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword30");
					break					
					
				case ItemType.Skill:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword7");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword30");
					break					
					
				case ItemType.Plant:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword8");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword27");
					break					
					
				case ItemType.Ore:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword9");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword27");
					break					
					
				case ItemType.Animal:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword10");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword27");
					break					
					
				case ItemType.SpecialStuff:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword11");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword27");
					break					
					
				case ItemType.Floor:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword12");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword32");
					break					

				case ItemType.Wall:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword13");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword32");
					break					
					
				case ItemType.Decoration:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword14");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword32");
					break					
					
				case ItemType.DecorOnWall:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword15");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword32");
					break					
					
				case ItemType.Door:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword16");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword32");
					break					
					
				case ItemType.Weapon:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword17");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword29");
					break					
					
				case ItemType.Armor:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword18");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword29");
					break	
					
				case ItemType.Other:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword19");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword29");
					break					
					
				case ItemType.Ornament:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword20");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword29");
					break
					
				case 41:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword23");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword23");
					break					
					
				case 71:
					 iview.type.text = LocaleWords.getInstance().getWord("IllustratedHandbookword24");
					 iview.itemtype1.text = LocaleWords.getInstance().getWord("IllustratedHandbookword31");
					break								
					
			}	
			
			list.setData(arr);

		}
		
		public function update():void 
		{
			var textmat:TextFormat;
            iview.make2.visible = false;	
			iview.nametxt.text = data.name;

			iview.cid.text = "NO." + data.cid;
			if (data.type == 6)
			{
				iview.fromtxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword21");
			}
			else
			{
				iview.fromtxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword22");			
			}
			
			iview.content.text = data.content;
			iview.source.text = data.source;
			
			if (data.type == 7)
			{
				iview.price.text = "";
				iview.coin.visible = false;				
			}
			else
			{
				if (DataManager.getInstance().itemData.getItemClass(int(data.itemCid)).coin)
				{
					iview.price.text  = LocaleWords.getInstance().getWord("IllustratedHandbookword25") + DataManager.getInstance().itemData.getItemClass(int(data.itemCid)).coin;
					iview.coin.visible = true;
				}
				else
				{
					iview.price.text = "";
					iview.coin.visible = false;
				}				
			}
				loadIcon();
				
			if (data.mixCid)
			{
				iview.make.visible = true;
			}
			else
			{
				iview.make.visible = false;
				return;
			}
			
			furnace = MagicWorld(DataManager.getInstance().worldState.world).getFreeFurnaceByCid(DataManager.getInstance().mixData.getMixClass(data.mixCid).furnaceCid);
			
			if (furnace)
			{
				iview.make2.visible = false;
				iview.make.visible = true;
				BtnStateControl.setBtnState(iview.make2, true);				
				
			}
			else
			{
				iview.make2.visible = true;
				iview.make.visible = false;				
				BtnStateControl.setBtnState(iview.make2, false);				
			}
				
		}
		
		private function loadIcon():void
		{
			if (itemicon)
			{
				iview.removeChild(itemicon);
			}
			itemicon = new IconView(40, 40,new Rectangle(125, -125, 40, 40));
			itemicon.setData(data.className);
			iview.addChild(itemicon);		
		}
		
		private function pagingPlayer():void
		{
			var flashMv:McShower = new McShower(Illustratedhandbookright, iview, null, null,mcComplete);
			flashMv.setMcScaleXY(1.0, 1.0);
			flashMv.x = -428.35;
			flashMv.y = -370.95;
			
		}		
		
		private function mcComplete():void 
		{
			iview.mouseChildren = true;
		    iview.mouseEnabled = true;	
			
			switch(selectbtn)
			{
				case "closebtn":					  					   
					   closeMe(true);
					   if (!ModuleManager.getInstance().getModule(IllustratedHandbookDictionary.MODULE_ILLUSTRATEDHANDBOOKVIEW))
					   {
                          EventManager.getInstance().dispatchEvent(new IllustratedHandbookEvent(IllustratedHandbookEvent.ILLUSTRATEMODULECLOSE));						   
					   }					   					   
					break;
					
				case "closebtn2":
				   					   
					break;	
			}
			
		}
		
	}

}