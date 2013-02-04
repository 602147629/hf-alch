package happyfish.storyEdit.model.vo 
{
	import happyfish.model.vo.BasicVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class EditStoryGridVo extends BasicVo 
	{
		public var actor:EditStoryActorVo;
		public var actorId:int;
		public var actions:Array;
		public var index:int;
		public function EditStoryGridVo() 
		{
			
		}
		
	}

}