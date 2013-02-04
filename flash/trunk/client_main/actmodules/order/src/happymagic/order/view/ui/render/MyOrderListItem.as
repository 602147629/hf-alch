package happymagic.order.view.ui.render 
{
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.geom.Rectangle;
	import flash.utils.Timer;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.ui.events.GridPageEvent;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.local.LocaleWords;
	import happyfish.utils.DateTools;
	import happyfish.utils.display.FiltersDomain;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.order.OrderType;
	import happymagic.model.vo.order.OrderVo;
	import happymagic.order.commands.CompleteOrderCommand;
	import happymagic.order.view.OrderAwardList;
	import happymagic.order.view.ui.MyOrderListRender;
	import happymagic.scene.world.SceneType;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class MyOrderListItem extends GridItem 
	{
		private var listView:DefaultListView;
		
		private var awardsTip:OrderAwardList;
		private var icon:IconView;
		private var timeBarMaxWidth:int;
		private var iview:MyOrderListRender;
		private var order:OrderVo;
		private var timer:Timer;
		
		public function MyOrderListItem(ui:MovieClip) 
		{
			super(ui);
			iview = view as MyOrderListRender;
			iview.mouseChildren = true;
			iview.buttonMode = false;
			iview.addEventListener(MouseEvent.CLICK, clickHandler);
			
			var rect:Rectangle = new Rectangle(iview.border.x, iview.border.y, iview.border.width, iview.border.height);
			icon = new IconView(rect.width, rect.height, rect);
			iview.addChildAt(icon, iview.getChildIndex(iview.border));
			iview.removeChild(iview.border);
			
			iview.removeChild(iview.chatTip);
			iview.chatTip.txt.defaultTextFormat = iview.chatTip.txt.getTextFormat(0, 1);
			iview.nameTxt.defaultTextFormat = iview.nameTxt.getTextFormat(0, 1);
			iview.rateTxt.defaultTextFormat = iview.rateTxt.getTextFormat(0, 1);
			iview.rateTxt.alpha = 0.2;
			iview.rateTxt.mouseEnabled = false;
			iview.timeBar.txt.defaultTextFormat = iview.timeBar.txt.getTextFormat(0, 1);
			timeBarMaxWidth = iview.timeBar.bar.width;
			
			listView = new DefaultListView(iview, iview, 4);
			listView.setGridItem(MyOrderListAwardItem, OrderConditionItemRender);
			listView.init(270, 55, 70, 53, 127, 37);
			listView.tweenDelay = 0;
			listView.tweenTime = 0;
			listView.selectCallBack = viewConidtionHandler;
			
			iview.viewAwardsBtn.addEventListener(MouseEvent.ROLL_OVER, overHandler);
			iview.viewAwardsBtn.addEventListener(MouseEvent.ROLL_OUT, outHandler);
			icon.addEventListener(MouseEvent.ROLL_OVER, overHandler);
			icon.addEventListener(MouseEvent.ROLL_OUT, outHandler);
		}
		
		private function viewConidtionHandler(e:GridPageEvent):void 
		{
			var condition:ConditionVo = MyOrderListAwardItem(e.item).data;
			if (condition.type != ConditionType.ITEM) return;
			
			var vo:ActVo = DataManager.getInstance().getActByName("IllustratedHandbook");
			vo.moduleData = { itemCid:int(condition.id) };
			ActModuleManager.getInstance().addActModule(vo);
			
			iview.dispatchEvent(new Event(Event.CLOSE, true));
		}
		
		private function overHandler(e:MouseEvent):void 
		{
			switch(e.currentTarget)
			{
				case icon :
					iview.addChild(iview.chatTip);
					iview.chatTip.txt.text = order.demandDialog;
					break;
					
				case iview.viewAwardsBtn :
					if (!awardsTip)
					{
						awardsTip = new OrderAwardList();
						awardsTip.x = iview.viewAwardsBtn.x;
						awardsTip.y = iview.viewAwardsBtn.y;
						awardsTip.setData(order.awards);
					}
					iview.addChild(awardsTip);
					break;
			}
		}
		
		private function outHandler(e:MouseEvent):void 
		{
			switch(e.currentTarget)
			{
				case icon :
					if (iview.chatTip.parent)
					{
						iview.removeChild(iview.chatTip);
					}
					break;
					
				case iview.viewAwardsBtn :
					if (awardsTip && awardsTip.parent)
					{
						iview.removeChild(awardsTip);
					}
					break;
			}
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			switch(e.target)
			{
				case iview.completeBtn :
					submitOrder(false);
					break;
					
				case iview.failedBtn :
					submitOrder(true);
					break;
			}
		}
		
		private function submitOrder(isFailed:Boolean):void 
		{
			iview.completeBtn.mouseEnabled = false;
			iview.failedBtn.mouseEnabled = false;
			if (timer)
			{
				timer.stop();
				timer.removeEventListener(TimerEvent.TIMER, timerHandler);
			}
			
			new CompleteOrderCommand().complete(order.id, isFailed);
		}
		
		override public function setData(value:Object):void 
		{
			order = value as OrderVo;
			listView.setData(order.needs);
			iview.addChild(iview.failedBtn);
			
			iview.nameTxt.text = order.avatarName;
			icon.setData(order.avatarFaceClass);
			iview.orderBg.gotoAndStop(order.awardType);
			
			if (order.totalTime != -1)
			{
				if (!timer)
				{
					timer = new Timer(1000);
					timer.addEventListener(TimerEvent.TIMER, timerHandler);
					iview.addEventListener(Event.REMOVED_FROM_STAGE, removedHandler);
				}
				timer.start();
			}
			
			timerHandler(null);
		}
		
		private function removedHandler(e:Event):void 
		{
			iview.removeEventListener(Event.REMOVED_FROM_STAGE, removedHandler);
			if (timer)
			{
				timer.stop();
				timer.removeEventListener(TimerEvent.TIMER, timerHandler);
			}
		}
		
		private function timerHandler(e:TimerEvent):void 
		{
			var t:int = order.remainingTime;
			if (-1 == t)
			{
				iview.timeBar.bar.width = timeBarMaxWidth;
				iview.timeBar.txt.text = LocaleWords.getInstance().getWord("order-infiniteTime");
				iview.timeBar.visible = true;
			}else if (0 == t)
			{
				iview.timeBar.visible = false;
			}else
			{
				iview.timeBar.bar.width = t / order.totalTime * timeBarMaxWidth;
				iview.timeBar.txt.text = DateTools.getRemainingTime(t, "%H:%I:%S");
				iview.timeBar.visible = true;
			}
			
			var curSceneType:int = DataManager.getInstance().curSceneType;
			var isHome:Boolean = curSceneType == SceneType.TYPE_HOME;
			
			iview.completeBtn.visible = OrderType.COMPLETED == order.state;
			iview.failedBtn.visible = OrderType.FAILED == order.state;
			iview.completeBtn.mouseEnabled = isHome;
			iview.failedBtn.mouseEnabled = isHome;
			iview.msgTxt.visible = OrderType.WORKING == order.state;
			
			if (!isHome && iview.failedBtn.visible)
			{
				iview.failedBtn.filters = [FiltersDomain.grayFilter];
			}
			if (!isHome && iview.completeBtn.visible)
			{
				iview.completeBtn.filters = [FiltersDomain.grayFilter];
			}
			
			var needs:Array = order.needs;
			var len:int = needs.length;
			var total:int = 0;
			var cur:int = 0;
			for (var i:int = 0; i < len; i++)
			{
				var vo:ConditionVo = needs[i];
				total += vo.num;
				cur += vo.curNum > vo.num ? vo.num : vo.curNum;
			}
			
			var rate:int = cur * 100 / total;
			if (rate > 100) rate = 100;
			iview.rateTxt.text = rate + "%";
			iview.rateTxt.textColor = cur == total ? 0x4EAB3B : 0xB06434;
			
			if (t < 0 || OrderType.FAILED == order.state) timer.stop();
		}
		
	}

}