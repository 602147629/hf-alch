package happymagic.battle.view.ui 
{
	import com.greensock.TweenMax;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import happyfish.display.view.IconView;
	import happyfish.manager.EventManager;
	import happymagic.battle.controller.AnimationClip;
	import happymagic.battle.events.BattleEvent;
	import happymagic.battle.model.vo.FriendSkillVo;
	import happymagic.battle.view.ExFriendSkillTimesTip;
	import happymagic.battle.view.FriendSkillTimesTip;
	import happymagic.battle.view.FriendSkillTip;
	import happymagic.battle.view.FriendSkillUi;
	import happymagic.battle.view.TalkBgUi;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.LevelInfoVo;
	import happymagic.model.vo.SkillAndItemVo;
	/**
	 * ...
	 * @author 
	 */
	public class BattleFriendSkillView extends FriendSkillUi 
	{
		private var data:Vector.<FriendSkillVo>;
		private var times:int;
		private var exTimes:int;
		
		private var skillTip:FriendSkillTip;
		private var skillTimesTip:FriendSkillTimesTip;
		private var exSkillTimesTip:ExFriendSkillTimesTip;
		
		private var exSkillTimesTipContent:String;
		
		public var skillUsed:Boolean; //标记 此次战斗中是否已经使用过援助技能
		
		public static const NEED_LEVEL:int = 6;
		
		public function BattleFriendSkillView() 
		{
			x = 100;
			y = 132;
			alpha = 0;
			
			if (DataManager.getInstance().currentUser.level < NEED_LEVEL) //6级之前不显示援助技能
			{
				visible = false;
				return;
			}
			
			for (var i:int = 0; i < 8; i++)
			{
				this["skill" + i].addEventListener(MouseEvent.ROLL_OVER, onSkillOver);
				this["skill" + i].addEventListener(MouseEvent.ROLL_OUT, onSkillOut);
				this["skill" + i].addEventListener(MouseEvent.CLICK, onSkillClick);
			}
			
			timesBtn.addEventListener(MouseEvent.ROLL_OVER, onTimesBtnOver);
			timesBtn.addEventListener(MouseEvent.ROLL_OUT, onTimesBtnOut);
			
			exTimesBtn.addEventListener(MouseEvent.ROLL_OVER, onExTimesBtnOver);
			exTimesBtn.addEventListener(MouseEvent.ROLL_OUT, onExTimesBtnOut);
			
			skillTip = new FriendSkillTip;
			skillTip.visible = false;
			addChild(skillTip);
			
			skillTimesTip = new FriendSkillTimesTip;
			skillTimesTip.visible = false;
			skillTimesTip.x = timesBtn.x;
			skillTimesTip.y = timesBtn.y;
			addChild(skillTimesTip);
			
			exSkillTimesTip = new ExFriendSkillTimesTip;
			exSkillTimesTip.visible = false;
			exSkillTimesTip.x = exTimesBtn.x;
			exSkillTimesTip.y = exTimesBtn.y;
			addChild(exSkillTimesTip);
			exSkillTimesTipContent = exSkillTimesTip.txt.text;
			
			var level:int = DataManager.getInstance().currentUser.level;
			var curLevelInfo:LevelInfoVo = DataManager.getInstance().getLevelInfo(level);
			var nextLevelInfo:LevelInfoVo = DataManager.getInstance().getLevelInfo(level++);
			while (nextLevelInfo && nextLevelInfo.assistCnt == curLevelInfo.assistCnt)
				nextLevelInfo = DataManager.getInstance().getLevelInfo(level++);
			var a:String = String(nextLevelInfo ? nextLevelInfo.level : curLevelInfo.level);
			var b:String = String(nextLevelInfo ? nextLevelInfo.assistCnt : curLevelInfo.assistCnt);
			var content:String = skillTimesTip.txt.text;
			content = content.replace("a", a);
			content = content.replace("b", b);
			skillTimesTip.txt.text = content;
			
			timesTxt.mouseEnabled = exTimesTxt.mouseEnabled = false;
		}
		
		public function show():void {
			if(DataManager.getInstance().currentUser.level >= NEED_LEVEL) TweenMax.to(this, .4, { autoAlpha:1, x: 150 } );
		}
		
		public function hide():void {
			if(DataManager.getInstance().currentUser.level >= NEED_LEVEL) TweenMax.to(this, .4, { autoAlpha:0, x: 100 } );
		}
		
		public function setData(data:Vector.<FriendSkillVo>,times:int,exTimes:int):void
		{
			this.data = data;
			this.times = times;
			this.exTimes = exTimes;
			
			for (var i:int = 0; i < 8; i++)
			{
				var skillUi:MovieClip = this["skill" + i];
				
				if (data.length <= i)
				{
					skillUi.visible = false;
					continue;
				}
				
				var skillData:FriendSkillVo = data[i];
				
				var iconContainer:Sprite = skillUi["iconContainer"];
				while (iconContainer.numChildren > 0) iconContainer.removeChildAt(0);
				var iconView:IconView = new IconView(25, 25);
				iconView.setData(skillData.skill.className);
				iconContainer.addChild(iconView);
				
				skillUi["lock"].visible = skillData.level == 0;
				skillUi.gotoAndStop(1);
			}
			this.timesTxt.text = "" + times;
			this.exTimesTxt.text = "" + exTimes;
		}
		
		public function unfreezeSkill():void
		{
			var canUse:Boolean = times > 0 || exTimes > 0;
			
			for (var i:int = 0; i < 8; i++)
			{
				var skillUi:MovieClip = this["skill" + i];
				skillUi.filters = canUse ? [] : [AnimationClip.grayFilter];
			}
		}
		
		public function freezeSkill():void
		{
			for (var i:int = 0; i < 8; i++)
			{
				var skillUi:MovieClip = this["skill" + i];
				skillUi.filters = [AnimationClip.grayFilter];
			}
		}
		
		private function onSkillOver(event:MouseEvent):void
		{
			var skillUi:MovieClip = event.currentTarget as MovieClip;
			var index:int = int(skillUi.name.substr(5));
			var skillData:FriendSkillVo = data[index];
			
			skillTip.visible = true;
			skillTip.x = skillUi.x;
			skillTip.y = skillUi.y;
			
			skillTip.skillNameTxt.text = skillData.skill.name;
			skillTip.skillContentTxt.text = skillData.skill.content==null ? "没有技能说明" : skillData.skill.content;
			
			if (skillData.needFriendJob == 0) skillTip.nextTxt.text = "已经是最高等级";
			else
			{
				skillTip.nextTxt.text = "拥有" + skillData.needFriendNum + "个" + skillData.needFriendLevel + "级";
				
				if (skillData.needFriendProp == 1) skillTip.nextTxt.appendText("风属性");
				else if (skillData.needFriendProp == 2) skillTip.nextTxt.appendText("火属性");
				else if (skillData.needFriendProp == 3) skillTip.nextTxt.appendText("水属性");
				
				if (skillData.needFriendJob == 1) skillTip.nextTxt.appendText("战士");
				else if (skillData.needFriendJob == 2) skillTip.nextTxt.appendText("弓手");
				else if (skillData.needFriendJob == 3) skillTip.nextTxt.appendText("法师");
				
				skillTip.nextTxt.appendText("好友");
			}
			
			skillUi.gotoAndStop(2);
		}
		
		private function onSkillOut(event:MouseEvent):void
		{
			skillTip.visible = false;
			
			var skillUi:MovieClip = event.currentTarget as MovieClip;
			skillUi.gotoAndStop(1);
		}
		
		private function onSkillClick(event:MouseEvent):void
		{
			if (times == 0 && exTimes == 0) return;
			
			var skillUi:MovieClip = event.currentTarget as MovieClip;
			if (skillUi.filters.length > 0) return; //灰掉了
			if (skillUi["lock"].visible) return; //锁住
			
			var index:int = int(event.currentTarget.name.substr(5));
			var skillData:FriendSkillVo = data[index];
			
			var battleEvent:BattleEvent = new BattleEvent(BattleEvent.REQUEST_SKILL);
			battleEvent.skillAndItemVo = skillData.skill;
			EventManager.getInstance().dispatchEvent(battleEvent);
		}
		
		public function reduceTimes():void
		{
			if (times > 0)
			{
				times --;
				this.timesTxt.text = "" + times;
			}
			else
			{
				exTimes --;
				this.exTimesTxt.text = "" + exTimes;
			}
		}
		
		private function onTimesBtnOver(event:MouseEvent):void
		{
			skillTimesTip.visible = true;
		}
		
		private function onTimesBtnOut(event:MouseEvent):void
		{
			skillTimesTip.visible = false;
		}
		
		private function onExTimesBtnOver(event:MouseEvent):void
		{
			exSkillTimesTip.visible = true;
			var content:String = exSkillTimesTipContent;
			content = content.replace("a", String(exTimes));
			exSkillTimesTip.txt.text = content;
		}
		
		private function onExTimesBtnOut(event:MouseEvent):void
		{
			exSkillTimesTip.visible = false;
		}
		
		public function clear():void
		{
			for (var i:int = 0; i < 8; i++)
			{
				this["skill" + i].removeEventListener(MouseEvent.ROLL_OVER, onSkillOver);
				this["skill" + i].removeEventListener(MouseEvent.ROLL_OUT, onSkillOut);
				this["skill" + i].removeEventListener(MouseEvent.CLICK, onSkillClick);
			}
			
			timesBtn.removeEventListener(MouseEvent.ROLL_OVER, onTimesBtnOver);
			timesBtn.removeEventListener(MouseEvent.ROLL_OUT, onTimesBtnOut);
			
			exTimesBtn.removeEventListener(MouseEvent.ROLL_OVER, onExTimesBtnOver);
			exTimesBtn.removeEventListener(MouseEvent.ROLL_OUT, onExTimesBtnOut);
		}
		
	}
}