package happyfish.editer.control 
{
	import com.friendsofed.isometric.IsoUtils;
	import com.friendsofed.isometric.Point3D;
	import flash.events.Event;
	import flash.geom.Point;
	import happyfish.editer.model.EditDataManager;
	import happyfish.editer.model.vo.MapClassVo;
	import happyfish.editer.scene.view.EditIsoItem;
	import happyfish.editer.view.Main;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoUtil;
	/**
	 * ...
	 * @author 
	 */
	public class EditDragIsoItemCommand 
	{
		private var item:EditIsoItem;
		private var main:Main;
		protected var pos:Point3D;
		protected var oldPos:Point3D;
		
		public function EditDragIsoItemCommand(item:EditIsoItem,main:Main) 
		{
			this.main = main;
			this.item = item;
			
			initPos();
			item.asset.addEventListener(Event.ENTER_FRAME, dragFun);
		}
		
		public function stop():void 
		{
			item.asset.removeEventListener(Event.ENTER_FRAME, dragFun);
			item = null;
		}
		
		private function dragFun(e:Event):void 
		{
			initPos();
		}
		
		private function initPos():void
		{
			oldPos = pos;
			var p:Point = new Point(main.mapSprite.stage.mouseX - 32, main.mapSprite.stage.mouseY);
			p = main.mapSprite.gridView.globalToLocal(p);
			pos = IsoUtil.isoToGrid(IsoUtil.screenToIso(p));
			
			//trace(pos);
			
			if (main.mapSprite.itemContainer.getItemByPos(pos.x,pos.z)) 
			{
				return;
			}else {
				main.mapSprite.itemContainer.moveItemByPos(item,pos.x,pos.z,item.sortPriority);
			}
			
			trace(pos,item.name,item.data.name);
			
			//item.setIsoPos(p.x,0,p.y);
			//var p:Point = IsoUtils.isoToScreen(IsoUtil.gridToIso(new Point3D(data.x, 0, data.z)));
			//item.asset.x = p.x;
			//item.asset.y = p.y;
		}
		
		public function get mapClass():MapClassVo {
			return EditDataManager.getInstance().curMapClass;
		}
	}

}