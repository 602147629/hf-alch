package happymagic.battle.view.battlefield 
{
	import flash.display.DisplayObject;
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.filters.BitmapFilter;
	import flash.filters.ColorMatrixFilter;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.utils.Timer;
	import happymagic.battle.Battle;
	import happymagic.battle.controller.AnimationClip;
	import happymagic.battle.controller.MoveController;
	import happymagic.battle.view.ArrowUI;
	import happymagic.battle.view.GridUI;
	import happymagic.battle.view.SelectedGridUI;
	import happymagic.model.vo.RoleVo;
	
	/**
	 * 战场
	 * 战斗模块的场景容器 注册点在左上
	 * @author XiaJunJie
	 */
	public class BattleField extends Sprite
	{
		//显示结构
		private var bg:Background; //背景
		private var gridLayer:Sprite; //格子层
		public var bottomAnimLayer:Sprite; //底部动画层
		private var roleLayer:Sprite; //角色层
		public var coverAnimLayer:Sprite; //覆盖动画层
		private var uiLayer:Sprite; //UI层 包括角色的名字、血条 以及箭头等
		
		//其他显示对象
		private var selectSign_Grid:SelectedGridUI; //被选择对象脚下的地形块
		private var selectSign_Arrow:ArrowUI; //被选择对象头上的箭头
		
		public var mainRole:Role; //主角
		public var boss:Role; //BOSS
		
		//辅助
		private var animList:Object = new Object; //动画列表
		private var filterList:Object = new Object; //滤镜列表
		
		private var moveSelectSign_Grid:MoveController; //会动的格子的移动控制器
		private var moveSelectSign_Arrow:MoveController; //箭头的移动控制器
		
		private var mouseObject:DisplayObject; //当前鼠标所在的显示对象
		private var mousePoint:Point = new Point; //临时存放鼠标相对于舞台的位置
		
		private var sortTimer:Timer; //排序计时器
		
		/**
		 * 构造
		 */
		public function BattleField() 
		{	
			//初始化背景
			bg = new Background;
			addChild(bg);
			
			//初始化Mask
			var shape:Shape = new Shape;
			with (shape.graphics)
			{
				beginFill(0);
				drawRect( 0, 0, Battle.BG_WIDTH, Battle.BG_HEIGHT);
				endFill();
			}
			addChild(shape);
			this.mask = shape;
			
			//初始化格子层
			gridLayer = new Sprite();
			for (var i:int = 0; i < 18; i++)
			{
				var grid:Grid = new Grid;
				grid.x = indexCoord[i][0];
				grid.y = indexCoord[i][1];
				grid.index = i;
				gridLayer.addChild(grid);
			}
			addChild(gridLayer);
			gridLayer.visible = false;
			
			//初始化被选择目标脚下的格子
			selectSign_Grid = new SelectedGridUI;
			addChild(selectSign_Grid);
			
			//初始化底部动画层
			bottomAnimLayer = new Sprite;
			addChild(bottomAnimLayer);
			
			//初始化角色层
			roleLayer = new Sprite;
			addChild(roleLayer);
			
			//初始化覆盖动画层
			coverAnimLayer = new Sprite;
			addChild(coverAnimLayer);
			
			//初始化UI层
			uiLayer = new Sprite;
			addChild(uiLayer);
			
			//初始化被选择目标头上的箭头
			selectSign_Arrow = new ArrowUI;
			addChild(selectSign_Arrow);
			
			//隐藏两个选择标记
			hideSelectSign();
			moveSelectSign_Arrow = new MoveController;
			moveSelectSign_Grid = new MoveController;
			
			//将子项的鼠标事件设为false 由本容器统一管理
			this.mouseChildren = false;
			
			//侦听鼠标事件
			addEventListener(MouseEvent.MOUSE_MOVE, onMouseMove);
			addEventListener(MouseEvent.MOUSE_UP, onMouseClick);
			
			//初始化排序计时器
			sortTimer = new Timer(100);
			sortTimer.addEventListener(TimerEvent.TIMER, sort);
		}
		
		/**
		 * 清除本对象
		 */
		public function clear():void
		{
			removeEventListener(MouseEvent.MOUSE_MOVE, onMouseMove);
			removeEventListener(MouseEvent.MOUSE_UP, onMouseClick);
			
			sortTimer.stop();
			sortTimer.removeEventListener(TimerEvent.TIMER, sort);
		}
		
		//背景-----------------------------------------------
		/**
		 * 加载背景
		 * @param className: 要加载的背景图的素材名
		 */
		public function loadBackground(className:String):void
		{
			bg.loadClass(className);
		}
		
		//角色-----------------------------------------------
		/**
		 * 添加一个角色
		 * @param	init : 如果为true的话 角色将被放在屏幕外面 等待跑步进场
		 */
		public function addRole(role:Role,init:Boolean = false):void
		{
			var index:int = role.vo.pos;
			if (index != FRIEND_INDEX && (index < 0 || index > 17)) throw new Error("invalid index");
			
			roleLayer.addChild(role);
			if (index != FRIEND_INDEX)
			{
				var coord:Array = indexCoord[index];
				role.x = coord[0];
				role.y = coord[1];
			}
			
			if (init)
			{
				if (index < 9) //敌人
				{
					role.x += INIT_OFFSET;
					role.y -= INIT_OFFSET;
					
					if (role.vo.label == RoleVo.BOSS) boss = role;
				}
				else //我方
				{
					role.x -= INIT_OFFSET;
					role.y += INIT_OFFSET;
					
					if (role.vo.label == RoleVo.MAIN_ROLE) mainRole = role;
				}
			}
		}
		
		/**
		 * 移除一个角色
		 * @param	role
		 */
		public function removeRole(role:Role):void
		{
			if (!role) return;
			if (roleLayer.contains(role))
			{
				roleLayer.removeChild(role);
				role.clear();
			}
		}
		
		/**
		 * 获得指定ID的角色
		 * @param	id
		 * @return
		 */
		public function getRoleById(id:int):Role
		{
			for (var i:int = 0; i < roleLayer.numChildren; i++ )
			{
				var role:Role = roleLayer.getChildAt(i) as Role;
				if (role && role.vo.id == id) return role;
			}
			return null;
		}
		
		/**
		 * 获得指定位置索引的角色
		 * @param	index
		 * @return
		 */
		public function getRoleByIndex(index:int):Role
		{
			if (index<0 || index>17) return null;
			for (var i:int = 0; i < roleLayer.numChildren; i++)
			{
				var role:Role = roleLayer.getChildAt(i) as Role;
				if (role && role.vo.pos == index) return role;
			}
			return null;
		}
		
		/**
		 * 获得指定位置索引前方是否有角色挡着
		 * @param	index
		 * @return
		 */
		public function isFrontOccupied(index:int):Boolean
		{
			var arrIndex:Point;
			var formationArray:Array;
			if (index<9 && index>=0) //敌方角色
			{
				arrIndex = getArrayIndexInFormation(index, true);
				formationArray = enemyIndexFormation;
			}
			else if (index >= 9 && index < 18) //我方角色
			{
				arrIndex = getArrayIndexInFormation(index, false);
				formationArray = selfIndexFormation;
			}
			else return false;
			
			for (var i:int = arrIndex.x - 1; i > 0; i--)
			{
				var role:Role = getRoleByIndex(formationArray[i][arrIndex.y]);
				if (role && role.vo.hp > 0) return true;
			}
			return false;
		}
		
		/**
		 * 获得一个角色或地块面前的地块的索引
		 * @param	target
		 */
		public function getFrontGridIndex(target:*):int
		{
			var index:int;
			if (target is RoleVo) index = (target as RoleVo).pos;
			else if (target is Role) index = (target as Role).vo.pos;
			else if (target is Grid) index = (target as Grid).index;
			else if (target is int) index = target;
			
			var arrayIndex:Point;
			if (index >= 0 && index < 9) //敌方角色
			{
				arrayIndex = getArrayIndexInFormation(index, true);
				return enemyIndexFormation[arrayIndex.x - 1][arrayIndex.y];
			}
			else if (index >= 9 && index < 18) //我方角色
			{
				arrayIndex = getArrayIndexInFormation(index, false);
				return selfIndexFormation[arrayIndex.x - 1][arrayIndex.y];
			}
			else if (index == 18 || index == 24) return 21;
			else if (index == 19 || index == 25) return 22;
			else if (index == 20 || index == 26) return 23;
			else return -1;
		}
		
		/**
		 * 获得一个角色或地块面前的地块的坐标
		 * @param	target
		 */
		public function getFrontGridCoord(target:*):Point
		{	
			return getGridCoord(getFrontGridIndex(target));
		}
		
		/**
		 * 获得某个格子的坐标
		 * @return
		 */
		public function getGridCoord(index:int):Point
		{
			if (index == FRIEND_INDEX) return new Point(FRIEND_POS_X, FRIEND_FIELD_POS_Y);
			if (index == FRIEND_FIELD_INDEX) return new Point(FRIEND_FIELD_POS_X, FRIEND_FIELD_POS_Y);
			
			var coord:Array = indexCoord[index];
			return new Point(coord[0], coord[1]);
		}
		
		//鼠标事件-------------------------------------------
		//获得当前 鼠标所在的显示对象
		private function getCurrentMouseObject():DisplayObject
		{
			mousePoint.x = stage.mouseX;
			mousePoint.y = stage.mouseY;
			
			//暂时忽略角色的鼠标事件
			//for (var i:int = roleLayer.numChildren - 1; i >= 0; i-- )
			//{
				//var obj:DisplayObject = roleLayer.getChildAt(i);
				//if (obj.hasOwnProperty("hitTest") && obj["hitTest"](mousePoint)) return obj;
			//}
			for (var i:int = gridLayer.numChildren - 1; i >= 0; i-- )
			{
				var obj:DisplayObject = gridLayer.getChildAt(i);
				if (obj.hasOwnProperty("hitTest") && obj["hitTest"](mousePoint)) return obj;
			}
			if (bg.hitTest(mousePoint)) return bg;
			return null;
		}
		
		public function onMouseMove(event:MouseEvent=null):void
		{
			var obj:DisplayObject = getCurrentMouseObject();
			
			if (obj != mouseObject || event == null)
			{
				if (mouseObject) mouseObject.dispatchEvent(new MouseEvent(MouseEvent.ROLL_OUT)); //可以冒泡
				if (obj) obj.dispatchEvent(new MouseEvent(MouseEvent.ROLL_OVER)); //可以冒泡
				mouseObject = obj;
			}
		}
		
		private function onMouseClick(event:MouseEvent):void
		{
			var obj:DisplayObject = getCurrentMouseObject();
			
			if (obj) obj.dispatchEvent(new MouseEvent(MouseEvent.CLICK)); //可以冒泡
		}
		
		//排序-----------------------------------------------
		//排序函数
		public function sort(event:TimerEvent = null):void
		{
			var roleList:Array = new Array;
			for (var i:int = 0; i < roleLayer.numChildren; i++)
			{
				var role:Role = roleLayer.getChildAt(i) as Role;
				if (role) roleList.push(role);
			}
			roleList.sortOn("y", Array.NUMERIC);
			
			for (i = 0; i < roleList.length; i++) roleLayer.setChildIndex(roleList[i], i);
		}
		
		//连续排序
		public function keepSorting():void
		{
			sortTimer.start();
		}
		
		//停止连续排序
		public function stopSorting():void
		{
			sortTimer.stop();
		}
		
		//其他接口-----------------------------------------------
		//显示格子
		public function showGrid():void
		{
			gridLayer.visible = true;
		}
		
		//隐藏格子
		public function hideGrid():void
		{
			gridLayer.visible = false;
		}
		
		//选择某个角色
		public function selectRole(role:Role):void
		{
			var gridCoord:Array = indexCoord[role.vo.pos];
			var gridCoordPoint:Point = new Point(gridCoord[0], gridCoord[1]);
			var roleTopPoint:Point = role.top;
			if (selectSign_Arrow.visible == false)
			{
				selectSign_Grid.visible = selectSign_Arrow.visible = true;
				selectSign_Grid.x = gridCoordPoint.x;
				selectSign_Grid.y = gridCoordPoint.y;
				selectSign_Arrow.x = roleTopPoint.x;
				selectSign_Arrow.y = roleTopPoint.y;
			}
			else
			{
				moveSelectSign_Grid.translate(selectSign_Grid, gridCoordPoint, 0.2, true);
				moveSelectSign_Arrow.jump(selectSign_Arrow, roleTopPoint, -1, 0.2, true);
			}
		}
		
		//选择某个地块
		public function selectGrid(grid:Grid):void
		{
			var coord:Array = indexCoord[grid.index];
			if (selectSign_Arrow.visible == false)
			{
				selectSign_Grid.visible = selectSign_Arrow.visible = true;
				selectSign_Grid.x = selectSign_Arrow.x = coord[0];
				selectSign_Grid.y = selectSign_Arrow.y = coord[1];
			}
			else
			{
				var point:Point = new Point(coord[0], coord[1]);
				moveSelectSign_Grid.translate(selectSign_Grid, point, 0.2, true);
				moveSelectSign_Arrow.jump(selectSign_Arrow, point, -1, 0.2, true);
			}
		}
		
		//选择某个地块 如果地块上有角色就选择这个角色
		public function select(grid:Grid):void
		{
			var role:Role = getRoleByIndex(grid.index);
			if (role) selectRole(role);
			else selectGrid(grid);
		}
		
		//隐藏选择标记
		public function hideSelectSign():void
		{
			selectSign_Grid.visible = selectSign_Arrow.visible = false;
		}
		
		//将所有的角色从灰色状态还原回来
		public function resetAllRoleSelectable():void
		{
			for (var i:int = 0; i < roleLayer.numChildren; i++)
			{
				var role:Role = roleLayer.getChildAt(i) as Role;
				if (role && !role.selectable) role.selectable = true;
			}
		}
		
		//设置某个地块是否可选 可选的话会变蓝色 不可选则无色
		public function setGridSelectable(index:int,selectable:Boolean):void
		{
			for (var i:int = 0; i < gridLayer.numChildren; i++)
			{
				var grid:Grid = gridLayer.getChildAt(i) as Grid;
				if (grid && grid.index == index)
				{
					grid.selectable = selectable;
					return;
				}
			}
		}
		
		//设置某个地块是否被选择 被选的话呈红色 否则要么蓝要么无色
		public function setGridSelected(index:int, selected:Boolean):void
		{
			for (var i:int = 0; i < gridLayer.numChildren; i++)
			{
				var grid:Grid = gridLayer.getChildAt(i) as Grid;
				if (grid && grid.index == index)
				{
					grid.selected = selected;
					return;
				}
			}
		}
		
		//设置某阵营所有地块是否可选
		public function setAllGridSelectable(enemySide:Boolean,selectable:Boolean):void
		{
			for (var i:int = 0; i < gridLayer.numChildren; i++)
			{
				var grid:Grid = gridLayer.getChildAt(i) as Grid;
				if (grid)
				{
					if (enemySide && grid.index >= 0 && grid.index < 9) grid.selectable = selectable;
					else if (!enemySide && grid.index >= 9 && grid.index < 18) grid.selectable = selectable;
				}
			}
		}
		
		//设置所有角色的血条是否显示
		public function setHpBarVisible(value:Boolean):void
		{
			for (var i:int = 0; i < roleLayer.numChildren; i++)
			{
				var role:Role = roleLayer.getChildAt(i) as Role;
				if (role) role.hpBar.visible = value;
			}
		}
		
		//将我方角色中除指定角色外的角色都变暗或取消变暗
		public function setRoleDarkness(curRole:RoleVo, showDark:Boolean = true):void
		{
			for (var i:int = 0; i < roleLayer.numChildren; i++)
			{
				var role:Role = roleLayer.getChildAt(i) as Role;
				if (role && role.vo.pos > 8 && role.vo.pos != curRole.pos)
				{
					if (showDark) role.addFilter(AnimationClip.darkFilter, AnimationClip.DARK_FILTER);
					else role.removeFilter(AnimationClip.DARK_FILTER);
				}
			}
		}
		
		//获得给定地块所在行的所有地块的索引
		//从左往右
		public function getGridsInRow(grid:*):Array
		{
			var index:int;
			if (grid is Grid) index = grid.index;
			else if (grid is int) index = grid;
			
			if (index > 17) return null;
			
			var indexFormation:Array = index < 9 ? enemyIndexFormation : selfIndexFormation;
			var point:Point = getArrayIndexInFormation(index, index < 9);
			var result:Array = (indexFormation[point.x] as Array).concat();
			
			return result;
		}
		
		//获得给定地块所在列的所有地块的索引
		//从前向后
		public function getGridsInCol(grid:*):Array
		{
			var index:int;
			if (grid is Grid) index = grid.index;
			else if (grid is int) index = grid;
			
			if (index > 17) return null;
			
			var indexFormation:Array = index < 9 ? enemyIndexFormation : selfIndexFormation;
			var point:Point = getArrayIndexInFormation(index, index < 9);
			var result:Array = new Array;
			for (var i:int = 1; i < indexFormation.length; i++)
			{
				var arr:Array = indexFormation[i] as Array;
				result.push(arr[point.y]);
			}
			
			return result;
		}
		
		//获得给定地块所在十字区域的所有地块的索引
		//首位为中心
		public function getGridsInCross(grid:*):Array
		{
			var index:int;
			if (grid is Grid) index = grid.index;
			else if (grid is int) index = grid;
			
			if (index > 17) return null;
			
			var indexFormation:Array = index < 9 ? enemyIndexFormation : selfIndexFormation;
			var point:Point = getArrayIndexInFormation(index, index < 9);
			var result:Array = new Array;
			
			result.push(index); //中心点
			
			//周边四点
			var gridIndex:int;
			gridIndex = scanFormationValid(point.x - 1 , point.y, indexFormation);
			if (gridIndex != -1) result.push(gridIndex);
			gridIndex = scanFormationValid(point.x + 1 , point.y, indexFormation);
			if (gridIndex != -1) result.push(gridIndex);
			gridIndex = scanFormationValid(point.x , point.y + 1, indexFormation);
			if (gridIndex != -1) result.push(gridIndex);
			gridIndex = scanFormationValid(point.x , point.y - 1, indexFormation);
			if (gridIndex != -1) result.push(gridIndex);
			
			return result;
		}
		
		private function scanFormationValid(xIndex:int, yIndex:int,indexFormation:Array):int
		{
			if (xIndex > 3 || xIndex < 1) return -1;
			if (yIndex > 2 || yIndex < 0) return -1;
			
			return indexFormation[xIndex][yIndex];
		}
		
		/**
		 * 获得某阵营中角色是否有站成横排 返回横排中人数最多的那排
		 * @param threshold : 一排中有几个人就算OK
		 * @return : Array首位为中心位置索引 接下来为Role
		 */
		public function getRowFormation(enemySide:Boolean, threshold:int = 3, needCloseAttackAble:Boolean = false):Array
		{
			var indexFormation:Array = enemySide ? enemyIndexFormation : selfIndexFormation;
			var result:Array;
			var maxNum:int = -1;
			
			for (var i:int = 1; i < indexFormation.length; i++)
			{
				var arr:Array = indexFormation[i] as Array;
				var tempResult:Array = new Array;
				var cantCloseAttack:Boolean = true;
				
				for (var j:int = 0; j < arr.length; j++)
				{
					var role:Role = getRoleByIndex(arr[j]);
					if (role != null && role.vo.hp > 0)
					{
						tempResult.push(role);
						if (needCloseAttackAble && !isFrontOccupied(role.vo.pos)) cantCloseAttack = false;
					}
				}
				
				//如果要求近身攻击且关键点无法被近身攻击到 则PASS
				if (needCloseAttackAble && cantCloseAttack) continue;
				
				if (tempResult.length > maxNum)
				{
					maxNum = tempResult.length;
					result = tempResult;
					result.unshift(arr[1]);
				}
			}
			
			if (maxNum >= threshold) return result;
			return null;
		}
		
		/**
		 * 获得某阵营中角色是否有站成纵列 返回纵列中人数最多的那列
		 * @param threshold : 一列中有几个人就算OK
		 * @return : Array首位为最靠前的那格的索引 接下来为Role
		 */
		public function getColFormation(enemySide:Boolean, threshold:int = 3):Array
		{
			var indexFormation:Array = enemySide ? enemyIndexFormation : selfIndexFormation;
			var result:Array;
			var maxNum:int = -1;
			
			for (var i:int = 0; i < 3; i++)
			{
				var tempResult:Array = new Array;
				
				for (var j:int = 1; j < 4; j++)
				{
					var role:Role = getRoleByIndex(indexFormation[j][i]);
					if (role != null && role.vo.hp > 0) tempResult.push(role);
				}
				
				if (tempResult.length > maxNum)
				{
					maxNum = tempResult.length;
					result = tempResult;
					result.unshift(indexFormation[1][i]);
				}
			}
			
			if (maxNum >= threshold) return result;
			else return null;
		}
		
		/**
		 * 获得某阵营中角色是否有站成十字形 返回人数最多的那个范围
		 * @param threshold : 十字区域中有几个人就算OK
		 * @return : Array首位为中心 接下来为Role
		 */
		public function getCrossFormation(enemySide:Boolean, threshold:int = 3, needCloseAttackAble:Boolean = false):Array
		{
			var indexFormation:Array = enemySide ? enemyIndexFormation : selfIndexFormation;
			var result:Array = new Array;
			var maxNum:int = -1;
			
			//遍历指定阵营中的每一格(共9次) 每次查看此格以及此格周围的上下左右格内有无人站 有的话push
			//找到人数最多的那个范围 在人数达到阀值的情况下返回该范围
			for (var i:int = 1; i < 4; i++)
			{
				for (var j:int = 0; j < 3; j++)
				{
					var tempResult:Array = new Array;
					var gridIndex:int;
					var role:Role;
					
					if (needCloseAttackAble && isFrontOccupied(indexFormation[i][j])) continue;
					
					//前后点
					gridIndex = scanFormationValid(i - 1, j, indexFormation);
					if (gridIndex != -1)
					{
						role = getRoleByIndex(gridIndex);
						if (role != null && role.vo.hp > 0) tempResult.push(role);
					}
					gridIndex = scanFormationValid(i + 1, j, indexFormation);
					if (gridIndex != -1)
					{
						role = getRoleByIndex(gridIndex);
						if (role != null && role.vo.hp > 0) tempResult.push(role);
					}
					
					//两侧点
					gridIndex = scanFormationValid(i, j + 1, indexFormation);
					if (gridIndex != -1)
					{
						role = getRoleByIndex(gridIndex);
						if (role != null && role.vo.hp > 0) tempResult.push(role);
					}
					gridIndex = scanFormationValid(i, j - 1, indexFormation);
					if (gridIndex != -1)
					{
						role = getRoleByIndex(gridIndex);
						if (role != null && role.vo.hp > 0) tempResult.push(role);
					}
					
					//中心点 - 关键点
					gridIndex = indexFormation[i][j];
					role = getRoleByIndex(gridIndex);
					if (role != null && role.vo.hp > 0) tempResult.push(role);
					
					if (tempResult.length > maxNum)
					{
						maxNum = tempResult.length;
						result = tempResult;
						result.unshift(gridIndex);
					}
				}
			}
			
			if (maxNum >= threshold) return result;
			else return null;
		}
		
		/**
		 * 获得所有的站位 本方法的意义在于检测人数阀值
		 * @param	enemySide
		 * @param	threshold
		 * @return : Array 首位为中心
		 */
		public function getAllFormation(enemySide:Boolean,threshold:int):Array
		{
			var result:Array = new Array;
			var offset:int = enemySide ? 0 : 9;
			for (var i:int = 0; i < 9; i++)
			{
				var index:int = i + offset;
				var role:Role = getRoleByIndex(index);
				if (role && role.vo.hp > 0) result.push(role);
			}
			if (result.length >= threshold)
			{
				var indexFormation:Array = enemySide ? enemyIndexFormation : selfIndexFormation;
				result.unshift(indexFormation[2][1]);
				return result;
			}
			else return null;
		}
		
		/**
		 * 添加一个叠加动画 或者底部动画
		 * @param	animation : 动画
		 * @param	className : 素材名 在这里其实是用作ID
		 * @param	atBottom : 是否底部动画
		 */
		public function addAnimation(animation:Sprite, className:String = null, atBottom:Boolean = false):void
		{
			if(className) removeAnimation(className);
			
			if (atBottom) bottomAnimLayer.addChild(animation);
			else coverAnimLayer.addChild(animation);
			
			if(className) animList[className] = animation;
		}
		
		/**
		 * 移除一个叠加动画或底部动画
		 */
		public function removeAnimation(className:String):void
		{
			var animation:Sprite = animList[className];;
			if (animation) animation.parent.removeChild(animation);
			delete animList[className];
		}
		
		public function addFilter(filter:BitmapFilter, filterType:int):void
		{
			removeFilter(filterType);
			
			var arr:Array = bg.filters;
			arr.push(filter);
			bg.filters = arr;
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
				bg.filters = arr;
			}
		}
		
		//常量-----------------------------------------------
		
		//静态位置数据
		private static var enemyIndexFormation:Array = [[18, 19, 20], [6, 7, 8], [3, 4, 5], [0, 1, 2]]; //敌方阵营站位索引
		private static var selfIndexFormation:Array = [[24, 25, 26], [9, 10, 11], [12, 13, 14], [15, 16, 17]]; //我方阵营站位索引
		
		private static function getArrayIndexInFormation(index:int,isEnemy:Boolean):Point
		{
			var formationArray:Array = isEnemy ? enemyIndexFormation : selfIndexFormation;
			for (var i:int = 0; i < formationArray.length; i++)
			{
				var arr:Array = formationArray[i];
				for (var j:int = 0; j < arr.length; j++)
				{
					if (arr[j] == index) return new Point(i, j);
				}
			}
			return null;
		}
		
		private static var indexCoord:Array = [[419, 149], [452, 165], [484, 181], [385, 165], [419, 181], [452, 197], [355, 180],
		[387, 197], [419, 213], [226, 246], [257, 262], [290, 278], [194, 262], [226, 278], [258, 295], [162, 278], [194, 294],
		[225, 310], [323, 197], [355, 213], [386, 229], [290, 213], [321, 230], [354, 246], [258, 230], [290, 246], [322, 262]];
		
		//一开始时角色距离自己位置的偏移量
		private const INIT_OFFSET:int = 240;
		
		//朋友所站的位置
		public static const FRIEND_POS_X:int = -50;
		public static const FRIEND_POS_Y:int = 520;
		public static const FRIEND_INDEX:int = 100;
		
		//朋友出来所站的位置
		public static const FRIEND_FIELD_POS_X:int = 160;
		public static const FRIEND_FIELD_POS_Y:int = 312;
		public static const FRIEND_FIELD_INDEX:int = 101;
	}

}