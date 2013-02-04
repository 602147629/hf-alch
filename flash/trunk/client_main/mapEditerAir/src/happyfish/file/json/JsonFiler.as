package happyfish.file.json 
{
	import com.brokenfunction.json.encodeJson;
	import flash.events.EventDispatcher;
	import flash.filesystem.File;
	import flash.filesystem.FileMode;
	import flash.filesystem.FileStream;
	
	/**
	 * ...
	 * @author ...
	 */
	public class JsonFiler extends EventDispatcher 
	{
		private var _encoding:String = "utf8";
		public function JsonFiler() 
		{
			
		}
		
		public function saveFile(fileName:String, value:*):void {
			var outStr:String = encodeJson(value);
			
			var file:File = File.documentsDirectory;
			file = new File(File.applicationDirectory.resolvePath(fileName).nativePath);
			var fileStream:FileStream = new FileStream();
			fileStream.open(file, FileMode.WRITE);
			fileStream.writeMultiByte(outStr,_encoding);
			fileStream.close();
		}
		
	}

}