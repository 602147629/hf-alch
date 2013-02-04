package happymagic.roleInfo.view.ui 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	import happyfish.manager.local.LocaleWords;
	import happyfish.time.Time;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.model.vo.RoleVo;
	import happymagic.roleInfo.events.RoleFormationEvent;
	import happymagic.roleInfo.view.ui.render.RoleItemRender;
	import happymagic.roleInfo.vo.RoleFormationVo;
	import happymagic.utils.AvatarUtil;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class RoleFormationItem extends GridItem 
	{
		private var role:RoleFormationVo;
		private var iview:RoleItemRender;
		private var icon:IconView;
		
		public function RoleFormationItem(ui:MovieClip) 
		{
			super(ui);
			iview = ui as RoleItemRender;
			iview.mouseChildren = true;
			iview.addEventListener(MouseEvent.CLICK, clickHandler);
			
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			
			var rect:Rectangle = new Rectangle(iview.border.x, iview.border.y, iview.border.width, iview.border.height);
			icon = new IconView(rect.width, rect.height, rect);
			iview.addChild(icon);
			iview.border.visible = false;
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			if (role.isDungeon) return;
			
			if (e.target == iview.cancelBtn)
			{
				view.dispatchEvent(new RoleFormationEvent(RoleFormationEvent.ROLE_FORMATION_CANCEL, true, false, role));
			}else
			{
				view.dispatchEvent(new RoleFormationEvent(RoleFormationEvent.ROLE_FORMATION_SELECT, true, false, role));
			}
		}
		
		override public function setData(value:Object):void 
		{
			var getWord:Function = LocaleWords.getInstance().getWord;
			role = value as RoleFormationVo;
			iview.nameTxt.text = role.role.name;
			iview.levelTxt.text = getWord("LVx", role.role.level);
			iview.jobIcon.gotoAndStop(role.role.profession);
			iview.propIcon.gotoAndStop(role.role.prop);
			iview.starLevel.gotoAndStop(role.role.quality);
			iview.cancelBtn.visible = !role.isDungeon && role.onBattle && role.role.label != RoleVo.TEMP && role.role.label != RoleVo.MAIN_ROLE;
			iview.tmpBtn.visible = RoleVo.TEMP == role.role.label;
			icon.setData(role.role.faceClass);
			iview.gotoAndStop(role.onBattle ? "Unselect_Battle" : "Unselect_Normal");
			
			iview.cdBtn.visible = Time.getRemainingTimeByEnd(role.role.occCdTime) > 0;
		}
		
		override protected function itemSelectFun(e:MouseEvent):void { }
		
		override public function set selected(value:Boolean):void 
		{
			var currentFrame:int = iview ? iview.currentFrame : 0;
			super.selected = value;
			if(iview) iview.gotoAndStop(currentFrame);
		}
	}

}