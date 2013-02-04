package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author slamjj
	 */
	public class StoryActionVo extends BasicVo 
	{
		public var npcId:uint; //角色ID
		
		//要让角色到达的位置
		public var x:int = -1;
		public var y:int = -1;
		
		//要让角色面向的方向
		public var faceX:uint;
		public var faceY:uint;
		
		//角色说话的参数
		public var content:String;
		public var chatTime:uint = 2500;
		
		public var camera:uint; //镜头跟随
		public var wait:uint = 1;
		public var immediately:uint;
		
		public var hide:uint; //隐藏角色
		public var shockScreenTime:int; //振屏时间
		
		public var actionLabel:String; //要让角色播放的动画标签
		public var labelTimes:int = 1; //角色标签动画的播放次数
		public var toStop:int = 0; //播完后是否停止
		
		public var className:String; //要叠加的动画的className
	}

}