package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class RoleWorkPointClassVo extends BasicVo
	{
		public var id:int;
		public var iconClass:String;
		public var x:int;
		public var y:int;
		public var awards:Array;
		public var sp:int; 
		public var roleLevel:int;
		public var roleNum:int;
		public var needTime:int;
		public var name:String;
		
		public function RoleWorkPointClassVo() 
		{
			
		}
		
	}

}