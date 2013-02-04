package happymagic.hire.view 
{
	import happyfish.manager.local.LocaleWords;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.hire.view.ui.SkillTipUI;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.classVo.ScrollClassVo;
	import happymagic.model.vo.SkillAndItemVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class SkillTip extends SkillTipUI 
	{
		public function SkillTip() 
		{
			TextFieldUtil.autoSetTxtDefaultFormat(this);
		}
		
		public function setData(cid:int):void 
		{
			var skillVo:SkillAndItemVo = DataManager.getInstance().getSkillAndItemVo(cid);
			mpTxt.text = skillVo.needMp+"";
			nameTxt.text = skillVo.name;
			
			var scrollVo:ScrollClassVo = DataManager.getInstance().itemData.getItemClass(cid) as ScrollClassVo;
			contentTxt.text = scrollVo.content;
			var oneJob:Boolean = scrollVo.jobs && 1 == scrollVo.jobs.length;
			var oneProp:Boolean = scrollVo.props && 1 == scrollVo.props.length;
			
			var getWord:Function = LocaleWords.getInstance().getWord;
			var str:String = null;
			if (oneJob && oneProp)
			{
				str = getWord("roleProp" + scrollVo.props[0]) + getWord("roleProfession" + scrollVo.jobs[0]);
			}else if (!oneJob && !oneProp)
			{
				str = getWord("allJobs");
			}else if (!oneProp)
			{
				str = getWord("xxTongyong", getWord("roleProfession" + scrollVo.jobs[0]));
			}else if (!oneJob)
			{
				str = getWord("xxTongyong", getWord("roleProp" + scrollVo.props[0]));
			}
			jobTxt.text = str;
		}
		
	}

}