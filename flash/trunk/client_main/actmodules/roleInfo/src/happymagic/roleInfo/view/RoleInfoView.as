package happymagic.roleInfo.view 
{
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import flash.utils.getQualifiedClassName;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.ui.events.GridPageEvent;
	import happyfish.display.ui.Pagination;
	import happyfish.display.view.IconView;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.display.view.ui.AvatarSprite;
	import happymagic.events.DataManagerEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.classVo.EquipmentClassVo;
	import happymagic.model.vo.ItemVo;
	import happymagic.model.vo.RoleVo;
	import happymagic.model.vo.SkillAndItemVo;
	import happymagic.roleInfo.commands.StripAllEquipCommand;
	import happymagic.roleInfo.commands.UnlockSkillCommand;
	import happymagic.roleInfo.events.RoleEvent;
	import happymagic.roleInfo.view.ui.render.RoleItemRender;
	import happymagic.roleInfo.view.ui.RoleInfoViewUI;
	import happymagic.roleInfo.view.ui.RoleItem;
	import happymagic.roleInfo.vo.CurTrainRoleValName;
	import happymagic.roleInfo.vo.DismissChat;
	import happymagic.roleInfo.vo.EquiRoleVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class RoleInfoView extends RoleInfoViewUI
	{
		private var role:RoleVo;
		private var oldRole:RoleVo = null;
		private var listView:DefaultListView;
		
		private var skillUnlock:Array;
		private var equiList:Vector.<ItemVo>; 
		
		private var avatar:AvatarSprite;
		private var tip:SkillTip;
		private var equipTip:EquipTip;
		
		private var defaultWearColor:uint;
		
		public function RoleInfoView() 
		{
			listView = new DefaultListView(this, this, 4, false, false);
			listView.init(223, 326, 223, 69, -318, -125);
			listView.setGridItem(RoleItem, RoleItemRender);
			listView.selectCallBack = selectRoleItemHandler;
			listView.pagination = new Pagination();
			listView.pagination.y = 185;
			listView.pagination.x = -210;
			
			avatar = new AvatarSprite();
			avatar.x = avatarBorder.x;
			avatar.y = avatarBorder.y;
			avatar.setBorder(avatarBorder.width, avatarBorder.height);
			addChild(avatar);
			removeChild(avatarBorder);
			
			TextFieldUtil.autoSetTxtDefaultFormat(this);
			TextFieldUtil.autoSetTxtDefaultFormat(skill0);
			TextFieldUtil.autoSetTxtDefaultFormat(skill1);
			TextFieldUtil.autoSetTxtDefaultFormat(skill2);
			TextFieldUtil.autoSetTxtDefaultFormat(skill3);
			defaultWearColor = wearTxt0.textColor;
			
			this.addEventListener(MouseEvent.CLICK, clickHandler);
			EventManager.addEventListener(RoleEvent.ROLE_CHANGE, roleEventHandler);
			//EventManager.addEventListener(RoleEvent.ROLE_DISMISS, roleEventHandler);
			
			EventManager.addEventListener(DataManagerEvent.ROLEDATA_CHANGE, roleEventHandler);
			
			for (var i:int = 0; i < 4; i++)
			{
				var skillMc:MovieClip = this["skill" + i];
				var border:DisplayObject = skillMc.border;
				var rect:Rectangle = new Rectangle(border.x, border.y, border.height, border.height);
				var icon:IconView = new IconView(rect.width, rect.height, rect);
				icon.addEventListener(MouseEvent.ROLL_OVER, skillOverHandler);
				icon.addEventListener(MouseEvent.ROLL_OUT, skillOutHandler);
				skillMc.lockMc.stop();
				skillMc.learnEffect.stop();
				skillMc.icon = icon;
				skillMc.addChildAt(icon, skillMc.getChildIndex(border));
				skillMc.removeChild(border);
				stopMovie(skillMc.lockMc);
				
				this["equipTipArea" + i].alpha = 0;
				this["equipTipArea" + i].addEventListener(MouseEvent.ROLL_OVER, equipTipAreaOverHandler);
				this["equipTipArea" + i].addEventListener(MouseEvent.ROLL_OUT, equipTipAreaOutHandler);
			}
			
			equiList = new Vector.<ItemVo>();
		}
		
		private function stopMovie(mc:MovieClip):void
		{
			mc.addFrameScript(mc.totalFrames - 1, function():void
			{
				mc.gotoAndStop(1);
				mc.visible = false;
			});
		}
		
		private function equipTipAreaOverHandler(e:MouseEvent):void 
		{
			if (!equipTip) equipTip = new EquipTip();
			equipTip.x = e.currentTarget.x + e.currentTarget.width/2 + e.currentTarget.parent.x;
			equipTip.y = e.currentTarget.y + e.currentTarget.height/2 + e.currentTarget.parent.y;
			addChild(equipTip);
			var idx:int = int(e.currentTarget.name.substr( -1, 1));
			equipTip.setData(equiList[idx]._base, 0 == equiList[idx].wear);
		}
		
		private function equipTipAreaOutHandler(e:MouseEvent):void 
		{
			if (equipTip && equipTip.parent) equipTip.parent.removeChild(equipTip);
		}
		
		private function skillOverHandler(e:MouseEvent):void 
		{
			var cid:int = e.currentTarget.parent.skillCid;
			if (!cid) return;
			
			if (!tip) tip = new SkillTip();
			tip.x = e.currentTarget.x + e.currentTarget.width + e.currentTarget.parent.x;
			tip.y = e.currentTarget.y + e.currentTarget.height/2 + e.currentTarget.parent.y;
			addChild(tip);
			tip.setData(cid);
		}
		
		private function skillOutHandler(e:MouseEvent):void 
		{
			if (tip && tip.parent) tip.parent.removeChild(tip);
		}
		
		private function roleEventHandler(e:Event):void 
		{
			var roleId:int = role ? role.id : 0;
			reset();
			if (role.id == roleId) return;
			
			var tmpVo:RoleVo = DataManager.getInstance().roleData.getRole(roleId);
			if (tmpVo)
			{
				listView.selectedValue = tmpVo;
				setRole(tmpVo);
			}
			//switch(e.type)
			//{
				//case RoleEvent.ROLE_CHANGE :
					//if (!role || role.id != e.role.id) return;
					//setRole(e.role);
					//break;
					//
				//case RoleEvent.ROLE_DISMISS :
					//reset();
					//break;
			//}
			
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			var name:String = e.target.name;
			// 点击装备更换按钮
			if ("equiReplaceBtn" == name.substr(0, 14))
			{
				var idx:int = int(name.charAt(14));
				clickEquiReplaceBtn(idx);
			}
			// 点击技能更换按钮
			else if ("skillReplaceBtn" == name)
			{
				idx = int(e.target.parent.name.charAt(5));
				clickSkillReplaceBtn(idx);
			}
			// 点击解锁
			else if ("unlockBtn" == name)
			{
				idx = int(e.target.parent.name.charAt(5));
				clickLockMcBtn(idx);
			}
			// 点击卸下装备
			else if (stripAllEquipBtn == e.target)
			{
				new StripAllEquipCommand().stripAllEquip(role.id);
			}
			// 点击解雇
			else if (dismissBtn == e.target)
			{
				showDismissPanel(role);
			}
			// 点击培养
			else if (trainButton == e.target)
			{
				showTrainPanel(role);
			}
		}
		
		public function changeTrainPanel(role:RoleVo):void
		{
			DataManager.getInstance().setVar(CurTrainRoleValName, role);
			EventManager.dispatchEvent(new Event("curTrainRoleChange"));
		}
		
		private function showTrainPanel(role:RoleVo):void 
		{
			DataManager.getInstance().setVar(CurTrainRoleValName, role);
			ActModuleManager.getInstance().addActModule(DataManager.getInstance().getActByName("StrengThen"));
			EventManager.dispatchEvent(new Event("curTrainRoleChange"));
		}
		
		private function showDismissPanel(role:RoleVo):void 
		{
			var moduleVo:ModuleVo = new ModuleVo();
			moduleVo.name = getQualifiedClassName(AlertMsgUISprite);
			moduleVo.className = moduleVo.name;
			var ui:AlertMsgUISprite = ModuleManager.getInstance().addModule(moduleVo) as AlertMsgUISprite;
			ModuleManager.getInstance().showModule(moduleVo.name);
			DisplayManager.uiSprite.setBg(ui);
			ui.setData(role.id, role.name, DismissChat.getRandomChat(), role.faceClass);
		}
		
		private function clickEquiReplaceBtn(idx:int):void 
		{
			var chooseSprite:ChooseEquipmentUISprite = ModuleManager.getInstance().getModule(ModuleName.ChooseEquipmentUISprite) as ChooseEquipmentUISprite;
			if (!chooseSprite)
			{
				var moduleVo:ModuleVo = new ModuleVo();
				moduleVo.className = getQualifiedClassName(ChooseEquipmentUISprite);
				moduleVo.name = ModuleName.ChooseEquipmentUISprite;
				chooseSprite = ModuleManager.getInstance().addModule(moduleVo) as ChooseEquipmentUISprite;
			}
			ModuleManager.getInstance().showModule(ModuleName.ChooseEquipmentUISprite);
			DisplayManager.uiSprite.setBg(chooseSprite);
			var equip:EquipmentClassVo = equiList[idx] ? equiList[idx]._base : null;
			var wear:int = equiList[idx] ? equiList[idx].wear : 0;
			chooseSprite.setData(new EquiRoleVo(role.id, role.level, equip, wear), 60 + idx + 1, role.profession);
		}
		
		private function clickSkillReplaceBtn(idx:int):void 
		{
			var learnSprite:SkillLearnUISprite = ModuleManager.getInstance().getModule(ModuleName.SkillLearnUISprite) as SkillLearnUISprite;
			if (!learnSprite)
			{
				var moduleVo:ModuleVo = new ModuleVo();
				moduleVo.className = getQualifiedClassName(SkillLearnUISprite);
				moduleVo.name = ModuleName.SkillLearnUISprite;
				learnSprite = ModuleManager.getInstance().addModule(moduleVo) as SkillLearnUISprite;
			}
			ModuleManager.getInstance().showModule(ModuleName.SkillLearnUISprite);
			DisplayManager.uiSprite.setBg(learnSprite);
			learnSprite.setData(role.id, role.level, idx + 1, role.profession, role.prop);
		}
		
		private function clickLockMcBtn(idx:int):void 
		{
			var lockMc:MovieClip = this["skill" + idx].lockMc;
			new UnlockSkillCommand().unlockSkill(role.id, idx + 1, function():void
			{
				lockMc.visible = true;
				lockMc.gotoAndPlay(1);
			});
		}
		
		private function selectRoleItemHandler(e:GridPageEvent):void 
		{
			setRole(listView.selectedValue as RoleVo);
		}
		
		private function setRole(vo:RoleVo):void
		{
			oldRole = role;
			var getWord:Function = LocaleWords.getInstance().getWord;
			role = vo;
			nameTxt.text = vo.name;
			levelTxt.text = getWord("LVx", vo.level);
			jobIcon.gotoAndStop(vo.profession);
			jobTxt.text = getWord("roleProfession" + vo.profession);
			propIcon.gotoAndStop(vo.prop);
			propTxt.text = getWord("roleProp" + vo.prop);
			expTxt.text = vo.exp + "/" + vo.maxExp;
			expBar.scaleX = vo.exp / vo.maxExp;
			mpTxt.text = vo.mp + "/" + vo.maxMp;
			mpBar.scaleX = vo.mp / vo.maxMp;
			hpTxt.text = vo.hp + "/" + vo.maxHp;
			hpBar.scaleX = vo.hp / vo.maxHp;
			paTxt.text = vo.phyAtk + "";
			pdTxt.text = vo.phyDef + "";
			maTxt.text = vo.magAtk + "";
			mdTxt.text = vo.magDef + "";
			speedTxt.text = vo.speed + "";
			starLevel.gotoAndStop(vo.quality);
			dismissBtn.visible = vo.label != RoleVo.MAIN_ROLE && vo.label != RoleVo.TEMP;
			trainButton.visible = vo.label != RoleVo.TEMP;
			avatar.load(vo.className);
			avatar.data = null;
			avatar.playLoop("wait");
			
			setEquiInfo();
			setSkillInfo();
			
			changeTrainPanel(role);
		}
		
		public function show():void
		{
			visible = true;
			reset();
		}
		
		public function hide():void
		{
			changeTrainPanel(null);
			visible = false;
			avatar.stop();
		}
		
		private function reset():void 
		{
			var arr:Array = DataManager.getInstance().roleData.getMyRoles();
			listView.setData(arr);
			listView.selectedValue = arr[0];
			setRole(arr[0] as RoleVo);
		}
		
		private function setEquiInfo():void 
		{
			var getItemClass:Function = DataManager.getInstance().itemData.getItemClass;
			equiList.length = 0;
			equiList.length = 4;
			var equipments:Array = role.equipments || [];
			for (var i:int = 0; i < 4; i++)
			{
				var arr:Array = equipments[i];
				if(arr)
				{
					var vo:ItemVo = new ItemVo();
					vo.setData( { id:arr[0], cid:arr[1], wear:arr[2] } );
					this["equiNameTxt" + i].text = vo.base.name;
					var maxWear:int = EquipmentClassVo(vo.base).maxWear;
					if (0 == maxWear)
					{
						this["wearTxt" + i].text = LocaleWords.getInstance().getWord("wearInfinite");
						this["wearTxt" + i].textColor = defaultWearColor;
					}else if (0 == vo.wear)
					{
						this["wearTxt" + i].text = LocaleWords.getInstance().getWord("wearZero");
						this["wearTxt" + i].textColor = 0xFF0000;
					}else
					{
						var wearR:int = Math.round(vo.wear / maxWear * 100) || 1;
						this["wearTxt" + i].text = wearR + "%";
						this["wearTxt" + i].textColor = defaultWearColor;
					}
					//this["wearTxt" + i].text = 0 == maxWear ? LocaleWords.getInstance().getWord("wearInfinite") : vo.wear + "/" + maxWear;
					equiList[i] = vo;
					this["equipTipArea" + i].visible = true;
				}else
				{
					this["equiNameTxt" + i].text = "";
					this["wearTxt" + i].text = "";
					this["equipTipArea" + i].visible = false;
				}
			}
		}
		
		private function setSkillInfo():void 
		{
			skillUnlock = DataManager.getInstance().gameSetting.skillUnlock;
			var getWord:Function = LocaleWords.getInstance().getWord;
			for (var i:int = 0; i < 4; i++)
			{
				var cid:int = role.skills[i];
				var skillMc:MovieClip = this["skill" + i] as MovieClip;
				var icon:IconView = skillMc.icon as IconView;
				skillMc.learnEffect.visible = false;
				
				var oldCid:int = skillMc.skillCid;
				skillMc.skillCid = cid;
				// 未解锁
				if ( -1 == cid)
				{
					icon.visible = false;
					skillMc.nameTxt.visible = false;
					skillMc.lockMc.visible = true;
					skillMc.lockMc.mouseChildren = false;
					skillMc.unlockBtn.visible = true;
					skillMc.skillReplaceBtn.visible = false;
					skillMc.numTxt.visible = true;
					skillMc.priceTypeMc.visible = true;
					skillMc.numTxt.text = skillUnlock[i][2] + "";
					skillMc.priceTypeMc.gotoAndStop(skillUnlock[i][1]);
					if (skillUnlock[i][0] > role.level)
					{
						skillMc.lockTxt.text = getWord("xLevelLockThis", skillUnlock[i][0]);
						skillMc.lockTxt.visible = true;
					}else
					{
						skillMc.lockTxt.visible = false;
					}
				}else if (0 == cid)
				{
					icon.visible = false;
					skillMc.nameTxt.visible = false;
					skillMc.lockTxt.visible = false;
					skillMc.lockMc.visible = false;
					skillMc.unlockBtn.visible = false;
					skillMc.skillReplaceBtn.visible = true;
					skillMc.numTxt.visible = false;
					skillMc.priceTypeMc.visible = false;
				}else
				{
					var vo:SkillAndItemVo = DataManager.getInstance().getSkillAndItemVo(cid);
					icon.visible = true;
					skillMc.nameTxt.visible = true;
					skillMc.lockTxt.visible = false;
					skillMc.lockMc.visible = false;
					skillMc.unlockBtn.visible = false;
					skillMc.skillReplaceBtn.visible = true;
					skillMc.numTxt.visible = false;
					skillMc.priceTypeMc.visible = false;
					skillMc.nameTxt.text = vo.name;
					icon.setData(vo.className);
					if (oldRole == role && oldCid != cid)
					{
						skillMc.learnEffect.gotoAndPlay(1);
						skillMc.learnEffect.visible = true;
					}
				}
			}
		}
		
	}

}