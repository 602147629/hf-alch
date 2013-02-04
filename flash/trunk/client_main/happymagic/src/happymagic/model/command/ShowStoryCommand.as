package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.display.control.StoryPlayCommand;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.ResultVo;
	/**
	 * 测试用 直接触发一个剧情
	 * @author XiaJunJie
	 */
	public class ShowStoryCommand extends BaseDataCommand
	{
		
		public function ShowStoryCommand(id:int)
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("showStory"), { id:id } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			StoryPlayCommand.getInstance().checkAndPlay();
			
			commandComplete();
		}
	}

}