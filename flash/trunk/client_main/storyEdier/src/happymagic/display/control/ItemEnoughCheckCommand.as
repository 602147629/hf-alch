package happymagic.display.control 
{
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.AlginType;
	import happymagic.display.view.ModuleDict;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ItemClassVo;
	import happymagic.model.vo.MiningType;
	/**
	 * 检查是否有足够的道具或装饰物 没有的话自动弹框提示 框的确定按钮链接到合成面板
	 * @author XiaJunJie
	 */
	public class ItemEnoughCheckCommand 
	{
		
		public function ItemEnoughCheckCommand() 
		{
			
		}
		
		public function check(id:int,num:int,type:uint):Boolean
		{
			var msg:String;
			if (type == ConditionType.ITEM)
			{
				if (!(DataManager.getInstance().getEnoughItems([[id,num]])))
				{
					var itemClassVo:BaseItemClassVo = DataManager.getInstance().itemData.getItemClass(id);
					var itemName:String = itemClassVo ? itemClassVo.name : "unknown";
					msg = LocaleWords.getInstance().getWord("notEnoughItems", String(num == 0 ? 1:num), itemName);
					DisplayManager.showSysMsg(msg,1,-1,gotoMergeItem);
					return false;
				}
				return true;
			}
			
			return false;
		}
		
		private function gotoMergeItem():void
		{
			//var compoundTotalView:CompoundTotalView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_COMPOUNDTOTAL, ModuleDict.MODULE_COMPOUNDTOTAL_CLASS, false, AlginType.CENTER, 0, 0) as CompoundTotalView;
			//compoundTotalView.setData(MixAndEquipmentType.ITEM,MiningType.ITEM);
			//DisplayManager.uiSprite.setBg(compoundTotalView);
		}
	}

}