package happymagic.scene.world.bigScene 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.TimerEvent;
	import flash.geom.Point;
	import flash.utils.ByteArray;
	import flash.utils.Timer;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happyfish.scene.astar.Node;
	import happyfish.scene.world.grid.BaseItem;
	import happymagic.model.vo.MineVo;
	import happymagic.model.vo.MoneyType;
	import happymagic.model.vo.MonsterVo;
	import happymagic.model.vo.PortalVo;
	import happymagic.model.vo.RoleVo;
	import happymagic.model.vo.SceneClassVo;
	import happymagic.scene.world.grid.person.Mine;
	import happymagic.scene.world.grid.person.Monster;
	import happymagic.scene.world.grid.item.Portal;
	import happymagic.scene.world.MagicWorld;
	import happymagic.scene.world.SceneType;
	//import happyfish.scene.personAction.control.PersonActionControl;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.WorldState;
	import happyfish.utils.CustomTools;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.task.TaskInfoView;
	import happymagic.events.TaskEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.AvatarType;
	import happymagic.model.vo.AvatarVo;
	import happymagic.model.vo.NpcVo;
	import happymagic.model.vo.SceneVo;
	import happymagic.scene.world.bigScene.events.BigSceneEvent;
	
	/**
	 * ...
	 * @author jj
	 */
	public class BigSceneView extends EventDispatcher
	{
		
		private var sceneData:SceneVo;
		private var npcVos:Vector.<NpcVo>;
		
		private var npcs:Array;
		private var enemyVos:Array;
		
		private var enemyList:Array;
		public var npcList:Array;
		private var enemyTimer:Timer;
		private var _worldState:WorldState;
		private var massesList:Array;
		
		//怪 2011.11.11
		private var monsterVos:Vector.<MonsterVo>;
		private var monsterList:Array;
		
		//矿 2011.11.11
		private var mineVos:Vector.<MineVo>;
		private var roleList:Array;
		
		public function BigSceneView($world_state:WorldState) 
		{
			_worldState = $world_state;
			
			enemyList = new Array();
			npcList = new Array();
			massesList = new Array();
			roleList = new Array();
			monsterList = new Array;
			
			EventManager.getInstance().addEventListener(TaskEvent.TASKS_STATE_CHANGE, taskChange);
		}
		
		private function taskChange(e:TaskEvent):void 
		{
			initAllNpcPao();
		}
		
		/**
		 * 刷新所有NPC的任务状态表现
		 */
		public function initAllNpcPao():void {
			for (var i:int = 0; i < npcList.length; i++) 
			{
				npcList[i].initPaoIcon();
			}
		}
		
		public function setData(__sceneVo:SceneVo, __npcs:Vector.<NpcVo>,__monsters:Vector.<MonsterVo>,__mine:Vector.<MineVo>):void {
			sceneData = __sceneVo;
			npcVos = __npcs;
			monsterVos = __monsters;
			mineVos = __mine;
			
			clear();
			
			//PersonActionControl.getInstance().state = _worldState;
			//PersonActionControl.getInstance().actions = DataManager.getInstance().getSceneClassById(sceneData.sceneId).actions;
			
			//雾
			if (DataManager.getInstance().getSceneClassById(sceneData.sceneId).withFog)
			{
				var initData:Object = DataManager.getInstance().getLocalFogData();
				(_worldState.world as MagicWorld).refreshFog(__sceneVo.numCols, __sceneVo.numCols,0,0.5,initData["data"]);
				(_worldState.world as MagicWorld).showFog();
			}
			else (_worldState.world as MagicWorld).clearFog();
			
			//创建怪物和矿 NPC
			createMonsters();
			createMine();
			createNpc();
			createRoles();
		}
		
		/**
		 * 创建场景内佣兵
		 */
		private function createRoles():void 
		{
			var rolevolist:Vector.<RoleVo> = DataManager.getInstance().roleList;
			var tmprole:SceneRolesView;
			for (var i:int = 0; i < rolevolist.length; i++) 
			{
				tmprole = new SceneRolesView(rolevolist[i],_worldState);
				roleList.push(tmprole);
				_worldState.world.addItem(tmprole);
			}
		}
		
		public function clear():void {
			
			var i:int;
			for (i = 0; i < npcList.length; i++) 
			{
				npcList[i].remove();
			}
			npcList = new Array();
			
			for (i = 0; i < roleList.length; i++) 
			{
				roleList[i].remove();
			}
			roleList = new Array();
			
			
			
			if (DisplayManager.dungeonTip) DisplayManager.dungeonTip.hide();
			
			//PersonActionControl.getInstance().clear();
		}
		
		public function hideAllNpc():void 
		{
			for (var i:int = 0; i < npcList.length; i++) 
			{
				var item:NpcView = npcList[i];
				item.visible = false;
			}
		}
		
		public function showAllNpc():void {
			for (var i:int = 0; i < npcList.length; i++) 
			{
				var item:NpcView = npcList[i];
				item.visible = true;
			}
		}
		
		private function createNpc():void
		{
			if (!npcVos) return;
			
			var tmpNpc:NpcView;
			for (var i:int = 0; i < npcVos.length; i++) 
			{
				tmpNpc = new NpcView(npcVos[i],_worldState);
				npcList.push(tmpNpc);
				_worldState.world.addItem(tmpNpc);
				
			}
			
			//createMasses();
		}
		
		/**
		 * 创建行人群众
		 */
		private function createMasses():void
		{
			var tmpobj:Object;
			var tmpid:uint;
			var tmp:MassesView;
			for (var i:int = 0; i < 10; i++) 
			{
				//tmpobj = new Object();
				//
				//var tmpnode:Node = DataManager.getInstance().getCustomSceneDoor();
				//
				//tmpobj.x = tmpnode.x;
				//tmpobj.z = tmpnode.y;
				//
				//tmpobj.class_name = DataManager.getInstance().getCustomStudentAvatarVo().className;
				//
				//tmp = new MassesView(tmpobj, _worldState);
				//massesList.push(tmp);
				//_worldState.world.addItem(tmp);
				
				//PersonActionControl.getInstance().addPerson(tmp as Person);
			}
			
		}
		
		//创建怪 2011.11.14
		private function createMonsters():void
		{
			if (!monsterVos) return;
			var monster:Monster;
			monsterList.splice(monsterList.length);
			for (var i:int = 0; i < monsterVos.length; i++)
			{
				monster = new Monster(monsterVos[i], _worldState);
				_worldState.world.addItem(monster);
				monsterList.push(monster);
			}
		}
		
		public function hideAllMonsters():void
		{
			for (var i:int = 0; i < monsterList.length; i++) 
			{
				var item:Monster = monsterList[i];
				item.visible = false;
			}
		}
		
		public function showAllMonsters():void
		{
			for (var i:int = 0; i < monsterList.length; i++) 
			{
				var item:Monster = monsterList[i];
				item.visible = true;
			}
		}
		
		//创建矿 2011.11.14
		private function createMine():void
		{
			if (!mineVos) return;
			var mine:Mine;
			for (var i:int = 0; i < mineVos.length; i++)
			{
				mine = new Mine(mineVos[i], _worldState);
				_worldState.world.addItem(mine);
			}
		}
		
	}

}