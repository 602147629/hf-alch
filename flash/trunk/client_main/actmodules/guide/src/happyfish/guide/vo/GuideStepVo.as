package happyfish.guide.vo 
{
	import flash.events.MouseEvent;
	import happyfish.veal.RuntimeUtil;
	/**
	 * ...
	 * @author lite3
	 */
	public class GuideStepVo
	{
		public var id:String;
		public var tip:String;
		
		public var offsetX:int;
		public var offsetY:int;
		
		/** 显示高亮的半径,如果<=0,则为actTips的内切圆半径,否则将使用该半径 */
		public var radius:int;
		
		/** 箭头指向的目标对象 */
		public var actTips:Function;
		
		/** 将要放入的容器 */
		public var container:Function;
		
		/** 是否已经开始执行了 */
		public var isRunnig:Boolean;
		
		/** 进入这一步(未测试状态)立刻执行的脚本 */
		public var promptlyHandler:Function;
		/** 点击后执行的脚本,仅当不会到达下一步并且被忽略时执行 */
		public var clickHandler:Function;
		
		/** 对话内容 */
		public var dialogList:Vector.<DialogVo>;
		
		/**
		 * 临时变量map
		 */
		public var testVars:Object;
		
		/** 与操作列表,必须都为true才行 */
		public var addList:Vector.<String>;
		/** 或操作列表,只有一个为true就行 */
		public var orList:Vector.<String>;
		
		/** 去下一步的点击 */
		public var toNextMouseList:Vector.<MouseEventVo>;
		
		/** 忽略的点击 */
		public var ignoreMouseList:Vector.<MouseEventVo>;
		
		public function GuideStepVo(addList:Vector.<String>, orList:Vector.<String>, dialogList:Vector.<DialogVo> = null, actTips:Function = null, container:Function = null) 
		{
			this.addList = addList;
			this.orList = orList;
			this.dialogList = dialogList;
			this.actTips = actTips;
			this.container = container;
		}
		
		/**
		 * 测试是否为成功
		 * @return
		 */
		public static function checkOk(vo:GuideStepVo):Boolean
		{
			var vars:Object = null;
			if (vo.testVars != null)
			{
				vars = { };
				for (var key:String in vo.testVars)
				{
					try {
						vars[key] = RuntimeUtil.getValue(vo.testVars[key]);
					}catch (err:Error){ };
				}
			}
			var len:int = vo.addList ? vo.addList.length : 0;
			for (var i:int = 0; i < len; i++)
			{
				try{
					if (!RuntimeUtil.getValue(vo.addList[i], vars)) return false;
				}catch (err:Error) { showErr(err); return false; }
			}
			len = vo.orList ? vo.orList.length : 0;
			for (i = 0; i < len; i++)
			{
				try{
					if (!RuntimeUtil.getValue(vo.orList[i], vars)) return false;
				}catch (err:Error) { showErr(err); return false; }
			}
			return true;
		}
		
		/**
		 * 测试点击
		 * @param	list 测试的列表
		 * @param	e 事件
		 * @param	whenEmptyListValue 当list为null或空时返回的值
		 * @return 是否测试成功
		 */
		public static function testMouseEvent(list:Vector.<MouseEventVo>, e:MouseEvent, whenEmptyListValue:Boolean):Boolean
		{
			list = filterList(list, e.type);
			var len:int;
			if (!list || 0 == (len = list.length)) return whenEmptyListValue;
			for ( var i:int = 0; i < len; i++)
			{
				if (list[i].doClick(e)) return true;
			}
			return false;
		}
		
		static private function filterList(list:Vector.<MouseEventVo>, type:String):Vector.<MouseEventVo> 
		{
			var len:int;
			if (!list || 0 == (len = list.length)) return null;
			var tmp:Vector.<MouseEventVo> = new Vector.<MouseEventVo>();
			for (var i:int = 0; i < len; i++)
			{
				if (list[i].type == type) tmp.push(list[i]);
			}
			return tmp;
		}
	}

}