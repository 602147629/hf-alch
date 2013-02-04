package happymagic.battle.view.ui 
{
	import com.greensock.TweenMax;
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import happyfish.display.view.IconView;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.RoleVo;
	import happymagic.model.vo.SkillAndItemVo;
	/**
	 * ...
	 * @author 
	 */
	public class BattleSkillListView extends skillListUi 
	{
		private var hideId:Number;
		public var skills:Array;
		private var rolePos:int;
		
		public function BattleSkillListView() 
		{
			visible = false;
			alpha = 0;
			mouseChildren = mouseEnabled = false;
			
			skill_0.visible=
			skill_1.visible=
			skill_2.visible = false;
			
			x = -218;
			y = 159;
		}
		
		private function init():void 
		{
			var role:RoleVo = DataManager.getInstance().getVar("curRoleVoInBattle") as RoleVo;
			
			if (rolePos==role.pos) 
			{
				return;
			}
			
			rolePos = role.pos;
			var arr:Array =  role.skills;
			
			skills = new Array();
			
			var tmpskill:SkillAndItemVo;
			for (var i:int = 0; i < 3; i++) 
			{
				tmpskill = DataManager.getInstance().getSkillAndItemVo(arr[i]);
				if (tmpskill) 
				{
					skills.push(tmpskill);
				}
			}
			
			initBtns();
			
		}
		
		private function clearBtnIcon():void {
			var item:skillListItemUi;
			var tmpicon:IconView;
			for (var i:int = 0; i < 3; i++) 
			{
				item = this["skill_" + i];
				item.skillNameTxt.text = "";
				tmpicon = item.getChildByName("icon") as IconView;
				if (tmpicon) 
				{
					tmpicon.parent.removeChild(tmpicon);
				}
				item.mouseChildren = 
				item.mouseEnabled = 
				item.buttonMode = false;
				
			}
		}
		
		private function initBtns():void 
		{
			clearBtnIcon();
			
			var item:skillListItemUi;
			var icon:IconView;
			for (var i:int = 0; i < 3; i++) 
			{
				item = this["skill_" + i];
				if (i<skills.length) 
				{
					if (item) 
					{
						icon = new IconView(30, 30, new Rectangle(11, 6, 30, 30));
						icon.setData(skills[i].className);
						icon.name = "icon";
						item.addChild(icon);
						
						item.skillNameTxt.text = skills[i].name;
						
						item.mouseEnabled = 
						item.buttonMode = true;
						
						item.visible = true;
					}
				}else {
					if (item) 
					{
						item.visible = false;
					}
				}
				
			}
		}
		
		public function show():void {
			TweenMax.killTweensOf(this);
			if (hideId) 
			{
				clearTimeout(hideId);
			}
			mouseChildren = mouseEnabled = true;
			TweenMax.to(this, .3, { y:159, autoAlpha:1, onComplete:show_complete } );
		}
		
		private function show_complete():void 
		{
			init();
		}
		
		public function hide(now:Boolean = true):void {
			if (hideId) 
			{
				clearTimeout(hideId);
			}
			if (now) 
			{
				hideMv();
			}else {
				hideId = setTimeout(hideMv, 300);
			}
			
		}
		
		private function hideMv():void {
			if (hideId) clearTimeout(hideId);
			hideId = 0;
			TweenMax.killTweensOf(this);
			TweenMax.to(this, .3, { y:129,autoAlpha:0,onComplete:hide_complete } );
		}
		
		private function hide_complete():void 
		{
			mouseChildren = mouseEnabled = false;
		}
		
	}

}