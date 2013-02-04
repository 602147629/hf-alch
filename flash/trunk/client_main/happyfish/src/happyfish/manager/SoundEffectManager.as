package happyfish.manager
{
	import flash.events.Event;
	import flash.media.Sound;
	import flash.media.SoundTransform;
	import happyfish.cacher.SwfClassCache;
	import happyfish.events.SwfClassCacheEvent;
	import happyfish.manager.module.interfaces.ISoundManager;
	import happymagic.manager.DataManager;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class SoundEffectManager implements ISoundManager
	{
		public var soundEffect:Boolean = true;
		
		private var soundList:Object = new Object;
		
		public function SoundEffectManager(access:Private) 
		{
			
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
				}
			}
			else
			{	
				throw new Error( "SoundEffictManager"+"单例" );
			}
		}
		
		public function playSound(sound:*):void
		{
			if (ShareObjectManager.getInstance().soundEffect) 
			{
				if (sound is Sound) play(sound);
				else if (sound is String)
				{
					var className:String = sound as String;
					var cache:SwfClassCache = SwfClassCache.getInstance();
					if (cache.hasClass(className)) getSoundClassAndPlay(null,className);
					else 
					{
						soundList[className] = new Date().valueOf();
						cache.addEventListener(SwfClassCacheEvent.COMPLETE, getSoundClassAndPlay);
						cache.loadClass(className);
					}
				}
			}
		}
		
		private function getSoundClassAndPlay(event:SwfClassCacheEvent = null, className:String = null):void
		{
			var clsName:String;
			if (event)
			{
				if (soundList[event.className] == null) return;
				event.target.removeEventListener(SwfClassCacheEvent.COMPLETE, getSoundClassAndPlay);
				delete soundList[event.className];
				
				var delay:Number = new Date().valueOf() - soundList[event.className];
				if (delay > 3000) return;
				
				clsName = event.className;
			}
			else clsName = className;
			
			var soundClass:Class = SwfClassCache.getInstance().getClass(clsName);
			var sound:Sound = new soundClass;
			play(sound);
		}
		
		private function play(sound:Sound):void
		{
			sound.addEventListener(Event.COMPLETE, play_complete);
			sound.play(0,0,new SoundTransform(.5));
		}
		
		private function play_complete(e:Event):void 
		{
			var tmpsound:Sound = e.target as Sound;
			e.target.removeEventListener(Event.COMPLETE, play_complete);
			
			tmpsound.close();
			tmpsound=null;
		}
		
		public static function getInstance():SoundEffectManager
		{
			if (instance == null)
			{
				instance = new SoundEffectManager( new Private() );
			}
			return instance;
		}
		
		
		private static var instance:SoundEffectManager;
		
	}
	
}
class Private {}