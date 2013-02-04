package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * 
	 * @author XiaJunJie
	 */
	public class RoleActScriptVo extends BasicVo
	{
		public var cid:int;
		public var type:int; //动作类型
		public var label:String;
		public var time:int; //播放时间 单位:毫秒
		public var coverLabel:String = ""; //叠加动画
		public var coverDelay:int = 0; //叠加动画的延迟
		public var coverTimes:int = 1; //叠加动画的时间
	}

}