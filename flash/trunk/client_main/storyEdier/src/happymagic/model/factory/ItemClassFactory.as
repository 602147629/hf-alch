package happymagic.model.factory 
{
	import happymagic.model.vo.classVo.DecorClassVo;
	import happymagic.model.vo.classVo.EquipmentClassVo;
	import happymagic.model.vo.classVo.FurnaceClassVo;
	import happymagic.model.vo.classVo.GoodsClassVo;
	import happymagic.model.vo.classVo.ScrollClassVo;
	import happymagic.model.vo.classVo.StuffClassVo;
	import happymagic.model.vo.ItemType;
	/**
	 * ...
	 * @author lite3
	 */
	public class ItemClassFactory 
	{
		public static function getClassByType(type:int):Class
		{
			switch(type)
			{
				case ItemType.Goods		: return GoodsClassVo;
				case ItemType.Scroll	: return ScrollClassVo;
				case ItemType.Stuff		: return StuffClassVo;
				case ItemType.Furnace	: return FurnaceClassVo;
				case ItemType.Decor		: return DecorClassVo;
				case ItemType.Equipment	: return EquipmentClassVo;
			}
			return null;
		}
	}

}