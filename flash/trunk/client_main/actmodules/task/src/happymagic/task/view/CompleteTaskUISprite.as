package happymagic.task.view 
{
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.display.view.ui.DefaultAwardItemRender;
	import happymagic.model.vo.task.TaskVo;
	import happymagic.task.view.render.AwardItem;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class CompleteTaskUISprite extends UISprite 
	{
		private var taskId:int;
		private var iview:CompleteTaskUI;
		
		private var awardListView:DefaultListView;
		private var npcIcon:IconView;
		
		public function CompleteTaskUISprite() 
		{
			iview = new CompleteTaskUI();
			_view = iview;
			
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			
			var rect:Rectangle = new Rectangle(iview.border.x, iview.border.y, iview.border.width, iview.border.height);
			npcIcon = new IconView(rect.width, rect.height, rect);
			iview.addChild(npcIcon);
			iview.removeChild(iview.border);
			
			awardListView = new DefaultListView(iview, iview, 4);
			awardListView.init(327, 80, 42, 61, -72, 40);
			awardListView.setGridItem(AwardItem, DefaultAwardItemRender);
			
			iview.addEventListener(MouseEvent.CLICK, clickHandler);
		}
		
		public function setData(task:TaskVo):void
		{
			taskId = task.id;
			
			iview.nameTxt.text = task.name;
			iview.npcNameTxt.text = task.npcName;
			iview.npcChatTxt.text = task.npcChatComplete;
			
			npcIcon.setData(task.npcFaceClass);
			awardListView.setData(task.awards || []);
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			switch(e.target)
			{
				case iview.okBtn :
				case iview.closeBtn :
					closeMe();
					break;
			}
		}
	}

}