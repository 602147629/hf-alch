package happymagic.guide.commands 
{
	import flash.events.Event;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.net.URLVariables;
	import happyfish.guide.tools.GuideListUtil;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.model.UrlConnecter;
	import happyfish.time.Time;
	/**
	 * ...
	 * @author lite3
	 */
	public class LoadGuideDataCommand extends UrlConnecter
	{
		private var callbackFun:Function;
		
		
		public function LoadGuideDataCommand(callbackFun:Function) 
		{
			super();
			this.callbackFun = callbackFun;
			
			var request:URLRequest = new URLRequest(InterfaceURLManager.getInstance().getUrl("guideData"));
			var vars:URLVariables = new URLVariables();
			vars.systime = Time.getCurTime();
			//request.data = vars;
			load(request);
			addEventListener(Event.COMPLETE, completeHandler);
		}
		
		private function completeHandler(e:Event):void 
		{
			GuideListUtil.setXMLString(data);
			if (callbackFun != null) callbackFun();
		}
		
	}

}