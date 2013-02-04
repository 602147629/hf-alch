package happymagic.guide.api 
{
	import flash.utils.setTimeout;
	import happyfish.guide.events.GuideEvent;
	import happyfish.guide.GuideManager;
	import happyfish.guide.GuideViewManger;
	import happyfish.guide.vo.GuideVo;
	import happyfish.veal.RuntimeUtil;
	/**
	 * ...
	 * @author lite3
	 */
	public class GuideHelper 
	{
		
		public function GuideHelper() 
		{
			GuideManager.getInstance().addEventListener(GuideEvent.STEP_FINISH, finishHandler);
		}
		
		private function finishHandler(e:GuideEvent):void 
		{
			var vo:GuideVo = e.guide;
			if (!vo) return;
			//new FinishGuideCommand().finishGuide(vo.id, vo.hasMasker);
		}
		
		public function gotoStep(step:*):void
		{
			if (step is Number) GuideManager.getInstance().gotoGuideStep(step);
			else if (step is String) GuideManager.getInstance().gotoGuideStepById(step);
		}
		
		public function gotoCurrGuideStep(idx:int):void
		{
			GuideManager.getInstance().gotoGuideStep(idx);
		}
		
		/**
		 * 重新执行当前步骤
		 */
		public function resetCurrStep():void
		{
			GuideManager.getInstance().resetCurrGuideStep();
		}
		
		public function finishCurrGuide():void
		{
			var vo:GuideVo = GuideManager.getInstance().getCurrGuide();
			if (!vo) return;
			GuideManager.getInstance().finishGuide(vo.id);
		}
		
		public function nextGuideStep():void
		{
			GuideManager.getInstance().nextGuideStep();
		}
		
		public function sendCurrGuide(step:int):void
		{
			var vo:GuideVo = GuideManager.getInstance().getCurrGuide();
			if (!vo) return;
			//new UpdateGuideCommand().update(vo.id, step, vo.hasMasker);
		}
		
		public function sendCurrGuideStep():void
		{
			var vo:GuideVo = GuideManager.getInstance().getCurrGuide();
			if (!vo) return;
			var step:int = GuideManager.getInstance().getCurrGuideStepIdx();
			if (step < 0) return;
			//new UpdateGuideCommand().update(vo.id, step, vo.hasMasker);
		}
		
		public function removeAllGuide():void
		{
			GuideManager.getInstance().removeAllGuide();
		}
		
		public function removeGuide(id:String):void
		{
			GuideManager.getInstance().removeGuide(id);
		}
		
		public function removeCurrGuide():void
		{
			removeGuide(currGuideId);
		}
		
		public function hideMasker():void
		{
			GuideViewManger.getInstance().hideMasker();
		}
		
		public function showMasker():void
		{
			GuideViewManger.getInstance().showMasker();
		}
		
		public function showArrow():void
		{
			GuideViewManger.getInstance().showArrow();
		}
		
		public function showHighlighting():void
		{
			GuideViewManger.getInstance().showHighlighting();
		}
		
		public function get currGuideId():String
		{
			var vo:GuideVo = GuideManager.getInstance().getCurrGuide();
			return vo ? vo.id : null;
		}
		
		public function delayCall(fun:*, time:int):void
		{
			var f:Function = fun is Function ? fun : RuntimeUtil.getValueFun(fun);
			setTimeout(f, time);
		}
		
		public function delay2NextStep(time:int):void
		{
			var currId:String = currGuideId;
			if (!currId) return;
			var manager:GuideManager = GuideManager.getInstance();
			var currStep:int = manager.getCurrGuideStepIdx();
			if (currStep < 0) return;
			
			setTimeout(function():void
			{
				if(currId != currGuideId || manager.getCurrGuideStepIdx() != currStep) return;
				manager.nextGuideStep();
			}, time);
		}
		
		/**
		 * 一个?:运算
		 * @param	_if  如果为String则通过RuntimeUtil计算结果并强转为Boolean, 否则直接取值
		 * @param	_then _if为true时执行的表达式字符串,用RuntimeUtil计算
		 * @param	_else _if为false时执行的表达式字符串,用RuntimeUtil计算
		 */
		public function ifThenElse(_if:* = null, _then:String = null, _else:String = null):void
		{
			if (_if is String)
			{
				try {
					_if = Boolean(RuntimeUtil.getValue(String(_if)));
				}catch (err:Error) { _if = false; }
			}
			if (_if) 
			{
				if (_then) RuntimeUtil.getValue(_then);
			}else
			{
				if (_else) RuntimeUtil.getValue(_else);
			}
		}
		
		public function sendNotification(name:String):void
		{
			//ApplicationFacade.getInstance().sendNotification(name);
		}
		
		/**
		 * 执行一个列表,如果元素是Function, 则直接调用,否则用RuntimeUtil执行结果
		 * @param	...args
		 */
		public function doList(...args):void
		{
			var len:int = args.length;
			for (var i:int = 0; i < len; i++)
			{
				try {
					if (args[i] is Function)
					{
						args[i]();
					}else
					{
						RuntimeUtil.getValue(String(args[i]));
					}
				}catch (err:Error) { }
			}
		}
	}

}