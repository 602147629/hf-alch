package happymagic.display.view.promptFrame 
{
	import flash.display.MovieClip;
	import happyfish.display.ui.GridItem;
	import happymagic.display.view.ui.DefaultAwardItemRender;
	import happymagic.model.vo.ConditionVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class GetNewItemsItem extends GridItem 
	{
		private var iview:DefaultAwardItemRender;
		
		public function GetNewItemsItem(ui:MovieClip) 
		{
			super(ui);
			iview = DefaultAwardItemRender(ui);
		}
		
		override public function setData(value:Object):void 
		{
			var vo:ConditionVo = ConditionVo(value);
			
			iview.icon.setCondition(vo);
			iview.nameTxt.text = vo.getName();
			iview.removeChild(iview.numTxt);
		}
		
		
	}

}