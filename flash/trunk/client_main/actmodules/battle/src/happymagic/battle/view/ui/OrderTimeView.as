package happymagic.battle.view.ui 
{
	import br.com.stimuli.loading.loadingtypes.VideoItem;
	import com.greensock.TweenMax;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.utils.setTimeout;
	import happyfish.manager.EventManager;
	import happymagic.battle.events.BattleEvent;
	import happymagic.model.vo.RoleVo;
	/**
	 * 战斗顺序条
	 * @author 
	 */
	public class OrderTimeView extends orderTimeUi 
	{
		private var _actionQueue:Vector.<RoleVo>;
		private var setQueueCallback:Function;
		private var iconSprite:Sprite;
		private var iconList:Vector.<OrderTimeItemView>;
		
		private var iconRectWidth:Number = 278;
		private var btwWidth:Number;
		private var _nextRoleCallBack:Function;
		private var firstNextRole:Boolean=true;
		public function OrderTimeView() 
		{
			visible = false;
			alpha = 0;
			x = -155;
			y = 150;
			
			iconList = new Vector.<OrderTimeItemView>();
			
			iconSprite = new Sprite();
			addChild(iconSprite);
			iconSprite.x = 0;
			iconSprite.y = -10;
			
			var iconMask:Sprite = new Sprite();
			iconMask.x = -89;
			iconMask.y = -43;
			iconMask.graphics.beginFill(0x000000, 1);
			iconMask.graphics.drawRect(0, 0, 338, 36);
			iconMask.graphics.endFill();
			
			iconSprite.addEventListener(MouseEvent.MOUSE_OVER, overFun);
			iconSprite.addEventListener(MouseEvent.MOUSE_OUT, outFun);
			
			EventManager.getInstance().addEventListener(BattleEvent.LIGHT_ROLE, lightRole);
			EventManager.getInstance().addEventListener(BattleEvent.CLOSE_LIGHT_ROLE, closeLightRole);
			
			//iconSprite.mask = iconMask;
		}
		
		private function closeLightRole(e:BattleEvent):void 
		{
			for (var i:int = 0; i < iconList.length; i++) 
			{
				iconList[i].setLight(false);
			}
		}
		
		private function lightRole(e:BattleEvent):void 
		{
			for (var i:int = 0; i < iconList.length; i++) 
			{
				if (iconList[i].data.pos==e.pos) 
				{
					iconList[i].setLight(true);
				}else {
					iconList[i].setLight(false);
				}
			}
		}
		
		private function outFun(e:MouseEvent):void 
		{
			var tmp:OrderTimeItemView = e.target as OrderTimeItemView;
			var event:BattleEvent = new BattleEvent(BattleEvent.CLOSE_LIGHT_ROLE);
			if (tmp) {
				
				event.pos = tmp.data.pos;
				EventManager.getInstance().dispatchEvent(event);
			}
		}
		
		private function overFun(e:MouseEvent):void 
		{
			var tmp:OrderTimeItemView = e.target as OrderTimeItemView;
			if (tmp) 
			{
				
				var event:BattleEvent = new BattleEvent(BattleEvent.LIGHT_ROLE);
				event.pos = tmp.data.pos;
				EventManager.getInstance().dispatchEvent(event);
			}
		}
		
		/**
		 * 设置战斗角色
		 * @param	actionQueue
		 * @param	callback
		 */
		public function setQueue(actionQueue:Vector.<RoleVo>,callback:Function):void {
			_actionQueue = actionQueue;
			
			btwWidth = iconRectWidth / _actionQueue.length;
			
			setQueueCallback = callback;
			
			initView();
		}
		
		public function removeRole(index:int):void {
			for (var i:int = 0; i < iconList.length; i++) 
			{
				if (iconList[i].data.pos==index) 
				{
					iconList[i].parent.removeChild(iconList[i]);
					iconList.splice(i, 1);
					break;
				}
			}
			btwWidth = iconRectWidth / _actionQueue.length;
			sortIcons(true);
		}
		
		private function initView():void 
		{
			while (iconSprite.numChildren>0) 
			{
				iconSprite.removeChildAt(0);
			}
			
			iconList = new Vector.<OrderTimeItemView>();
			
			var tmp:OrderTimeItemView;
			for (var i:int = 0; i < _actionQueue.length; i++) 
			{
				tmp = new OrderTimeItemView(_actionQueue[i]);
				iconList.push(tmp);
				iconSprite.addChild(tmp);
			}
			
			sortIcons(true,true);
		}
		
		private function sortIcons(tween:Boolean=false,init:Boolean=false):void {
			for (var i:int = 0; i < iconList.length; i++) 
			{
				if (init) 
				{
					iconList[i].x = iconRectWidth;
				}
				if (tween) 
				{
					iconList[i].alpha = 0;
					if (i==iconList.length-1) 
					{
						TweenMax.to(iconList[i], .2, { x:i * btwWidth, alpha:1, onComplete:initView_complete } );
					}else {
						TweenMax.to(iconList[i], .2, { x:i * btwWidth, alpha:1 } );
					}
				}else {
					iconList[i].x = i * btwWidth;
				}	
			}
			
			if (!tween && init) 
			{
				initView_complete();
			}
		}
		
		private function initView_complete():void 
		{
			if (setQueueCallback!=null) 
			{
				setQueueCallback.apply();
				setQueueCallback = null;
			}
		}
		
		public function nextRole():void {
			if (firstNextRole) 
			{
				//第一次时，直接显示当前角色头像出位
				firstNextRole = false;
				curRoleOut();
			}else {
				//第一次外，都需先把之前角色头像都上移一位
				nextRoleNextMv();
			}
		}
		
		private function curRoleOut():void {
			TweenMax.to(iconList[0], .2, { x:iconList[0].x - 24 } );
			TweenMax.to(iconList[0], .1, { tint:0xffffff, scaleX:.8, scaleY:.8, delay:.2 } );
			TweenMax.to(iconList[0], .2, { removeTint:true, scaleX:1.2,scaleY:1.2, alpha:0, delay:.3, onComplete:nextRole_complete } );
		}
		
		private function nextRole_complete():void {
			if (nextRoleCallBack!=null) 
			{
				nextRoleCallBack.apply();
			}
		}
		
		public function nextRoleNextMv():void 
		{
			iconList[0].scaleX = 
			iconList[0].scaleY = 1;
			iconList[0].alpha = 1;
			iconList[0].x = iconList[iconList.length - 1].x + btwWidth;
			
			iconList.push(iconList.shift());
			
			for (var i:int = 0; i < iconList.length; i++) 
			{
				if (i==iconList.length-1) 
				{
					TweenMax.to(iconList[i], .2, { x:iconList[i].x - btwWidth,onComplete:nextRoleNextMv_complete } );
				}else {
					TweenMax.to(iconList[i], .2, { x:iconList[i].x - btwWidth } );
				}
				
			}
		}
		
		private function nextRoleNextMv_complete():void 
		{
			curRoleOut();
		}
		
		public function get nextRoleCallBack():Function 
		{
			return _nextRoleCallBack;
		}
		
		public function set nextRoleCallBack(value:Function):void 
		{
			_nextRoleCallBack = value;
		}
		
		public function show():void {
			
			TweenMax.to(this, .4, { autoAlpha:1, y: 150 } );
		}
		
		public function hide():void {
			TweenMax.to(this, .4, { autoAlpha:0, y: 100 } );
		}
	}

}