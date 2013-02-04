package happymagic.model.data 
{
	import happymagic.manager.DataManager;
	import happymagic.model.factory.ItemClassFactory;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	import happymagic.model.vo.ItemType;
	import happymagic.model.vo.ItemVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class ItemData 
	{
		
		//---------------静态基础信息-----------------
		/** 物品基础数据列表 */
		private const goodsClassList:Array		= [];
		/** 卷轴基础数据列表 */
		private const scrollClassList:Array		= [];
		/** 材料基础数据列表 */
		private const stuffClassList:Array		= [];
		/** 工作台基础数据列表 */
		private const furnaceClassList:Array		= [];
		/** 装饰基础数据列表 */
		private const decorClassList:Array		= [];
		/** 装备基础数据列表 */
		private const equipmentClassList:Array	= [];
		
		// Map<int:ItemType, Array:ClassArray>
		private const itemClassMap:Array = [[], goodsClassList, scrollClassList, stuffClassList, furnaceClassList, decorClassList, equipmentClassList];
		
		// MAP<int:cid, BaseItemClassVo>
		private const itemCidClassMap:Object = { };
		
		/**
		 * 获取一个基础数据
		 * @param	cid
		 * @return
		 */
		public function getItemClass(cid:int):BaseItemClassVo
		{
			return itemCidClassMap[cid] as BaseItemClassVo;
		}
		
		public function getItemClassByName(name:String):BaseItemClassVo
		{
			if (!name) return null;
			for each(var vo:BaseItemClassVo in itemCidClassMap)
			{
				if (vo.name == name) return vo;
			}
			return null;
		}
		
		/**
		 * 设置itemClass列表
		 * @param	arr Array<Object> 未转换为VO的Object列表, 不同type的数据都混在一起
		 */
		public function setItemClassList(arr:Array):void
		{
			var len:int = arr.length;
			for (var i:int = 0; i < len; i++)
			{
				var cid:int = arr[i].cid;
				var type:int = ItemType.getType(cid);
				var Ref:Class = ItemClassFactory.getClassByType(type);
				if (!Ref) return;
				var vo:BaseItemClassVo = BaseItemClassVo(new Ref());
				itemClassMap[type].push(vo.setData(arr[i]));
				itemCidClassMap[cid] = vo;
			}
		}
		
		
		// 商店列表
		private const shopClassList:Array = [];
		private const shopClassMap:Object = { };
		
		
		/**
		 * 获取商店列表
		 * @return
		 */
		public function getShopItemClassList():Array { return shopClassList; }
		
		public function getShopItemClass(cid:int):BaseItemClassVo { return shopClassMap[cid]; }
		
		/**
		 * 设置商店列表
		 * @param	arr Array<int:cid>
		 */
		public function setShopItemList(arr:Array):void
		{
			for (var k:String in shopClassMap)
			{
				delete shopClassMap[k];
			}
			
			var len:int = arr.length;
			shopClassList.length = len;
			for (var i:int = len - 1; i >= 0; i--)
			{
				var vo:BaseItemClassVo = getItemClass(arr[i]);
				shopClassList[i] = vo;
				shopClassMap[vo.cid] = vo;
			}
		}
		
		
		
		//---------------包裹信息-----------------
		/** 物品数据列表 */
		private const goodsList:Array		= [];
		/** 卷轴数据列表 */
		private const scrollList:Array		= [];
		/** 材料数据列表 */
		private const stuffList:Array		= [];
		/** 工作台数据列表 */
		private const furnaceList:Array		= [];
		/** 装饰数据列表 */
		private const decorList:Array		= [];
		/** 装备数据列表 */
		private const equipmentList:Array	= [];
		
		// Map<int:ItemType, Array:Array<ItemVo>>
		private const itemMap:Array = [null, goodsList, scrollList, stuffList, furnaceList, decorList, equipmentList];
		
		
		/**
		 * 根据id获取数据
		 * @param	id
		 * @return
		 */
		public function getItem(id:String):ItemVo
		{
			for (var i:int = itemMap.length - 1; i >= 1; i--)
			{
				var arr:Array = itemMap[i] as Array;
				for (var j:int = arr.length - 1; j >= 0; j--)
				{
					var vo:ItemVo = ItemVo(arr[j]);
					if (vo.id == id) return vo;
				}
			}
			return null;
		}
		
		//判断是不是给自己使用的物品
		public function isSpecialItem(_cid:int):Boolean
		{
			for (var i:int = 0; i < DataManager.getInstance().gameSetting.useItemArray.length; i++) 
			{
				if (DataManager.getInstance().gameSetting.useItemArray[i] == _cid)
				{
					return true;
				}
			}
			return false;
		}
		
		/**
		 * 获取第一个(装备)相应cid的ItemVo
		 * @param	cid
		 * @return
		 */
		public function getItemByCid(cid:int):ItemVo
		{
			var arr:Array = itemMap[ItemType.getType(cid)] as Array;
			if (!arr) return null;
			for (var i:int = arr.length - 1; i >= 0; i--)
			{
				var vo:ItemVo = ItemVo(arr[i]);
				if (vo.cid == cid) return vo;
			}
			return null;
		}
		
		/**
		 * 计算Item的数量
		 * @param	cid
		 * @param	onlyFullWear 是否仅计算满耐久度(如果是装备)
		 * @return
		 */
		public function getItemCount(cid:int, onlyFullWear:Boolean = false):int
		{
			var arr:Array = itemMap[ItemType.getType(cid)] as Array;
			if (!arr) return 0;
			
			var n:int = 0;
			var canOverlay:Boolean = ItemType.getType(cid) == ItemType.Equipment;
			for (var i:int = arr.length - 1; i >= 0; i--)
			{
				var vo:ItemVo = ItemVo(arr[i]);
				if (vo.cid == cid)
				{
					if (!canOverlay) return vo.num;
					
					if (!onlyFullWear || 0 == vo._base.maxWear || vo.wear == vo._base.maxWear)
					{
						n += arr[i].num;
					}
				}
			}
			return n;
		}
		
		/**
		 * 按分类获取包裹里的数据列表
		 * @param	type
		 * @return
		 */
		public function getItemListByType(type:int):Array { return itemMap[type] as Array; }
		
		public function getItemListByType2(type2:int):Array {
			var arr:Array = itemMap[ItemType.getType(type2)] as Array;
			if (!arr) return [];
			var len:int = arr.length;
			if (0 == len) return [];
			var tArr:Array = [];
			for (var i:int = 0; i < len; i++)
			{
				var vo:ItemVo = arr[i] as ItemVo;
				if (vo.base.type2 == type2) tArr.push(vo);
			}
			return tArr;
		}
		
		/**
		 * 
		 * @param	id
		 * @param	cid
		 * @param	num
		 */
		public function addItem(vo:ItemVo):void
		{
			addItemByCid(vo.cid, vo.num, vo.id, vo.wear);
		}
		
		/**
		 * 添加一个Item
		 * @param	cid
		 * @param	num
		 * @param	id 如果空id或id=cid,则累加Item, 否则单一Item(忽略num)
		 * @param	waer 耐久度,仅限装备有
		 */
		public function addItemByCid(cid:int, num:int = 1, id:String = null, wear:int = 0):void
		{
			var vo:ItemVo;
			if (!id || id == cid+"")
			{
				vo = getItemByCid(cid);
				if (vo != null)
				{
					vo.num += num;
					return;
				}
			}
			
			vo = new ItemVo();
			vo.setData( { cid:cid, num:num, id:id, wear:wear } );
			
			var arr:Array = itemMap[ItemType.getType(cid)];
			if (arr) arr.push(vo);
		}
		
		public function removeItem(vo:ItemVo):void
		{
			removeItemByCid(vo.cid, vo.num, vo.id);
		}
		
		/**
		 * 
		 * @param	cid
		 * @param	num
		 * @param	id 如果空id或id=cid,则减少Item, 否则单一Item(忽略num)
		 */
		public function removeItemByCid(cid:int, num:int = 1, id:String = null):void
		{
			var vo:ItemVo;
			if (!id || id == cid+"")
			{
				vo = getItemByCid(cid);
				if (vo != null)
				{
					vo.num -= num;
					if (vo.num > 0) return;
				}
			}else
			{
				vo = getItem(id);
			}
			
			if (!vo) return;
			var arr:Array = itemMap[ItemType.getType(cid)];
			arr.splice(arr.indexOf(vo), 1);
		}
		
		/**
		 * 
		 * @param	arr Array<[cid, num, id, wear]>
		 */
		public function setItemList(arr:Array):void
		{
			goodsList.length = 0;
			scrollList.length = 0;
			stuffList.length = 0;
			furnaceList.length = 0;
			decorList.length = 0;
			equipmentList.length = 0;
			
			var len:int = arr.length;
			for (var i:int = 0; i < len; i++)
			{
				var vo:ItemVo = new ItemVo();
				var tmp:Array = arr[i];
				vo.setData( { cid:tmp[0], num:tmp[1], id:tmp[2], wear:tmp[3] } );
				var tmpType:int = ItemType.getType(vo.cid);
				if (itemMap[tmpType]) 
				{
					itemMap[tmpType].push(vo);
				}
				
			}
		}
		
	}

}