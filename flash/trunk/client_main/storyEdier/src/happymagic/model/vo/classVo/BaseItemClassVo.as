package happymagic.model.vo.classVo 
{
	import happyfish.model.vo.BasicVo;
	import happymagic.model.vo.ItemType;
	
	/**
	 * 道具,物品,等静态数据的基类
	 * @author lite3
	 */
	public class BaseItemClassVo extends BasicVo 
	{
		public var cid:int;
		// 大类型
		public var type:int;
		// 小类型
		public var type2:int;
		// 购买所需的金币
		public var coin:int;
		// 购买所需的钻石
		public var gem:int;
		// 出售的金币
		public var sale:int;
		// 卖个顾客的价格
		public var worth:int;
		
		public var isNew:Boolean;
		public var canBuy:Boolean;
		
		public var name:String;
		public var className:String;
		public var content:String;
		
		override public function setData(obj:Object):BasicVo 
		{
			super.setData(obj);
			type = ItemType.getType(cid);
			type2 = ItemType.getType2(cid);
			return this;
		}
		
	}

}