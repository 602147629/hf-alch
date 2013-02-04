package happymagic.illustratedHandbook.model.view 
{
	import com.greensock.loading.data.VideoLoaderVars;
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import happyfish.display.ui.GridItem;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.module.ModuleManager;
	import happyfish.utils.display.McShower;
	import happymagic.illustratedHandbook.event.IllustratedHandbookEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.classVo.IllustrationsClassVo;
	import happymagic.model.vo.ItemType;
	/**
	 * ...
	 * @author ZC
	 */
	public class IllustratedHandbookItemView extends GridItem
	{
		private var iview:IllustratedHandbookItemViewUi;
		private var data:IllustrationsClassVo;
		
		public function IllustratedHandbookItemView(_uiview:MovieClip) 
		{
			super(_uiview);
			iview = _uiview as IllustratedHandbookItemViewUi;	
			
			iview.mouseChildren = true;
			iview.mouseEnabled = true;
			iview.buttonMode = true;
			iview.addEventListener(MouseEvent.CLICK, clickrun);
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			iview.mouseChildren = false;
			iview.mouseEnabled = false;
			EventManager.getInstance().dispatchEvent(new IllustratedHandbookEvent(IllustratedHandbookEvent.ILLUSTRATEDHANDBOOKVIEWCHANGE)); 
			pagingPlayer();
		}
		
		override public function setData(_data:Object):void
		{
			data = _data as IllustrationsClassVo;
			
			switch(data.type2)
			{
				case ItemType.Drink:
					 iview.nametxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword1");
					break
					
				case ItemType.Food:
					 iview.nametxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword2");
					break					
					
				case ItemType.Tool:
					 iview.nametxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword3");
					break					
					
				case ItemType.Atk:
					 iview.nametxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword4");
					break					
					
				case ItemType.Merchandise:
					 iview.nametxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword5");
					break					
					
				case ItemType.Mix:
					 iview.nametxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword6");
					break					
					
				case ItemType.Skill:
					 iview.nametxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword7");
					break					
					
				case ItemType.Plant:
					 iview.nametxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword8");
					break					
					
				case ItemType.Ore:
					 iview.nametxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword9");
					break					
					
				case ItemType.Animal:
					 iview.nametxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword10");
					break					
					
				case ItemType.SpecialStuff:
					 iview.nametxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword11");
					break					
					
				case ItemType.Floor:
					 iview.nametxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword12");
					break					

				case ItemType.Wall:
					 iview.nametxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword13");
					break					
					
				case ItemType.Decoration:
					 iview.nametxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword14");
					break					
					
				case ItemType.DecorOnWall:
					 iview.nametxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword15");
					break					
					
				case ItemType.Door:
					 iview.nametxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword16");
					break					
					
				case ItemType.Weapon:
					 iview.nametxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword17");
					break					
					
				case ItemType.Armor:
					 iview.nametxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword18");
					break	
					
				case ItemType.Other:
					 iview.nametxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword19");
					break					
					
				case ItemType.Ornament:
					 iview.nametxt.text = LocaleWords.getInstance().getWord("IllustratedHandbookword20");
					break						
			}
			
			iview.nametxt.mouseEnabled = false;
			
			var arrtempinit2:Array = DataManager.getInstance().illustratedData.illustratedHandbookInit;
			var arrtempinit1:Array = new Array();
			var arrtempstatic1:Array = new Array();	
			var arrtempstatic2:Array = DataManager.getInstance().illustratedData.illustratedHandbookStatic;	
			var vo:IllustrationsClassVo;
			
			for (var i:int  = 0; i < arrtempinit2.length; i++ )
			{
				vo = DataManager.getInstance().illustratedData.getIllustrationsClassVo(arrtempinit2[i].cid);
				if (vo.type2 == data.type2)
				{
					arrtempinit1.push(vo);
				}
			}		
			
			for (i = 0; i < arrtempstatic2.length; i++ )
			{
				if (arrtempstatic2[i].type2 == data.type2)
				{
					arrtempstatic1.push(vo);
				}
			}		
			
			iview.number1.text = arrtempinit1.length.toString();
            iview.number2.text = arrtempstatic1.length.toString();
			
		}
		
		private function pagingPlayer():void
		{
			var flashMv:McShower = new McShower(Illustratedhandbookleft, ModuleManager.getInstance().getModule("IllustratedHandbookView").view, null, null,showview);
			flashMv.setMcScaleXY(1.0, 1.0);
			flashMv.x = -277.05;
			flashMv.y = -376.35;	
			
		}		
		
		private function showview():void
		{
			iview.mouseChildren = true;
			iview.mouseEnabled = true;			
			var illustratedHandbookHomepageView:IllustratedHandbookHomepageView = DisplayManager.uiSprite.addModule(IllustratedHandbookDictionary.MODULE_ILLUSTRATEDHANDBOOKHOMEPAGEVIEW, 
			                                        IllustratedHandbookDictionary.MODULE_ILLUSTRATEDHANDBOOKHOMEPAGEVIEW_CLASS, false, AlginType.CENTER, 0, -5,0,0,"fromCenter",0) as IllustratedHandbookHomepageView;
			illustratedHandbookHomepageView.setData(data.type2);
			//DisplayManager.uiSprite.setBg(illustratedHandbookHomepageView);	
			
			EventManager.getInstance().dispatchEvent(new IllustratedHandbookEvent(IllustratedHandbookEvent.ILLUSTRATEDHANDBOOKVIEWCHANGECOMPLETE)); 	
		}
		
		
	}

}