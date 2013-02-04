package  
{
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author ZC
	 */
	public class PathFinding 
	{
		private var arr:Array = new Array();
		private var bool:Boolean = true;
		private var arrtemp:Array = new Array();
		
		public function PathFinding() 
		{

		}
		
		
		
		public function findgrid(id:int ):Array
		{
			for (var i:int = 0 ; i < arr.length; i++ )
			{
				if (arr[i][0] == id)
				{
					return arr[i][1];
				}
			}
			return null;
			
		}
		//设置路线
		public function setData(_arr:Array):void
		{
			for (var i:int = 0; i <_arr.length ; i++) 
			{
				var arrtemp:Array = new Array();
				arrtemp.push(_arr[i].cid);
				
				var arrtemp2:Array = new Array();
				for (var j:int = 0; j < _arr[i].links.length; j++) 
				{
					arrtemp2.push(_arr[i].links[j][0]);
				}
				arrtemp.push(arrtemp2);
				arr.push(arrtemp);
			}
		}
		
		//返回寻找的路程【1,4,5】
		public function getGoArray(_start:int, _end:int):Array
		{
			var qidian:int= _start;
		    var zhongdian:int = _end; //终点
			var stepnumber:uint = 1;
			var arr1:Array = [[qidian]];
			arrtemp = arr1;
			bool = true;
			while (bool)
			{ 
				//重置列表				
				arr1 = new Array();	
				stepnumber++;
				for (var i:int = 0; i < arrtemp.length; i++ )
			    {
                     var arr3:Array = arrtemp[i];
					 var arr4:Array = findgrid(arr3[arr3.length - 1]);
					 
					 for (var j:int = 0; j < arr4.length; j++ )
					 {
						 var arr5:Array = new Array();
						 
						 for (var k:int = 0; k < arr3.length; k++ )
						 {
						    arr5.push(arr3[k]);							 
						 }
						 
						 if (arr4[j] == zhongdian)
						 {
							 bool = false;
						 }
						 
                         if (DataManager.getInstance().worldData.isworldMapLock(arr4[j]))
						 {
						 	 arr5.push(arr4[j]);
							 arr1.push(arr5);							 
						 }

						 
					 }
				}
					 arrtemp = arr1;

					 
			    //清除已经走过的路
				
				for (var l:int ; l < arrtemp.length; l++ )
				{
					var temp3:Array = arrtemp[l];
					
				}
			}
			
			
			for (var m:int = 0; m <arrtemp.length ; m++) 
			{
				if (arrtemp[m][stepnumber - 1] == _end)
				{
				    return arrtemp[m];
				}
			}
			
			return null;
		}
		
	}

}