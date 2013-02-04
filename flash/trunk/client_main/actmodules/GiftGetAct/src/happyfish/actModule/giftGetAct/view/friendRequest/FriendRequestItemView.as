package happyfish.actModule.giftGetAct.view.friendRequest 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happyfish.actModule.giftGetAct.model.vo.GiftFriendUserVo;
	import happyfish.actModule.giftGetAct.model.vo.GiftRequestVo;
	import happyfish.actModule.giftGetAct.model.vo.GiftVo;
	import happyfish.actModule.giftGetAct.view.current.CurrentItemView;
	import happyfish.actModule.giftGetAct.view.current.CurrentListView;
	import happyfish.display.ui.GridItem;
	import happyfish.manager.local.LocaleWords;
	import happyfish.time.Time;
	/**
	 * ...
	 * @author ZC
	 */
	
	//好友的请求界面中的列表所显示的物品
	public class FriendRequestItemView extends GridItem
	{
		private var iview:FriendRequestItemViewUi;
		private var data:GiftRequestVo;
		private var currentlist:CurrentListView;
		public function FriendRequestItemView(_uview:MovieClip) 
		{
			super(_uview);
			iview = _uview as FriendRequestItemViewUi 
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			iview.mouseChildren = true;
			
			currentlist = new CurrentListView(new GiftGetListUi, iview, 3, true, false, CurrentItemView.FRIENDREQUEST);
			currentlist.init(245, 90, 80, 80, 287, 0);
			currentlist.x = 0;
			currentlist.y = 0;
			currentlist.iview["pageNumTxt"].visible = false;
			currentlist.setGridItem(CurrentItemView, CurrentItemViewUi);
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			
		}
		
		override public function setData(vaule:Object):void
		{
			data = vaule as GiftRequestVo;

			var time:uint = Time.getCurTime() - data.date;
			
			if (time > 86400)
			{
				time = time / 86400;
				iview.date.text = String(time);	
				iview.daynum.text = LocaleWords.getInstance().getWord("GiftGetActword6");			
			}
			else if (time>3600)
			{
				time = time / 3600;				
				iview.date.text = String(time);	
				iview.daynum.text = LocaleWords.getInstance().getWord("GiftGetActword7");
			}
			else if (time > 60)
			{
				time = time / 60;
				iview.date.text = String(time);	
				iview.daynum.text = LocaleWords.getInstance().getWord("GiftGetActword8");			
			}
			else 
			{
				iview.date.text = String(time);	
				iview.daynum.text = LocaleWords.getInstance().getWord("GiftGetActword9");
			}	
			
			
			iview.nametxt.text = LocaleWords.getInstance().getWord("GiftGetActword10") + GiftDomain.getInstance().getFriendUserVo(data.uid).name + LocaleWords.getInstance().getWord("GiftGetActword11");
			iview.uidnum = data.id;
			
			var tempJ:Array = new Array();
			
			for (var j:int = 0; j < data.gifts.length; j++)
			{
				var tempK:Array = new Array();
				for (var k:int = 0; k < data.gifts[j].length; k++ )
				{
					tempK.push(data.gifts[j][k]);
				}
				tempJ.push(tempK);
			}
			
			for (var i :int = 0; i < tempJ.length; i++ )
			{   
				var tempvo:GiftVo = GiftDomain.getInstance().getGiftVo(tempJ[i][0]);
				tempJ[i].push(tempvo.name);
				tempJ[i].push(tempvo.className);
				tempJ[i].push(data.id);
				tempJ[i].push(data.hasGet);
				tempJ[i].push(data.uid);
			}
			
			currentlist.setData(tempJ);
			
			var usergiftvo:GiftFriendUserVo = GiftDomain.getInstance().getFriendUserVo(data.uid);
			var faceview:DisplayObjectContainer = GiftDomain.getInstance().showFaceView(usergiftvo.face);
			iview.addChild(faceview);
			faceview.x = 20;
			faceview.y = 23;			
			
		}
		
		
		
	}

}