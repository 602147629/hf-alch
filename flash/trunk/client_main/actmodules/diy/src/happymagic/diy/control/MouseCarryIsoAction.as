package happymagic.diy.control 
{
	import com.friendsofed.isometric.Point3D;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import flash.utils.setTimeout;
	import happyfish.events.DEvent;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.grid.Tile;
	import happyfish.scene.world.grid.Wall;
	import happyfish.scene.world.WorldState;
	import happyfish.utils.display.CameraSharkControl;
	import happyfish.utils.display.McShower;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.diy.model.SaveDiyAddCommand;
	import happymagic.diy.model.SaveDiyMoveCommand;
	import happymagic.events.DataManagerEvent;
	import happymagic.events.UserInfoChangeVo;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.DecorType;
	import happymagic.model.vo.DecorVo;
	import happymagic.scene.world.control.MouseDefaultAction;
	import happymagic.scene.world.control.MouseMagicAction;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.grid.item.FurnaceDecor;
	import happymagic.scene.world.grid.item.WallDecor;
	import happymagic.scene.world.MagicWorld;
	/**
	 * 移动\增加 物品
	 * @author slam
	 */
	public class MouseCarryIsoAction extends MouseMagicAction
	{
		public static const	TYPE_MOVE:String = "move";
		public static const	TYPE_ADD:String = "add";
		
		private var _isoItem:IsoItem;
		public var type:String = "move";
		
		private var oldPos:Point3D;//物件原位置记录
		public function MouseCarryIsoAction($iso_item:IsoItem, $state:WorldState,_type:String="move") 
		{
			
			type = _type;
			
			super($state);
			(state.world as MagicWorld).enterEditMode();
			this._isoItem = $iso_item;
			oldPos = new Point3D(_isoItem.x, _isoItem.y, _isoItem.z);
			_isoItem.mouseEnabled = false;
			
			//门在移动时需要先重置门后的墙的门洞,
			//并从门列表中移除,以使课桌寻路计算正常
			if (_isoItem is Door) 
			{
				(_isoItem as Door).resetWallView();
				state.world.removeToGrid(_isoItem);
				//通知world从门队列中清除此门
				(state.world as MagicWorld).removeDoorFromList(_isoItem as Door);
			}
			
			_isoItem.physics = false;
			
			DisplayManager.uiSprite.mouseChildren=
			DisplayManager.uiSprite.mouseEnabled = false;
		}

        override public function onBackgroundClick(event:GameMouseEvent) : void
        {
            event.stopImmediatePropagation();
			
			//如果是在区域外,把物件放回原处,移动操作不可删除
			if (this._isoItem.outOfArea() || this._isoItem.isoUiSprite != null) {
				
				DisplayManager.uiSprite.mouseChildren=
				DisplayManager.uiSprite.mouseEnabled = true;
				
				//飘字显示不可移下
				//DisplayManager.showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, LocaleWords.getInstance().getWord("doorCantMove"));
				
				//移动物件回原位或回箱子
				_isoItem.move(oldPos);
				//_isoItem.finishMove();
				
				//换回editAction
				remove();
				//更换action
				switch (type) 
				{
					case TYPE_ADD:
						super.remove();
						new MouseDefaultAction(state);
					break;
					
					case TYPE_MOVE:
						super.remove();
						new MouseEditAction(state);
					break;
				}
				
				EventManager.getInstance().dispatchEvent(new DEvent("diyCancelPutItem", { cid:_isoItem.data.cid } ));
				return;
			}
			
            if (this._isoItem.positionIsValid())
            {
				
				DisplayManager.uiSprite.mouseChildren=
				DisplayManager.uiSprite.mouseEnabled = true;
				
				if (_isoItem is FurnaceDecor) 
				{
					var container:Sprite = DataManager.getInstance().worldState.view.isoView.getLayer(0);
					var p:Point = new Point(_isoItem.asset.x, _isoItem.asset.y);
					p = _isoItem.asset.parent.localToGlobal(p);
					p = container.globalToLocal(p);
					var tmpmc:McShower = new McShower(furnaceDiyMv, container);
					tmpmc.x = p.x;
					tmpmc.y = p.y;
					
				}
				
				//if (!(_isoItem is WallDecor)) 
				//{
					//_isoItem.view.container.y = -4;
					//_isoItem.view.container.vy = -4;
					//state.physicsControl.physicsFun(_isoItem);
				//}
				//
				//_isoItem.physics = true;
				remove();
				
				//替换地板或墙纸
				changeView();
				
				recodeChange();
				
				//更换action
				switch (type) 
				{
					case TYPE_ADD:
						super.remove();
						new MouseDefaultAction(state);
					break;
					
					case TYPE_MOVE:
						super.remove();
						new MouseEditAction(state);
					break;
				}
                
            }
			
            return;
        }
		
		private function changeView():void {
			//更换全部地板或墙
			if (_isoItem is Wall) 
			{
				(state.world as MagicWorld).changeAllWall(_isoItem.data.cid);
				_isoItem.remove();
			}else if (_isoItem is Tile) 
			{
				(state.world as MagicWorld).changeAllTile(_isoItem.data.cid);
				_isoItem.remove();
			}
		}
		
		override public function remove($stack_flg:Boolean = true) : void
        {
			//物件正常化
            this._isoItem.finishMove();
			_isoItem.mouseEnabled = true;
			
            return;
        }
		
		private function recodeChange():void {
			switch (type) 
			{
				case TYPE_ADD:
					requestAdd();
				break;
				
				case TYPE_MOVE:
					requestMove();
				break;
			}
		}
		
		private function requestMove():void 
		{
			var command:SaveDiyMoveCommand = new SaveDiyMoveCommand();
			command.addEventListener(Event.COMPLETE, requestMove_complete);
			var tmp:DecorVo = _isoItem.data as DecorVo;
			command.decorVo = tmp;
			command.save(tmp.id, _isoItem.x, _isoItem.z, _isoItem.mirror);
		}
		
		private function requestMove_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, requestMove_complete);
			var command:SaveDiyMoveCommand = e.target as SaveDiyMoveCommand;
			EventManager.getInstance().dispatchEvent(new DEvent("diyPutItem", { cid:command.decorVo.cid, id:command.decorVo.id } ));
		}
		
		private function requestAdd():void 
		{
			var command:SaveDiyAddCommand = new SaveDiyAddCommand();
			command.addEventListener(Event.COMPLETE, requestAdd_complete);
			var tmp:DecorVo = _isoItem.data as DecorVo;
			command.add(tmp.cid, _isoItem.x, _isoItem.z, _isoItem.mirror,_isoItem.data as DecorVo);
		}
		
		private function requestAdd_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, requestAdd_complete);
			var command:SaveDiyAddCommand = e.target as SaveDiyAddCommand;
			EventManager.getInstance().dispatchEvent(new DEvent("diyPutItem", { cid:command.decor.cid, id:command.decor.id } ));
		}
		
        override public function onMouseMove(event:MouseEvent) : void
        {
            _isoItem.move(worldPosition());
        }
		
	}

}