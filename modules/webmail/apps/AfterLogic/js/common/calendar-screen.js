/*
 * Classes:
 *  CCalendarScreen()
 */

function CCalendarScreen()
{
//public
	this.id = SCREEN_CALENDAR;
	this.isBuilded = false;
	this.bodyAutoOverflow = false;
//private
	this._needReload = false;
	this._shown = false;
	this._ifrCalendar = null;
}

CCalendarScreen.prototype = {
//public
	placeData: function() { },
	
	clickBody: function() { },

	resizeBody: function()
	{
		if (this._ifrCalendar == null) return;
		this._ifrCalendar.style.width = GetWidth() + 'px';
		this._ifrCalendar.style.height = GetHeight() + 'px';
	},
	
	show: function ()
	{
		if (this._ifrCalendar == null) {
			this._create();
		}
		else if (this._needReload) {
			this._reload();
		}
		this.resizeBody();
		if (this._shown) {
			this.display();
		}
		else {
			WebMail.showInfo(Lang.Loading);
		}
	},
	
	display: function ()
	{
		var oDoc = this._getDocument();
		
		this._ifrCalendar.className = '';
		this._shown = true;
		WebMail.hideInfo();
		
		if (typeof oDoc.RefreshData === 'function') {
			oDoc.RefreshData();
		}
	},
	
	restoreFromHistory: function () { },
	
	needReload: function ()
	{
		this._needReload = true;
	},

	hide: function()
	{
		if (this._ifrCalendar == null) return;
		this._ifrCalendar.className = (Browser.mozilla)
			? 'wm_unvisible'	// IE7 make iframe unvisible dirty
			: 'wm_hide';		// FireFox2 reload iframe if set "display: none;"
	},
	
	build: function()
	{
		this._needReload = false;
		this.isBuilded = true;
	},

//private
	_create: function ()
	{
		this._ifrCalendar = CreateChild(document.body, 'iframe', [['src', CalendarUrl],
			['frameborder', '0']]);

		this._ifrCalendar.style.padding = '0';
		this._ifrCalendar.style.margin = '0';
		this._ifrCalendar.style.border = 'none';
		this._ifrCalendar.style.position = 'absolute';
		this._ifrCalendar.style.left = '0';
		this._ifrCalendar.style.top = '0';
		this._ifrCalendar.style.zIndex = '10';

		var obj = this;
		this._ifrCalendar.onresize = function() {
			obj._ifrCalendar.style.width = (GetWidth() + 1) + 'px';
		};

		if (WebMail && WebMail.Settings) {
			var sett = WebMail.Settings;
			if (typeof(sett.idleSessionTimeout) != 'undefined' && sett.idleSessionTimeout > 0) {
				this._setCalendarFrameHandlers(function () {
					WebMail.startIdleTimer();
				}, Array('click', 'keyup'));
			}
		}

		this.hide();
	},

	_reload: function ()
	{
		/* FireFox2 and IE6,7 can reload iframe without parameter
		 * Opera9 can't reload iframe without parameter
		 */
		this._ifrCalendar.src = CalendarUrl + '?p=' + Math.random();
		this._needReload = false;
		this._shown = false;
	},

	_setCalendarFrameHandlers : function (eventFunction, eventsList)
	{
		var
			oDoc = this._getDocument(),
			iIndex = 0,
			iLen = eventsList.length
		;
		
		for (; iIndex < iLen; iIndex++) {
			$addHandler(oDoc, eventsList[iIndex],  eventFunction);
		}
	},
	
	_getDocument: function ()
	{
		return (Browser.ie) ? this._ifrCalendar.document : this._ifrCalendar.contentWindow;
	}
};

if (typeof window.JSFileLoaded != 'undefined') {
	JSFileLoaded();
}