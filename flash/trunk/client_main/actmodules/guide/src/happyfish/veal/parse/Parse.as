package happyfish.veal.parse 
{
	import flash.utils.getDefinitionByName;
	/**
	 * ...
	 * @author lite3
	 */
	public class Parse 
	{
		public static const baseParamMap:ParamMap = new ParamMap();
		
		private static const binaryArr:Vector.<int> = new Vector.<int>(6);
		private static const stopArr:Vector.<int> = new Vector.<int>(4);
		
		
		// 符号栈
		private var signStack:Vector.<int>;
		// 表达式栈
		private var expStack:Array;
		
		private var leftBracketCount:int = 0;
		private var righBracketCount:int = 0;
		private var leftParenCount:int = 0;
		private var rightParenCount:int = 0;
		private var leftBraceCount:int = 0;
		private var rightBraceCount:int = 0;
		private var s:String
		private var i:int;
		private var len:int;
		// 参数列表 Map<String, *>
		private var paramMap:Object;
		
		/**
		 * 
		 * @param	s 字符串表达式
		 */
		public function Parse(s:String, paramMap:Object = null)
		{
			// 初始化
			if (0 == binaryArr[0])
			{
				binaryArr[0] = Sign.Equality;
				binaryArr[1] = Sign.Inequality;
				binaryArr[2] = Sign.GreaterThanOrEqualTo;
				binaryArr[3] = Sign.LessThanOrEqualTo;
				binaryArr[4] = Char.LeftAngle;
				binaryArr[5] = Char.RightAngle;
				stopArr[0] = Char.LeftParen;
				stopArr[1] = Char.Comma;
				stopArr[2] = Char.LeftBrace;
				stopArr[3] = Char.LeftBracket;
			}
			signStack = new Vector.<int>();
			expStack = [];
			this.paramMap = paramMap;
			this.s = s;
		}
		
		public function getInput():String
		{
			return s;
		}
		
		public function getValue():*
		{
			len = s.length;
			for (i = 0; i < len; i++)
			{
				var ch:int = s.charCodeAt(i);
				
				switch(ch)
				{
					case Char.Dot :
						expStack.push(new Sign(ch));
						signStack.push(ch);
						break;
						
					case Char.Comma :
						doBinaryOperator();
						expStack.push(new Sign(ch));
						signStack.push(ch);
						break;
						
					case Char.SingleQuote :
					case Char.DoubleQuote :
						expStack.push(getStr(ch));
						break;
					
					case Char.LeftBracket :
						leftBracketCount++;
						expStack.push(new Sign(ch));
						signStack.push(ch);
						break;
						
					case Char.RightBracket :
						checkRightBracketSyntax();
						doBinaryOperator();
						doRightBracket();
						break;
						
					case Char.LeftParen :
						leftParenCount++;
						expStack.push(new Sign(ch));
						signStack.push(ch);
						break;
						
					case Char.RightParen :
						checkRightParenSyntax();
						doBinaryOperator();
						doRightParen();
						break;
						
					case Char.LeftBrace :
						leftBraceCount++;
						expStack.push(new Sign(ch));
						signStack.push(ch);
						break;
						
					case Char.RightBrace :
						checkRightBraceSyntax();
						doBinaryOperator();
						doRightBrace();
						break;
						
					// 两个相同的符号
					case Char.Equal : // ==
					case Char.Colon : // ::
						if (s.charCodeAt(i + 1) != ch) throw new Error("find unknow sign:" + String.fromCharCode(ch));
						i++;
						signStack.push((ch << 8) + ch);
						expStack.push(new Sign((ch << 8) + ch));
						break;
					// !=
					case Char.Bang :
						if (s.charCodeAt(i + 1) != Char.Equal) throw new Error("find unknow sign:" + String.fromCharCode(ch));
						i++;
						signStack.push(Sign.Inequality);
						expStack.push(new Sign(Sign.Inequality));
						break;
						
					case Char.LeftAngle  : // <
					case Char.RightAngle : // >
						// < or >
						if (s.charCodeAt(i + 1) != Char.Equal)
						{
							signStack.push( ch);
							expStack.push(new Sign(ch));
						}
						// <= or >=
						else
						{
							i++;
							signStack.push((ch << 8) + Char.Equal);
							expStack.push(new Sign((ch << 8) + Char.Equal));
						}
						break;
					case Char.Space :
						break;
						
					default :
						if (Char.Dash == ch || (ch >= Char.Zero && ch <= Char.Nine))
						{
							expStack.push(getNum());
						}else if (Char.isIdentifierStart(ch))
						{
							expStack.push(getIdentifierStr());
							doIdentifier();
						}else
						{
							throw new Error("find unknow character:" + s.charAt(i) + "\"");
						}
						break;
				}
			}
			
			doBinaryOperator();
			
			if (expStack.length != 1)
			{
				// 最后应该只有一个元素,就是结果
				var msg:String = "has more element: len=" +expStack.length + " expStack=" + expStack.toString();
				throw  new Error(msg);
			}
			return expStack[0];
		}
		
		/**
		 * 执行二目运算操作, 这里仅有逻辑比较运算符(> >= < <= == !=)
		 */
		private function doBinaryOperator():void
		{
			var signArr:Vector.<int> = new Vector.<int>();
			
			var hasBinary:Boolean = false;
			var len:int = expStack.length;
			for (var i:int = len - 2; i >= 0; i--)
			{
				var sign:Sign = expStack[i] as Sign;
				if (!sign || 0 == i) continue;
				
				if (binaryArr.indexOf(sign.ch) >= 0) hasBinary = true;
				else if (stopArr.indexOf(sign.ch) >= 0) break;
				
				signArr.push(sign.ch);
			}
			
			if (!hasBinary) return;
			var begin:int = ++i;
			var signIdx:int = signStack.length - signArr.length - 1;
			for (i = begin; i <= len - 2; i++)
			{
				sign = expStack[i] as Sign;
				if (!sign || 0 == i) continue;
				
				signIdx++;
				var r:Boolean = false;
				var hasResult:Boolean = true;
				switch(sign.ch)
				{
					case Sign.Equality   : r = expStack[i - 1] == expStack[i + 1]; break;
					case Sign.Inequality : r = expStack[i - 1] != expStack[i + 1]; break;
					case Char.LeftAngle  : r = expStack[i - 1] <  expStack[i + 1]; break;
					case Char.RightAngle : r = expStack[i - 1] >  expStack[i + 1]; break;
					case Sign.GreaterThanOrEqualTo : r = expStack[i - 1] >= expStack[i + 1]; break;
					case Sign.LessThanOrEqualTo    : r = expStack[i - 1] <= expStack[i + 1]; break;
					default : hasResult = false; break;
				}
				if (hasResult)
				{
					len -= 2;
					expStack[i - 1] = r;
					expStack.splice(i, 2);
					signStack.splice(signIdx, 1);
					i--;
				}
			}
		}
		
		/**
		 * 一个调用
		 * @return 是否调用成功
		 */
		private function doIdentifier():Boolean 
		{
			var len:int = expStack.length;
			
			if (expStack.length < 2) return false;
			
			var sign:Sign = Sign(expStack[len - 2]);
			// o.xxx 这种形式,方法调用或者调用属性,
			//至少要有两个元素
			if (Char.Dot == sign.ch)
			{
				if (len < 3) return false;
				var o:* = expStack[len - 3];
				var hasErr:Boolean = false;
				try {
					var r:* = o[expStack[len-1]];
				}catch (err:Error)
				{
					showErr(err);
					hasErr = true;
				}
				if (!hasErr)
				{
					// 设置结果
					expStack[len - 3] = r;
					expStack.splice(len - 2, 2);
					signStack.pop();
					return true;
				}
			}
			// 获取一个定义(类,接口,函数)
			else if(Sign.NameSpace == sign.ch)
			{
				doCreateClass();
				return true;
			}
			
			return false;
		}
		
		private function doRightParen():void 
		{
			var len:int = expStack.length;
			var argList:Array = [];
			// 这是(
			var signNum:int = 1;
			
			// i > 1 一个参数和一个逗号共两个应该是i>=1,并且必须有一个执行者,就是i>1
			for (var i:int = len - 1; i >= 0; i--)
			{
				var sign:Sign = expStack[i] as Sign;
				// 一个参数
				if (null == sign)
				{
					argList.push(expStack[i]);
				}
				// 左括号
				else if (Char.LeftParen == sign.ch) break;
				// 逗号
				else if (Char.Comma == sign.ch) continue;
				// 未知的符号
				else throw new Error("find unnokn sign:" + String.fromCharCode(sign.ch));
			}
			if (Sign(expStack[i]).ch != Char.LeftParen) throw Error("not find left paren");
			
			var argLen:int = argList.length;
			
			// 一个(x)操作, 更改顺序
			if (argLen > 0 && (0==i || expStack[i-1] is Sign))
			{
				expStack[i] = argList[argLen - 1];
				expStack.splice(i + 1, argLen * 2);
				signStack.splice(signStack.length - argLen, argLen);
				
				if (i != 0) doIdentifier();
			}
			// 方法调用
			else
			{
				// TODO 这里有问题. 要再添加一个Function的类型
				var fun:Function = expStack[i - 1] as Function;
				var r:* = fun.apply(null, argList.reverse());
				expStack[i - 1] = r;
				var delExps:int = argLen > 0 ? argLen * 2 : 1;
				var delSigns:int = argLen > 0 ? argLen : 1;
				expStack.splice(i, delExps);
				//expStack.splice(i, argLen * 2 - 1 + 1);
				signStack.splice(signStack.length - delSigns, delSigns);
			}
		}
		
		private function doRightBracket():void 
		{
			var len:int = expStack.length;
			if (len < 3 || Sign(expStack[len - 2]).ch != Char.LeftBracket) throw  new Error("doRightBracket Error");
			
			var o:* = expStack[len - 3];
			var r:* = o[expStack[len - 1]];
			expStack[len - 3] = r;
			leftBracketCount--;
			expStack.splice(len - 2, 2);
			signStack.splice(signStack.length - 1, 1);
		}
		
		private function doRightBrace():void 
		{
			// {event
			var len:int = expStack.length;
			if (len < 2 || Sign(expStack[len - 2]).ch != Char.LeftBrace) throw  new Error("doRightBrace Error");
			
			
			var r:* = getParamValue(expStack[len - 1]);
			expStack[len - 2] = r;
			leftBraceCount--;
			expStack.splice(len - 1, 1);
			signStack.splice(signStack.length - 1, 1);
			
			if (len > 2) doIdentifier();;
		}
		
		private function doCreateClass():void 
		{
			var dotNum:int = 0;
			var len:int = expStack.length;
			var className:String = "::" + expStack[len - 1];
			for (var i:int = len - 3; i >= 0; i--)
			{
				var tmp:* = expStack[i];
				if (tmp is Sign)
				{
					if (Char.Dot == Sign(tmp).ch)
					{
						className = "." + className;
						dotNum++;
					}else
					{
						i++;
						break;
					}
				}else if (tmp is String)
				{
					className = tmp + className;
				}
			}
			var ref:Object = getDefinitionByName(className);
			if (i < 0) i = 0;
			expStack[i] = ref;
			expStack.splice(i + 1, expStack.length - i - 1);
			signStack.splice(signStack.length - dotNum - 1, dotNum + 1);
		}
		
		private function getIdentifierStr():* 
		{
			var j:int = i + 1;
			for (j; j < len; j++)
			{
				var ch:int = s.charCodeAt(j);
				if (!Char.isIdentifierPart(ch))
				{
					break;
				}
			}
			var str:String = s.substring(i, j);
			i = j - 1;
			if ("null" == str) return null;
			else if ("false" == str) return false;
			else if ("true" == str) return true;
			
			return str;
		}
		
		private function getStr(endCh:int):String 
		{
			var idx:int = s.indexOf(String.fromCharCode(endCh), i + 1);
			if ( -1 == idx) throw new Error("pase Error, not find end \"");
			var str:String = s.substring(i + 1, idx);
			i = idx;
			return str;
		}
		
		private function getNum():Number 
		{
			var j:int = i;
			var hasDash:Boolean = false;
			var hasDot:Boolean = false;
			for (j; j < len; j++)
			{
				var ch:int = s.charCodeAt(j);
				if (Char.Dash == ch)
				{
					if (!hasDash) hasDash = true;
					else throw new Error("has 2 dash");
				}else if (Char.Dot == ch)
				{
					if (!hasDot) hasDot = true;
					else throw new Error("has 2 dot");
				}else if (!Char.isDigit(ch))
				{
					break;
				}
			};
			var n:Number = parseFloat(s.substring(i, j));
			i = j - 1;
			return n;
		}
		
		private function getParamValue(name:String):*
		{
			if (paramMap && (name in paramMap)) return paramMap[name];
			return baseParamMap.getValue(name);
		}
		
		private function checkRightBracketSyntax():void 
		{
			if (righBracketCount >= leftBracketCount)
			{
				throw new Error("find right bracket, not pair");
			}
			if (Char.LeftParen == signStack[signStack.length - 1])
			{
				throw new Error("find right bracket, prev sign is left paren");
			}
		}
		
		private function checkRightParenSyntax():void 
		{
			if (righBracketCount >= leftParenCount)
			{
				throw new Error("find right paren, not pair");
			}
			if (Char.LeftBracket == signStack[signStack.length - 1])
			{
				throw new Error("find right paren, prev sign is left paren");
			}
		}
		private function checkRightBraceSyntax():void 
		{
			if (rightBraceCount >= leftBraceCount)
			{
				throw new Error("find right brace, not pair");
			}
			//if (Char.LeftBrace == signStack[signStack.length - 1])
			//{
				//throw new Error("find right brace, prev sign is left brace");
			//}
		}
	}

}