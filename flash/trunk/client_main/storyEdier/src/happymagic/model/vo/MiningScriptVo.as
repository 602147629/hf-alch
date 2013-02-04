package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * 挖矿脚本
	 * @author XiaJunJie
	 */
	public class MiningScriptVo extends BasicVo
	{
		public var cid:int; //矿的CID
		public var label:String; //主角要播放的动画
		public var time:int; //播放时间 单位:毫秒
		public var coverLabel:String = ""; //叠加动画
		public var coverDelay:int = 0; //叠加动画的延迟
		public var coverTimes:int = 1; //叠加动画的时间
	}

}