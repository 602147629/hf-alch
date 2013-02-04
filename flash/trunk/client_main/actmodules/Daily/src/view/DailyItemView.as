package view 
{
	import command.DeletediaryCommand;
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.ui.GridItem;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.model.vo.DiaryVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class DailyItemView extends GridItem
	{
		private var iview:DailyItemViewUi;
		private var data:DiaryVo;		
		
		public function DailyItemView(_uiview:MovieClip) 
		{
			super(_uiview);
			iview = _uiview as DailyItemViewUi;	
			
			iview.mouseChildren = true;
			iview.mouseEnabled = true;
			
			iview.addEventListener(MouseEvent.CLICK, clickrun);
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "deletebtn":
					  iview.mouseChildren = false;
					  iview.mouseEnabled = false;
					  var Deletediarycommand:DeletediaryCommand = new DeletediaryCommand();
					  Deletediarycommand.setData(data.type);
					  Deletediarycommand.addEventListener(Event.COMPLETE, commandcomplete);
					  
					break;
			}
		}
		
		private function commandcomplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, commandcomplete);
			iview.mouseChildren = true;
			iview.mouseEnabled = true;			
			EventManager.getInstance().dispatchEvent(new DailyEvent(DailyEvent.DAILYDELETE));
		}
		
		override public function setData(_data:Object):void
		{
			data = _data as  DiaryVo;
			
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			
			iview.nametxt.text = data.uName;
			iview.content.htmlText = data.content;
			
			iview.timetxt.text = data.createTime.fullYear + LocaleWords.getInstance().getWord("year") + (data.createTime.month+1) + LocaleWords.getInstance().getWord("month") + data.createTime.day + LocaleWords.getInstance().getWord("day2") + data.createTime.hours + LocaleWords.getInstance().getWord("hour") + data.createTime.minutes + LocaleWords.getInstance().getWord("minutes");
			
		}			
		
	}

}