package happymagic.order.view 
{
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.geom.Rectangle;
	import flash.utils.Timer;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.ui.events.GridPageEvent;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.time.Time;
	import happyfish.utils.display.FiltersDomain;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.display.view.ui.DefaultAwardItemRender;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.order.OrderVo;
	import happymagic.order.commands.AcceptOrderCommand;
	import happymagic.order.commands.RefreshOrderCommand;
	import happymagic.order.commands.RejectOrderCommand;
	import happymagic.order.events.OrderEvent;
	import happymagic.order.flow.OrderAcceptMovieFlow;
	import happymagic.order.utils.TimeUtil;
	import happymagic.order.view.ui.OrderDescriptionUI;
	import happymagic.order.view.ui.render.OrderAwardsItem;
	import happymagic.order.view.ui.render.OrderConditionItemRender;
	import happymagic.order.view.ui.render.OrderItem;
	import happymagic.order.view.ui.render.OrderItemRender;
	import happymagic.order.vo.Data;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class OrderDescriptionUISprite extends UISprite 
	{
		private var iview:OrderDescriptionUI;
		private var icon:IconView;
		
		private var orderListView:DefaultListView;
		private var awardListView:DefaultListView;
		private var conditionListView:DefaultListView;
		
		private var order:OrderVo;
		
		private var word:String;
		private var timer:Timer;
		private var delayClose:Boolean = false;
		
		
		public function OrderDescriptionUISprite() 
		{
			iview = new OrderDescriptionUI();
			_view = iview;
			
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			
			var rect:Rectangle = new Rectangle(iview.faceBorder.x, iview.faceBorder.y, iview.faceBorder.width, iview.faceBorder.height);
			icon = new IconView(rect.width, rect.height, rect);
			iview.addChildAt(icon, iview.getChildIndex(iview.faceBorder));
			iview.removeChild(iview.faceBorder);
			
			iview.refreshGemTxt.mouseEnabled = false;
			iview.timeTxt.defaultTextFormat = iview.timeTxt.getTextFormat(0, -1);
			iview.orderAwardBg.stop();
			
			iview.addEventListener(MouseEvent.CLICK, clickHandler);
			
			conditionListView = new DefaultListView(iview, iview, 9);
			conditionListView.init(340, 55, 80, 53, -46, -69);
			conditionListView.setGridItem(OrderAwardsItem, OrderConditionItemRender);
			conditionListView.tweenDelay = 0;
			conditionListView.tweenTime = 0;
			conditionListView.selectCallBack = viewConidtionHandler;
			
			awardListView = new DefaultListView(iview, iview, 9);
			awardListView.init(450, 70, 90, 70, -134, 70);
			awardListView.setGridItem(OrderAwardsItem, DefaultAwardItemRender);
			awardListView.tweenDelay = 0;
			awardListView.tweenTime = 0;
			
			orderListView = new DefaultListView(iview.orderListContainer, iview, 5, false, false);
			orderListView.init(95, 302, 95, 55, 0, 34);
			orderListView.setGridItem(OrderItem, OrderItemRender);
			orderListView.selectCallBack = selectHandler;
			orderListView.tweenDelay = 0;
			orderListView.tweenTime = 0;
			
			iview.addChild(iview.helpBtn);
			
			EventManager.addEventListener(OrderEvent.REJECT_ORDER, refreshOrderHandler, false, -1);
			EventManager.addEventListener(OrderEvent.ACCEPT_ORDER, refreshOrderHandler, false, -1);
			EventManager.addEventListener(OrderEvent.REFRESH_ORDER, refreshOrderHandler, false, -1);
			EventManager.addEventListener(OrderEvent.DELAY_ORDER, refreshOrderHandler, false, -1);
		}
		
		private function viewConidtionHandler(e:GridPageEvent):void 
		{
			var condition:ConditionVo = OrderAwardsItem(e.item).data;
			if (condition.type != ConditionType.ITEM) return;
			
			var vo:ActVo = DataManager.getInstance().getActByName("IllustratedHandbook");
			vo.moduleData = { itemCid:int(condition.id) };
			ActModuleManager.getInstance().addActModule(vo);
			
			closeMe();
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			switch(e.target)
			{
				case iview.cancelBtn :
				case iview.closeBtn :
					closeMe();
					break;
					
				case iview.rejectBtn :
					new RejectOrderCommand().reject(order.id, true, requestCompleter);
					iview.mouseChildren = false;
					break;
					
				case iview.okBtn :
					if (!order) return;
					new AcceptOrderCommand().accept(order.id, requestCompleter);
					var customer:Customer = order ? Data.instance.getCustomerById(order.id) : null;
					if (customer) customer.hideRequest();
					iview.mouseChildren = false;
					break;
					
				case iview.refreshBtn :
					new RefreshOrderCommand().refresh(requestCompleter);
					iview.mouseChildren = false;
					break;
			}
			
			function requestCompleter(success:Boolean):void 
			{
				iview.mouseChildren = true;
				
				if (!success)
				{
					if(customer) customer.showRequest();
				}else if (e.target == iview.okBtn)
				{
					showAcceptMovie();
				}else if (e.target == iview.refreshBtn)
				{
					closeMe();
				}
			}
		}
		
		private function showAcceptMovie():void
		{
			new OrderAcceptMovieFlow().showAt(iview.x, iview.y, delayCloseMe);
		}
		
		private function delayCloseMe():void 
		{
			if (delayClose)
			{
				delayClose = false;
				iview.mouseChildren = true;
				closeMe();
			}
		}
		
		override public function closeMe(del:Boolean = false):void 
		{
			order = null;
			if(timer) timer.stop();
			super.closeMe(del);
		}
		
		public function setData(order:OrderVo):void
		{
			if (!order) order = DataManager.getInstance().orderData.getFriendOrder();
			resetShow(order);
		}
		
		public function showFriendOrder(o:Object):void
		{
			setData(null);
		}
		
		/**
		 * 
		 * @param	order
		 */
		public function resetShow(order:OrderVo):void 
		{
			if (!order && 0 == Data.instance.personList.length)
			{
				closeMe();
				return;
			}
			
			var friend:OrderVo = DataManager.getInstance().orderData.getFriendOrder();
			setFriendView(friend && order && order.id == friend.id);
			
			iview.refreshGemTxt.text = DataManager.getInstance().gameSetting.orderRefreshGem + "";
			orderListView.setData(Data.instance.personList);
			var idx:int = order ? Data.instance.personList.indexOf(Data.instance.getCustomerById(order.id)) : 0;
			if (idx < 0) idx = 0;
			orderListView.selectedIndex = idx;
			
			if (orderListView.selectedValue)
			{
				order = Customer(orderListView.selectedValue).order;
			}
			
			showOrder(order);
		}
		
		private function setFriendView(isFriend:Boolean):void 
		{
			iview.refreshBtn.filters = isFriend ? [FiltersDomain.grayFilter] : [];
			iview.refreshGemTxt.filters = isFriend ? [FiltersDomain.grayFilter] : [];
		}
		
		private function showOrder(order:OrderVo):void
		{
			this.order = order;
			var canAccept:Boolean = Data.instance.workingList.length < DataManager.getInstance().currentUser.maxOrder;
			
			icon.setData(order.avatarFaceClass);
			
			iview.chatTxt.text = order.demandDialog;
			//iview.chatTxt.width = iview.chatTxt.textWidth + 4;
			//iview.chatBg.width = iview.chatTxt.width + (iview.chatTxt.x - iview.chatBg.x) * 2;
			
			iview.orderAwardBg.gotoAndStop(order.awardType);
			iview.okBtn.visible = canAccept;
			iview.cancelBtn.visible = !canAccept;
			word = canAccept ? "order-numSecondsLeft" : "order-orderToUpLimitWillLeft";
			iview.timeTxt.htmlText = TimeUtil.getOrderRemainingTimeHtml(order.totalTime, true);
			
			conditionListView.setData(order.needs || []);
			awardListView.setData(order.awards || []);
			
			if (!timer)
			{
				timer = new Timer(1000);
				timer.addEventListener(TimerEvent.TIMER, refreshTime);
			}
			timer.start();
			refreshTime(null);
		}
		
		private function refreshTime(e:TimerEvent):void 
		{
			var sec:int = Time.getRemainingTimeByEnd(order.outTime);
			iview.stateTxt.text = LocaleWords.getInstance().getWord(word, sec);
		}
		
		private function selectHandler(e:GridPageEvent):void
		{
			showOrder(Customer(orderListView.selectedValue).order);
		}
		
		private function refreshOrderHandler(e:OrderEvent):void 
		{
			if (0 == Data.instance.personList.length)
			{
				if (OrderEvent.ACCEPT_ORDER == e.type)
				{
					delayClose = true;
					iview.mouseChildren = false;
				}
				return;
			}
			resetShow(order);
		}
	}

}