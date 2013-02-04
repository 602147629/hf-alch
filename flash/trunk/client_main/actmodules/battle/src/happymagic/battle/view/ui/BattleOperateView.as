package happymagic.battle.view.ui 
{
	import com.greensock.TweenLite;
	import com.greensock.TweenMax;
	import flash.display.DisplayObject;
	import flash.events.MouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.utils.display.FiltersDomain;
	import happymagic.battle.events.BattleEvent;
	import happymagic.battle.view.ui.BattleUI;
	/**
	 * 点半中操作面板
	 * @author 
	 */
	public class BattleOperateView extends battleActionUi 
	{
		private var allBtn:Array;
		private var battleUi:BattleUI;
		
		public function BattleOperateView(_battleUi:BattleUI) 
		{
			battleUi = _battleUi;
			allBtn = [atkBtn, skillBtn, itemBtn, defenseBtn, escBtn, autoBtn];
			
			visible = false;
			alpha = 0;
			x = -300;
			y = 186;
		}
		
		/**
		 * 冻结指定按钮
		 * @param	...btns
		 */
		public function frozeBtn(btns:Array = null):void {
			for (var j:int = 0; j < allBtn.length; j++) 
			{
				allBtn[j].mouseEnabled = true;
				allBtn[j].filters = [];
			}
			if (!btns) return;
			for (var i:int = 0; i < btns.length; i++) 
			{
				allBtn[btns[i]].mouseEnabled = false;
				allBtn[btns[i]].filters = [FiltersDomain.grayFilter];
			}
		}
		
		public function show():void {
			
			TweenMax.to(this, .4, { autoAlpha:1, y: 223 } );
		}
		
		public function hide():void {
			TweenMax.to(this, .4, { autoAlpha:0, y: 186 } );
		}
		
		
		
	}

}