package happyfish.guide 
{
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.display.Stage;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import happyfish.guide.events.GuideEvent;
	import happyfish.guide.interfaces.IGuideArrow;
	import happyfish.guide.interfaces.IGuideDialog;
	import happyfish.guide.interfaces.IGuideMasker;
	import happyfish.guide.interfaces.IGuideTip;
	import happyfish.guide.vo.DialogVo;
	import happyfish.guide.vo.GuideStepVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class GuideViewManger
	{
		private var isPrevStep:Boolean;
		private var dialogList:Vector.<DialogVo>;
		
		private var step:GuideStepVo;
		private var arrow:IGuideArrow;
		private var tip:IGuideTip;
		private var stage:Stage;
		private var masker:IGuideMasker;
		private var needShowMasker:Boolean;
		
		public function init(stage:Stage, arrow:IGuideArrow, masker:IGuideMasker, tip:IGuideTip):void
		{
			this.arrow = arrow;
			this.masker = masker;
			this.tip = tip;
			masker.setStage(stage);
			if (tip) tip.setStage(stage);
			this.stage = stage;
			//DisplayObject(masker).addEventListener(MouseEvent.CLICK, ignoreClick, false, int.MAX_VALUE);
			//DisplayObject(arrow).addEventListener(MouseEvent.CLICK, ignoreClick, false, int.MAX_VALUE);
			_init();
		}
		
		//private function ignoreClick(e:MouseEvent):void 
		//{
			//e.stopImmediatePropagation();
		//}
		
		public static var instance:GuideViewManger;
		public static function getInstance():GuideViewManger
		{
			if (!instance) instance = new GuideViewManger(Singleton);
			return instance;
		}
		
		public function GuideViewManger(p:Class) 
		{
			if (p != Singleton) throw new Error("this is single");
		}
		
		private function _init():void 
		{
			//GuideManager.getInstance().addEventListener(GuideEvent.ALL_STEP_FINISH, finishHandler);
			GuideManager.getInstance().addEventListener(GuideEvent.STEP_ALL_FINISH, stepAllFinishHandler);
			GuideManager.getInstance().addEventListener(GuideEvent.STEP_RESET, stepResetHandler);
			GuideManager.getInstance().addEventListener(GuideEvent.STEP_START, stepStartHandler);
			GuideManager.getInstance().addEventListener(GuideEvent.STEP_REMOVE, stepAllFinishHandler);
			//GuideManager.getInstance().addEventListener(GuideEvent.STEP_BEFORE, stepBeforeHandler);
		}
		
		private function stepStartHandler(e:GuideEvent):void 
		{
			trace("into guide ", e.guide.id);
			if (e.guide.hasMasker) masker.show();
			else masker.remove();
		}
		
		private function stepAllFinishHandler(e:GuideEvent):void 
		{
			clearStep();
			masker.remove();
		}
		
		//private function stepBeforeHandler(e:GuideEvent):void 
		//{
			//dialogIndex = -1;
			//dialogList = GuideManager.getInstance().getStepBeforeDialogList();
			//showNextDialog();
		//}
		
		private function stepResetHandler(e:GuideEvent):void 
		{
			if (e.guide) trace("reset step", GuideManager.getInstance().getCurrGuideStepId());
			needShowMasker = e.guide && e.guide.hasMasker;
			if (needShowMasker) masker.show();
			var newStep:GuideStepVo = GuideManager.getInstance().getCurrGuideStep();
			if (newStep && !newStep.isRunnig) newStep = null;
			isPrevStep = newStep == step;
			if (step != newStep) clearStep();
			
			step = newStep;
			initStep();
		}
		
		private function clearStep():void 
		{
			if (arrow) arrow.remove();
			if (tip) tip.remove();
			//if (arrow.parent) arrow.parent.removeChild(arrow);
			//if (dialog.parent) dialog.parent.removeChild(dialog);
			step = null;
		}
		
		private function initStep():void 
		{
			if (!step)
			{
				clearStep();
				return;
			}
			if(needShowMasker) masker.show();
			
			if (!isPrevStep)
			{
				dialogList = step.dialogList;
				showDialog();
			}
		}
		
		public function hideMasker():void
		{
			masker.visible = false;
		}
		
		public function showMasker():void
		{
			masker.visible = true;
		}
		
		public function showArrow():void
		{
			if (!step) return;
			
			var actTips:*;
			var container:DisplayObjectContainer;
			if (step.actTips != null)
			{
				try {
					actTips = step.actTips();
					if(actTips != null) container = step.container();
				}catch (err:Error) { showErr(err); }
			}
			
			if (container)
			{
				var p:Point;
				var radius:int;
				if (actTips is DisplayObject)
				{
					var rect:Rectangle = DisplayObject(actTips).getRect(stage);
					p = new Point(rect.x + rect.width / 2, rect.y + rect.height / 2);
					radius = step.radius > 0 ? step.radius : (Math.min(rect.width, rect.height) / 2);
					//radius = 200;
				}else if(("x" in actTips) && ("y" in actTips))
				{
					p = new Point(actTips.x, actTips.y);
					radius = step.radius;
				}
				
				p.x += step.offsetX;
				p.y += step.offsetY;
				
				if (needShowMasker) masker.showHasCircle(p.x, p.y, radius);
				
				p.y -= radius;
				p = container.globalToLocal(p);
				arrow.showAt(p.x, p.y, container);
				
				if (tip && step.tip) tip.show(step.tip);
			}
		}
		
		/**
		 * 显示高亮
		 */
		public function showHighlighting():void
		{
			if (!step || !needShowMasker) return;
			
			var actTips:*;
			var container:DisplayObjectContainer;
			try {
				actTips = step.actTips();
				container = step.container();
			}catch (err:Error) { showErr(err); }
			
			if (actTips && container)
			{
				var p:Point;
				var radius:int;
				if (actTips is DisplayObject)
				{
					var rect:Rectangle = DisplayObject(actTips).getRect(stage);
					p = new Point(rect.x + rect.width / 2, rect.y + rect.height / 2);
					radius = step.radius > 0 ? step.radius : Math.min(rect.width, rect.height) / 2;
				}else if(("x" in actTips) && ("y" in actTips))
				{
					p = new Point(actTips.x, actTips.y);
					radius = step.radius;
				}
				masker.showHasCircle(p.x, p.y, radius);
			}
		}
		
		private function showDialog():void 
		{
			if (dialogList && dialogList.length > 0)
			{
				GuideDialogManager.getInstance().showDialogs(dialogList, stage, dialogEndFun);
			}else
			{
				dialogEndFun(dialogList);
			}
		}
		
		private function dialogEndFun(dialogList:Vector.<DialogVo>):void
		{
			if(dialogList == this.dialogList) showArrow();
		}
		
		//private function finishHandler(e:GuideEvent):void 
		//{
			//GuideManager.getInstance().removeEventListener(GuideEvent.ALL_STEP_FINISH, finishHandler);
			//GuideManager.getInstance().removeEventListener(GuideEvent.STEP_RESET, stepResetHandler);
			//clearStep();
		//}
		
	}

}

class Singleton { }