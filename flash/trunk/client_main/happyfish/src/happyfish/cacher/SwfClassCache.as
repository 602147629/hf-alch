package happyfish.cacher 
{
	import br.com.stimuli.loading.BulkLoader;
	import br.com.stimuli.loading.BulkProgressEvent;
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	import flash.display.Loader;
	import flash.display.Stage;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.IOErrorEvent;
	import flash.events.ProgressEvent;
	import flash.net.URLRequest;
	import flash.system.ApplicationDomain;
	import flash.system.LoaderContext;
	import flash.system.SecurityDomain;
	import happyfish.events.SwfClassCacheEvent;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.manager.module.interfaces.IClassManager;
	import happyfish.manager.SwfURLManager;
	import happyfish.model.SwfLoader;
	import happyfish.utils.SysTracer;
	
	//[Event(name = "complete", type = "flash.events.Event")]
	
	/**
	 * 素材类加载与管理类
	 * @author slamjj
	 */
	public class SwfClassCache extends EventDispatcher implements IClassManager
	{
		private var appDomain:ApplicationDomain;
		
		private static var instance:SwfClassCache;
		private var loadedPer:uint;
		private var loadedSwfList:Object;
		private var currentUrl:String;
		
		private var loadArr:Array = new Array();
		private var currentClass:String;
		public function SwfClassCache(access:Private) 
		{
			
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
					loadedSwfList = new Object();
					appDomain = ApplicationDomain.currentDomain;
				}
			}
			else
			{	
				throw new Error( "SwfClassCache"+"单例" );
			}
		}
		
		/**
		 * 通知开始获取指定class
		 * @param	className
		 */
		public function loadClass(className:String):void {
			
			if (hasClass(className))
			{
				dispatchComplete(className);
				return;
			}
			
			if (1 == loadArr.push(className))
			{
				loadNext();
			}
		}
		
		private function getClassSwfUrl(className:String):String {
			return SwfURLManager.getInstance().getClassURL(className);
		}
		
		private function loadClassSwf(url:String):void
		{
			currentUrl = url;
			loadedSwfList[url] = 1;
			var loader:LoadingItem = SwfLoader.getInstance().add(url);
			loader.addEventListener(Event.COMPLETE, loadClassSwf_complete);
			loader.addEventListener(BulkLoader.ERROR, loadClassSwf_complete);
		}
		
		private function loadClassSwf_complete(e:Event):void 
		{
			loadedSwfList[currentUrl] = 2;
			e.target.removeEventListener(Event.COMPLETE, loadClassSwf_complete);
			e.target.removeEventListener(IOErrorEvent.IO_ERROR, loadClassSwf_complete);
			dispatchComplete(currentClass);
			loadNext();
		}
		
		private function loadNext():void
		{
			if (0 == loadArr.length) return;
			
			currentClass = loadArr[0] as String;
			var url:String;
			if (hasClass(currentClass) || 2 == loadedSwfList[(url = getClassSwfUrl(currentClass))])
			{
				loadArr.shift();
				dispatchComplete(currentClass);
				loadNext();
			}else
			{
				loadClassSwf(url);
			}
		}
		
		private function dispatchComplete(className:String):void
		{
			var e:SwfClassCacheEvent = new SwfClassCacheEvent(SwfClassCacheEvent.COMPLETE);
			e.className = className;
			e.hasClass = hasClass(className);
			if (!e.hasClass) 
			{
				SysTracer.systrace("no class", className);
			}
			dispatchEvent(e);
		}
		
		public function hasClass(className:String):Boolean {
			return appDomain.hasDefinition(className);
		}
		
		public function getClass(className:String):Class {
			if (appDomain.hasDefinition(className)) 
			{
				return appDomain.getDefinition(className) as Class;
			}
			return null;
		}
		
		public static function getInstance():SwfClassCache
		{
			if (instance == null)
			{
				instance = new SwfClassCache( new Private() );
			}
			return instance;
		}
		
	}
	
}
class Private {}