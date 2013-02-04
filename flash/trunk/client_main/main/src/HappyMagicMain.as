package 
{
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	import com.brokenfunction.json.decodeJson;
	import com.brokenfunction.json.encodeJson;
	import com.greensock.easing.Back;
	import com.greensock.easing.Bounce;
	import com.greensock.easing.Circ;
	import com.greensock.easing.Elastic;
	import com.greensock.easing.Expo;
	import com.greensock.easing.Linear;
	import com.greensock.easing.Quad;
	import com.greensock.easing.Strong;
	import flash.display.Sprite;
	import flash.display.StageAlign;
	import flash.display.StageScaleMode;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.net.registerClassAlias;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.text.TextField;
	import happyfish.display.ui.accordion.Accordion;
	import happyfish.display.ui.NumSelecterView;
	import happyfish.display.ui.Pagination;
	import happyfish.display.ui.RadioButtonGroup;
	import happyfish.events.MainEvent;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.ActTipsManager;
	import happyfish.manager.BgMusicManager;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.manager.mouse.MouseManager;
	import happyfish.manager.ShareObjectManager;
	import happyfish.manager.SoundEffectManager;
	import happyfish.manager.SwfURLManager;
	import happyfish.model.UrlConnecter;
	import happyfish.scene.camera.CameraControl;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.control.IsoPhysicsControl;
	import happyfish.time.Time;
	import happyfish.utils.display.ItemDropController;
	import happyfish.utils.display.McUtil;
	import happyfish.utils.display.TextFieldTools;
	import happyfish.utils.display.TextFieldUtil;
	import happyfish.utils.display.TitleSprite;
	import happyfish.utils.MouseActionCommand;
	import happymagic.display.control.StoryPlayCommand;
	import happymagic.display.view.Console;
	import happymagic.display.view.diary.DiaryView;
	import happymagic.display.view.dungeon.DungeonInfoView;
	import happymagic.display.view.dungeon.MiningHand;
	import happymagic.display.view.friends.FriendsView;
	import happymagic.display.view.maininfo.MainInfoView;
	import happymagic.display.view.mainMenu.MainMenuView;
	import happymagic.display.view.promptFrame.BusinessLevelNoEnoughView;
	import happymagic.display.view.promptFrame.BusinessLevelUpView;
	import happymagic.display.view.promptFrame.CoinNoEnoughView;
	import happymagic.display.view.promptFrame.GemNoEnoughView;
	import happymagic.display.view.promptFrame.GetNewItemsView;
	import happymagic.display.view.promptFrame.NeedMoreItemView;
	import happymagic.display.view.promptFrame.NeedMorePhysicalStrengthView;
	import happymagic.display.view.RightCenterMenuView;
	import happymagic.display.view.RightMenuView;
	import happymagic.display.view.SysMenuView;
	import happymagic.display.view.ui.AvatarSprite;
	import happymagic.display.view.ui.AwardResultView;
	import happymagic.display.view.ui.BuyItemPopUp;
	import happymagic.display.view.ui.DefaultAwardItemRender;
	import happymagic.display.view.ui.ItemIcon;
	import happymagic.display.view.ui.ItemIconView;
	import happymagic.display.view.ui.personMsg.PersonMsgManager;
	import happymagic.events.DiyEvent;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.mouse.MagicMouseIconType;
	import happymagic.manager.PublicDomain;
	import happymagic.manager.UiManager;
	import happymagic.model.command.BuyItemsCommand;
	import happymagic.model.command.GetSceneClassCommand;
	import happymagic.model.command.initCommand;
	import happymagic.model.command.initStaticCommand;
	import happymagic.model.command.MoveSceneCommand;
	import happymagic.model.command.ShowStoryCommand;
	import happymagic.model.MagicUrlLoader;
	import happymagic.model.vo.ai.AIActionVo;
	import happymagic.model.vo.ai.AIConditionVo;
	import happymagic.model.vo.ai.AIFormationVo;
	import happymagic.model.vo.ai.AIRoleVo;
	import happymagic.model.vo.ai.AIScriptVo;
	import happymagic.model.vo.ai.AITargetVo;
	import happymagic.model.vo.AnimationVo;
	import happymagic.model.vo.EffectDictionary;
	import happymagic.model.vo.EffectVo;
	import happymagic.model.vo.IllustrationsVo;
	import happymagic.model.vo.RoleVo;
	import happymagic.model.vo.SkillAndItemVo;
	import happymagic.model.vo.StatusVo;
	import happymagic.model.vo.StoryVo;
	import happymagic.scene.world.control.ChangeSceneCommand;
	import happymagic.scene.world.MagicState;
	import happymagic.scene.world.MagicView;
	import happymagic.scene.world.MagicWorld;
	//import happymagic.task.manager.MagicTaskStateManager;
	import happymagic.utils.AvatarUtil;
	//import happymagic.display.view.roomUp.RoomUpView;
	//import happymagic.display.view.worldMap.WorldMap;
	//import happymagic.display.view.worldMap.WorldMapSceneIconView;
	//import happymagic.display.view.worldMap.WorldMapSceneInfoView;

	
	/**
	 * ...
	 * @author slam
	 */
	public class HappyMagicMain extends Sprite 
	{
		private var sceneSprite:Sprite;
		private var uiSprite:UiManager;
		private var mouseIconSprite:Sprite;
		private var storyUiSprite:Sprite;
		
		public function HappyMagicMain():void 
		{
			//Security.allowDomain("*");   
			
			registerClassAlias("TextFieldTools2", TextFieldTools);
			registerClassAlias("ActTipsManager2", ActTipsManager);
			registerClassAlias("Linear2", Linear);
			registerClassAlias("Back2", Back);
			registerClassAlias("Bounce2", Bounce);
			registerClassAlias("Circ2", Circ);
			registerClassAlias("Elastic2", Elastic);
			registerClassAlias("Expo2", Expo);
			registerClassAlias("Quad2", Quad);
			registerClassAlias("Strong2", Strong);
			
			
			registerClassAlias("MainInfoView2", MainInfoView);
			registerClassAlias("FriendsView2", FriendsView);
			registerClassAlias("MainMenuView2", MainMenuView);
			registerClassAlias("RightMenuView2", RightMenuView);
			registerClassAlias("FriendsView2", FriendsView);
			registerClassAlias("SysMenuView2", SysMenuView);
			registerClassAlias("DiaryView2", DiaryView);
			registerClassAlias("AwardResultView2", AwardResultView);
			registerClassAlias("RoleVo2", RoleVo);
			registerClassAlias("StatusVo2", StatusVo);
			registerClassAlias("AnimationVo2", AnimationVo);
			registerClassAlias("SkillAndItemVo2", SkillAndItemVo);
			registerClassAlias("EffectVo2", EffectVo);
			registerClassAlias("EffectDictionary2", EffectDictionary);
			registerClassAlias("AIActionVo2", AIActionVo);
			registerClassAlias("AIConditionVo2", AIConditionVo);
			registerClassAlias("AIFormationVo2", AIFormationVo);
			registerClassAlias("AIRoleVo2", AIRoleVo);
			registerClassAlias("AIScriptVo2", AIScriptVo);
			registerClassAlias("AITargetVo2", AITargetVo);
			registerClassAlias("IllustrationsVo2", IllustrationsVo);
			registerClassAlias("ItemIconView2", ItemIconView);
			registerClassAlias("DiyEvent2", DiyEvent);
			registerClassAlias("Pagination2", Pagination);
			
			NumSelecterView;
			BuyItemsCommand;
			ItemIcon;
			AvatarUtil;
			PersonMsgManager;
			Accordion;
			encodeJson(new Object());
			MouseActionCommand;
			McUtil;
			DungeonInfoView;
			DefaultAwardItemRender;
			BuyItemPopUp;
			TextFieldUtil;
			RadioButtonGroup;
			RightCenterMenuView;
			TitleSprite;
			GemNoEnoughView;
			BusinessLevelNoEnoughView;
			BusinessLevelUpView;
			CoinNoEnoughView;
			NeedMoreItemView;
			NeedMorePhysicalStrengthView;
			AvatarSprite;
			GetNewItemsView;
			ItemDropController;
			
			Time.setCurrTime(new Date().getTime()/1000);
			
			InterfaceURLManager.getInstance().tmpUrls = new Array();
			if (stage) this.ready_startInit();
			else addEventListener(Event.ADDED_TO_STAGE, ready_startInit);
		}
		
		private function ready_startInit(e:Event=null):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, ready_startInit);
			
			//parent.addChild(new Fps());
			
			//默认场景ID
			PublicDomain.getInstance().setVar("defaultSceneId", 1000001);
			
			//ui层容器与管理器
			uiSprite = new UiManager();
			//场景容器
			sceneSprite = new Sprite();
			//手型容器
			mouseIconSprite = new Sprite();
			//幕布容器
			storyUiSprite = new Sprite();
			
			//初始化容器
			addChild(sceneSprite);
			addChild(uiSprite);
			addChild(storyUiSprite);
			addChild(mouseIconSprite);
			
			uiSprite.showSceneOutMv(startInit);
			trace("startInit");
			
			EventManager.dispatchEvent(new Event(MainEvent.MAIN_INIT_COMPLETE));
		}
		
		private function startInit(e:Event = null):void {
			
			//初始化UI
			uiSprite.init();
			
			//放入显示管理类
			DisplayManager.uiSprite = this.uiSprite;
			PublicDomain.getInstance().setVar("uiSprite",uiSprite);
			DisplayManager.sceneSprite = this.sceneSprite;
			DisplayManager.storyUiSprite = storyUiSprite;
			DisplayManager.mouseIconSprite = mouseIconSprite;
			
			
			
			//设置全局对齐方式与不缩放
			stage.align = StageAlign.TOP_LEFT;
			stage.scaleMode = StageScaleMode.NO_SCALE;
			
			//镜头控制
			CameraControl.getInstance().init(PublicDomain.WORLD_WIDTH, PublicDomain.WORLD_HEIGHT);
			
			//初始化鼠标手型控制器
			MouseManager.getInstance().initManager(stage);
			//MouseManager.getInstance().addMouseIcon(MagicMouseIconType.DEFAULT_HAND, new mouse_default());
			//MouseManager.getInstance().defaultMouseIcon= MouseManager.getInstance().getMouseIcon(MagicMouseIconType.DEFAULT_HAND);
			//设置默认手型
			//MouseManager.getInstance().addMouseIcon(MagicMouseIconType.DOWN_HAND,new mouse_down_icon());
			//MouseManager.getInstance().addMouseIcon(MagicMouseIconType.OVER_HAND, new mouse_over_icon());
			MouseManager.getInstance().addMouseIcon(MagicMouseIconType.STUDENT_HAND, new mouse_student());
			MouseManager.getInstance().addMouseIcon(MagicMouseIconType.PICK_HAND, new mouse_pick());
			MouseManager.getInstance().addMouseIcon(MagicMouseIconType.MINING_HAND, new MiningHand());
			MouseManager.getInstance().addMouseIcon(MagicMouseIconType.SWORD_HAND, new SwordHand());
			//更新当前手型
			MouseManager.getInstance().setIcon();
			
			//读取静态数据
			loadStatic();
			
			//控制台
			new Console(this);
		}
		
		/**
		 * 静态数据读取
		 */
		private function loadStatic():void
		{
			//uiSprite.showLoading();
			
			//初始化urlLoader的事件派发实例
			MagicUrlLoader.showSysMsg = DisplayManager.showSysMsg;
			
			//请求
			var init_static_command:initStaticCommand = new initStaticCommand();
			init_static_command.addEventListener(Event.COMPLETE, loadInit);
			init_static_command.load();
			
		}
		
		/**
		 * 静态数据读取完毕与动态数据加载
		 * @param	e
		 */
		private function loadInit(e:Event):void
		{
			//读取玩家初始信息,包括场景\屋中的学生\玩家魔法等
			var init_command:initCommand = new initCommand();
			init_command.addEventListener(Event.COMPLETE, init);
			init_command.load();
		}
		
		/**
		 * 动态数据加载完毕,开始初始化场景
		 * @param	e
		 */
		private function init(e:Event = null):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, init);
			// entry point
			
			//场景容器位置
			this.sceneSprite.x = 0;
			this.sceneSprite.y = IsoUtil.TILE_SIZE / 2;
			
			//创建存储世界对象的类
			var magicState:MagicState = new MagicState();
			
			//世界view
			var magicView:MagicView = new MagicView(magicState);
			this.sceneSprite.addChild(magicView); //将世界view加入显示列表
			
			//准备创建世界
			var world:MagicWorld = new MagicWorld(magicState);
			DataManager.getInstance().setVar("magicWorld", world);
			
			//侦听世界创建完成事件
			EventManager.getInstance().addEventListener(SceneEvent.SCENE_COMPLETE, worldCreate_complete);
			
			//物理
			var physicsControl:IsoPhysicsControl = new IsoPhysicsControl();
			DataManager.getInstance().physicsControl = physicsControl;
			
			//本地缓存数据
			ShareObjectManager.getInstance().init("happyMagic",
				{ bgSound:true, soundEffect:true } //默认数据
			);
			
			//音效
			SoundEffectManager.getInstance();
			
			//初始化worldState
			magicState.init(magicView, world, physicsControl);
			
			DataManager.getInstance().worldState = magicState;
			
			//鼠标移动
            addEventListener(MouseEvent.MOUSE_MOVE, magicView.onMouseMove);
			
			//任务状态监听
			//var taskStateManager:MagicTaskStateManager = new MagicTaskStateManager();
			//DataManager.getInstance().setVar("taskStateManager", taskStateManager);
			
			//判断是否加载引导模块,如果有就先加载引导，再创建场景
			if (DataManager.getInstance().guides.length > 0)
			{
				var guidesActVo:ActVo = new ActVo();
				guidesActVo.actName = "guides";
				guidesActVo.moduleUrl = SwfURLManager.getInstance().getOtherSWfUrl("guides");
				var loadingitem:LoadingItem = ActModuleManager.getInstance().addActModule(guidesActVo);
			}
				
			createWorld();
		}
		
		private function guidesLoad_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, guidesLoad_complete);
			createWorld();
			
		}
		
		/**
		 * 场景开始创建
		 */
		private function createWorld():void
		{
			//如果没有当前场景的静态数据 先请求静态数据
			var sceneId:uint = DataManager.getInstance().currentUser.currentSceneId;
			var command:MoveSceneCommand = new MoveSceneCommand();
			command.addEventListener(Event.COMPLETE, onMoveSceneComplete);
			command.moveScene(sceneId);
		}
		
		private function onMoveSceneComplete(event:Event):void
		{
			event.target.removeEventListener(Event.COMPLETE, onMoveSceneComplete);
			
			//背景音乐
			BgMusicManager.getInstance().soundFlag=ShareObjectManager.getInstance().bgSound;
			
			//初始化模块
			uiSprite.initModules();
			
			EventManager.dispatchEvent(new Event(MainEvent.MAIN_DATA_COMPELTE));
		}
		
		private function worldCreate_complete(e:SceneEvent):void 
		{
			
		}
		
		
	}
	
}