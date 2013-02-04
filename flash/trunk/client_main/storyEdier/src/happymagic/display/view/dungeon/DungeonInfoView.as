package happymagic.display.view.dungeon 
{
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.utils.Timer;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.time.Time;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.PublicDomain;
	import happymagic.model.command.MoveSceneCommand;
	import happymagic.model.command.ProtectCommand;
	import happymagic.model.vo.SceneClassVo;
	import happymagic.scene.world.SceneType;
	/**
	 * ...
	 * @author ...
	 */
	public class DungeonInfoView extends UISprite
	{
		private var countUi:DungeonInfoUi;
		private var protectUi:ProtectUi;
		private var timer:Timer;
		private var remain:Number;
		
		public function DungeonInfoView() 
		{
			super();
			
			countUi = new DungeonInfoUi();
			countUi.mouseChildren = countUi.mouseEnabled = false;
			countUi.visible = false;
			
			protectUi = new ProtectUi();
			protectUi.startBtn.addEventListener(MouseEvent.CLICK, protect);
			protectUi.visible = false;
			
			_view = new MovieClip;
			_view.addChild(countUi);
			_view.addChild(protectUi);
			
			EventManager.getInstance().addEventListener(SceneEvent.SCENE_DATA_COMPLETE, refresh);
			refresh();
		}
		
		public function refresh(event:SceneEvent = null):void
		{
			clearTimer();
			
			var sceneId:int = DataManager.getInstance().currentUser.currentSceneId;
			var sceneClassVo:SceneClassVo = DataManager.getInstance().getSceneClassById(sceneId);
			var sceneType:int = DataManager.getInstance().curSceneType;
			
			if (sceneType == SceneType.TYPE_EXPLORE)
			{
				var refreshTime:int = DataManager.getInstance().getVar("sceneRefreshTime");
				remain = Time.getRemainingTimeByEnd(refreshTime);
				
				initTimer();
				
				countUi.nameTxt.text = sceneClassVo.name;
				countUi.visible = true;
				protectUi.visible = false;
			}
			else if (sceneType == SceneType.TYPE_FRIEND_VILIAGE || sceneType == SceneType.TYPE_SELF_VILIAGE)
			{
				var safeTime:int = DataManager.getInstance().curSceneUser.safeTime;
				remain = Time.getRemainingTimeByEnd(safeTime);
				if (remain > 0)
				{
					initTimer();
					protectUi.visible = false;
					countUi.visible = true;
					countUi.nameTxt.text = "保护开启中";
				}
				else
				{
					protectUi.visible = countUi.visible = false;
					if (sceneType == SceneType.TYPE_SELF_VILIAGE) protectUi.visible = true;
				}
			}
			else if (sceneType == SceneType.TYPE_HOME) countUi.visible = protectUi.visible = false;
		}
		
		private function initTimer():void
		{
			timer = new Timer(1000);
			timer.addEventListener(TimerEvent.TIMER, onTimer);
			timer.start();
		}
		
		private function onTimer(event:TimerEvent = null):void
		{
			remain--;
			
			if (remain <= 0)
			{
				var sceneType:uint = DataManager.getInstance().curSceneType;
				if (sceneType == SceneType.TYPE_EXPLORE) countUi.timeTxt.text = "refreshed";
				else if (sceneType == SceneType.TYPE_FRIEND_VILIAGE || sceneType == SceneType.TYPE_SELF_VILIAGE) countUi.visible = false;
				
				clearTimer();
			}
			else
			{
				var sec:int = remain % 60;
				var min:int = remain % 3600 / 60;
				var hour:int = remain / 3600;
				
				countUi.timeTxt.text = hour + ":" + min + ":" + sec;
			}
		}
		
		public function clearTimer():void
		{
			if (!timer) return;
			
			timer.stop();
			timer.addEventListener(TimerEvent.TIMER, onTimer);
			timer = null;
		}
		
		private function protect(event:MouseEvent):void
		{
			var command:ProtectCommand = new ProtectCommand;
			command.addEventListener(Event.COMPLETE, onProtectComplete);
		}
		
		private function onProtectComplete(event:Event):void
		{
			event.target.removeEventListener(Event.COMPLETE, onProtectComplete);
			
			var command:ProtectCommand = event.target as ProtectCommand;
			
			if (int(command.objdata["result"]["status"]) <= 0) return;
			
			DataManager.getInstance().curSceneUser.safeTime = command.safeTime;
			
			remain = Time.getRemainingTimeByEnd(command.safeTime);
			clearTimer();
			initTimer();
			countUi.visible = true;
			countUi.nameTxt.text = "保护开启中";
			
			protectUi.visible = false;
		}
		
	}

}