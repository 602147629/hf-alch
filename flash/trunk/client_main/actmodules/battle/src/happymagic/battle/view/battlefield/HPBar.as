package happymagic.battle.view.battlefield 
{
	import com.greensock.TweenLite;
	import happymagic.battle.view.HPBarUI;
	/**
	 * 血条
	 * @author XiaJunJie
	 */
	public class HPBar extends HPBarUI
	{
		private var _hp:int;
		private var _maxHp:int;
		
		public function setHpAndMaxHp(hp:int, maxHp:int):void
		{
			_hp = hp;
			_maxHp = maxHp;
			updateView();
		}
		
		public function setHp(v:int):void 
		{
			_hp = v;
			updateView();
		}
		
		public function setMaxHp(v:int):void
		{
			_maxHp = v;
			updateView();
		}
		
		private function updateView():void
		{
			TweenLite.to(bar, 0.5, { x:_hp / _maxHp * 28 - 42 } );
		}
		
	}

}