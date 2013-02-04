package model.view 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.view.IconView;
	import happyfish.display.view.PerBarView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.utils.display.TextFieldUtil;
	import happyfish.utils.HtmlTextTools;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.RoleVo;
	import model.command.StrengThenRequestCommand;
	import model.command.StrengThenSaveAttributeCommand;
	/**
	 * ...
	 * @author ZC
	 */
	
	public class StrengThenView extends UISprite
	{
		private var iview:StrengThenViewUi;
		private var list:StrengThenListView;
		private var select:int;
		private var role:RoleVo; //显示的角色数据
		private var newrole:RoleVo;
		private var icon:IconView;
		private var expPer:PerBarView;
		
		public function StrengThenView() 
		{
			_view = new StrengThenViewUi();
			iview = _view as StrengThenViewUi;
			
			
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			
		    list = new StrengThenListView(new StrengThenListViewUi(), iview, 4, true, false);
			list.init(400, 150, 150, 32, -130,70);
			list.setGridItem(StrengThenItemView, StrengThenItemViewUi);
			list.x = 0;
			list.y = 0;
			list.tweenTime = 0; 
			
			EventManager.getInstance().addEventListener("curTrainRoleChange", curTrainRoleChange);
			
			DataManager.getInstance().setVar("StrengThenSelectId", 1);

		}
		
		private function curTrainRoleChange(e:Event):void 
		{
			if (DataManager.getInstance().getVar("curTrainRole"))
			{
				setData();
			}
			else
			{
				close();
			}
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "closebtn":					
					close();
					break;
					
				case "peiyang":
					iview.mouseChildren = false;
					iview.mouseEnabled = false;
					
					var strengThenRequestCommand:StrengThenRequestCommand = new StrengThenRequestCommand();					
					strengThenRequestCommand.setData(DataManager.getInstance().getVar("StrengThenSelectId"),role.id);
					strengThenRequestCommand.addEventListener(Event.COMPLETE, strengThenRequestCommandComplete);
					
					break;
					
				case "save":
					iview.mouseChildren = false;
					iview.mouseEnabled = false;
					
					var strengThenSaveAttributeCommand:StrengThenSaveAttributeCommand = new StrengThenSaveAttributeCommand();
					strengThenSaveAttributeCommand.setData(role.id);
					strengThenSaveAttributeCommand.addEventListener(Event.COMPLETE, strengThenSaveAttributeCommandComplete);
					break;
					
				case "esc":
					setData();
					break;
			}
		}
		
		private function strengThenSaveAttributeCommandComplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, strengThenSaveAttributeCommandComplete);	
			
			iview.save.visible = false;
			iview.peiyang.visible = true;			
			iview.esc.visible = false;
			
			iview.mouseChildren = true;
			iview.mouseEnabled = true;

			setData()
		}
		
		private function strengThenRequestCommandComplete(e:Event):void 
		{
		    e.target.removeEventListener(Event.COMPLETE, strengThenRequestCommandComplete);	
			
			iview.save.visible = true;
			iview.peiyang.visible = false;
			iview.esc.visible = true;
			
			iview.mouseChildren = true;
			iview.mouseEnabled = true;
			
			if (e.target.objdata.result.status == 1)
			{
				newrole = DataManager.getInstance().getVar("newstrengthenvo");
			
				iview.icon1.visible = true;
				iview.icon2.visible = true;
				iview.icon3.visible = true;
				iview.icon4.visible = true;
				iview.icon5.visible = true;
			
				if (newrole.sPhyAtk > role.sPhyAtk)
				{
					iview.newone1.htmlText = HtmlTextTools.greenWords("+" + newrole.sPhyAtk.toString());
					iview.icon1.gotoAndStop(1);
				}
				else if (newrole.sPhyAtk == role.sPhyAtk)
				{
					iview.icon1.visible = false;
					iview.newone1.htmlText = HtmlTextTools.fontWord("+" + newrole.sPhyAtk.toString(),"#341807",12,"Berlin Sans FB Demi");					
				}
				else
				{
					iview.newone1.htmlText = HtmlTextTools.redWords("+" + newrole.sPhyAtk.toString());				
					iview.icon1.gotoAndStop(2);
				}
			
				if (newrole.sPhyDef > role.sPhyDef)
				{
					iview.newone2.htmlText = HtmlTextTools.greenWords("+" + newrole.sPhyDef.toString());
					iview.icon2.gotoAndStop(1);
				}
				else if (newrole.sPhyDef == role.sPhyDef)
				{
					iview.icon2.visible = false;
					iview.newone2.htmlText = HtmlTextTools.fontWord("+" + newrole.sPhyDef.toString(),"#341807",12,"Berlin Sans FB Demi");				
				}				
				else
				{
					iview.newone2.htmlText = HtmlTextTools.redWords("+" + newrole.sPhyDef.toString());
					iview.icon2.gotoAndStop(2);
				}			
			
				if (newrole.sMagAtk > role.sMagAtk)
				{
					iview.newone3.htmlText = HtmlTextTools.greenWords("+" + newrole.sMagAtk.toString());
					iview.icon3.gotoAndStop(1);
				}
				else if (newrole.sMagAtk ==role.sMagAtk)
				{
					iview.icon3.visible = false;
					iview.newone3.htmlText = HtmlTextTools.fontWord("+" + newrole.sMagAtk.toString(),"#341807",12,"Berlin Sans FB Demi");				
				}					
				else
				{
					iview.newone3.htmlText = HtmlTextTools.redWords("+" + newrole.sMagAtk.toString());
					iview.icon3.gotoAndStop(2);
				}			
			
				if (newrole.sMagDef > role.sMagDef)
				{
					iview.newone4.htmlText = HtmlTextTools.greenWords("+" + newrole.sMagDef.toString());
					iview.icon4.gotoAndStop(1);
				}
				else if (newrole.sMagDef == role.sMagDef)
				{
					iview.icon4.visible = false;
					iview.newone4.htmlText = HtmlTextTools.fontWord("+" + newrole.sMagDef.toString(),"#341807",12,"Berlin Sans FB Demi");				
				}					
				else
				{
					iview.newone4.htmlText = HtmlTextTools.redWords("+" + newrole.sMagDef.toString());
					iview.icon4.gotoAndStop(2);
				}
			
				if (newrole.sSpeed > role.sSpeed)
				{
					iview.newone5.htmlText = HtmlTextTools.greenWords("+" + newrole.sSpeed.toString());
					iview.icon5.gotoAndStop(1);
				}
				else if (newrole.sSpeed == role.sSpeed)
				{
					iview.icon5.visible = false;
					iview.newone5.htmlText = HtmlTextTools.fontWord("+" + newrole.sSpeed.toString(),"#341807",12,"Berlin Sans FB Demi");			
				}					
				else
				{
					iview.newone5.htmlText = HtmlTextTools.redWords("+" + newrole.sSpeed.toString());
					iview.icon5.gotoAndStop(2);
				}					
			}
		
			
		}
		
		private function close():void 
		{
			EventManager.getInstance().removeEventListener("curTrainRoleChange", curTrainRoleChange);
			closeMe(true);
			EventManager.getInstance().dispatchEvent(new StrengThenEvent(StrengThenEvent.STRENGTHENCLOSE));			
		}
		
		public function setData():void
		{	
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			
			role = DataManager.getInstance().getVar("curTrainRole");	
			
			var arr1:Array = [1, 2, 3, 4];
			var arr2:Array = [1, 2, 3];
			var arr3:Array = [1, 2];

			if (role.level >= 30)
			{
			    list.setData(arr1);				
			}
			else if (role.level >= 15)
			{
				list.setData(arr2);	
			}
			else
			{
				list.setData(arr3);
			}
			

			
			iview.icon1.visible = false;
			iview.icon2.visible = false;
			iview.icon3.visible = false;
			iview.icon4.visible = false;
			iview.icon5.visible = false;
			
			iview.peiyang.visible  = true;
			iview.newone1.text = "";
			iview.newone2.text = "";
			iview.newone3.text = "";
			iview.newone4.text = "";
			iview.newone5.text = "";
			
			iview.icon1.stop();
			iview.icon2.stop();
			iview.icon3.stop();
			iview.icon4.stop();
			iview.icon5.stop();
			
			iview.save.visible = false;			
			iview.esc.visible = false;
			

			
			iview.pa.text = role.phyAtk.toString();
			iview.pd.text = role.phyDef.toString();
			iview.ma.text = role.magAtk.toString();
			iview.md.text = role.magDef.toString();			
			iview.speed.text = role.speed.toString();
		
			iview.currtentone1.text = "+" + role.sPhyAtk;
			iview.currtentone2.text = "+" + role.sPhyDef;
			iview.currtentone3.text = "+" + role.sMagAtk;
			iview.currtentone4.text = "+" + role.sMagDef;
			iview.currtentone5.text = "+" + role.sSpeed;
			
			iview.exp.text = role.exp + "/" + role.maxExp;
			
			expPer = new PerBarView(iview.expbar, iview.expbar.width);
			
			expPer.maxValue = role.maxExp;
			
			expPer.setData(role.exp);
			
			loadicon(role);
			
			iview.nametxt.text = role.name;
			iview.leveltxt.text = role.level.toString();
			
			iview.jobIcon.gotoAndStop(role.profession);
			iview.propIcon.gotoAndStop(role.prop);
			
		}
		
		private function loadicon(vo:RoleVo):void 
		{
			if (icon)
			{
				iview.removeChild(icon);
			}
			icon = new IconView(70, 70, new Rectangle( -182, -200, 70, 70));
			icon.setData(vo.className);
			iview.addChild(icon);			
		}
		
	}

}