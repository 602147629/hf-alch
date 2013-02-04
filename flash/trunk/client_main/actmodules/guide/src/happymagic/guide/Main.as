package happymagic.guide
{
	import flash.display.Sprite;
	import flash.display.Stage;
	import flash.events.Event;
	import flash.events.IEventDispatcher;
	import flash.utils.setTimeout;
	import happyfish.events.MainEvent;
	import happyfish.guide.GuideDialogManager;
	import happyfish.guide.GuideManager;
	import happyfish.guide.GuideViewManger;
	import happyfish.guide.tools.GuideListUtil;
	import happyfish.guide.tools.GuideXMLConverter;
	import happyfish.guide.view.GuideArrow;
	import happyfish.guide.view.GuideDialog;
	import happyfish.guide.view.GuideMasker;
	import happyfish.guide.view.GuideTip;
	import happyfish.guide.vo.GuideVo;
	import happyfish.manager.EventManager;
	import happyfish.veal.RuntimeUtil;
	import happymagic.guide.api.BattleAPI;
	import happymagic.guide.api.CommandAPI;
	import happymagic.guide.api.DiyAPI;
	import happymagic.guide.api.FunctionFilterAPI;
	import happymagic.guide.api.GuideHelper;
	import happymagic.guide.api.ModuleAPI;
	import happymagic.guide.api.OrderAPI;
	import happymagic.guide.api.RoleAPI;
	import happymagic.guide.api.SceneAPI;
	import happymagic.guide.api.StoryAPI;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.UserVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class Main extends Sprite 
	{
		private var mainDataOk:Boolean;
		private var guideDataOk:Boolean;
		
		public function Main():void 
		{
			EventManager.addEventListener(MainEvent.MAIN_DATA_COMPELTE, mainDataComplete);
			
			//new LoadGuideDataCommand(guideDataComplete);
		}
		
		private function mainDataComplete(e:Event):void
		{
			EventManager.removeEventListener(MainEvent.MAIN_DATA_COMPELTE, mainDataComplete);
			//mainDataOk = true;
			init();
		}
		
		private function guideDataComplete():void 
		{
			//guideDataOk = true;
			init();
		}
		
		private function init():void 
		{
			var c:* = DisplayManager.menuView;
			//if (DataManager.getInstance().currentUser.uid != "10258") return;
			//if (!guideDataOk || !mainDataOk) return;
			
			//GuideListUtil.setXMLString(DataManager.getInstance().initStaticData.guideStatic);
			//delete DataManager.getInstance().initStaticData.guideStatic;
			
			EventManager.addEventListener("showGuide", showGuideHandler);
			
			var stage:Stage = DisplayManager.uiSprite.stage;
			var helper:GuideHelper = new GuideHelper();
			var command:CommandAPI = new CommandAPI();
			var funcFilter:FunctionFilterAPI = new FunctionFilterAPI();
			
			RuntimeUtil.getBaseParamMap().addParam("avatar_lingling", "happyfish.guide.view.Avatar_lingling");
			RuntimeUtil.getBaseParamMap().addParam("avatar_lili", "happyfish.guide.view.Avatar_lili");
			RuntimeUtil.getBaseParamMap().addParam("avatar_panda", "happyfish.guide.view.Avatar_panda");
			RuntimeUtil.getBaseParamMap().addParam("sendFinish", command.finishGuide);
			RuntimeUtil.getBaseParamMap().addParam("sendStep", command.updateStep);
			RuntimeUtil.getBaseParamMap().addParam("curStep", GuideManager.getInstance().getCurrGuideStepIdx);
			RuntimeUtil.getBaseParamMap().addParam("delayCall", helper.delayCall);
			RuntimeUtil.getBaseParamMap().addParam("stage", stage);
			RuntimeUtil.getBaseParamMap().addParam("helper", helper);
			RuntimeUtil.getBaseParamMap().addParam("if", helper.ifThenElse);
			RuntimeUtil.getBaseParamMap().addParam("goto", helper.gotoStep);
			RuntimeUtil.getBaseParamMap().addParam("story", new StoryAPI());
			RuntimeUtil.getBaseParamMap().addParam("battle", new BattleAPI());
			RuntimeUtil.getBaseParamMap().addParam("module", new ModuleAPI());
			RuntimeUtil.getBaseParamMap().addParam("scene", new SceneAPI());
			RuntimeUtil.getBaseParamMap().addParam("diy", new DiyAPI());
			RuntimeUtil.getBaseParamMap().addParam("order", new OrderAPI());
			RuntimeUtil.getBaseParamMap().addParam("role", new RoleAPI());
			RuntimeUtil.getBaseParamMap().addParam("unlock", funcFilter.unlock);
			RuntimeUtil.getBaseParamMap().addParam("hasLock", funcFilter.hasLock);
			RuntimeUtil.getBaseParamMap().addParam("stageEnabled", function():void { stage.mouseChildren = true; } );
			RuntimeUtil.getBaseParamMap().addParam("stageDisabled", function():void { stage.mouseChildren = false; } );
			
			
			GuideManager.getInstance().init(eventManager, stage);
			GuideViewManger.getInstance().init(stage, new GuideArrow(), new GuideMasker(), new GuideTip);
			GuideDialogManager.getInstance().setDialog(new GuideDialog());
			
			var info:Object = DataManager.getInstance().currentUser;
			var hasNovice:Boolean =  (info.guideInfo && info.guideInfo.id && info.guideInfo.id != "0");
			
			var xml:XML;
			include "../../noviceGuide.xml"
			
			GuideListUtil.setXMLString(xml.toString());
			
			if (hasNovice)
			{
				var list:Vector.<GuideVo> = GuideXMLConverter.convectToGuideList(xml);
				var find:Boolean = false;
				var curId:String = info.guideInfo.id;
				var curStep:int = info.guideInfo.idx;
				trace("curId=", curId, "curStep=", curStep);
				
				var len:int = list.length;
				for (var i:int = 0; i < len; i++)
				{
					if (find || curId == list[i].id)
					{
						find = true;
						GuideManager.getInstance().addGuide(list[i]);
					}
				}
				//throw new Error( xml);
				GuideManager.getInstance().gotoGuideStep(curStep-1);
				GuideManager.getInstance().autoNextGuide = true;
				//GuideManager.getInstance().addEventListener(GuideEvent.ALL_STEP_FINISH, noviceGuideFinish);
				//stage.addEventListener(MouseEvent.MOUSE_DOWN, ignoreMouseDown, true, int.MAX_VALUE);
			}
			
			
		}
		
		private function showGuideHandler(e:Event):void 
		{
			GuideManager.getInstance().removeAllGuide();
			setTimeout(function():void
			{
				GuideListUtil.startGuide(e["data"]);
			},0);
			
		}
		
		//private function initTaskGuide():void
		//{
			//GuideXMLConverter.autoAddIgnoreMouse = true;
			//GuideManager.getInstance().autoNextGuide = false;
			//var xml:XML;
			//include "../../taskGuide.xml"
			//GuideListUtil.setXMLString(xml.toString());
		//}
		
		private function get eventManager():IEventDispatcher
		{
			return EventManager.getInstance();
		}
		
	}
	
}