package  
{
	import manager.StrengThenManager;
	import model.vo.StrengThenVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class test 
	{
		
		public function test() 
		{
			StrengThenManager.getInstance().strengThenArr = new Array();
			var vo:StrengThenVo;
			vo = new StrengThenVo();
			vo.id = 0;
			vo.ma = 12;
			vo.md = 14;
			vo.pa = 17;
			vo.pd = 20;
			vo.speed = 30;
			StrengThenManager.getInstance().strengThenArr.push(vo);
			
			vo = new StrengThenVo();
			vo.id = 100;
			vo.ma = 112;
			vo.md = 114;
			vo.pa = 117;
			vo.pd = 220;
			vo.speed = 30;
			StrengThenManager.getInstance().strengThenArr.push(vo);
			
			vo = new StrengThenVo();
			vo.id = 101;
			vo.ma = 212;
			vo.md = 214;
			vo.pa = 217;
			vo.pd = 320;
			vo.speed = 30;
			StrengThenManager.getInstance().strengThenArr.push(vo);			
		}
		
	}

}