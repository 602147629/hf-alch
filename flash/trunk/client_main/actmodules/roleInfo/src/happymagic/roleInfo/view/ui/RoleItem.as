package happymagic.roleInfo.view.ui 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.filters.GlowFilter;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	import happyfish.manager.local.LocaleWords;
	import happyfish.time.Time;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.roleInfo.view.ui.render.RoleItemRender;
	import happymagic.model.vo.RoleVo;
	import happymagic.utils.AvatarUtil;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class RoleItem extends GridItem 
	{
		private var role:RoleVo;
		private var iview:RoleItemRender;
		private var icon:IconView;
		
		public function RoleItem(ui:MovieClip) 
		{
			super(ui);
			iview = ui as RoleItemRender;
			iview.cancelBtn.visible = false;
			
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			
			var rect:Rectangle = new Rectangle(iview.border.x, iview.border.y, iview.border.width, iview.border.height);
			icon = new IconView(rect.width, rect.height, rect);
			iview.addChild(icon);
			iview.border.visible = false;
		}
		
		override public function setData(value:Object):void 
		{
			var getWord:Function = LocaleWords.getInstance().getWord;
			role = value as RoleVo;
			iview.nameTxt.text = role.name;
			iview.levelTxt.text = getWord("LVx", role.level);
			iview.jobIcon.gotoAndStop(role.profession);
			iview.propIcon.gotoAndStop(role.prop);
			iview.starLevel.gotoAndStop(role.quality);
			iview.tmpBtn.visible = RoleVo.TEMP == role.label;
			icon.setData(role.faceClass);
			selected = selected;
			
			iview.cdBtn.visible = Time.getRemainingTimeByEnd(role.occCdTime) > 0;
		}
		
		override public function set selected(value:Boolean):void 
		{
			if (role)
			{
				super.selected = value;
				var frame:String = value ? "Select" : "Unselect";
				frame += -1 == role.pos ? "_Normal" : "_Battle";
				iview.gotoAndStop(frame);
				iview.filters = value ? [new GlowFilter(0xFF6600, 1, 8, 8, 2.5, 1)] : [];
			}
		}
		
	}

}