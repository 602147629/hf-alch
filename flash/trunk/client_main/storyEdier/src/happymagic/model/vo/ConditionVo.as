package happymagic.model.vo 
{
	import happyfish.manager.local.LocaleWords;
	import happyfish.model.vo.BasicVo;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author jj
	 */
	public class ConditionVo extends BasicVo
	{
		public var id:String;
		public var type:int;
		public var num:int;
		public var curNum:int;
		public var content:String; // 任务进度描述
		
		public function isFinish():Boolean { return num <= curNum; }
		
		public function get isCoin():Boolean {
			if (type==ConditionType.USER && (id==ConditionType.USER_COIN ) ) 
			{
				return true;
			}else {
				return false;
			}
		}
		
		public function set isCoin(value:Boolean):void {
			return;
		}
		
		public function get isGem():Boolean {
			if (type==ConditionType.USER && id==ConditionType.USER_GEM ) 
			{
				return true;
			}else {
				return false;
			}
		}
		public function set isGem(value:Boolean):void {
			return;
		}
		
		public function get currentNum():uint 
		{
			return curNum;
		}
		
		public function set currentNum(value:uint):void 
		{
			curNum = value;
		}
		
		/**
		 * 获取道具的名字
		 * @return
		 */
		public function getName():String
		{
			switch(type)
			{
				case ConditionType.ITEM : return DataManager.getInstance().itemData.getItemClass(int(id)).name;
				case ConditionType.USER : return LocaleWords.getInstance().getWord(id);
				case ConditionType.Mob  : return DataManager.getInstance().illustratedData.getIllustrationsClassVo(int(id)).name;
			}
			return "";
		}
		
		public function updateCurNum():void
		{
			switch(type)
			{
				case ConditionType.ITEM :
					curNum = DataManager.getInstance().itemData.getItemCount(int(id), true);
					break;
				case ConditionType.USER :
					var vo:UserVo = DataManager.getInstance().currentUser;
					if (ConditionType.USER_COIN == id) curNum = vo.coin;
					else if (ConditionType.USER_GEM == id) curNum = vo.gem;
					else if (ConditionType.USER_EXP == id) curNum = vo.exp;
					else if (ConditionType.USER_SP == id) curNum = vo.sp;
					break;
			}
		}
		
		public static function turnResultVoToConditions(result:Object,addItems:Array):Vector.<ConditionVo>
		{
			var awards:Vector.<ConditionVo> = new Vector.<ConditionVo>;
			
			if (result.coin)
			{
				awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_COIN, num:result.coin } ));
			}
			if (result.gem)
			{
				awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_GEM, num:result.gem } ));
			}
			if (addItems)
			{
				var list:Object = new Object;
				for (var i:int = 0; i < addItems.length; i++) 
				{
					var conditionVo:ConditionVo;
					var id:String = String(addItems[i][0]);
					var num:int = int(addItems[i][1]);
					if (list[id] != null)
					{
						conditionVo = list[id];
						conditionVo.num += num;
					}
					else
					{
						conditionVo = new ConditionVo().setData( { type:ConditionType.ITEM, id:id, num:num } ) as ConditionVo;
						list[conditionVo.id] = conditionVo;
						awards.push(conditionVo);
					}
				}
			}
			
			return awards;
		}
		
	}

}