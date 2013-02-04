package happymagic.display.control 
{
	import flash.display.DisplayObjectContainer;
	import flash.events.EventDispatcher;
	import flash.geom.Point;
	import happyfish.manager.module.ModuleManager;
	import happymagic.display.view.PiaoMsgItemView;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.manager.DisplayManager;
	
	/**
	 * ...
	 * @author jj
	 */
	public class PiaoMsgControl 
	{
		
		public function PiaoMsgControl(access:Private) 
		{
			
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
				}
			}
			else
			{	
				throw new Error( "PiaoMsgControl"+"单例" );
			}
		}
		
		public function setContainer(value:DisplayObjectContainer,eventer:EventDispatcher):void {
			_container = value;
			
			eventer.addEventListener(PiaoMsgEvent.SHOW_PIAO_MSG, showMsg);
		}
		
		private function showMsg(e:PiaoMsgEvent):void 
		{
			var toPoint:Point; 
			var num:uint=0;
			for (var i:int = 0; i < e.msgs.length; i++) 
			{
				if (e.msgs[i][1]) 
				{
					var tmp:PiaoMsgItemView = new PiaoMsgItemView();
					tmp.now = e.now;
					tmp.justShow = e.justShow;
					_container.addChild(tmp);
					tmp.setData(e.msgs[i][0], e.msgs[i][1], e.x, e.y, num * 1500, e.msgs[i][2], e.msgs[i][3], e.msgs[i][4], e.msgs[i][5]);
					num++;
				}
			}
			
		}
		
		public static function getInstance():PiaoMsgControl
		{
			if (instance == null)
			{
				instance = new PiaoMsgControl( new Private() );
			}
			return instance;
		}
		
		/**
		 *  Map<String, Point|Function>
		 * Function:function():Point;
		 */
		private var pointMap:Object = { };
		public function setPointByType(type:String, p:*):void
		{
			if (!p)
			{
				delete pointMap[type];
			}else
			{
				pointMap[type] = p;
			}
		}
		
		public function getPoint(type:String):Point
		{
			var p:* = pointMap[type];
			if (p is Point) return p;
			else if (p is Function) return p();
			return null;
		}
		
		
		private static var instance:PiaoMsgControl;
		private var _container:DisplayObjectContainer;
		
	}
	
}
class Private {}