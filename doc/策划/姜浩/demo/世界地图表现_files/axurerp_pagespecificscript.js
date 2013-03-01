
var PageName = '世界地图表现';
var PageId = 'p66f58c67582d43a89b10b4de963c398b'
var PageUrl = '世界地图表现.html'
document.title = '世界地图表现';

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

eval(GetDynamicPanelScript('u32', 1));

eval(GetDynamicPanelScript('u60', 1));

eval(GetDynamicPanelScript('u42', 1));

eval(GetDynamicPanelScript('u37', 1));

eval(GetDynamicPanelScript('u56', 1));

eval(GetDynamicPanelScript('u13', 1));

eval(GetDynamicPanelScript('u8', 1));

var u20 = document.getElementById('u20');

var u64 = document.getElementById('u64');

u64.style.cursor = 'pointer';
if (bIE) u64.attachEvent("onclick", Clicku64);
else u64.addEventListener("click", Clicku64, true);
function Clicku64(e)
{

if (true) {

	SetPanelVisibilityu60("hidden");

}

}

var u51 = document.getElementById('u51');
gv_vAlignTable['u51'] = 'center';
var u36 = document.getElementById('u36');

u36.style.cursor = 'pointer';
if (bIE) u36.attachEvent("onclick", Clicku36);
else u36.addEventListener("click", Clicku36, true);
function Clicku36(e)
{

if (true) {

	SetPanelVisibilityu32("hidden");

}

}

var u31 = document.getElementById('u31');
gv_vAlignTable['u31'] = 'center';
var u45 = document.getElementById('u45');

u45.style.cursor = 'pointer';
if (bIE) u45.attachEvent("onclick", u45Click);
else u45.addEventListener("click", u45Click, true);
InsertAfterBegin(document.body, "<DIV class='intcases' id='u45LinksClick'></DIV>")
var u45LinksClick = document.getElementById('u45LinksClick');
function u45Click(e) 
{

	ToggleLinks(e, 'u45LinksClick');
}

InsertBeforeEnd(u45LinksClick, "<div class='intcaselink' onmouseout='SuppressBubble(event)' onclick='u45Clickua88276c99d7e4a6a90ea97ddf06fb769(event)'>道具足够</div>");
function u45Clickua88276c99d7e4a6a90ea97ddf06fb769(e)
{

	SetPanelVisibilityu56("");

	SetPanelVisibilityu42("hidden");

	ToggleLinks(e, 'u45LinksClick');
}

InsertBeforeEnd(u45LinksClick, "<div class='intcaselink' onmouseout='SuppressBubble(event)' onclick='u45Clickuf17661ab2b384c3c8065e35579c2a8c8(event)'>道具不足</div>");
function u45Clickuf17661ab2b384c3c8065e35579c2a8c8(e)
{

	SetPanelVisibilityu60("");

	SetPanelVisibilityu42("hidden");

	ToggleLinks(e, 'u45LinksClick');
}

var u11 = document.getElementById('u11');

u11.style.cursor = 'pointer';
if (bIE) u11.attachEvent("onclick", Clicku11);
else u11.addEventListener("click", Clicku11, true);
function Clicku11(e)
{

if (true) {

	SetPanelVisibilityu32("");

}

}

if (bIE) u11.attachEvent("onmouseover", MouseOveru11);
else u11.addEventListener("mouseover", MouseOveru11, true);
function MouseOveru11(e)
{
if (!IsTrueMouseOver('u11',e)) return;
if (true) {

	SetPanelVisibilityu8("");

}

}

if (bIE) u11.attachEvent("onmouseout", MouseOutu11);
else u11.addEventListener("mouseout", MouseOutu11, true);
function MouseOutu11(e)
{
if (!IsTrueMouseOut('u11',e)) return;
if (true) {

	SetPanelVisibilityu8("hidden");

}

}

var u27 = document.getElementById('u27');
gv_vAlignTable['u27'] = 'center';
var u6 = document.getElementById('u6');
gv_vAlignTable['u6'] = 'center';
var u4 = document.getElementById('u4');
gv_vAlignTable['u4'] = 'center';
var u2 = document.getElementById('u2');

u2.style.cursor = 'pointer';
if (bIE) u2.attachEvent("onclick", Clicku2);
else u2.addEventListener("click", Clicku2, true);
function Clicku2(e)
{

if (true) {

	SetPanelVisibilityu37("");

}

}

var u10 = document.getElementById('u10');
gv_vAlignTable['u10'] = 'center';
var u0 = document.getElementById('u0');

var u26 = document.getElementById('u26');

var u49 = document.getElementById('u49');

var u63 = document.getElementById('u63');

var u35 = document.getElementById('u35');

u35.style.cursor = 'pointer';
if (bIE) u35.attachEvent("onclick", u35Click);
else u35.addEventListener("click", u35Click, true);
InsertAfterBegin(document.body, "<DIV class='intcases' id='u35LinksClick'></DIV>")
var u35LinksClick = document.getElementById('u35LinksClick');
function u35Click(e) 
{

	ToggleLinks(e, 'u35LinksClick');
}

InsertBeforeEnd(u35LinksClick, "<div class='intcaselink' onmouseout='SuppressBubble(event)' onclick='u35Clicku38111090780540bcba9ffaa616f694ce(event)'>行动力足够</div>");
function u35Clicku38111090780540bcba9ffaa616f694ce(e)
{

	SetPanelVisibilityu13("");

	SetPanelVisibilityu32("hidden");

	ToggleLinks(e, 'u35LinksClick');
}

