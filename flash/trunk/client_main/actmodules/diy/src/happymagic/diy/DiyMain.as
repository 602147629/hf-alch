package happymagic.diy
{
	import com.friendsofed.isometric.Point3D;
	import flash.display.Sprite;
	import flash.events.Event;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.ModuleMvType;
	import happyfish.manager.module.vo.ModuleVo;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.grid.Tile;
	import happyfish.scene.world.grid.Wall;
	import happyfish.scene.world.WorldState;
	import happymagic.diy.control.MouseCarryIsoAction;
	import happymagic.diy.control.MouseEditAction;
	import happymagic.diy.control.MouseRemoveItemAction;
	import happymagic.diy.control.MouseRotationAction;
	import happymagic.diy.display.DiyMenuView;
	import happymagic.events.DiyEvent;
	import happymagic.events.MagicEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.ItemType;
	import happymagic.model.vo.ItemVo;
	import happymagic.scene.world.grid.item.Decor;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.grid.item.FurnaceDecor;
	import happymagic.scene.world.grid.item.WallDecor;

	/**
	 * ...
	 * @author 
	 */
	public class DiyMain extends Sprite 
	{
		private var diyMenu:DiyMenuView;

		public function DiyMain():void 
		{
			EventManager.getInstance().addEventListener(MagicEvent.UI_INIT, init);
		}

		private function init(e:MagicEvent):void 
		{
			e.target.removeEventListener(MagicEvent.UI_INIT, init);
			
			EventManager.getInstance().addEventListener(DiyEvent.ADD_ITEM, addItem);
			EventManager.getInstance().addEventListener(DiyEvent.REMOVE_ITEM, removeItem);
			EventManager.getInstance().addEventListener(DiyEvent.MOVE_ITEM, moveItem);
			EventManager.getInstance().addEventListener(DiyEvent.MIRROR_ITEM, mirrorItem);
			
			/*var module:ModuleVo = new ModuleVo();
			module.name = "diyMenu";
			module.className = "happymagic.diy.display.DiyMenuView";
			module.algin = AlginType.BR;
			module.x = 180;
			module.y = 180;
			
			
			diyMenu = ModuleManager.getInstance().addModule(module) as DiyMenuView;
			ModuleManager.getInstance().showModule("diyMenu");*/
			
			
		}
		
		private function moveItem(e:DiyEvent):void 
		{
			var state:WorldState = DataManager.getInstance().worldState;
			new MouseEditAction(state);
		}
		
		private function mirrorItem(e:DiyEvent):void 
		{
			var state:WorldState = DataManager.getInstance().worldState;
			new MouseRotationAction(state);
		}
		
		private function removeItem(e:DiyEvent):void 
		{
			var state:WorldState = DataManager.getInstance().worldState;
			new MouseRemoveItemAction(state);
		}
		
		private function addItem(e:DiyEvent):void 
		{
			var state:WorldState = DataManager.getInstance().worldState;
			var item:ItemVo = e.item;
			
			var itemdata:DecorVo = new DecorVo().setValue( { cid:item.cid, id:item.id,x:0,z:0,mirror:0 } );
			
			var isoItem:IsoItem;
			switch (item.base.type2) 
			{
				case ItemType.Decoration:
					isoItem = new Decor(itemdata, state, item_ready);
					isoItem.mouseEnabled = false;
			
					state.world.addItem(isoItem);
					isoItem.move(worldPosition());
					
					
					new MouseCarryIsoAction(isoItem, state, MouseCarryIsoAction.TYPE_ADD);
					isoItem.addIsoTile();
				break;
				
				case ItemType.Door:
					isoItem = new Door(itemdata, state, item_ready);
					isoItem.mouseEnabled = false;
					(isoItem as Door).diyState = true;
					
					state.world.addItem(isoItem);
					isoItem.move(worldPosition());
					isoItem.addIsoTile();
					
					new MouseCarryIsoAction(isoItem, state, MouseCarryIsoAction.TYPE_ADD);
				break;
				
				case ItemType.DecorOnWall:
					isoItem = new WallDecor(itemdata, state, item_ready);
					isoItem.mouseEnabled = false;
					
					state.world.addItem(isoItem);
					isoItem.move(worldPosition());
					isoItem.addIsoTile();
					
					new MouseCarryIsoAction(isoItem, state, MouseCarryIsoAction.TYPE_ADD);
				break;
				
				case ItemType.Wall:
					isoItem = new Wall(itemdata, state, item_ready);
					isoItem.mouseEnabled = false;
					
					state.world.addItem(isoItem);
					isoItem.move(worldPosition());
					
					
					new MouseCarryIsoAction(isoItem, state, MouseCarryIsoAction.TYPE_ADD);
					isoItem.addIsoTile();
				break;
				
				case ItemType.Floor:
					isoItem = new Tile(itemdata, state, item_ready);
					isoItem.mouseEnabled = false;
					
					state.view.isoView.backgroundContainer.addChild(isoItem.view.container);
					isoItem.move(worldPosition());
					
					new MouseCarryIsoAction(isoItem, state, MouseCarryIsoAction.TYPE_ADD);
				break;
				
				case ItemType.FurnaceType:
					isoItem = new FurnaceDecor(itemdata, state, item_ready);
					isoItem.mouseEnabled = false;
			
					state.world.addItem(isoItem);
					isoItem.move(worldPosition());
					
					
					new MouseCarryIsoAction(isoItem, state, MouseCarryIsoAction.TYPE_ADD);
					isoItem.addIsoTile();
				break;
				
			}
			
			
		}
		
		private function item_ready(target:IsoItem=null):void 
		{
			
		}
		
		/**
		 * 返回grid坐标
		 * @return
		 */
		public function worldPosition():Point3D
		{
			return DataManager.getInstance().worldState.view.targetGrid();
		}

	}

}