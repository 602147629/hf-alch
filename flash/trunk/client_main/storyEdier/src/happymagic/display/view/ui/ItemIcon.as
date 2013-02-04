package happymagic.display.view.ui 
{
	import flash.geom.Rectangle;
	import happyfish.display.view.IconView;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class ItemIcon extends IconView 
	{
		
		public function ItemIcon(_w:Number=0, _h:Number=0, _rect:Rectangle=null, _autoCenter:Boolean=false) 
		{
			super(_w, _h, _rect, _autoCenter);
		}
		
		public function setCondition(vo:ConditionVo):void 
		{
			var className:String;
			var frame:String = null;
			switch (vo.type)
			{
				case ConditionType.ITEM :
					className = DataManager.getInstance().itemData.getItemClass(int(vo.id)).className;
					break;
				
				case ConditionType.USER :
					className = "conditionNeedIcon";
					frame = vo.id;
					break;
					
				case ConditionType.Mob :
					className = DataManager.getInstance().illustratedData.getIllustrationsClassByItemCid(int(vo.id)).className;
					break;
					
				case ConditionType.NONE :
					className = vo.id;
					break;
			}
			super.setData(className, frame);
		}
		
	}

}