package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * 怪物VO 2011.11.10
	 * @author XiaJunJie
	 */
	public class MonsterVo extends ActItemClassVo
	{
		public var id:int;
		public var currentHp:int; //当前血量
		
		//位置
		public var x:int;
		public var y:int;
		public var z:int;
		
		//闲逛范围
		public var fiddleRangeX:int;
		public var fiddleRangeZ:int;
		
		public var level:int;
		
		//碰撞范围
		public var collisionRange:int;
		
		//打此怪时的战斗背景
		public var fightBg:String;
		
		override public function setData(obj:Object):BasicVo
		{
			for (var name:String in obj) 
			{
				if (name == "conditions") parseConditions(obj[name] as Array);
				else if ( this.hasOwnProperty(name)) 
				{
					this[name] = obj[name];
				}
			}
			
			return this;
		}
		
	}

}