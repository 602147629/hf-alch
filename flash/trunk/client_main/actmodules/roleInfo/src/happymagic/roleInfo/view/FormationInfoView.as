package happymagic.roleInfo.view 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.utils.setTimeout;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.Pagination;
	import happyfish.events.DEvent;
	import happyfish.manager.EventManager;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.display.view.ui.AvatarSprite;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.RoleVo;
	import happymagic.model.vo.SceneClassVo;
	import happymagic.roleInfo.commands.RoleFormationSetCommand;
	import happymagic.roleInfo.events.RoleEvent;
	import happymagic.roleInfo.events.RoleFormationEvent;
	import happymagic.roleInfo.view.ui.FormationInfoUI;
	import happymagic.roleInfo.view.ui.render.RoleItemRender;
	import happymagic.roleInfo.view.ui.RoleFormationItem;
	import happymagic.roleInfo.vo.RoleFormationVo;
	import happymagic.utils.AvatarUtil;
	/**
	 * ...
	 * @author lite3
	 */
	public class FormationInfoView extends FormationInfoUI
	{
		private var listView:DefaultListView;
		private var roleFormationList:Array = [];
		private var freeAvatarList:Array = [];
		
		private var dragAvatar:AvatarSprite;
		private var overTile:MovieClip;
		
		private var isDungeon:Boolean;
		
		public function FormationInfoView() 
		{
			listView = new DefaultListView(this, this, 4, false, false);
			listView.init(223, 326, 223, 69, -318, -125);
			listView.setGridItem(RoleFormationItem, RoleItemRender);
			listView.pagination = new Pagination();
			listView.pagination.y = 185;
			listView.pagination.x = -210;
			
			TextFieldUtil.autoSetDefaultFormat(numTxt);
			
			//EventManager.addEventListener(RoleEvent.ROLE_DISMISS, roleEventHandler);
			addEventListener(RoleFormationEvent.ROLE_FORMATION_SELECT, roleFormationHandler);
			addEventListener(RoleFormationEvent.ROLE_FORMATION_CANCEL, roleFormationHandler);
			addEventListener(MouseEvent.CLICK, beginRemoveAvatar);
			
			for (var i:int = 9; i <= 17; i++)
			{
				var tile:MovieClip = MovieClip(this.getChildByName("tile" + i));
				tile.mouseChildren = false;
				tile.pos = i;
				tile.gotoAndStop("NotAvatar");
			}
		}
		
		private function submitFormation(e:MouseEvent = null):void 
		{
			setBattleRolesCount();
			var map:Object = { };
			for (var i:int = roleFormationList.length - 1; i >= 0; i--)
			{
				var vo:RoleFormationVo = roleFormationList[i] as RoleFormationVo;
				if(vo.onBattle) map[vo.pos] = vo.role.id;
			}
			new RoleFormationSetCommand().roleFormationSet(map);
		}
		
		public function show():void
		{
			visible = true;
			reset();
		}
		
		public function hide():void
		{
			visible = false;
			clear();
		}
		
		private function beginRemoveAvatar(e:MouseEvent):void 
		{
			if (dragAvatar) return;
			
			var tile:MovieClip = getTileByTarget(e.target as MovieClip);
			if (!tile || !tile.avatar) return;
			
			dragAvatar = tile.avatar;
			startDragAvatar(tile);
		}
		
		private function roleFormationHandler(e:RoleFormationEvent):void 
		{
			e.stopImmediatePropagation();
			switch(e.type)
			{
				case RoleFormationEvent.ROLE_FORMATION_CANCEL :
					cancelRoleInBattle(e.role);
					break;
					
				case RoleFormationEvent.ROLE_FORMATION_SELECT :
					selectRoleInBattle(e.role);
					break;
			}
		}
		
		private function selectRoleInBattle(role:RoleFormationVo):void
		{
			if (dragAvatar || role.onBattle) return;
			
			var n:int = 0;
			for (var i:int = roleFormationList.length - 1; i >= 0; i--)
			{
				var vo:RoleFormationVo = roleFormationList[i] as RoleFormationVo;
				if (vo.onBattle && vo.role.label != RoleVo.TEMP) n++;
			}
			if (n >= DataManager.getInstance().gameSetting.maxBattleRoles) return;
			//if(roleFormationList.length >= DataManager.getInstance().currentUser.role
			
			dragAvatar = getFreeAvatar(role);
			
			startDragAvatar(null);
		}
		
		private function setBattleRolesCount():void
		{
			var max:int = DataManager.getInstance().gameSetting.maxBattleRoles;
			var cur:int = 0;
			for (var i:int = roleFormationList.length - 1; i >= 0; i--)
			{
				var vo:RoleFormationVo = roleFormationList[i] as RoleFormationVo;
				if (vo.onBattle) cur++;
				if (RoleVo.TEMP == vo.role.label) max++;
			}
			numTxt.text = cur + "/" + max;
		}
		
		private function startDragAvatar(tile:MovieClip):void 
		{
			dragAvatar.x = mouseX;
			dragAvatar.y = mouseY;
			addChild(dragAvatar);
			removeEventListener(MouseEvent.CLICK, beginRemoveAvatar);
			if (tile)
			{
				overTile = tile;
				tile.avatar = null;
			}
			stage.addEventListener(MouseEvent.MOUSE_UP, mouseUpHandler);
			stage.addEventListener(MouseEvent.MOUSE_MOVE, mouseMoveHandler);
			
			EventManager.dispatchEvent(new DEvent("roleFormationDrag"));
		}
		
		public function cancelRoleInBattle(role:RoleFormationVo):void
		{
			cancelDrag();
			
			if (role.onBattle)
			{
				
				MovieClip(this.getChildByName("tile" + role.pos)).gotoAndStop("NotAvatar");
				MovieClip(this.getChildByName("tile" + role.pos)).avatar = null;
				role.pos = -1;
				setAwatarState(role);
				listView.getItemByValue(role).setData(role);
			}
			
			submitFormation();
		}
		
		private function cancelDrag():void 
		{
			if (!dragAvatar) return;
			
			dragAvatar = null;
			if (stage)
			{
				stage.removeEventListener(MouseEvent.MOUSE_UP, mouseUpHandler);
				stage.removeEventListener(MouseEvent.MOUSE_MOVE, mouseMoveHandler);
			}
			setTimeout(delayAddClickEvent, 0);
			EventManager.dispatchEvent(new DEvent("roleFormationDrop"));
		}
		
		private function delayAddClickEvent():void
		{
			addEventListener(MouseEvent.CLICK, beginRemoveAvatar);
		}
		
		private function getTileByTarget(target:MovieClip):MovieClip
		{
			if (!target) return null;
			var overName:String = target.name;
			var tile:MovieClip = 0 == overName.indexOf("tile") && target.parent == this ? target : null;
			return tile;
		}
		
		private function mouseMoveHandler(e:MouseEvent):void 
		{
			if (!dragAvatar) return;
			
			dragAvatar.x = mouseX;
			dragAvatar.y = mouseY;
			var tile:MovieClip = getTileByTarget(e.target as MovieClip);
			
			if (tile == overTile) return;
			
			if (overTile)
			{
				overTile.gotoAndStop("NotAvatar");
				overTile = null;
			}
			
			if (tile && !tile.avatar)
			{
				overTile = tile;
				tile.gotoAndStop("HasAvatar");
			}
		}
		
		private function mouseUpHandler(e:MouseEvent):void 
		{
			if (!dragAvatar) return;
			
			var tmpTitle:MovieClip = overTile;
			if (overTile)
			{
				overTile.avatar = dragAvatar;
				dragAvatar.x = overTile.x;
				dragAvatar.y = overTile.y;
				dragAvatar.data.pos = overTile.pos;
				overTile = null;
			}
			
			var gridItem:GridItem = listView.getItemByValue(dragAvatar.data);
			if (gridItem) gridItem.setData(dragAvatar.data);
			dragAvatar.data.avatar = dragAvatar;
			var changePos:Boolean = dragAvatar.data.pos != dragAvatar.data.role.pos;
			setAwatarState(dragAvatar.data as RoleFormationVo);
			cancelDrag();
			if (changePos)
			{
				if (tmpTitle) tmpTitle.gotoAndStop("PutAvatar");
				submitFormation();
			}
		}
		
		private function roleEventHandler(e:RoleEvent):void 
		{
			reset();
		}
		
		private function reset():void 
		{
			clear();
			var arr:Array = DataManager.getInstance().roleData.getMyRoles();
			checkDungeon();
			var len:int = arr.length;
			roleFormationList.length = 0;
			for (var i:int = 0; i < len; i++)
			{
				var role:RoleVo = arr[i];
				if (!isDungeon || role.pos != -1)
				{
					var vo:RoleFormationVo = new RoleFormationVo(role, isDungeon);
					setAwatarState(vo);
					roleFormationList.push(vo);
				}
			}
			listView.setData(roleFormationList);
			setBattleRolesCount();
		}
		
		private function clear():void 
		{
			for (var i:int = roleFormationList.length - 1; i >= 0; i--)
			{
				var vo:RoleFormationVo = roleFormationList[i] as RoleFormationVo;
				if (vo.avatar)
				{
					if (vo.avatar.parent) vo.avatar.parent.removeChild(vo.avatar);
					pushFreeAvatar(vo.avatar);
					vo.avatar = null;
				}
			}
			roleFormationList.length = 0;
			if (dragAvatar && dragAvatar.parent) dragAvatar.parent.removeChild(dragAvatar); 
			if (overTile) overTile = null;
			for (i = 9; i <= 17; i++)
			{
				MovieClip(this.getChildByName("tile" + i)).gotoAndStop("NotAvatar");
				MovieClip(this.getChildByName("tile" + i)).avatar = null;
			}
			
			cancelDrag();
		}
		
		private function setAwatarState(vo:RoleFormationVo):void 
		{
			if (vo.onBattle)
			{
				if (!vo.avatar) getFreeAvatar(vo);
				var title:MovieClip = MovieClip(this.getChildByName("tile" + vo.pos));
				vo.avatar.x = title.x;
				vo.avatar.y = title.y;
				title.gotoAndStop("HasAvatar");
				title.avatar = vo.avatar;
				addChild(vo.avatar);
			}else
			{
				if (vo.avatar)
				{
					if(vo.avatar.parent) removeChild(vo.avatar);
					pushFreeAvatar(vo.avatar);
					vo.avatar = null;
				}
			}
		}
		
		private function getFreeAvatar(vo:RoleFormationVo):AvatarSprite 
		{
			var avatar:AvatarSprite = freeAvatarList.pop() as AvatarSprite;
			if (!avatar) avatar = new AvatarSprite();
			avatar.load(AvatarUtil.getBackAvatar(vo.role));
			avatar.data = vo;
			avatar.playLoop("wait");
			vo.avatar = avatar;
			return avatar;
		}
		
		private function pushFreeAvatar(avatar:AvatarSprite):void
		{
			avatar.stop();
			freeAvatarList.push(avatar);
		}
		
		private function checkDungeon():void
		{
			var sceneId:int = DataManager.getInstance().currentUser.currentSceneId;
			var vo:SceneClassVo = DataManager.getInstance().getSceneClassById(sceneId);
			isDungeon = vo.type == SceneClassVo.DUNGEON;
		}
		
	}

}