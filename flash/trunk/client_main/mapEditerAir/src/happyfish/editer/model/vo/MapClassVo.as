package happyfish.editer.model.vo 
{
	import com.adobe.serialization.json.JSON;
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author ...
	 */
	public class MapClassVo extends BasicVo
	{
		public var sceneId:uint;
		public var name:String;
		public var content:String;
		public var type:String;
		public var bg:String="";
		public var bgSound:String="";
		public var bgType:uint;
		public var numCols:uint;
		public var numRows:uint;
		public var isoStartX:uint;
		public var isoStartZ:uint;
		public var nodeStr:String;
		public var entrances:String="";
		public var parentSceneId:uint;
		public var needLevel:uint;
		public var withFog:int = 1;
		public var jump:uint;//0 不可跳  1 可跳
		//背景素材要跳转到的帧,岛主用的
		public var frame:uint;
		public var fightBg:String="";
		public function MapClassVo() 
		{
			
		}
		
		override public function setData(obj:Object):BasicVo 
		{
			for (var name:String in obj) 
			{
				if ( this.hasOwnProperty(name)) 
				{
					if (this[name] is Array) 
					{
						//this[name] = Array(obj[name]);
					}else {
						this[name] = obj[name];
					}
					
				}
			}
			return this;
		}
		
		public function outObject():Object 
		{
			var souceObj:Object = JSON.decode(JSON.encode(this));
			var obj:Object = new Object();
			for (var name:String in souceObj) 
			{
				switch (name) 
				{
					case "frame":
					
					break;
					
					default:
						obj[name] = souceObj[name];
					break;
				}
			}
			
			return obj;
			
		}
		
	}

}