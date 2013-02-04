package happymagic.scene.world.control
{
	import com.friendsofed.isometric.Point3D;
    import flash.geom.*;

    public class AvatarCommand extends Object
    {
		//到达点后的朝向目标点
        public var faceTowards:Point3D;
		//目的地
        public var destination:Point3D;
		//回调函数
        private var moveCallback:Function;
		private var moveCallbackParams:Array;
		
		//朝向目标点后做的动画动作
        public var actionAnimation:String;
		//到达后表现动画时间
        public var actionDuration:Number;
        public var actionTimes:Number;//动画表现次数,与动画时间只取其一
		public var actionToStop:Boolean;//播放动画时是否只放一遍然后停止
		
		//动画结束回调
		public var actionCallback:Function;
		//强制走到目标点,就算那点不可行走
		public var mustGo:Boolean;
		//最终目标点为像素点,会走到目标点后再移动到指定像素坐标
		public var truePoint:Point;
		
		public var autoNext:Boolean = true; //完成此条后是否自动开始下一条command
		
		public var type:String;//标记command类型，现在只有标walk,表示是主角的行走操作,可能会在这时要清除之前所有command
		
		
		/**
		 * 
		 * @param	_destination	[Point3D] 格子坐标，目标位置
		 * @param	_moveCallback	完成后的回调方法
		 * @param	_faceTowards	到达后人物朝向位置
		 * @param	_actionDuration	到达后表现动画时间
		 * @param	_actionAnimation	动画标签
		 * @param	_actionCallback		最后动画表现完成后回调方法
		 * @param	_type		command类型,现只有walk
		 * @param	_mustGo		无视目标点的是否可行
		 * @param	_moveCallbackParams		移动完成后回调的参数
		 * @param	_truePoint		最终要走到的坐标,此坐标不是格子坐标,而是像素坐标
		 */
        public function AvatarCommand(_destination:Point3D=null, _moveCallback:Function = null, _faceTowards:Point3D = null, _actionDuration:Number = 0, _actionAnimation:String = null, _actionCallback:Function = null,_type:String="",_mustGo:Boolean=false,_moveCallbackParams:Array=null,_truePoint:Point=null)
        {
			type = _type;
			truePoint = _truePoint;
			
			destination = _destination;
            moveCallback = _moveCallback;
			moveCallbackParams = _moveCallbackParams;
			faceTowards = _faceTowards;
			
            actionAnimation = _actionAnimation;
            actionDuration = _actionDuration;
			actionCallback = _actionCallback;
			
			mustGo = _mustGo;
            return;
        }
		
		/**
		 * 设置移动
		 * @param	_destination	目标点,GRID坐标
		 * @param	_faceTowards	设置走到目标点后,朝向哪个点,GRID坐
		 * @param	_moveCallback	移动完成后回调
		 * @param	_callbackParams	移动完成后回调的参数
		 */
		public function setMovePos(_destination:Point3D,_faceTowards:Point3D=null,_moveCallback:Function = null,_callbackParams:Array=null):void {
			destination = _destination;
			faceTowards = _faceTowards;
			
            moveCallback = _moveCallback;
			moveCallbackParams = _callbackParams;
		}
		
		/**
		 * 设置动作动画 
		 * @param	_actionAnimation	动作动画标签
		 * @param	_actionDuration		动作动画时间,秒
		 * @param	_actionTimes		动作动画播放次数
		 * @param	_actionCallback		动作动画完成后的回调
		 */
		public function setAction(_actionAnimation:String = null,_actionDuration:Number = 0, _actionTimes:Number=0, _actionCallback:Function = null,_toStop:Boolean=false):void {
			actionAnimation = _actionAnimation;
            actionDuration = _actionDuration;
			actionCallback = _actionCallback;
			actionTimes = _actionTimes;
			actionToStop = _toStop;
		}
		
		/**
		 * 开始队列处理
		 */
        public function doIt() : void
        {
            if (moveCallback != null)
            {
				moveCallback.apply(null,moveCallbackParams);
            }
            return;
        }
		
		public function fiddleDoIt():void
		{
            if (actionCallback != null)
            {
                actionCallback();
            }
            return;
		}

    }
}
