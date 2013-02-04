package happymagic.model.vo 
{
	import happymagic.model.vo.DecorVo;
	/**
	 * 传送门VO 2011.11.10
	 * @author XiaJunJie
	 */
	public class PortalVo extends DecorVo
	{
		public var targetSceneId:int; //目标场景ID
		public var targetSceneName:String = "未知场景"; //目标场景名字
		
	}

}