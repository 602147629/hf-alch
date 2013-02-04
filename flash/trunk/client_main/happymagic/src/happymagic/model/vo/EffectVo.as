package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * 技能或物品的效果
	 * @author XiaJunJie
	 */
	public class EffectVo extends BasicVo 
	{
		public var cid:int; //CID
		
		public var type:int; //类型
		
		public var statusProp:int; //1:正面状态 2:负面状态 0:无属性状态
		
		public var value:Number; //作用值
		public var isAbs:Boolean; //作用值是否绝对值
		public var duration:int; //持续回合
		
		public var isPhysic:Boolean; //是否是物理攻击
		
		public var statusScript:Array; //状态消失时的显示脚本
		
		override public function setData(obj:Object):BasicVo 
		{
			for (var key:String in obj)
			{
				if (key == "statusScript")
				{
					statusScript = new Array;
					var arr:Array = obj[key] as Array;
					var animationVo:AnimationVo;
					
					if (arr)
					{
						for (var i:int = 0; i < arr.length; i++)
						{
							var arr2:Array = arr[i][0] as Array;
							if (arr2 == null)
							{
								animationVo = new AnimationVo();
								animationVo.setData(arr[i]);
								statusScript.push(animationVo);
							}
							else
							{
								arr2 = arr[i];
								var arr3:Array = new Array;
								for (var j:int = 0; j < arr2.length; j++)
								{
									animationVo = new AnimationVo();
									animationVo.setData(arr[i][j]);
									arr3.push(animationVo);
								}
								statusScript.push(arr3);
							}
						}
					}
					
				}
				else if (hasOwnProperty(key)) this[key] = obj[key];
			}
			return this;
		}
		
	}

}