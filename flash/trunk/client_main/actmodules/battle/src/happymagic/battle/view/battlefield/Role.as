package happymagic.battle.view.battlefield 
{
	import com.greensock.TweenLite;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.TimerEvent;
	import flash.filters.BitmapFilter;
	import flash.filters.ColorMatrixFilter;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.text.TextField;
	import flash.utils.Timer;
	import happyfish.cacher.SwfClassCache;
	import happyfish.events.SwfClassCacheEvent;
	import happymagic.battle.controller.MCController;
	import happymagic.model.vo.RoleVo;
	/**
	 * 参战角色
	 * @author XiaJunJie
	 */
	public class Role extends Sprite
	{
		public var vo:RoleVo; //数据
		public var view:MovieClip; //视图
		public var viewController:MCController;
		
		public var hpBar:HPBar;
		
		public var coverAnimLayer:Sprite; //覆盖动画层
		public var bottomAnimLayer:Sprite; //底部动画层
		
		private var animList:Object; //覆盖或底部动画列表
		private var filterList:Object; //滤镜列表
		
		private var _currentLabel:String = WAIT; //当前动画
		private var _currentPlayType:int = MCController.LOOP; //播放类型 参考MCController的静态常量
		
		private var _selectable:Boolean = true; //是否可选
		
		private var _rect:Rectangle;
		
		private var nameTxt:TextField;
		
		/**
		 * 构造
		 * @param	vo 角色数据
		 * @param	isEnemy 是否敌方角色
		 */
		public function Role(vo:RoleVo)
		{
			this.vo = vo;
			
			//初始化叠加动画的容器
			coverAnimLayer = new Sprite;
			bottomAnimLayer = new Sprite;
			animList = new Object;
			filterList = new Object;
			
			//加载素材
			var className:String = vo.className;
			if (vo.pos > 8) className += "_back";
			var cache:SwfClassCache = SwfClassCache.getInstance();
			if (cache.hasClass(className)) viewComplete(null,className);
			else 
			{
				cache.addEventListener(SwfClassCacheEvent.COMPLETE, viewComplete);
				cache.loadClass(className);
			}
		}
		
		private function viewComplete(event:SwfClassCacheEvent = null,clsName:String = null):void
		{
			var className:String;
			if (event)
			{
				className = vo.className;
				if (vo.pos > 8) className += "_back";
				if (event.className != className) return;
				event.target.removeEventListener(SwfClassCacheEvent.COMPLETE, viewComplete);
			}
			else className = clsName;
			
			var viewClass:Class = SwfClassCache.getInstance().getClass(className);
			view = new viewClass();
			addChild(view);
			viewController = new MCController(view,8);
			
			_rect = getBounds(this);
			
			hpBar = new HPBar;
			hpBar.setHpAndMaxHp(vo.hp, vo.maxHp);
			addChild(hpBar);
			hpBar.y = _rect.top;
			hpBar.visible = false;
			
			addChild(coverAnimLayer);
			addChildAt(bottomAnimLayer, 0);
			
			if (nameTxt){
				nameTxt.y = _rect.top - nameTxt.textHeight;
				view.addChild(nameTxt);
			}
			
			playAnimation();
			dispatchEvent(new Event(Event.COMPLETE));
		}
		
		/**
		 * 播放动画
		 * @param	label
		 */
		public function playAnimation(label:String = null, playType:int = MCController.STOP_AT_END):void
		{
			if (label != null)
			{
				_currentLabel = label;
				_currentPlayType = playType;
			}
			if (view) viewController.gotoAndPlay(_currentLabel,_currentPlayType);
		}
		
		/**
		 * 是否可选
		 * 不可选的话会变灰
		 */
		public function set selectable(value:Boolean):void
		{
			_selectable = value;
			if (value) this.filters = [];
			else this.filters = [grayFilter];
		}
		public function get selectable():Boolean
		{
			return _selectable;
		}
		
		/**
		 * 获得顶部中点在父对象中的坐标
		 */
		public function get top():Point
		{
			var result:Point = new Point;
			if (_rect)
			{
				result.x = (_rect.left + _rect.right) / 2;
				result.y = _rect.top;
			}
			result.x += x;
			result.y += y;
			return result;
		}
		
		/**
		 * 添加一个叠加动画 或者底部动画
		 * @param	anim : 动画
		 * @param	className : 素材名 在这里其实是用作ID
		 * @param	atBottom : 是否底部动画
		 */
		public function addAnimation(animation:Sprite, className:String, atBottom:Boolean = false):void
		{
			removeAnimation(className);
			
			if (atBottom) bottomAnimLayer.addChild(animation);
			else coverAnimLayer.addChild(animation);
			
			animList[className] = animation;
		}
		
		/**
		 * 移除一个叠加动画或底部动画
		 */
		public function removeAnimation(className:String):void
		{
			var animation:Sprite = animList[className];
			if (animation) animation.parent.removeChild(animation);
			delete animList[className];
		}
		
		public function addFilter(filter:BitmapFilter, filterType:int):void
		{
			removeFilter(filterType);
			
			var arr:Array = view.filters;
			arr.push(filter);
			view.filters = arr;
			filterList[filterType] = filter;
		}
		
		public function removeFilter(filterType:int):void
		{
			var filter:BitmapFilter = filterList[filterType] as BitmapFilter;
			if (filter)
			{
				delete filterList[filterType];
				
				var arr:Array = [];
				for each(filter in filterList) arr.push(filter);
				view.filters = arr;
			}
		}
		
		public function showName():void
		{
			nameTxt = new TextField;
			nameTxt.text = vo.name;
			nameTxt.x = -nameTxt.textWidth / 2;
			nameTxt.textColor = 0xFFFFFF;
		}
		
		public function clear():void
		{
			if (viewController) viewController.clear();
		}
		
		//常量------------------------------------
		public static const WAIT:String = "wait"; //常规
		public static const MOVE:String = "move"; //移动
		public static const BACK:String = "back"; //跳回
		public static const ATTACK:String = "atk"; //攻击
		public static const HITTED:String = "hit"; //被打
		public static const CASTMAGIC:String = "magic"; //使用魔法
		public static const SKILL:String = "skill"; //使用技能
		public static const DEFEND:String = "defense"; //防御
		public static const DODGE:String = "miss"; //闪躲
		public static const ESCAPE:String = "esc"; //逃跑
		public static const DEAD:String = "dead"; //倒下
		public static const WIN:String = "win"; //胜利
		public static const USEITEM:String = "item"; //物品使用
		public static const STEALITEM:String = "steal"; //偷取物品
		public static const THROWITEM:String = "throwing"; //投掷物品
		
		private static var grayFilter:ColorMatrixFilter = new ColorMatrixFilter(
															[0.33, 0.33, 0.33, 0, 0,
															 0.33, 0.33, 0.33, 0, 0,
															 0.33, 0.33, 0.33, 0, 0,
															 0, 0, 0, 1, 0]
															);
		
		public static const ROLE_HEIGHT:Number = 20;
	}

}