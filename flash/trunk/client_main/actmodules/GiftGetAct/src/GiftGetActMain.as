package 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.actModule.giftGetAct.commond.GiftGetActInitCommond;
	import happyfish.actModule.giftGetAct.commond.GiftGetActInitStaticCommond;
	import happyfish.actModule.giftGetAct.event.GiftGetActEvent;
	import happyfish.actModule.giftGetAct.GiftGetActDict;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happyfish.actModule.giftGetAct.model.vo.GiftDiaryVo;
	import happyfish.actModule.giftGetAct.model.vo.GiftRequestVo;
	import happyfish.actModule.giftGetAct.model.vo.GiftUserVo;
	import happyfish.actModule.giftGetAct.model.vo.GiftVo;
	import happyfish.actModule.giftGetAct.view.giftGetAct.GiftGetActView;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happymagic.display.view.RightCenterMenuView;
	import happymagic.events.MagicEvent;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.TestCommand;
	import happymagic.scene.world.SceneType;
	
	/**
	 * ...
	 * @author ZC
	 */
	
	 //其他项目请将ActModuleBase改成sprite
	public class GiftGetActMain extends ActModuleBase 
	{
		private var state:uint;
		private var giftgetbtn:GiftGetActBtn;
		private var actvo:ActVo;
		private var tips3:gifttips3;
		public function GiftGetActMain():void 
		{
			//其他项目请注释
			EventManager.getInstance().addEventListener(GiftGetActEvent.CLOSE, fullClose);
		}
		
		//每个版本的init都不一样 请自己重新改写
		override public function init(actVo:ActVo, _type:uint = 1):void 
		{
			actvo = actVo;
			GiftDomain.getInstance().setVar("firstBoolean", true);				
			
			super.init(DataManager.getInstance().getActByName("giftact"), 1);		
			
			GiftDomain.getInstance().stage = stage;	
			
			var loopback:Boolean = false;
			GiftDomain.getInstance().setVar("loopbackboolean", loopback);
			
			//游戏内部调用此模块
			EventManager.getInstance().addEventListener("giftActEventStart", giftgetbtnclick);
			var giftGetActInitStaticCommond:GiftGetActInitStaticCommond = new GiftGetActInitStaticCommond();
			giftGetActInitStaticCommond.setData();
			giftGetActInitStaticCommond.addEventListener(Event.COMPLETE, initstaticcommondcomplete1);
			
			
			if (DisplayManager.uiSprite.getModule("rightCenterMenu"))
			{
				giftgetbtn = new GiftGetActBtn();
				giftgetbtn.addEventListener(MouseEvent.CLICK, giftgetbtnclick);		
			    giftgetbtn.addEventListener(MouseEvent.MOUSE_OVER,giftgetbtnover);	
			    giftgetbtn.addEventListener(MouseEvent.MOUSE_OUT, giftgetbtnout);
				giftgetbtn.tips1.visible = false;
				(DisplayManager.uiSprite.getModule("rightCenterMenu") as RightCenterMenuView).addMc("giftbtn", giftgetbtn, 100);
				
				tips3 = new gifttips3();
				tips3.x= -41;
				tips3.y = -1;
				giftgetbtn.addChild(tips3);
				tips3.visible = false;
				if (actvo.moduleData.giftNum)
				{
					var giftuservo:GiftUserVo = GiftDomain.getInstance().getVar("giftUserVo");
					giftgetbtn.numbertips["num"].text = String(actvo.moduleData.giftNum);
             	    EventManager.getInstance().addEventListener(GiftGetActEvent.CLOSE_NUMBERSHOW,closenumbershow);					
				}
				else
				{
					giftgetbtn.numbertips.visible = false;
				}				
				
				
			}
			else
			{
				EventManager.getInstance().addEventListener(MagicEvent.RIGHT_CENTER_INIT, initbtncomplete);
			}
			
		}
		
		//侦听右边菜单栏完成事件
		private function initbtncomplete(e:MagicEvent):void 
		{
			giftgetbtn = new GiftGetActBtn();
			giftgetbtn.addEventListener(MouseEvent.CLICK, giftgetbtnclick);		
			giftgetbtn.addEventListener(MouseEvent.MOUSE_OVER,giftgetbtnover);	
			giftgetbtn.addEventListener(MouseEvent.MOUSE_OUT, giftgetbtnout);
		    //giftgetbtn.tips1.visible = false;
			
		    (DisplayManager.uiSprite.getModule("rightCenterMenu") as RightCenterMenuView).addMc("giftbtn", giftgetbtn, 100);
			
			tips3 = new gifttips3();
			tips3.x= -41;
			tips3.y = -1;
			giftgetbtn.addChild(tips3);		
			tips3.visible = false;
			if (actvo.moduleData.giftNum)
			{
				var giftuservo:GiftUserVo = GiftDomain.getInstance().getVar("giftUserVo");
				giftgetbtn.numbertips["num"].text = String(giftuservo.giftNum);
             	EventManager.getInstance().addEventListener(GiftGetActEvent.CLOSE_NUMBERSHOW,closenumbershow);					
			}
			else
			{
				giftgetbtn.numbertips.visible = false;
			}				
			
		}
		
		private function initstaticcommondcomplete1(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, initstaticcommondcomplete1);
		    go();
		}
		
		private function go():void 
		{
             //TODO 将礼物的按钮放入游戏主界面上	------------------------------------------------------------			
		}
			
		private function giftgetbtnclick(e:Event = null):void 
		{
			giftgetbtn.mouseChildren = false;
			giftgetbtn.mouseEnabled = false;
			
			if (e is MouseEvent)
			{
				state = 0;
			}
			else
			{
				state = 3;
			}
			
			start();			
		}
		
		//其他项目请注释
		private function fullClose(e:GiftGetActEvent):void 
		{
			close();
		}
		
		//其他项目请注释
		override public function close():void 
		{
			super.close();
		}
		
		public function start():void
		{		
			if (GiftDomain.getInstance().getVar("gifts"))
			{
				var giftgetinitcommond:GiftGetActInitCommond = new GiftGetActInitCommond();
				giftgetinitcommond.setData();
				giftgetinitcommond.addEventListener(Event.COMPLETE, initcommondcomplete2);					
			}
			else
			{
				var giftGetActInitStaticCommond:GiftGetActInitStaticCommond = new GiftGetActInitStaticCommond();
				giftGetActInitStaticCommond.setData();
				giftGetActInitStaticCommond.addEventListener(Event.COMPLETE, initstaticcommondcomplete2);				
			}

			
		}
		
		private function initstaticcommondcomplete2(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, initstaticcommondcomplete2);
			var giftgetinitcommond:GiftGetActInitCommond = new GiftGetActInitCommond();
			giftgetinitcommond.setData();
			giftgetinitcommond.addEventListener(Event.COMPLETE, initcommondcomplete2);			
		}
		
		private function initcommondcomplete2(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, initcommondcomplete2);
			giftgetbtn.mouseChildren = true;
			giftgetbtn.mouseEnabled = true;
			var modlueVo:ModuleVo = new ModuleVo();
			modlueVo.name = GiftGetActDict.ACTDICT_GIFTGETACT;
			modlueVo.className = GiftGetActDict.ACTDICT_GIFTGETACT_CLASS;
			modlueVo.algin = "center";
			modlueVo.mvTime = 0.5;
			modlueVo.mvType = "fromCenter";
			modlueVo.single = false;

		    var giftgetactview:GiftGetActView = GiftDomain.getInstance().addModule(modlueVo) as GiftGetActView;
		    giftgetactview.setData(state);
			GiftDomain.getInstance().setBg(giftgetactview);					
		
		}
		
		private function closenumbershow(e:GiftGetActEvent):void 
		{
			giftgetbtn.numbertips.visible = false;			
		}
		
		private function giftgetbtnout(e:MouseEvent):void 
		{
			giftgetbtn.tips1.visible = false;	
			giftgetbtn.tips2.visible = true;
			tips3.visible = false;
		}
		
		private function giftgetbtnover(e:MouseEvent):void 
		{
			giftgetbtn.tips1.visible = true;	
		    giftgetbtn.tips2.visible = false;
			tips3.visible = true;		
		}

	}
	
}