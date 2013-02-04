package happymagic.model.vo 
{
	import com.adobe.serialization.json.JSON;
	import com.brokenfunction.json.decodeJson;
	import com.brokenfunction.json.encodeJson;
	import happyfish.model.vo.BasicVo;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author jj
	 */
	public class NpcVo extends NpcClassVo
	{
		public var chats:String="";
		public var chatState:uint;	//是否已说过此对话，2 为已说过 1为未说过
		public var chatId:int;
		
		public function NpcVo() 
		{
			
		}
		
		public function setValue(obj:Object):NpcVo {
			setData(obj);
			if (cid) 
			{
				var npcclass:Object = DataManager.getInstance().getNpcClassByNpcId(cid);
				setData(npcclass);
			}
			
			return this;
		}
		
	}

}