package happymagic.model.vo.classVo 
{
	import com.adobe.serialization.json.JSON;
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class WorldMapClassVo extends BasicVo
	{
		public var sceneId:int;
		public var name:String;
		public var sp:int;
		public var iconClass:String;
		public var x:int;
		public var y:int;
		public var links:Array;
		public var cid:int;
		public var roleConditionLevel:int;
		
		public function WorldMapClassVo() 
		{
			
		}
		
		public function setVaule(obj:Object):WorldMapClassVo
		{
			for (var str:String in obj)
			{
				if (obj == "links")
				{
					links = JSON.decode(obj.links);
				}
		        else
				{
				   this[str] = obj[str];						
				}				
			}
			return this;
		}
		
	}

}