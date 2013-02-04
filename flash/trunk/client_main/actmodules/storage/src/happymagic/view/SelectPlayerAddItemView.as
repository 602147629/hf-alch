package happymagic.view 
{
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.filters.GlowFilter;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	import happyfish.display.view.PerBarView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.ModuleManager;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.event.StorageEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.StoragItemUseCommand;
	import happymagic.model.vo.ProfessionType;
	import happymagic.model.vo.RoleVo;
	import happymagic.model.vo.StoragItemVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class SelectPlayerAddItemView extends GridItem
	{
		private var iview:SelectPlayerAddItemViewUi;
		private var data:RoleVo;
		private var hpPer:PerBarView;
		private var mpPer:PerBarView;
		private var itemcid:int;
		public function SelectPlayerAddItemView(_uiview:MovieClip) 
		{
			super(_uiview);
			iview = _uiview as SelectPlayerAddItemViewUi;
			
			iview.mouseChildren = true;
			iview.mouseEnabled = true;
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			iview.addEventListener(MouseEvent.MOUSE_OVER, mouseover);			
			iview.addEventListener(MouseEvent.MOUSE_OUT, mouseout);			
		}
		
		private function mouseout(e:MouseEvent):void 
		{
			iview.filters = new Array();
		}
		
		private function mouseover(e:MouseEvent):void 
		{
			iview.filters = [new GlowFilter(0xFF6600, 1, 10, 10, 2.5, 1, false, false)];			
		}
		
		private function clickrun(e:MouseEvent):void 
		{
            iview.mouseChildren = false;
			iview.mouseEnabled = false;
			
			var storagItemUseCommand:StoragItemUseCommand = new StoragItemUseCommand();
			storagItemUseCommand.useItem(DataManager.getInstance().getVar("itemcid"), data.id);
			storagItemUseCommand.addEventListener(Event.COMPLETE, storagItemUseCommandComplete);
		}
		
		private function storagItemUseCommandComplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, storagItemUseCommandComplete);
            iview.mouseChildren = true;
			iview.mouseEnabled = true;	
			
			if (e.target.objdata.result.status == 1)
			{
				var arr:Array = DataManager.getInstance().getVar("conentarray");
			
				var vo:StoragItemVo = arr[0];
				var str:String;
				switch(vo.type)
				{
			    	case 8:
                     	//hp
					 	str = LocaleWords.getInstance().getWord("Storageword8") + data.name + LocaleWords.getInstance().getWord("Storageword9") + vo.num + LocaleWords.getInstance().getWord("Storageword10");
                     	DisplayManager.showPiaoStr(PiaoMsgType.TYPE_GOOD_STRING, str);
					break;
								
					case 9:
                     	//mp
					 	str = LocaleWords.getInstance().getWord("Storageword8") + data.name + LocaleWords.getInstance().getWord("Storageword9") + vo.num + LocaleWords.getInstance().getWord("Storageword11");
                     	DisplayManager.showPiaoStr(PiaoMsgType.TYPE_GOOD_STRING, str);						
					break;										
								
				    case 11:
					    //exp	
					 	str = LocaleWords.getInstance().getWord("Storageword8") + data.name + LocaleWords.getInstance().getWord("Storageword9") + vo.num + LocaleWords.getInstance().getWord("Storageword12");
                     	DisplayManager.showPiaoStr(PiaoMsgType.TYPE_GOOD_STRING, str);					
                    	 						
					break;	
				}
			
				ModuleManager.getInstance().closeModule("SelectPlayerAddView",true);
				EventManager.getInstance().dispatchEvent(new StorageEvent(StorageEvent.USEITEMCOMPLETE));				
			}
		}
		
		override public function setData(obj:Object):void
		{
			data = obj as RoleVo;
			
			iview.nametxt.text = data.name;
			
			iview.level.text = data.level.toString();
			
			iview.hp.text = data.hp + "/" + data.maxHp;
			iview.mp.text = data.mp + "/" + data.maxMp;
			
			hpPer = new PerBarView(iview.hpmc, iview.hpmc.width);
			mpPer = new PerBarView(iview.mpmc, iview.mpmc.width);
			
			
			hpPer.maxValue = data.maxHp;
			mpPer.maxValue = data.maxMp;
			

			iview.jobIcon.gotoAndStop(data.profession);
			iview.propIcon.gotoAndStop(data.prop);
				  
			hpPer.setData(data.hp);
			mpPer.setData(data.mp);
			
			loadIcon()
			
		}
		
		private function loadIcon():void 
		{
			var icon:IconView = new IconView(45, 45, new Rectangle(2.4, 13.8, 45, 45));
			icon.setData(data.className);
			iview.addChild(icon);			
		}
	}

}