package happymagic.model.control 
{
	import flash.geom.Point;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.AlginType;
	import happymagic.display.view.levelUpgrade.LevelUpgradeView;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.events.DataManagerEvent;
	import happymagic.events.MainInfoEvent;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.events.UserInfoChangeVo;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.LevelInfoVo;
	import happymagic.model.vo.ResultVo;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.MagicWorld;
	
	/**
	 * ...
	 * @author jj
	 */
	public class TakeResultVoControl 
	{
		
		public function TakeResultVoControl(access:Private) 
		{
			
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
				}
			}
			else
			{	
				throw new Error( "TakeResultVoControl"+"单例" );
			}
		}
		
		/**
		 * 处理resultVo
		 * @param	value
		 * @param	piao	是否用飘屏显示错误信息
		 */
		public function take(value:ResultVo,piao:Boolean=false,piaoPoint:Point=null):void {
			
			//显示加减水晶等信息表现
			var infoChange:UserInfoChangeVo = new UserInfoChangeVo();
			infoChange.turnFromResultVo(value);
			
			//tmp
			//infoChange.levelUp = true;
			
			//显示升级面板
			if (infoChange.levelUp) 
			{
				var tmpuser:UserVo = DataManager.getInstance().currentUser;
				tmpuser.level++;
				
				var nextlevel:LevelInfoVo = DataManager.getInstance().getLevelInfo(tmpuser.level + 1);
				var tmplevel:LevelInfoVo = DataManager.getInstance().getLevelInfo(tmpuser.level);
				
				tmpuser.maxExp = nextlevel.maxExp;
				//tmpuser.max_mp = tmplevel.magic_limit + (DataManager.getInstance().worldState.world as MagicWorld).getAddMaxMp();
				tmpuser.maxSp += 10;
				tmpuser.sp = tmpuser.maxSp;
				infoChange.sp = 0;
				
				DataManager.getInstance().curSceneUser = tmpuser;
				//if (DataManager.getInstance().isDiying) 
				//{
					//DisplayManager.uiSprite.getModule(ModuleDict.MODULE_MAININFO)["diyingUserLevelUp"]();
				//}
				
				DisplayManager.showBusinessLevelUpView(tmpuser.level);
				
				DataManager.getInstance().setVar("levelUpDoing", false);
				
				//var levelInfoView:LevelUpgradeView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_LEVELINFO, ModuleDict.MODULE_LEVELINFO_CLASS,true,AlginType.CENTER,30,-50) as LevelUpgradeView;
				//levelInfoView.setData(DataManager.getInstance().getLevelInfo(tmpuser.level), 0);
				//DisplayManager.uiSprite.setBg(levelInfoView);
			}
			
			infoChange.piao = piao;
			infoChange.showPoint = piaoPoint;
			if (!infoChange.isEmpty) 
			{
				var e:DataManagerEvent = new DataManagerEvent(DataManagerEvent.USERINFO_CHANGE);
				e.userChange = infoChange;
				EventManager.getInstance().dispatchEvent(e);
			}
			
			//报错信息弹出
			if (value.status!=ResultVo.SUCCESS) 
			{
				var content:String = LocaleWords.getInstance().getWord(value.content);
				if (piao) 
				{
					//漂字
					var msgs_new:Array = [[PiaoMsgType.TYPE_BAD_STRING, content]];
					if (!piaoPoint) 
					{
						piaoPoint = new Point(DisplayManager.uiSprite.mouseX, DisplayManager.uiSprite.mouseY);
					}
					var event_piao_msg:PiaoMsgEvent = new PiaoMsgEvent(PiaoMsgEvent.SHOW_PIAO_MSG, msgs_new, piaoPoint.x, piaoPoint.y);
					EventManager.getInstance().dispatchEvent(event_piao_msg);
				}else {
					DisplayManager.showSysMsg(content);
				}
				
			}
		}
		
		public static function getInstance():TakeResultVoControl
		{
			if (instance == null)
			{
				instance = new TakeResultVoControl( new Private() );
			}
			return instance;
		}
		
		
		private static var instance:TakeResultVoControl;
		
	}
	
}
class Private {}