package happymagic.scene.world.control 
{
	import com.friendsofed.isometric.Point3D;
	import com.greensock.TweenMax;
	import flash.display.Sprite;
	import flash.display.Stage;
	import flash.events.Event;
	import flash.events.TimerEvent;
	import flash.geom.Rectangle;
	import flash.utils.Timer;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.scene.astar.Node;
	import happyfish.time.Time;
	import happyfish.utils.display.McShower;
	import happymagic.display.control.StoryPlayCommand;
	import happymagic.display.view.dungeon.DungeonInfoView;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.UiManager;
	import happymagic.model.command.BattleInitCommand;
	import happymagic.model.command.MoveSceneCommand;
	import happymagic.model.vo.MonsterVo;
	import happymagic.model.vo.SceneClassVo;
	import happymagic.scene.world.grid.person.Monster;
	import happymagic.scene.world.grid.person.Player;
	import happymagic.scene.world.MagicWorld;
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class HandleBattleCommand 
	{
		private var player:Player;
		private var monster:Monster;
		
		private var mask:Sprite; //一个遮罩 用以屏蔽操作事件
		
		public function HandleBattleCommand()
		{
			EventManager.getInstance().addEventListener(SceneEvent.SCENE_COMPLETE, resetPlayer);
			
			mask = new Sprite;
			with (mask.graphics)
			{
				beginFill(0);
				drawRect(0, 0, 100, 100);
				endFill();
			}
			mask.alpha = 0;
		}
		
		private function resetPlayer(event:Event):void
		{
			player = (DataManager.getInstance().worldState.world as MagicWorld).player;
		}
		
		public function playerChargeToMonster(monster:Monster):void
		{
			this.monster = monster;
			monster.stopMove(); //停止正在进行的移动
			monster.stopFiddle(); //停止继续闲逛
			
			var startNode:Node = new Node(player.x, player.z);
			var rect:Rectangle = new Rectangle(monster.gridPos.x, monster.gridPos.z, monster.vo.sizeX, monster.vo.sizeZ);
			var targetNode:Node = DataManager.getInstance().worldState.world.findCanWalkNodeFromRect(startNode, rect);
			if (!targetNode) return;
			
			var target:Point3D = new Point3D(targetNode.x, 0, targetNode.y);
			var dir:Point3D = new Point3D(monster.gridPos.x, 0 , monster.gridPos.z);
			
			var command:AvatarCommand = new AvatarCommand(target, showMonsterSurprise, dir);
			player.addCommand(command);
			
			player.currentAction = Player.MOVING;
		}
		
		private function showMonsterSurprise():void
		{
			monster.showSurprise(startBattle, new Point3D(player.gridPos.x, player.gridPos.z));
			monster.stopFiddle();
		}
		
		public function monsterChargeToPlayer(monster:Monster):void
		{
			if (!player.isBusy && !player.untouchable)
			{
				this.monster = monster;
				monster.stopMove(); //停止正在进行的移动
				monster.stopFiddle(); //停止继续闲逛
				
				player.stopMove(); //主角停止移动
				player.currentAction = Player.NOTICED; //将主角设置成被关注
				
				monster.showSurprise(monsterGo,new Point3D(player.gridPos.x,0,player.gridPos.z));
			}
		}
		
		private function monsterGo():void
		{
			var rect:Rectangle = new Rectangle(player.gridPos.x, player.gridPos.z, 1, 1);
			var targetNode:Node = DataManager.getInstance().worldState.world.findCanWalkNodeFromRect(new Node(monster.gridPos.x,monster.gridPos.z), rect);
			if (targetNode)
			{
				var target:Point3D = new Point3D(targetNode.x, 0, targetNode.y);
				var dir:Point3D = new Point3D(player.gridPos.x, 0 , player.gridPos.z);
				
				var command:AvatarCommand = new AvatarCommand(target, showBattleSign, dir);
				monster.addCommand(command);
				
				var stage:Stage = player.view.container.stage;
				mask.width = stage.stageWidth;
				mask.height = stage.stageHeight;
				stage.addChild(mask);
			}
		}
		
		private function showBattleSign():void
		{
			var stage:Stage = monster.view.container.stage;
			var mcShower:McShower = new McShower(BattleSign, stage, null, null, startBattle);
			mcShower.x = stage.stageWidth / 2;
			mcShower.y = stage.stageHeight / 2;
		}
		
		private function startBattle():void
		{
			player.currentAction = Player.FIGHTING;
			player.clearAllCommand();
			
			//发包获取数据
			var command:BattleInitCommand = new BattleInitCommand(monster.data["id"]);
			command.addEventListener(Event.COMPLETE, battleInit);
		}
		
		public function battleInit(event:Event = null, battleInitData:Object = null):void
		{
			var fightBg:String;
			
			if (event){
				event.target.removeEventListener(Event.COMPLETE, battleInit);
				battleInitData = (event.target as BattleInitCommand).objdata;
				fightBg = (monster.vo as MonsterVo).fightBg;
			}
			
			if (fightBg == null || fightBg == ""){
				var sceneClassVo:SceneClassVo = DataManager.getInstance().getCurrentScene();
				fightBg = sceneClassVo.fightBg;
			}
			if(fightBg != null) battleInitData["BattleVo"]["bgClassName"] = fightBg;
			
			DataManager.getInstance().setVar("battleInitData", battleInitData);
			
			DisplayManager.sceneSprite.visible = false;
			
			var actVo:ActVo = DataManager.getInstance().getActByName("battle");
			ActModuleManager.getInstance().addActModule(actVo);
			EventManager.getInstance().addEventListener(BATTLE_FINISH, onBattleFinish);
			
			if (mask.parent) mask.parent.removeChild(mask);
			
			EventManager.getInstance().dispatchEvent(new Event(BATTLE_BEGIN));
		}
		
		private function onBattleFinish(event:Event):void
		{
			DisplayManager.sceneSprite.visible = true;
			
			player.currentAction = Player.WAITING;
			EventManager.getInstance().removeEventListener("battleFinish", onBattleFinish);
			var result:int = DataManager.getInstance().getVar("battleResult");
			if (result == 1) //如果赢了
			{
				if (monster){
					TweenMax.to(monster.asset, 0.75, { autoAlpha:0, onComplete:monster.remove } );
					if (DisplayManager.dungeonTip) DisplayManager.dungeonTip.hide();
					monster.stopCollision();
					monster = null;
				}
				
				//var sceneId:int = DataManager.getInstance().getVar("battleEndScene");
				//if (sceneId) //如果战斗结束后需要立即跳场景
				//{
					//var command:MoveSceneCommand = new MoveSceneCommand();
					//command.moveScene(sceneId);
				//}
				//else (DataManager.getInstance().worldState.world as MagicWorld).playBgMusic(); //播放场景音乐
				(DataManager.getInstance().worldState.world as MagicWorld).playBgMusic(); //播放场景音乐
			}
			else{
				player.untouchable = true;
				var timer:Timer = new Timer(5000, 1);
				timer.addEventListener(TimerEvent.TIMER_COMPLETE, onTimerComplete);
				timer.start();
			}
			
			StoryPlayCommand.getInstance().checkAndPlay();
		}
		
		private function onTimerComplete(event:TimerEvent):void
		{
			var timer:Timer = event.target as Timer;
			timer.stop();
			timer.removeEventListener(TimerEvent.TIMER_COMPLETE, onTimerComplete);
			player.untouchable = false;
		}
		
		private static var _instance:HandleBattleCommand;
		public static function getInstance():HandleBattleCommand
		{
			if (!_instance)
			{
				_instance = new HandleBattleCommand;
				_instance.player = (DataManager.getInstance().worldState.world as MagicWorld).player;
			}
			return _instance;
		}
		
		public static const BATTLE_BEGIN:String = "battleBegin";
		public static const BATTLE_FINISH:String = "battleFinish";
	}

}