package happymagic.order
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.TimerEvent;
	import flash.utils.getQualifiedClassName;
	import flash.utils.Timer;
	import happyfish.events.GameMouseEvent;
	import happyfish.events.MainEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.interfaces.IModule;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happyfish.time.Time;
	import happymagic.events.DataManagerEvent;
	import happymagic.events.FunctionFilterEvent;
	import happymagic.events.MagicEvent;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.data.OrderData;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.order.OrderType;
	import happymagic.model.vo.order.OrderVo;
	import happymagic.order.commands.RejectOrderCommand;
	import happymagic.order.commands.RequestOrderCommand;
	import happymagic.order.events.OrderEvent;
	import happymagic.order.view.Customer;
	import happymagic.order.view.MyOrderIcon;
	import happymagic.order.view.MyOrderListUISprite;
	import happymagic.order.view.OrderDescriptionUISprite;
	import happymagic.order.vo.Data;
	import happymagic.order.vo.ModuleType;
	import happymagic.scene.world.SceneType;
	//import happymagic.order.view.SatisfactionBar;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class OrderMain extends Sprite 
	{
		private var delayShowPersonTimer:Timer;
		private var requestOrderTimer:Timer;
		private var personOutTimer:Timer;
		private var orderStateCheckTimer:Timer;
		
		private var freeList:Array = [];
		
		private var data:Data;
		
		
		private var myOrderIcon:MyOrderIcon;
		//private var satisfactionBar:SatisfactionBar;
		
		private var isSelf:Boolean;
		
		public function OrderMain():void 
		{
			TipCoin;
			data = Data.instance;
			EventManager.addEventListener(MainEvent.MAIN_DATA_COMPELTE, init);
		}
		
		private function init(e:Event):void 
		{
			EventManager.removeEventListener(MainEvent.MAIN_DATA_COMPELTE, init);
			
			EventManager.addEventListener(SceneEvent.SCENE_COMPLETE, sceneCompleteHandler);
			EventManager.addEventListener(SceneEvent.SCENE_CLEARED, clear);
			EventManager.addEventListener(GameMouseEvent.GAME_MOUSE_EVENT, customerClickHandler);
			EventManager.addEventListener(DataManagerEvent.ITEMS_CHANGE, orderStateCheck);
			
			EventManager.addEventListener(OrderEvent.ACCEPT_ORDER, orderHandler);
			EventManager.addEventListener(OrderEvent.REJECT_ORDER, orderHandler);
			EventManager.addEventListener(OrderEvent.FAILED_ORDER, orderHandler);
			EventManager.addEventListener(OrderEvent.COMPLETE_ORDER, orderHandler);
			EventManager.addEventListener(OrderEvent.REFRESH_ORDER, orderHandler);
			
			var module:ModuleVo = new ModuleVo();
			module.className = getQualifiedClassName(OrderDescriptionUISprite);
			module.name = ModuleType.ORDER_DESCRIPTION_NAME;
			ModuleManager.getInstance().addModule(module);
			
			module = new ModuleVo();
			module.className = getQualifiedClassName(MyOrderListUISprite);
			module.name = ModuleType.MY_ORDER_LIST_NAME;
			ModuleManager.getInstance().addModule(module);
			
			//module = new ModuleVo();
			//module.className = getQualifiedClassName(SatisfactionBar);
			//module.name = ModuleType.SATISFACTION_BAR_NAME;
			//module.algin = AlginType.TC;
			//module.mvType = ModuleMvType.FROM_TOP;
			//module.layer = 2;
			//module.x = 170 - 452;
			//module.y = 54;
			//satisfactionBar = ModuleManager.getInstance().addModule(module) as SatisfactionBar;
			//ModuleManager.getInstance().showModule(ModuleType.SATISFACTION_BAR_NAME);
			
			
			myOrderIcon = new MyOrderIcon();
			if (ModuleManager.getInstance().getModule("rightCenterMenu"))
			{
				showMyOrderIcon(null);
			}else
			{
				EventManager.addEventListener(MagicEvent.RIGHT_CENTER_INIT, showMyOrderIcon);
			}
			
			Data.instance.requestNoviceOrder = requestNoviceOrder;
		}
		
		private function notRequest():void
		{
			
		}
		
		private function showMyOrderIcon(e:Event):void
		{
			EventManager.removeEventListener(MagicEvent.RIGHT_CENTER_INIT, showMyOrderIcon);
			var rightCenterMenu:IModule = ModuleManager.getInstance().getModule("rightCenterMenu");
			rightCenterMenu["addMc"]("order", myOrderIcon, int.MAX_VALUE);
		}
		
		private function customerClickHandler(e:GameMouseEvent):void 
		{
			if (e.mouseEventType != GameMouseEvent.CLICK) return;
			if(e.itemType != "Customer") return;
			
			var customer:Customer = e.item as Customer;
			if (!customer.canRequest) return;
			
			var uisprite:OrderDescriptionUISprite = ModuleManager.getInstance().showModule(ModuleType.ORDER_DESCRIPTION_NAME) as OrderDescriptionUISprite;
			DisplayManager.uiSprite.setBg(uisprite);
			uisprite.resetShow(customer.order);
		}
		
		private function orderHandler(e:OrderEvent):void 
		{
			switch(e.type)
			{
				case OrderEvent.ACCEPT_ORDER :
					var order:OrderVo = null;
					var customer:Customer = getCustomerById(e.id, true);
					if (customer)
					{
						order = customer.order;
					}else if(e.id == DataManager.getInstance().orderData.getFriendOrder().id)
					{
						order = DataManager.getInstance().orderData.getFriendOrder();
						DataManager.getInstance().orderData.friendOrder2working();
					}
					if (!order) return;
					
					order.state = OrderType.WORKING;
					data.workingList.push(order);
					if(customer) customer.accepted();
					refreshOrderState(order);
					myOrderIcon.refreshState();
					break;
					
				case OrderEvent.REJECT_ORDER :
					customer = getCustomerById(e.id, true);
					if (!customer) break;
					DataManager.getInstance().orderData.removeOrder(customer.order.id);
					customer.rejected();
					
					if (!e.order) break;
					DataManager.getInstance().orderData.addOrder(e.order);
					freeList.push(e.order);
					startDelayShowPerson();
					break;
					
				case OrderEvent.COMPLETE_ORDER :
					order = getOrderById(e.id, true);
					order.state = OrderType.COMPLETED;
					DataManager.getInstance().orderData.removeOrder(order.id);
					myOrderIcon.refreshState();
					customer = CustomerFactory.createCustomer(order);
					if (!customer) break;
					customer.comeInAndOut(Customer.COMPLETE, order.successDialog, e.flow);
					break;
					
				case OrderEvent.FAILED_ORDER :
					order = getOrderById(e.id, true);
					order.state = OrderType.FAILED;
					DataManager.getInstance().orderData.removeOrder(order.id);
					myOrderIcon.refreshState();
					customer = CustomerFactory.createCustomer(order);
					if (!customer) break;
					customer.comeInAndOut(Customer.FAILED, order.failedDialog, e.flow);
					break;
					
				case OrderEvent.REFRESH_ORDER :
					for (var i:int = data.personList.length - 1; i >= 0; i--)
					{
						var person:Customer = Customer(data.personList[i]);
						person.gotoOut(Customer.NORMAL);
					}
					data.personList.length = 0;
					freeList.length = 0;
					sceneCompleteHandler(null);
					break;
			}
		}
		
		private function getCustomerById(id:String, autoRemove:Boolean = false):Customer 
		{
			for (var i:int = data.personList.length - 1; i >= 0; i--)
			{
				var person:Customer = data.personList[i] as Customer;
				if (person.order.id == id)
				{
					if (autoRemove) data.personList.splice(i, 1);
					return person;
				}
			}
			return null;
		}
		
		private function getOrderById(id:String, autoRemove:Boolean = false):OrderVo 
		{
			for (var i:int = data.workingList.length - 1; i >= 0; i--)
			{
				var vo:OrderVo = data.workingList[i] as OrderVo;
				if (vo.id == id)
				{
					if (autoRemove) data.workingList.splice(i, 1);
					return vo;
				}
			}
			return null;
		}
		
		/**
		 * 更新curNum
		 * @param	vo
		 * @return curNum是否有改变
		 */
		private function refreshOrderCurNum(vo:OrderVo):Boolean
		{
			var getCount:Function = DataManager.getInstance().itemData.getItemCount;
			var change:Boolean = false;
			var len:int = vo.needs ? vo.needs.length : 0;
			for (var i:int = 0; i < len; i++)
			{
				var tmp:ConditionVo = vo.needs[i] as ConditionVo;
				var num:int = getCount(int(tmp.id), true);
				if (num != tmp.curNum)
				{
					change = true;
					tmp.curNum = num;
				}
			}
			return change;
		}
		
		// 更新订单状态并返回是否修改
		private function refreshOrderState(vo:OrderVo):Boolean 
		{
			if (vo.state != OrderType.WORKING && vo.state != OrderType.COMPLETED) return false;
			
			// 测试失败
			if (0 == vo.remainingTime)
			{
				vo.state = OrderType.FAILED;
				return true;
			}
			
			var getCount:Function = DataManager.getInstance().itemData.getItemCount;
			var enough:Boolean = true;
			var len:int = vo.needs ? vo.needs.length : 0;
			for (var i:int = 0; i < len; i++)
			{
				var tmp:ConditionVo = vo.needs[i] as ConditionVo;
				tmp.currentNum = getCount(int(tmp.id), true);
				if(tmp.currentNum < tmp.num)
				{
					enough = false;
					break;
				}
			}
			const state:int = enough ? OrderType.COMPLETED : OrderType.WORKING;
			if (state == vo.state) return false;
			vo.state = state;
			return true;
		}
		
		private function sceneCompleteHandler(event:Event):void 
		{
			if (DataManager.getInstance().functionFilterData.isLock("order"))
			{
				EventManager.addEventListener(FunctionFilterEvent.UNLOCK, unlockFuncHandler);
				return;
			}
			myOrderIcon.refreshState();
			if (!orderStateCheckTimer)
			{
				orderStateCheckTimer = new Timer(1000);
				orderStateCheckTimer.addEventListener(TimerEvent.TIMER, orderStateCheck);
				orderStateCheckTimer.start();
			}
			
			var curSceneType:int = DataManager.getInstance().curSceneType;
			if (curSceneType != SceneType.TYPE_HOME)
			{
				clear();
				return;
			}else
			{
				data.workingList.length = 0;
				//satisfactionBar.view.visible = true;
				var orderData:OrderData = DataManager.getInstance().orderData;
				var list:Vector.<OrderVo> = orderData.getOrderList();
				for (var i:int = list.length - 1; i >= 0; i--)
				{
					(OrderType.REQUEST == list[i].state ? freeList : data.workingList).push(list[i]);
				}
			}
			
			if (freeList.length != 0)
			{
				startDelayShowPerson();
			}else
			{
				startRequestOrder();
			}
			
			if (!personOutTimer)
			{
				personOutTimer = new Timer(1000);
				personOutTimer.addEventListener(TimerEvent.TIMER, personOutTimerHandler);
			}
			personOutTimer.start();
		}
		
		private function unlockFuncHandler(e:FunctionFilterEvent):void 
		{
			if (e.name == "order")
			{
				EventManager.removeEventListener(FunctionFilterEvent.UNLOCK, unlockFuncHandler);
				sceneCompleteHandler(null);
			}
		}
		
		private function orderStateCheck(e:Event):void 
		{
			var change:Boolean = false;
			var list:Vector.<OrderVo> = DataManager.getInstance().orderData.getOrderList();
			for (var i:int = list.length - 1; i >= 0; i--)
			{
				if(refreshOrderState(list[i])) change = true;
			}
			if (change) myOrderIcon.refreshState();
		}
		
		private function personOutTimerHandler(e:TimerEvent):void 
		{
			for (var i:int = data.personList.length - 1; i >= 0; i--)
			{
				var customer:Customer = data.personList[i] as Customer;
				if (customer.order.state != OrderType.REQUEST) continue;
				var time:int = Time.getRemainingTimeByEnd(customer.order.outTime);
				if (0 == time)
				{
					new RejectOrderCommand().reject(customer.order.id, false, null);
					customer.hideRequest();
					//var ui:UISprite = ModuleManager.getInstance().getModule(ModuleType.ORDER_DESCRIPTION_NAME) as UISprite;
					//if (ui && ModuleStateType.SHOWING == ui.state) ui.closeMe();
				}else if (time < 5)
				{
					customer.readyRejectLeave();
				}
			}
		}
		
		private function startDelayShowPerson():void 
		{
			if (!delayShowPersonTimer)
			{
				delayShowPersonTimer = new Timer(5000);
				delayShowPersonTimer.addEventListener(TimerEvent.TIMER, delayShowPersonTimerHandler);
			}
			delayShowPersonTimer.start();
		}
		
		private function delayShowPersonTimerHandler(e:TimerEvent):void 
		{
			var order:OrderVo = freeList.shift() as OrderVo;
			if (!order)
			{
				delayShowPersonTimer.stop();
				return;
			}
			
			var customer:Customer = CustomerFactory.createCustomer(order);
			customer.comeInAndShowRequest();
			data.personList.push(customer);
			EventManager.dispatchEvent(new OrderEvent(OrderEvent.DELAY_ORDER));
			
			if (0 == freeList.length)
			{
				delayShowPersonTimer.stop();
				startRequestOrder();
			}
		}
		
		private function startRequestOrder():void 
		{
			if (Data.instance.notRequestOrder) return;
			
			if (!requestOrderTimer)
			{
				requestOrderTimer = new Timer(DataManager.getInstance().gameSetting.customerInTime * 1000);
				requestOrderTimer.addEventListener(TimerEvent.TIMER, requestOrderTimerHandler);
			}
			
			if (!requestOrderTimer.running)
			{
				requestOrderTimer.start();
				requestOrderTimerHandler(null);
			}
		}
		
		public function requestNoviceOrder():void
		{
			new RequestOrderCommand().request(requestOrderComplete, true);
		}
		
		private function requestOrderTimerHandler(e:TimerEvent):void 
		{
			if (data.personList.length >= 10) return;
			new RequestOrderCommand().request(requestOrderComplete);
		}
		
		private function requestOrderComplete(order:OrderVo):void
		{
			// 创建小人
			DataManager.getInstance().orderData.addOrder(order);
			var customer:Customer = CustomerFactory.createCustomer(order);
			if (!customer) return;
			customer.comeInAndShowRequest();
			data.personList.push(customer);
			
		}
		
		private function clear(e:Event = null):void
		{
			freeList.length = 0;
			data.personList.length = 0;
			if(delayShowPersonTimer) delayShowPersonTimer.stop();
			if(requestOrderTimer) requestOrderTimer.stop();
			if(personOutTimer) personOutTimer.stop();
			//satisfactionBar.view.visible = false;
		}
	}
}