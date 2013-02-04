package happymagic.model.data 
{
	import happyfish.model.vo.BasicVo;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.classVo.MixClassVo;
	import happymagic.model.vo.MixVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class MixData 
	{
		
		// ==========================合成基础数据列表===================================
		private const _mixClassList:Vector.<MixClassVo> = new Vector.<MixClassVo>();
		private const _mixClassMap:Object = { };
		
		public function getMixClass(cid:int):MixClassVo { return _mixClassMap[cid] as MixClassVo; }
		public function getMixClassList():Vector.<MixClassVo> { return _mixClassList; }
		
		/**
		 * 设置MixClass列表
		 * @param	arr Array<Object> 未转换为VO的Object列表
		 */
		public function setMixClassList(arr:Array):void
		{
			for (var k:* in _mixClassMap)
			{
				delete _mixClassMap[k];
			}
			var len:int = arr.length;
			_mixClassList.length = len;
			for (var i:int = 0; i < len; i++)
			{
				var vo:MixClassVo = new MixClassVo().setData(arr[i]) as MixClassVo;
				_mixClassList.push(vo);
				_mixClassMap[vo.cid] = vo;
			}
		}
		
		
		// ==========================当前已经学会的合成术列表===================================
		private const mixList:Vector.<MixClassVo> = new Vector.<MixClassVo>();
		private const mixCidList:Vector.<int> = new Vector.<int>();
		// Map<int:furnaceCid, Vector.<MixClassVo>:currMixList(Part)>
		private const mixMap:Object = { };
		
		public function hasMix(cid:int):Boolean { return mixList.indexOf(cid) >= 0; }
		public function getMixList():Vector.<MixClassVo> { return mixList; }
		public function getMixCidList():Vector.<int> { return mixCidList; }
		
		public function getMixListByFurnace(fid:int):Vector.<MixClassVo>
		{
			if (!mixMap[fid]) return null;
			return mixMap[fid];
		}
		
		/**
		 * 设置已经学会的合成术列表
		 * @param	list Array<int>
		 */
		public function setMixCidList(list:Array):void
		{
			for (var k:* in mixMap)
			{
				delete mixMap[k];
			}
			mixCidList.length = 0;
			addMixs(list);
		}
		
		/**
		 * 学习一些合成术
		 * @param	list Array<int>
		 */
		public function addMixs(list:Array):void
		{
			var len:int = list.length;
			for (var i:int = 0; i < len; i++)
			{
				var cid:int = list[i];
				var vo:MixClassVo = getMixClass(cid);
				var v:Vector.<MixClassVo> = mixMap[vo.furnaceCid] as Vector.<MixClassVo>;
				if (!v) v = mixMap[vo.furnaceCid] = new Vector.<MixClassVo>();
				
				if (v.indexOf(vo) >= 0) continue;
				v.push(vo);
				mixCidList.push(cid);
			}
		}
		
		
		// ==========================当前正在做的合成术列表=================================== 
		
		private const curMixList:Vector.<MixVo> = new Vector.<MixVo>();
		
		/**
		 * 获取当前正在运行的合成术
		 * @param	furnaceId 炉子id
		 * @return
		 */
		public function getCurMix(furnaceId:String):MixVo
		{
			for (var i:int = curMixList.length - 1; i >= 0; i--)
			{
				if (curMixList[i].furnaceId == furnaceId) return curMixList[i];
			}
			return null;
		}
		
		public function getCurMixList():Vector.<MixVo> { return curMixList; }
		
		/**
		 * 设置正在进行中的合成术列表
		 * @param	arr Array<Object> 非MixVo
		 */ 
		public function setCurMixList(arr:Array):void
		{
			curMixList.length = 0;
			var len:int = arr.length;
			for (var i:int = 0; i < len; i++)
			{
				var vo:MixVo = new MixVo();
				vo.setData(arr[i]);
				curMixList.push(vo);
			}
		}
		
		public function addCurMix(mixVo:MixVo):void
		{
			curMixList.push(mixVo);
		}
		
		/**
		 * 移除一个合成术
		 * @param	id 炉子的id
		 */
		public function removeCurMix(id:String):void
		{
			for (var i:int = curMixList.length - 1; i >= 0; i--)
			{
				if (curMixList[i].furnaceId == id)
				{
					curMixList.splice(i, 1);
					return;
				}
			}
		}
	}

}