package happymagic.battle.view.ui 
{
	import com.greensock.TweenLite;
	import com.greensock.TweenMax;
	import flash.geom.Rectangle;
	import happyfish.display.view.IconView;
	import happyfish.display.view.PerBarView;
	import happyfish.manager.EventManager;
	import happymagic.battle.events.BattleEvent;
	import happymagic.model.vo.RoleVo;
	import happymagic.utils.AvatarUtil;
	/**
	 * 当前行动角色信息栏
	 * @author 
	 */
	public class BattleUserInfoView extends battleUserInfoUi 
	{
		public var curRole:RoleVo;
		private var hpPer:PerBarView;
		private var mpPer:PerBarView;
		private var faceIcon:IconView;
		
		public function BattleUserInfoView() 
		{
			visible = false;
			mouseChildren = mouseEnabled = false;
			
			x = -281;
			y = 162;
			
			hpPer = new PerBarView(hpPerBar, hpPerBar.width);
			mpPer = new PerBarView(mpPerBar, mpPerBar.width);
			
			EventManager.getInstance().addEventListener(BattleEvent.NEXT_ROLE, showRole);
		}
		
		private function showRole(e:BattleEvent):void 
		{
			setRole(e.roleVo);
			show();
		}
		
		public function setRole(role:RoleVo):void {
			curRole = role;
			
			nameTxt.text = curRole.name;
			
			hpPer.maxValue = curRole.maxHp;
			mpPer.maxValue = curRole.maxMp;
			
			loadFace();
			
			refresh();
			
			show();
		}
		
		private function loadFace():void 
		{
			if (!faceIcon) 
			{
				faceIcon = new IconView(105,125,new Rectangle(-52,-155,105,125));
				//faceIcon.x = 6;
				//faceIcon.y = -48;
			}
			
			faceIcon.setData(curRole.faceClass);
			addChildAt(faceIcon,0);
		}
		
		public function show():void {
			TweenMax.to(this, .4, { autoAlpha:1, x: -261 } );
		}
		
		public function hide():void {
			TweenMax.to(this, .4, { autoAlpha:0, x: -281 } );
		}
		
		public function refresh():void {
			hpTxt.text = curRole.hp.toString();
			mpTxt.text = curRole.mp.toString();
			hpPer.setData(curRole.hp);
			mpPer.setData(curRole.mp);
		}
		
	}

}