package happymagic.view 
{
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.filters.GlowFilter;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	import happyfish.manager.EventManager;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.event.RoleWorkEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.RoleWorkStartWorkCommand;
	import happymagic.model.vo.RoleVo;
	import happymagic.model.vo.RoleWorkVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class RoleWorkItem extends GridItem
	{
		private var iview:RoleWorkItemUi;
		private var data:RoleVo;
		private var data2:RoleWorkVo;
		
		public function RoleWorkItem(_uiview:MovieClip) 
		{
			super(_uiview)
			iview = _uiview as RoleWorkItemUi;
			iview.mouseChildren = true;
			iview.mouseEnabled = true;
			
			iview.working.visible = false;
			iview.RoleWorkTips.visible = false;
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			iview.addEventListener(MouseEvent.MOUSE_OVER, clickover);
			iview.addEventListener(MouseEvent.MOUSE_OUT, clickout);
		}
		
		private function clickover(e:MouseEvent):void 
		{
			iview.RoleWorkTips.visible = true;
		}
		
		private function clickout(e:MouseEvent):void 
		{
			iview.RoleWorkTips.visible = false;
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "selectbtn":
					var arr :Array = DataManager.getInstance().getVar("selectroleworkarray");
					
					DataManager.getInstance().setVar("selectroleworkarray", arr);
					
					arr.push(data.id);
					iview.selectbtn.visible = false;
					iview.filters = [new GlowFilter(0xFF6600, 1, 10, 10, 2.5, 1, false, false)];
					
					if (arr.length  < DataManager.getInstance().getVar("roleworknum"))
					{

					}
					else
					{
						if (!DataManager.getInstance().getVar("RoleWorkrequest"))
						{
							var id:int = DataManager.getInstance().getVar("roleworklevel").id;
							DataManager.getInstance().setVar("RoleWorkrequest", true);
							var roleWorkStartWorkCommand:RoleWorkStartWorkCommand = new RoleWorkStartWorkCommand();
							roleWorkStartWorkCommand.setData(id, arr.join(","));
							roleWorkStartWorkCommand.addEventListener(Event.COMPLETE, roleWorkStartWorkCommandComplete);							
						}
						
						DataManager.getInstance().setVar("RoleWorkrequest", true);
					}
					break;
			}
		}
		
		private function roleWorkStartWorkCommandComplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, roleWorkStartWorkCommandComplete);
			
			if (e.target.objdata.result.status < 0)
			{
				DisplayManager.showNeedMorePhysicalStrengthView();
			}
			
			EventManager.getInstance().dispatchEvent(new RoleWorkEvent(RoleWorkEvent.ROLEWORKSELECTCLOSE));
			
		}
	    
		override public function setData(value:Object):void 
		{
			
	        TextFieldUtil.autoSetTxtDefaultFormat(iview);		
			if (value is RoleWorkVo)
			{
				data2 = value as RoleWorkVo;
				
				data = DataManager.getInstance().roleData.getRole(data2.id);
				iview.noenoughMc.visible = false;
				iview.selectbtn.visible = false;
				iview.working.visible  = true;
			}
			
			if (value is RoleVo)
			{
				data = value as RoleVo;
				if (DataManager.getInstance().getVar("roleworklevel").roleLevel > data.level)
				{
					iview.noenoughMc.visible = true;
					iview.selectbtn.visible = false;
				}
				else
				{
					iview.noenoughMc.visible = false;
					iview.selectbtn.visible = true;				
				}
			
				if (data.work)
				{
					iview.noenoughMc.visible = false;
					iview.selectbtn.visible = false;
					iview.working.visible = true;
				}					
			}
			
			iview.leveltxt.text = data.level.toString();
			
			iview.RoleWorkTips["rolename"].text = data.name;
			iview.RoleWorkTips["leveltxt"].text = data.level.toString();
			iview.RoleWorkTips["starLevel"].gotoAndStop(data.quality);
			iview.RoleWorkTips["jobIcon"].gotoAndStop(data.profession);
			iview.RoleWorkTips["propIcon"].gotoAndStop(data.prop);				

			
			loadIcon();
			
			DataManager.getInstance().setVar("RoleWorkrequest", false);
		}
		
		private function loadIcon():void 
		{
			var icon:IconView = new IconView(35, 35, new Rectangle(4, 5, 35, 35));
			icon.setData(data.className);
			iview.addChild(icon);					
		}
	}

}