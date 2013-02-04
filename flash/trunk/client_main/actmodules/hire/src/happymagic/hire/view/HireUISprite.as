package happymagic.hire.view 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.geom.Rectangle;
	import flash.utils.Timer;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.utils.DateTools;
	import happyfish.utils.display.FiltersDomain;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.hire.commands.CompleteHireCommand;
	import happymagic.hire.commands.HireRoleCommand;
	import happymagic.hire.commands.RefreshHireCommand;
	import happymagic.hire.data.HireData;
	import happymagic.hire.events.HireActEvent;
	import happymagic.hire.view.ui.HireUI;
	import happymagic.hire.vo.HireVo;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.SkillAndItemVo;
	import happymagic.utils.AvatarUtil;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class HireUISprite extends UISprite 
	{
		private var iview:HireUI;
		private var tip:SkillTip;
		
		private var countdownTimer:Timer
		private var npcId:int;
		
		
		public function HireUISprite() 
		{
			iview = new HireUI();
			_view = iview;
			
			TextFieldUtil.autoSetTxtDefaultFormat(iview.infoMc0);
			TextFieldUtil.autoSetTxtDefaultFormat(iview.infoMc1);
			TextFieldUtil.autoSetTxtDefaultFormat(iview.countdownMc0);
			TextFieldUtil.autoSetTxtDefaultFormat(iview.countdownMc1);
			
			iview.infoMc0.starLevel.stop();
			iview.infoMc0.jobIcon.stop();
			iview.infoMc0.propIcon.stop();
			iview.infoMc1.starLevel.stop();
			iview.infoMc1.jobIcon.stop();
			iview.infoMc1.propIcon.stop();
			
			view.addEventListener(MouseEvent.CLICK, clickHandler);
		}
		
		public function show(npcId:int):void
		{
			this.npcId = npcId;
			var arr:Array = HireData.instance.getHireList(npcId);
			for (var i:int = arr.length - 1; i >= 0; i--)
			{
				setHireInfo(i, arr[i] as HireVo);
			}
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			if (e.target == view.closeBtn)
			{
				closeMe();
				return;
			}
			
			var s:String = e.target.parent.name;
			var idx:int = int(s.charAt(s.length - 1));
			
			var fun:Function;
			switch(e.target.name)
			{
				case "completeBtn" : fun = new CompleteHireCommand().completeHire; break;
				case "freeCompleteBtn" : fun = new CompleteHireCommand().freeCompleteHire; break;
				case "refreshBtn"  : fun = new RefreshHireCommand().refreshHire; break;
				case "hireBtn"  : fun = new HireRoleCommand().hireRole; break;
				default : return;
			}
			e.target.mouseEnabled = false;
			fun(npcId, idx + 1, function(success:Boolean, npcId:int, pos:int, hire:HireVo):void
			{
				e.target.mouseEnabled = true;
				if (success) refreshShow(npcId, pos, hire);
			});
		}
		
		private function refreshShow(npcId:int, pos:int, hire:HireVo):void 
		{
			if (npcId != this.npcId) return;
			
			setHireInfo(pos - 1, hire);
		}
		
		
		private function setHireInfo(idx:int, vo:HireVo):void
		{
			var infoMc:MovieClip = view["infoMc" + idx];
			var countdownMc:MovieClip = view["countdownMc" + idx];
			var time:int = vo.remainingTime;
			if (time > 0)
			{
				infoMc.visible = false;
				countdownMc.visible = true;
				if (!countdownTimer)
				{
					countdownTimer = new Timer(1000);
					countdownTimer.addEventListener(TimerEvent.TIMER, countDownHandler);
				}
				countdownTimer.start();
				countdownMc.timeTxt.text = DateTools.getRemainingTime(time, "%H:%I:%S");
				countdownMc.numTxt.text = DataManager.getInstance().gameSetting.needWineNum + "";
				var canFreeComplete:Boolean = 0 == DataManager.getInstance().currentUser.hireHelpUsed
					&& DataManager.getInstance().currentUser.hireHelp == DataManager.getInstance().gameSetting.maxHireHelp;
					
				countdownMc.numTxt.filters = canFreeComplete ? [FiltersDomain.grayFilter] : [];
				countdownMc.completeBtn.filters = canFreeComplete ? [FiltersDomain.grayFilter] : [];
				countdownMc.freeCompleteBtn.filters = canFreeComplete ? [] : [FiltersDomain.grayFilter];
				countdownMc.freeCompleteBtn.mouseEnabled = canFreeComplete;
			}else
			{
				infoMc.visible = true;
				countdownMc.visible = false;
				
				infoMc.nameTxt.text = vo.name;
				infoMc.jobTxt.text = LocaleWords.getInstance().getWord("roleProfession" + vo.profession);
				infoMc.jobIcon.gotoAndStop(vo.profession);
				infoMc.propTxt.text = LocaleWords.getInstance().getWord("roleProp" + vo.prop);
				infoMc.propIcon.gotoAndStop(vo.prop);
				infoMc.mpTxt.text = vo.mp + "";
				infoMc.hpTxt.text = vo.hp + "";
				infoMc.mpBar.scaleX = vo.mp / vo.maxMp;
				infoMc.hpBar.scaleX = vo.hp / vo.maxHp;
				infoMc.paTxt.text = vo.phyAtk + "";
				infoMc.pdTxt.text = vo.phyDef + "";
				infoMc.maTxt.text = vo.magAtk + "";
				infoMc.mdTxt.text = vo.magDef + "";
				infoMc.starLevel.gotoAndStop(vo.quality);
				infoMc.priceTxt.text = vo.price + "";
				infoMc.speedTxt.text = vo.speed + "";
				
				var face:IconView = infoMc.face as IconView;
				if (!face)
				{
					var rect:Rectangle = new Rectangle(infoMc.faceBorder.x, infoMc.faceBorder.y, infoMc.faceBorder.width, infoMc.faceBorder.height);
					face = new IconView(rect.width, rect.height, rect);
					infoMc.addChildAt(face, infoMc.getChildIndex(infoMc.faceBorder));
					infoMc.removeChild(infoMc.faceBorder);
					infoMc.face = face;
				}
				face.setData(vo.faceClass);
				
				infoMc.chatTxt.text = vo.dialog;
				
				var skillIcon:IconView = infoMc.skillIcon as IconView;
				if (!skillIcon)
				{
					rect = new Rectangle(infoMc.skillBorder.x, infoMc.skillBorder.y, infoMc.skillBorder.width, infoMc.skillBorder.height);
					skillIcon = new IconView(rect.width, rect.height, rect);
					infoMc.addChildAt(skillIcon, infoMc.getChildIndex(infoMc.skillBorder));
					infoMc.removeChild(infoMc.skillBorder);
					infoMc.skillIcon = skillIcon;
				}
				
				if (vo.skills && vo.skills.length > 0 && vo.skills[0] > 0)
				{
					var skill:SkillAndItemVo = DataManager.getInstance().getSkillAndItemVo(vo.skills[0]);
					infoMc.skill = skill;
					skillIcon.setData(skill.className);
					skillIcon.visible = true;
					skillIcon.addEventListener(MouseEvent.ROLL_OVER, overHander);
					skillIcon.addEventListener(MouseEvent.ROLL_OUT, outHander);
				}else
				{
					skillIcon.visible = false;
				}
			}
		}
		
		private function countDownHandler(e:TimerEvent):void 
		{
			var list:Array = HireData.instance.getHireList(npcId);
			for (var i:int = list.length - 1; i >= 0; i--)
			{
				var vo:HireVo = list[i] as HireVo;
				if (0 == vo.remainingTime > 0 || view["countdownMc" + i].visible)
				{
					setHireInfo(i, vo);
				}
			}
		}
		
		override public function closeMe(del:Boolean = false):void 
		{
			if (countdownTimer) countdownTimer.stop();
			super.closeMe(del);
			EventManager.dispatchEvent(new HireActEvent(HireActEvent.HIRE_CLOSE));
		}
		
		private function overHander(e:MouseEvent):void 
		{
			var skill:SkillAndItemVo = e.currentTarget.parent.skill as SkillAndItemVo;
			if (!skill) return;
			
			if (!tip) tip = new SkillTip();
			tip.x = e.currentTarget.x + e.target.width;
			tip.y = e.currentTarget.y + e.target.height;
			view.addChild(tip);
			tip.setData(skill.cid);
		}
		
		private function outHander(e:MouseEvent):void 
		{
			if (tip && tip.parent) tip.parent.removeChild(tip);
		}
		
		
		
	}

}