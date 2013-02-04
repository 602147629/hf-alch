package happyfish.guide.vo 
{
	/**
	 * ...
	 * @author lite3
	 */
	public class DialogVo
	{
		public var showedCount:int = 0;
		/** 说话的内容 */
		public var chat:String;
		/** 立刻执行的脚本 */
		public var promptlyHandler:Function;
		/** 点击后执行的代码 */
		public var clickHandler:Function;
		
		/** 0:左边  1:右边 */
		public var pos:int;
		
		/** 头像的类名 */
		public var avatarRef:String;
		/** 说话时的状态标签 */
		public var label:String;
		
		public function DialogVo(chat:String, pos:int, avatarRef:String, label:String, clickHandler:Function, promptlyHandler:Function) 
		{
			this.chat = chat;
			this.pos = pos;
			this.avatarRef = avatarRef;
			this.label = label;
			this.chat = chat;
			this.clickHandler = clickHandler;
			this.promptlyHandler = promptlyHandler;
		}
		
	}

}