package  
{
	import fl.transitions.Blinds;
	import fl.transitions.easing.*;
	import fl.transitions.Fade;
	import fl.transitions.Fly;
	import fl.transitions.Iris;
	import fl.transitions.Photo;
	import fl.transitions.PixelDissolve;
	import fl.transitions.Rotate;
	import fl.transitions.Squeeze;
	import fl.transitions.Transition;
	import fl.transitions.TransitionManager;
	import fl.transitions.Wipe;
	import fl.transitions.Zoom;
	import flash.display.MovieClip;
	import flash.events.TimerEvent;
	import flash.media.Microphone;
	import flash.utils.Timer;
    import flash.utils.Timer;
    import flash.events.TimerEvent;
    import flash.display.MovieClip;
	/**
	 * ...
	 * @author ZC
	 */
	public class PageStackEffect 
	{
	
                protected var m_transition:TransitionManager;
                protected var m_duration:Number;
                protected var m_doLater:Function;
                
                public static const TOTAL_NUM:uint = 10;
                public static const RANDOM:int = -2;
                
                /**
                 * 构造函数
                 * @param        doLaterFun 执行完动画后的回调函数
                 */
                public function PageStackEffect(doLaterFun:Function = null):void{
                        if (doLaterFun != null) {
                                m_doLater = doLaterFun;
                        }
                }
                
                /**
                 * 设置要过渡的MC
                 * @param        mc                        需要过渡的MC
                 * @param        duration        过渡的秒数
                 * @param        effect                过渡的效果
                 * 具体参数为
                 * 0 - TOTAL_NUM、RANDOM、若不填则需要调用具体的效果函数启动效果
                 */
                public function targetPage(mc:MovieClip, duration:Number = 1, effect:Number = -1) : void {
                        m_transition = new TransitionManager(mc);
                        m_duration = duration;
                        if (effect == -1) {
                                return;
                        }else {
                                var num:int = effect;
                                if (effect == RANDOM) {
                                        //num = MathUtil.random(0, TOTAL_NUM);
                                }
                                switch(num) {
                                        case 0:
                                                blinds();
                                                break;
                                        case 1:
                                                fade();
                                                break;
                                        case 2:
                                                fly();
                                                break;
                                        case 3:
                                                iris();
                                                break;
                                        case 4:
                                                photo();
                                                break;
                                        case 5:
                                                pixelDissolve();
                                                break;
                                        case 6:
                                                rotate();
                                                break;
                                        case 7:
                                                squeeze();
                                                break;
                                        case 8:
                                                wipe();
                                                break;
                                        case 9:
                                                zoom();
                                                break;
                                        //default:
                                                //blinds();
                                                //break;ya ,
                                }
                        }
                }
                
                /**
                 * 遮帘过渡
                 * @param        dimension        遮罩条纹是垂直的 (0) 还是水平的 (1)
                 * @param        numStrips        “遮帘”效果中的遮罩条纹数。 建议的范围是 1 到 50
				 * @param        inandout        1 出现 2 消失
                 */
                public function blinds(dimension:uint = 0, numStrips:uint = 10):void{
                        m_transition.startTransition( { type:Blinds, direction:Transition.IN, duration:m_duration, easing:None.easeNone, numStrips:numStrips, dimension:dimension } );
                        setTimer();
                }
                
                //淡化过渡
                public function fade():void{
                        m_transition.startTransition( { type:Fade, direction:Transition.IN, duration:m_duration, easing:None.easeNone } );
                        setTimer();
                }
                
                /**
                 * 飞行过渡
                 * @param        startPoint        一个指示起始位置的整数；
                 * 左上 1；上中 2；右上 3；左中 4；中心 5；右中 6；左下 7；下中 8；右下 9
                 */
                public function fly(startPoint:uint = 5):void{
                        m_transition.startTransition( { type:Fly, direction:Transition.IN, duration:m_duration, easing:Elastic.easeOut, startPoint:startPoint } );
                        setTimer();
                }
                
                /**
                 * 光圈过渡
                 * @param        shape                遮罩形状 方形(0)或 圆形(1)
                 * @param        startPoint        一个指示起始位置的整数；
                 * 左上 1；上中 2；右上 3；左中 4；中心 5；右中 6；左下 7；下中 8；右下 9
                 */
                public function iris(shape:uint = 0, startPoint:uint = 5):void {
                        var flag:String;
                        if (shape == 0) {
                                flag = Iris.SQUARE;
                        }else {
                                flag = Iris.CIRCLE;
                        }
                        m_transition.startTransition( { type:Iris, direction:Transition.IN, duration:m_duration, easing:Strong.easeOut, startPoint:startPoint, shape:flag } );
                        setTimer();
                }
                
                //照片过渡
                public function photo():void{
                        m_transition.startTransition( { type:Photo, direction:Transition.IN, duration:m_duration, easing:None.easeNone } );
                        setTimer();
                }
                
                /**
                 * 像素溶解
                 * @param        xSections        沿水平轴的遮罩矩形部分的数目。 建议的范围是 1 到 50
                 * @param        ySections        沿垂直轴的遮罩矩形部分的数目。 建议的范围是 1 到 50
				 * @param        inandout        1 出现 0 消失
                 */
                public function pixelDissolve(xSections:uint = 10, ySections:uint = 10, inandout:int = 1):void {
					    if (inandout)
						{
                            m_transition.startTransition( { type:PixelDissolve, direction:Transition.IN, duration:m_duration, easing:None.easeNone, xSections:xSections, ySections:ySections } );							
						}
						else
						{
                            m_transition.startTransition( { type:PixelDissolve, direction:Transition.OUT, duration:m_duration, easing:None.easeNone, xSections:xSections, ySections:ySections } );								
						}

                        setTimer();
                }
                
                /**
                 * 旋转
                 * @param        ccw                对于顺时针旋转为 false；对于逆时针旋转为 true
                 * @param        degrees        旋转的度数。 建议的范围是 1 到 9999
                 */
                public function rotate(ccw:Boolean = false, degrees:uint = 720):void{
                        m_transition.startTransition( { type:Rotate, direction:Transition.IN, duration:m_duration, easing:Strong.easeInOut, ccw:ccw, degrees:degrees } );
                        setTimer();
                }
                
                /**
                 * 挤压过渡
                 * @param        dimension 指示“挤压”效果应是水平的 (0) 还是垂直的 (1)
                 */
                public function squeeze(dimension:uint = 1):void{
                        m_transition.startTransition( { type:Squeeze, direction:Transition.IN, duration:m_duration, easing:Elastic.easeOut, dimension:dimension } );
                        setTimer();
                }
                
                /**
                 * 划入划出
                 * @param        startPoint 一个指示起始位置的整数；
                 * 左上 1；上中 2；右上 3；左中 4；中心 5；右中 6；左下 7；下中 8；右下 9
                 */
                public function wipe(startPoint:uint = 1):void{
                        m_transition.startTransition( { type:Wipe, direction:Transition.IN, duration:m_duration, easing:None.easeNone, startPoint:startPoint } );
                        setTimer();
                }
                
                //缩放
                public function zoom():void{
                        m_transition.startTransition( { type:Zoom, direction:Transition.IN, duration:m_duration, easing:Elastic.easeOut } );
                        setTimer();
                }
                
                protected function setTimer():void {
                        var timer:Timer = new Timer(m_duration * 1000, 1);
                        timer.addEventListener(TimerEvent.TIMER, onComplete_handler);
                        timer.start();
                }
                
                protected function onComplete_handler(e:TimerEvent):void {
                        e.currentTarget.removeEventListener(TimerEvent.TIMER, onComplete_handler);
                        m_doLater();
                }
        }        
}