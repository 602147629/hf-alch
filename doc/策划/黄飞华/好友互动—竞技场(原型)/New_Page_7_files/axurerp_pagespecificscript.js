
var PageName = 'New Page 7';
var PageId = 'pfb533c6dbe894ab382610b1c9a30fb42'
var PageUrl = 'New_Page_7.html'
document.title = 'New Page 7';

if (top.location != self.location)
{
	if (parent.HandleMainFrameChanged) {
		parent.HandleMainFrameChanged();
	}
}

if (window.OnLoad) OnLoad();
