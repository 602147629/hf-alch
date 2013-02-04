package happyfish.storyEdit.display 
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.ui.defaultList.DefaultVLineListView;
	import happyfish.manager.EventManager;
	import happyfish.storyEdit.events.StoryEditEvent;
	import happyfish.storyEdit.model.EditDataManager;
	import happyfish.storyEdit.model.vo.EditStoryActionVo;
	import happyfish.storyEdit.model.vo.EditStoryActorVo;
	import happyfish.storyEdit.model.vo.EditStoryGridVo;
	import happyfish.storyEdit.model.vo.EditStoryVo;
	import xrope.HLineLayout;
	
	/**
	 * ...
	 * @author jj
	 */
	public class StoryActionGrid extends Sprite 
	{
		public var curStoryVo:EditStoryVo;
		private var actionList:EditActionVListView;
		private var actorList:DefaultVLineListView;
		//public var allActions:Vector.<EditStoryActionVo>;
		public var storyGridData:Array;
		private var dm:EditDataManager;
		public function StoryActionGrid() 
		{
			dm = EditDataManager.getInstance();
			
			actionList = new EditActionVListView(new actionsListUi, this, 6,false,false);
			actionList.tweenTime = 0;
			actionList.init(450, 180, 450, 22, 14, -64);
			actionList.setGridItem(StoryActionListItemView, MovieClip);
			actionList.x = 90;
			actionList.y = 75;
			
			actorList = new DefaultVLineListView(new actorListUi, this, 6,true);
			actorList.iview.addEventListener(MouseEvent.CLICK, pageClickFun);
			actorList.tweenTime = 0;
			actorList.init(73, 180, 73, 22, -36, 11);
			actorList.setGridItem(StoryActorListItemView, actorListItemUi);
			actorList.x = 36;
			
			
		}
		
		public function saveOutData():void {
			curStoryVo.actions = new Array();
			curStoryVo.actorList = new Array();
			
			var tmpdata:EditStoryGridVo;
			for (var i:int = 0; i < storyGridData.length; i++) 
			{
				tmpdata = storyGridData[i] as EditStoryGridVo;
				
				curStoryVo.actorList.push(tmpdata.actor);
				for (var j:int = 0; j < tmpdata.actions.length; j++) 
				{
					curStoryVo.actions.push(tmpdata.actions[j]);
				}
			}
		}
		
		/**
		 * GRID翻页操作
		 * @param	e
		 */
		private function pageClickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case actorList.iview["nextBtn"]:
					actionList.nextPage();
				break;
				
				case actorList.iview["prevBtn"]:
					actionList.prevPage();
				break;
			}
		}
		
		public function setData(story:EditStoryVo):void {
			curStoryVo = story;
			initGridData();
		}
		
		public function initGridData():void 
		{
			//整理数据
			storyGridData = new Array();
			
			//allActions = new Vector.<EditStoryActionVo>;
			
			var tmparr:Array;
			var tmpdata:EditStoryGridVo;
			for (var i:int = 0; i < curStoryVo.actorList.length; i++) 
			{
				tmpdata = new EditStoryGridVo();
				tmpdata.actorId = curStoryVo.actorList[i].id;
				tmpdata.actor = new EditStoryActorVo().setData(dm.getClassFrom("actorList", "id", tmpdata.actorId)) as EditStoryActorVo;
				tmpdata.actions = dm.getArrayFromVars("actions", "npcId", tmpdata.actorId);
				//allActions = allActions.concat(tmpdata.actions);
				tmpdata.index = i;
				
				storyGridData.push(tmpdata);
			}
			
			initGridView();
		}
		
		private function initGridView():void 
		{
			actionList.setData(storyGridData);
			actorList.setData(storyGridData);
		}
		
		public function addActor(actor:EditStoryActorVo):void {
			var tmpdata:EditStoryGridVo = new EditStoryGridVo();
			tmpdata.actor = actor;
			tmpdata.actorId = actor.id;
			tmpdata.actions = [];
			
			storyGridData.push(tmpdata);
			
			actorList.initPage();
			actionList.initPage();
		}
		
		public function removeActor(actorId:int):void {
			for (var i:int = 0; i < storyGridData.length; i++) 
			{
				if (actorId == storyGridData[i].actorId) 
				{
					storyGridData.splice(i, 1);
					
					actorList.initPage();
					actionList.initPage();
					return;
				}
			}
		}
		
		public function saveAction(actorId:int,index:int, action:EditStoryActionVo):void {
			var tmp:EditStoryGridVo;
			for (var i:int = 0; i < storyGridData.length; i++) 
			{
				tmp = storyGridData[i] as EditStoryGridVo;
				if (tmp.actorId == actorId) 
				{
					var checkIndex:int = dm.getIndexByKeyFromArr(tmp.actions, "index", index);
					if (checkIndex>-1) 
					{
						tmp.actions.splice(checkIndex, 1);
					}
					if (action) 
					{
						tmp.actions.push(action);
					}
					
					tmp.actions.sortOn("index", Array.NUMERIC | Array.DESCENDING);
					if (actionList.grid.data.length>0) 
					{
						dm.setVar("oldActionPage", (actionList.grid.data[0] as StoryActionListItemView).list.currentPage);
					}else {
						dm.setVar("oldActionPage", 1);
					}
					
					actionList.initPage();
					return;
				}
			}
		}
		
	}

}