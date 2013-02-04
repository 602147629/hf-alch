package happymagic.model.data 
{
	import happymagic.model.vo.classVo.IllustrationsClassVo;
	import happymagic.model.vo.IllustrationsVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class IllustratedData 
	{
		//图鉴的静态数据
		private var _illustratedHandbookStatic:Array = new Array();
		
		//图鉴的动态数据	
		private var _illustratedHandbookInit:Array = new Array();
		
		public function IllustratedData() 
		{
			
		}

		public function getIllustrationsClassByItemCid(cid:int):IllustrationsClassVo
		{
			for (var i:int = illustratedHandbookStatic.length - 1; i >= 0; i--)
			{
				if (illustratedHandbookStatic[i].itemCid == cid )
				{
					return illustratedHandbookStatic[i];
				}
			}
			return null;
		}

		//根据CID获取图鉴的静态数据
		public function getIllustrationsClassVo(_cid:int):IllustrationsClassVo
		{
			for (var i:int = 0; i < illustratedHandbookStatic.length; i++)
			{
				if (illustratedHandbookStatic[i].cid == _cid )
				{
					return illustratedHandbookStatic[i];
				}
			}
			return null;
		}	
		
		//根据CID获取图鉴的动态数据
		public function getIllustrationsVo(_cid:int):IllustrationsVo
		{
			for (var i:int = 0; i < illustratedHandbookInit.length; i++)
			{
				if (illustratedHandbookInit[i].cid == _cid )
				{
					return illustratedHandbookInit[i];
				}
			}
			return null;
		}			
		
		public function get illustratedHandbookStatic():Array 
		{
			return _illustratedHandbookStatic;
		}
		
		public function set illustratedHandbookStatic(value:Array):void 
		{
			_illustratedHandbookStatic = value;
		}
		
		public function get illustratedHandbookInit():Array 
		{
			return _illustratedHandbookInit;
		}
		
		public function set illustratedHandbookInit(value:Array):void 
		{
			_illustratedHandbookInit = value;
		}
		
		//是不是有新的图鉴
		public function get newIllustratedArr():Array {
			var arr:Array = new Array();
			var tmp:IllustrationsVo;
			for (var i:int = 0; i < illustratedHandbookInit.length; i++) 
			{
				tmp = illustratedHandbookInit[i] as IllustrationsVo;
				if (tmp.isNew) 
				{
					arr.push(tmp);
				}
			}
			return arr;
		}		
		
	}

}