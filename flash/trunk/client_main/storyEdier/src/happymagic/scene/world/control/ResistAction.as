package happymagic.scene.world.control 
{
	import happyfish.scene.world.WorldState;

	/**
	 * 反抗action
	 * @author 
	 */
	public class ResistAction extends MouseDefaultAction 
	{
		
		public function ResistAction(state:WorldState, stack_flg:Boolean = false) 
		{
			super(state, stack_flg);
		}
		
	}

}