package happyfish.guide 
{
	import flash.display.Stage;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.IEventDispatcher;
	import flash.events.MouseEvent;
	import flash.utils.clearInterval;
	import flash.utils.clearTimeout;
	import flash.utils.setInterval;
	import flash.utils.setTimeout;
	import happyfish.guide.events.GuideEvent;
	import happyfish.guide.interfaces.IGuideMasker;
	import happyfish.guide.utils.AfterCall;
	import happyfish.guide.view.GuideMasker;
	import happyfish.guide.vo.GuideFinishVo;
	import happyfish.guide.vo.GuideStepVo;
	import happyfish.guide.vo.GuideVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class GuideManager extends EventDispatcher 
	{
		
		private var eventDict:Object;
		private var list:Vector.<GuideVo>;
		
		private var loopCheckStepStateHandler:uint;
		private var afterCheckStepStateHandler:uint;
		
		private var currGuideIndex:int;
		private var currGuideStepIndex:int;
		// 调用getGuide后, vo在list中的索引
		private var getGuideIdx:int;
		
		private var stage:Stage;
		// 用于接收主程序发送的事件
		private var eventDispatcher:IEventDispatcher;
		
		// 是否当一个引导完成的时候自动跳到下一个引导
		public var autoNextGuide:Boolean;
		
		
		public function getGuide(id:String):GuideVo
		{
			if (!id) return null;
			var len:int = list.length;
			for (getGuideIdx = 0; getGuideIdx < len; getGuideIdx++)
			{
				if (list[getGuideIdx] && list[getGuideIdx].id == id)
				{
					return list[getGuideIdx];
				}
			}
			
			getGuideIdx = -1;
			return null;
		}
		
		public function getCurrGuide():GuideVo
		{
			if (currGuideIndex >= 0 && currGuideIndex < list.length)
			{
				return list[currGuideIndex];
			}
			return null;
		}
		
		public function getCurrGuideStepIdx():int { return currGuideStepIndex; }
		public function getCurrGuideStepId():String
		{
			var step:GuideStepVo = getCurrGuideStep();
			return step ? step.id : "-1";
		}
		
		public function getCurrGuideStep():GuideStepVo
		{
			var vo:GuideVo = getCurrGuide();
			if (vo) return vo.getStepStep(currGuideStepIndex);
			return null;
		}
		
		
		/**
		 * 添加一个Guide
		 * @param	guide
		 */
		public function addGuide(guide:GuideVo):void
		{
			for (var i:int = guide.finishList.length - 1; i >= 0; i--)
			{
				eventDispatcher.addEventListener(guide.finishList[i].type, finishCheckHandler);
			}
			
			if (1 == list.push(guide))
			{
				nextGuide();
			}
		}
		
		/**
		 * 移除一个Guide
		 * @param	id
		 */
		public function removeGuide(id:String):void
		{
			var vo:GuideVo = getGuide(id);
			if (!vo) return;
			
			list.splice(getGuideIdx, 1);
			// 当前正在执行
			if (getGuideIdx == currGuideIndex)
			{
				stopLoopCheckState();
				// 正在执行
				currGuideIndex = -1;
				currGuideStepIndex = -1;
				// 移除事件
				stage.removeEventListener(MouseEvent.CLICK, stageMouseHandler, true);
				stage.removeEventListener(MouseEvent.MOUSE_DOWN, stageMouseHandler, true);
				dispatchEvent(new GuideEvent(GuideEvent.STEP_REMOVE, false, false, -1, vo));
			}
			
			if (getGuideIdx < currGuideIndex) currGuideIndex--;
			//clearGuide(vo);
			
			dispatchEvent(new GuideEvent(GuideEvent.STEP_RESET));
			
			if (autoNextGuide)
			{
				nextGuide();
			}
		}
		
		public function removeAllGuide():void
		{
			stopLoopCheckState();
			for (var i:int = list.length - 1; i >= 0; i--)
			{
				if (!list[i]) continue;
				clearGuide(list[i]);
			}
			currGuideIndex = -1;
			currGuideStepIndex = -1;
			stage.removeEventListener(MouseEvent.CLICK, stageMouseHandler, true);
			stage.removeEventListener(MouseEvent.MOUSE_DOWN, stageMouseHandler, true);
			list.splice(0, list.length);
			dispatchEvent(new GuideEvent(GuideEvent.STEP_REMOVE));
		}
		
		/**
		 * 完成一个Guide
		 * @param	id
		 */
		public function finishGuide(id:String):void
		{
			var vo:GuideVo = getGuide(id);
			if (!vo) return;
			
			var index:int = getGuideIdx;
			var len:int = list.length;
			
			// 是当前步骤,要前进一小步 
			if (index == currGuideIndex && !list[getGuideIdx].isFinish)
			{
				stopLoopCheckState();
				//currStepIsFinish = true;
				vo.isFinish = true;
				dispatchEvent(new GuideEvent(GuideEvent.STEP_FINISH, false, false, index, vo));
				currGuideStepIndex = -1;
				
				nextGuideStep();
			}
			// 不是当前步骤,去下一步
			else if(index != currGuideIndex)
			{
				trace("not current step is finish, guide id=", vo.id);
				/*list[index] = null;
				//nextStep();
				//pause();
				dispatchEvent(new GuideEvent(GuideEvent.STEP_FINISH, false, false, index, vo));
				//dispatchEvent(new GuideEvent(GuideEvent.STEP_ALL_FINISH, false, false, index, vo));*/
			}
		}
		
		/**
		 * 执行下一个Guide
		 */
		public function nextGuide():void
		{
			stopLoopCheckState();
			var len:int = list.length;
			for (var i:int = currGuideIndex + 1; i < len; i++)
			{
				var vo:GuideVo = list[i];
				if (vo != null) break;
			}
			
			if (!vo)
			{
				for (var j:int = 0; j < i; j++)
				{
					vo = list[j];
					if (vo) break;
				}
			}
			
			if (!vo)
			{
				removeAllGuide();
				dispatchEvent(new GuideEvent(GuideEvent.ALL_STEP_FINISH));
				return;
			}
			
			stage.addEventListener(MouseEvent.CLICK, stageMouseHandler, true);
			stage.addEventListener(MouseEvent.MOUSE_DOWN, stageMouseHandler, true);
			currGuideIndex = i;
			currGuideStepIndex = -1;
			dispatchEvent(new GuideEvent(GuideEvent.STEP_START, false, false, currGuideIndex, vo));
			dispatchEvent(new GuideEvent(GuideEvent.STEP_RESET, false, false, currGuideIndex, list[i]));
			nextGuideStep();
		}
		
		/**
		 * 跳转到特定guide, 如果当前guide是否完成不会改变
		 * @param	id
		 */
		public function gotoGuide(id:String):void
		{
			var vo:GuideVo = getGuide(id);
			if (!vo) return;
			
			if (currGuideIndex == getGuideIdx) return;
			currGuideIndex = getGuideIdx;
			currGuideStepIndex = -1;
			dispatchEvent(new GuideEvent(GuideEvent.STEP_START, false, false, currGuideIndex, vo));
			
			nextGuideStep();
		}
		/**
		 * 执行当前Guide的下一个步骤
		 */
		public function nextGuideStep():void
		{
			var vo:GuideVo = getCurrGuide();
			if (!vo) return;
			currGuideStepIndex++;
			var currGuideStep:GuideStepVo;
			if (vo.isFinish)
			{
				// 结尾了
				if (!vo.nextStepList || currGuideStepIndex >= vo.nextStepList.length)
				{
					currGuideStepIndex = -1;
					list[currGuideIndex] = null;
					//clearGuide(vo);
					dispatchEvent(new GuideEvent(GuideEvent.STEP_ALL_FINISH, false, false, currGuideIndex, vo));
					
					if (autoNextGuide)
					{
						nextGuide();
					}
					return;
				}else
				{
					currGuideStep = vo.nextStepList[currGuideStepIndex];
					currGuideStep.isRunnig = false;
					dispatchEvent(new GuideEvent(GuideEvent.STEP_RESET, false, false, currGuideIndex, vo));
				}
			}else
			{
				if (currGuideStepIndex < vo.prevStepList.length)
				{
					currGuideStep = vo.prevStepList[currGuideStepIndex];
					currGuideStep.isRunnig = false;
					//currGuideStepIndex--;
				}
				
				dispatchEvent(new GuideEvent(GuideEvent.STEP_RESET, false, false, currGuideIndex, vo));
			}
			
			// 立刻执行单步
			if (currGuideStep && currGuideStep.promptlyHandler != null)
			{
				AfterCall.call(currGuideStep.promptlyHandler);
			}
			
			startLoopCheckState();
		}
		
		private function startLoopCheckState():void
		{
			stopLoopCheckState();
			loopCheckStepStateHandler = setInterval(loopCheckStepState, 1000);
			afterCheckStepStateHandler = setTimeout(loopCheckStepState, 0);
		}
		
		private function stopLoopCheckState():void
		{
			if (afterCheckStepStateHandler > 0)
			{
				clearTimeout(afterCheckStepStateHandler);
				afterCheckStepStateHandler = 0;
			}
			if (loopCheckStepStateHandler > 0)
			{
				clearInterval(loopCheckStepStateHandler);
				loopCheckStepStateHandler = 0;
			}
		}
		
		private function loopCheckStepState():void
		{
			trace("this is loopCheckStepState");
			afterCheckStepStateHandler = 0;
			var vo:GuideStepVo = getCurrGuideStep();
			if (!vo || vo.isRunnig)
			{
				stopLoopCheckState();
				return;
			}
			
			if (!vo.isRunnig && GuideStepVo.checkOk(vo))
			{
				stopLoopCheckState();
				vo.isRunnig = true;
				dispatchEvent(new GuideEvent(GuideEvent.STEP_RESET, false, false, currGuideIndex, getCurrGuide())); 
			}
		}
		
		public function gotoGuideStepById(id:String):void
		{
			var step:GuideStepVo = getCurrGuideStep();
			if (step && step.id == id) return;
			var vo:GuideVo = getCurrGuide();
			if (!vo) return;
			
			var guideStepList:Vector.<GuideStepVo> = vo.isFinish ? vo.nextStepList : vo.prevStepList;
			for (var i:int = guideStepList.length - 1; i >= 0; i--)
			{
				if (guideStepList[i].id != id) continue;
				currGuideStepIndex = i;
				step = guideStepList[i];
				step.isRunnig = false;
				dispatchEvent(new GuideEvent(GuideEvent.STEP_RESET, false, false, currGuideIndex, vo));
				
				AfterCall.call(step.promptlyHandler);
				startLoopCheckState();
			}
		}
		
		/**
		 * 跳转到当前Guide的特定步骤
		 * @param	idx 从0开始
		 */
		public function gotoGuideStep(idx:int):void
		{
			if (currGuideStepIndex == idx) return;
			var vo:GuideVo = getCurrGuide();
			if (!vo) return;
			
			var guideStepList:Vector.<GuideStepVo> = vo.isFinish ? vo.nextStepList : vo.prevStepList;
			var len:int = guideStepList.length;
			if (idx >= 0 && idx < len)
			{
				currGuideStepIndex = idx;
				var step:GuideStepVo = vo.getStepStep(idx);
				step.isRunnig = false;
				dispatchEvent(new GuideEvent(GuideEvent.STEP_RESET, false, false, currGuideIndex, vo));
				
				AfterCall.call(step.promptlyHandler);
				startLoopCheckState();
			}
		}
		
		/**
		 * 重新执行当前步骤
		 */
		public function resetCurrGuideStep():void
		{
			var step:GuideStepVo = getCurrGuideStep();
			if (!step || !step.isRunnig) return;
			step.isRunnig = false;
			dispatchEvent(new GuideEvent(GuideEvent.STEP_RESET));
			
			AfterCall.call(step.promptlyHandler);
			startLoopCheckState();
		}
		
		/**
		 * 初始化
		 * @param	eventDispatcher
		 * @param	stage
		 */
		public function init(eventDispatcher:IEventDispatcher, stage:Stage):void
		{
			this.stage = stage;
			this.eventDispatcher = eventDispatcher;
		}
		
		
		private static var instance:GuideManager;
		public static function getInstance():GuideManager
		{
			if (!instance) instance = new GuideManager(Singleton);
			
			return instance;
		}
		
		public function GuideManager(p:Class) 
		{
			if (p != Singleton) throw new Error("GuideManager is an singlon class, can not create it!");
			
			
			list = new Vector.<GuideVo>();
			eventDict = { };
			
			currGuideIndex = -1;
			currGuideStepIndex = -1;
			
			//initStep();
		}
		
		private function stageMouseHandler(e:MouseEvent):void 
		{
			var vo:GuideStepVo = getCurrGuideStep();
			if (!vo || !vo.isRunnig) return;
			
			if (e.type == MouseEvent.CLICK)
			{
				//trace("debug to click");
			}else
			{
				//trace("debug to mouseDown");
			}
			var isMasker:Boolean = e.target is IGuideMasker;
			// 测试去下一步骤
			if (GuideStepVo.testMouseEvent(vo.toNextMouseList, e, false))
			{
				nextGuideStep();
			}
			// 也没有被忽略就取消
			else if(!(e.target is IGuideMasker) && !GuideStepVo.testMouseEvent(vo.ignoreMouseList, e, true))
			{
				removeGuide(getCurrGuide().id);
			}
			// 执行click脚本
			else if(MouseEvent.CLICK == e.type)
			{
				AfterCall.call(vo.clickHandler);
			}
		}
		
		private function clearGuide(vo:GuideVo):void
		{
			for (var i:int = vo.finishList.length - 1; i >= 0; i--)
			{
				eventDispatcher.removeEventListener(vo.finishList[i].type, finishCheckHandler);
			}
		}
		
		private function finishCheckHandler(e:Event):void 
		{
			var len:int = list.length;
			for (var i:int = 0; i < len; i++)
			{
				var step:GuideVo = list[i];
				if (!step) continue;
				for (var j:int = step.finishList.length - 1; j >= 0; j--)
				{
					if (GuideFinishVo.testFinish(e, step.finishList[j]))
					{
						finishGuide(step.id);
						return;
					}
				}
			}
		}
	}

}

class Singleton { }