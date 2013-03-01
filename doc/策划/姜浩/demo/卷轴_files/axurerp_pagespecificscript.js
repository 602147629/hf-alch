
var PageName = '卷轴';
var PageId = 'p121389c215de44a4b1e713efbea157d8'
var PageUrl = '卷轴.html'
document.title = '卷轴';

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

var u20 = document.getElementById('u20');
gv_vAlignTable['u20'] = 'top';
var u64 = document.getElementById('u64');
gv_vAlignTable['u64'] = 'center';
var u51 = document.getElementById('u51');
gv_vAlignTable['u51'] = 'top';
var u36 = document.getElementById('u36');
gv_vAlignTable['u36'] = 'center';
var u31 = document.getElementById('u31');

if (bIE) u31.attachEvent("onmouseover", MouseOveru31);
else u31.addEventListener("mouseover", MouseOveru31, true);
function MouseOveru31(e)
{
if (!IsTrueMouseOver('u31',e)) return;
if (true) {

}

}

if (bIE) u31.attachEvent("onmouseout", MouseOutu31);
else u31.addEventListener("mouseout", MouseOutu31, true);
function MouseOutu31(e)
{
if (!IsTrueMouseOut('u31',e)) return;
if (true) {

}

}

var u45 = document.getElementById('u45');
gv_vAlignTable['u45'] = 'top';
var u11 = document.getElementById('u11');

var u27 = document.getElementById('u27');

if (bIE) u27.attachEvent("onmouseover", MouseOveru27);
else u27.addEventListener("mouseover", MouseOveru27, true);
function MouseOveru27(e)
{
if (!IsTrueMouseOver('u27',e)) return;
if (true) {

}

}

if (bIE) u27.attachEvent("onmouseout", MouseOutu27);
else u27.addEventListener("mouseout", MouseOutu27, true);
function MouseOutu27(e)
{
if (!IsTrueMouseOut('u27',e)) return;
if (true) {

}

}

var u6 = document.getElementById('u6');

var u67 = document.getElementById('u67');
gv_vAlignTable['u67'] = 'top';
var u4 = document.getElementById('u4');

var u2 = document.getElementById('u2');

var u10 = document.getElementById('u10');

u10.style.cursor = 'pointer';
if (bIE) u10.attachEvent("onclick", Clicku10);
else u10.addEventListener("click", Clicku10, true);
function Clicku10(e)
{

if (true) {

	self.location.href="道具.html" + GetQuerystring();

}

}
gv_vAlignTable['u10'] = 'top';
var u0 = document.getElementById('u0');

var u26 = document.getElementById('u26');

u26.style.cursor = 'pointer';
if (bIE) u26.attachEvent("onclick", Clicku26);
else u26.addEventListener("click", Clicku26, true);
function Clicku26(e)
{

if (true) {

	self.location.href="仓库.html" + GetQuerystring();

}

}
gv_vAlignTable['u26'] = 'top';
var u49 = document.getElementById('u49');

if (bIE) u49.attachEvent("onmouseover", MouseOveru49);
else u49.addEventListener("mouseover", MouseOveru49, true);
function MouseOveru49(e)
{
if (!IsTrueMouseOver('u49',e)) return;
if (true) {

}

}

if (bIE) u49.attachEvent("onmouseout", MouseOutu49);
else u49.addEventListener("mouseout", MouseOutu49, true);
function MouseOutu49(e)
{
if (!IsTrueMouseOut('u49',e)) return;
if (true) {

}

}

var u63 = document.getElementById('u63');

var u35 = document.getElementById('u35');

var u29 = document.getElementById('u29');

if (bIE) u29.attachEvent("onmouseover", MouseOveru29);
else u29.addEventListener("mouseover", MouseOveru29, true);
function MouseOveru29(e)
{
if (!IsTrueMouseOver('u29',e)) return;
if (true) {

}

}

if (bIE) u29.attachEvent("onmouseout", MouseOutu29);
else u29.addEventListener("mouseout", MouseOutu29, true);
function MouseOutu29(e)
{
if (!IsTrueMouseOut('u29',e)) return;
if (true) {

}

}

var u54 = document.getElementById('u54');
gv_vAlignTable['u54'] = 'top';
var u8 = document.getElementById('u8');

