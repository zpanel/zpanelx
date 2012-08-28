/*
 * Classes:
 *  CMessagePicturesController(bInNewWindow)
 *  CMessageReadConfirmationController(readConfirmationHandler, parent)
 *  CMessageSensivityController()
 *  CAppointmentConfirmationController()
 */

function CMessagePicturesController(bInNewWindow)
{
	this._bInNewWindow = bInNewWindow;
	this._fromAddr = '';
	this._offerAlwaysShowPictures = false;
	this._safety = SAFETY_NOTHING;
	this._offerShowPictures = false;
	
	this._container = null;
	this._showPicturesText = null;
}

CMessagePicturesController.prototype =
{
	SetSafety: function (safety)
	{
		this._safety = safety;
		this._offerShowPictures = (this._safety == SAFETY_NOTHING);
		this._setClassName();
	},
	
	SetFromAddr: function (fromAddr)
	{
		this._fromAddr = fromAddr;
		this._offerAlwaysShowPictures = (this._fromAddr.length > 0 && window.UseDb !== false);
		this._setClassName();
	},
	
	_setClassName: function ()
	{
		this._showPicturesText.className = (this._offerShowPictures) ? '' : 'wm_hide';
		this._showAlwaysPicturesText.className = (this._offerAlwaysShowPictures) ? '' : 'wm_hide';
	},
	
	show: function ()
	{
		this._setClassName();
		this._container.className = (this._offerShowPictures || this._offerAlwaysShowPictures) ? 'wm_safety_info' : 'wm_hide';
	},
	
	hide: function ()
	{
		this._container.className = 'wm_hide';
	},
	
	getHeight: function ()
	{
		return this._container.offsetHeight;
	},
	
	resizeWidth: function (width)
	{
		var paddings = 16;
		var borders = 2;
		this._container.style.width = (width - paddings - borders) + 'px';
	},
	
	showPictures: function ()
	{
		this.SetSafety(SAFETY_MESSAGE);
		this.show();
		ShowPicturesHandler(SAFETY_MESSAGE);

		if (this._bInNewWindow) {
			window.opener.SetMessageSafetyHandler(ViewMessage);
			window.opener.ShowPicturesHandler(SAFETY_MESSAGE);
		}
		else {
			SetMessageSafetyHandler();
		}
	},
	
	showPicturesFromSender: function ()
	{
		this.hide();
		ShowPicturesHandler(SAFETY_FULL);
		
		if (this._bInNewWindow) {
			window.opener.SetSenderSafetyHandler(this._fromAddr);
			window.opener.ShowPicturesHandler(SAFETY_FULL);
		}
		else {
			SetSenderSafetyHandler(this._fromAddr);
		}
	},
	
	build: function (parent)
	{
		this._container = CreateChild(parent, 'div', [['style', 'font:12px Tahoma,Arial,Helvetica,sans-serif;']]);
		
		var span = CreateChild(this._container, 'span');
		this._showPicturesText = span;
		var text = CreateChild(span, 'span');
		text.innerHTML = Lang.PicturesBlocked + '&nbsp;';
		WebMail.langChanger.register('innerHTML', text, 'PicturesBlocked', '&nbsp;');
		var a = CreateChild(span, 'a');
		a.innerHTML = Lang.ShowPictures;
		a.href = '#';
		var obj = this;
		a.onclick = function () {
			obj.showPictures();
			return false;
		};
		WebMail.langChanger.register('innerHTML', a, 'ShowPictures', '');
		text = CreateChild(span, 'span');
		text.innerHTML = '.&nbsp;';
		
		span = CreateChild(this._container, 'span');
		a = CreateChild(span, 'a');
		a.innerHTML = Lang.ShowPicturesFromSender;
		a.href = '#';
		a.onclick = function () {
			obj.showPicturesFromSender();
			return false;
		};
		WebMail.langChanger.register('innerHTML', a, 'ShowPicturesFromSender', '');
		text = CreateTextChild(span, '.');
		this._showAlwaysPicturesText = span;
		this.hide();
	}
};

function CMessageReadConfirmationController(readConfirmationHandler, parent)
{
	this._readConfirmationHandler = readConfirmationHandler;
	this._parent = parent;

	this._container = null;
}

