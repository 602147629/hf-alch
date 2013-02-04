package happyfish.storyEdit.control 
{
	import com.greensock.plugins.RemoveTintPlugin;
	import fl.controls.DataGrid;
	import fl.controls.dataGridClasses.DataGridColumn;
	import fl.controls.ScrollPolicy;
	import fl.data.DataProvider;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.MouseEvent;
	import happyfish.storyEdit.display.ActionRender;
	import happyfish.storyEdit.display.ActorRender;
	import happyfish.storyEdit.model.EditDataManager;
	import happyfish.storyEdit.model.vo.EditStoryActionVo;
	import happyfish.storyEdit.model.vo.EditStoryActorVo;
	import happyfish.storyEdit.model.vo.EditStoryGridVo;
	import happyfish.storyEdit.model.vo.EditStoryVo;
	
	
	/**
	 * ...
	 * @author jj
	 */
	public class StoryActionGridControl extends EventDispatcher 
	{
		public var actorColum:DataGridColumn;
		public var storyActionGrid:DataGrid;
		public var allActions:Vector.<EditStoryActionVo>;
		public var actionColum:DataGridColumn;
		public var storyGridData:Array;//表格用的数据
		private var curStoryVo:EditStoryVo;
		private var dm:EditDataManager;
		
		public function StoryActionGridControl(grid:DataGrid,curStoryVo:EditStoryVo) 
		{
			this.curStoryVo = curStoryVo;
			storyActionGrid = grid;
			
			dm = EditDataManager.getInstance();
			
			storyActionGrid.addEventListener(MouseEvent.CLICK, storyActionGrid_select);
			storyActionGrid.horizontalScrollPolicy = ScrollPolicy.AUTO;
			storyActionGrid.verticalScrollPolicy = ScrollPolicy.AUTO;
			
			actorColum = new DataGridColumn("角色");
			actorColum.cellRenderer = ActorRender;
			actorColum.sortable = false;
			actorColum.width = 70;
		}
		
		private function storyActionGrid_select(e:Event):void 
		{
			var render:ActionRender = e.target as ActionRender;
			if (!render) 
			{
				return;
			}
			
			var index:int = render.listData.index;
			var column:int = render.columnIndex;
			//trace(column);
			storyActionGrid.horizontalScrollBar.scrollPosition;
			var action:EditStoryActionVo = storyActionGrid.selectedItem.actions[column];
			//trace(storyActionGrid.selectedItem.actions.length,storyActionGrid.getColumnAt(column).headerText)
			if (action) 
			{
				
			}else {
				createAction(index,column,render.data as EditStoryGridVo);
			}
		}
			
		/**
		 * 调出面板创建一个动作到指定格
		 * @param	index
		 * @param	column
		 */
		private function createAction(index:int, column:int,gridVo:EditStoryGridVo):void 
		{
			var action:EditStoryActionVo = new EditStoryActionVo();
			action.npcId = gridVo.actorId;
			
			storyActionGrid.selectedItem.actions.push(action);
			allActions.push(action);
			initGridView();
		}
		
		public function initGrid():void 
		{
			//整理数据
			storyGridData = new Array();
			
			allActions = new Vector.<EditStoryActionVo>;
			
			var tmparr:Array;
			var tmpdata:EditStoryGridVo;
			for (var i:int = 0; i < curStoryVo.actorList.length; i++) 
			{
				tmpdata = new EditStoryGridVo();
				tmpdata.actorId = curStoryVo.actorList[i].id;
				tmpdata.actor = dm.getClassFrom("actorList", "id", tmpdata.actorId) as EditStoryActorVo;
				tmpdata.actions = dm.getArrayFromVars("actions", "npcId", tmpdata.actorId);
				allActions = allActions.concat(tmpdata.actions);
				tmpdata.index = i;
				
				storyGridData.push(tmpdata);
			}
			
			initGridView();
		}
		
		public function initGridView():void {
			storyActionGrid.removeAllColumns();
			storyActionGrid.removeAll();
			
			storyActionGrid.addColumn(actorColum);
			
			var tmpcolum:DataGridColumn;
			for (var j:int = 0; j < allActions.length+1; j++) 
			{
				tmpcolum = new DataGridColumn(j.toString());
				tmpcolum.cellRenderer = ActionRender;
				tmpcolum.sortable = false;
				tmpcolum.width = 30;
				tmpcolum.minWidth = 30;
				
				storyActionGrid.addColumn(tmpcolum);
			}
			
			storyGridData.sortOn("index", Array.NUMERIC | Array.DESCENDING);
			storyActionGrid.dataProvider = new DataProvider(storyGridData);
			
			//storyActionGrid.horizontalScrollBar.setScrollPosition(storyActionGrid.horizontalScrollBar.maxScrollPosition+100);
			//storyActionGrid.horizontalScrollPosition = 0;
			//storyActionGrid.verticalScrollPosition = 0;
			
			
		}
		
		public function refreshGrid():void {
			storyGridData.sortOn("index", Array.NUMERIC | Array.DESCENDING);
			storyActionGrid.dataProvider = new DataProvider(storyGridData);
			
		}
	}

}