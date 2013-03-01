
var PageName = '遇怪';
var PageId = 'pcecc8da7624d48fbbc081a8d3ef2806d'
var PageUrl = '遇怪.html'
document.title = '遇怪';

if (top.location != self.location)
{
	if (parent.HandleMainFrameChanged) {
		parent.HandleMainFrameChanged();
	}
}

var $OnLoadVariable = '';

var $CSUM;

var hasQuery = false;
var query = window.location.hash.substring(1);
if (query.length > 0) hasQuery = true;
var vars = query.split("&");
for (var i = 0; i < vars.length; i++) {
    var pair = vars[i].split("=");
    if (pair[0].length > 0) eval("$" + pair[0] + " = decodeURIComponent(pair[1]);");
} 

if (hasQuery && $CSUM != 1) {
alert('Prototype Warning: The variable values were too long to pass to this page.\nIf you are using IE, using Firefox will support more data.');
}

function GetQuerystring() {
    return '#OnLoadVariable=' + encodeURIComponent($OnLoadVariable) + '&CSUM=1';
}

function PopulateVariables(value) {
  value = value.replace(/\[\[OnLoadVariable\]\]/g, $OnLoadVariable);
  value = value.replace(/\[\[PageName\]\]/g, PageName);
  return value;
}

function OnLoad(e) {

}

eval(GetDynamicPanelScript('u8', 1));

var u10 = document.getElementById('u10');
gv_vAlignTable['u10'] = 'center';
var u5 = document.getElementById('u5');
gv_vAlignTable['u5'] = 'center';
var u0 = document.getElementById('u0');

var u3 = document.getElementById('u3');
gv_vAlignTable['u3'] = 'center';
var u9 = document.getElementById('u9');

var u6 = document.getElementById('u6');

var u1 = document.getElementById('u1');
gv_vAlignTable['u1'] = 'center';
var u4 = document.getElementById('u4');

var u7 = document.getElementById('u7');
gv_vAlignTable['u7'] = 'center';
var u2 = document.getElementById('u2');

u2.style.cursor = 'pointer';
if (bIE) u2.attachEvent("onclick", Clicku2);
else u2.addEventListener("click", Clicku2, true);
function Clicku2(e)
{

if (true) {

	SetPanelVisibilityu8("");

}

}

if (bIE) u2.attachEvent("onmouseover", MouseOveru2);
else u2.addEventListener("mouseover", MouseOveru2, true);
function MouseOveru2(e)
{
if (!IsTrueMouseOver('u2',e)) return;
if (true) {

}

}

if (bIE) u2.attachEvent("onmouseout", MouseOutu2);
else u2.addEventListener("mouseout", MouseOutu2, true);
function MouseOutu2(e)
{
if (!IsTrueMouseOut('u2',e)) return;
if (true) {

}

}

var u8 = document.getElementById('u8');

if (window.OnLoad) OnLoad();
