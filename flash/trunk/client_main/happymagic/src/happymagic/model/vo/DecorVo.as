package happymagic.model.vo 
{
	import com.adobe.serialization.json.JSON;
	import com.adobe.utils.ArrayUtil;
	import com.brokenfunction.json.decodeJson;
	import com.brokenfunction.json.encodeJson;
	import happyfish.model.vo.BasicVo;
	import happyfish.scene.iso.IsoUtil;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	import happymagic.model.vo.classVo.DecorClassVo;
	/**
	 * ...
	 * @author slam
	 */
	public class DecorVo extends DecorClassVo
	{
		public var id:String;
		public var x:int;
		public var y:int;
		public var z:int;
		public var mirror:int;
		
		public function DecorVo() 
		{
			super();
		}
		
		public function setValue(obj:Object):DecorVo {
			for (var name:String in obj) 
			{
				this[name] = obj[name];
			}
			
			x += IsoUtil.isoStartX;
			z += IsoUtil.isoStartZ;
			
			//获得静态数据
			var decor_class_vo:BaseItemClassVo = DataManager.getInstance().itemData.getItemClass(cid) as BaseItemClassVo;
			setClass(decor_class_vo);
			
			return this;
		}
		
		public function createDefaultObj(_cid:int, $x:int, $z:int):void
		{
			var obj:Object = { };
			obj.id = 0;
			obj.x = $x;
			obj.y = 0;
			obj.z = $z;
			obj.cid = _cid;

			this.setValue(obj);
		}
		
		public function setClass($decor_class_vo:BaseItemClassVo):void
		{
			if ($decor_class_vo) 
			{
				var tmpobj:Object = decodeJson(JSON.encode($decor_class_vo));
				for (var name2:String in tmpobj) 
				{
					if ( this.hasOwnProperty(name2)) 
					{
						this[name2] = tmpobj[name2];
					}
				}
			}
		}
		
		public function clone():DecorVo {
			var tmp:DecorVo = new DecorVo();
			
			var tmpobj:Object = decodeJson(JSON.encode(this));
			for (var name2:String in tmpobj) 
			{
				tmp[name2] = tmpobj[name2];
			}
			return tmp;
		}
		
	}

}