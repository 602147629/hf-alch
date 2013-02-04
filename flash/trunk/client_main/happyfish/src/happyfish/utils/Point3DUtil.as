package happyfish.utils 
{
	import com.friendsofed.isometric.Point3D;
	/**
	 * ...
	 * @author lite3
	 */
	public class Point3DUtil 
	{
		
		public static function equals(p1:Point3D, p2:Point3D):Boolean 
		{
			return p1 == p2 || (p1.x == p2.x && p1.y == p2.y && p1.z == p2.z);
		}
		
	}

}