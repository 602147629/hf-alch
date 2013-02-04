package happymagic.task.view.render 
{
	import flash.display.MovieClip;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.Tooltips;
	import happymagic.display.view.ui.DefaultAwardItemRender;
	import happymagic.display.view.ui.ItemIcon;
	import happymagic.model.vo.ConditionVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class AwardItem extends GridItem 
	{
		private var ivew:DefaultAwardItemRender;
		private var itemIcon:ItemIcon;
		
		public function AwardItem(ui:MovieClip) 
		{
			super(ui);
			ivew = ui as DefaultAwardItemRender;
		}
		
		override public function setData(value:Object):void 
		{
			var vo:ConditionVo = value as ConditionVo;
			ivew.icon.setCondition(vo);
			ivew.numTxt.text = vo.num + "";
			ivew.nameTxt.text = vo.getName();
			Tooltips.getInstance().register(ivew, ivew.nameTxt.text, Tooltips.getInstance().getBg("defaultBg"));
		}
		
	}

}