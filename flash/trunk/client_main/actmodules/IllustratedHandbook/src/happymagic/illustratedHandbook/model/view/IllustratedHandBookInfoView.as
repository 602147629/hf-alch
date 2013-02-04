package happymagic.illustratedHandbook.model.view 
{
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	import happymagic.model.vo.classVo.IllustrationsClassVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class IllustratedHandBookInfoView extends IllustratedHandBookInfoViewUi
	{
		private var data:IllustrationsClassVo;
		public function IllustratedHandBookInfoView() 
		{
			addEventListener(MouseEvent.CLICK, clickrun);
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch (e.target.name)
			{
				case "make":
					
					break;
			}
		}
		
		public function setData(_data:IllustrationsClassVo):void
		{
			data = _data;
		}
		
	}

}