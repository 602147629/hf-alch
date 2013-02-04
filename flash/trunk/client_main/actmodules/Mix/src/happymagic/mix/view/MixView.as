package happymagic.mix.view 
{
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import happyfish.display.ui.NumSelecterView;
	import happyfish.display.ui.Tooltips;
	import happyfish.display.view.IconView;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.utils.DateTools;
	import happyfish.utils.display.FiltersDomain;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.events.DataManagerEvent;
	import happymagic.manager.DataManager;
	import happymagic.mix.commands.MixItemStartCommand;
	import happymagic.mix.vo.MixNeedVo;
	import happymagic.model.command.BuyItemsCommand;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	import happymagic.model.vo.classVo.MixClassVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class MixView extends MixUI
	{
		private var vo:MixClassVo;
		private var furnaceId:String;
		private var complexIcon:IconView;
		private var needList:Vector.<MixNeedVo>;
		private var countSelecter:NumSelecterView;
		private var probabilitySelecter:NumSelecterView;
		
		private var addProbability:int = 0;
		private var initTimeMcX:int;
		
		private var enoughAll:Boolean = false;
		
		public var backHandler:Function;
		public var closeHandler:Function;
		
		public function MixView() 
		{
			var rect:Rectangle = new Rectangle(border.x, border.y, border.width, border.height);
			complexIcon = new IconView(rect.width, rect.height, rect);
			addChildAt(complexIcon, getChildIndex(border));
			removeChild(border);
			
			goodsBgEffect.gotoAndStop(goodsBgEffect.totalFrames);
			goodsBgEffect.mouseChildren = false;
			goodsBgEffect.mouseEnabled = false;
			finishEffect.stop();
			finishEffect.visible = false;
			finishEffect.mouseChildren = false;
			finishEffect.mouseEnabled = false;
			goodsBgEffect.addFrameScript(goodsBgEffect.totalFrames - 1, showFinishEffect);
			finishEffect.addFrameScript(finishEffect.totalFrames - 1, finishEffectEnd);
			
			initTimeMcX = timeMc.x;
			
			needList = new Vector.<MixNeedVo>();
			countSelecter = new NumSelecterView(counter);
			probabilitySelecter = new NumSelecterView(probabilityer);
			
			addEventListener(MouseEvent.CLICK, clickHandler);
			
			buyAll.mouseChildren = false;
			buyAll.buttonMode = true;
			
			probabilityer.visible = false;
			showHideBtn.gotoAndStop("Show");
			showHideBtn.mouseChildren = false;
			showHideBtn.buttonMode = true;
			
			TextFieldUtil.autoSetTxtDefaultFormat(this, true);
		}
		
		private function finishEffectEnd():void 
		{
			finishEffect.stop();
		}
		
		private function showFinishEffect():void 
		{
			finishEffect.gotoAndPlay(1);
			finishEffect.visible = true;
			goodsBgEffect.stop();
			goodsBgEffect.visible = false;
		}
		
		
		public function setData(vo:MixClassVo, furnaceId:String, count:int = 1):void
		{
			this.vo = vo;
			this.furnaceId = furnaceId;
			
			enoughAll = false;
			
			finishEffect.gotoAndStop(1);
			finishEffect.visible = false;
			goodsBgEffect.gotoAndStop(1);
			goodsBgEffect.visible = true;
			
			var getItemClass:Function = DataManager.getInstance().itemData.getItemClass;
			var getItemCount:Function = DataManager.getInstance().itemData.getItemCount;
			
			var item:BaseItemClassVo = getItemClass(vo.itemCid);
			complexIcon.setData(item.className);
			nameTxt.text = item.name;
			
			needList.length = 0;
			var len:int = vo.needs ? vo.needs.length : 0;
			for (var i:int = 0; i < len; i++)
			{
				needList.push(new MixNeedVo(vo.needs[i][1] * count, getItemCount(vo.needs[i][0], true), getItemClass(vo.needs[i][0])));
			}
			
			showNeedsIcon();
			showNeedsCount();
			addProbability = 0;
			probabilityTxt.text = vo.probability + "%";
			
			countSelecter.maxNum = int.MAX_VALUE;
			countSelecter.setNum(count);
			
			probabilitySelecter.snapInterval = vo.perProbabilityGem;
			probabilitySelecter.minNum = 0;
			probabilitySelecter.maxNum = getMaxCardNum();
			probabilitySelecter.setNum(0);
			
			countSelecter.addEventListener(Event.CHANGE, counterChangeHandler);
			probabilitySelecter.addEventListener(Event.CHANGE, probabilityChangeHandler);
			
			EventManager.getInstance().addEventListener(DataManagerEvent.ITEMS_CHANGE, itemsChangeHander);
			counterChangeHandler(null);
			
			Tooltips.getInstance().register(complexIcon, DataManager.getInstance().itemData.getItemClass(vo.itemCid).content);
		}
		
		private function showNeedsIcon():void 
		{
			var len:int = needList.length;
			for (var i:int = 0; i < 6; i++)
			{
				var item:MovieClip = this["itemRender" + i] as MovieClip;
				var icon:IconView = item.icon as IconView;
				var border:DisplayObject = item.border as DisplayObject;
				if (!icon)
				{
					var rect:Rectangle = new Rectangle(border.x, border.y, border.width, border.height);
					icon = new IconView(rect.width, rect.height, rect);
					item.icon = icon;
					item.addChildAt(icon, item.getChildIndex(border));
					item.removeChild(border);
				}
				if (i < len)
				{
					icon.setData(needList[i].vo.className);
					Tooltips.getInstance().register(icon, needList[i].vo.name, null, Tooltips.TYPE_STAND, new Point(border.x + border.width / 2, border.y));
				}
				this["arrow" + i].visible = i < len;
				this["arrow" + i].stop();
				this["itemRender" + i].visible = i < len;
				
			}
		}
		
		private function showNeedsCount():void 
		{
			var all:Boolean = true;
			var len:int = needList.length;
			for (var i:int = 0; i < len; i++)
			{
				var enough:Boolean = needList[i].has >= needList[i].need;
				all &&= enough;
				this["arrow" + i].gotoAndStop(enough ? 1 + i : 11 + i);
				var html:String = "<font color='" +(enough ? "#D4F536" : "") + "'>" + needList[i].need + "</font>";
				this["itemRender" + i].numTxt.htmlText = html;
			}
			
			if (all == enoughAll) return;
			enoughAll = all;
			if (all)
			{
				goodsBgEffect.gotoAndPlay(1);
			}else
			{
				goodsBgEffect.visible = true;
				goodsBgEffect.gotoAndStop(1);
				finishEffect.visible = false;
				finishEffect.gotoAndStop(1);
			}
		}
		
		private function counterChangeHandler(e:Event):void 
		{
			//timeTxt.text = DateTools.getLostTime(vo.time * countSelecter.num * 1000) + "/" + DateTools.getLostTime(vo.maxTime * 1000);
			timeMc.timeTxt.text = DateTools.getRemainingTime(vo.time * countSelecter.num, "%H:%I:%S");
			
			var needBuy:Boolean = false;
			var needBuyGem:int = 0;
			var cannotBuy:Boolean = false;
			for (var i:int = needList.length - 1; i >= 0; i--)
			{
				var tmp:MixNeedVo = needList[i];
				tmp.need = vo.needs[i][1] * countSelecter.num;
				if (tmp.need > tmp.has)
				{
					needBuy = true;
					if (!tmp.vo.canBuy) cannotBuy = true;
					else
					{
						needBuyGem += (tmp.has - tmp.need) * ((tmp.vo.gem > 0) ? tmp.vo.gem : tmp.vo.coin);
					}
				}
			}
			showNeedsCount();
			buyAll.visible = needBuy && !cannotBuy;
			buyAll.numTxt.text = needBuyGem + "";
			
			timeMc.x = needBuy && !cannotBuy ? initTimeMcX : 0;
			
			buildBtn.mouseEnabled = !needBuy;
			buildBtn.filters = needBuy ? [FiltersDomain.grayFilter] : [];
			
			if (probabilitySelecter.num != 0)
			{
				probabilitySelecter.removeEventListener(Event.CHANGE, probabilityChangeHandler);
				probabilitySelecter.maxNum = getMaxCardNum();
				probabilitySelecter.snapInterval = vo.perProbabilityGem * countSelecter.num;
				probabilitySelecter.setNum(Math.ceil(addProbability / vo.probabilityInterval) * vo.perProbabilityGem * countSelecter.num);
				probabilitySelecter.addEventListener(Event.CHANGE, probabilityChangeHandler);
			}
			updateSelecterMax();
		}
		
		private function probabilityChangeHandler(e:Event):void 
		{
			addProbability = probabilitySelecter.num / vo.perProbabilityGem / countSelecter.num * vo.probabilityInterval;
			if (addProbability + vo.probability > 100) addProbability = 100 - vo.probability;
			probabilityTxt.text = vo.probability + addProbability + "%";
			updateSelecterMax();
		}
		
		
		private function itemsChangeHander(e:DataManagerEvent):void 
		{
			if (!vo) return;
			var getItemCount:Function = DataManager.getInstance().itemData.getItemCount;
			
			for (var i:int = needList.length - 1; i >= 0; i--)
			{
				needList[i].has = getItemCount(needList[i].vo.cid, true);
			}
			counterChangeHandler(null);
		}
		
		private function getMaxCardNum():int
		{
			var probability:int = 100 - vo.probability;
			var cards:int = 0 == vo.perProbabilityGem ? 0 : Math.ceil(probability / vo.probabilityInterval)*vo.perProbabilityGem * countSelecter.num;
			var hasCard:int = DataManager.getInstance().itemData.getItemCount(DataManager.getInstance().gameSetting.crystalCid);
			hasCard -= hasCard % (vo.perProbabilityGem * countSelecter.num);
			return Math.min(hasCard, cards);
		}
		
		private function updateSelecterMax():void
		{
			probabilitySelecter.maxNum = getMaxCardNum();
			probabilitySelecter.snapInterval = vo.perProbabilityGem * countSelecter.num;
			
			var hasCard:int = DataManager.getInstance().itemData.getItemCount(DataManager.getInstance().gameSetting.crystalCid);
			var canCount:int = 0 == probabilitySelecter.num ? int.MAX_VALUE : hasCard / probabilitySelecter.num;
			countSelecter.maxNum = Math.min(canCount, int(vo.maxTime / vo.time));
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			switch(e.target)
			{
				case buyAll:
					buyAllItem();
					break;
					
				case backBtn :
					clear();
					backHandler();
					break;
					
				case illustrationsBtn :
					goIllustrations(vo.itemCid);
					clear();
					closeHandler();
					break;
					
				case buildBtn :
					build();
					clear();
					closeHandler();
					break;
					
				case showHideBtn :
					probabilityer.visible = !probabilityer.visible;
					showHideBtn.gotoAndStop(probabilityer.visible ? "Hide" : "Show");
					break;
			}
		}
		
		private function build():void 
		{
			new MixItemStartCommand().start(vo.cid, countSelecter.num, vo.probability + addProbability, furnaceId);
		}
		
		public function clear():void 
		{
			vo = null;
			goodsBgEffect.gotoAndStop(goodsBgEffect.totalFrames);
			finishEffect.gotoAndStop(1);
			EventManager.getInstance().removeEventListener(DataManagerEvent.ITEMS_CHANGE, itemsChangeHander);
			countSelecter.removeEventListener(Event.CHANGE, counterChangeHandler);
			probabilitySelecter.removeEventListener(Event.CHANGE, probabilityChangeHandler);
		}
		
		private function buyAllItem():void 
		{
			var arr:Array = [];
			for (var i:int = needList.length - 1; i >= 0; i--)
			{
				var tmp:MixNeedVo = needList[i];
				tmp.need = vo.needs[i][1] * countSelecter.num;
				if (tmp.need > tmp.has)
				{
					arr.push([tmp.vo.cid, tmp.need - tmp.has]);
				}
			}
			
			new BuyItemsCommand().buyItems(arr);
		}
		
		private function goIllustrations(itemCid:int):void
		{
			var vo:ActVo = DataManager.getInstance().getActByName("IllustratedHandbook");
			vo.moduleData = { itemCid:itemCid };
			ActModuleManager.getInstance().addActModule(vo);
		}
	}

}