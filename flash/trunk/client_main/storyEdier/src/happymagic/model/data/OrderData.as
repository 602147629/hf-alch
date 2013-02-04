package happymagic.model.data 
{
	import happymagic.model.vo.order.OrderType;
	import happymagic.model.vo.order.OrderVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class OrderData 
	{
		
		private var friendOrder:OrderVo;
		private const myOrderList:Vector.<OrderVo> = new Vector.<OrderVo>();
		private const otherOderList:Vector.<OrderVo> = new Vector.<OrderVo>();
		
		public function friendOrder2working():void
		{
			if (friendOrder)
			{
				myOrderList.push(friendOrder);
				friendOrder = null;
			}
		}
		
		public function getFriendOrder():OrderVo { return friendOrder; }
		public function setFriendOrder(o:Object):void
		{
			if (o)
			{
				friendOrder = new OrderVo();
				friendOrder.setData(o);
			}else
			{
				friendOrder = null;
			}
		}
		
		public function getOrderList(other:Boolean = false):Vector.<OrderVo>
		{
			return other ? otherOderList : myOrderList;
		}
		
		public function getAcceptOrderList(other:Boolean = false):Vector.<OrderVo>
		{
			var orderList:Vector.<OrderVo> = getOrderList(other);
			var len:int = orderList.length;
			if (0 == len) return null;
			
			var list:Vector.<OrderVo> = new Vector.<OrderVo>();
			for (var i:int = 0; i < len; i++)
			{
				if (orderList[i].state != OrderType.REQUEST)
				{
					list.push(orderList[i]);
				}
			}
			return list;
		}
		
		/**
		 * 设置订单清单
		 * @param	arr
		 * @param	other 是否为别人家的
		 */
		public function setOrderList(arr:Array, other:Boolean):void
		{
			var len:int = arr.length;
			var orderList:Vector.<OrderVo> = getOrderList(other);
			orderList.length = len;
			for (var i:int = 0; i < len; i++)
			{
				var vo:OrderVo = new OrderVo();
				vo.setData(arr[i]);
				orderList[i] = vo;
			}
		}
		
		public function setRequestOrderList(arr:Array, other:Boolean):void
		{
			var orderList:Vector.<OrderVo> = getOrderList(other);
			var len:int = orderList.length;
			for (var i:int = 0; i < len; i++)
			{
				var order:OrderVo = orderList[i];
				if (order.state != OrderType.COMPLETED && order.state != OrderType.FAILED && order.state != OrderType.WORKING)
				{
					orderList.splice(i, 1);
					len--;
				}
			}
			var arrLen:int = arr.length;
			len = orderList.length + arrLen;
			orderList.length = len;
			var begin:int = len - arrLen;
			for (i = begin; i < len; i++)
			{
				var vo:OrderVo = new OrderVo();
				vo.setData(arr[i - begin]);
				orderList[i] = vo;
			}
		}
		
		public function addOrder(vo:OrderVo, other:Boolean = false):void
		{
			getOrderList(other).push(vo);
		}
		
		public function removeOrder(id:String, other:Boolean = false):void
		{
			var orderList:Vector.<OrderVo> = getOrderList(other);
			for (var i:int = orderList.length - 1; i >= 0; i--)
			{
				if (orderList[i].id == id)
				{
					orderList.splice(i, 1);
					break;
				}
			}
		}
		
	}

}