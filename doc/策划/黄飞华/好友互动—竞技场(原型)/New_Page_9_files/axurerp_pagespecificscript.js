
var PageName = 'New Page 9';
var PageId = 'p90a43b09050e4bf7b456ed477733f5b5'
var PageUrl = 'New_Page_9.html'
document.title = 'New Page 9';

if (top.location != self.location)
{
	if (parent.HandleMainFrameChanged) {
		parent.HandleMainFrameChanged();
	}
}

if (window.OnLoad) OnLoad();
