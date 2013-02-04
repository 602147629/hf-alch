package happymagic.display.view.dungeon 
{
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.text.TextFieldAutoSize;
	import happymagic.manager.DataManager;
	import happymagic.scene.world.grid.person.ActItem;
	import happymagic.scene.world.grid.person.Monster;
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class DungeonTip extends ui_DungeonTip
	{
		
		public function DungeonTip() 
		{
			this.mouseChildren = this.mouseEnabled = false;
			txt.autoSize = TextFieldAutoSize.CENTER;
		}
		
		public function show(actItem:ActItem):void
		{
			if (actItem is Monster)
			{
				var level:int = actItem.vo["level"];
				txt.text = "LV:" + level + " " + actItem.vo.name;
				var delta:int = level - DataManager.getInstance().roleData.getRole(0).level;
				
				if (delta < 2) txt.textColor = 0xFFFFFF;
				else if (delta >= 2 && delta <4) txt.textColor = 0xFFCC00;
				else if (delta >= 4) txt.textColor = 0xFF0000;
			}
			else
			{
				txt.textColor = 0xFFFFFF;
				txt.text = actItem.vo.name + " " + actItem.currentHp + "/" + actItem.vo.maxHp;
			}
			
			var pos:Point = actItem.view.container.localToGlobal(new Point);
			x = pos.x - this.width/2;
			y = pos.y - actItem.view.container.height - this.height;
			
			visible = true;
		}
		
		public function hide():void
		{
			visible = false;
		}
		
	}

}