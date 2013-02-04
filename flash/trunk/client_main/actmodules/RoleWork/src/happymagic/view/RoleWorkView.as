package happymagic.view 
{
	import com.greensock.loading.core.DisplayObjectLoader;
	import flash.display.DisplayObject;
	import flash.display.Loader;
	import flash.display.MovieClip;
	import flash.display.SimpleButton;
	import flash.display.Sprite;
	import flash.display.StageDisplayState;
	import flash.events.Event;
	import flash.events.FullScreenEvent;
	import flash.events.IOErrorEvent;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.filters.GlowFilter;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.net.URLRequest;
	import flash.utils.Timer;
	import happyfish.cacher.SwfClassCache;
	import happyfish.display.control.FloatController;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.module.ModuleManager;
	import happyfish.time.Time;
	import happyfish.utils.DateTools;
	import happyfish.utils.display.BtnStateControl;
	import happyfish.utils.display.ItemDropController;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.display.view.maininfo.MainInfoView;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.display.view.task.TaskNeedItemListView;
	import happymagic.event.RoleWorkEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.RoleWorkManager;
	import happymagic.model.command.RoleWorkGetAwardCommand;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.RoleVo;
	import happymagic.model.vo.RoleWorkMapVo;
	import happymagic.model.vo.RoleWorkPointClassVo;
	import happymagic.model.vo.RoleWorkPointVo;
	import xrope.LayoutAlign;
	/**
	 * ...
	 * @author ZC
	 */
	public class RoleWorkView extends UISprite
	{
		private var pathData:Array;
		private var sp1:Sprite;
		private var sp2:Sprite;
		private var btn:DisplayObject;
		private var title:DisplayObject;
		private var stop:int = 1;		
		private var tips:RoleWorkWorldTips;
		private var awardsMc:TaskNeedItemListView;
		private var timer:Timer;
		
		private var vectpoint:Point		
		private var firarr:Array;
		private var viewx:int;
		private var viewy:int;
		public function RoleWorkView() 
		{
			_view = new MovieClip();
					
			sp1 = new Sprite();
			_view.addChild(sp1);
		
			_view.addEventListener(MouseEvent.CLICK, clickrun);
			_view.addEventListener(MouseEvent.MOUSE_OVER, clickover);
			_view.addEventListener(MouseEvent.MOUSE_OUT, clickout);
			
			_view.addEventListener(Event.ADDED_TO_STAGE	, addedtostage);
			
			timer = new Timer(1000);
			timer.addEventListener(TimerEvent.TIMER, timerComplete);
			EventManager.getInstance().addEventListener(RoleWorkEvent.ROLEWORKQUICKCOMPLETE,roleworkquickcomplete);
		    EventManager.getInstance().addEventListener(RoleWorkEvent.ROLEUPDATA, roleupdatacomplete);
		}
		
		private function roleupdatacomplete(e:RoleWorkEvent):void 
		{
			cleartitle();
		}
		
		private function timerComplete(e:TimerEvent):void 
		{
			cleartitle();
		}
		
		private function cleartitle():void 
		{
			var mcClass1:Class = SwfClassCache.getInstance().getClass("awardIcon_UnknowIcon");	

			for (var i:int = 0; i < sp1.numChildren; i++) 
			{
				if (sp1.getChildAt(i) is RoleWorkingPrompt|| sp1.getChildAt(i) is RoleWorkCompletePrompt||sp1.getChildAt(i) is mcClass1)
				{
					sp1.removeChild(sp1.getChildAt(i));
					i--;
				}
			}
			
			updata();
		}
		
		private function roleworkquickcomplete(e:RoleWorkEvent):void 
		{
			cleartitle();
		}
		
		private function clickover(e:MouseEvent):void 
		{	
			for (var i:int = 0; i < RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass.length; i++) 
			{
				switch(e.target.name)
			    {
				   case (RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i] as RoleWorkPointClassVo).iconClass:
	                     
					     (e.target as MovieClip).filters = [new GlowFilter(0xFF6600, 1, 10, 10, 2.5, 1, false, false)];;
					     
					     tips = new RoleWorkWorldTips();
						 tips.mouseChildren = false;
						 tips.mouseEnabled = false;
						 TextFieldUtil.autoSetTxtDefaultFormat(tips);
						 tips.nametxt.text = (RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i] as RoleWorkPointClassVo).name;
						 tips.leveltxt.text = (RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i] as RoleWorkPointClassVo).roleLevel.toString();
						 tips.numtxt.text = (RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i] as RoleWorkPointClassVo).roleNum.toString();
						 tips.sptxt.text = (RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i] as RoleWorkPointClassVo).sp.toString();
						 tips.timer.text = updataTime((RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i] as RoleWorkPointClassVo).needTime)
						 tips.x = (RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i] as RoleWorkPointClassVo).x;
						 tips.y = (RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i] as RoleWorkPointClassVo).y;
						 
						 awardsMc = new TaskNeedItemListView(new defaultListUi(),tips,5,true);
						 awardsMc.x = -170;
						 awardsMc.y = -100;
			
						 awardsMc.init(280, 90, 44, 90, 52, 45, LayoutAlign.CENTER);	
						 awardsMc.tweenTime = 0;
						 var ponvo:RoleWorkPointVo = RoleWorkManager.getInstance().roleWorkData.getRoleWorkVo(RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i].id);
						 
						 awardsMc.setData(ponvo.awards);
						 _view.addChild(tips);
			             var tmpclass:Class = SwfClassCache.getInstance().getClass((RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i] as RoleWorkPointClassVo).iconClass);
			             var icon:MovieClip = new tmpclass() as MovieClip;						 
						 tips.x =  RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i].x + sp1.x;
						 tips.y =  RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i].y - tips.height/2 + sp1.y;
						 
						 var tipsStagepoint:Point = new Point();
						 tipsStagepoint.x =  sp1.stage.mouseX;
						 tipsStagepoint.y =  sp1.stage.mouseY;

						 if (tipsStagepoint.x -tips.width/2 < 0)
						 {
							 tips.x += tips.width/2;
						 }
						 
						 if (_view.stage.stageWidth - tipsStagepoint.x < tips.width/2)
						 {
							 tips.x -= tips.width/2;	
						 }

						 if (tipsStagepoint.y -tips.height < 0)
						 {
							 tips.y += tips.height;
						 }
						 
						 if (_view.stage.stageHeight - tipsStagepoint.y  <tips.height/2)
						 {
							 tips.y -= tips.height;
						 }							 
						 
				     break;
			    }		
			}	
		}
		
		private function clickout(e:MouseEvent):void 
		{
			for (var i:int = 0; i < RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass.length; i++) 
			{
				switch(e.target.name)
			    {
				   case (RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i] as RoleWorkPointClassVo).iconClass:
	                  _view.removeChild(tips);
					  (e.target as MovieClip).filters = firarr;
				     break;
			    }		
			}					
		}
		
		private function addedtostage(e:Event):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, addedtostage);
			_view.stage.addEventListener(FullScreenEvent.FULL_SCREEN,fullresize);
		}
		
		private function fullresize(e:FullScreenEvent):void 
		{
			trace("asd");
			
			btn.x =  _view.stage.stageWidth / 2 -65;
			btn.y =  _view.stage.stageHeight / 2  -560;	
			
			title.x =  _view.stage.stageWidth / 2 -560;
			title.y =  _view.stage.stageHeight / 2  -590;				
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "closebtn":
					 close();
					break;								
			}
			
			for (var i:int = 0; i < RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass.length; i++) 
			{
				var namestr:String = (RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i] as RoleWorkPointClassVo).iconClass + "1";
				switch(e.target.name)
			    {
				   case (RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i] as RoleWorkPointClassVo).iconClass:
		           case namestr:
			            var vo:RoleWorkPointVo = RoleWorkManager.getInstance().roleWorkData.getRoleWorkVo((RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i] as RoleWorkPointClassVo).id);
						
						if (vo.state == 1)
						{
							var roleWorkSelectView:RoleWorkSelectView = DisplayManager.uiSprite.addModule("RoleWorkSelectView", "happymagic.view.RoleWorkSelectView", false, AlginType.CENTER, 20, 0) as RoleWorkSelectView;
			                roleWorkSelectView.setData(RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i]);
			                DisplayManager.uiSprite.setBg(roleWorkSelectView);	
						}
						else if (vo.state == 2)
						{
							if (Time.getRemainingTimeByEnd(vo.time))
							{
								//打工中
								var roleWorkingView:RoleWorkingView = DisplayManager.uiSprite.addModule("RoleWorkingView", "happymagic.view.RoleWorkingView", false, AlginType.CENTER, 20, 0) as RoleWorkingView;
			                	roleWorkingView.setData(RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i]);
			                	DisplayManager.uiSprite.setBg(roleWorkingView);								
							}
							else
							{
								//领奖请求
								_view.mouseEnabled = false;
								_view.mouseChildren = false;
								var roleWorkGetAwardCommand:RoleWorkGetAwardCommand = new RoleWorkGetAwardCommand();
								roleWorkGetAwardCommand.setData(RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i].id);
								roleWorkGetAwardCommand.addEventListener(Event.COMPLETE, roleWorkGetAwardCommandComplete);				
								vectpoint = new Point((RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i] as RoleWorkPointClassVo).x, (RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass[i] as RoleWorkPointClassVo).y);
							}
							
						}
						
				     break;
			    }		
			}			
			
		}	
		
		private function roleWorkGetAwardCommandComplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, roleWorkGetAwardCommandComplete);
			_view.mouseEnabled = true;
			_view.mouseChildren = true;
			
			var vectlist:Vector.<ConditionVo> = new Vector.<ConditionVo>();
			for (var i:int = 0; i < e.target.awards.length; i++) 
			{
				vectlist.push(e.target.awards[i]);
			}
			var itemDropController:ItemDropController = new ItemDropController(sp1);
			itemDropController.drop(vectlist, vectpoint, new Point(), 30, 30, false);
			cleartitle();
		}
		
		public function setData():void
		{
			var loader:Loader = new Loader();
			loader.contentLoaderInfo.addEventListener(Event.COMPLETE, loadFace_complete);
			loader.contentLoaderInfo.addEventListener(IOErrorEvent.IO_ERROR, loadFace_ioError);
			try {
				loader.load(new URLRequest(RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.bg));
			}catch (e:Error) {
				
			}
			
			new RoleWorkDrag(sp1);
	
		}
		
		private function loadFace_ioError(e:IOErrorEvent):void 
		{
			e.target.removeEventListener(Event.COMPLETE, loadFace_complete);
			e.target.removeEventListener(IOErrorEvent.IO_ERROR, loadFace_ioError);
			
			dispatchEvent(new Event(Event.COMPLETE));			
		}
		
		private function loadFace_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, loadFace_complete);
			e.target.removeEventListener(IOErrorEvent.IO_ERROR, loadFace_ioError);
			
			var i:int;
			
			var tmpbt:Loader = e.target.loader as Loader;
			sp1.addChild(tmpbt);
			tmpbt.x = -tmpbt.width / 2;
			tmpbt.y = -tmpbt.height / 2;
			viewx = -tmpbt.width / 2;
			viewy = -tmpbt.height / 2;
			var mcClass:Class = SwfClassCache.getInstance().getClass("WorldMapcloseBtn");
			
			btn= new mcClass() as DisplayObject;
			btn.name = "closebtn";
			_view.addChild(btn);
			
			if (_view.stage.displayState == StageDisplayState.NORMAL)
			{
				btn.x =  _view.stage.stageWidth / 2 -65;
				btn.y =  _view.stage.stageHeight / 2  -560;				
			}
			else
			{
				btn.x =  530;
				btn.y =  -250;						
			}
			
			var mcClass1:Class = SwfClassCache.getInstance().getClass("worldtitle");
			
			title = new mcClass1() as DisplayObject;
			_view.addChild(title);

			
			if (_view.stage.displayState == StageDisplayState.NORMAL)
			{
				title.x =  _view.stage.stageWidth / 2 -560;
				title.y =  _view.stage.stageHeight / 2  -590;				
			}
			else
			{
				title.x =  -177;
				title.y =  -286;						
			}			
			
			var arr:Array = RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass;
			
			for (i = 0; i < arr.length; i++) 
			{
				var staticvo:RoleWorkPointClassVo = arr[i] as RoleWorkPointClassVo;
				
			    var tmpclass:Class = SwfClassCache.getInstance().getClass(staticvo.iconClass);
			    var icon:MovieClip = new tmpclass() as MovieClip;
				icon.buttonMode = true;
				icon.x = staticvo.x;
				icon.y = staticvo.y;
				icon.mouseChildren = false;
				icon.name = staticvo.iconClass;
				firarr = icon.filters;
				
			    sp1.addChild(icon);								
				
				if (!RoleWorkManager.getInstance().roleWorkData.getRoleWorkVo(staticvo.id))
				{
					BtnStateControl.setBtnState(icon, false);
				}
				else
				{
					var vo:RoleWorkPointVo = RoleWorkManager.getInstance().roleWorkData.getRoleWorkVo((arr[i] as RoleWorkPointClassVo).id);
					var namestr:String = staticvo.iconClass +"1";
					if (vo.state == 2)
					{
						if (Time.getRemainingTimeByEnd(vo.time))
						{
							var RoleWorkingPromptmc:RoleWorkingPrompt = new RoleWorkingPrompt();
							RoleWorkingPromptmc.timer.text = DateTools.getLostTime(Time.getRemainingTimeByEnd(vo.time)*1000, true, ":", ":", ":", " ");
							RoleWorkingPromptmc.buttonMode = true;
							RoleWorkingPromptmc.mouseChildren = false;			
							RoleWorkingPromptmc.name = namestr;
							var rolevo:RoleVo = DataManager.getInstance().roleData.getRole(vo.roleIds[0]);
							loadIcon(rolevo.className,RoleWorkingPromptmc);
							sp1.addChild(RoleWorkingPromptmc);
							RoleWorkingPromptmc.x = staticvo.x;
							RoleWorkingPromptmc.y = staticvo.y - icon.height / 4;
							new FloatController(RoleWorkingPromptmc,5,1);
						}
						else
						{
							var RoleWorkCompletePromptmc:RoleWorkCompletePrompt = new RoleWorkCompletePrompt();
							RoleWorkCompletePromptmc.buttonMode = true;
							RoleWorkCompletePromptmc.mouseChildren = false;
							RoleWorkCompletePromptmc.name = namestr;
							sp1.addChild(RoleWorkCompletePromptmc);
							RoleWorkCompletePromptmc.x = staticvo.x;
							RoleWorkCompletePromptmc.y = staticvo.y - icon.height / 4;	
							new FloatController(RoleWorkCompletePromptmc,5,1);
						}
					}
					else
					{
						var point:RoleWorkPointVo = RoleWorkManager.getInstance().roleWorkData.getRoleWorkVo(arr[i].id);
						if (isRandomItem(point))
						{
							var mcClass2:Class = SwfClassCache.getInstance().getClass("awardIcon_UnknowIcon");	
							var UnKnowIcon:MovieClip = new mcClass2() as MovieClip;
							sp1.addChild(UnKnowIcon);
							UnKnowIcon.x = staticvo.x;
							UnKnowIcon.y = staticvo.y - icon.height / 4;
							new FloatController(UnKnowIcon,5,1);
						}						
					}
				}	
			}		
				timer.start();					
		}
		
		private function loadIcon(classname:String,mc:MovieClip):void 
		{
			var icon:IconView = new IconView(30, 30, new Rectangle(-16.7, -43.15, 30, 30));
			icon.setData(classname);
			mc.addChild(icon);			
		}

		public function close():void
		{
			_view.stage.removeEventListener(FullScreenEvent.FULL_SCREEN	, fullresize);		
			EventManager.getInstance().removeEventListener(RoleWorkEvent.ROLEWORKQUICKCOMPLETE, roleworkquickcomplete);
			EventManager.getInstance().removeEventListener(RoleWorkEvent.ROLEUPDATA, roleupdatacomplete);
			EventManager.getInstance().dispatchEvent(new RoleWorkEvent(RoleWorkEvent.ROLEWORKMOUDLECLOSE));		
			closeMe(true);
		}
		
		public function updataTime(time:int):String
		{
			var timeStr:String;
			var timeS:int;
			var timeH:int;
			
			if (time > 3600)
			{
				timeH = time / 3600;
				timeS = (time - timeH * 3600) / 60;
				timeStr = timeH + LocaleWords.getInstance().getWord("hour") + timeS + LocaleWords.getInstance().getWord("minutes");
			}
			else
			{
				timeS = time / 60;
				timeStr = timeS + LocaleWords.getInstance().getWord("minutes");
			}
			return timeStr;
		}
		
		private function updata():void
		{
			var arr:Array = RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass;
			var i:int;
			for (i = 0; i < arr.length; i++) 
			{			
				var staticvo:RoleWorkPointClassVo = arr[i] as RoleWorkPointClassVo;
				if (RoleWorkManager.getInstance().roleWorkData.getRoleWorkVo(staticvo.id))
				{
					var vo:RoleWorkPointVo = RoleWorkManager.getInstance().roleWorkData.getRoleWorkVo((arr[i] as RoleWorkPointClassVo).id);
					var namestr:String = staticvo.iconClass +"1";
					var tmpclass:Class = SwfClassCache.getInstance().getClass(staticvo.iconClass);
					var icon:MovieClip = new tmpclass() as MovieClip;
					
					if (vo.state == 2)
					{					
						if (Time.getRemainingTimeByEnd(vo.time))
						{
							var data:Date = new Date();
							data.time = vo.time * 1000;
							var test1:int = Time.getRemainingTimeByEnd(vo.time);
							var RoleWorkingPromptmc:RoleWorkingPrompt = new RoleWorkingPrompt();
							
							RoleWorkingPromptmc.timer.text = DateTools.getLostTime(Time.getRemainingTimeByEnd(vo.time)*1000, true, ":", ":", ":", " ");
							RoleWorkingPromptmc.buttonMode = true;
							RoleWorkingPromptmc.mouseChildren = false;			
							RoleWorkingPromptmc.name = namestr;
							var rolevo:RoleVo = DataManager.getInstance().roleData.getRole(vo.roleIds[0]);
							loadIcon(rolevo.className,RoleWorkingPromptmc);
							sp1.addChild(RoleWorkingPromptmc);
							RoleWorkingPromptmc.x = staticvo.x;
							RoleWorkingPromptmc.y = staticvo.y - icon.height / 4;
							new FloatController(RoleWorkingPromptmc,5,1);
						}
						else
						{
							var RoleWorkCompletePromptmc:RoleWorkCompletePrompt = new RoleWorkCompletePrompt();
							RoleWorkCompletePromptmc.buttonMode = true;
							RoleWorkCompletePromptmc.mouseChildren = false;
							RoleWorkCompletePromptmc.name = namestr;
							sp1.addChild(RoleWorkCompletePromptmc);
							RoleWorkCompletePromptmc.x = staticvo.x;
							RoleWorkCompletePromptmc.y = staticvo.y - icon.height / 4;	
							new FloatController(RoleWorkCompletePromptmc,5,1);
						}
					}	
					else
					{
						var point:RoleWorkPointVo = RoleWorkManager.getInstance().roleWorkData.getRoleWorkVo(arr[i].id);
						if (isRandomItem(point))
						{
							var mcClass2:Class = SwfClassCache.getInstance().getClass("awardIcon_UnknowIcon");	
							var UnKnowIcon:MovieClip = new mcClass2() as MovieClip;
							sp1.addChild(UnKnowIcon);
							UnKnowIcon.x = staticvo.x;
							UnKnowIcon.y = staticvo.y - icon.height / 4;
							new FloatController(UnKnowIcon,5,1);
						}
					}
				}	
			}					
		}
		
		//是否有随机物品
		private function isRandomItem(rpvo:RoleWorkPointVo):Boolean 
		{
			for (var i:int = 0; i < rpvo.awards.length; i++) 
			{
				if ((rpvo.awards[i] as ConditionVo).type == ConditionType.UNKNOW)
				{
					return true;
				}
			}
			return false;
		}
			
	}

}