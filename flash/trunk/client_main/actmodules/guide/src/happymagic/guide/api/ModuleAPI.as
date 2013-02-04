package happymagic.guide.api 
{
	import flash.display.MovieClip;
	import happyfish.manager.module.interfaces.IModule;
	import happyfish.manager.module.ModuleManager;
	/**
	 * ...
	 * @author lite3
	 */
	public class ModuleAPI 
	{
		
		public function ModuleAPI() 
		{
			
		}
		
		public function getModule(name:String):IModule
		{
			return ModuleManager.getInstance().getModule(name) as IModule;
		}
		
		public function getView(name:String):MovieClip
		{
			var ui:IModule = getModule(name);
			return ui ? ui.view : null;
		}
		
	}

}