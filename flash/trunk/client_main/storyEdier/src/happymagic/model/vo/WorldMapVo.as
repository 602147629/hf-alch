package happymagic.model.vo 
{
	import com.brokenfunction.json.decodeJson;
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class WorldMapVo extends BasicVo
	{
		public var bg:String;
		public var curOpenScene:Array;
		
		public function WorldMapVo() 
		{
			
		}

		public function setVaule(obj:Object):WorldMapVo
		{
			for (var str:String in obj)
			{
				if (obj == "curOpenScene")
				{
                   curOpenScene = decodeJson(obj.curOpenScene);			
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