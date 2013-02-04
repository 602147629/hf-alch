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
			iview.num1.text = DataManager.getInstance().itemData.getItem(DataManager.getInstance().gameSetting.spaddItemIdArray[0]).num.toString();
			iview.num2.text = DataManager.getInstance().itemData.getItem(DataManager.getInstance().gameSetting.spaddItemIdArray[1]).num.toString();
			iview.num3.text = DataManager.getInstance().itemData.getItem(DataManager.getInstance().gameSetting.spaddItemIdArray[2]).num.toString();
			iview.num4.text = DataManager.getInstance().itemData.getItem(DataManager.getInstance().gameSetting.spaddItemIdArray[3]).num.toString();
			
			if (DataManager.getInstance().itemData.getItem(DataManager.getInstance().gameSetting.spaddItemIdArray[0]).num)
			{
				iview.buybtn1.visible = false;
				iview.usebtn1.visible = true;
			}
			else
			{
				iview.buybtn1.visible = true;
				iview.usebtn1.visible = false;				
			}
			
			if (DataManager.getInstance().itemData.getItem(DataManager.getInstance().gameSetting.spaddItemIdArray[1]).num)
			{
				iview.buybtn2.visible = false;
				iview.usebtn2.visible = true;
			}
			else
			{
				iview.buybtn2.visible = true;
				iview.usebtn2.visible = false;				
			}			
			
			if (DataManager.getInstance().itemData.getItem(DataManager.getInstance().gameSetting.spaddItemIdArray[2]).num)
			{
				iview.buybtn3.visible = false;
				iview.usebtn3.visible = true;
			}
			else
			{
				iview.buybtn3.visible = true;
				iview.usebtn3.visible = false;				
			}			
			
			if (DataManager.getInstance().itemData.getItem(DataManager.getInstance().gameSetting.spaddItemIdArray[3]).num)
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