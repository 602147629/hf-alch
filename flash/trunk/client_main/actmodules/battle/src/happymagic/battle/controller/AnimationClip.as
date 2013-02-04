package happymagic.battle.controller 
{
	import com.greensock.TweenLite;
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.TimerEvent;
	import flash.filters.BitmapFilter;
	import flash.filters.ColorMatrixFilter;
	import flash.filters.GlowFilter;
	import flash.geom.Point;
	import flash.media.Sound;
	import flash.text.TextField;
	import flash.utils.Timer;
	import happyfish.cacher.SwfClassCache;
	import happyfish.events.SwfClassCacheEvent;
	import happyfish.manager.SoundEffectManager;
	import happyfish.utils.display.CameraSharkControl;
	import happyfish.utils.display.McShower;
	import happymagic.battle.Battle;
	import happymagic.battle.view.battlefield.BattleField;
	import happymagic.battle.view.battlefield.Role;
	import happymagic.battle.view.CritBg;
	import happymagic.battle.view.ui.CustomNumber;
	import happymagic.model.vo.ConditionVo;
	
	/**
	 * 动画剪辑
	 * 一套动画流程中的子动画
	 * @author XiaJunJie
	 */
	public class AnimationClip extends EventDispatcher
	{
		private var type:int; //类型
		
		private var role:Role; //要播放动画的角色
		
		private var label:String; //要播放的标签
		private var stopAtLabelEnd:Boolean; //播放完后是否停在标签的末尾
		
		private var target:Point; //要移动的位置
		private var jump:Boolean; //是否跳跃
		private var duration:Number; //历时
		
		private var battleField:BattleField; //战场容器 只有在播放战场叠加/底部动画时才会用到此项
		private var gridIndex:int; //战场中的某个位置索引 在播放战场叠加/底部动画时指明动画出现的位置
		
		private var className:String; //叠加动画或底部动画的className
		private var flyingItem:Sprite; //飞行道具
		
		private var customNumber:CustomNumber; //自定义数字 飘字用
		private var piaoType:int; //飘字方式
		
		private var container:Sprite; //容器 只有在播放物品掉落时才用到此项
		private var itemList:Vector.<ConditionVo>; //物品列表 只有在播放物品掉落时才用到此项
		private var dropFrom:Point; //物品掉落的起点
		
		private var filterType:int; //滤镜类型
		
		private var shockObj:DisplayObject; //震动对象
		private var shockTime:int; //震动时间
		
		private var word:TextField; //技能文字
		private var list:Array; //数组
		
		private var delay:int; //延迟 单位:毫秒
		private var times:int; //播放次数
		
		public var complete:Boolean; //是否已经完成 播放时用到的变量
		
		//辅助
		private var timer:Timer;
		private var moveController:MoveController;
		
		/**
		 * 将此对象设置为角色动画剪辑
		 * @param	role
		 * @param	label
		 * @param	times : 播放次数 0表示循环
		 * @param	delay
		 * @return
		 */
		public function setRole(role:Role, label:String, times:int, delay:int = 0, stopAtLabelEnd:Boolean = false):AnimationClip
		{
			this.type = ROLE;
			this.role = role;
			this.label = label;
			this.delay = delay;
			this.times = times;
			this.stopAtLabelEnd = stopAtLabelEnd;
			
			return this;
		}
		
		public function setMove(role:Role, target:Point, jump:Boolean = false, duration:Number = -1, delay:int = 0):AnimationClip
		{
			this.type = MOVE;
			this.role = role;
			this.target = target;
			this.jump = jump;
			this.duration = duration;
			this.delay = delay;
			
			return this;
		}
		
		public function setCoverAnim_Role(className:String, role:Role, times:int,delay:int):AnimationClip
		{
			this.type = COVER_ROLE;
			this.className = className;
			this.role = role;
			this.times = times;
			this.delay = delay;
			
			return this;
		}
		
		public function setBottomAnim_Role(className:String, role:Role, times:int,delay:int):AnimationClip
		{
			this.type = BOTTOM_ROLE;
			this.className = className;
			this.role = role;
			this.times = times;
			this.delay = delay;
			
			return this;
		}
		
		public function setCoverAnim_BattleField(className:String, battleField:BattleField, gridIndex:int, times:int,delay:int):AnimationClip
		{
			this.type = COVER_BATTLEFIELD;
			this.className = className;
			this.battleField = battleField;
			this.gridIndex = gridIndex;
			this.times = times;
			this.delay = delay;
			
			return this;
		}
		
		public function setBottomAnim_BattleField(className:String, battleField:BattleField, gridIndex:int, times:int,delay:int):AnimationClip
		{
			this.type = BOTTOM_BATTLEFIELD;
			this.className = className;
			this.battleField = battleField;
			this.gridIndex = gridIndex;
			this.times = times;
			this.delay = delay;
			
			return this;
		}
		
		public function setPiao(font:String, value:int, isCrit:Boolean, role:Role, piaoType:int = PIAOTYPE_UP, delay:int = 0):AnimationClip
		{
			this.type = PIAO;
			
			customNumber = new CustomNumber(font);
			customNumber.value = value;
			if (isCrit) customNumber.addChildAt(new CritBg, 0);
			
			this.role = role;
			this.piaoType = piaoType;
			this.delay = delay;
			
			return this;
		}
		
		public function setInvisible(role:Role):AnimationClip
		{
			this.type = INVISIBLE;
			
			this.role = role;
			
			return this;
		}
		
		public function setDropItem(itemList:Vector.<ConditionVo>, container:Sprite, from:Point, role:Role):AnimationClip
		{
			this.type = DROP_ITEM;
			this.container = container;
			this.itemList = itemList;
			this.dropFrom = from;
			this.role = role;
			
			return this;
		}
		
		public function setRoleFilter(role:Role, filterType:int, delay:int = 0):AnimationClip
		{
			this.type = ROLE_FILTER;
			this.role = role;
			this.filterType = filterType;
			this.delay = delay;
			
			return this;
		}
		
		public function setRemoveAnim_Role(role:Role, className:String, delay:int = 0):AnimationClip
		{
			this.type = REMOVE_ANIM_ROLE;
			this.role = role;
			this.className = className;
			this.delay = delay;
			
			return this;
		}
		
		public function setRemoveFilter_Role(role:Role, filterType:int, delay:int = 0):AnimationClip
		{
			this.type = REMOVE_FILTER_ROLE;
			this.role = role;
			this.filterType = filterType;
			this.delay = delay;
			
			return this;
		}
		
		public function setFlying(className:String, battleField:BattleField, role:Role, target:Point, jump:Boolean = false, delay:int = 0):AnimationClip
		{
			this.type = FLYING;
			this.className = className;
			this.battleField = battleField;
			this.role = role;
			this.target = target;
			this.jump = jump;
			this.delay = delay;
			
			return this;
		}
		
		public function setShock(shockObj:DisplayObject, shockTime:int, delay:int = 0):AnimationClip
		{
			this.type = SHOCK;
			this.shockObj = shockObj;
			this.shockTime = shockTime;
			this.delay = delay;
			
			return this;
		}
		
		public function setSound(className:String, delay:int = 0):AnimationClip
		{
			this.type = SOUND;
			this.className = className;
			this.delay = delay;
			
			return this;
		}
		
		public function setSkillWord(word:String, battleField:BattleField, delay:int = 0):AnimationClip
		{
			this.type = SKILL_WORD;
			this.word = new TextField;
			this.word.text = word;
			this.word.textColor = 0xFFFFFF;
			this.battleField = battleField;
			this.delay = delay;
			
			return this;
		}
		
		public function setSceneDark(battleField:BattleField, roles:Array, delay:int = 0):AnimationClip
		{
			this.type = SCENE_DARK;
			this.battleField = battleField;
			this.list = roles;
			this.delay = delay;
			
			return this;
		}
		
		public function setCancelSceneDark(battleField:BattleField, roles:Array, delay:int = 0):AnimationClip
		{
			this.type = CANCEL_SCENE_DARK;
			this.battleField = battleField;
			this.list = roles;
			this.delay = delay;
			
			return this;
		}
		
		/**
		 * 开始播放
		 */
		public function start():void
		{
			if (delay == 0) play();
			else
			{
				timer = new Timer(delay,1);
				timer.addEventListener(TimerEvent.TIMER_COMPLETE, play);
				timer.start();
			}
		}
		
		private function play(event:TimerEvent = null):void
		{
			if (event)
			{
				timer.stop();
				timer.removeEventListener(TimerEvent.TIMER_COMPLETE, play);
				timer = null;
			}
			
			complete = false;
			var container:Sprite;
			
			if (type == ROLE) //播放角色动画
			{
				if (label == Role.WAIT)
				{
					role.playAnimation(Role.WAIT, MCController.LOOP);
					done();
				}
				else  if (times != 0)
				{
					role.playAnimation(label);
					role.view.addEventListener(Event.COMPLETE, playLabelOneTimesComplete);
				}
				else
				{
					role.playAnimation(label, MCController.LOOP);
					done();
				}
			}
			else if (type == MOVE) //移动角色
			{
				if (duration == -1)
				{
					duration = Point.distance(target, new Point(role.x, role.y)) / 280;
					if (duration < 0.35) duration = 0.35;
				}
				
				moveController = new MoveController;
				moveController.addEventListener(Event.COMPLETE, done);
				if (jump) moveController.jump(role, target, -0.5, duration);
				else moveController.translate(role, target, duration);
			}
			else if (type == COVER_ROLE || type == BOTTOM_ROLE || type == COVER_BATTLEFIELD || type == BOTTOM_BATTLEFIELD) //播放叠加动画或底部动画
			{
				loadAnimation(className);
				if (times == 0) done(); //循环播放
			}
			else if (type == PIAO) //飘字
			{
				role.parent.addChild(customNumber);
				var top:Point = role.top;
				customNumber.x = top.x;
				customNumber.y = top.y;
				
				var vars:Object;
				customNumber.alphaT = 0;
				customNumber.resetDropT();
				if (piaoType == PIAOTYPE_UP)
				{
					vars = { "alphaT":1, "y":customNumber.y - CustomNumber.height_Up, onComplete:onPiaoComplete };
				}
				else if (piaoType == PIAOTYPE_DROP)
				{
					vars = { "alphaT":1, "dropT":1 , onComplete:onPiaoComplete };
				}
				else done();
				
				TweenLite.to(customNumber, CustomNumber.piaoDuration, vars);
				
				//顺便把血条设置下
				role.hpBar.visible = true;
				role.hpBar.setHpAndMaxHp(role.vo.hp, role.vo.maxHp);
				
				done();
			}
			else if (type == INVISIBLE) //渐隐
			{
				//TweenLite.to(role, 1, { alpha:0, onComplete:done } );
				TweenLite.to(role, 1, { alpha:0} );
				done();
			}
			else if (type == DROP_ITEM) //物品掉落
			{
				var itemDropController:ItemDropController = new ItemDropController(this.container);
				//itemDropController.addEventListener(Event.COMPLETE, done);
				itemDropController.drop(itemList, dropFrom, role);
				done();
			}
			else if (type == ROLE_FILTER) //角色滤镜
			{
				if (filterType == GRAY_FILTER) role.addFilter(grayFilter,GRAY_FILTER); //灰色滤镜
				else if (filterType == RED_FILTER) role.addFilter(redFilter,RED_FILTER); //红色滤镜
				else if (filterType == GREEN_FILTER) role.addFilter(greenFilter,GREEN_FILTER); //绿色滤镜
				//...不断扩展
				done();
			}
			else if (type == REMOVE_ANIM_ROLE) //移除角色动画
			{
				role.removeAnimation(className);
				done();
			}
			else if (type == REMOVE_FILTER_ROLE) //移除角色滤镜
			{
				role.removeFilter(filterType);
				done();
			}
			else if (type == FLYING) //飞向道具
			{
				loadAnimation_Flying(className);
			}
			else if (type == SHOCK) //震动动画
			{
				CameraSharkControl.shark(shockObj, 3, shockTime, done);
			}
			else if (type == SOUND) //播放声音
			{
				SoundEffectManager.getInstance().playSound(className);
				done();
			}
			else if (type == SKILL_WORD) //技能文字展现
			{
				word.x = - word.width;
				word.y = (battleField.height - word.height) / 2;
				battleField.coverAnimLayer.addChild(word);
				TweenLite.to(word, 0.5, { x:(Battle.BG_WIDTH - word.width) / 2,onComplete:onSkillWordPhase1Complete} );
			}
			else if (type == SCENE_DARK) //场景变暗
			{
				battleField.addFilter(darkFilter, DARK_FILTER);
				for each(role in list)
				{
					if (role) role.addFilter(darkFilter, DARK_FILTER);
				}
				done();
			}
			else if (type == CANCEL_SCENE_DARK) //场景变回
			{
				battleField.removeFilter(DARK_FILTER);
				for each(role in list)
				{
					if (role) role.removeFilter(DARK_FILTER);
				}
				done();
			}
		}
		
		//播放角色动画 一次动画播放完毕
		private function playLabelOneTimesComplete(event:Event):void
		{
			times --;
			if (times == 0)
			{
				role.view.removeEventListener(Event.COMPLETE, playLabelOneTimesComplete);
				if (stopAtLabelEnd) role.viewController.stop();
				else role.playAnimation(Role.WAIT, MCController.LOOP);
				done();
			}
			else role.playAnimation(label);
		}
		
		private function onPiaoComplete():void
		{
			customNumber.parent.removeChild(customNumber);
			role.hpBar.visible = false;
		}
		
		private function done(event:Event = null):void
		{
			if (type == MOVE) moveController.removeEventListener(Event.COMPLETE, done);
			else if (type == FLYING)
			{
				moveController.removeEventListener(Event.COMPLETE, done);
				if(flyingItem.parent) flyingItem.parent.removeChild(flyingItem);
			}
			
			complete = true;
			dispatchEvent(new Event(Event.COMPLETE));
		}
		
		//加载动画--------------------------------------------
		
		private function loadAnimation(className:String):void
		{
			SwfClassCache.getInstance().addEventListener(SwfClassCacheEvent.COMPLETE, classGetted);
			SwfClassCache.getInstance().loadClass(className);
		}
		
		private function classGetted(event:SwfClassCacheEvent):void
		{
			if (event.className==className) 
			{
				SwfClassCache.getInstance().removeEventListener(SwfClassCacheEvent.COMPLETE,classGetted);
				
				var mcClass:Class=SwfClassCache.getInstance().getClass(className);
				var mc:MovieClip = new mcClass() as MovieClip;
				mc.gotoAndStop(1);
				mc.addEventListener(Event.ENTER_FRAME, onCoverAnimEnterFrame);
				mc.addEventListener(Event.REMOVED_FROM_STAGE, onCoverAnimRemoveFromStage);
				
				if (type == COVER_ROLE) role.addAnimation(mc,className);
				else if (type == BOTTOM_ROLE) role.addAnimation(mc, className, true);
				else
				{
					var coord:Point = battleField.getGridCoord(gridIndex);
					mc.x = coord.x;
					mc.y = coord.y;
					if (type == COVER_BATTLEFIELD) battleField.addAnimation(mc, className);
					else if (type == BOTTOM_BATTLEFIELD) battleField.addAnimation(mc, className, true);
				}
			}
		}
		
		private var coverAnimFrameCount:int = 0;
		private function onCoverAnimEnterFrame(event:Event):void
		{
			if (coverAnimFrameCount < 2) coverAnimFrameCount++;
			else
			{
				coverAnimFrameCount = 0;
				var mc:MovieClip = event.target as MovieClip;
				if (mc.currentFrame == mc.totalFrames - 1)
				{
					times --;
					if (times <= 0)
					{
						if (type == COVER_ROLE || type == BOTTOM_ROLE) role.removeAnimation(className);
						else if (type == COVER_BATTLEFIELD || type == BOTTOM_BATTLEFIELD) battleField.removeAnimation(className);
						done();
					}
					else mc.gotoAndStop(1);
				}
				else mc.nextFrame();
			}
		}
		
		private function onCoverAnimRemoveFromStage(event:Event):void
		{
			event.target.removeEventListener(Event.ENTER_FRAME, onCoverAnimEnterFrame);
			event.target.removeEventListener(Event.REMOVED_FROM_STAGE, onCoverAnimRemoveFromStage);
		}
		
		private function loadAnimation_Flying(className:String):void
		{
			SwfClassCache.getInstance().addEventListener(SwfClassCacheEvent.COMPLETE, classGetted_Flying);
			SwfClassCache.getInstance().loadClass(className);
		}
		
		private function classGetted_Flying(event:SwfClassCacheEvent):void
		{
			if (event.className==className) 
			{
				SwfClassCache.getInstance().removeEventListener(SwfClassCacheEvent.COMPLETE,classGetted_Flying);
				
				var spClass:Class=SwfClassCache.getInstance().getClass(className);
				flyingItem = new spClass() as Sprite;
				battleField.coverAnimLayer.addChild(flyingItem);
				flyingItem.x = role.x;
				flyingItem.y = role.y - Role.ROLE_HEIGHT;
				
				var duration:Number = Point.distance(target, new Point(role.x, role.y)) / 280;
				if (duration < 0.35) duration = 0.35;
				
				moveController = new MoveController;
				moveController.addEventListener(Event.COMPLETE, done);
				if (jump) moveController.jump(flyingItem, target, -0.5, duration);
				else moveController.translate(flyingItem, target, duration/2);
				
				var arr:Array = className.split("_");
				if (arr.pop() == "dir")
				{
					var dx:Number = target.x - flyingItem.x;
					var dy:Number = target.y - flyingItem.y;
					var len:Number = Math.sqrt(dx * dx + dy * dy);
					var ang:Number = Math.acos(dx / len);
					if (dy < 0) ang = - ang;
					ang = ang / Math.PI * 180;
					flyingItem.rotation = ang;
				}
			}
		}
		
		//技能飞字----------------------------------------
		private function onSkillWordPhase1Complete():void
		{
			var skillWordTimer:Timer = new Timer(1000, 1);
			skillWordTimer.addEventListener(TimerEvent.TIMER_COMPLETE, onSkillWordPhase2Complete);
			skillWordTimer.start();
		}
		
		private function onSkillWordPhase2Complete(event:TimerEvent):void
		{
			event.target.removeEventListener(TimerEvent.TIMER_COMPLETE, onSkillWordPhase2Complete);
			TweenLite.to(word, 0.5, { x:Battle.BG_WIDTH, onComplete:done } );
		}
		
		//滤镜--------------------------------------------
		//灰色
		private static var _grayFilter:ColorMatrixFilter;
		public static function get grayFilter():ColorMatrixFilter
		{
			if (!_grayFilter)
			{
				var matrix:Array = new Array;
				matrix = matrix.concat([1/3, 1/3, 1/3, 0, 0]); // red
				matrix = matrix.concat([1/3, 1/3, 1/3, 0, 0]); // green
				matrix = matrix.concat([1/3, 1/3, 1/3, 0, 0]); // blue
				matrix = matrix.concat([0, 0, 0, 1, 0]); // alpha
				_grayFilter = new ColorMatrixFilter(matrix);
			}
			return _grayFilter;
		}
		
		//绿色
		private static var _greenFilter:ColorMatrixFilter;
		public static function get greenFilter():ColorMatrixFilter
		{
			if (!_greenFilter)
			{
				var matrix:Array = new Array;
				matrix = matrix.concat([1, 0, 0, 0, 0]); // red
				matrix = matrix.concat([0, 2, 0, 0, 0]); // green
				matrix = matrix.concat([0, 0, 1, 0, 0]); // blue
				matrix = matrix.concat([0, 0, 0, 1, 0]); // alpha
				_greenFilter = new ColorMatrixFilter(matrix);
			}
			return _greenFilter;
		}
		
		//红色
		private static var _redFilter:ColorMatrixFilter;
		public static function get redFilter():ColorMatrixFilter
		{
			if (!_redFilter)
			{
				var matrix:Array = new Array;
				matrix = matrix.concat([2, 0, 0, 0, 0]); // red
				matrix = matrix.concat([0, 1, 0, 0, 0]); // green
				matrix = matrix.concat([0, 0, 1, 0, 0]); // blue
				matrix = matrix.concat([0, 0, 0, 1, 0]); // alpha
				_redFilter = new ColorMatrixFilter(matrix);
			}
			return _redFilter;
		}
		
		//高亮
		private static var _highLightFilter:ColorMatrixFilter;
		public static function get highLightFilter():ColorMatrixFilter
		{
			if (!_highLightFilter)
			{
				var matrix:Array = new Array;
				matrix = matrix.concat([2, 0, 0, 0, 0]); // red
				matrix = matrix.concat([0, 2, 0, 0, 0]); // green
				matrix = matrix.concat([0, 0, 2, 0, 0]); // blue
				matrix = matrix.concat([0, 0, 0, 1, 0]); // alpha
				_highLightFilter = new ColorMatrixFilter(matrix);
			}
			return _highLightFilter;
		}
		
		//变暗滤镜
		private static var _darkFilter:ColorMatrixFilter;
		public static function get darkFilter():ColorMatrixFilter
		{
			if (!_darkFilter)
			{
				var matrix:Array = new Array;
				matrix = matrix.concat([0.5, 0, 0, 0, 0]); // red
				matrix = matrix.concat([0, 0.5, 0, 0, 0]); // green
				matrix = matrix.concat([0, 0, 0.5, 0, 0]); // blue
				matrix = matrix.concat([0, 0, 0, 1, 0]); // alpha
				_darkFilter = new ColorMatrixFilter(matrix);
			}
			return _darkFilter;
		}
		
		//常量--------------------------------------------
		public static const ROLE:int = 1; //播放角色动画
		public static const MOVE:int = 2; //移动角色
		public static const COVER_ROLE:int = 3; //角色叠加动画
		public static const BOTTOM_ROLE:int = 4; //角色底部动画
		public static const COVER_BATTLEFIELD:int = 5; //战场叠加动画
		public static const BOTTOM_BATTLEFIELD:int = 6; //战场底部动画
		public static const PIAO:int = 7; //飘字
		public static const FLYING:int = 8; //飞行道具
		public static const ROLE_FILTER:int = 9; //角色滤镜
		public static const SHOCK:int = 10; //震动
		public static const SOUND:int = 11; //声音
		public static const REMOVE_ANIM_ROLE:int = 21; //移除角色身上的覆盖动画或底部动画
		public static const REMOVE_FILTER_ROLE:int = 22; //移除角色身上的滤镜
		public static const INVISIBLE:int = 30; //渐隐
		public static const DROP_ITEM:int = 31; //物品掉落
		public static const SKILL_WORD:int = 32; //技能文字
		public static const SCENE_DARK:int = 33; //场景变暗
		public static const CANCEL_SCENE_DARK:int = 34; //场景变回
		
		public static const PIAOTYPE_UP:int = 101; //向上淡出
		public static const PIAOTYPE_DROP:int = 102; //掉落淡出
		
		public static const GRAY_FILTER:int = 201; //灰色滤镜
		public static const RED_FILTER:int = 202; //红色滤镜
		public static const GREEN_FILTER:int = 203; //绿色滤镜
		public static const HIGHTLIGHT_FILTER:int = 299; //高亮滤镜
		public static const DARK_FILTER:int = 298; //变暗滤镜
	}

}