package happymagic.display.view.promptFrame 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.UseItemCommand;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	import happymagic.model.vo.ItemVo;
	/**
	 * ...
	 * @author ZC
	 */
	//需要更多的体力
	public class NeedMorePhysicalStrengthView extends UISprite
	{
		
		private var iview:NeedMorePhysicalStrengthViewUi;
		
		public function NeedMorePhysicalStrengthView() 
		{
			_view = new NeedMorePhysicalStrengthViewUi();
			
			iview = _view as NeedMorePhysicalStrengthViewUi;
			
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			setData();
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			var command:UseItemCommand;
			command = new UseItemCommand();
            var itemvo:BaseItemClassVo;
			
            var moduleVo:ModuleVo = DataManager.getInstance().getModuleVo("buyItemPopUp");		
			switch(e.target.name)
			{
				case "closebtn":
					closeMe(true);
					break;
					
	            case "askfor":
                    EventManager.getInstance().dispatchEvent(new Event("giftActEventStart"));					 
					break;
					
	            case "usebtn1":
					  command.useItem(DataManager.getInstance().gameSetting.spaddItemIdArray[0]);
					  command.addEventListener(Event.COMPLETE, commandcomplete);
					break;					
					
	            case "usebtn2":
					  command.useItem(DataManager.getInstance().gameSetting.spaddItemIdArray[1]);
					  command.addEventListener(Event.COMPLETE, commandcomplete);
					break;					
					
	            case "usebtn3":
					  command.useItem(DataManager.getInstance().gameSetting.spaddItemIdArray[2]);
					  command.addEventListener(Event.COMPLETE, commandcomplete);
					break;
					
	            case "usebtn4":
					  command.useItem(DataManager.getInstance().gameSetting.spaddItemIdArray[3]);
					  command.addEventListener(Event.COMPLETE, commandcomplete);
					break;					
					
	            case "buybtn1":
					  itemvo = DataManager.getInstance().itemData.getItemClass(DataManager.getInstance().gameSetting.spaddItemIdArray[0]);
					  DisplayManager.uiSprite.addModuleByVo(moduleVo)["setData"](itemvo);	
					break;					
					
	            case "buybtn2":
					  itemvo = DataManager.getInstance().itemData.getItemClass(DataManager.getInstance().gameSetting.spaddItemIdArray[1]);
					  DisplayManager.uiSprite.addModuleByVo(moduleVo)["setData"](itemvo);	
					break;					
					
	            case "buybtn3":
					  itemvo = DataManager.getInstance().itemData.getItemClass(DataManager.getInstance().gameSetting.spaddItemIdArray[2]);
					  DisplayManager.uiSprite.addModuleByVo(moduleVo)["setData"](itemvo);
					break;					
					
	            case "buybtn4":
					  itemvo = DataManager.getInstance().itemData.getItemClass(DataManager.getInstance().gameSetting.spaddItemIdArray[3]);
					  DisplayManager.uiSprite.addModuleByVo(moduleVo)["setData"](itemvo);	
					break;						
									
			}
		}
		
		private function commandcomplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, commandcomplete);
			setData();
		}
		
		public function setData():void
		{
			var arr:Array = DataManager.getInstance().gameSetting.spaddItemIdArray;
			var itemvo1:ItemVo;
			var itemvo2:ItemVo;
			var itemvo3:ItemVo;
			var itemvo4:ItemVo;	
			
			itemvo1 = DataManager.getInstance().itemData.getItem(String(DataManager.getInstance().gameSetting.spaddItemIdArray[0]));
			if (itemvo1)
			{
				iview.num1.text = itemvo1.num.toString();
			}
			else
			{
				iview.num1.text = "0";
			}
			
			itemvo2 = DataManager.getInstance().itemData.getItem(String(DataManager.getInstance().gameSetting.spaddItemIdArray[1]));
			if (itemvo2)
			{
				iview.num2.text = itemvo2.num.toString();
			}
			else
			{
				iview.num2.text = "0";
			}
			
			itemvo3 = DataManager.getInstance().itemData.getItem(String(DataManager.getInstance().gameSetting.spaddItemIdArray[2]));
			if (itemvo3)
			{
				iview.num3.text = itemvo3.num.toString();
			}
			else
			{
				iview.num3.text = "0";
			}			
			
			itemvo4 = DataManager.getInstance().itemData.getItem(String(DataManager.getInstance().gameSetting.spaddItemIdArray[3]));
			if (itemvo4)
			{
				iview.num4.text = itemvo4.num.toString();
			}
			else
			{
				iview.num4.text = "0";
			}			
			
			
			if (int(iview.num1.text.toString()))
			{
				iview.buybtn1.visible = false;
				iview.usebtn1.visible = true;
			}
			else
			{
				iview.buybtn1.visible = true;
				iview.usebtn1.visible = false;				
			}
			
			if (int(iview.num2.text.toString()))
			{
				iview.buybtn2.visible = false;
				iview.usebtn2.visible = true;
			}
			else
			{
				iview.buybtn2.visible = true;
				iview.usebtn2.visible = false;				
			}			
			
			if (int(iview.num3.text.toString()))
			{
				iview.buybtn3.visible = false;
				iview.usebtn3.visible = true;
			}
			else
			{
				iview.buybtn3.visible = true;
				iview.usebtn3.visible = false;				
			}			
			
			if (int(iview.num4.text.toString()))
			{
				iview.buybtn4.visible = false;
				iview.usebtn4.visible = true;
			}
			else
			{
				iview.buybtn4.visible = true;
				iview.usebtn4.visible = false;				
			}			
			
		}
		
	}

}