InsertBeforeEnd(u35LinksClick, "<div class='intcaselink' onmouseout='SuppressBubble(event)' onclick='u35Clickuafb1c2e6f8304084bb118574226fcfa0(event)'>行动力不足</div>");
function u35Clickuafb1c2e6f8304084bb118574226fcfa0(e)
{

	SetPanelVisibilityu42("");

	SetPanelVisibilityu32("hidden");

	ToggleLinks(e, 'u35LinksClick');
}

var u29 = document.getElementById('u29');
gv_vAlignTable['u29'] = 'center';
var u54 = document.getElementById('u54');

var u8 = document.getElementById('u8');

var u34 = document.getElementById('u34');
gv_vAlignTable['u34'] = 'center';
var u14 = document.getElementById('u14');

var u48 = document.getElementById('u48');

var u28 = document.getElementById('u28');

var u44 = document.getElementById('u44');
gv_vAlignTable['u44'] = 'center';
var u33 = document.getElementById('u33');

var u50 = document.getElementById('u50');

var u22 = document.getElementById('u22');

var u52 = document.getElementById('u52');

var u13 = document.getElementById('u13');

var u47 = document.getElementById('u47');
gv_vAlignTable['u47'] = 'top';
var u12 = document.getElementById('u12');
gv_vAlignTable['u12'] = 'center';
var u41 = document.getElementById('u41');

u41.style.cursor = 'pointer';
if (bIE) u41.attachEvent("onclick", Clicku41);
else u41.addEventListener("click", Clicku41, true);
function Clicku41(e)
{

if (true) {

	SetPanelVisibilityu37("hidden");

}

}

var u53 = document.getElementById('u53');
gv_vAlignTable['u53'] = 'center';
var u57 = document.getElementById('u57');

u57.style.cursor = 'pointer';
if (bIE) u57.attachEvent("onclick", Clicku57);
else u57.addEventListener("click", Clicku57, true);
function Clicku57(e)
{

if (true) {

	SetPanelVisibilityu56("hidden");

}

}

var u21 = document.getElementById('u21');
gv_vAlignTable['u21'] = 'center';
var u37 = document.getElementById('u37');

var u7 = document.getElementById('u7');

var u40 = document.getElementById('u40');

u40.style.cursor = 'pointer';
if (bIE) u40.attachEvent("onclick", u40Click);
else u40.addEventListener("click", u40Click, true);
InsertAfterBegin(document.body, "<DIV class='intcases' id='u40LinksClick'></DIV>")
var u40LinksClick = document.getElementById('u40LinksClick');
function u40Click(e) 
{

	ToggleLinks(e, 'u40LinksClick');
}

InsertBeforeEnd(u40LinksClick, "<div class='intcaselink' onmouseout='SuppressBubble(event)' onclick='u40Clicku442c30cbb25447dcb4d2d8c4bf56ba71(event)'>Case 1</div>");
function u40Clicku442c30cbb25447dcb4d2d8c4bf56ba71(e)
{

	SetPanelVisibilityu13("");

	ToggleLinks(e, 'u40LinksClick');
}

InsertBeforeEnd(u40LinksClick, "<div class='intcaselink' onmouseout='SuppressBubble(event)' onclick='u40Clicku835cfed1cae64e11871b7ec4ed67b5f6(event)'>Case 2</div>");
function u40Clicku835cfed1cae64e11871b7ec4ed67b5f6(e)
{

	SetPanelVisibilityu37("hidden");

	ToggleLinks(e, 'u40LinksClick');
}

var u17 = document.getElementById('u17');
gv_vAlignTable['u17'] = 'center';
var u5 = document.getElementById('u5');

var u15 = document.getElementById('u15');
gv_vAlignTable['u15'] = 'center';
var u56 = document.getElementById('u56');

var u3 = document.getElementById('u3');

var u65 = document.getElementById('u65');

var u1 = document.getElementById('u1');
gv_vAlignTable['u1'] = 'center';
var u25 = document.getElementById('u25');
gv_vAlignTable['u25'] = 'center';
var u59 = document.getElementById('u59');

var u43 = document.getElementById('u43');

var u16 = document.getElementById('u16');

var u39 = document.getElementById('u39');
gv_vAlignTable['u39'] = 'center';
var u19 = document.getElementById('u19');
gv_vAlignTable['u19'] = 'center';
var u9 = document.getElementById('u9');

var u30 = document.getElementById('u30');

var u60 = document.getElementById('u60');

var u24 = document.getElementById('u24');

var u46 = document.getElementById('u46');

u46.style.cursor = 'pointer';
if (bIE) u46.attachEvent("onclick", Clicku46);
else u46.addEventListener("click", Clicku46, true);
function Clicku46(e)
{

if (true) {

	SetPanelVisibilityu42("hidden");

}

}

var u55 = document.getElementById('u55');
gv_vAlignTable['u55'] = 'center';
var u38 = document.getElementById('u38');

var u61 = document.getElementById('u61');

var u18 = document.getElementById('u18');

var u62 = document.getElementById('u62');
gv_vAlignTable['u62'] = 'center';
var u32 = document.getElementById('u32');

var u42 = document.getElementById('u42');

var u23 = document.getElementById('u23');
gv_vAlignTable['u23'] = 'center';
var u58 = document.getElementById('u58');
gv_vAlignTable['u58'] = 'center';
if (window.OnLoad) OnLoad();
