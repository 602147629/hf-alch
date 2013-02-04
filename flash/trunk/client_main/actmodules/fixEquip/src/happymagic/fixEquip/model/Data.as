package happymagic.fixEquip.model 
{
	/**
	 * ...
	 * @author lite3
	 */
	public class Data 
	{
		public var inited:Boolean = false;
		
		public var npcFace:String;
		public var chatList:Array;
		public var smithyBuildLevel:int;
		
		
		
		private static var _instance:Data;
		public static function get instance():Data
		{
			_instance ||= new Data();
			return _instance;
		}
		
	}

}