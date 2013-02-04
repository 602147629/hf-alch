package happyfish.editer.scene.view 
{
	import happyfish.cacher.CacheSprite;
	/**
	 * ...
	 * @author ...
	 */
	public class EditTileItemView extends EditIsoItem
	{
		private var callback2:Function;
		public function EditTileItemView($data:Object,_container:EditIsoLayer,__callBack:Function=null) 
		{
			super($data, _container, __callBack);	
		}
		
		override public function get depth():Number 
		{
			return super.depth+sortPriority;
		}
		
		override public function resetView(className:String = "", _callBack:Function = null):void 
		{
			super.resetView(className, _callBack);
		}
	}

}