package happymagic.battle.view.ui 
{
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.view.UISprite;
	import happymagic.model.vo.ConditionVo;
	/**
	 * ...
	 * @author 
	 */
	public class BattleWinView extends UISprite 
	{
		private var iview:battleWinUi;
		private var awardLayer:DefaultListView;
		
		public function BattleWinView() 
		{
			_view = new battleWinUi();
			
			iview = _view as battleWinUi;
			
			iview.roleInfo_0.visible=
			iview.roleInfo_1.visible=
			iview.roleInfo_2.visible =
			iview.roleInfo_3.visible = false;
			
			awardLayer = new DefaultListView(new battleWinAwardListUi(), iview, 6,true);
			awardLayer.init(435, 85, 60, 85, 41, -35);
			awardLayer.x = -232;
			awardLayer.y = 77;
			awardLayer.setGridItem(BattleWinAwardItemView, battleWinItemUi);
			
			iview.wordTxt.text = WORDS[Math.floor(Math.random() * WORDS.length)];
			
			_view.addEventListener(MouseEvent.CLICK, clickFun);
		}
		
		/**
		 * 
		 * @param	roleInfo	[[name:string,exp:int,level:int]]
		 * @param	award		[ConditonVo数组]
		 */
		public function setData(roleInfo:Array, award:Vector.<ConditionVo>):void {
			
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
			
			awardLayer.setData(award);
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
		
		public static var WORDS:Array = ["恭喜你胜利了","英雄大人！怪物神马的已经无法阻止你了。",
		"不愧是主角，太厉害啦！", "你胜利的光芒，太刺眼了。", "胜利，撒花，领赏咯！", "大家快来围观无敌英雄啊！",
		"原来你真是传说中的主角，厉害！", "亲，领好赏钱记得交税哦~", "哇~整个游戏都以你为豪！", "跟主角做对的，就认命做炮灰吧。",
		"打架会赢说明你人品够好。", "再赢下去，你都要成神啦！", "做人必须野蛮暴力，就像你这样。", "赢是赢了，不过可别乱得瑟。",
		"连你都赢了，可以出手买彩票了。", "看来你被胜利女神附身了。", "开个赌局，赌你还能胜几次。", "牛人，什么时候开庆功宴？",
		"敌人因你的主角光环致盲而死。","全世界的目光都集中在了你身上。"];
	}

}