package happymagic.view 
{
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.event.RoleWorkEvent;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.RoleVo;
	import happymagic.model.vo.RoleWorkPointClassVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class RoleWorkSelectView extends UISprite
	{
		private var iview:RoleWorkSelectViewUi;
		private var list:RoleWorkListView;
		private var data:RoleWorkPointClassVo;
		
		public function RoleWorkSelectView() 
		{
			_view = new RoleWorkSelectViewUi();
			iview = _view as RoleWorkSelectViewUi;
			
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			
		    list = new RoleWorkListView(new RoleWorkListViewUi(), iview, 8, false, false);
			list.init(450, 270, 90, 85, 0,0);
			list.setGridItem(RoleWorkItem, RoleWorkItemUi);
			list.x = -220.55;
			list.y = -120.85;
			list.setButtonPosition(20, 140, 420, 140);
			list.tweenTime = 0;
			
			EventManager.getInstance().addEventListener(RoleWorkEvent.ROLEWORKSELECTCLOSE,roleworkselectclose);
		}
		
		private function roleworkselectclose(e:RoleWorkEvent):void 
		{
			close();
			EventManager.getInstance().dispatchEvent(new RoleWorkEvent(RoleWorkEvent.ROLEUPDATA))
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
		
		private function close():void 
		{			
			EventManager.getInstance().removeEventListener(RoleWorkEvent.ROLEWORKSELECTCLOSE, roleworkselectclose);
			closeMe(true);
		}
		
		public function setData(vo:RoleWorkPointClassVo):void
		{	
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			
			data = vo;

			var arr:Array = new Array();
			DataManager.getInstance().setVar("roleworklevel", vo);
			DataManager.getInstance().setVar("roleworknum", data.roleNum);
			DataManager.getInstance().setVar("selectroleworkarray", arr);
			
			
			var myroles:Array = new Array();
			
			for (var i:int = 0; i < DataManager.getInstance().roleData.getMyRoles().length; i++) 
			{
				myroles.push(DataManager.getInstance().roleData.getMyRoles()[i]);
			}
			
			//排序
			myroles = sort(myroles,vo.roleLevel);
			list.setData(myroles);
			
			iview.num.text = data.roleNum.toString();
		}
		
		private function sort(arr:Array,level:int):Array 
		{
			//第一步 选出未工作的人
			var arrtemp:Array = new Array();
			var arrtemp1:Array = new Array();
			
			for (var i:int = 0; i < arr.length; i++) 
			{
				if (!arr[i].work)
				{
					arrtemp.push(arr[i]);
				}
			}
			
			//第二步 选择符合条件的工作的人
			for (var j:int = 0; j < arrtemp.length; j++) 
			{
				if (arrtemp[j].level >= level)
				{
					arrtemp1.push(arrtemp[j]);
				}				
			}
			
			for (var k:int = 0; k < arrtemp.length; k++) 
			{
				if (arrtemp[k].level < level)
				{
					arrtemp1.push(arrtemp[k]);
				}				
			}			
			
			//将已经工作的人添加进数组			
			for (var l:int = 0; l < arr.length; l++) 
			{
				if (arr[l].work)
				{
					arrtemp1.push(arr[l]);
				}
			}
			
			return arrtemp1;
			
		}
		
	}

}