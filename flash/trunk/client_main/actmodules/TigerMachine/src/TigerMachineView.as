package  
{
	import br.com.stimuli.loading.loadingtypes.VideoItem;
	import com.greensock.TweenMax;
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.utils.setTimeout;
	import happyfish.display.ui.FaceView;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.AlginType;
	import happyfish.utils.display.McShower;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.RoleVo;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.FriendActionType;
	/**
	 * ...
	 * @author ZC
	 */
	public class TigerMachineView extends UISprite
	{
		private var iview:TigerMachineViewUi;
		private var sp1:TigerMachineSprite;
		private var sp2:TigerMachineSprite;		
		private var sp3:TigerMachineSprite;		
		private var sp4:TigerMachineSprite;		
		private var sp5:TigerMachineSprite;		
		private var sp6:TigerMachineSprite;
		private var sp11:myrole;
		private var sp22:myrole;		
		private var sp33:myrole;		
		private var sp44:friendrole;		
		private var sp55:friendrole;		
		private var sp66:friendrole;		
		
		private var type:int;
		private var frienduserid:int;
		private var friendbuildid:int;
		private var allspritenum:int;
		private var spritenum:int = 0;
		private var mylevelarr:Array;
		private var enemylevelarr:Array;
		private var fightmc:fightMc;
		private var vsMC:Fightvs;
		
		private var friendname:String;
		private var friendfaceclass:String;
		
		private var myRolesArray:Array;
		private var friendarray:Array;
		public function TigerMachineView() 
		{
			_view = new MovieClip();
			iview = new TigerMachineViewUi();
			
			_view.addChild(iview);
			TextFieldUtil.autoSetTxtDefaultFormat(_view);
			iview.x = -iview.width / 2;
			iview.y = -iview.height / 2;
			iview.yaogan.mouseChildren = false;
			iview.buttonMode = true;
			iview.yaogan.stop();
			//iview.yaogan["yaogan1"].stop();
			iview.yaogan["yaogan1"]["yaogan2"].stop();
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			EventManager.getInstance().addEventListener(TigerMachineEvent.TIGERCOMPLETE, tigercomplete);
			
			
			
			//iview.myground.visible = false;
			//iview.friendground.visible = false;
		}
		
		private function tigercomplete(e:TigerMachineEvent):void 
		{
			var icon:IconView;				
			
			if (sp1)
			{   
				if (sp1.complete)
				{
					if (sp2)
					{
						sp2.startslow();
						if (!sp2)
						{
							sp2.last = 1;
						}
					}

					sp1.complete = false;
				}			
			}
			
			if (sp2)
			{
				if (sp2.complete)
				{
					if (sp3)
					{
						sp3.startslow();	
						sp3.last = 1;
					}

					sp2.complete = false;
				}					
			}
			
			if (sp3)
			{
				if (sp3.complete)
				{
                    sp3.complete = false;
				}
			
			}
			
			if (sp4)
			{
				if (sp4.complete)
				{
					if (sp5)
					{
						sp5.startslow();	
						if (!sp6)
						{
							sp5.last = 1;
						}
					}

					sp4.complete = false;
				}
				
				
			}
			
			if (sp5)
			{
				if (sp5.complete)
				{
					if (sp6)
					{
						sp6.startslow();
						sp6.last = 1;
					}

					sp5.complete = false;
				}
			
			}	
			
			if (sp6)
			{
				if (sp6.complete)
				{
					sp6.complete = false;
				}		
			}		
			
			
			spritenum++;
			if (spritenum == allspritenum)
			{
				
				if (sp1)
				{   
					sp11 = new myrole();
					sp11.mc["level"].text = getMyRole(mylevelarr[0]).level.toString();
					sp11.jobIcon.gotoAndStop(getMyRole(mylevelarr[0]).profession);
					sp11.propIcon.gotoAndStop(getMyRole(mylevelarr[0]).prop);
					sp11.starLevel.gotoAndStop(getMyRole(mylevelarr[0]).quality);
					icon = new IconView(55,55,new Rectangle(-26,-45,50,50));
					icon.setData(getMyRole(mylevelarr[0]).className);
					sp11.addChild(icon);
					_view.addChild(sp11);
					sp11.x = sp1.x - iview.width / 2+ sp11.width / 2 ;	
					sp11.y = sp1.y + 91 -iview.height / 2 + sp11.height / 2 -1;				
				}
			
				if (sp2)
				{
					sp22 = new myrole(); 
					sp22.mc["level"].text = getMyRole(mylevelarr[1]).level.toString();
					sp22.jobIcon.gotoAndStop(getMyRole(mylevelarr[1]).profession);
					sp22.propIcon.gotoAndStop(getMyRole(mylevelarr[1]).prop);
					sp22.starLevel.gotoAndStop(getMyRole(mylevelarr[1]).quality);					
					
					icon = new IconView(55,55,new Rectangle(-26,-45,50,50));
					icon.setData(getMyRole(mylevelarr[1]).className);
					sp22.addChild(icon);
					_view.addChild(sp22);				
					sp22.x = sp2.x - iview.width / 2+ sp22.width / 2 ;	;
					sp22.y = sp2.y + 91 -iview.height / 2 + sp22.height / 2 - 1;				
				}
			
				if (sp3)
				{
					sp33 = new myrole();
					sp33.mc["level"].text = getMyRole(mylevelarr[2]).level.toString();
					sp33.jobIcon.gotoAndStop(getMyRole(mylevelarr[2]).profession);
					sp33.propIcon.gotoAndStop(getMyRole(mylevelarr[2]).prop);
					sp33.starLevel.gotoAndStop(getMyRole(mylevelarr[2]).quality);
					
					icon = new IconView(55,55,new Rectangle(-26,-45,50,50));
					icon.setData(getMyRole(mylevelarr[2]).className);
					sp33.addChild(icon);
					_view.addChild(sp33);
					sp33.x = sp3.x - iview.width / 2 + sp33.width / 2;
					sp33.y = sp3.y + 91 -iview.height / 2 + sp33.height / 2 - 1;	
				}	
				
				if (sp4)
				{
					sp44 = new friendrole();
					sp44.mc["level"].text = getEnemyRole(enemylevelarr[0]).level.toString();
					sp44.jobIcon.gotoAndStop(getEnemyRole(enemylevelarr[0]).profession);
					sp44.propIcon.gotoAndStop(getEnemyRole(enemylevelarr[0]).prop);
					sp44.starLevel.gotoAndStop(getEnemyRole(enemylevelarr[0]).quality);					
					
					icon = new IconView(55,55,new Rectangle(-26,-45,50,50));
					icon.setData(getEnemyRole(enemylevelarr[0]).className);
					sp44.addChild(icon);
					_view.addChild(sp44);	
					sp44.x = sp4.x - iview.width / 2+ sp44.width / 2;
					sp44.y = sp4.y + 91 -iview.height / 2 + sp44.height / 2 - 2;
				}				
				
				if (sp5)
				{
					sp55 = new friendrole();
					sp55.mc["level"].text = getEnemyRole(enemylevelarr[1]).level.toString();
					sp55.jobIcon.gotoAndStop(getEnemyRole(enemylevelarr[1]).profession);
					sp55.propIcon.gotoAndStop(getEnemyRole(enemylevelarr[1]).prop);
					sp55.starLevel.gotoAndStop(getEnemyRole(enemylevelarr[1]).quality);					
				
					icon = new IconView(55,55,new Rectangle(-26,-45,50,50));
					icon.setData(getEnemyRole(enemylevelarr[1]).className);
					sp55.addChild(icon);
					_view.addChild(sp55);	
					sp55.x = sp5.x - iview.width / 2+ sp55.width / 2;
					sp55.y = sp5.y + 91 -iview.height / 2 + sp55.height / 2 - 2;			
				}					
				
				if (sp6)
				{
					sp66 = new friendrole();
					sp66.mc["level"].text = getEnemyRole(enemylevelarr[2]).level.toString();
					sp66.jobIcon.gotoAndStop(getEnemyRole(enemylevelarr[2]).profession);
					sp66.propIcon.gotoAndStop(getEnemyRole(enemylevelarr[2]).prop);
					sp66.starLevel.gotoAndStop(getEnemyRole(enemylevelarr[2]).quality);					
					
					icon = new IconView(55,55,new Rectangle(-26,-45,50,50));
					icon.setData(getEnemyRole(enemylevelarr[2]).className);
					sp66.addChild(icon);
					_view.addChild(sp66);
					sp66.x = sp6.x - iview.width / 2+ sp66.width / 2;
					sp66.y = sp6.y + 91 -iview.height / 2 + sp66.height / 2 - 2;		
				}	
				
				readygo();
			}			
			
		}
		
		private function readygo():void 
		{
			//准备界面变黑
			
			TweenMax.to(iview, 0.3, {alpha:0.5,onComplete:iviewcolorcomplete});
		}
		
		private function iviewcolorcomplete():void 
		{		
			vsMC = new Fightvs();
			_view.addChild(vsMC);
			vsMC.x = -iview.width / 2;
			vsMC.y = -iview.height / 2 +50;
			
			vsMC.addEventListener(Event.ENTER_FRAME, vsMCplay);
			
			if (sp1)
			{
				TweenMax.to(sp11, 0.4, {delay:0.1, x: sp11.x , y: sp11.y -200, scaleX:1.3, scaleY:1.3 } );				
			}
			
			if (sp2)
			{				
				TweenMax.to(sp22, 0.4, {delay:0.15, x: sp22.x+40 , y: sp22.y -200, scaleX:1.3, scaleY:1.3 } );
			}			
			
			if (sp3)
			{	
				TweenMax.to(sp33, 0.4, {delay:0.20, x: sp33.x+80 , y: sp33.y -200, scaleX:1.3, scaleY:1.3 } );
			}			
			
			if (sp4)
			{	
				TweenMax.to(sp44, 0.4, {delay:0.1, x: sp44.x-30, y: sp44.y +148, scaleX:1.3, scaleY:1.3 } );
			}			
			
			if (sp5)
			{	
				TweenMax.to(sp55, 0.4, {delay:0.15, x: sp55.x+10 , y: sp55.y +148, scaleX:1.3, scaleY:1.3 } );
			}			
			
			if (sp6)
			{	
				TweenMax.to(sp66, 0.4, {delay:0.20, x: sp66.x+50 , y: sp66.y +148, scaleX:1.3, scaleY:1.3 } );
			}			
				
			setTimeout(showfightview, 800);
			
			
		}
		
		private function vsMCplay(e:Event):void 
		{
			if (e.target.currentFrame == 76)
			{
				vsMC.removeEventListener(Event.ENTER_FRAME, vsMCplay);		
				vsMC.stop();
			}
		}
		
		private function fightmcframe(e:Event):void 
		{
			if (e.target.currentFrame == 31)
			{
				fightmc.removeEventListener(Event.ENTER_FRAME, fightmcframe);
				fightmc.stop();			
			    ActModuleManager.getInstance().addActModule(DataManager.getInstance().getActByName("battle"));	
			    close();				
			}				
		}
		
		private function showfightview():void 
		{
			fightmc = new fightMc();
			_view.addChild(fightmc);
			fightmc.x = -iview.width / 2;
			fightmc.y = -iview.height / 2;
			
			
			fightmc.addEventListener(Event.ENTER_FRAME, fightmcframe);
			
		}
		
		private function clickrun(e:MouseEvent):void
		{
			switch(e.target.name)
			{
				case "closebtn":
					close();
					break;
					
				case "yaogan":
					iview.mouseChildren = false;
					iview.mouseEnabled = false;
					
					iview.yaogan["yaogan1"]["yaogan2"].gotoAndPlay(1);
					iview.yaogan["yaogan1"]["yaogan2"].addEventListener(Event.ENTER_FRAME,yaoganplay);
					if (sp1)
					{
					    sp1.readgo();
					}
			
					if (sp2)
					{
					    sp2.readgo();
					}	
			
					if (sp3)
					{
					    sp3.readgo();
					}	
			
					if (sp4)
					{
					    sp4.readgo();
					}
			
					if (sp5)
					{
					    sp5.readgo();
					}	
			
					if (sp6)
					{
					    sp6.readgo();
					}	
					
					break;
					
				case "help":
			 	    var view:TigerMachineHelpView = DisplayManager.uiSprite.addModule("TigerMachineHelpView", "TigerMachineHelpView", false, AlginType.CENTER, 20, -10) as TigerMachineHelpView;
			 		DisplayManager.uiSprite.setBg(view);					
					break;
					
				case "test":
					ActModuleManager.getInstance().addActModule(DataManager.getInstance().getActByName("battle"));	
					close();
					break;
			}
		}
		
		private function yaoganplay(e:Event):void 
		{
			if (e.target.currentFrame == 12)
			{
				iview.yaogan["yaogan1"]["yaogan2"].removeEventListener(Event.ENTER_FRAME, yaoganplay);
				
				iview.yaogan["yaogan1"]["yaogan2"].stop();
				
			    var command:TigerMachineReadyCommond = new TigerMachineReadyCommond();
				command.setData(frienduserid,friendbuildid,type);
				command.addEventListener(Event.COMPLETE, commandComplete);				
			}			
		}
		
		private function close():void 
		{
			closeMe(true);
			EventManager.getInstance().dispatchEvent(new TigerMachineEvent(TigerMachineEvent.TIGERMACHCLOSEMOUDLUE));
			EventManager.getInstance().removeEventListener(TigerMachineEvent.TIGERCOMPLETE, tigercomplete);
		}
		
		private function commandComplete(e:Event = null):void 
		{
			e.target.removeEventListener(Event.COMPLETE, commandComplete);
			
			iview.mouseChildren = true;
			iview.mouseEnabled = true;
			
			var mylevelall:int;
			var i:int;
			mylevelarr = DataManager.getInstance().getVar("myRolesId");
			allspritenum += mylevelarr.length;
			
			for (i = 0; i < mylevelarr.length; i++) 
			{
				var mylevel:int = getMyRole(mylevelarr[i]).level;
				mylevelall += mylevel;
			}
			
			var enemylevelall:int;
			
			enemylevelarr = DataManager.getInstance().getVar("enemyRolesId");
			allspritenum += enemylevelarr.length;
			
			for (i = 0; i < enemylevelarr.length; i++) 
			{
				var enemylevel:int = getEnemyRole(enemylevelarr[i]).level;
				enemylevelall += enemylevel;
			}			
			
			var levelcha:int;
			levelcha = mylevelall - enemylevelall;
			
			
			
			if (levelcha<=-30)
			{
				iview.fightname.text = LocaleWords.getInstance().getWord("TigerMachineword1");
			}
			else if (levelcha <= -26)
			{
				iview.fightname.text = LocaleWords.getInstance().getWord("TigerMachineword2");
			}
			else if (levelcha <= -21)
			{
				iview.fightname.text = LocaleWords.getInstance().getWord("TigerMachineword3");
			}
			else if (levelcha <= -11)
			{
				iview.fightname.text = LocaleWords.getInstance().getWord("TigerMachineword4");
			}				
			else if (levelcha <= -6)
			{
				iview.fightname.text = LocaleWords.getInstance().getWord("TigerMachineword5");
			}				
			else if (levelcha <= -1)
			{
				iview.fightname.text = LocaleWords.getInstance().getWord("TigerMachineword6");
			}				
			else if (levelcha <= 1)
			{
				iview.fightname.text = LocaleWords.getInstance().getWord("TigerMachineword6");
			}				
			else if (levelcha <= 6)
			{
				iview.fightname.text = LocaleWords.getInstance().getWord("TigerMachineword7");
			}				
			else if (levelcha <= 11)
			{
				iview.fightname.text = LocaleWords.getInstance().getWord("TigerMachineword8");
			}				
			else if (levelcha <= 21)
			{
				iview.fightname.text = LocaleWords.getInstance().getWord("TigerMachineword9");
			}				
			else if (levelcha <= 26)
			{
				iview.fightname.text = LocaleWords.getInstance().getWord("TigerMachineword10");
			}				
			else if (levelcha <= 30)
			{
				iview.fightname.text = LocaleWords.getInstance().getWord("TigerMachineword11");
			}				
			
			
			if (sp1)
			{
			    sp1.start(mylevelarr[0]);
			}
			
			if (sp2)
			{
				sp2.start(mylevelarr[1]);
			}	
			
			if (sp3)
			{
				sp3.start(mylevelarr[2]);
			}	
			
			if (sp4)
			{
				sp4.start(enemylevelarr[0]);
			}
			
			if (sp5)
			{
				sp5.start(enemylevelarr[1]);
			}	
			
			if (sp6)
			{
				sp6.start(enemylevelarr[2]);
			}				
			
			setTimeout(spStop,500);
			
			
		}
		
		private function spStop():void 
		{
			sp1.startslow();
			
			if (!sp2)
			{
				sp1.last = 1;
			}
			
			sp4.startslow();
			
			if (!sp5)
			{
				sp4.last = 1;
			}			
			
		}
		
		public function setData(_enemyId:int,_buildid:int,_name:String,_faceclassname:String,_type:int):void
		{
			friendname = _name;
			friendfaceclass = _faceclassname;
			frienduserid = _enemyId;
			friendbuildid = _buildid;
			
			//自己的角色初始化
		    myRolesArray = DataManager.getInstance().getVar("myRolesArray");
			
            //var all:int = array.length % 3;
			
			for (var i:int = 0; i < myRolesArray.length; i++) 
			{
				if (i == 0)
				{
					sp1 = new TigerMachineSprite(1);

					iview.addChildAt(sp1,iview.getChildIndex(iview.myground));
					sp1.x = -285;
					sp1.y = -67;
				}
				
				if (i == 1)
				{
					sp2 = new TigerMachineSprite(1);
					sp2.x = -210;
					sp2.y = -67;
					iview.addChildAt(sp2,iview.getChildIndex(iview.myground));
				}
				
				if (i == 2)
				{
					sp3 = new TigerMachineSprite(1);
					sp3.x = -135;
					sp3.y = -67;
					iview.addChildAt(sp3,iview.getChildIndex(iview.myground));
				}				
				
			}
			
			if (sp1)
			{
			    sp1.init(myRolesArray);				
			}

			if (sp2)
			{
			    sp2.init(myRolesArray);				
			}			
			
			if (sp3)
			{
			    sp3.init(myRolesArray);				
			}			
			
			//好友角色的初始化
			friendarray = DataManager.getInstance().getVar("enemyRolesArray");
			
            //var friendall:int = friendarray.length % 3;
			
			for (i = 0; i < friendarray.length; i++) 
			{
				if (i == 0)
				{
					sp4 = new TigerMachineSprite(2);

					iview.addChildAt(sp4,iview.getChildIndex(iview.friendground));
					sp4.x = 62;
					sp4.y = -67;
				}
				
				if (i == 1)
				{
					sp5 = new TigerMachineSprite(2);
					sp5.x = 137;
					sp5.y = -67;
					iview.addChildAt(sp5,iview.getChildIndex(iview.friendground));
				}
				
				if (i == 2)
				{
					sp6 = new TigerMachineSprite(2);
					sp6.x = 211;
					sp6.y = -67;
					iview.addChildAt(sp6,iview.getChildIndex(iview.friendground));
				}				
				
			}
			
			if (sp4)
			{
			    sp4.init(friendarray);				
			}

			if (sp5)
			{
			    sp5.init(friendarray);				
			}			
			
			if (sp6)
			{
			    sp6.init(friendarray);				
			}				
			
			itemMyIcon();
			
			itemFriendIcon(_enemyId);
			
			switch(_type)
			{
				case FriendActionType.OCC:
					iview.title.gotoAndStop(1);
					  type = 1;
					break;
					
				case FriendActionType.ASSISTANCE:
					iview.title.gotoAndStop(3);
					  type = 3;
					break;					
					
				case FriendActionType.RESIST:
					iview.title.gotoAndStop(2);
					  type = 2;
					break;					
			}
			
		}
		
		private function itemFriendIcon(_enemyId:int):void 
		{
			//var user:UserVo = DataManager.getInstance().getFriendUserVo(_enemyId.toString());
			iview.friendname.text = friendname;	
			
			var faceicon:FaceView = new FaceView(45);
			faceicon.loadFace(friendfaceclass);
			iview.addChild(faceicon);
			faceicon.x = 219.3;
			faceicon.y = -175.35;
		}
		
		private function itemMyIcon():void 
		{
			iview.myname.text = DataManager.getInstance().currentUser.name;	
			
			var faceicon:FaceView = new FaceView(45);
			faceicon.loadFace(DataManager.getInstance().currentUser.face);
			iview.addChild(faceicon);
			faceicon.x = -252.55;
			faceicon.y = -175.55;			
		}
		
		private function getMyRole(_id:int):RoleVo
		{
            var array:Array = DataManager.getInstance().getVar("myRolesArray");		
			
			for (var i:int = 0; i < array.length; i++) 
			{
				if ((array[i] as RoleVo).id == _id)
				{
					return array[i];
				}
			}
			return null;
		}

		private function getEnemyRole(_id:int):RoleVo
		{
            var array:Array = DataManager.getInstance().getVar("enemyRolesArray");		
			
			for (var i:int = 0; i < array.length; i++) 
			{
				if ((array[i] as RoleVo).id == _id)
				{
					return array[i];
				}
			}
			return null;
			
		}		
		
	}

}