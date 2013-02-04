package happyfish.utils.display 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.utils.getDefinitionByName;
	import flash.utils.getQualifiedClassName;
	import happyfish.cacher.SwfClassCache;
	import happyfish.events.SwfClassCacheEvent;
	import happymagic.manager.DisplayManager;
	/**
	 * ...
	 * @author jj
	 */
	public class McShower
	{
		private var callBackParmas:Array;
		public var mc:MovieClip;
		private var container:DisplayObjectContainer;
		private var callBack:Function;
		private var type:*;
		private var autoRemove:Boolean;
		private var className:String;
		private var label_actions:Object;
		private var currentLabel:String;
		private var _play:Boolean=true;
		public var playing:Boolean;
		
		
		//还需播放多少次
		private var playTimes:int;
		
		private var curDelay:int = 0;
		public var delay:int=0;
		
		/**
		 * 
		 * @param	mcClass
		 * @param	_container
		 * @param	_label_actions	播放到某标签时调用的方法 例:{label1:label1Func,label2:label2Func}
		 * @param	_type 播放到位置,可传帧数\标签名\null表示播放完全
		 * @param	_callBack
		 * @param	_callBackParmas
		 * @param	_autoRemove		播放完成后是否自动移除,如不移除会停在最后一帧
		 * @param	__play			是否在创建完动画后立即开始播放
		 * @param	__playTimes		播放的次数,-1为无限播放,默认为播放1次
		 */
		public function McShower(mcClass:*,_container:DisplayObjectContainer,_label_actions:Object=null, _type:*= null,_callBack:Function=null,_callBackParmas:Array=null,_autoRemove:Boolean=true,__play:Boolean=true,__playTimes:int=1) 
		{
			container = _container;
			callBack = _callBack;
			callBackParmas = _callBackParmas;
			type = _type;
			_play = __play;
			playTimes = __playTimes;
			autoRemove = _autoRemove;
			if (_label_actions) 
			{
				label_actions = _label_actions;
			}
			
			if (mcClass is String) 
			{
				className = mcClass;
				loadClass();
			}else {
				className = getQualifiedClassName(mcClass);
				createMc();
			}
		}
		
		private function loadClass():void
		{
			if (!className) 
			{
				trace("class不存在");
				return;
			}
			SwfClassCache.getInstance().addEventListener(SwfClassCacheEvent.COMPLETE, classGeted);
			SwfClassCache.getInstance().loadClass(className);
		}
		
		private function classGeted(e:SwfClassCacheEvent):void 
		{
			if (e.className==className) 
			{
				SwfClassCache.getInstance().removeEventListener(SwfClassCacheEvent.COMPLETE,classGeted);
				
				createMc();
			}
		}
		
		private function createMc():void
		{
			var mcClass:Class=SwfClassCache.getInstance().getClass(className);
			mc = new mcClass() as MovieClip;
			if (_play) 
			{
				startPlay();
			}
		}
		
		public function startPlay():void {
			if (!playing) 
			{
				playing = true;
				container.addChild(mc);
				
				mc.addEventListener(Event.ENTER_FRAME, playMovieFun);
			}
		}
		
		private function playMovieFun(e:Event):void {
			
			if (curDelay<delay) 
			{
				curDelay++;
				return;
			}
			
			mc.nextFrame();
			curDelay = 0;
			
			var target:MovieClip = e.target as MovieClip;
			
			currentLabel = target.currentLabel;
			
			if (type==null) 
			{
				if (target.currentFrame==target.totalFrames) 
				{
					playEnd(target);
				}
				return;
			}
			
			if (type is Number) 
			{
				if (target.currentFrame==type) 
				{
					playEnd(target);
				}
				return;
			}
			
			if (type is String) 
			{
				if (target.currentLabel==type) 
				{
					playEnd(target);
				}
				return;
			}
			
			
		}
		
		private function playEnd(target:MovieClip):void {
			
			
			if (playTimes<0) 
			{
				return;
			}
			
			playTimes--;
			if(playTimes==0){
				
			}else {
				return;
			}
			
			stopPlay();
			
			playing = false;
			
			if (autoRemove) 
			{
				removeMe();
			}
			
			if (callBack!=null) 
			{
				callBack.apply(null,callBackParmas);
				callBack = null;
			}
			
		}
		
		private function stopPlay():void 
		{
			if (mc) 
			{
				if (mc.hasEventListener(Event.ENTER_FRAME)) 
				{
					mc.removeEventListener(Event.ENTER_FRAME, playMovieFun);
				}
			}
			
		}
		
		public function removeMe():void
		{
			if (SwfClassCache.getInstance().hasEventListener(SwfClassCacheEvent.COMPLETE)) 
			{
				SwfClassCache.getInstance().removeEventListener(SwfClassCacheEvent.COMPLETE, classGeted);
			}
			
			stopPlay();
			if (mc) 
			{
				mc.parent.removeChild(mc);
				mc = null;
			}
		}
		
		public function get x():Number { return mc.x; }
		
		public function set x(value:Number):void 
		{
			mc.x = value;
		}
		
		public function get y():Number { return mc.y; }
		
		public function set y(value:Number):void 
		{
			mc.y = value;
		}
		
		public function get mouseEnabled():Boolean { return mc.mouseEnabled; }
		
		public function set mouseEnabled(value:Boolean):void 
		{
			mc.mouseEnabled 
			mc.mouseChildren = value;
		}
		
		public function get play():Boolean 
		{
			return _play;
		}
		
		public function set play(value:Boolean):void 
		{
			_play = value;
		}
		
		//设置缩放大小
		public function setMcScaleXY(Sx:Number,Sy:Number):void
		{
			mc.scaleX = Sx;
			mc.scaleY = Sy;
		}
	}

}