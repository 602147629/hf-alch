package happymagic.battle.view.ui 
{
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	
	/**
	 * ...
	 * @author 
	 */
	public class BattleFaultView extends UISprite 
	{
		private var iview:battleFaultUi;
		
		public function BattleFaultView() 
		{
			_view = new battleFaultUi();
			
			iview = view as battleFaultUi;
			
			iview.roleInfo_0.visible=
			iview.roleInfo_1.visible=
			iview.roleInfo_2.visible = 
			iview.roleInfo_3.visible = false;
			
			iview.addEventListener(MouseEvent.CLICK, clickFun);
			
			iview.wordTxt.text = WORDS[Math.floor(Math.random() * WORDS.length)];
		}
		
		/**
		 * 
		 * @param	roleInfo	[[name:string,exp:int,level:int]]
		 */
		public function setData(roleInfo:Array):void {
			var tmproleinfo:battleWinRoleUi;
			var tmprole:Object;
			for (var i:int = 0; i < roleInfo.length; i++) 
			{
				tmprole = roleInfo[i];
				tmproleinfo = iview["roleInfo_" + i] as battleWinRoleUi;
				tmproleinfo.visible = true;
				
				tmproleinfo.nameTxt.text = tmprole.name;
				tmproleinfo.expTxt.text = "+" + tmprole.exp;
				if (tmprole.level) 
				{
					tmproleinfo.levelUpTxt.text = "LV"+tmprole.level;
					tmproleinfo.levelUpIcon.visible = true;
				}else {
					tmproleinfo.levelUpIcon.visible = false;
				}
			}
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case iview.yesBtn:
					closeMe(true);
				break;
			}
		}
		
		public static var WORDS:Array = ["20年后又是一条好汉。", "失败是成功它妈妈。", "回去再练几年功吧。",
		"大虾，请重新来过。", "这么就输了？现在墓地很贵哒。", "就算是游戏，也请珍惜生命。", "尼玛太失败了有木有。",
		"再输下去，主角的威信要不保了。", "你不会输不够了吧？", "胜败乃常事，生不带来死不带去。", "为你大无畏的送死精神，默哀~",
		"长见识啦！主角也会被打趴下哦~", "世界如此美妙，所以你会挂掉。", "人在江湖飘，挨刀很正常。", "你尽力了，是对手太黄太暴力。",
		"输得真可怜~表伤心哦，亲~", "战斗失败啦！输输更健康哦~", "为了爱与正义，下次不能输！", "你没医保，战斗失败怎么疗伤啊？",
		"你把幸运用完了，所以就失败了。"];
		
	}

}