var u34 = document.getElementById('u34');
gv_vAlignTable['u34'] = 'center';
var u14 = document.getElementById('u14');
gv_vAlignTable['u14'] = 'center';
var u48 = document.getElementById('u48');
gv_vAlignTable['u48'] = 'top';
var u28 = document.getElementById('u28');
gv_vAlignTable['u28'] = 'center';
var u44 = document.getElementById('u44');
gv_vAlignTable['u44'] = 'center';
var u33 = document.getElementById('u33');

var u50 = document.getElementById('u50');
gv_vAlignTable['u50'] = 'center';
var u22 = document.getElementById('u22');
gv_vAlignTable['u22'] = 'center';
var u52 = document.getElementById('u52');

var u66 = document.getElementById('u66');
gv_vAlignTable['u66'] = 'top';
var u13 = document.getElementById('u13');

var u47 = document.getElementById('u47');
gv_vAlignTable['u47'] = 'center';
var u12 = document.getElementById('u12');
gv_vAlignTable['u12'] = 'center';
var u41 = document.getElementById('u41');

var u53 = document.getElementById('u53');
gv_vAlignTable['u53'] = 'center';
var u57 = document.getElementById('u57');
gv_vAlignTable['u57'] = 'top';
var u21 = document.getElementById('u21');

var u37 = document.getElementById('u37');

var u7 = document.getElementById('u7');
gv_vAlignTable['u7'] = 'center';
var u40 = document.getElementById('u40');
gv_vAlignTable['u40'] = 'center';
var u17 = document.getElementById('u17');
gv_vAlignTable['u17'] = 'center';
var u5 = document.getElementById('u5');
gv_vAlignTable['u5'] = 'center';
var u15 = document.getElementById('u15');
gv_vAlignTable['u15'] = 'top';
var u56 = document.getElementById('u56');
gv_vAlignTable['u56'] = 'center';
var u3 = document.getElementById('u3');
gv_vAlignTable['u3'] = 'center';
var u65 = document.getElementById('u65');
gv_vAlignTable['u65'] = 'top';
var u1 = document.getElementById('u1');
gv_vAlignTable['u1'] = 'center';
var u25 = document.getElementById('u25');
gv_vAlignTable['u25'] = 'top';
var u59 = document.getElementById('u59');
gv_vAlignTable['u59'] = 'center';
var u43 = document.getElementById('u43');

if (bIE) u43.attachEvent("onmouseover", MouseOveru43);
else u43.addEventListener("mouseover", MouseOveru43, true);
function MouseOveru43(e)
{
if (!IsTrueMouseOver('u43',e)) return;
if (true) {

}

}

if (bIE) u43.attachEvent("onmouseout", MouseOutu43);
else u43.addEventListener("mouseout", MouseOutu43, true);
function MouseOutu43(e)
{
if (!IsTrueMouseOut('u43',e)) return;
if (true) {

}

}

var u16 = document.getElementById('u16');

var u39 = document.getElementById('u39');

var u19 = document.getElementById('u19');
gv_vAlignTable['u19'] = 'center';
var u9 = document.getElementById('u9');
gv_vAlignTable['u9'] = 'center';
var u30 = document.getElementById('u30');
gv_vAlignTable['u30'] = 'center';
var u60 = document.getElementById('u60');
gv_vAlignTable['u60'] = 'top';
var u24 = document.getElementById('u24');
gv_vAlignTable['u24'] = 'center';
var u46 = document.getElementById('u46');

var u55 = document.getElementById('u55');

if (bIE) u55.attachEvent("onmouseover", MouseOveru55);
else u55.addEventListener("mouseover", MouseOveru55, true);
function MouseOveru55(e)
{
if (!IsTrueMouseOver('u55',e)) return;
if (true) {

}

}

if (bIE) u55.attachEvent("onmouseout", MouseOutu55);
else u55.addEventListener("mouseout", MouseOutu55, true);
function MouseOutu55(e)
{
if (!IsTrueMouseOut('u55',e)) return;
if (true) {

}

}

var u38 = document.getElementById('u38');
gv_vAlignTable['u38'] = 'center';
var u61 = document.getElementById('u61');

var u18 = document.getElementById('u18');

var u62 = document.getElementById('u62');
gv_vAlignTable['u62'] = 'center';
var u32 = document.getElementById('u32');
gv_vAlignTable['u32'] = 'center';
var u42 = document.getElementById('u42');
gv_vAlignTable['u42'] = 'center';
var u23 = document.getElementById('u23');

var u58 = document.getElementById('u58');

if (window.OnLoad) OnLoad();
