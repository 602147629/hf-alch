package model.view 
{
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	import com.greensock.TweenMax;
	import event.WorldMapEvent;
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
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
	import happyfish.cacher.CacheSprite;
	import happyfish.cacher.SwfClassCache;
	import happyfish.display.ui.Tooltips;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.AlginType;
	import happyfish.model.SwfLoader;
	import happyfish.scene.camera.CameraControl;
	import happyfish.scene.world.control.MapDrag;
	import happyfish.scene.world.grid.Person;
	import happyfish.utils.display.BtnStateControl;
	import happyfish.utils.display.TextFieldUtil;
	import happyfish.utils.HtmlTextTools;
	import happymagic.display.view.ui.ItemIconView;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.MoveSceneCommand;
	import happymagic.model.vo.classVo.WorldMapClassVo;
	import happymagic.model.vo.SceneClassVo;
	import happymagic.model.vo.task.TaskType;
	import happymagic.model.vo.task.TaskVo;
	import happymagic.model.vo.WorldMapVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class WorldMapView extends UISprite
	{
		private var pathfind:PathFinding;
		private var pathData:Array;
		private var end:int; //终点
		private var asset:CacheSprite;
		private var goalDistance:Number;
		private var xLength:int;
		private var yLength:int;
		private var goal:Point;
		private var speed:int = 2;
		private var linespeed:int = 10;
		private var play:Point;
		private var xStep:Number;
		private var yStep:Number;		
		private var s:Number;
		private var movelinebool:Boolean = false;
		private var movebool:Boolean;//是否走动
		private var bushu:int = 0;//步数
		private var allbushu:int;//所有的步数
		private var linebushu:int = 0;//步数		
		private var senceid:String = "";
		private var sp1:Sprite;
		private var sp2:Sprite;
		private var btn:DisplayObject;
		private var title:DisplayObject;
		private var stop:int = 1;
		private var mcarray:Array;
		private var drawline:Boolean = false;
		private var spline:Sprite;
		private var temppoint:Point;
		private var isrolerun:Boolean = false;
		private var tips:WorkWorldTips;
		private var firarr:Array;
		//Person.LEFT Person.RIGHT Person.DOWN Person.UP
		public function WorldMapView() 
		{
			_view = new MovieClip();
			
			
			sp1 = new Sprite();
			_view.addChild(sp1);
		
			_view.addEventListener(MouseEvent.CLICK, clickrun);
			_view.addEventListener(MouseEvent.MOUSE_OVER, clickover);
			_view.addEventListener(MouseEvent.MOUSE_OUT, clickout);
			
			EventManager.getInstance().addEventListener(WorldMapEvent.DRAWMAPOPEN, drawmapopen);
			_view.addEventListener(Event.ADDED_TO_STAGE	, addedtostage);
		    
		}
		
		private function clickover(e:MouseEvent):void 
		{	
			for (var i:int = 0; i < DataManager.getInstance().worldData.worldmapStaticData.length; i++) 
			{
				switch(e.target.name)
			    {
				   case (DataManager.getInstance().worldData.worldmapStaticData[i] as WorldMapClassVo).iconClass:
				         if (int(senceid) != (DataManager.getInstance().worldData.worldmapStaticData[i] as WorldMapClassVo).sceneId)
					     {
						     (e.target as MovieClip).filters = [new GlowFilter(0xFF6600, 1, 10, 10, 2.5, 1, false, false)];;
					         var vo:WorldMapClassVo = DataManager.getInstance().worldData.worldmapStaticData[i] as WorldMapClassVo;
					         tips = new WorkWorldTips();
						     tips.mouseChildren = false;
						 	 tips.mouseEnabled = false;
							 TextFieldUtil.autoSetTxtDefaultFormat(tips);
							 tips.nametxt.text = (DataManager.getInstance().worldData.worldmapStaticData[i] as WorldMapClassVo).name;
							 if (isConditionRole(vo.roleConditionLevel))
							 {
						 	  	 tips.leveltxt.htmlText = vo.roleConditionLevel.toString();								 
							 }
							 else
							 {
								 tips.leveltxt.htmlText = HtmlTextTools.redWords(vo.roleConditionLevel.toString());
							 }
							 
							 if (DataManager.getInstance().currentUser.sp >= vo.sp)
							 {
						 	 	 tips.sptxt.text = (DataManager.getInstance().worldData.worldmapStaticData[i] as WorldMapClassVo).sp.toString();								 
							 }
							 else
							 {
								 tips.sptxt.htmlText = HtmlTextTools.redWords(vo.sp.toString());								 
							 }

						 	 tips.x = (DataManager.getInstance().worldData.worldmapStaticData[i] as WorldMapClassVo).x;
						 	 tips.y = (DataManager.getInstance().worldData.worldmapStaticData[i] as WorldMapClassVo).y;
						 
						 	 _view.addChild(tips);
			             	 var tmpclass:Class = SwfClassCache.getInstance().getClass((DataManager.getInstance().worldData.worldmapStaticData[i] as WorldMapClassVo).iconClass);
			             	 var icon:MovieClip = new tmpclass() as MovieClip;						 
							 tips.x =  DataManager.getInstance().worldData.worldmapStaticData[i].x + sp1.x;
						 	 tips.y =  DataManager.getInstance().worldData.worldmapStaticData[i].y - tips.height / 2 + sp1.y +10;
						 
						 	 var tipsStagepoint:Point = new Point();
						 	 tipsStagepoint.x =  sp1.stage.mouseX;
						 	 tipsStagepoint.y =  sp1.stage.mouseY;

							 if (tipsStagepoint.x -tips.width/2 < 0)
						 	 {
							 	 tips.x += tips.width;
						 	 }
						 
						 	 if (_view.stage.stageWidth - tipsStagepoint.x < tips.width/2+40)
							 {
							 	 tips.x -= tips.width;	
						 	 }

						 	 if (tipsStagepoint.y -tips.height < 0)
						 	 {
							 	 tips.y += tips.height;
						 	 }
						 
						 	 if (_view.stage.stageHeight - tipsStagepoint.y  <tips.height/2+20)
						 	 {
							 	 tips.y -= tips.height;
						 	 }									 
							 
						     end = (DataManager.getInstance().worldData.worldmapStaticData[i] as WorldMapClassVo).cid;
						     drawlink();										
					     }		
				     break;
			    }		
			}
		
		}
		
		private function drawlink():void 
		{
			if (senceid == "")
			{
				return;
			}
			var patharr:Array = pathfind.getGoArray(DataManager.getInstance().worldData.getWorldMapClassVo(int(senceid)).cid, end);
			drawpath(patharr);			
		}
		
		private function clickout(e:MouseEvent):void 
		{
			for (var i:int = 0; i < DataManager.getInstance().worldData.worldmapStaticData.length; i++) 
			{
				switch(e.target.name)
			    {
				   case (DataManager.getInstance().worldData.worldmapStaticData[i] as WorldMapClassVo).iconClass:
				         if (int(senceid) != (DataManager.getInstance().worldData.worldmapStaticData[i] as WorldMapClassVo).sceneId)
					     {
						     _view.removeChild(tips);
					        (e.target as MovieClip).filters = firarr;							 
						     end = (DataManager.getInstance().worldData.worldmapStaticData[i] as WorldMapClassVo).cid;
						    
						     drawclear();										
					     }		
				     break;
			    }		
			}			
			
		}
		
		private function drawclear():void 
		{
			if (mcarray == null)
			{
				return;
			}
			
			movelinebool = false;
			
			for (var i:int = 0; i < mcarray.length; i++) 
			{
				sp1.removeChild(mcarray[i]);			
			}	
			
			spline.graphics.clear();
			
			mcarray = new Array();
		}
		
		private function addedtostage(e:Event):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, addedtostage);
			_view.stage.addEventListener(FullScreenEvent.FULL_SCREEN	, fullresize);
		}
		
		private function fullresize(e:FullScreenEvent):void 
		{
			trace("asd");
			
			btn.x =  _view.stage.stageWidth / 2 -65;
			btn.y =  _view.stage.stageHeight / 2  -560;	
			
			title.x =  _view.stage.stageWidth / 2 -560;
			title.y =  _view.stage.stageHeight / 2  -590;				
		}
		
		private function drawmapopen(e:WorldMapEvent = null):void 
		{		
			_view.mouseChildren = false;
			_view.mouseEnabled = false;	

			_view.removeEventListener(MouseEvent.MOUSE_OVER, clickover);
			_view.removeEventListener(MouseEvent.MOUSE_OUT, clickout);			
			
			bushu = 0;
            var patharr:Array = pathfind.getGoArray(DataManager.getInstance().worldData.getWorldMapClassVo(int(senceid)).cid, end);			
			allbushu = patharr.length;
			isrolerun = true;
			CameraControl.getInstance().followTarget(asset, sp1);
			
			drawlink();
			movelinebool = true;
			movebool = true;			
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			var worldMapAffirmView:WorldMapAffirmView;
			switch(e.target.name)
			{
				case "closebtn":
					 close();
					break;
					
				case "mapicon1":
                    if (int(senceid) != 2)
					{
						end = 2;
					    startrun(DataManager.getInstance().worldData.getWorldMapClassVo(int(senceid)),e);
					}				
					return;				
					break;									
			}
			
			for (var i:int = 0; i < DataManager.getInstance().worldData.worldmapStaticData.length; i++) 
			{
				switch(e.target.name)
			    {
				   case (DataManager.getInstance().worldData.worldmapStaticData[i] as WorldMapClassVo).iconClass:
				         if (int(senceid) != (DataManager.getInstance().worldData.worldmapStaticData[i] as WorldMapClassVo).sceneId)
					     {
						     end = (DataManager.getInstance().worldData.worldmapStaticData[i] as WorldMapClassVo).cid;
                             startrun(DataManager.getInstance().worldData.worldmapStaticData[i],e);		
							 
					     }		
				     break;
			    }		
			}			
			
		}	
		
		private function startrun(_data:WorldMapClassVo,e:MouseEvent):void 
		{
			if (!isConditionRole(_data.roleConditionLevel))
			{
				DisplayManager.showSysMsg(LocaleWords.getInstance().getWord("WorldMapWord1"));
				return;
			}
					
			if (DataManager.getInstance().currentUser.sp >= _data.sp)
			{
				clickout(e);
			    EventManager.getInstance().dispatchEvent(new WorldMapEvent(WorldMapEvent.DRAWMAPOPEN));						
			}
			else
			{
				DisplayManager.showNeedMorePhysicalStrengthView();
			}				
		}
		
		public function setData():void
		{
			var loader:Loader = new Loader();
			loader.contentLoaderInfo.addEventListener(Event.COMPLETE, loadFace_complete);
			loader.contentLoaderInfo.addEventListener(IOErrorEvent.IO_ERROR, loadFace_ioError);
			try {
				loader.load(new URLRequest(DataManager.getInstance().worldData.worldmapInitData.bg));
			}catch (e:Error) {
				
			}
			//var tmpclass:Class = SwfClassCache.getInstance().getClass(loader.className);
			//var icon:MovieClip = new tmpclass() as MovieClip;
			//addChild(icon);		
			new WorldMapDrag(sp1);
			
			pathfind = new PathFinding();
			pathfind.setData(DataManager.getInstance().worldData.worldmapStaticData);
	
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
			tmpbt.x = -650;
			tmpbt.y = -300;
			
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
			
			
			
			var vo:WorldMapVo = DataManager.getInstance().worldData.worldmapInitData;
			var arr:Array = DataManager.getInstance().worldData.worldmapStaticData;
			for (i = 0; i < arr.length; i++) 
			{
				var staticvo:WorldMapClassVo = arr[i] as WorldMapClassVo;
				
			    var tmpclass:Class = SwfClassCache.getInstance().getClass(staticvo.iconClass);
			    var icon:MovieClip = new tmpclass() as MovieClip;
				icon.x = staticvo.x;
				icon.y = staticvo.y;
				icon.buttonMode = true;
				icon.name = staticvo.iconClass;
				firarr = icon.filters;
			    sp1.addChild(icon);				
				if (!DataManager.getInstance().worldData.isworldMapLock(staticvo.cid))
				{
					BtnStateControl.setBtnState(icon, false);
				}
				
			}
			
			//地图的任务图标显示
			var maparr:Array = DataManager.getInstance().worldData.worldmapStaticData;
			
			for (i = 0; i < maparr.length; i++) 
			{
				var type:int;
				
				if (DataManager.getInstance().worldData.isworldMapLock((maparr[i] as WorldMapClassVo).cid))
				{
					var task : Array = DataManager.getInstance().taskData.getTasksBySceneId((maparr[i] as WorldMapClassVo).sceneId,false,true);
					var worldmap:WorldMapClassVo = DataManager.getInstance().worldData.getWorldMapClassVo((maparr[i] as WorldMapClassVo).sceneId);
					
					if (task.length)
					{
						if (findistask(task, TaskType.MAIN))
						{
							type = TaskType.MAIN;
						}				
						else if (findistask(task, TaskType.LATERAL))
						{
							type = TaskType.LATERAL;
						}		
						
                        var tmpclass1:Class = SwfClassCache.getInstance().getClass(((maparr[i]as WorldMapClassVo).iconClass));
			            var icon1:MovieClip = new tmpclass1() as MovieClip;					
						
						
						
						var tips:taskTips = new taskTips();
						tips.mouseEnabled = false;
						tips.mouseChildren = false;
                    	sp1.addChild(tips);
						tips.gotoAndStop(type);
						tips.x = (maparr[i] as WorldMapClassVo).x;
						tips.y = (maparr[i] as WorldMapClassVo).y - icon1.width / 2;					
					}

				}
			}			
			
			//创建主角
            asset = new CacheSprite();
			asset.bodyComplete_callback = drawplayer;	
			asset.className = DataManager.getInstance().getAvatarVo(DataManager.getInstance().currentUser.avatar).className;
			
			
		}

		private function playmove(e:Event):void 
		{
			if (movebool)
			{
				if (bushu == allbushu)
				{
				   goal = new Point();
				   goal.x = DataManager.getInstance().worldData.getWorldMapClassCidVo(end).x;
				   goal.y = DataManager.getInstance().worldData.getWorldMapClassCidVo(end).y;
				}
			    else
				{
                   goal = new Point(pathData[bushu][0], pathData[bushu][1]);					
				}
			   
			    xLength = goal.x - asset.x;
	            yLength = goal.y - asset.y;

			    play = new Point(asset.x, asset.y);
			   
	            goalDistance = Point.distance(goal, play);	//计算人物到目标的距离
			   
	            xStep = xLength/goalDistance;
	            yStep = yLength/goalDistance;
			   
			    s = xStep/yStep;
			    if(goalDistance > 5)
			     {
				    var str:String = "move_"
				   
				    if (xLength>=0)	//目标点位置相对人物在下边的情况
				       {

					       if (s<=0.26 && s>=0)
					       {
						      str += Person.DOWN;
					       }
					       if(s>0.26 && s<= 3.73)
					       {
						      str += Person.RIGHT;
					       }
					       if (s>3.73 || s<-3.73)
					       {
						      str += Person.RIGHT;
					       }
					       if (s>=-3.73 && s<-0.26)
					       {
						      str += Person.RIGHT;
					       }
					       if (s>-0.26 && s<=0)
					       {
						      str += Person.UP;
					       }
				       }	
				       if (xLength<0)//目标点位置相对人物在上边的情况
				       {
					       if (s<=0.26 && s>=0)
					       {
					   	     str += Person.UP;
					       }
					       if(s>0.26 && s<= 3.73)
					       {
					   	     str += Person.LEFT;
					       }
					       if (s>3.73 || s<-3.73)
					       {
						     str += Person.LEFT;
					       }
					       if (s>=-3.73 && s<-0.26)
					       {
						     str += Person.LEFT;
					       }
					       if (s>=-0.26 && s<=0)
					       {
						     str += Person.DOWN;
					       }
				       }
					   
					   
					   
					   asset.bitmap_movie_mc.gotoAndPlayLabels(str);
				       asset.x += speed * xStep;
				       asset.y += speed * yStep;
				       movebool = true;
			       }  
			       else
			       {
				       bushu ++;
					   if (bushu > allbushu)
					   {
						   movebool = false;
						   var command:MoveSceneCommand = new MoveSceneCommand();
						   var vo:WorldMapClassVo = DataManager.getInstance().worldData.getWorldMapClassCidVo(end);
						   command.moveScene(vo.sceneId);
						   command.addEventListener(Event.COMPLETE, commandComplete);
					   }
			       }							
			}
		}
		
		private function commandComplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, commandComplete);
			close();
			_view.mouseChildren = true;
			_view.mouseEnabled = true;			
		}
		
		private function drawplayer():void 
		{
			
			asset.bitmap_movie_mc.drawFrame = 2;
			var str:String = "move_" + Person.DOWN;
			asset.bitmap_movie_mc.gotoAndPlayLabels(str);
			if(DataManager.getInstance().currentUser.currentSceneId.toString().length >= 3)
            {
				senceid = DataManager.getInstance().currentUser.currentSceneId.toString();
			
				senceid = senceid.substring(0, senceid.length - 2);
				senceid += "01";				
			}
			else
			{
				senceid = "2";
			}
			
			var assetvo:WorldMapClassVo = DataManager.getInstance().worldData.getWorldMapClassVo(int(senceid));
			
			asset.x = assetvo.x;
			asset.y = assetvo.y;
			asset.bitmap_movie_mc.addEventListener(Event.ENTER_FRAME, playmove);		
            asset.bitmap_movie_mc.addEventListener(Event.ENTER_FRAME, lineDraw);
			sp1.addChild(asset);			
		}
		
		public function close():void
		{
			_view.stage.removeEventListener(FullScreenEvent.FULL_SCREEN	, fullresize);		
			EventManager.getInstance().removeEventListener(WorldMapEvent.DRAWMAPOPEN, drawmapopen);
			closeMe(true);
			EventManager.getInstance().dispatchEvent(new WorldMapEvent(WorldMapEvent.WORLDMAPCLOSE));			
		}
		
		//绘制路线
		public function drawpath(_arr:Array):void
		{
			pathData = new Array();
			for (var i:int = 0; i < _arr.length; i++) 
			{
				
				var vo:WorldMapClassVo = DataManager.getInstance().worldData.getWorldMapClassCidVo(_arr[i]);
				
				for (var j:int = 0; j < vo.links.length; j++) 
				{
					if (vo.links[j][0] == _arr[i + 1])
					{
						for (var k:int = 0; k < vo.links[j][1].length; k++) 
						{
							pathData.push(vo.links[j][1][k]);
						}
					}
				}
			}
			
			spline = new Sprite();
			var bitmapData:WorldMapLine = new WorldMapLine(15, 15);
			spline.mouseChildren = false;
			spline.mouseEnabled = false;
			spline.graphics.lineStyle(14);
			spline.graphics.lineBitmapStyle(bitmapData)
			sp1.addChildAt(spline, sp1.getChildIndex(asset));
			spline.graphics.moveTo(asset.x,asset.y);
			spline.x = 0;
			spline.y = 0;
			
			//for (var m:int = 0; m < pathData.length; m++) 
			//{
				//spline.graphics.lineTo(pathData[m][0], pathData[m][1]);	
			//}
			
			
			mcarray = new Array();
			for (var l:int = 0; l < pathData.length; l++) 
			{
				var mc:mappoint = new mappoint();
				mc.mouseChildren = false;
				mc.mouseEnabled = false;
				sp1.addChild(mc);
				sp1.addChildAt(mc,sp1.getChildIndex(asset));
				mc.x = pathData[l][0];
				mc.y = pathData[l][1];
				mc.alpha = 0;
				mcarray.push(mc);
				if (l + 1 == pathData.length)
				{
                    TweenMax.to(mc, 0.1, {delay:0.1*l*stop, x:mc.x, y:mc.y,alpha:100,onComplete:lineDrawstart} );					
				}
				else
				{
                    TweenMax.to(mc, 0.1, {delay:0.1*l*stop, x:mc.x, y:mc.y,alpha:100} );						
				}
			}
			
			linebushu = 0;
			
		    temppoint = new Point();
			temppoint.x = asset.x;
		    temppoint.y = asset.y;
			
			allbushu = pathData.length;	
			
			
			
		}
		
		private function lineDraw(e:Event):void
		{
			if (movelinebool)
			{
                goal = new Point(pathData[linebushu][0], pathData[linebushu][1]);					

			   
			    xLength = goal.x - temppoint.x;
	            yLength = goal.y - temppoint.y;

			    play = new Point(temppoint.x, temppoint.y);
			   
	            goalDistance = Point.distance(goal, play);	//计算人物到目标的距离
			   
	            xStep = xLength/goalDistance;
	            yStep = yLength/goalDistance;
			   
			    s = xStep/yStep;
			    if(goalDistance > 5)
			     {
					spline.graphics.lineTo(temppoint.x, temppoint.y);
				    temppoint.x += linespeed * xStep;
				    temppoint.y += linespeed * yStep;
				    movelinebool = true;
			     }  
			     else
			     {
				    linebushu ++;
					if (linebushu == allbushu)
					{
						movelinebool = false;
					}
			     }							
			}			
		}
		
		private function lineDrawstart():void 
		{
			if (!isrolerun)
			{
				movelinebool = true;	
			}
			else
			{
				isrolerun = false;
			}
		}	
		
		//根据type查找这个数组里书否有这种类型的任务
		private function findistask(task:Array,type:int):Boolean
		{			
			for (var j:int = 0; j < task.length; j++) 
			{
				if ((task[j] as TaskVo).type == type)
				{
					return true;
				}
			}			

			return false;
		}
		
		public function getbtnMc(name:String):DisplayObject
		{
			for (var i:int = 0; i < sp1.numChildren; i++) 
			{
				if (sp1.getChildAt(i).name == name)
				{
                   return sp1.getChildAt(i);
				}
			}
			
			return null;
		}
		
		//是不是符合佣兵条件
		public function isConditionRole(roleConditionLevel:int):Boolean
		{			
			var arr:Array = DataManager.getInstance().roleData.getMyRoles();
			for (var i:int = 0; i < arr.length; i++) 
			{
				if (arr[i].level < roleConditionLevel&&arr[i].pos >0)
				{
					return false;
				}
			}
			return true;
			
		}		
		
	}

}