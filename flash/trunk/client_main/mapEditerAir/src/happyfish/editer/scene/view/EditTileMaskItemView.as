package happyfish.editer.scene.view 
{
	/**
	 * ...
	 * @author 
	 */
	public class EditTileMaskItemView extends EditTileItemView 
	{
		
		public function EditTileMaskItemView($data:Object,_container:EditIsoLayer,__callBack:Function=null) 
		{
			super($data, _container, __callBack);
			sortPriority = 1;
		}
		
	}

}