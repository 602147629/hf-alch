package happymagic.display.view.maininfo 
{
	import flash.geom.Rectangle;
	import happyfish.display.view.IconView;
	import happyfish.display.view.PerBarView;
	import happymagic.model.vo.RoleVo;
	/**
	 * ...
	 * @author 
	 */
	public class MainInfoPlayerRoleView extends maininfo_roleInfoUi
	{
		private var data:RoleVo;
		private var icon:IconView;
		private var hpBar:PerBarView;
		private var mpBar:PerBarView;
		
		public function MainInfoPlayerRoleView() 
		{
			icon = new IconView(50, 50, new Rectangle(3, 2, 50, 50));
			icon.mask = faceMask;
			addChildAt(icon,getChildIndex(levelTxt));
			
			hpBar = new PerBarView(hpBarUi, hpBarUi.width);
			mpBar = new PerBarView(mpBarUi, mpBarUi.width);
		}
		
		public function setData(role:RoleVo):void {
			data = role;
			
			hpBar.setData(data.hp);
			hpBar.maxValue = data.maxHp;
			hpTxt.text = data.hp.toString();
			
			mpBar.setData(data.mp);
			mpBar.maxValue = data.maxMp;
			mpTxt.text = data.mp.toString();
			
			levelTxt.text = data.level.toString();
			
			icon.setData(data.faceClass);
		}
		
	}

}