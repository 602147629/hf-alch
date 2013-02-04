package happymagic.battle.controller
{
	import com.greensock.easing.Linear;
	import com.greensock.TweenLite;
	import flash.display.DisplayObject;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.geom.Point;

	/**
	 * 移动控制器
	 * 可以控制显示对象进行平移和跳跃
	 * @author XiaJunJie
	 */
	public class MoveController extends EventDispatcher
	{
		public var object:DisplayObject; //移动对象
		
		//辅助
		private var _isMoving:Boolean = false; //是否正在跳跃
		
		/**
		 * 平移
		 * @param object: 平移的对象
		 * @param target: 平移的目标点(相对于对象的父容器)
		 * @param duration: 整个过程历时多少时间 单位:秒
		 * @param interrupt: 是否打断正在进行的移动
		 */
		public function translate(object:DisplayObject, target:Point, duration:Number = 0.5, interrupt:Boolean = false):void
		{
			if (_isMoving)
			{
				if (interrupt) TweenLite.killTweensOf(this.object);
				else return;
			}
			
			TweenLite.to(object, duration, { x:target.x, y:target.y, onComplete:onComplete, ease:Linear.easeNone } );
			_isMoving = true;
		}
		
		/**
		 * 跳跃
		 * @param object: 跳跃的对象
		 * @param target: 跳跃的目标点(相对于对象的父容器)
		 * @param height: 跳跃的高度 如果为负的小数 则取起点和终点距离的小数倍
		 * @param duration: 整个过程历时多少时间 单位:秒
		 * @param interrupt: 是否打断正在进行的移动
		 */ 
		public function jump(object:DisplayObject, target:Point, height:Number = -1, duration:Number = 0.5, interrupt:Boolean = true):void
		{
			if (_isMoving)
			{
				if (interrupt) TweenLite.killTweensOf(this.object);
				else return;
			}
			
			this.object = object;
			
			//计算起点、终点和控制点
			var p0:Point = new Point(object.x,object.y); //起点
			var p2:Point = target; //终点
			var p1:Point = Point.interpolate(p0,p2,0.5); //控制点
			if(height<0)
			{
				var len:Number = Point.distance(p0,p2);
				p1.y += height*len;
			}
			else p1.y -= height;
			
			//开始跳跃
			TweenLite.to(object, duration, { bezier:[{x:p1.x, y:p1.y}, {x:p2.x, y:p2.y}], onComplete:onComplete, ease:Linear.easeNone} ); //开始缓动
			_isMoving = true;
		}
		
		/**
		 * 掉落
		 * @param object: 掉落的对象
		 * @param target: 掉落的目标点(相对于对象的父容器)
		 * @param jumpHeight: 弹跳的高度 如果为负的小数 则取起点和终点距离的小数倍
		 * @param jumpTimes: 弹跳的次数 每次弹起 高度都会衰减到之前的一半
		 * @param duration: 整个过程历时多少时间 单位:秒
		 * @param interrupt: 是否打断正在进行的移动
		 */
		public function drop(object:DisplayObject, target:Point, jumpHeight:Number = -1, jumpTimes:int = 3, duration:Number = 0.5, interrupt:Boolean = true):void
		{
			if (_isMoving)
			{
				if (interrupt) TweenLite.killTweensOf(this.object);
				else return;
			}
			
			this.object = object;
			this.jumpHeight = jumpHeight;
			dropTargets = new Vector.<Point>();
			dropDuration = new Vector.<Number>();
			
			//计算起点、终点和中间目标点
			var p0:Point = new Point(object.x,object.y); //起点
			var p2:Point = target; //终点
			
			var totalWeight:Number = 0;
			var currentWeight:Number = 1;
			
			for (var i:int = 0; i < jumpTimes; i++)
			{
				totalWeight += currentWeight;
				currentWeight = currentWeight / 2;
			}
			currentWeight = 1;
			for (i = 0; i < jumpTimes; i++)
			{
				var point:Point = Point.interpolate(p2, p0, currentWeight / totalWeight);
				if (i != 0)
				{
					point = point.subtract(p0);
					point = point.add(dropTargets[dropTargets.length - 1]);
				}
				dropTargets.push(point);
				
				currentWeight = currentWeight / 2;
			}
			
			//计算时间
			currentWeight = 1;
			for (i = 0; i < jumpTimes; i++)
			{
				dropDuration.push(duration * currentWeight / totalWeight);
				currentWeight = currentWeight / 2;
			}
			
			//开始跳跃
			dropJump();
			_isMoving = true;
		}
		
		private var dropTargets:Vector.<Point>;
		private var dropDuration:Vector.<Number>;
		private var jumpHeight:Number;
		
		private function dropJump():void
		{
			if (dropTargets.length == 0) onComplete();
			else
			{
				var target:Point = dropTargets.shift();
				var now:Point = new Point(object.x, object.y);
				
				var control:Point = Point.interpolate(now, target, 0.5);
				if (jumpHeight < 0) control.y += jumpHeight * Point.distance(target,now);
				else control.y -= jumpHeight;
				
				jumpHeight = jumpHeight / 2;
				
				TweenLite.to(object, dropDuration.shift(), { bezier:[{x:control.x, y:control.y}, {x:target.x, y:target.y}], onComplete:dropJump, ease:Linear.easeNone} ); //开始缓动
			}	
		}
		
		private function onComplete():void
		{
			_isMoving = false;
			dispatchEvent(new Event(Event.COMPLETE));
		}
		
		/**
		 * 是否正在移动
		 * @return
		 */
		public function isMoving():Boolean
		{
			return _isMoving;
		}
		
	}
}