package happymagic.scene.world 
{
	import com.friendsofed.isometric.IsoUtils;
	import com.friendsofed.isometric.Point3D;
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.geom.Matrix;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.utils.ByteArray;
	import flash.utils.setTimeout;
	import happyfish.events.DEvent;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.BgMusicManager;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happyfish.scene.astar.Node;
	import happyfish.scene.camera.CameraControl;
	import happyfish.scene.fog.FogEvent;
	import happyfish.scene.fog.FogManager;
	import happyfish.scene.fog.FogNode;
	import happyfish.scene.fog.FogView;
	import happyfish.scene.iso.IsoLayer;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.control.collision.CollisionController;
	import happyfish.scene.world.control.IsoPhysicsControl;
	import happyfish.scene.world.control.MapDrag;
	import happyfish.scene.world.control.MouseCursorAction;
	import happyfish.scene.world.GameWorld;
	import happyfish.scene.world.grid.BaseItem;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.grid.Tile;
	import happyfish.scene.world.grid.Wall;
	import happyfish.scene.world.WorldState;
	import happyfish.scene.world.WorldView;
	import happyfish.utils.display.TitleSprite;
	import happymagic.display.view.ModuleDict;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.PublicDomain;
	import happymagic.manager.SceneTimeManager;
	import happymagic.model.command.MoveSceneCommand;
	import happymagic.model.MagicJSManager;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.ItemType;
	import happymagic.model.vo.PortalVo;
	import happymagic.model.vo.SceneClassVo;
	import happymagic.model.vo.SceneState;
	import happymagic.model.vo.SceneVo;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.award.AwardItemManager;
	import happymagic.scene.world.award.AwardItemView;
	import happymagic.scene.world.award.AwardType;
	import happymagic.scene.world.bigScene.BigSceneBg;
	import happymagic.scene.world.bigScene.BigSceneView;
	import happymagic.scene.world.bigScene.NpcView;
	import happymagic.scene.world.control.AssistanceAction;
	import happymagic.scene.world.control.DoorForPersonControl;
	import happymagic.scene.world.control.FriendHomeAction;
	import happymagic.scene.world.control.MouseDefaultAction;
	import happymagic.scene.world.control.OccFriendAction;
	import happymagic.scene.world.control.TaxesAction;
	import happymagic.scene.world.control.VisitFriendAction;
	import happymagic.scene.world.grid.item.Decor;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.grid.item.FurnaceDecor;
	import happymagic.scene.world.grid.item.Portal;
	import happymagic.scene.world.grid.item.RoomUpItem;
	import happymagic.scene.world.grid.item.SpecialBuild;
	import happymagic.scene.world.grid.item.WallDecor;
	import happymagic.scene.world.grid.person.Mine;
	import happymagic.scene.world.grid.person.Player;
	import mx.modules.IModule;
	/**
	 * ...
	 * @author slam
	 */
	public class MagicWorld extends GameWorld
	{
		public var avatarsAreHidden:Boolean = false;
		public var bigSceneView:BigSceneView;
		public var sceneVo:SceneVo;
		private var _decorList:Object;
		private var _floorList:Array;
		private var _floorList2:Array;
		private var _wallList:Array;
		private var _portalList:Vector.<PortalVo>;
		private var _userInfo:UserVo;
		private var _initFlg:Boolean = false;
		private var tileNeedLoad:uint;
		private var mouseGridIcon:gotoIcon;
		private var wallNeedLoad:uint;
		private var decorNeedLoad:uint;
		private var personNeedLoad:uint;
		private var tileList:Array;
		private var physicsControl:IsoPhysicsControl;
		private var playerFlagIcon:playerHalo;
		public var doorControl:DoorForPersonControl;
		public var doorList:Vector.<Door>;
		
		//主角
		protected var _player:Player;
		//场景主人
		public var scenePlayer:Player;
		private var changeAllWallNeeded:int;
		private var firstInited:Boolean;
		
		private var fog:FogView;
		
		public function MagicWorld($worldState:WorldState) 
		{
			super($worldState);
			
			$worldState.mouseAction = new MouseDefaultAction($worldState);
            MouseCursorAction.defaultAction = $worldState.mouseAction;
			
			//初始化其他
			this.preInit();
		}
		
		public function pause():void {
			var p:Person;
			for (var i:int = 0; i < items.length; i++) 
			{
				p = items[i] as Person
				if (p) 
				{
					p.pause();
				}
			}
		}
		
		public function preInit():void
		{
			//创建目标格标志icon
			if (!mouseGridIcon) 
			{
				mouseGridIcon = new gotoIcon();
				mouseGridIcon.mouseChildren=
				mouseGridIcon.mouseEnabled = false;
			}
			
			if (!playerFlagIcon) 
			{
				playerFlagIcon = new playerHalo();
				playerFlagIcon.mouseChildren=
				playerFlagIcon.mouseEnabled = false;
			}
			
		}
		
		/**
		 * 设置主角移动位置小旗位置
		 * @param	p
		 */
		public function setPlayerFlag(p:Point3D):void {
			playerFlagIcon.visible = true;
			_view.isoView.getLayer(0).addChild(playerFlagIcon);
			p = IsoUtil.gridToIso(p);
			var p2:Point = IsoUtils.isoToScreen(p);
			
			playerFlagIcon.x = p2.x;
			playerFlagIcon.y = p2.y;
			
			//playerFlagIcon.x = -(_view.isoView.camera.stage.stageWidth) / 2;
			//playerFlagIcon.y = 300;
		}
		
		/**
		 * 隐藏主角移动位置小旗
		 */
		public function clearPlayerFlag():void {
			playerFlagIcon.visible = false;
			if (playerFlagIcon.parent) 
			{
				playerFlagIcon.parent.removeChild(playerFlagIcon);
			}
		}
		
		public function init():void
		{
			//监听点击事件
			this._view.addEventListener(GameMouseEvent.GAME_MOUSE_EVENT, this.onGameMouseEvent);
			
			//进入编辑模式
			//EventManager.getInstance().addEventListener(SceneEvent.START_DIY, this.enterEditMode);
			
/*			//退出编辑模式
			EventManager.getInstance().addEventListener(SceneEvent.DIY_FINISHED, diyFinished);
			//取消编辑模式
			EventManager.getInstance().addEventListener(SceneEvent.DIY_CANCELDIY, diyCancel);*/
			
			//点击好友
			EventManager.getInstance().addEventListener(SceneEvent.CHANGE_SCENE, this.goFriendsHome);
			
			//初始化物理控制
			_worldState.physicsControl.initPhysics(_worldState);
			
			//奖励manager
			AwardItemManager.getInstance().init(_worldState);
			
			//门开关control
			doorControl = new DoorForPersonControl(_worldState);
			
			//侦听大小变化
			DisplayManager.sceneSprite.stage.addEventListener(Event.RESIZE, resizeFun);
		}
		
		/**
		 * 居中
		 */
		public function resizeFun(e:Event):void
		{
			_view.center();
			//(ModuleManager.getInstance().getModule(ModuleDict.MODULE_SYSMENU) as SysMenuView).init();
		}
		
		override public function create($data:Object, $init_flg:Boolean = true):void
		{
			//更新主菜单的表现,自已家与别人家的区别
			//if(DisplayManager.menuView) DisplayManager.menuView.setType();
			
			//地下城修改 2011.11.11
			//更换场景交互逻辑state,也是自己家与别人家两套
			switch (DataManager.getInstance().curSceneType) 
			{
				case SceneType.TYPE_HOME:
				case SceneType.TYPE_SELF_VILIAGE:
				case SceneType.TYPE_EXPLORE:
					MouseCursorAction.defaultAction = new MouseDefaultAction(_worldState);
				break;
				
				case SceneType.TYPE_FRIEND_HOME:
					MouseCursorAction.defaultAction = new FriendHomeAction(_worldState);
				break;
				
				case SceneType.TYPE_FRIEND_VILIAGE:
					switch (DataManager.getInstance().getVar("friendActionType")) 
					{
						case FriendActionType.OCC:
							DisplayManager.showSysMsg("请用鼠标点击您想侵占的房屋");
							
							MouseCursorAction.defaultAction = new OccFriendAction(_worldState);
						break;
						
						case FriendActionType.TAXES:
							MouseCursorAction.defaultAction = new TaxesAction(_worldState);
						break;
						
						case FriendActionType.ASSISTANCE:
							MouseCursorAction.defaultAction = new AssistanceAction(_worldState);
						break;
						
						case FriendActionType.VISIT:
							MouseCursorAction.defaultAction = new VisitFriendAction(_worldState);
						break;
						
						default:
							MouseCursorAction.defaultAction = new VisitFriendAction(_worldState);
						break;
					}
				break;
				
			}
			
			//倒计时准备
			//暂时不用了，都在面板里有检查了
			//SceneTimeManager.getInstance().initSceneTime();
			
			//
			tileList = new Array();
			//清空门队列
			doorList = new Vector.<Door>();
			
			//标记场景正在渲染
			sceneLoading = true;
			
			this._view = this._worldState.view;
			
			//初始化,目前只是一些事件侦听
			init();
			
			//游戏数据
			this._data = $data;
			this._decorList = this._data.decorList;
			this._wallList = this._data.wallList;
			this._floorList = this._data.floorList;
			this._floorList2 = this._data.floorList2;
			this._userInfo = this._data.userInfo as UserVo;
			this._initFlg = $init_flg;
			
			//广播场景数据更换
			EventManager.getInstance().dispatchEvent(new SceneEvent(SceneEvent.SCENE_DATA_COMPLETE));
			
			//初始化grid
			if ($init_flg)
			{
				sceneVo = DataManager.getInstance().getSceneVoByClass(DataManager.getInstance().currentUser.currentSceneId, SceneState.OPEN);
				_worldState.view.isoView.resize(sceneVo.numCols);
				if (_wallList) 
				{
					if (_wallList.length>0) 
					{
						this._worldState.initGrid(this._userInfo.tileX, this._userInfo.tileZ,sceneVo);
					}else {
						this._worldState.initGrid(0, 0,sceneVo);
					}
				}else {
					this._worldState.initGrid(0, 0,sceneVo);
				}
				CollisionController.getInstance().refreshScene(sceneVo.numCols, sceneVo.numRows);
			}

			//创建背景尺寸
			this.groundRect = new Rectangle();
			this.groundRect.width = sceneVo.bg!="" ? 2000 : IsoUtil.TILE_SIZE * sceneVo.numCols * 2;
			this.groundRect.height = sceneVo.bg!="" ? 1300 : IsoUtil.TILE_SIZE * sceneVo.numCols;
			
			this.groundRect.x = -groundRect.width / 2;
			
			
			startCreateScene();
		}
		
		private function startCreateScene():void {
			//加载大背景地图
			loadBigSceneBg(true);
		}
		
		public function loadBigSceneBg(isInit:Boolean=false):void {
			//创建场景背景图
			if (sceneVo.bg && sceneVo.bg!="")
			{
				var bigbm:BigSceneBg = new BigSceneBg();
				bigbm.addEventListener(Event.COMPLETE, onBgComplete);
				bigbm.loadClass(sceneVo.bg);
				_view.setBigBg(bigbm as Bitmap, -groundRect.width / 2, -groundRect.height / 2);
			}
			else _view.setBigBg();
			
			if(isInit) createTile(sceneVo.type==SceneClassVo.HOME ? 1 : 0);
		}
		
		private function onBgComplete(event:Event=null):void
		{
			if(event) event.target.removeEventListener(Event.COMPLETE, onBgComplete);
			this._view.center();
		}
		
		
		private function createTile(offset:int = 1):void {
			_groundSprite = new Sprite();
			tileList = [];
			//如果地板为空,就跳过
			if (_floorList && _floorList.length>0) 
			{
				
				tileNeedLoad = _floorList.length * _floorList[0].length;
				if (_floorList2.length>0) 
				{
					tileNeedLoad += _floorList2.length * _floorList2[0].length;
				}
			
				var tmp_decor_vo:DecorVo;
				var tilemap:Tile;
				var i:int;
				var j:int;
				
				for(i = 0; i < _floorList.length; i++)
				{
					for (j = 0; j < _floorList[0].length; j++)
					{
						tmp_decor_vo = new DecorVo();
						tmp_decor_vo.createDefaultObj(_floorList[i][j], i + offset, j + offset);
						
						tilemap = new Tile(tmp_decor_vo, _worldState,tile_complete);
						
						//记录墙到列表内
						this.saveWallTileNodeItem(tilemap);
						
						tileList.push(tilemap.view);
						
						//_groundSprite.addChild(tilemap.view.container);
					}
				}
				
				//副本时的第二层地板
				if (_floorList2.length>0) 
				{
					
					for(i = 0; i < _floorList2.length; i++)
					{
						for (j = 0; j < _floorList2[0].length; j++)
						{
							tmp_decor_vo = new DecorVo();
							tmp_decor_vo.createDefaultObj(_floorList2[i][j], i + offset, j + offset);
							
							tilemap = new Tile(tmp_decor_vo, _worldState,tile_complete);
							tilemap.view.container.sortPriority = 1;
							
							tileList.push(tilemap.view);
							
							//_groundSprite.addChild(tilemap.view.container);
						}
					}
				}
				
			}else {
				//地板为空时,跳到地板渲染
				layTile();
				return;
			}
			
			
			//================================================================================
			
			//如果不是初始化场景,就直接创建后续对象(人物\物件之类)
			if (this._initFlg === false) {
				this.createOther();
			}
		}
		
		/**
		 * 某块地板完成时调用,判断全部完成时进入下一步
		 */
		private function tile_complete():void
		{
			tileNeedLoad--;
			if (tileNeedLoad<=0) 
			{
				setTimeout(layTile,100);
			}
		}
		
		public function createWall():void {
			//第一次进入场景时,关闭loading动画
			if (PublicDomain.getInstance().getVar("clearLoader")) 
			{
				PublicDomain.getInstance().getVar("clearLoader")();
				PublicDomain.getInstance().setVar("clearLoader",null);
			}
			
			//=============创建墙壁===========================================================
			
			if (_wallList && _wallList.length>0) 
			{
				wallNeedLoad = _wallList[0].length + _wallList[1].length;
			
			
				var tmp_decor_vo:DecorVo;
				for (var k:int = 0; k < this._wallList[0].length; k++) {
					if (_worldState.isWallArea(k + IsoUtil.roomStart, IsoUtil.roomStart)) {
						tmp_decor_vo = new DecorVo();
						tmp_decor_vo.createDefaultObj(this._wallList[0][k], k+1, 0);
						
						this.addItem(new Wall( tmp_decor_vo, this._worldState,wallComplete ));
					}
				}
				
				for (var m:int = 0; m < this._wallList[1].length; m++) {
					if (_worldState.isWallArea(m + IsoUtil.roomStart, IsoUtil.roomStart)) {
						tmp_decor_vo = new DecorVo();
						tmp_decor_vo.createDefaultObj(this._wallList[1][m], 0, m+1);
						
						this.addItem(new Wall( tmp_decor_vo, this._worldState,wallComplete ));
					}
				}
			}else {
				createWall_complete();
			}
			
			
		}
		
		private function wallComplete():void
		{
			wallNeedLoad--;
			if (wallNeedLoad<=0) 
			{
				setTimeout(createWall_complete, 500);
			}
		}
		
		/**
		 * 墙渲染完成
		 * @param	e
		 * @param	hasRoom	当前场景中是否有房间
		 */
		private function createWall_complete(e:Event = null ):void 
		{
			if (e) e.target.removeEventListener(Event.COMPLETE, createWall_complete);
			
			if (_wallList && _wallList.length>0) 
			{
				//设置房间的墙所在格不可走
				_worldState.closeRoomGrid();
				EventManager.getInstance().dispatchEvent(new SceneEvent(SceneEvent.WALL_COMPLETE));
			}
			createDecor();
			
			//关闭幕布
			//DisplayManager.uiSprite.showSceneEndMv();
		}
		
		private function createDecor():void
		{
			//var bitmap_cacher_queue:BitmapCacherQueue = BitmapCacherQueue.getInstance();
			//bitmap_cacher_queue.addEventListener(Event.COMPLETE, createDecor_complete);
			
			decorNeedLoad = 0;
			if (_decorList[ItemType.DecorOnWall]) decorNeedLoad += _decorList[ItemType.DecorOnWall].length;
			if (_decorList[ItemType.Decoration]) decorNeedLoad += _decorList[ItemType.Decoration].length;
			if (_decorList[ItemType.Door]) decorNeedLoad += _decorList[ItemType.Door].length;
			if (_decorList[ItemType.FurnaceType]) decorNeedLoad += _decorList[ItemType.FurnaceType].length;
			if (_decorList[ItemType.Build]) decorNeedLoad += _decorList[ItemType.Build].length;
			
			var portalList:Vector.<PortalVo> = DataManager.getInstance().portalList;
			if (portalList && portalList.length >= 0) decorNeedLoad += portalList.length;
			
			//如果没有装饰物,就直接进入一下步
			if (decorNeedLoad==0) 
			{
				createDecor_complete();
				return;
			}
			
			//创建墙上装饰物
			if (_decorList[ItemType.DecorOnWall]) {
				
				for (var m:int = 0; m < this._decorList[ItemType.DecorOnWall].length; m++) {
					this.addItem(new WallDecor( this._decorList[ItemType.DecorOnWall][m], this._worldState, doorComplete ));
				}
			}
			//this.addItem(new WallDecor( { class_name:'decor.1.hongseguanzi', x:0, z:5 ,size_x:1, size_z:1 }, this._worldState ));
			
			//创建普通装饰物品
			if (this._decorList[ItemType.Decoration]) {
				for (var n:int = 0; n < this._decorList[ItemType.Decoration].length; n++) {
					this.addItem(new Decor( this._decorList[ItemType.Decoration][n], this._worldState,decorComplete ));
				}
			}
			
			//创建工作台
			if (this._decorList[ItemType.FurnaceType]) {
				for (var q:int = 0; q < this._decorList[ItemType.FurnaceType].length; q++) {
					this.addItem(new FurnaceDecor( this._decorList[ItemType.FurnaceType][q], this._worldState,decorComplete ));
				}
			}
			
			//创建门(一种墙上装饰)
			var tmpdoor:Door;
			if (this._decorList[ItemType.Door]) {
				for (var p:int = 0; p < this._decorList[ItemType.Door].length; p++) {
					tmpdoor = new Door( this._decorList[ItemType.Door][p], this._worldState, doorComplete);
					//放入门队列
					addDoorToList(tmpdoor);
				}
			}
			
			if (_decorList[ItemType.Build]) 
			{
				for (var i:int = 0; i < _decorList[ItemType.Build].length; i++) 
				{
					addItem(new SpecialBuild( _decorList[ItemType.Build][i], _worldState,decorComplete ));
				}
			}
			
			//创建传送门
			if (portalList) 
			{
				for (var j:int = 0; j < portalList.length; j++)
				{
					_worldState.world.addItem(new Portal(portalList[j], _worldState, decorComplete));
				}
			}
			
		}
		
		/**
		 * 更新特殊建筑的表现,按新的等级数据
		 * @param	buildId
		 */
		public function refreshSpecialBuild(buildId:int):void {
			var tmp:SpecialBuild;
			for (var i:int = 0; i < items.length; i++) 
			{
				tmp = items[i] as SpecialBuild;
				if (tmp) 
				{
					if (tmp.data.id==buildId) 
					{
						tmp.resetClassName();
						tmp.resetView(tmp.data.className);
						return;
					}
				}
			}
		}
		
		public function doorComplete(target:WallDecor):void
		{
			if (target is Door) 
			{
				//用门来挖空他后面的墙
				//得到墙
				//var tmpp:Point;
				//if (target.mirror) 
				//{
					//tmpp = new Point(target.gridPos.x, target.gridPos.z-1);
				//}else {
					//tmpp = new Point(target.gridPos.x-1, target.gridPos.z);
				//}
				var wall:Wall = getWallByNode(target.gridPos.x, target.gridPos.z) as Wall;
				//if(wall) wall.cutDoor((target as Door).asset.bitmap_movie_mc);
				if (wall) 
				{
					wall.cutDoor((target as Door).asset.bitmap_movie_mc);
				}
				
				addItem(target);
			}
			
			decorComplete();
		}
		
		private function decorComplete():void
		{
			decorNeedLoad--;
			
			if (decorNeedLoad<=0) 
			{
				createDecor_complete();
			}
		}
		
		private function createDecor_complete(e:Event=null):void 
		{
			if(e) e.target.removeEventListener(Event.COMPLETE, createDecor_complete);
			doorControl.getAllDoorNodes();
			
			createBigScene(); //创建大场景容器 传送门、矿等挡路物必须在角色创建之前创建
			createPlayer(); //创建角色
			this._view.center();
			
			//关闭幕布
			DisplayManager.uiSprite.showSceneEndMv();
			
			//显示场景名
			var moduleVo:ModuleVo = DataManager.getInstance().getModuleVo("TitleSprite");
			DisplayManager.uiSprite.addModuleByVo(moduleVo);					
			
			//游戏开始了JS
			if (!firstInited) {
				firstInited = true;
				MagicJSManager.getInstance().callJs("loadCompleted");
			}
		}
		
		
		public function createPlayer():void
		{
			//TODO 客人数
			personNeedLoad = 1;
			if (!DataManager.getInstance().isSelfScene) personNeedLoad++;
			
			var wNode:Node;
			if (_initFlg) {			
				//创建主角
				if (DataManager.getInstance().lastSceneId && _portalList) //如果有上一个场景
				{
					for (var j:int = 0; j < _portalList.length; j++) //在本场景中寻找通向上一个场景的传送门
					{
						if (_portalList[j].targetSceneId == DataManager.getInstance().lastSceneId)
						{
							//获取该传送门周围一圈格子内可以走的一个格子
							for (var dx:int = -1; dx <= 1; dx++)
							{
								for (var dz:int = -1; dz <= 1; dz++)
								{
									var node:Node = _worldState.grid.getNode(_portalList[j].x + dx, _portalList[j].z + dz);
									if (node && node.walkable)
									{
										wNode = node;
										break;
									}
								}
							}
							break;
						}
					}
				}
				if (!wNode)
				{
					if (sceneVo.entrances && sceneVo.entrances.length > 0 && sceneVo.type!=SceneClassVo.HOME)
					{
						var index:int = Math.floor(sceneVo.entrances.length * Math.random());
						wNode = new Node(sceneVo.entrances[index][0], sceneVo.entrances[index][1]);
					}
					else wNode = _worldState.getCustomRoomWalkAbleNode();
				}
				
				this._player = new Player( DataManager.getInstance().currentUser, this._worldState, wNode.x, wNode.y, playerComplete);
				
				this.addItem(this._player);
				
			}
			
			if (!DataManager.getInstance().isSelfScene) {
				wNode = _worldState.getCustomRoomWalkAbleNode();
				scenePlayer = new Player( DataManager.getInstance().curSceneUser, this._worldState, wNode.x, wNode.y, personComplete);
				this.addItem(this.scenePlayer);
				scenePlayer.fiddle();
			}
		}
		
		private function playerComplete():void 
		{
			sceneLoading = false;
			personComplete();
			centerCameraToPlayer();
			
		}
		
		public function centerCameraToPlayer():void {
			if (player) 
			{
				if (player.asset.stage) 
				{
					CameraControl.getInstance().centerTweenTo(player.asset, DisplayManager.camera);
					setTimeout(dispatchCameraMoveCompleteEvent, 1000);
					//CameraControl.getInstance().followTarget(player.asset, _view.isoView.camera);
				}else {
					setTimeout(centerCameraToPlayer, 200);
				}
			}
			
		}
		
		private function dispatchCameraMoveCompleteEvent():void {
			EventManager.getInstance().dispatchEvent(new DEvent("sceneMoveComplete"));
		}
		
		private function personComplete():void
		{
			personNeedLoad--;
			if (personNeedLoad<=0) 
			{
				createPlayer_complete();
			}
		}
		
		public function playBgMusic():void {
			var sceneClass:SceneClassVo = DataManager.getInstance().getSceneClassById(DataManager.getInstance().currentUser.currentSceneId);
			if (sceneClass.bgSound) 
			{
				BgMusicManager.getInstance().setSound(InterfaceURLManager.getInstance().staticHost+sceneClass.bgSound);
			}else {
				BgMusicManager.getInstance().setSound(InterfaceURLManager.getInstance().staticHost+PublicDomain.getInstance().getVar("bgMusic"));
			}
		}
		
		private function createPlayer_complete(e:Event=null):void 
		{
			//doorControl.startCheckDoor();
			
			if (_initFlg) 
			{
				//播放背景音乐
				playBgMusic();
			}
			
			//标记场景初始化完成
			_initFlg = false;
			
			if(e) e.target.removeEventListener(Event.COMPLETE, createPlayer_complete);
			
			DisplayManager.uiSprite.closeLoading();
			
			
			EventManager.getInstance().dispatchEvent(new SceneEvent(SceneEvent.SCENE_COMPLETE));
			
			
			//BgMusicManager.getInstance().setSound(sceneVo.bgSound);
			//BgMusicManager.getInstance().setSound(PublicDomain.getInstance().getVar("bgMusic"));
			
		}
		
		/**
		 * 获得指定NPC
		 * @param	id
		 * @return
		 */
		public function getNpcById(id:int):NpcView {
			var npcs:Array = bigSceneView.npcList;
			for (var i:int = 0; i < npcs.length; i++) 
			{
				if (npcs[i].npcvo.id == id) 
				{
					return npcs[i] as NpcView;
				}
			}
			return null;
		}
		
		/**
		 * 返回随机一个可放置格子的屏幕像素坐标
		 * @return
		 */
		public function getCanWalkNodeScreenPoint():Point {
			var node:Node = _worldState.getCustomRoomWalkAbleNode();
			var point:Point = IsoUtil.nodeToScreen(node);
			return point;
		}
		
		/**
		 * 创建大场景容器
		 */
		private function createBigScene():void {
			//创建大场景内的物件
			if (!bigSceneView) 
			{
				bigSceneView = new BigSceneView(_worldState);
			}
			
			_portalList = DataManager.getInstance().portalList;
			
			bigSceneView.setData(sceneVo,
				DataManager.getInstance().npcList,
				DataManager.getInstance().monsterList,
				DataManager.getInstance().mineList
			);
			
			DataManager.getInstance().npcList = null;
			DataManager.getInstance().portalList = null;
			DataManager.getInstance().monsterList = null;
			DataManager.getInstance().mineList = null;
		}
		
		/**
		 * 增加一个掉落物
		 * 目前会把按1\10\100来分割成几个水晶来表现
		 * @param	_type
		 * @param	_num
		 * @param	startP
		 */
		public function createAwardItem(_type:uint, _num:uint, startP:Point3D, id:uint = 0):void {
			
			var awardItem:AwardItemView;
			var tmpItem:BaseItemClassVo;
			var i:int;
			if (_type==AwardType.ITEM && id) 
			{
				//道具
				tmpItem = DataManager.getInstance().itemData.getItemClass(id);
				for ( i = 0; i < _num; i++) 
				{
					awardItem = new AwardItemView( { type:_type, num:_num, className:tmpItem.className, x:startP.x, y:0, z:startP.z }, _worldState);
					setTimeout(addItem,100*i,awardItem);
				}
			}else if (_type!=AwardType.OTHER ) {
				//获得奖品类名
				var tmpclassstr:String = "awardIcon_";
				if (_type == AwardType.COIN) 
				{
					//coin按多少分类
					if (_num<10) 
					{
						tmpclassstr += _type.toString()+ "_2";
					}else if (_num<100) 
					{
						tmpclassstr += _type.toString()+ "_2";
					}else  
					{
						tmpclassstr += _type.toString()+ "_3";
					}
				}else {
					//经验和乐币
					tmpclassstr += _type.toString();
				}
				
				awardItem = new AwardItemView( { type:_type, num:_num, className:tmpclassstr, x:startP.x, y: -1, z:startP.z }, _worldState);
				addItem(awardItem);
			}
		}
		
		/**
		 * 后创建
		 * @param	$init_flg
		 */
		public function createOther():void {
			
			createWall();
			
			return;
			
		}
		
		/**
		 * 渲染地板
		 * @param	e
		 */
		public function layTile(e:Event = null):void
		{
			if (e) 
			{
				e.target.removeEventListener(Event.COMPLETE, layTile);
			}
			
			while (_groundSprite.numChildren>0) 
				{
					_groundSprite.removeChildAt(0);
				}
				tileList.sortOn("depth", Array.NUMERIC);
				for (var k:int = 0; k < tileList.length; k++) 
				{
					_groundSprite.addChild(tileList[k].container);
				}
			
			this.updateGroundBitmapData();
			//浸染大背景图
			var rect:Rectangle = groundSprite.getBounds(groundSprite);
			this._view.setBackground(this.groundData, rect.x, rect.y);
			
			if (this._initFlg) {
				//开始侦听拖动地图
				MapDrag.getInstance(_view.isoView.camera);
			
				createOther();
			}
		}
		
		/**
		 * 更换所有地板，只在DIY时使用，只更换上面这层地板(floorList)
		 * @param	cid
		 */
		public function changeAllTile(cid:int):void {
			if (_groundSprite && _groundSprite.parent) _groundSprite.parent.removeChild(_groundSprite);
			
			for (var i:int = 0; i < _floorList.length; i++) 
			{
				for (var j:int = 0; j < _floorList[i].length; j++) 
				{
					_floorList[i][j] = cid;
				}
			}
			
			createTile(sceneVo.type==SceneClassVo.HOME ? 1 : 0);
		}
		
		/**
		 * 更换所有墙，只在DIY时使用
		 * @param	cid
		 */
		public function changeAllWall(cid:int):void {
			
			changeAllWallNeeded = _wallList[0].length + _wallList[1].length;
			
			var tmp_decor_vo:DecorVo;
			for (var k:int = 0; k < this._wallList[0].length; k++) {
				if (_worldState.isWallArea(k + IsoUtil.roomStart, IsoUtil.roomStart)) {
					_wallList[0][k] = cid;
					tmp_decor_vo = new DecorVo();
					tmp_decor_vo.createDefaultObj(_wallList[0][k], k + 1, 0);
					//移除原墙
					getWallByNode(k+1, 0).remove();
					
					addItem(new Wall( tmp_decor_vo, _worldState,changeAllWallComplete ));
				}
			}
			
			for (var m:int = 0; m < this._wallList[1].length; m++) {
				if (_worldState.isWallArea(m + IsoUtil.roomStart, IsoUtil.roomStart)) {
					_wallList[1][m] = cid;
					tmp_decor_vo = new DecorVo();
					tmp_decor_vo.createDefaultObj(this._wallList[1][m], 0, m+1);
					//移除原墙
					getWallByNode(0, m+1).remove();
					
					addItem(new Wall( tmp_decor_vo, _worldState,changeAllWallComplete ));
				}
			}
		}
		
		private function changeAllWallComplete():void 
		{
			changeAllWallNeeded--;
			if (changeAllWallNeeded<=0) 
			{
				setTimeout(changeAllWall_Allcomplete, 500);
			}
			
		}
		
		private function changeAllWall_Allcomplete():void 
		{
			if (_wallList.length>0) 
			{
				//设置房间的墙所在格不可走
				//_worldState.closeRoomGrid();
				EventManager.getInstance().dispatchEvent(new SceneEvent(SceneEvent.WALL_COMPLETE));
			}
		}
		
		private function startDragMouseGrid():void
		{
			_worldState.view.isoView.addChild(mouseGridIcon);
			_view.addEventListener(Event.ENTER_FRAME, mouseGridFun);
		}
		
		private function mouseGridFun(e:Event):void 
		{
			var p:Point3D = _worldState.view.targetGrid();
			p = IsoUtil.gridToIso(p);
			var p2:Point = IsoUtils.isoToScreen(p);
			mouseGridIcon.x = p2.x;
			mouseGridIcon.y = p2.y;
		}
		
		public function getCustomDoor():Door {
			for (var i:int = 0; i < items.length; i++) 
			{
				if (items[i] is Door ) 
				{
					return items[i];
				}
			}
			return null;
		}
		
		/**
		 * 增加物件
		 * @param	$baseItem
		 * @param	tmpAdd	临时增加入，不放入物件列表
		 */
        override public function addItem(isoItem:IsoItem,tmpAdd:Boolean=false) : void
        {
			//放入item列表
			if (isoItem.view) {
				super.addItem(isoItem);
				_view.addIsoChild(isoItem.view);
			}
			
			//设置可行走和可DIY属性
			//加入格子
            if(isoItem.block) this.addToGrid(isoItem);
			//把墙放入墙队列
			if (isoItem is Wall && !tmpAdd){
				this.saveWallTileNodeItem(isoItem);
			}
			
            return;
        }
		
		override public function clear():void 
		{
			super.clear();
			
			//清除item
			destroyItems();
			
			clearTile();
			
			doorList = new Vector.<Door>();
			
			//清除场景名动画
			var sceneNameModule:TitleSprite = ModuleManager.getInstance().getModule("TitleSprite") as TitleSprite;
			if (sceneNameModule) 
			{
				sceneNameModule.closeMe(true);
			}
			
			
			//清除场景内可行走数据
			_worldState.clearRoomWalkAbles();
			
			if (bigSceneView) bigSceneView.clear();
			
			if (_view)
			{
				_view.clearBackground();
				_view.clearBigBg();
				clearFog();
			}
			
			clearPlayerFlag();
			
			EventManager.getInstance().dispatchEvent(new SceneEvent(SceneEvent.SCENE_CLEARED));
			
		}
		
		/**
		 * 清除地板和墙数据,和地板的显示
		 */
		private function clearTile():void {
			if (_groundSprite && _groundSprite.parent) _groundSprite.parent.removeChild(_groundSprite);
			_floorList = [];
			_floorList2 = [];
			tileList = [];
			//nodeWallTileItems = new Object();
		}
		
		override public function destroyItems():void
		{
			//清除item
			while (_items.length>0) 
			{
				_items[0].remove();
			}
		}
		
		public function addDoorToList(door:Door):void {
			doorList.push(door);
		}
		
		public function removeDoorFromList(door:Door):void {
			for (var i:int = 0; i < doorList.length; i++) 
			{
				if (doorList[i] == door) {
					doorList.splice(i, 1);
					return;
				}
			}
		}
		
		/**
		 * 进入DIY模式
		 * @param	e
		 */
        public function enterEditMode(e:Event = null) : void
        {
			//标记DIY状态
			DataManager.getInstance().isDiying = true;
			
			//隐藏人物
            if (!this.avatarsAreHidden)
            {
                allItemToDiyState();
            }
			
			//暂停门开关控制
			//doorControl.stopCheckDoor();
			
            //this.view.showLayer(this.view.editContainer);
            //this._worldState.whichDisplay = "edit";
            return;
        }
		
		/**
		 * 离开编辑模式
		 */
		override public function leaveEditMode():void
		{
			//标记DIY状态
			DataManager.getInstance().isDiying = false;
			
			var i:int;
			var tmparr:Array;
			
			//设置所有场景上道具对象的DATA
			tmparr = _items;
			for (i = 0; i < tmparr.length; i++) 
			{
				if(tmparr[i].data is DecorVo) tmparr[i].hideGlow();
			}
			
			//new MouseDefaultAction(this._worldState);
			
			//==========重新创建地板=============================================================
			//清除原有地板
			/*clearTile();
			
			var tmp:*;
			_groundSprite = new Sprite();
			for (var x:String in this.nodeWallTileItems) 
			{
				for (var y:String in nodeWallTileItems[x]) 
				{
					//如果不是墙,并且不是在0,0位置的,就是地板
					tmp= nodeWallTileItems[x][y];
					//if (!_worldState.isWallArea(int(x), int(y)) && !(x == '0' && y == '0')) {
					if (tmp is Tile && !(x == '0' && y == '0')) {
						this._groundSprite.addChild(tmp.view.container);
						tileList.push(tmp);
					}
				}
			}
			
			this.layTile();*/
			
			//显示所有人形
			allItemStopDiyState();
			
			//doorControl.startCheckDoor();
		}
		
		/**
		 * 隐藏主角和npc
		 */
		public function hidePlayer():void {
			if (player) 
			{
				player.visible = false;
			}
			
			if (bigSceneView)
			{
				bigSceneView.hideAllNpc();
				bigSceneView.hideAllMonsters();
			}
		}
		
		public function showPlayer():void {
			player.visible = true;
			bigSceneView.showAllNpc();
			bigSceneView.showAllMonsters();
		}
		
		/**
		 * 隐藏所有人形
		 * 所有物件进入diy状态
		 */
		public function allItemToDiyState():void
		{
			this.avatarsAreHidden = true;
			
			for (var i:int = 0; i < this._items.length; i++ ) {
				if ((this._items[i] is Person)) {
					_items[i].visible = false;
				}else if (_items[i] is Door || _items[i] is RoomUpItem) 
				{
					_items[i].diyState = true;
				} else if (_items[i] is AwardItemView) {
					_items[i].visible = false;
				}
			}
		}
		
		/**
		 * 所有物品停止DIY中状态
		 */
		public function allItemStopDiyState():void {
			this.avatarsAreHidden = false;
			
			for (var i:int = 0; i < this._items.length; i++ ) {
				if ((this._items[i] is Person)) {
					_items[i].visible = true;
					
					//如果是主角，如果主角所在位置变成了不可行走区域，就随机一个位置放置主角
					if (_items[i] is Player) 
					{
						if (!_worldState.grid.getNode((_items[i] as Player).x,(_items[i] as Player).z).walkable) 
						{
							var tmpnode:Node = _worldState.getCustomRoomWalkAbleNode();
							(_items[i] as Player).setPos(new Point3D(tmpnode.x, 0, tmpnode.y));
						}
					}
					
				}else if (_items[i] is Door || _items[i] is RoomUpItem) 
				{
					_items[i].diyState = false;
				} else if (_items[i] is AwardItemView) {
					_items[i].visible = true;
				}
			}
		}
		
		/**
		 * 场景内所有点击事件统一处理接口
		 * @param	event
		 */
        private function onGameMouseEvent(event:GameMouseEvent) : void
        {
			if (sceneLoading) 
			{
				return;
			}
            var event_type:String = '';
            if (this._worldState.mouseAction != null)
            {
				if(!DataManager.getInstance().isDiying) EventManager.getInstance().dispatchEvent(event);
                event_type = "on" + event.itemType + event.mouseEventType;
				//trace(event_type);
				
                if (event_type in this._worldState.mouseAction)
                {
					this._worldState.mouseAction[event_type](event);
                }
            }
            return;
        }
		
		override public function get userInfo():Object
		{
			return this._userInfo;
		}
		
		public function get decorList():Object { return _decorList; }
		
		public function goFriendsHome(e:SceneEvent):void
		{
			new MoveSceneCommand().moveScene(e.sceneId,0,0,1,e.uid);
		}
		
		private function isDecorView(value:IsoItem):Boolean {
			if ((value is Wall || value is WallDecor || value is Door || value is Decor) && !(value is RoomUpItem) ) 
			{
				return true;
			}
			return false;
		}
		
		public function getDecorByIdType(id:uint,type:Class):IsoItem {
			for (var i:int = 0; i < items.length; i++) 
			{
				if ((items[i] is type)) 
				{
					if (items[i].data.id==id) 
					{
						return items[i];
					}
				}
			}
			return null;
		}
		
		public function getFreeFurnaceByCid(cid:int):FurnaceDecor {
			var tmp:FurnaceDecor;
			for (var i:int = 0; i < items.length; i++) 
			{
				tmp = items[i] as FurnaceDecor;
				if (tmp) 
				{
					if (tmp.state==FurnaceDecor.STATE_IDLE && tmp.data.cid==cid) 
					{
						return items[i] as FurnaceDecor;
					}
				}
			}
			return null;
		}
		
		public function getFurnaceList():Array
		{
			var arr:Array = [];
			var len:int = items.length;
			for (var i:int = 0; i < len; i++)
			{
				if (items[i] is FurnaceDecor)
				{
					arr.push(items[i]);
				}
			}
			return arr;
		}
		
		public function getDecorList(cid:int = 0):Array
		{
			var arr:Array = [];
			var len:int = items.length;
			for (var i:int = 0; i < len; i++)
			{
				var decor:Decor = items[i] as Decor;
				if (!decor) continue;
				if (0 == cid) arr.push(decor);
				else if (decor.decorVo.cid == cid) arr.push(decor);
			}
			return arr;
		}
		
		public function getDecorById(id:String):IsoItem {
			for (var i:int = 0; i < items.length; i++) 
			{
				if (!(items[i] is Person)) 
				{
					if (items[i].data.id==id) 
					{
						return items[i];
					}
				}
			}
			return null;
		}
		
		override public function addToGrid(item:IsoItem, inRoom:Boolean = true):void 
		{
			if (!item.block) return; //如果ITEM不挡路
			var tmpx:int;
			var tmpz:int;
			var skip:Boolean;
			//设置不可行走和放置 XXX 只是觉得放在类实例化过程不太合适,应该加入场景之后才设置不可走,所以放在了这里
			for (var i:int = 0; i < item.grid_size_x; i++) {
				
				item.nodes[item.x + i] = new Object();
				
				for (var j:int = 0; j < item.grid_size_z; j++) {
					tmpx = item.x + i;
					tmpz = item.z + j;
					
					if (inRoom) 
					{
						if (!_worldState.checkInRoom(tmpx,tmpz)) 
						{
							continue;
						}
					}
					
					if (nodeItems[tmpx]) 
					{
						if (nodeItems[tmpx][tmpz]) 
						{
							if (!(nodeItems[tmpx][tmpz] is Wall )) 
							{
								skip = true;
							}
							
						}
					}
					if (!skip) 
					{
						item.nodes[tmpx][tmpz] = true;
						this._worldState.grid.setWalkable(tmpx, tmpz, false);
						this._worldState.grid.setDiyable(tmpx, tmpz, false);
						this.saveNodeItems(item,tmpx,tmpz);
					}
					
					//门的特殊处理
					if (item is Door) {
						if ( i==0 && j ==0) {
							
							if ((item as Door).mirror) 
							{
								this._worldState.grid.setDiyable(tmpx, tmpz+1, false);
							}else {
								this._worldState.grid.setDiyable(tmpx+1, tmpz, false);
							}
						}
						this._worldState.grid.setWalkable(tmpx, tmpz, true);
					}
				}
			}
		}
		
		override public function removeToGrid(item:IsoItem, inRoom:Boolean = true):void 
		{
			var tmpx:int;
			var tmpz:int;
			var skip:Boolean;
			if (item) 
			{
				//设置不可行走和放置 XXX 只是觉得放在类实例化过程不太合适,应该加入场景之后才设置不可走,所以放在了这里
				for (var i:int = 0; i < item.grid_size_x; i++) {
					for (var j:int = 0; j < item.grid_size_z; j++) {
						
						tmpx = item.x + i;
						tmpz = item.z + j;
						if (inRoom) 
						{
							if (!_worldState.checkInRoom(tmpx,tmpz)) 
							{
								continue;
							}
						}
						
						if (nodeItems[tmpx]) 
						{
							if (nodeItems[tmpx][tmpz]) 
							{
								if (nodeItems[tmpx][tmpz]!=item  && !(nodeItems[tmpx][tmpz] is Wall)) 
								{
									skip = true;
								}
								
							}
						}
						if (!skip) 
						{
							this._worldState.grid.setWalkable(tmpx, tmpz, true);
							this._worldState.grid.setDiyable(tmpx, tmpz, true);
							removeNodeItems(item,tmpx,tmpz);
						}
						
						
						if (item is Door) {
							if ( i==0 && j ==0) {
								if ((item as Door).mirror) 
								{
									this._worldState.grid.setDiyable(tmpx, tmpz+1, true);
								}else {
									this._worldState.grid.setDiyable(tmpx+1, tmpz, true);
								}
							}
							this._worldState.grid.setWalkable(tmpx, tmpz, false);
						}
					}
				}
				item.nodes = new Object;
			}
		}
		
		override public function saveNodeItems(item:IsoItem, x:int, z:int):void 
		{
			super.saveNodeItems(item, x, z);
		}
		
		override public function removeNodeItems(item:IsoItem, x:int, z:int):void 
		{
			super.removeNodeItems(item, x, z);
		}
		
		public function get player():Player		
		{
			return this._player;
		}
		
		//根据ID获得物件 2011.11.14
		public function getItemById(id:int):BaseItem
		{
			for (var i:int = 0; i < this._items.length; i++)
			{
				var data:Object = (_items[i] as BaseItem).data;
				if (data.hasOwnProperty("id"))
				{
					if (parseInt(data["id"]) == id) return _items[i];
				}
			}
			return null;
		}
		
		public function set fogVisible(value:Boolean):void
		{
			if (value) showFog();
			else hideFog();
		}
		
		//清除所有矿的连点次数
		public function clearMineClickTimes():void
		{
			for (var i:int = 0; i < _items.length; i++)
			{
				if (_items[i] is Mine) (_items[i] as Mine).clickTimes = 0;
			}
		}
		
		//雾
		public function refreshFog(w:int,h:int,color:int,alpha:Number,initData:ByteArray):void
		{
			clearFog();
			
			(_view.isoView.getLayer(WorldView.LAYER_REALTIME_SORT) as IsoLayer).sortFun();
			
			var sceneClassVo:SceneClassVo = DataManager.getInstance().getCurrentScene();
			var rect:Rectangle = DataManager.getInstance().sceneFogPicRect[sceneClassVo.sceneId];
			if (!rect)
			{
				rect = _view.isoView.camera.getBounds(_view.isoView.camera);
				DataManager.getInstance().sceneFogPicRect[sceneClassVo.sceneId] = rect;
			}
			var scenePic:BitmapData = DataManager.getInstance().sceneFogPic[sceneClassVo.sceneId];
			if (!scenePic)
			{
				scenePic = new BitmapData(rect.width, rect.height, true, 0xFF000000);
				//截图
				//var mat:Matrix = new Matrix;
				//mat.translate(-rect.left, -rect.top);
				//scenePic.draw(_view.isoView.camera,mat);
				
				DataManager.getInstance().sceneFogPic[sceneClassVo.sceneId] = scenePic;
			}
			
			fog = new FogView(scenePic, color, alpha, IsoUtil.TILE_SIZE);
			_view.isoView.camera.addChild(fog);
			fog.x = rect.left;
			fog.y = rect.top;
			
			//var point:Point = IsoUtil.isoToScreen(new Point3D(IsoUtil.TILE_SIZE / 2, 0, IsoUtil.TILE_SIZE / 2));
			var point:Point = new Point;
			point = (_view.isoView.layers[0] as IsoLayer).localToGlobal(point);
			point = fog.globalToLocal(point);
			fog.offsetX = point.x;
			fog.offsetY = point.y;
			
			if(!FogManager.getInstance().hasEventListener(FogEvent.FOG_SAVE))
				FogManager.getInstance().addEventListener(FogEvent.FOG_SAVE, saveFogData);
				
			FogManager.getInstance().refreshScene(w, h, sceneClassVo.sceneId,initData);
		}
		private function saveFogData(event:FogEvent):void
		{
			var fogData:ByteArray = DataManager.getInstance().getLocalFogData()["data"];
			for each(var fogNode:FogNode in event.list)
			{
				FogManager.writeNodeIntoByteArray(fogNode, fogData);
			}
			DataManager.getInstance().localData.flush();
		}
		public function hideFog():void
		{
			if (fog) fog.visible = false;
		}
		public function showFog():void
		{
			if (fog) fog.visible = true;
		}
		public function clearFog():void
		{
			if (fog)
			{
				fog.clear();
				fog = null;
			}
		}
		
		public function get view():WorldView
		{
			return _view;
		}
		
		public function get initFlg():Boolean 
		{
			return _initFlg;
		}
		
	}

}