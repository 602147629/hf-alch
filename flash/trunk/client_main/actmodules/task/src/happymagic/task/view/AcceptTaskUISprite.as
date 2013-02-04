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
	import happymagic.task.commands.AcceptTaskCommand;
	import happymagic.task.view.render.AwardItem;
	import happymagic.task.view.render.ConditionItem;
	import happymagic.task.view.render.ConditionRender;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class AcceptTaskUISprite extends UISprite 
	{
		private var taskId:int;
		private var iview:AcceptTaskUI;
		
		private var conditionListView:DefaultListView;
		private var awardListView:DefaultListView;
		private var npcIcon:IconView;
		
		public function AcceptTaskUISprite() 
		{
			iview = new AcceptTaskUI();
			_view = iview;
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			
			var rect:Rectangle = new Rectangle(iview.border.x, iview.border.y, iview.border.width, iview.border.height);
			npcIcon = new IconView(rect.width, rect.height, rect);
			iview.addChild(npcIcon);
			iview.removeChild(iview.border);
			
			conditionListView = new DefaultListView(iview, iview, 3);
			conditionListView.init(327, 123, 300, 35, -60, -65);
			conditionListView.setGridItem(ConditionItem, ConditionRender);
			
			awardListView = new DefaultListView(iview, iview, 4);
			awardListView.init(327, 80, 60, 80, -73, 113);
			awardListView.setGridItem(AwardItem, DefaultAwardItemRender);
			
			iview.addEventListener(MouseEvent.CLICK, clickHandler);
		}
		
		public function setData(task:TaskVo):void
		{
			taskId = task.id;
			
			iview.nameTxt.text = task.name;
			iview.contentTxt.text = task.content;
			iview.npcNameTxt.text = task.npcName;
			iview.npcChatTxt.text = task.npcChatAccept;
			
			npcIcon.setData(task.npcFaceClass);
			conditionListView.setData(task.conditions || []);
			awardListView.setData(task.awards || []);
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			switch(e.target)
			{
				case iview.acceptBtn :
					new AcceptTaskCommand().acceptTask(taskId);
					closeMe();
					break;
					
				case iview.closeBtn :
					closeMe();
					break;
			}
		}
		
	}

}