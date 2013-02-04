package happymagic.model.command 
{
	import happyfish.manager.InterfaceURLManager;
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class CompleteChatCommand extends BaseDataCommand
	{
		
		public function CompleteChatCommand(chatId:int) 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("completeChat"), { chatId:chatId } );
			
			loader.load(request);
		}
		
	}

}