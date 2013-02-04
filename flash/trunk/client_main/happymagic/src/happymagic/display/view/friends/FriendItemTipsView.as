package happymagic.display.view.friends 
{
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.utils.Timer;
	import happyfish.manager.EventManager;
	import happyfish.time.Time;
	import happyfish.utils.DateTools;
	import happyfish.utils.display.BtnStateControl;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.FriendActionType;
	/**
	 * ...
	 * @author 
	 */
	public class FriendItemTipsView extends friendItemTipsUi 
	{
		private var data:UserVo;
		private var curTime:Number;
		private var timer:Timer;
		private var timeStr:String;
		
		public function FriendItemTipsView() 
		{
			
			occBtn.visible=
			assistanceBtn.visible=
			safeBtn.visible=
			taxesBtn.visible=
			timeTxt.visible=
			lowLevel10Mc.visible=
			lowLevel15Mc.visible = false;
			
			addEventListener(MouseEvent.CLICK, clickFun);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case visitBtn:
					DataManager.getInstance().setVar("friendActionType", FriendActionType.VISIT);
					go();
				break;
				
				case assistanceBtn:
					DataManager.getInstance().setVar("friendActionType", FriendActionType.ASSISTANCE);
					go();
				break;
				
				case occBtn:
					DataManager.getInstance().setVar("friendActionType", FriendActionType.OCC);
					go();
				break;
				
				case taxesBtn:
					DataManager.getInstance().setVar("friendActionType", FriendActionType.TAXES);
					go();
				break;
				
			}
		}
		
		private function go():void {
			DisplayManager.uiSprite.closeModule("friend",true);
			var tmpe:SceneEvent = new SceneEvent(SceneEvent.CHANGE_SCENE);
			tmpe.uid = data.uid;
			tmpe.sceneId = 2;
			EventManager.getInstance().dispatchEvent(tmpe);
		}
		
		public function setData(value:UserVo):void {
			data = value;
			
			var curUser:UserVo = DataManager.getInstance().currentUser;
			if (data.ownerUid) 
			{
				if (data.ownerUid.toString()==curUser.uid) 
				{
					if (Time.getRemainingTimeByEnd(data.ownerAwardTime)==0) 
					{
						//收税
						taxesBtn.visible = true;
						BtnStateControl.setBtnState(taxesBtn, true);
					}else {
						//等收税
						taxesBtn.visible = true;
						
						BtnStateControl.setBtnState(taxesBtn, false);
						timeTxt.visible = true;
						startCheckTime(data.ownerAwardTime,"冷却时间:\n");
					}
				}else {
					//援助
					assistanceBtn.visible = true;
				}
			}else {
				var safeTime:int = Time.getRemainingTimeByEnd(data.safeTime);
				var atkSafeTime:int = Time.getRemainingTimeByEnd(data.atkSafeTime);
				if (safeTime>0 || atkSafeTime>0) 
				{
					//保护中
					safeBtn.visible = true;
					timeTxt.visible = true;
					if (atkSafeTime>0) 
					{
						startCheckTime(data.atkSafeTime,"维护时间:\n");
					}else if (safeTime>0) 
					{
						startCheckTime(data.safeTime,"维护时间:\n");
					}
					
				}else {
					//入侵
					//if (data.level<15) 
					//{
						//lowLevel15Mc.visible = true;
					//}else if ((DataManager.getInstance().currentUser.level-data.level)>=10) 
					//{
						//lowLevel10Mc.visible = true;
					//}else {
						//occBtn.visible = true;
					//}
					occBtn.visible = true;
				}
			}
			
		}
		
		private function startCheckTime(_curTime:Number,addStr:String):void 
		{
			timeStr = addStr;
			curTime = _curTime;
			timer = new Timer(1000);
			timer.addEventListener(TimerEvent.TIMER, checkTime);
			timer.start();
		}
		
		private function checkTime(e:TimerEvent):void 
		{
			var remainTime:Number = Time.getRemainingTimeByEnd(curTime);
			if (remainTime==0) 
			{
				timeTxt.text = timeStr + DateTools.getRemainingTime(remainTime,"%d:%h:%i:%s");
				timer.stop();
				timer.removeEventListener(TimerEvent.TIMER, checkTime);
				timer = null;
				setData(data);
				
			}else {
				timeTxt.text = timeStr + DateTools.getRemainingTime(remainTime,"%d:%h:%i:%s");
			}
			
		}
		
	}

}