CMessageReadConfirmationController.prototype =
{
	show: function ()
	{
		this._container.className = 'wm_safety_info';
	},

	hide: function ()
	{
		this._container.className = 'wm_hide';
	},

	SendConfirmationMail: function ()
	{
		this.hide();
		this._readConfirmationHandler.call(this._parent);
	},

	getHeight: function ()
	{
		return this._container.offsetHeight;
	},

	resizeWidth: function (width)
	{
		var paddings = 16;
		var borders = 2;
		this._container.style.width = (width - paddings - borders) + 'px';
	},
	
	build: function (container)
	{
		var obj = this;
		this._container = CreateChild(container, 'div', [['class', 'wm_hide']]);
		var span = CreateChild(this._container, 'span');
		var text = CreateChild(span, 'span');
		text.innerHTML = Lang.ReturnReceiptTopText + '&nbsp;';
		WebMail.langChanger.register('innerHTML', text, 'ReturnReceiptTopText', '&nbsp;');
		var a = CreateChild(span, 'a');
		a.innerHTML = Lang.ReturnReceiptTopLink;
		a.href = '#';
		a.onclick = function () {
			obj.SendConfirmationMail();
			return false;
		};
		WebMail.langChanger.register('innerHTML', a, 'ReturnReceiptTopLink', '');
	}
};

function CMessageSensivityController()
{
	this._message = null;
}

CMessageSensivityController.prototype =
{
	show: function (sensivity)
	{
		this._message.className = 'wm_safety_info';
		switch (sensivity) {
			case SENSIVITY_CONFIDENTIAL:
				this._message.innerHTML = Lang.SensivityConfidential;
				break;
			case SENSIVITY_PRIVATE:
				this._message.innerHTML = Lang.SensivityPrivate;
				break;
			case SENSIVITY_PERSONAL:
				this._message.innerHTML = Lang.SensivityPersonal;
				break;
			default:
				this.hide();
				break;
		}
	},

	hide: function ()
	{
		this._message.className = 'wm_hide';
	},
	
	getHeight: function ()
	{
		return this._message.offsetHeight;
	},
	
	resizeWidth: function (width)
	{
		var paddings = 16;
		var borders = 2;
		this._message.style.width = (width - paddings - borders) + 'px';
	},
	
	build: function (container)
	{
		this._message = CreateChild(container, 'div');
		this.hide();
	}
};

function CAppointmentConfirmationController()
{
	this.$container = null;
	this.$location = null;
	this.$calendars = null;
	this.$calendar = null;
	this.$when = null;
	this.$description = null;
	this.$accept = null;
	this.$decline = null;
	this.$tentative = null;
	this.oAppointment = null;
	this.oMsg = null;
}

