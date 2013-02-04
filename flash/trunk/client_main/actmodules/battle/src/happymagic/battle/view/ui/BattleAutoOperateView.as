package happymagic.battle.view.ui 
{
	import com.greensock.TweenMax;
	/**
	 * ...
	 * @author 
	 */
	public class BattleAutoOperateView extends autoActionUi 
	{
		
		public function BattleAutoOperateView() 
		{
			visible = false;
			alpha = 0;
			x = -294;
			y = 186;
			
			buttonMode = true;
		}
		
		public function update(times:int):void {
			timesTxt.text = "(" + times + ")";
		}
		
		public function show():void {
			
			TweenMax.to(this, .4, { autoAlpha:1, y: 223 } );
		}
		
		public function hide():void {
			TweenMax.to(this, .4, { autoAlpha:0, y: 186 } );
		}
		
	}

}