package happymagic.model.vo 
{
	/**
	 * ...
	 * @author jj
	 */
	public class ItemType
	{
		// 大分类 type
		public static const Goods:int		= 1; // 物品
		public static const Scroll:int		= 2; // 卷轴
		public static const Stuff:int		= 3; // 材料
		public static const Furnace:int		= 4; // 工作台,炼金炉
		public static const Decor:int		= 5; // 装饰
		public static const Equipment:int	= 6; // 装备
		
		// 不属于物品,图鉴时用
		public static const Mob:int			= 7; // 怪
		
		// 物品小分类 type2
		public static const Drink:int		= 11; // 药剂
		public static const Food:int 		= 12; // 食物
		public static const Tool:int		= 13; // 工具
		public static const Atk:int			= 14; // 攻击类物品
		public static const Merchandise:int	= 15; // 商品
		public static const Task:int        = 16; // 任务
		// 卷轴小分类 type2
		public static const Mix:int			= 21; // 合成
		public static const Skill:int		= 22; // 技能
		// 材料小分类 type2
		public static const Plant:int		= 31; // 植物
		public static const Ore:int			= 32; // 矿石
		public static const Animal:int		= 33; // 动物
		public static const SpecialStuff:int = 34; // 特殊矿石
		// 工作台小分类 type2, 没有
		public static const FurnaceType:int	= 41; // 工作台
		// 家具小分类 tyope2
		public static const Floor:int		= 51; // 地板
		public static const Wall:int		= 52; // 墙
		public static const Decoration:int	= 53; // 装饰
		public static const DecorOnWall:int	= 54; // 墙上装饰物
		public static const Door:int		= 55; // 门
		public static const Build:int		= 59; // 建筑
		// 装备小分类 type2
		public static const Weapon:int	= 61; // 武器
		public static const Armor:int	= 62; // 衣服
		public static const Other:int	= 63; // 防具
		public static const Ornament:int= 64; // 角色身上的装饰
		

		public static function getType(cid:int):int { return (cid % 100) / 10; }
		
		public static function getType2(cid:int):int { return cid % 100; }
		
		public static function isType(cid:int, type:int):Boolean { return getType(cid) == type; }
		
		public static function isType2(cid:int, type2:int):Boolean { return getType2(cid) == type2; }
		
	}

}