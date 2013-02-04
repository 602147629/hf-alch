package happymagic.display.view.friends 
{
	import com.greensock.TweenLite;
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import happyfish.display.ui.FaceView;
	import happyfish.display.ui.GridItem;
	import happyfish.manager.EventManager;
	import happyfish.time.Time;
	import happyfish.utils.display.ItemOverControl;
	import happyfish.utils.MouseActionCommand;
	import happymagic.display.view.friends.FriendItemTipsView;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.UserVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class FriendsItemView extends GridItem
	{
		public var data:UserVo;
		private var iview:friendItemUi;
		private var _index:uint;
		private var face:FaceView;
		
		
		private const STATE_TAXES:uint = 1;
		private const STATE_NOTAXES:uint = 2;
		private const STATE_NEEDHELP:uint = 3;
		private const STATE_SAFE:uint = 4;
		private var tips:FriendItemTipsView;
		public function FriendsItemView(uiview:MovieClip) 
		{
			super(uiview);
			iview = uiview as friendItemUi;
			
			iview.mouseChildren = false;
			iview.buttonMode = true;
			iview.addEventListener(MouseEvent.CLICK, clickFun);
			//ItemOverControl.getInstance().addOverItem(iview,overFun,outFun,true);
			
			
		}
		
		private function outFun(value:Object):void 
		{
			if (value.relatedObject == tips || value.relatedObject == iview
				|| value.target == tips || value.target == iview)
			{
				return;
			}
			//ItemOverControl.getInstance().removeOverItem(tips);
			TweenLite.to(tips, .3, { alpha:0, x:iview.x + 67,onComplete:outFun_complete } );
		}
		
		private function outFun_complete():void 
		{
			tips.parent.removeChild(tips);
			tips = null;
		}
		
		private function overFun(value:Object):void 
		{
			
			if (!tips) 
			{
				tips = new FriendItemTipsView();
			}
			tips.x = iview.x + 77;
			tips.y = iview.y + 16;
			//tips.alpha = 0;
			iview.parent.addChild(tips);
			tips.setData(data);
			ItemOverControl.getInstance().addOverItem(tips, overFun, outFun, true);
			
			TweenLite.from(tips, .3, { alpha:0, x:"-10" } );
			
		}
		
		private function clickFun(e:MouseEvent=null):void 
		{
			if (data.uid==DataManager.getInstance().currentUser.uid) 
			{
				return;
			}
			if (!tips) 
			{
				tips = new FriendItemTipsView();
				tips.x = iview.x + 77;
				tips.y = iview.y + 16;
				//tips.alpha = 0;
				iview.parent.addChild(tips);
				tips.setData(data);
				
				TweenLite.from(tips, .3, { alpha:0, x:"-10" } );
				
				new MouseActionCommand().outSideClickCommand(tips, iview.stage, closeTips);
			}else {
				closeTips();
			}
			
		}
		
		private function closeTips():void {
			TweenLite.to(tips, .3, { alpha:0, x:iview.x + 67,onComplete:outFun_complete } );
		}
		
		override public function setData(value:Object):void 
		{
			data = value as UserVo;
			
			iview.nameTxt.text = data.name;
			iview.roleLevelTxt.text = data.roleLevel.toString();
			iview.homeLevelTxt.text = data.level.toString();
			
			face = new FaceView(56);
			face.loadFace(data.face);
			face.x = 20;
			face.y = 24;
			iview.addChildAt(face, iview.getChildIndex(iview.stateIcon));
			
			
			initStateIcon();
			
		}
		
		public function initStateIcon():void 
		{
			iview.stateIcon.visible = true;
			var user:UserVo = DataManager.getInstance().currentUser;
			if (Time.getRemainingTimeByEnd(data.safeTime)>0 || Time.getRemainingTimeByEnd(data.atkSafeTime)>0) 
			{
				//保护
				iview.stateIcon.gotoAndStop(STATE_SAFE);
			}else if (data.ownerUid) 
			{	
				if (data.ownerUid.toString()==user.uid) 
				{
					//占领,收税
					if (Time.getRemainingTimeByEnd(data.ownerAwardTime)==0) 
					{
						//可收税
						iview.stateIcon.gotoAndStop(STATE_TAXES);
					}else {
						//已收税
						iview.stateIcon.gotoAndStop(STATE_NOTAXES);
					}
				}else {
					//援助
					iview.stateIcon.gotoAndStop(STATE_NEEDHELP);
				}
			}else {
				iview.stateIcon.visible = false;
			}
		}
	}

}