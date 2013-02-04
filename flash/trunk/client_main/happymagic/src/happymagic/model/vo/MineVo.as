package happymagic.model.vo 
{
	import com.brokenfunction.json.decodeJson;
	import happyfish.model.vo.BasicVo;
	/**
	 * 矿VO
	 * @author XiaJunJie
	 */
	public class MineVo extends ActItemClassVo
	{
		public var id:int;
		
		public var currentHp:int; //血量
		
		//位置
		public var x:int;
		public var y:int;
		public var z:int;
		
		public var miningScript:MiningScriptVo;
		
		override public function setData(obj:Object):BasicVo
		{
			for (var name:String in obj) 
			{
				if (name == "conditions") parseConditions(decodeJson(obj[name]));
				else if (name == "miningScript")
				{
					miningScript = new MiningScriptVo;
					miningScript.setData(obj[name]);
				}
				else if ( this.hasOwnProperty(name)) 
				{
					this[name] = obj[name];
				}
			}
			
			return this;
		}
	}

}