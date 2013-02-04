package happymagic.roleInfo.view 
{
	import flash.geom.Rectangle;
	import happyfish.display.view.IconView;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.model.vo.classVo.EquipmentClassVo;
	import happymagic.roleInfo.view.ui.EquipTipUI;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class EquipTip extends EquipTipUI 
	{
		private var icon:IconView;
		
		public function EquipTip() 
		{
			TextFieldUtil.autoSetTxtDefaultFormat(this);
			var rect:Rectangle = new Rectangle(border.x, border.y, border.width, border.height);
			icon = new IconView(rect.width, rect.height, rect);
			addChildAt(icon, getChildIndex(border));
			removeChild(border);
			
			mouseEnabled = false;
			mouseChildren = false;
		}
		
		public function setData(vo:EquipmentClassVo, wearZero:Boolean):void
		{
			nameTxt.text = vo.name;
			icon.setData(vo.className);
			wearZero &&= vo.maxWear != 0;
			
			
			// [物攻,物防,魔攻,魔防,速度]
			var props:Array = ["pa", "pd", "ma", "md", "speed"];
			var arr:Array = [];
			for (var i:int = 0; i < props.length; i++)
			{
				arr[i] = { key:props[i], value:vo[props[i]] };
			}
			arr.sortOn("value", Array.NUMERIC|Array.DESCENDING);
			for (i = 0; i < 3; i++)
			{
				if (0 == vo[arr[i].key])
				{
					this["icon" + i].visible = false;
					this["txt" + i].visible = false;
				}else
				{
					this["icon" + i].gotoAndStop(arr[i].key);
					this["txt" + i].text = wearZero ? "0" : vo[arr[i].key] + "";
				}
			}
		}
		
	}

}