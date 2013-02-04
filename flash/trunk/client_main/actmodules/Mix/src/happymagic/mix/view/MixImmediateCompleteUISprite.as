package happymagic.mix.view 
{
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.geom.Rectangle;
	import flash.utils.Timer;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happyfish.utils.DateTools;
	import happyfish.utils.display.FiltersDomain;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.mix.commands.MixItemCompleteCommand;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	import happymagic.model.vo.MixVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class MixImmediateCompleteUISprite extends UISprite 
	{
		private var iview:MixImmediateCompleteUI;
		private var icon:IconView;
		private var vo:MixVo;
		private var timer:Timer;
		private var lackNum:int;
		
		public function MixImmediateCompleteUISprite() 
		{
			iview = new MixImmediateCompleteUI();
			_view = iview;
			iview.addEventListener(MouseEvent.CLICK, clickHandler);
			
			var rect:Rectangle = new Rectangle(iview.border.x, iview.border.y, iview.border.width, iview.border.height);
			icon = new IconView(rect.width, rect.height, rect);
			iview.addChild(icon);
			iview.removeChild(iview.border);
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			switch(e.target)
			{
				case iview.closeBtn :
					closeMe(false);
					break;
					
				case iview.completeBtn :
					if (lackNum > 0)
					{
						DisplayManager.showNeedMoreItemView(DataManager.getInstance().gameSetting.shalouCid);
					}else
					{
						new MixItemCompleteCommand().complete(vo.furnaceId, false);
						closeMe();
					}
					break;
			}
		}
		
		public function setData(vo:MixVo):void
		{
			this.vo = vo;
			var dataManager:DataManager = DataManager.getInstance();
			var itemVo:BaseItemClassVo = dataManager.itemData.getItemClass(vo.base.furnaceCid);
			iview.titleTxt.text = itemVo.name;
			itemVo = dataManager.itemData.getItemClass(vo.base.itemCid);
			iview.nameTxt.text = itemVo.name;
			iview.priceTxt.text = itemVo.worth + "";
			iview.countTxt.text = vo.num + "";
			iview.probabilityTxt.text = vo.curProbability + "";
			if (!timer)
			{
				timer = new Timer(1000);
				timer.addEventListener(TimerEvent.TIMER, refreshTime);
			}
			timer.start();
			refreshTime(null);
			
			icon.setData(itemVo.className);
		}
		
		private function refreshTime(e:TimerEvent):void 
		{
			if (vo.remainingTime <= 0)
			{
				closeMe();
			}else
			{
				iview.timeTxt.text = DateTools.getRemainingTime(vo.remainingTime, "%H:%I:%S");
				var dataManager:DataManager = DataManager.getInstance();
				var cid:int = dataManager.gameSetting.shalouCid;
				var has:int = dataManager.itemData.getItemCount(cid);
				var time:int = dataManager.gameSetting.shalouTime;
				var need:int = Math.ceil(vo.remainingTime / time);
				iview.propTxt.text = has + "/" + need;
				lackNum = need - has;
				if (lackNum < 0) lackNum = 0;
				//iview.completeBtn.mouseEnabled = need <= has;
				//iview.completeBtn.filters = need <= has ? [] : [FiltersDomain.grayFilter];
			}
		}
		
		override public function closeMe(del:Boolean = false):void 
		{
			vo = null;
			timer.stop();
			super.closeMe(del);
		}
		
	}

}