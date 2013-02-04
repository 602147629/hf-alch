package happymagic.battle.controller 
{
	import com.greensock.TweenLite;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.geom.Point;
	import flash.utils.Timer;
	import happyfish.display.view.IconView;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class ItemDropController extends EventDispatcher 
	{
		private var container:Sprite;
		
		private var targetPoint:Point;
		private var targetSprite:Sprite;
		private var from:Point;
		private var w:int;
		private var h:int;
		
		private var timer:Timer;
		private var iconList:Vector.<IconView>;
		
		private var _isDroping:Boolean;
		private var remain:int;
		
		public function ItemDropController(container:Sprite)
		{
			this.container = container;
		}
		
		/**
		 * 爆出物品
		 * @param	list : 物品列表
		 * @param	from : 从哪里掉出
		 * @param	to : 最后飞向哪里 可以是Point或Sprite 也可以是null 表示不会飞
		 * @param	w : icon的宽
		 * @param	h : icon的高
		 */
		public function drop(list:Vector.<ConditionVo>, from:Point, to:*, w:int = 30, h:int = 30):void
		{
			if (list.length == 0)
			{
				dispatchEvent(new Event(Event.COMPLETE));
				return;
			}
			
			this.from = from;
			this.targetPoint = to as Point;
			this.targetSprite = to as Sprite;
			this.w = w;
			this.h = h;
			
			remain = 0;
			iconList = new Vector.<IconView>();
			
			for each(var conditionVo:ConditionVo in list)
			{
				//获得className
				var className:String;
				if (conditionVo.type==ConditionType.ITEM) //物品
				{
					className = DataManager.getInstance().itemData.getItemClass(Number(conditionVo.id)).className;
					
					for (var i:int = 0; i < conditionVo.num; i++) create(className);
				}
				else if (conditionVo.type == ConditionType.USER)
				{
					className = "awardIcon_";
					if (conditionVo.id ==  ConditionType.USER_COIN) //金币
					{
						if (conditionVo.num < 10) className += "1_2";
						else if (conditionVo.num<100) className += "1_2";
						else className += "1_3";
					}
					else if(conditionVo.id == ConditionType.USER_GEM) className += "2"; //宝石
					else if (conditionVo.id == ConditionType.USER_EXP) className += "3"; //经验
					
					create(className);
				}
			}
			
			_isDroping = true;
		}
		
		private function create(className:String):void
		{
			//创建图标
			var iconView:IconView = new IconView(w, h);
			iconView.setData(className);
			container.addChild(iconView);
			iconView.x = from.x;
			iconView.y = from.y;
			
			iconList.push(iconView);
			
			//计算目标点
			var target:Point = new Point;
			target.x = from.x + (Math.random() - 0.5) * 300;
			target.y = from.y + (Math.random() - 0.5) * 300;
			
			var moveController:MoveController = new MoveController;
			moveController.drop(iconView, target, -1, 3, 1);
			moveController.addEventListener(Event.COMPLETE, onComplete);
			
			remain ++;
		}
		
		private function onComplete(event:Event):void
		{
			event.target.removeEventListener(Event.COMPLETE, onComplete);
			
			var needToGotoTarget:Boolean = targetPoint != null || targetSprite != null;
			
			if(needToGotoTarget) (event.target as MoveController).object.addEventListener(MouseEvent.ROLL_OVER, onIconRollOver);
			
			remain --;
			if (remain <= 0)
			{
				if (needToGotoTarget)
				{
					timer = new Timer(delay, 1);
					timer.addEventListener(TimerEvent.TIMER_COMPLETE, onTimerComplete);
					timer.start();
				}
				_isDroping = false;
				dispatchEvent(new Event(Event.COMPLETE));
			}
		}
		
		//当掉在地上的物品被鼠标滑过后 飞向目标
		private function onIconRollOver(event:MouseEvent):void
		{
			gotoTarget(event.target as IconView);
		}
		
		//时间到 剩余物品自动飞向目标
		private function onTimerComplete(event:TimerEvent):void
		{
			event.target.removeEventListener(TimerEvent.TIMER_COMPLETE, onTimerComplete);
			
			for each(var iconView:IconView in iconList) gotoTarget(iconView);
		}
		
		//飞向目标
		private function gotoTarget(iconView:IconView):void
		{
			iconView.removeEventListener(MouseEvent.ROLL_OVER, onIconRollOver);
			
			var index:int = iconList.indexOf(iconView);
			if (index != -1) iconList.splice(index, 1);
			else return;
			
			var point:Point = targetPoint;
			if (!point) point = new Point(targetSprite.x, targetSprite.y);
			
			TweenLite.to(iconView, 0.5, { x:point.x, y:point.y, onComplete:onGotoTargetComplete, onCompleteParams:[iconView] } );
		}
		
		//飞到后消失
		private function onGotoTargetComplete(iconView:IconView):void
		{
			iconView.parent.removeChild(iconView);
		}
		
		//是否正在暴出
		public function get isDroping():Boolean
		{
			return _isDroping;
		}
		
		//常量------------------------------------------
		private const delay:int = 7000; //多少时间后自动飞向目标
		
	}
}