CAppointmentConfirmationController.prototype =
{
	show: function (oMsg)
	{
		this.oMsg = oMsg;
		this.$container.show();
		this.fill(oMsg.oAppointment);
	},

	hide: function ()
	{
		this.$container.hide();
		this.oAppointment = null;
		this.oMsg = null;
	},

	getHeight: function ()
	{
		return this.$container.is(':visible') ? this.$container.height() : 0;
	},

	resizeWidth: function (iWidth)
	{
		var
			iPaddings = 16,
			iBorders = 2
		;
		this.$container.css('width', (iWidth - iPaddings - iBorders));
	},
	
	fill: function (oAppointment)
	{
		this.oAppointment = oAppointment;
		switch (oAppointment.sType) {
			case EnumAppointmentType.Request:
				this.fillRequestAppointment(oAppointment);
				this.fillCalendars(oAppointment.aCalendars, oAppointment.sCalId);
				break;
			default:
				this.$accept.hide();
				this.$decline.hide();
				this.$tentative.hide();
				break;
		}
	},
	
	fillCalendars: function (aCalendars, sCalId)
	{
		var
			iLen = aCalendars.length,
			iIndex = 0,
			oCalendar = null,
			bSelected = false,
			$option = null
		;
		this.$calendars.html('');
		for (; iIndex < iLen; iIndex++) {
			oCalendar = aCalendars[iIndex];
			$option = $('<option></option>')
				.text(oCalendar.sName)
				.attr('value', oCalendar.sId)
				.appendTo(this.$calendars);
			if (sCalId === oCalendar.sId) {
				$option.attr('selected', 'selected');
				bSelected = true;
				this.$calendar.text(oCalendar.sName);
			}
		}
		
		if (bSelected) {
			this.$calendars.hide();
			this.$calendar.show();
		}
		else {
			this.$calendars.show();
			this.$calendar.hide();
		}
	},
	
	fillRequestAppointment: function (oAppointment)
	{
		var self = this;
		
		this.$location.text(oAppointment.sLocation);
		this.$when.text(oAppointment.sWhen);
		this.$description.text(oAppointment.sDescription);
	
		this.$accept.show();
		this.$decline.show();
		this.$tentative.show();
		this.$accept.unbind('click');
		this.$decline.unbind('click');
		this.$tentative.unbind('click');
		switch (oAppointment.sConfig) {
			case EnumAppointmentConfig.Accepted:
				this.$accept.addClass('disable');
				this.$decline.bind('click', function () {self.sendAppointmentConfirmation(false, EnumAppointmentConfig.Declined);});
				this.$decline.removeClass('disable');
				this.$tentative.bind('click', function () {self.sendAppointmentConfirmation(false, EnumAppointmentConfig.Tentative);});
				this.$tentative.removeClass('disable');
				break;
			case EnumAppointmentConfig.Declined:
				this.$accept.bind('click', function () {self.sendAppointmentConfirmation(true, EnumAppointmentConfig.Accepted);});
				this.$accept.removeClass('disable');
				this.$decline.addClass('disable');
				this.$tentative.bind('click', function () {self.sendAppointmentConfirmation(false, EnumAppointmentConfig.Tentative);});
				this.$tentative.removeClass('disable');
				break;
			case EnumAppointmentConfig.Tentative:
				this.$accept.bind('click', function () {self.sendAppointmentConfirmation(true, EnumAppointmentConfig.Accepted);});
				this.$accept.removeClass('disable');
				this.$decline.bind('click', function () {self.sendAppointmentConfirmation(false, EnumAppointmentConfig.Declined);});
				this.$decline.removeClass('disable');
				this.$tentative.addClass('disable');
				break;
			case EnumAppointmentConfig.NeedAction:
				this.$accept.bind('click', function () {self.sendAppointmentConfirmation(true, EnumAppointmentConfig.Accepted);});
				this.$accept.removeClass('disable');
				this.$decline.bind('click', function () {self.sendAppointmentConfirmation(false, EnumAppointmentConfig.Declined);});
				this.$decline.removeClass('disable');
				this.$tentative.bind('click', function () {self.sendAppointmentConfirmation(false, EnumAppointmentConfig.Tentative);});
				this.$tentative.removeClass('disable');
				break;
			default:
				this.$accept.hide();
				this.$decline.hide();
				this.$tentative.hide();
				break;
		}
	},
	
	sendAppointmentConfirmation: function (bAccepted, sAction)
	{
		if (sAction === undefined) {
			sAction = this.oAppointment.sConfig;
		}
		
		this.oAppointment.sCalId = this.$calendars.val();
		
		SendAppointmentConfirmationHandler(this.oAppointment, bAccepted, sAction);
		this.oAppointment.sConfig = sAction;
		this.fill(this.oAppointment);
		WebMail.DataSource.cache.setMessageAppointment(this.oMsg.id, this.oMsg.uid, this.oMsg.idFolder, 
			this.oMsg.folderFullName, this.oAppointment)
	},
	
	build: function (eParent)
	{
		var
			$innerCont = null,
			$actionsCont = null
		;
		
		this.$container = $('<div class="wm_appointment_info"></div>').hide().appendTo(eParent);
		
		$innerCont = $('<div></div>').appendTo(this.$container);
		$('<span></span>').addClass('wm_appointment_title').text('Location:').appendTo($innerCont);
		this.$location = $('<span></span>').appendTo($innerCont);
		this.$calendars = $('<select></select>').css('float', 'right').appendTo($innerCont);
		this.$calendar = $('<span></span>').css('float', 'right').appendTo($innerCont);
		$('<span></span>').addClass('wm_appointment_title').css('float', 'right').text('Calendar:').appendTo($innerCont);
		
		$innerCont = $('<div></div>').appendTo(this.$container);
		$('<span></span>').addClass('wm_appointment_title').text('When:').appendTo($innerCont);
		this.$when = $('<span></span>').appendTo($innerCont);
		
		$innerCont = $('<div></div>').appendTo(this.$container);
		$('<span></span>').addClass('wm_appointment_title').text('Description:').appendTo($innerCont);
		this.$description = $('<span></span>').appendTo($innerCont);
		
		$actionsCont = $('<div class="wm_appointment_decision"></div>').appendTo(this.$container);
		this.$accept = $('<a href="#"></a>').css('margin', '5px').html('Accept').appendTo($actionsCont);
		this.$tentative = $('<a href="#"></a>').css('margin', '5px').html('Tentative').appendTo($actionsCont);
		this.$decline = $('<a href="#"></a>').css('margin', '5px').html('Decline').appendTo($actionsCont);
	}
};

if (typeof window.JSFileLoaded != 'undefined') {
	JSFileLoaded();
}