package happymagic.model.command 
{
	import adobe.utils.CustomActions;
	import com.adobe.serialization.json.JSON;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.model.vo.GuidesVo;
	import happymagic.manager.DataManager;
	import happymagic.model.MagicUrlLoader;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.DiaryVo;
	import happymagic.model.vo.IllustrationsVo;
	import happymagic.model.vo.ItemVo;
	import happymagic.model.vo.UserVo;
	import happymagic.model.vo.WorldMapVo;
	
	/**
	 * 场景,用户信息初始化
	 * @author slam
	 */
	public class initCommand extends BaseDataCommand
	{
		
		public function initCommand() 
		{
			playStory = false;
		}
		
		public function load():void {
			
			createLoad();
			
			createRequest(InterfaceURLManager.getInstance().getUrl('loadinit'), { tmp:1 } );
			var loader:MagicUrlLoader = new MagicUrlLoader();
			loader.retry = true;
			loader.addEventListener(Event.COMPLETE, load_complete);
			
			
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			var tmdm:DataManager = DataManager.getInstance();
			
			//赋值userVo
			DataManager.getInstance().currentUser = new UserVo().setData(objdata.user) as UserVo;
			
			var i:int;
			
			//拥有的道具
			if (objdata.items) 
			{
				DataManager.getInstance().itemData.setItemList(objdata.items);
			}
			
			// 拥有的合成术
			if (objdata.mixs)
			{
				DataManager.getInstance().mixData.setMixCidList(objdata.mixs);
			}
			
			// 正在进行的合成术
			if (objdata.curMixs)
			{
				DataManager.getInstance().mixData.setCurMixList(objdata.curMixs);
			}
			
			//角色信息
			if (objdata.roles) 
			{
				DataManager.getInstance().roleData.setRoles(objdata.roles);
			}
			
			//任务
			if (objdata.tasks) 
			{
				DataManager.getInstance().taskData.setTasks(objdata.tasks);
			}
			
			//日志
			if (objdata.diarys) 
			{
				
				
			}
			
			//引导状态
			tmdm.guides = new Array();
			if (objdata.guides) 
			{
				for (i = 0; i < objdata.guides.length; i++) 
				{
					tmdm.guides.push(new GuidesVo().setValue(objdata.guides[i]));
				}
			}
			
			//活动模块数据
			tmdm.acts = new Array();
			if (objdata.acts) 
			{
				for (var j:int = 0; j < objdata.acts.length; j++) 
				{
					tmdm.acts.push(new ActVo().setData(objdata.acts[j]));
				}
			}
			
			//世界地图动态数据						
			if (objdata.isnewworldmap) 
			{
               DataManager.getInstance().isNewWorldMap = 1;
			}
			
			//图鉴的动态数据
			if (objdata.illustrations)
			{
				
				for (i = 0; i < objdata.illustrations.length; i++) 
				{
					var vo:IllustrationsVo = new IllustrationsVo();
					vo.setData(objdata.illustrations[i]);
					vo.base = DataManager.getInstance().illustratedData.getIllustrationsClassVo(vo.cid);
					tmdm.illustratedData.illustratedHandbookInit.push(vo);
				}						
			}
			
			
			commandComplete();
		}
	}

}