package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * 状态VO
	 * @author XiaJunJie
	 */
	public class StatusVo extends BasicVo
	{
		public var type:int; //类型 参考EffectDictionary.as
		public var remain:int; //持续回合
		public var value:int; //作用值
		
		public var prop:int; //1:正面状态 2:负面状态 0:无属性状态
		
		public var script:Array; //消失时的显示脚本
		
		public function toArray():Array
		{
			return [type, remain, value, prop];
		}
		
		public function setArray(arr:Array):void
		{
			type = arr[0];
			remain = arr[1];
			value = arr[2];
			prop = arr[3];
		}
	}

}