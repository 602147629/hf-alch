package happymagic.recoverHpMp.view 
{
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import flash.utils.setTimeout;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.local.LocaleWords;
	import happyfish.utils.display.FiltersDomain;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.display.view.ui.AvatarSprite;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.ConditionVo;
	import happymagic.recoverHpMp.commands.RoleUpgradeStarCommand;
	import happymagic.recoverHpMp.model.ConditionId;
	import happymagic.recoverHpMp.model.RoleUpgaradeStarVo;
	import happymagic.recoverHpMp.view.ui.render.NeedItem;
	import happymagic.recoverHpMp.view.ui.render.NeedItemRender;
	import happymagic.recoverHpMp.view.ui.UpgradeStarUI;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class UpgradeStarUISprite extends UISprite 
	{
		private var npcIcon:AvatarSprite;
		private var iview:UpgradeStarUI;
		private var listView:DefaultListView;
		
		public function UpgradeStarUISprite() 
		{
			iview = new UpgradeStarUI();
			_view = iview;
			
			iview.effect1.stop();
			iview.effect1.visible = false;
			iview.effect2.stop();
			iview.effect2.visible = false;
			iview.effect3.stop();
			iview.effect3.visible = false;
			iview.effect4.stop();
			iview.effect4.visible = false;
			iview.rotationEffect.stop();
			iview.rotationEffect.visible = false;
			iview.npcBottomMovie.stop();
			
			iview.oldStarLevel.gotoAndStop("None");
			iview.newStarLevel.gotoAndStop("None");
			
			TextFieldUtil.autoSetDefaultFormat(iview.titleTxt);
			
			var rect:Rectangle = new Rectangle(iview.border.x, iview.border.y, iview.border.width, iview.border.height);
			npcIcon = new AvatarSprite(rect.x, rect.y, rect.width, rect.height);
			iview.addChildAt(npcIcon, iview.getChildIndex(iview.border));
			iview.removeChild(iview.border);
			
			listView = new DefaultListView(iview, iview, 6);
			listView.init(300, 190, 290, 45, -86, -55);
			listView.setGridItem(NeedItem, NeedItemRender);
			
			iview.addEventListener(MouseEvent.CLICK, clickHandler);
		}
		
		public function setData(vo:RoleUpgaradeStarVo):void
		{
			iview.oldStarLevel.gotoAndStop(vo.quality - 1);
			iview.newStarLevel.gotoAndStop(vo.quality);
			iview.titleTxt.text = LocaleWords.getInstance().getWord("xxxUpgradeQuality2xxxStar", DataManager.getInstance().currentUser.name, vo.quality);
			iview.nameTxt.text = DataManager.getInstance().roleData.getRole(0).name;
			var canUpgarde:Boolean = true;
			for (var i:int = vo.conditions.length - 1; i >= 0; i--)
			{
				var condition:ConditionVo = vo.conditions[i];
				if (ConditionId.NeedLevel == condition.id)
				{
					condition.curNum = DataManager.getInstance().currentUser.level;
				}else if (ConditionId.NeedRoleLevel == condition.id)
				{
					condition.curNum = DataManager.getInstance().roleData.getRole(0).level;
				}else
				{
					condition.updateCurNum();
				}
				canUpgarde &&= vo.conditions[i].isFinish();
			}
			iview.upgradeBtn.mouseEnabled = canUpgarde;
			iview.upgradeBtn.filters = canUpgarde ? [] : [FiltersDomain.grayFilter];
			
			listView.setData(vo.conditions);
			
			npcIcon.load(DataManager.getInstance().roleData.getRole(0).className);
			npcIcon.playLoop("wait");
			iview.npcBottomMovie.play();
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			switch(e.target)
			{
				case iview.upgradeBtn :
					upgradeQuality();
					break;
					
				case iview.closeBtn :
					closeMe(true);
					break;
			}
		}
		
		private function upgradeQuality():void 
		{
			iview.mouseChildren = false;
			new RoleUpgradeStarCommand().upgrade(upgradeComplete);
		}
		
		private function upgradeComplete(success:Boolean):void 
		{
			if (success)
			{
				iview.effect1.gotoAndPlay(1);
				iview.effect1.visible = true;
				iview.effect2.gotoAndPlay(1);
				iview.effect2.visible = true;
				iview.effect3.gotoAndPlay(1);
				iview.effect3.visible = true;
				iview.effect4.gotoAndPlay(1);
				iview.effect4.visible = true;
				iview.rotationEffect.gotoAndPlay(1);
				iview.rotationEffect.visible = true;
				setTimeout(closeMe, 3000);
			}else
			{
				closeMe();
			}
		}
		
		override public function closeMe(del:Boolean = false):void 
		{
			iview.npcBottomMovie.stop();
			iview.rotationEffect.stop();
			
			iview.oldStarLevel.gotoAndStop("None");
			iview.newStarLevel.gotoAndStop("None");
			
			super.closeMe(true);
		}
		
	}

}