package happyfish.guide.tools 
{
	import happyfish.guide.GuideManager;
	import happyfish.guide.vo.GuideVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class GuideListUtil 
	{
		private static var guideList:XMLList;
		
		public static function setXMLString(s:String):void
		{
			if (!s) return;
			guideList = new XML(s).guide;
		}
		
		public static function hasGuideById(id:String):Boolean
		{
			if (!guideList) return false;
			var list:XMLList = guideList.(@id == id);
			return list.length() != 0;
		}
		
		public static function getGuideById(id:String):GuideVo
		{
			if (!guideList) return null;
			var list:XMLList = guideList.(@id == id);
			if (0 == list.length()) return null;
			
			return GuideXMLConverter.createGuide(list[0]);
		}
		
		public static function startGuide(id:String, idx:int = 0):void
		{
			GuideManager.getInstance().removeAllGuide();
			
			var vo:GuideVo = getGuideById(id);
			if (!vo) return;
			
			GuideManager.getInstance().addGuide(vo);
			GuideManager.getInstance().gotoGuideStep(idx);
		}
		
		public static function finishGuide(id:String):void
		{
			GuideManager.getInstance().finishGuide(id);
		}
		
	}

}