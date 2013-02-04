package happymagic.scene.world.grid.item 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.geom.Rectangle;
	import flash.utils.setTimeout;
	import flash.utils.Timer;
	import happyfish.display.control.FloatController;
	import happyfish.display.ui.FaceView;
	import happyfish.display.ui.Tooltips;
	import happyfish.events.GameMouseEvent;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.world.WorldState;
	import happyfish.time.Time;
	import happyfish.utils.DateTools;
	import happyfish.utils.display.FiltersDomain;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.FriendActionType;
	/**
	 * ...
	 * @author 
	 */
	public class SpecialBuild extends Decor 
	{
		private var tip:Sprite;
		private var timer:Timer;
		private var remain:int;
		private var tipFloatController:FloatController;
		
		public function SpecialBuild($data:Object, $worldState:WorldState,__callBack:Function=null) 
		{
			super($data, $worldState, __callBack);
			
			resetClassName();
			
			typeName = "SpecialBuild";
			
			//鼠标事件
            view.container.addEventListener(MouseEvent.ROLL_OVER, onMouseOver);
            view.container.addEventListener(MouseEvent.ROLL_OUT, onMouseOut);
			view.container.addEventListener(MouseEvent.MOUSE_MOVE, onMouseOverMove);
            view.container.addEventListener(MouseEvent.CLICK, onClick);
			
		}
		
		public function resetClassName():void {
			var tmparr:Array;
			if (decorVo.id==String(DataManager.getInstance().gameSetting.homeBuildId)) 
			{
				tmparr = decorVo.className.split(".");
				tmparr[1] = DataManager.getInstance().curSceneUser.viliageInfo[decorVo.id].toString();
				decorVo.className = tmparr.join(".");
			}
			
			if (DataManager.getInstance().gameSetting.barBuildIds.indexOf(decorVo.id)>-1) 
			{
				tmparr = decorVo.className.split(".");
				tmparr[1] = DataManager.getInstance().curSceneUser.viliageInfo[decorVo.id].toString();
				decorVo.className = tmparr.join(".");
			}
		}
		
		override protected function view_complete():void 
		{
			
			super.view_complete();
			setMirro(mirror);
			
		}
		
		override protected function makeView():IsoSprite 
		{
			return super.makeView();
		}
		
		override protected function bodyComplete():void 
		{
			super.bodyComplete();
			view.container.buttonMode = true;
			
			setTimeout(initTip,1000);
		}
		
		public function initTip():void
		{
			clearTip();
			
			var curUser:UserVo = DataManager.getInstance().currentUser;
			var sceneUser:UserVo = DataManager.getInstance().curSceneUser;
			var buildId:int = int(data["id"]);
			
			if (DataManager.getInstance().isSelfScene) //自己家
			{
				if (sceneUser.ownerUid != 0 && buildId == sceneUser.ownerBuildId) initOccTip(sceneUser,false,true);
			}
			else
			{
				var actionType:int = DataManager.getInstance().getVar("friendActionType");
				if (actionType == FriendActionType.OCC) //侵占
				{
					if (buildId == sceneUser.ownerBuildId) initOccTip(sceneUser, true);
				}
				if (actionType == FriendActionType.ASSISTANCE) //援助
				{
					if (buildId == sceneUser.ownerBuildId) initOccTip(sceneUser, false, true, true);
				}
				else if (actionType == FriendActionType.TAXES) //收税
				{
					if (buildId == sceneUser.ownerBuildId) initTaxTip(sceneUser);
				}
				else if (actionType == FriendActionType.VISIT) //访问
				{
					if (DataManager.getInstance().gameSetting.barBuildIds.indexOf(buildId) != -1) //酒馆
					{
						if (sceneUser.hireHelpUsed==0 && sceneUser.hireHelp < DataManager.getInstance().gameSetting.maxHireHelp) //可以加酒
						{
							initAddWineTip();
						}
						else initAddWineTip(true);
					}
				}
			}
		}
		
		private function initOccTip(sceneUser:UserVo,withTimer:Boolean = false,withTip:Boolean = false,assist:Boolean = false):void
		{
			tip = new SpecialBuildingOccTip;
			tip["nameTxt"].text = sceneUser.ownerName;
			
			var face:FaceView = new FaceView(25);
			face.loadFace(sceneUser.ownerFace);
			tip["photoContainer"].addChild(face);
			
			if (withTimer)
			{
				remain = Time.getRemainingTimeByEnd(sceneUser.ownerAwardTime);
				initTimer();
			}
			else tip["timeTip"].visible = tip["timeTxt"].visible = false;
			
			addTip();
		}
		
		private function initTaxTip(sceneUser:UserVo):void
		{
			tip = new SpecialBuildingTaxTip;
			tip["nameTxt"].text = sceneUser.ownerName;
			
			addTip();
		}
		
		private function initAddWineTip(gray:Boolean = false):void
		{
			tip = new SpecialBuildingAddWineTip;
			if (gray) tip.filters = [FiltersDomain.grayFilter];
			addTip();
		}
		
		private function addTip():void
		{
			var rect:Rectangle = _view.container.getBounds(_view.container);
			_view.container.addChild(tip);
			tip.x = (rect.left + rect.right) / 2;
			tip.y = rect.top;
			
			if (tipFloatController) tipFloatController.stop();
			tipFloatController = new FloatController(tip);
		}
		
		private function initTimer():void
		{
			timer = new Timer(1000);
			timer.addEventListener(TimerEvent.TIMER, onTimer);
			timer.start();
		}
		
		private function onTimer(event:TimerEvent):void
		{
			remain--;
			if (remain > 0) tip["timeTxt"].text = DateTools.getRemainingTime(remain,"%h:%i:%s");
			else
			{
				tip["timeTip"].visible = tip["timeTxt"].visible = false;
				clearTimer();
			}
		}
		
		private function clearTimer():void
		{
			if (!timer) return;
			timer.stop();
			timer.removeEventListener(TimerEvent.TIMER, onTimer);
			timer = null;
		}
		
		public function clearTip():void
		{
			if (!tip) return;
			if(tip.parent) tip.parent.removeChild(tip);
		}
		
		override public function clear():void 
		{
			super.clear();
			clearTimer();
			clearTip();
		}
		
	}

}