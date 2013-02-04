package happymagic.model.data 
{
	import com.adobe.utils.VectorUtil;
	import happymagic.model.vo.task.TaskState;
	import happymagic.model.vo.task.TaskType;
	import happymagic.model.vo.task.TaskVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class TaskData 
	{
		private var taskTypesList:Array = [null, [], [], []];
		
		public function setTasks(arr:Array):void
		{
			for (var i:int = 1; i <= 3; i++)
			{
				taskTypesList[i].length = 0;
			}
			var len:int = arr.length;
			for (i = 0; i < len; i++)
			{
				var vo:TaskVo = new TaskVo();
				vo.setData(arr[i]);
				taskTypesList[vo.type].push(vo);
			}
		}
		
		public function setDailyTasks(arr:Array):void
		{
			var len:int = arr.length;
			var list:Array = taskTypesList[TaskType.DAILY];
			list.length = len;
			for (var i:int = 0; i < len; i++)
			{
				if (!list[i]) list[i] = new TaskVo();
				list[i].setData(arr[i]);
			}
		}
		
		public function addTask(vo:TaskVo):void
		{
			var list:Array = taskTypesList[vo.type];
			if(list) list.unshift(vo);
		}
		
		/**
		 * 
		 * @param	data
		 * @return	这个任务是否存在
		 */
		public function updateTask(vo:TaskVo, toFirst:Boolean = false):Boolean
		{
			var list:Array = taskTypesList[vo.type];
			for (var i:int = list.length - 1; i >= 0; i--)
			{
				if (list[i].id == vo.id)
				{
					list[i].setData(vo);
					if (toFirst) list.unshift(list.splice(i, 1)[0]);
					return true;
				}
			}
			return false;
		}
		
		public function getTask(id:int):TaskVo
		{
			var list:Array = taskTypesList[TaskType.getType(id)];
			if (!list) return null;
			for (var i:int = list.length - 1; i >= 0; i--)
			{
				if (list[i].id == id) return list[i];
			}
			return null;
		}
		
		/**
		 * 移除这个任务
		 * @param	id
		 * @return
		 */
		public function removeTask(id:int):TaskVo
		{
			var list:Array = taskTypesList[TaskType.getType(id)];
			for (var i:int = list.length - 1; i >= 0; i--)
			{
				if (list[i].id == id)
				{
					return list.splice(i, 1)[0];
				}
			}
			return null;
		}
		
		/**
		 * 
		 * @param	type
		 * @param	hasAccept
		 * @param	hasNotAccept
		 * @return
		 */
		public function getTasks(type:int = 0, hasAccept:Boolean = true, hasNotAccept:Boolean = false):Array
		{
			var list:Array = taskTypesList[type];
			if (!list)
			{
				list = [];
				for (var i:int = 1; i <= 3; i++)
				{
					list = list.concat(taskTypesList[i]);
				}
			}
			
			if (hasAccept && hasNotAccept) return list;
			var arr:Array = [];
			var len:int = list.length;
			for (i = 0; i < len; i++)
			{
				var vo:TaskVo = list[i];
				if (vo.type != type) continue;
				if(hasAccept && (vo.state != TaskState.NOT_ACCEPT))
				{
					arr.push(vo);
				}else if(hasNotAccept && (vo.state == TaskState.NOT_ACCEPT))
				{
					arr.push(vo);
				}
			}
			return arr;
		}
		
		/**
		 * 
		 * @param	npcId
		 * @param	hasAccept
		 * @param	hasNotAccept
		 * @return
		 */
		public function getTasksByNpcId(npcId:int, hasAccept:Boolean = true, hasNotAccept:Boolean = false):Array
		{
			var arr:Array = [];
			for (var i:int = 1; i <= 3; i++)
			{
				var list:Array = taskTypesList[i];
				var len:int = list.length;
				for (var j:int = 0; j < len; j++)
				{
					var vo:TaskVo = list[j];
					if (vo.npcId != npcId) continue;
					if(hasAccept && (vo.state != TaskState.NOT_ACCEPT))
					{
						arr.push(vo);
					}else if(hasNotAccept && (vo.state == TaskState.NOT_ACCEPT))
					{
						arr.push(vo);
					}
				}
			}
			return arr;
		}
		
		/**
		 * 获得指定场景所相关的所有任务
		 * @param	sceneId
		 * @param	hasAccept
		 * @param	hasNotAccept
		 * @return
		 */
		public function getTasksBySceneId(sceneId:uint, hasAccept:Boolean = true, hasNotAccept:Boolean = false):Array
		{
			var arr:Array = [];
			for (var i:int = 1; i <= 3; i++)
			{
				var list:Array = taskTypesList[i];
				var len:int = list.length;
				for (var j:int = 0; j < len; j++)
				{
					var vo:TaskVo = list[j];
					if (vo.sceneId != sceneId) continue;
					if(hasAccept && (vo.state != TaskState.NOT_ACCEPT))
					{
						arr.push(vo);
					}else if(hasNotAccept && (vo.state == TaskState.NOT_ACCEPT))
					{
						arr.push(vo);
					}
				}
			}
			return arr;
		}
		
		public function sort():void
		{
			for (var i:int = 1; i <= 3; i++)
			{
				var list:Array = taskTypesList[i];
				list.sort(taskSortFun);
			}
		}
		
		private function taskSortFun(a:TaskVo, b:TaskVo):int 
		{
			var aFinish:Boolean = a.isFinish();
			var bFinish:Boolean = b.isFinish();
			if (aFinish && bFinish)
			{
				return a.index - b.index;
			}else if (aFinish)
			{
				return -1;
			}
			return 1;
		}
	}
}