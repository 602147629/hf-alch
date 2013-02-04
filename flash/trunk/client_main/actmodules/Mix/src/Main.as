package 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import happymagic.manager.DataManager;
	import happymagic.mix.view.MixUISprite;
	import happymagic.model.vo.classVo.FurnaceClassVo;
	import happymagic.model.vo.classVo.MixClassVo;
	import happymagic.model.vo.classVo.StuffClassVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class Main extends Sprite 
	{
		
		public function Main():void 
		{
			if (stage) init();
			else addEventListener(Event.ADDED_TO_STAGE, init);
		}
		
		private function init(e:Event = null):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, init);
			// entry point
			try {
				
			var uis:MixUISprite = new MixUISprite();
			addChild(uis.view);
			uis.view.x = (stage.stageWidth) / 2;
			uis.view.y = (stage.stageHeight) / 2;
			var mixList:Array = [];
			var mixCidList:Array = [];
			for (var i:int = 1; i < 100; i++)
			{
				mixList.push({ cid:i * 100, name:"合成" + i, furnaceCid:141, itemCid:i * 100 + 11, time:1000,
							  probability:50,probabilityInterval:1,
							  needs:[[211,3]]
							} );
				mixCidList.push(i * 100);
			}
			DataManager.getInstance().mixData.setMixClassList(mixList);
			DataManager.getInstance().mixData.setMixCidList(mixCidList);
			var itemClassList:Array = [ { cid: 141, name:"炼金炉", types:[11, 12] } ];
			
			for (i = 1; i < 100; i++)
			{
				itemClassList.push( {
					cid:100 * i + 11,
					name:"材料" + i,
					gem:2,
					canBuy:1,
					worth:int(Math.random()*1000)
				});
			}
			DataManager.getInstance().itemData.setItemClassList(itemClassList);
			uis.showMixByMid(100);
			
			}catch (err:Error)
			{
				trace(err.getStackTrace());
			}
		}
		
	}
	
}