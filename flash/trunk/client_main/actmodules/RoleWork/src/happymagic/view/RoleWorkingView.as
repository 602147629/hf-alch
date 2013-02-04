package happymagic.view 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.utils.Timer;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.time.Time;
	import happyfish.utils.DateTools;
	import happymagic.event.RoleWorkEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.RoleWorkManager;
	import happymagic.model.command.RoleWorkEscCommand;
	import happymagic.model.command.RoleWorkFastCompleteCommand;
	import happymagic.model.vo.RoleVo;
	import happymagic.model.vo.RoleWorkPointClassVo;
	import happymagic.model.vo.RoleWorkPointVo;
	import happymagic.model.vo.RoleWorkVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class RoleWorkingView extends UISprite
	{
		private var iview:RoleWorkingViewUi;
		private var data:RoleWorkPointClassVo;
		private var timer:Timer;
		private var list:RoleWorkListView;
		private var pointvo:RoleWorkPointVo;
		
		public function RoleWorkingView() 
		{
			_view = new RoleWorkingViewUi();
			iview = _view as RoleWorkingViewUi;
			timer = new Timer(1000);
			timer.addEventListener(TimerEvent.TIMER, timerComplete);
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			
		    list = new RoleWorkListView(new RoleWorkListViewUi(), iview, 3, true, false);
			list.init(300, 45, 53, 45, 0,0);
			list.setGridItem(RoleWorkItem, RoleWorkItemUi);
			list.x = -200.55;
			list.y = -60.85;
			list.setButtonPosition(20, 140, 420, 140);
			list.tweenTime = 0;
		}
		
		private function timerComplete(e:TimerEvent):void 
		{
			var time:int = Time.getRemainingTimeByEnd(pointvo.time);
			var str:String = DateTools.getLostTime(time*1000, true, ":", ":", ":", " ");
			
			iview.timer.text = str;
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "closebtn":
					  closeMe(true);
					break;
					
				case "quickcomplete":
					  iview.mouseChildren = false;
					  iview.mouseEnabled  = false;
					  
					  var roleWorkFastCompleteCommand:RoleWorkFastCompleteCommand = new RoleWorkFastCompleteCommand();
					  roleWorkFastCompleteCommand.setData(data.id);
					  roleWorkFastCompleteCommand.addEventListener(Event.COMPLETE, roleWorkFastCompleteCommandComplete);
					break;
					
				case "escbtn":
					  iview.mouseChildren = false;
					  iview.mouseEnabled  = false;
					  
					  var roleWorkEscCommand:RoleWorkEscCommand = new RoleWorkEscCommand();
					  roleWorkEscCommand.setData(data.id);
					  roleWorkEscCommand.addEventListener(Event.COMPLETE, roleWorkEscCommandComplete);					
					break;
			}
		}
		
		private function roleWorkFastCompleteCommandComplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, roleWorkFastCompleteCommandComplete);
			EventManager.getInstance().dispatchEvent( new RoleWorkEvent(RoleWorkEvent.ROLEWORKQUICKCOMPLETE));
			close();
		}
		
		private function roleWorkEscCommandComplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, roleWorkEscCommandComplete);
			
			pointvo.time = 0;
			pointvo.state = 1;
			
			EventManager.getInstance().dispatchEvent( new RoleWorkEvent(RoleWorkEvent.ROLEWORKQUICKCOMPLETE));
			
			close();
		}
		
		public function setData(_data:RoleWorkPointClassVo):void
		{
			data = _data;
			
			iview.addname.text = data.name;
			
			pointvo = RoleWorkManager.getInstance().roleWorkData.getRoleWorkVo(data.id);
			
			var rolename:String = "佣兵";
			
			for (var i:int = 0; i < pointvo.roleIds.length; i++) 
			{
				var rolevo:RoleVo = DataManager.getInstance().roleData.getRole(pointvo.roleIds[i]);
				rolename += rolevo.name;
				
				if (i == pointvo.roleIds.length -1)
				{
					rolename += "打工中";
				}
				else
				{
					rolename += ",";
				}
			}
			iview.rolename.text = rolename;
			
			timer.start();
			var listarr:Array = new Array();
			var roleworkvo:RoleWorkVo;
			for (var j:int = 0; j < pointvo.roleIds.length; j++) 
			{
			    roleworkvo = new RoleWorkVo();
				roleworkvo.id = pointvo.roleIds[j];
				listarr.push(roleworkvo);
			}
			
			list.setData(listarr);
		}
		
		public function close():void
		{
			closeMe(true);
			timer.stop();
			timer.removeEventListener(TimerEvent.TIMER, timerComplete);
		}
		
	}

}