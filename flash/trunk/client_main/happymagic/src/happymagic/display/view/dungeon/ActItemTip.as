package happymagic.display.view.dungeon 
{
	import flash.display.Sprite;
	import flash.geom.Point;
	import flash.text.TextField;
	import happymagic.scene.world.grid.person.ActItem;
	/**
	 * 交互对象TIP 2011.11.16
	 * @author XiaJunJie
	 */
	public class ActItemTip extends Sprite
	{
		private var nameTxt:TextField;
		private var hpTxt:TextField;
		private var hpBar:Sprite;
		
		public function ActItemTip() 
		{
			nameTxt = new TextField;
			nameTxt.text = "name";
			nameTxt.height = nameTxt.textHeight+4;
			addChild(nameTxt);
			
			hpTxt = new TextField;
			hpTxt.text = "hp";
			hpTxt.height = hpTxt.textHeight+4;
			addChild(hpTxt);
			hpTxt.y = nameTxt.height;
			
			var barBg:Sprite = new Sprite;
			with (barBg.graphics)
			{
				beginFill(0xDDDDDD);
				drawRect(0, 0, 100, 12);
				endFill();
			}
			addChild(barBg);
			barBg.y = this.height;
			
			hpBar = new Sprite;
			with (hpBar.graphics)
			{
				beginFill(0xFF6600);
				drawRect(0, 0, 100, 10);
				endFill();
			}
			addChild(hpBar);
			hpBar.y = barBg.y + 1;
			
			var bg:Sprite = new Sprite;
			with (bg.graphics)
			{
				beginFill(0xFFFFFF);
				drawRect(0, 0, 100, this.height);
				endFill();
			}
			addChildAt(bg, 0);
		}
		
		public function show(actItem:ActItem):void
		{
			nameTxt.text = actItem.vo.name;
			hpTxt.text = actItem.currentHp + "/" + actItem.vo.maxHp;
			hpBar.width = actItem.currentHp / actItem.vo.maxHp * 100;
			
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