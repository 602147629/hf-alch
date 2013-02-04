package  
{
	import happymagic.manager.DataManager;
	import happymagic.model.vo.classVo.IllustrationsClassVo;
	import happymagic.model.vo.IllustrationsVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class IllustratedHandbookUpdata 
	{
		private var data:Array = new Array();
		
		public function IllustratedHandbookUpdata() 
		{
			var vo:IllustrationsClassVo = new IllustrationsClassVo();
			
			vo = new IllustrationsClassVo();
			vo.type = 2;
			vo.type2 = 21;
			data.push(vo);	
			
			vo = new IllustrationsClassVo();
			vo.type = 2;
			vo.type2 = 22;
			data.push(vo);
			
			vo = new IllustrationsClassVo();
			vo.type = 1;
			vo.type2 = 11;
			data.push(vo);	
			
			vo = new IllustrationsClassVo();
			vo.type = 1;
			vo.type2 = 12;
			data.push(vo);			
			
			vo = new IllustrationsClassVo();
			vo.type = 1;
			vo.type2 = 13;
			data.push(vo);	
			
			vo = new IllustrationsClassVo();
			vo.type = 1;
			vo.type2 = 14;
			data.push(vo);				
			
			vo = new IllustrationsClassVo();
			vo.type = 1;
			vo.type2 = 15;
			data.push(vo);	
			
			vo = new IllustrationsClassVo();
			vo.type = 3;
			vo.type2 = 31;
			data.push(vo);				
			
			vo = new IllustrationsClassVo();
			vo.type = 3;
			vo.type2 = 32;
			data.push(vo);			

			vo = new IllustrationsClassVo();
			vo.type = 3;
			vo.type2 = 33;
			data.push(vo);
			
			vo = new IllustrationsClassVo();
			vo.type = 3;
			vo.type2 = 34;
			data.push(vo);			

			vo = new IllustrationsClassVo();
			vo.type = 4;
			vo.type2 = 41;
			data.push(vo);
			
			vo = new IllustrationsClassVo();
			vo.type = 5;
			vo.type2 = 51;
			data.push(vo);	
			
			vo = new IllustrationsClassVo();
			vo.type = 5;
			vo.type2 = 52;
			data.push(vo);			
			
			vo = new IllustrationsClassVo();
			vo.type = 5;
			vo.type2 = 53;
			data.push(vo);	
			
			vo = new IllustrationsClassVo();
			vo.type = 5;
			vo.type2 = 54;
			data.push(vo);				
			
			vo = new IllustrationsClassVo();
			vo.type = 5;
			vo.type2 = 55;
			data.push(vo);				
			
			vo = new IllustrationsClassVo();
			vo.type = 6;
			vo.type2 = 61;
			data.push(vo);	
			
			vo = new IllustrationsClassVo();
			vo.type = 6;
			vo.type2 = 62;
			data.push(vo);			
			
			vo = new IllustrationsClassVo();
			vo.type = 6;
			vo.type2 = 63;
			data.push(vo);	
			
			vo = new IllustrationsClassVo();
			vo.type = 6;
			vo.type2 = 64;
			data.push(vo);			
			
			vo = new IllustrationsClassVo();
			vo.type = 7;
			vo.type2 = 71;
			data.push(vo);	
			
			
			var dataarr:Array = new Array();
			
			vo = new IllustrationsClassVo();
			vo.type = 1;
			vo.type2 = 11;			
			
			
		}
		
		public function getdata():Array
		{
			return  data;
		}
		
	}

}