package happymagic.illustratedHandbook.model.view 
{
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.filters.GlowFilter;
	import flash.geom.Rectangle;
	import flash.text.TextFormat;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	import happyfish.manager.EventManager;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.display.view.mainMenu.MainMenuView;
	import happymagic.display.view.ui.ItemIconView;
	import happymagic.illustratedHandbook.event.IllustratedHandbookEvent;
	import happymagic.illustratedHandbook.model.command.IllustratedHandbookIsNewCommand;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.classVo.IllustrationsClassVo;
	import happymagic.model.vo.IllustrationsVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class IllustratedHandbookHomepageItemView extends GridItem
	{
		private var iview:IllustratedHandbookHomepageItemViewUi
		public var data:IllustrationsClassVo;
		private var color:Array;
		private var initdata:IllustrationsVo;
		public function IllustratedHandbookHomepageItemView(_uiview:MovieClip) 
		{
			super(_uiview);
			iview = _uiview as IllustratedHandbookHomepageItemViewUi;
			
			iview.mouseChildren = true;
			iview.mouseEnabled = true;
			
			iview.select.visible = false;
			iview.unselect.visible = false;
			iview.addEventListener(MouseEvent.CLICK, mouseclick);
			
			EventManager.getInstance().addEventListener(IllustratedHandbookEvent.ILLUSTRATESELECT,illustrateselect); 
		}
		
		private function illustrateselect(e:IllustratedHandbookEvent):void 
		{
			if ((e.obj as IllustrationsClassVo).cid == data.cid)
			{
				iview.filters = [new GlowFilter(0xFF6600, 1, 10, 10, 2.5, 1, false, false)];
			    iview.select.visible = true;
			    iview.unselect.visible = false;				
			}
			else
			{
			    iview.select.visible = false;
			    iview.unselect.visible = true;					
				iview.filters = color;
			}
			
			
		}
		
		private function mouseclick(e:MouseEvent):void 
		{
			if (initdata.isNew)
			{
				var commandIllustratedHandbook:IllustratedHandbookIsNewCommand = new IllustratedHandbookIsNewCommand();
				commandIllustratedHandbook.setData(initdata.cid);
				commandIllustratedHandbook.addEventListener(Event.COMPLETE,commandIllustratedHandbookcomplete);
			}
			else
			{
				EventManager.getInstance().dispatchEvent(new IllustratedHandbookEvent(IllustratedHandbookEvent.ILLUSTRATESELECT, data)); 
			}
		}
		
		private function commandIllustratedHandbookcomplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, commandIllustratedHandbookcomplete);
			
			initdata.isNew = false;
			iview.isnew.visible = false;
			
            EventManager.getInstance().dispatchEvent(new IllustratedHandbookEvent(IllustratedHandbookEvent.ILLUSTRATESELECT, data)); 
			
			if (!DataManager.getInstance().illustratedData.newIllustratedArr.length)
			{
				var moudleview:MainMenuView = DisplayManager.uiSprite.getModule("menu") as MainMenuView;
				moudleview.illusIcon.enabled = false;
				DataManager.getInstance().isNewIllus = 0;				
			}			
		}
	
		override public function setData(_data:Object):void
		{
			data = _data as IllustrationsClassVo;
			
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			
		    initdata = DataManager.getInstance().illustratedData.getIllustrationsVo(data.cid);
			if (initdata.isNew)
			{
				iview.isnew.visible = true;
			}
			else
			{
				iview.isnew.visible = false;
			}
			
			
			iview.cid.text = "NO" + data.cid;

			if (data.itemCid)
			{
				iview.num.text = String(DataManager.getInstance().itemData.getItemCount(int(data.itemCid)));
			}
			
			color = iview.filters;
			
			if (DataManager.getInstance().getVar("HandBookSelect") == data.cid)
			{
				iview.filters = [new GlowFilter(0xFF6600, 1, 10, 10, 2.5, 1, false, false)];
			    iview.select.visible = true;
			    iview.unselect.visible = false;					
			}
			else
			{
				iview.filters = color;
			    iview.select.visible = false;
			    iview.unselect.visible = true;					
			}
			
			loadicon();
		}
		
		private function loadicon():void
		{
			var itemicon:IconView = new IconView(40, 40, new Rectangle(14, 22, 40, 40));
			itemicon.setData(data.className);
			iview.addChild(itemicon);			
		}		
		
		
	}

}