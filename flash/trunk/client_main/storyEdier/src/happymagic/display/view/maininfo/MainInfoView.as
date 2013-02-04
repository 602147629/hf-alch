package happymagic.display.view.maininfo 
{
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	import com.greensock.TweenLite;
	import com.greensock.TweenMax;
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.geom.Point;
	import flash.utils.Timer;
	import happyfish.display.ui.FaceView;
	import happyfish.display.view.PerBarView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.ModuleMvType;
	import happyfish.utils.DateTools;
	import happyfish.utils.display.McShower;
	import happyfish.utils.display.TitleSprite;
	import happymagic.display.view.levelUpgrade.LevelUpgradeView;
	import happymagic.display.view.maininfo.MainInfoHomeView;
	import happymagic.display.view.maininfo.MainInfoRoleView;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.display.view.ui.AwardResultView;
	import happymagic.events.DataManagerEvent;
	import happymagic.events.MainInfoEvent;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.PublicDomain;
	import happymagic.model.command.LoadUserInfoCommand;
	import happymagic.model.command.TestCommand;
	import happymagic.model.MagicJSManager;
	import happymagic.model.vo.AvatarVo;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.LevelInfoVo;
	import happymagic.model.vo.SceneClassVo;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.control.RoomUpgradeMvCommand;
	import happymagic.scene.world.MagicWorld;
	import happymagic.scene.world.SceneType;
	
	/**
	 * ...
	 * @author jj
	 */
	public class MainInfoView extends UISprite
	{
		
		private var spBar:PerBarView;
		
		private var spHealTimer:Timer;
		private var spHealTime:Number;
		
			
		private var spHealNeedTime:Number;
		private var iview:alchemyMainInfoUi;
		private var homeInfo:MainInfoHomeView;
		private var roleInfo:MainInfoRoleView;
		
		public function MainInfoView() 
		{
			super();
			_view = new alchemyMainInfoUi();
			
			iview = _view as alchemyMainInfoUi;
			
			homeInfo = new MainInfoHomeView();
			iview.addChild(homeInfo);
			
			roleInfo = new MainInfoRoleView();
			iview.addChild(roleInfo);
			roleInfo.hide();
			
			iview.coinFlashMc.alpha = 
			iview.gemFlashMc.alpha = 
			iview.spFlashMc.alpha = 0;	
			
			iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			spBar = new PerBarView(iview.spBarUi, iview.spBarUi.width);
			//spBar.minW = 2;
			
			EventManager.getInstance().addEventListener(SceneEvent.SCENE_DATA_COMPLETE, sceneDataComplete);
			EventManager.getInstance().addEventListener(SceneEvent.SCENE_COMPLETE, sceneAllComplete);
			
			EventManager.getInstance().addEventListener(DataManagerEvent.USERINFO_CHANGE, userInfoChange);
			EventManager.getInstance().addEventListener(DataManagerEvent.ROLEDATA_CHANGE, roleDataChnage);
			
			//loadData();
			if (DataManager.getInstance().currentUser) 
			{
				initInfo();
				startSpHealTimer();
			}
			
		}
		
		private function roleDataChnage(e:DataManagerEvent):void 
		{
			if (e.role_addOrRemove) 
			{
				roleInfo.init();
			}else {
				roleInfo.initInfo();
			}
			
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case iview.addSpBtn:
					//var aa:McShower = new McShower(roomUpgradeMv, view.stage);
					//aa.delay = 3;
					//aa.x = 748 / 2;
					//aa.y = 600 / 2;
					
					//var awards:Array = new Array();
					//awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_COIN, num:111 } ));
					//awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_GEM, num:111 } ));
					//awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_EXP, num:111 } ));
					//awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_SP, num:111 } ));
					//
					//var awardwin:AwardResultView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_AWARD_RESULT, ModuleDict.MODULE_AWARD_RESULT_CLASS,true) as AwardResultView;
					//awardwin.setData( { name:LocaleWords.getInstance().getWord("awardTile"), awards:awards } );
					//DisplayManager.uiSprite.setBg(awardwin);
				break;
				
				case iview.addGemBtn:
                     ActModuleManager.getInstance().addActModule(DataManager.getInstance().getActByName("RoleWork"));						
				break;
				
			}
		}
		
		private function sceneAllComplete(e:SceneEvent):void 
		{
			//initInfo();
			switch (DataManager.getInstance().curSceneType) 
			{
				
				case SceneType.TYPE_EXPLORE:
					homeInfo.hide();
					roleInfo.show();
					roleInfo.initInfo();
					
				break;
				
				default:
					homeInfo.show();
					roleInfo.hide();
					homeInfo.initInfo();
				break;
			}
		}
		
		private function sceneDataComplete(e:SceneEvent):void 
		{
			//场景数据更改,重新刷新数据
			//initInfo();
			
			
		}
		
		private function startSpHealTimer():void
		{
			spHealTimer = new Timer(1000);
			spHealTimer.addEventListener(TimerEvent.TIMER, spHealFun);
			
			checkSpTimer();
		}
		
		private function spHealFun(e:TimerEvent):void 
		{
			DataManager.getInstance().curSceneUser.replySpTime--;
			if (DataManager.getInstance().curSceneUser.replySpTime<=0) 
			{
				DataManager.getInstance().curSceneUser.sp += DataManager.getInstance().gameSetting.replySp;
				DataManager.getInstance().curSceneUser.replySpTime = DataManager.getInstance().gameSetting.replySpTime;
				initInfo();
			}
			
			_view.spHealTimeTxt.text = 
				DateTools.getRemainingTime(DataManager.getInstance().curSceneUser.replySpTime)
				+ LocaleWords.getInstance().getWord("mpHealTips", DataManager.getInstance().gameSetting.replySp.toString());
		}
		
		/**
		 * 根据TYPE返回该值在界面上的位置
		 * @param	valueType
		 * @return
		 */
		public function getValuePosition(valueType:uint):Point {
			var p:Point;
			
			switch (valueType) 
			{
				case PiaoMsgType.TYPE_COIN:
					//TODO 还没有UI,暂用RED
					p = new Point(iview.coinNumTxt.x+30, iview.coinNumTxt.y+10);
				break;
				
				case PiaoMsgType.TYPE_GEM:
					p = new Point(iview.gemNumTxt.x+30, iview.gemNumTxt.y+10);
				break;
				
				case PiaoMsgType.TYPE_EXP:
					p = new Point(homeInfo.expTxt.x + 20, homeInfo.expTxt.y + 10);
					p = homeInfo.localToGlobal(p);
					return p;
				break; 
				
				case PiaoMsgType.TYPE_SP:
					p = new Point(iview.spTxt.x+20, iview.spTxt.y+10);
				break;
				
				default:
				return null;
				break;
			}
			
			p = iview.localToGlobal(p);
			return p;
		}
		
		
		/**
		 * 修改用户自己的信息,并可以飘屏显示
		 * @param	coin
		 * @param	gem
		 * @param	exp
		 * @param	mp
		 * @param	piao	是否要显示飘屏,位置为当前鼠标位置
		 * @param	showPoint	显示的位置,如不传就出现在当前鼠标位置
		 */
		public function changeUserInfo(coin:int,gem:int,exp:int,sp:int,piao:Boolean=false,showPoint:Point=null):void {
			
			
			//如果不显示飘,直接更新数据
			//如果飘屏,就会在飘屏完成时改变数据
			if (!piao) 
			{
				changeCrystalAndGem(coin,gem);
				changeExpAndSp(exp, sp);
			}
			
			initInfo();
			
			//通知飘屏
			if (piao) 
			{
				if (sp) 
				{
					changeExpAndSp(0, sp);
					initInfo();
				}
				
				var msgs:Array = [[PiaoMsgType.TYPE_COIN, coin],
				[PiaoMsgType.TYPE_GEM, gem],
				[PiaoMsgType.TYPE_EXP, exp]
				];
				var px:Number;
				var py:Number;
				if (showPoint) 
				{
					px = showPoint.x;
					py = showPoint.y;
				}else {
					px = _view.stage.mouseX;
					py = _view.stage.mouseY;
				}
				var event:PiaoMsgEvent = new PiaoMsgEvent(PiaoMsgEvent.SHOW_PIAO_MSG, msgs,px,py);
				EventManager.getInstance().dispatchEvent(event);
			}
		}
		
		private function changeCrystalAndGem(coin:int=0,gem:int=0):void {
			DataManager.getInstance().changeCurUserInfo(coin,gem);
		}
		
		/**
		 * 修改当前场景主人的EXP和MP改变
		 * 只是改变数值,没有刷新显示
		 * @param	exp
		 * @param	mp
		 */
		private function changeExpAndSp(exp:int,sp:int):void {
			if (DataManager.getInstance().isSelfScene) 
			{
				DataManager.getInstance().changeCurUserInfo(0,0,exp,sp);
			}else {
				DataManager.getInstance().changeCurUserInfo(0,0,exp,sp);
			}
		}
		
		/**
		 * 让指定值的表现物闪动一次
		 * @param	type
		 * @param	num
		 */
		public function flashValue(type:uint,num:int):void
		{
			switch (type) 
			{
				case PiaoMsgType.TYPE_COIN:
					
					flashIt(iview.coinFlashMc);
					changeCrystalAndGem(num);
				break;
				
				case PiaoMsgType.TYPE_GEM:
					flashIt(iview.gemFlashMc);
					changeCrystalAndGem(0,num);
				break;
				
				case PiaoMsgType.TYPE_EXP:
					if (DataManager.getInstance().isSelfScene) 
					{
						flashIt(homeInfo.expFlashMc);
						changeExpAndSp(num,0);
					}
				break;
				
				case PiaoMsgType.TYPE_SP:
					//if (DataManager.getInstance().isSelfScene || DataManager.getInstance().getCurrentScene().type==SceneClassVo.DUNGEON) 
					if (DataManager.getInstance().isSelfScene) 
					{
						flashIt(iview.spFlashMc);
						changeExpAndSp(0, num);
					}
				break;
			}
			
			initInfo();
		}
		
		private function flashIt(target:DisplayObject):void {
			TweenMax.killTweensOf(target,true);
			TweenMax.to(target, .2, { tint:0xffffff,alpha:1  } );
			TweenMax.to(target, .2, { tint:null,alpha:0,delay:.2  } );
		}
		
		/**
		 * 用户信息改变事件
		 * @param	e
		 */
		private function userInfoChange(e:DataManagerEvent):void 
		{
			if (e.userChange) 
			{
				//如果有userChange参数，说明需要飘屏显示
				//显示飘屏
				//修改用户数据,并直接飘屏显示
				/*if (DataManager.getInstance().isSelfScene) 
				{
					changeUserInfo(e.userChange.coin, e.userChange.gem, e.userChange.exp,
								e.userChange.sp,e.userChange.maxSp, e.userChange.piao, e.userChange.showPoint);
				}else {
					changeUserInfo(e.userChange.coin, e.userChange.gem, e.userChange.exp,
								0,0, e.userChange.piao, e.userChange.showPoint);
					changeUserInfo(0, 0, 0, e.userChange.sp, e.userChange.maxSp);
				}*/
				changeUserInfo(e.userChange.coin, e.userChange.gem, e.userChange.exp,
								e.userChange.sp, e.userChange.piao, e.userChange.showPoint);
				
				
			}else {
				//无userChange参数，说明数据已修改，无需飘屏,直接刷新数据
				initInfo();
			}
		}
		
		public function initInfo():void {
			var data:UserVo = DataManager.getInstance().currentUser;
			
			var sp:Number = Math.min(data.sp, data.maxSp);
			iview.spTxt.text = sp + "/" + data.maxSp;
			
			//检查魔法是否满了
			checkSpTimer();
			
			spBar.maxValue = data.maxSp;
			spBar.setData(data.sp);
			
			iview.coinNumTxt.text = data.coin.toString();
			iview.gemNumTxt.text = data.gem.toString();
			
			homeInfo.initInfo();
			roleInfo.initInfo();
		}
		
		private function checkSpTimer():void 
		{
			iview.spHealTimeTxt.visible = false;
			//判断魔法是否满了,如果满了,就停止魔法回复进程,如果未满并没有进程中,就重启进程
			var tmpdata:UserVo = DataManager.getInstance().currentUser;
			
			var sp:int = tmpdata.sp;
			var maxSp:uint = tmpdata.maxSp;
			if (sp>=maxSp) 
			{
				if (spHealTimer) 
				{
					spHealTimer.stop();
					iview.spHealTimeTxt.visible = false;
				}
			}else {
				if (spHealTimer) 
				{
					if (!spHealTimer.running) 
					{
						DataManager.getInstance().curSceneUser.replySpTime = DataManager.getInstance().gameSetting.replySpTime;
						spHealTimer.start();
						
					}
					iview.spHealTimeTxt.visible = true;
				}
			}
		}
		
		
	}

}