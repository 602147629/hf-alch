package  
{
	import happymagic.manager.DataManager;
	import happymagic.model.vo.DiaryVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class test 
	{
		
		public function test() 
		{
			var vo:DiaryVo;
			var arr:Array = new Array();
			vo = new DiaryVo();
			vo.content = "asdsadasddas";
			vo.uName = "11111111111111111";
			vo.type = 1;
			arr.push(vo);
			
			vo = new DiaryVo();
			vo.content = "asdsadasddas";
			vo.uName = "33333333333";
			vo.type = 1;
			arr.push(vo);
			
			vo = new DiaryVo();
			vo.content = "asdsadasddas";
			vo.uName = "333333333333";
			vo.type = 1;			
			arr.push(vo);
			
			vo = new DiaryVo();
			vo.content = "asdsadasddas";
			vo.uName = "444444444444";
			vo.type = 1;			
			arr.push(vo);			
			DataManager.getInstance().diarys = arr;
		}
		
	}

}