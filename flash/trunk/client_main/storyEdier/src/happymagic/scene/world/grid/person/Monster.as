package happymagic.scene.world.grid.person 
{
	import com.friendsofed.isometric.Point3D;
	import flash.events.Event;
	import flash.events.TimerEvent;
	import flash.geom.Rectangle;
	import flash.utils.Timer;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.world.control.collision.CollisionController;
	import happyfish.scene.world.control.collision.CollisionEvent;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.MonsterVo;
	import happyfish.scene.world.WorldState;
	import happymagic.model.vo.SceneVo;
	import happymagic.scene.world.control.AvatarCommand;
	import happymagic.scene.world.control.HandleBattleCommand;
	import happymagic.scene.world.control.MouseDefaultAction;
	import happymagic.scene.world.MagicWorld;
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class Monster extends ActItem
	{
		private var fiddleRange:Array;
		private var fiddleTimer:Timer;
		
		private var surpriseIcon:Mood_RedSurprise;
		private var surpriseCallback:Function;
		private var supriseTimer:Timer;
		
		public function Monster(vo:MonsterVo, $worldState:WorldState,__callBack:Function=null)  
		{	
			super(vo, $worldState,vo.id, vo.x, vo.z, vo.currentHp, __callBack);
			typeName = "Monster";
			
			fiddleRange = new Array;
			
			var fiddleWidth:int = Math.max(vo["fiddleRangeX"], 1);
			var fiddleHeight:int = Math.max(vo["fiddleRangeZ"], 1);
			
			var sceneVo:SceneVo = ($worldState.world as MagicWorld).sceneVo;
			var maxX:int = vo.x + fiddleWidth;
			maxX = maxX > sceneVo.numCols ? sceneVo.numCols : maxX;
			var maxZ:int = vo.z + fiddleHeight;
			maxZ = maxZ > sceneVo.numRows ? sceneVo.numRows : maxZ;
			
			for (var i:int = vo.x; i < maxX; i++)
			{
				for (var j:int = vo.z; j < maxZ; j++)
				{
					var node:Node = _worldState.grid.getNode(i,j);
					if (node.walkable) fiddleRange.push(node);
				}
			}
			
			if (fiddleRange.length>1) //如果怪会闲逛
			{
				fiddleTimer = new Timer(500);
				fiddleTimer.addEventListener(TimerEvent.TIMER, monsterFiddle);
				startFiddle();
				setCollision(vo.collisionRange); //参与碰撞
				
				EventManager.getInstance().addEventListener(CollisionEvent.COLLISION_IN, onCollision);
				EventManager.getInstance().addEventListener(CollisionEvent.COLLISION_OVER, onCollision);
			}
			else block = true; //否则不参与碰撞 但是挡路
		}
		
		override public function clear():void 
		{
			super.clear();
			
			if (supriseTimer) 
			{
				supriseTimer.stop();
				supriseTimer.removeEventListener(TimerEvent.TIMER, onTimerComplete);
				supriseTimer = null;
			}
		}
		
		override protected function view_complete():void 
		{
			super.view_complete();
		}
		
		public function startFiddle():void
		{
			if (fiddleRange.length == 0) return;
			if (fiddleTimer) fiddleTimer.start();
		}
		
		public function stopFiddle():void
		{
			if (fiddleTimer) fiddleTimer.stop();
		}
		
		private function monsterFiddle(event:TimerEvent):void
		{
			var player:Player = (_worldState.world as MagicWorld).player;
			if (player.currentAction == Player.FIGHTING || player.currentAction == Player.NOTICED) return;
			if (_path && _index < _path.length) return;
			if (Math.random() < 0.75) return; //加点间隔的不稳定性
			
			var index:int = Math.floor(Math.random() * fiddleRange.length);
			var node:Node = fiddleRange[index] as Node;
			
			var target:Point3D = new Point3D(node.x, 0, node.y);
			var command:AvatarCommand = new AvatarCommand(target);
			this.addCommand(command);
		}
		
		override protected function makeView():IsoSprite
		{
			super.makeView();
			
			this._view.setPos(new Point3D(vo["x"],0, vo["z"]));
			
			return _view;
		}
		
		override public function remove():void
		{
			if (fiddleTimer)
			{
				fiddleTimer.stop();
				fiddleTimer.removeEventListener(TimerEvent.TIMER, monsterFiddle);
				fiddleTimer = null;
			}
			
			stopCollision();
			
			super.remove();
			
			if (block) //清除占格
			{
				this._worldState.world.removeToGrid(this, false);
			}
		}
		
		public function stopCollision():void
		{
			if (canCollision)
			{
				cancelCollision();
				EventManager.getInstance().removeEventListener(CollisionEvent.COLLISION_IN, onCollision);
				EventManager.getInstance().removeEventListener(CollisionEvent.COLLISION_OVER, onCollision);
			}
		}
		
		public function showSurprise(callback:Function,dir:Point3D):void
		{
			if(!surpriseIcon) surpriseIcon = new Mood_RedSurprise;
			view.container.addChild(surpriseIcon);
			
			surpriseCallback = callback;
			
			supriseTimer = new Timer(1000,1);
			supriseTimer.addEventListener(TimerEvent.TIMER_COMPLETE, onTimerComplete);
			supriseTimer.start();
			
			addCommand(new AvatarCommand(null, null, dir));
		}
		
		private function onTimerComplete(event:TimerEvent):void
		{
			event.target.removeEventListener(TimerEvent.TIMER_COMPLETE, onTimerComplete);
			
			if(surpriseIcon.parent) surpriseIcon.parent.removeChild(surpriseIcon);
			
			if (surpriseCallback != null) surpriseCallback.apply();
		}
		
		private function onCollision(event:CollisionEvent):void
		{
			if (!event.objId == vo["id"]) return;
			var collisionInfo:Object = CollisionController.getInstance().getCollisionInfo(data["id"]);
			if (collisionInfo)
			{
				for(var id:String in collisionInfo) //遍历受到碰撞的区域
				{
					if (id == "0")
					{
						HandleBattleCommand.getInstance().monsterChargeToPlayer(this); //如果和主角碰撞 开始战斗
						break;
					}
				}
			}
		}
		
	}
}