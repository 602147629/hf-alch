package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	import happymagic.manager.DataManager;
	/**
	 * 交互动画中一回合的VO 2011.11.11
	 * @author XiaJunJie
	 */
	public class ActRoundVo extends BasicVo
	{
		public var roles:Array;
		public var hpChange:int; //守方改变的血量
		public var addItem:Array; //获得的道具
		public var removeItem:Array; //损失的道具
		public var addResult:ResultVo = new ResultVo;
		public var result:ResultVo = new ResultVo;
		public var removeResult:ResultVo = new ResultVo;
		
		override public function setData(obj:Object):BasicVo
		{
			var arr:Array;
			var vec:Array;
			var i:int;
			
			for (var name:String in obj)
			{
				if (this.hasOwnProperty(name))
				{
					if (name == "addItem" || name == "removeItem")
					{
						vec = new Array;
						arr = obj[name] as Array;
						for (i = 0; i < arr.length; i++)
						{
							var itemVo:ItemVo = new ItemVo();
							itemVo.cid = arr[i][0];
							itemVo.num = Number(arr[i][1]);
							vec.push(itemVo);
						}
						this[name] = vec;
					}
					else if (name == "addResult" || name=="result")
					{
						this[name].setValue(obj[name]);
					}
					else if (name == "removeResult")
					{
						removeResult.setValue(obj[name]);
						removeResult.coin = -removeResult.coin;
						removeResult.gem = -removeResult.gem;
						removeResult.sp = -removeResult.sp;
					}
					else this[name] = obj[name]; 
				}
			}
			if (!addItem) addItem = new Array;
			if (!removeItem) removeItem = new Array;
			
			return this;
		}
		
	}

}