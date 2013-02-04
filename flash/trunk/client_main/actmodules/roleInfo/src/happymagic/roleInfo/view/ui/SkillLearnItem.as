package happymagic.roleInfo.view.ui 
{
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	import happyfish.manager.local.LocaleWords;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.roleInfo.commands.ReplaceSkillCommand;
	import happymagic.roleInfo.view.SkillTip;
	import happymagic.roleInfo.view.ui.render.SkillLearnItemRender;
	import happymagic.roleInfo.vo.SkillPosVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class SkillLearnItem extends GridItem 
	{
		private var iview:SkillLearnItemRender;
		private var icon:IconView;
		private var tip:SkillTip;
		private var vo:SkillPosVo;
		
		public function SkillLearnItem(ui:MovieClip) 
		{
			super(ui);
			iview = ui as SkillLearnItemRender;
			iview.buttonMode = false;
			
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			
			var rect:Rectangle = new Rectangle(iview.border.x, iview.border.y, iview.border.width, iview.border.height);
			icon = new IconView(rect.width, rect.height, rect);
			iview.addChildAt(icon, iview.getChildIndex(iview.border));
			iview.removeChild(iview.border);
			iview.mouseChildren = true;
			icon.mouseChildren = false;
			icon.addEventListener(MouseEvent.ROLL_OVER, skillOverHandler); 
			icon.addEventListener(MouseEvent.ROLL_OUT, skillOutHandler); 
			
			iview.learnBtn.addEventListener(MouseEvent.CLICK, clickHandler);
		}
		
		private function skillOverHandler(e:MouseEvent):void 
		{
			if (!tip) tip = new SkillTip();
			tip.x = e.currentTarget.x + e.currentTarget.width;
			tip.y = e.currentTarget.y + e.currentTarget.height/2;
			iview.parent.addChild(tip);
			tip.setData(vo.scroll.cid);
		}
		
		private function skillOutHandler(e:MouseEvent):void 
		{
			if (tip && tip.parent) tip.parent.removeChild(tip);
		}
		
		override public function setData(value:Object):void 
		{
			vo = value as SkillPosVo;
			iview.nameTxt.text = vo.scroll.name;
			iview.contentTxt.text = vo.scroll.content;
			iview.mpTxt.text = LocaleWords.getInstance().getWord("(needMpX)", vo.skill.needMp);
			iview.mpTxt.x = iview.nameTxt.x + iview.nameTxt.textWidth + 8;
			iview.mpTxt.width = iview.mpTxt.textWidth + 4;
			icon.setData(vo.scroll.className);
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			new ReplaceSkillCommand().replaceSkill(vo.roleId, vo.pos, vo.scroll.cid);
			super.itemSelectFun(e);
		}
		
		// 屏蔽默认的选择
		override protected function itemSelectFun(e:MouseEvent):void { }
		
	}

}