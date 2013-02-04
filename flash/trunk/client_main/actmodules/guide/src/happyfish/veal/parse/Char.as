package happyfish.veal.parse 
{
	/**
	 * ...
	 * @author lite3
	 */
	public class Char 
	{
		public static const EOS:int = 0;
        public static const a:int = "a".charCodeAt(0);
        public static const b:int = "b".charCodeAt(0);
        public static const c:int = "c".charCodeAt(0);
        public static const d:int = "d".charCodeAt(0);
        public static const e:int = "e".charCodeAt(0);
        public static const f:int = "f".charCodeAt(0);
        public static const g:int = "g".charCodeAt(0);
        public static const h:int = "h".charCodeAt(0);
        public static const i:int = "i".charCodeAt(0);
        public static const j:int = "j".charCodeAt(0);
        public static const k:int = "k".charCodeAt(0);
        public static const l:int = "l".charCodeAt(0);
        public static const m:int = "m".charCodeAt(0);
        public static const n:int = "n".charCodeAt(0);
        public static const o:int = "o".charCodeAt(0);
        public static const p:int = "p".charCodeAt(0);
        public static const q:int = "q".charCodeAt(0);
        public static const r:int = "r".charCodeAt(0);
        public static const s:int = "s".charCodeAt(0);
        public static const t:int = "t".charCodeAt(0);
        public static const u:int = "u".charCodeAt(0);
        public static const v:int = "v".charCodeAt(0);
        public static const w:int = "w".charCodeAt(0);
        public static const x:int = "x".charCodeAt(0);
        public static const y:int = "y".charCodeAt(0);
        public static const z:int = "z".charCodeAt(0);
        public static const A:int = "A".charCodeAt(0);
        public static const B:int = "B".charCodeAt(0);
        public static const C:int = "C".charCodeAt(0);
        public static const D:int = "D".charCodeAt(0);
        public static const E:int = "E".charCodeAt(0);
        public static const F:int = "F".charCodeAt(0);
        public static const G:int = "G".charCodeAt(0);
        public static const H:int = "H".charCodeAt(0);
        public static const I:int = "I".charCodeAt(0);
        public static const J:int = "J".charCodeAt(0);
        public static const K:int = "K".charCodeAt(0);
        public static const L:int = "L".charCodeAt(0);
        public static const M:int = "M".charCodeAt(0);
        public static const N:int = "N".charCodeAt(0);
        public static const O:int = "O".charCodeAt(0);
        public static const P:int = "P".charCodeAt(0);
        public static const Q:int = "Q".charCodeAt(0);
        public static const R:int = "R".charCodeAt(0);
        public static const S:int = "S".charCodeAt(0);
        public static const T:int = "T".charCodeAt(0);
        public static const U:int = "U".charCodeAt(0);
        public static const V:int = "V".charCodeAt(0);
        public static const W:int = "W".charCodeAt(0);
        public static const X:int = "X".charCodeAt(0);
        public static const Y:int = "Y".charCodeAt(0);
        public static const Z:int = "Z".charCodeAt(0);
        public static const Zero:int = "0".charCodeAt(0);
        public static const One:int = "1".charCodeAt(0);
        public static const Two:int = "2".charCodeAt(0);
        public static const Three:int = "3".charCodeAt(0);
        public static const Four:int = "4".charCodeAt(0);
        public static const Five:int = "5".charCodeAt(0);
        public static const Six:int = "6".charCodeAt(0);
        public static const Seven:int = "7".charCodeAt(0);
        public static const Eight:int = "8".charCodeAt(0);
        public static const Nine:int = "9".charCodeAt(0);
        public static const Dot:int = ".".charCodeAt(0);
        public static const Bang:int = "!".charCodeAt(0);
        public static const Equal:int = "=".charCodeAt(0);
        public static const Percent:int = "%".charCodeAt(0);
        public static const Ampersand:int = "&".charCodeAt(0);
        public static const Asterisk:int = "*".charCodeAt(0);
        public static const Plus:int = "+".charCodeAt(0);
        public static const Dash:int = "-".charCodeAt(0);
        public static const Slash:int = "/".charCodeAt(0);
        public static const BackSlash:int = "\\".charCodeAt(0);
        public static const Comma:int = ",".charCodeAt(0);
        public static const Colon:int = ":".charCodeAt(0);
        public static const Semicolon:int = ";".charCodeAt(0);
        public static const LeftAngle:int = "<".charCodeAt(0);
        public static const RightAngle:int = ">".charCodeAt(0);
        public static const Caret:int = "^".charCodeAt(0);
        public static const Bar:int = "|".charCodeAt(0);
        public static const QuestionMark:int = "?".charCodeAt(0);
        public static const LeftParen:int = "(".charCodeAt(0);
        public static const RightParen:int = ")".charCodeAt(0);
        public static const LeftBrace:int = "{".charCodeAt(0);
        public static const RightBrace:int = "}".charCodeAt(0);
        public static const LeftBracket:int = "[".charCodeAt(0);
        public static const RightBracket:int = "]".charCodeAt(0);
        public static const Tilde:int = "~".charCodeAt(0);
        public static const At:int = "@".charCodeAt(0);
        public static const SingleQuote:int = "'".charCodeAt(0);
        public static const DoubleQuote:int = "\"".charCodeAt(0);
        public static const UnderScore:int = "_".charCodeAt(0);
        public static const Dollar:int = "$".charCodeAt(0);
        public static const Space:int = " ".charCodeAt(0);
        public static const Tab:int = "\t".charCodeAt(0);
        public static const VerticalTab:int = "\v".charCodeAt(0);
        public static const Newline:int = "\n".charCodeAt(0);
        public static const CarriageReturn:int = "\r".charCodeAt(0);
        // inexplicably missing constants from Char :((. WTF is going on?
        public static const Backspace:int = "\b".charCodeAt(0);
        public static const Formfeed:int = "\f".charCodeAt(0);
        public static const Minus:int = Dash;
    
        public static function fromOctal (str:String): int
        {
			return parseInt (str);
        }
    
        public static function fromHex (str:String): int
        {
			return parseInt (str);
        }
    
        public static function isIdentifierStart(c:int):Boolean
		{
            if (c >= Char.A && c <= Char.Z) return true;
            else if (c >= Char.a && c <= Char.z) return true;
            else if (c == Char.UnderScore) return true;
            else if (c == Char.Dollar) return true;
            return false;
        }
    
        public static function isDigit (c:int):Boolean
		{
            if (c >= Char.Zero && c <= Char.Nine) return true;
            return false;
        }
    
        public static function isIdentifierPart(c:int):Boolean
		{
            if (isIdentifierStart (c)) return true;
            else if (isDigit (c)) return true;
            return false;
        }
		
        public static function test ():void {
            trace ("testing lex-char.es");
            trace ("Space=",Space);
            trace ("Tab=",Tab);
            trace ("Newline=",Newline);
        }
	}
}