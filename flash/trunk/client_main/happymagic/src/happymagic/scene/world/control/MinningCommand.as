package happymagic.scene.world.control 
{
	import com.friendsofed.isometric.Point3D;
	import com.greensock.TweenMax;
	import flash.events.Event;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.mouse.MouseManager;
	import happyfish.scene.astar.Node;
	import happyfish.utils.display.McShower;
	import happymagic.display.control.ItemEnoughCheckCommand;
	import happymagic.display.view.levelUpgrade.LevelUpgradeView;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.MiningCommand;
	import happymagic.model.control.TakeResultVoControl;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.LevelInfoVo;
	import happymagic.model.vo.MineVo;
	import happymagic.model.vo.MiningScriptVo;
	import happymagic.model.vo.ResultVo;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.award.AwardItemManager;
	import happymagic.scene.world.grid.person.Mine;
	import happymagic.scene.world.grid.person.Player;
	import happymagic.scene.world.MagicWorld;
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class MinningCommand 
	{
		private var mine:Mine;
		private var mainPlayer:Player;
		
		public function MinningCommand(mine:Mine, mainPlayer:Player) 
		{
			this.mine = mine;
			this.mainPlayer = mainPlayer;
			
			if (mine.clickTimes >= mine.currentHp) return;
			mine.clickTimes ++;
			
			if (mine.currentHp <= 0 || (!checkCanMinning()))
			{
				end(false);
				return;
			}
			
			var startNode:Node = new Node(mainPlayer.x, mainPlayer.z);
			var rect:Rectangle = new Rectangle(mine.x, mine.z, mine.vo.sizeX, mine.vo.sizeZ);
			var targetNode:Node = DataManager.getInstance().worldState.world.findCanWalkNodeFromRect(startNode, rect);
			if (!targetNode) return;
			
			var target:Point3D = new Point3D(targetNode.x, 0, targetNode.y);
			var dir:Point3D = new Point3D(mine.gridPos.x, 0 , mine.gridPos.z);
			
			var script:MiningScriptVo = (mine.vo as MineVo).miningScript;
			if (!script) //如果没有相关脚本 使用默认脚本
			{
				script = new MiningScriptVo();
				script.label = "mine";
				script.time = 1500;
			}
			
			var command:AvatarCommand = new AvatarCommand(target, showProgressBar, dir, script.time, script.label, postMiningCommand);
			command.autoNext = false;
			mainPlayer.addCommand(command);
			
			mainPlayer.currentAction = Player.MOVING;
		}
		
		//检查是否可以打怪挖矿
		private function checkCanMinning():Boolean
		{
			var conditions:Vector.<ConditionVo> = mine.vo.conditions;
			if (!conditions) return true;
			
			for (var i:int = 0; i < conditions.length; i++)
			{
				var id:String = conditions[i].id;
				var num:int = conditions[i].num;
				var msg:String;
				
				switch(id)
				{
					case "coin":
						if (DataManager.getInstance().currentUser.coin < num)
						{
							msg = LocaleWords.getInstance().getWord("serverWord_207");
							DisplayManager.showPiaoStr(PiaoMsgType.TYPE_COIN, msg);
							MouseManager.getInstance().clearTmpIcon(1);
							return false;
						}
						break;
					case "gem":
						if (DataManager.getInstance().currentUser.gem < num)
						{
							msg = LocaleWords.getInstance().getWord("buyitemmsggem");
							DisplayManager.showSysMsg(msg);
							MouseManager.getInstance().clearTmpIcon(1);
							return false;
						}
						break;
					case "sp":
						if (!DataManager.getInstance().getEnoughSp(num))
						{
							msg = LocaleWords.getInstance().getWord("serverWord_208");
							DisplayManager.showPiaoStr(PiaoMsgType.TYPE_SP, msg);
							MouseManager.getInstance().clearTmpIcon(1);
							return false;
						}
						break;
					default:
						var itemEnoughCheckCommand:ItemEnoughCheckCommand = new ItemEnoughCheckCommand;
						if (!itemEnoughCheckCommand.check(parseInt(id), num, conditions[i].type))
						{
							MouseManager.getInstance().clearTmpIcon(1);
							return false;
						}
						break;
				}
			}
			return true;
		}
		
		private function showProgressBar():void
		{
			if (mine.currentHp <= 0 || (!checkCanMinning()))
			{
				mainPlayer.shiftCommand();
				end(false);
				return;
			}
			
			mainPlayer.currentAction = Player.MINING;
			var mc:McShower = new McShower(timeMovie, mine.view.container);
			var rect:Rectangle = mine.view.container.getRect(mine.view.container);
			mc.y = rect.top - 10;
		}
		
		//挖矿 发包
		private function postMiningCommand():void
		{
			var command:MiningCommand = new MiningCommand();
			command.addEventListener(Event.COMPLETE, onMiningComplete);
			command.start(mine["vo"]["id"]);
		}
		
		//打怪挖矿 收包
		private function onMiningComplete(event:Event):void
		{
			var command:MiningCommand = event.target as MiningCommand;
			command.removeEventListener(Event.COMPLETE, onMiningComplete);
			
			var result:Object = command.objdata;
			var world:MagicWorld = DataManager.getInstance().worldState.world as MagicWorld;
			
			//爆出奖品
			var pos:Point3D = new Point3D(mine.gridPos.x, 0, mine.gridPos.z);
			var items:Array = new Array;
			if (result.addItems)
			{
				for (var i:int = 0; i < result.addItems.length; i++)
					items.push( { id:result.addItems[i][0], num:result.addItems[i][1] } );
			}
			
			var resultVo:ResultVo = new ResultVo().setValue(result.result);
			AwardItemManager.getInstance().addAwardsByResultVo(resultVo, items, pos);
			
			//飘屏
			var point:Point = world.player.view.container.localToGlobal(new Point(0,-world.player.view.container.height));
			AwardItemManager.getInstance().piaoStrByResultVo(new ResultVo().setValue(result.removeResult), point);	
			
			//升级
			//if (result.result.levelUP)
			//{
				//var tmpuser:UserVo = DataManager.getInstance().currentUser;
				//tmpuser.level++;
				//
				//var nextlevel:LevelInfoVo = DataManager.getInstance().getLevelInfo(tmpuser.level + 1);
				//var tmplevel:LevelInfoVo = DataManager.getInstance().getLevelInfo(tmpuser.level);
				//
				//tmpuser.maxExp = nextlevel.maxExp;
				//tmpuser.maxSp += 10;
				//tmpuser.sp = tmpuser.maxSp;
				//
				//DataManager.getInstance().curSceneUser = tmpuser;
				//if (DataManager.getInstance().isDiying) 
				//{
					//DisplayManager.uiSprite.getModule(ModuleDict.MODULE_MAININFO)["diyingUserLevelUp"]();
				//}
				//
				//var levelInfoView:LevelUpgradeView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_LEVELINFO, ModuleDict.MODULE_LEVELINFO_CLASS,true,AlginType.CENTER,30,-50) as LevelUpgradeView;
				//levelInfoView.setData(DataManager.getInstance().getLevelInfo(tmpuser.level), 0);
				//DisplayManager.uiSprite.setBg(levelInfoView);
			//}
			
			mine.currentHp --;
			mine.clickTimes --;
			if (mine.currentHp <= 0)
			{
				TweenMax.to(mine.asset, 0.75, { autoAlpha:0, onComplete:end } );
			}
			else end(false);
		}
		
		private function end(remove:Boolean = true):void
		{
			if (remove){
				mine.remove();
				if (DisplayManager.dungeonTip) DisplayManager.dungeonTip.hide();
			}
			
			mainPlayer.currentAction = Player.WAITING; //脚本播放完毕
			mainPlayer.playCommand();
		}
		
	}

}