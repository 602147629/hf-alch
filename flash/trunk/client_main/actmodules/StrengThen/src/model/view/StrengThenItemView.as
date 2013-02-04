package model.view 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import happyfish.display.ui.GridItem;
	import happyfish.manager.EventManager;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author ZC
	 */
	public class StrengThenItemView extends GridItem
	{
		private var iview:StrengThenItemViewUi;
		private var num:int;
		public function StrengThenItemView(_uiview:MovieClip) 
		{
			super(_uiview);
			
			iview = _uiview as StrengThenItemViewUi;
			
			iview.mouseChildren = true;
			iview.mouseEnabled = true;
			
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			
			EventManager.getInstance().addEventListener(StrengThenEvent.STRENGTHENCHANGE, strengthenchangecomplete);
		}
		
		private function strengthenchangecomplete(e:StrengThenEvent):void 
		{
			if (DataManager.getInstance().getVar("StrengThenSelectId") == num)
			{
				iview.yes.visible = true;
			}
			else
			{
				iview.yes.visible = false;
			}
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "mc":
					     DataManager.getInstance().setVar("StrengThenSelectId", num);
					     EventManager.getInstance().dispatchEvent(new StrengThenEvent(StrengThenEvent.STRENGTHENCHANGE));
					break;
			}
		}

		override public function setData(_data:Object):void
		{
			num = _data as int;
			
			iview.icon.gotoAndStop(num);
			
			if (DataManager.getInstance().getVar("StrengThenSelectId") == num)
			{
				iview.yes.visible = true;
			}
			else
			{
				iview.yes.visible = false;
			}
		}
		
	}

}