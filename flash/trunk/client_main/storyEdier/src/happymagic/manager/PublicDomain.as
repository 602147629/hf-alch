package happymagic.manager 
{
	import flash.display.Stage;
	import flash.events.Event;
	import flash.system.ApplicationDomain;
	import flash.system.LoaderContext;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class PublicDomain 
	{
		private static var instance:PublicDomain;
		
		public var customObj:Object = new Object();
		private var timeDifference:Number;
		
		public var stageWidth:Number=748;
		public var stageHeight:Number=600;
		private var stage:Stage;
		
		public var ver:String;
		public var snsType:String;
		public var debug:Boolean;
		public var isLocal:Boolean;
		public var initUi:String;
		
		//公共加载用的应用域
		public var loaderContext:LoaderContext;
		public var appDomain:ApplicationDomain;
		public var createModule:String;
		public var createUrl:String;
		public var piantou:String;
		
		public static const WORLD_WIDTH:Number = 2000;
		public static const WORLD_HEIGHT:Number = 1300;
		public static const UI_HEIGHT:Number = 140; //底部和顶部UI框的高度 舞台高度减掉这个高度才是场景高度
		public static const TOP_UI_HEIGHT:Number = 45; //顶部UI框高度
		
		public function PublicDomain(access:Private) 
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
				throw new Error( "PublicDomain"+"单例" );
			}
		}
		
		public function setVar(name:String,val:*):void {
			customObj[name] = val;
		}
		
		public function getVar(name:String):* {
			return customObj[name];
		}
		
		public function set sysTime(value:Date):void {
			var now:Date = new Date();
			
			timeDifference = now.getTime() - value.getTime();
		}
		
		public function get sysTime():Date {
			var time:Date = new Date();
			var tmptime:Number = time.getTime()-timeDifference;
			time.setTime(tmptime);
			
			return time;
		}
		
		public static function getInstance():PublicDomain
		{
			if (instance == null)
			{
				instance = new PublicDomain( new Private() );
			}
			return instance;
		}
	}
	
}
class Private {}