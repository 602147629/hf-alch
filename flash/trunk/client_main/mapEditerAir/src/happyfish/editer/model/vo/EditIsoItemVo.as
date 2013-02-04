package happyfish.editer.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author ...
	 */
	dynamic public class EditIsoItemVo extends BasicVo
	{
		public var className:String;
		public var x:uint;
		public var y:uint;
		public var z:uint;
		public function EditIsoItemVo() 
		{
			
		}
		
		override public function setData(obj:Object):BasicVo 
		{
			for (var name:String in obj) 
			{
				this[name] = obj[name];
			}
			return this;
		}
		
	}

}