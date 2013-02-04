package happymagic.display.view.maininfo 
{
	import happyfish.display.view.PerBarView;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.LevelInfoVo;
	import happymagic.model.vo.UserVo;
	/**
	 * ...
	 * @author 
	 */
	public class MainInfoHomeView extends homeInfoUi 
	{
		private var expBar:PerBarView;
		
		public function MainInfoHomeView() 
		{
			expFlashMc.alpha = 0;
			levelTxt.mouseEnabled = false;
			
			expBar = new PerBarView(expBarUi, expBarUi.width);
			//expBar.minW = 2;
		}
		
		public function initInfo():void {
			var data:UserVo = DataManager.getInstance().currentUser;
			var nextlevel:LevelInfoVo = DataManager.getInstance().getLevelInfo(data.level + 1);
			
			levelTxt.text = data.level.toString();
			expTxt.text = data.exp + "/" + nextlevel.maxExp;
			
			expBar.maxValue = nextlevel.maxExp;
			expBar.setData(data.exp);
		}
		
		public function show():void {
			visible = true;
		}
		
		public function hide():void {
			visible = false;
		}
		
	}

}