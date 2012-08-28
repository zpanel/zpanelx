/*
 * Classes:
 *  CContact()
 *  CContacts()
 *  CGroups()
 *  CGroup()
 */

function CContact()
{
	this.type = TYPE_CONTACT;
	this.bReadonly = false;
	
	this.sContactId = '';
	this.primaryEmail = PRIMARY_DEFAULT_EMAIL;
	this.useFriendlyName = false;
	this.email = '';

	this.name = ''; //fullName
	this.title = '';
	this.firstName = '';
	this.surName = '';
	this.nickName = '';

	this.day = 0;
	this.month = 0;
	this.year = 0;
	
	this.hEmail = '';
	this.hStreet = '';
	this.hCity = '';
	this.hState = '';
	this.hZip = '';
	this.hCountry = '';
	this.hFax = '';
	this.hPhone = '';
	this.hMobile = '';
	this.hWeb = '';

	this.bEmail = '';
	this.bCompany = '';
	this.bJobTitle = '';
	this.bDepartment = '';
	this.bOffice = '';
	this.bStreet = '';
	this.bCity = '';
	this.bState = '';
	this.bZip = '';
	this.bCountry = '';
	this.bFax = '';
	this.bPhone = '';
	this.bMobile = '';
	this.bWeb = '';
	
	this.otherEmail = '';
	this.notes = '';
	
	this.groups = [];
	this.onlyMainData = true;
	this.hasHomeData = false;
	this.hasBusinessData = false;
	this.hasOtherData = false;
}

CContact.prototype = {
	getStringDataKeys: function()
	{
		return this.sContactId;
	},

	getIdForList: function ()
	{
		var arDataKeys = [ this.sContactId, 0, this.name, this.email ];
		return arDataKeys.join(STR_SEPARATOR);
	},
	
	getInXml: function (params)
	{
		var attrs = '';
		if (this.sContactId.length > 0) attrs += ' id="' + this.sContactId + '"';
		attrs += ' primary_email="' + this.primaryEmail + '"';
		attrs += (this.useFriendlyName) ? ' use_friendly_nm="1"' : ' use_friendly_nm="0"';

		var nodes = '<fullname>' + GetCData(this.name) + '</fullname>';
		nodes += '<title>' + GetCData(this.title) + '</title>';
		nodes += '<firstname>' + GetCData(this.firstName) + '</firstname>';
		nodes += '<surname>' + GetCData(this.surName) + '</surname>';
		nodes += '<nickname>' + GetCData(this.nickName) + '</nickname>';
		nodes += '<birthday day="' + this.day + '" month="' + this.month + '" year="' + this.year + '"/>';

		var personal = '<email>' + GetCData(this.hEmail) + '</email>';
		personal += '<street>' + GetCData(this.hStreet) + '</street>';
		personal += '<city>' + GetCData(this.hCity) + '</city>';
		personal += '<state>' + GetCData(this.hState) + '</state>';
		personal += '<zip>' + GetCData(this.hZip) + '</zip>';
		personal += '<country>' + GetCData(this.hCountry) + '</country>';
		personal += '<fax>' + GetCData(this.hFax) + '</fax>';
		personal += '<phone>' + GetCData(this.hPhone) + '</phone>';
		personal += '<mobile>' + GetCData(this.hMobile) + '</mobile>';
		personal += '<web>' + GetCData(this.hWeb) + '</web>';
		nodes += '<personal>' + personal + '</personal>';

		var business = '<email>' + GetCData(this.bEmail) + '</email>';
		business += '<company>' + GetCData(this.bCompany) + '</company>';
		business += '<job_title>' + GetCData(this.bJobTitle) + '</job_title>';
		business += '<department>' + GetCData(this.bDepartment) + '</department>';
		business += '<office>' + GetCData(this.bOffice) + '</office>';
		business += '<street>' + GetCData(this.bStreet) + '</street>';
		business += '<city>' + GetCData(this.bCity) + '</city>';
		business += '<state>' + GetCData(this.bState) + '</state>';
		business += '<zip>' + GetCData(this.bZip) + '</zip>';
		business += '<country>' + GetCData(this.bCountry) + '</country>';
		business += '<fax>' + GetCData(this.bFax) + '</fax>';
		business += '<phone>' + GetCData(this.bPhone) + '</phone>';
		business += '<modile>' + GetCData(this.bMobile) + '</modile>';
		business += '<web>' + GetCData(this.bWeb) + '</web>';
		nodes += '<business>' + business + '</business>';

		var other = '<email>' + GetCData(this.otherEmail) + '</email>';
		other += '<notes>' + GetCData(this.notes) + '</notes>';
		nodes += '<other>' + other + '</other>';

		var groups = '';
		var groupsCount = this.groups.length;
		for (var groupIndex = 0; groupIndex < groupsCount; groupIndex++) {
			groups += '<group id="' + this.groups[groupIndex].sGroupId + '"/>';
		}

		nodes += '<groups>' + groups + '</groups>';
		return params + '<contact' + attrs + '>' + nodes + '</contact>';
	},
	
	getFromXml: function(rootElement)
	{
		this.bReadonly = XmlHelper.getBoolAttributeByName(rootElement, 'read_only', this.bReadonly);
		this.sContactId = XmlHelper.getAttributeByName(rootElement, 'id', this.sContactId);
		this.primaryEmail = XmlHelper.getIntAttributeByName(rootElement, 'primaryEmail', this.primaryEmail);
		this.useFriendlyName = XmlHelper.getBoolAttributeByName(rootElement, 'use_friendly_name', this.useFriendlyName);

		var nameNode = XmlHelper.getFirstChildNodeByName(rootElement, 'fullname');
		this.name = XmlHelper.getFirstChildValue(nameNode, this.name);

		var titleNode = XmlHelper.getFirstChildNodeByName(rootElement, 'title');
		this.title = XmlHelper.getFirstChildValue(titleNode, this.title);

		var firstNameNode = XmlHelper.getFirstChildNodeByName(rootElement, 'firstname');
		this.firstName = XmlHelper.getFirstChildValue(firstNameNode, this.firstName);

		var surNameNode = XmlHelper.getFirstChildNodeByName(rootElement, 'surname');
		this.surName = XmlHelper.getFirstChildValue(surNameNode, this.surName);

		var nickNameNode = XmlHelper.getFirstChildNodeByName(rootElement, 'nickname');
		this.nickName = XmlHelper.getFirstChildValue(nickNameNode, this.nickName);

		var birthdayNode = XmlHelper.getFirstChildNodeByName(rootElement, 'birthday');
		this.day = XmlHelper.getIntAttributeByName(birthdayNode, 'day', this.day);
		this.month = XmlHelper.getIntAttributeByName(birthdayNode, 'month', this.month);
		this.year = XmlHelper.getIntAttributeByName(birthdayNode, 'year', this.year);
		if (this.day > 0 || this.month > 0 || this.year > 0) {
			this.hasOtherData = true;
			this.onlyMainData = false;
		}

		var personalNode = XmlHelper.getFirstChildNodeByName(rootElement, 'personal');
		this._readPersonal(personalNode);

		var businessNode = XmlHelper.getFirstChildNodeByName(rootElement, 'business');
		this._readBusiness(businessNode);

		var otherNode = XmlHelper.getFirstChildNodeByName(rootElement, 'other');
		this._readOther(otherNode);

		var groupsNode = XmlHelper.getFirstChildNodeByName(rootElement, 'groups');
		this._readGroups(groupsNode);

		switch (this.primaryEmail) {
			case PRIMARY_BUS_EMAIL:
				this.email = this.bEmail;
				break;
			case PRIMARY_OTHER_EMAIL:
				this.email = this.otherEmail;
				break;
			default:
				this.email = this.hEmail;
				break;
		}
	},

	_readPersonal: function (personalNode)
	{
		if (personalNode === null) return;
		this.hasHomeData = true;

		var emailNode = XmlHelper.getFirstChildNodeByName(personalNode, 'email');
		this.hEmail = XmlHelper.getFirstChildValue(emailNode, this.hEmail);
		if (this.hEmail != '' && this.primaryEmail != PRIMARY_HOME_EMAIL) this.onlyMainData = false;

		var streetNode = XmlHelper.getFirstChildNodeByName(personalNode, 'street');
		this.hStreet = XmlHelper.getFirstChildValue(streetNode, this.hStreet);

		var cityNode = XmlHelper.getFirstChildNodeByName(personalNode, 'city');
		this.hCity = XmlHelper.getFirstChildValue(cityNode, this.hCity);

		var stateNode = XmlHelper.getFirstChildNodeByName(personalNode, 'state');
		this.hState = XmlHelper.getFirstChildValue(stateNode, this.hState);

		var zipNode = XmlHelper.getFirstChildNodeByName(personalNode, 'zip');
		this.hZip = XmlHelper.getFirstChildValue(zipNode, this.hZip);

		var countryNode = XmlHelper.getFirstChildNodeByName(personalNode, 'country');
		this.hCountry = XmlHelper.getFirstChildValue(countryNode, this.hCountry);

		var faxNode = XmlHelper.getFirstChildNodeByName(personalNode, 'fax');
		this.hFax = XmlHelper.getFirstChildValue(faxNode, this.hFax);

		var phoneNode = XmlHelper.getFirstChildNodeByName(personalNode, 'phone');
		this.hPhone = XmlHelper.getFirstChildValue(phoneNode, this.hPhone);

		var mobileNode = XmlHelper.getFirstChildNodeByName(personalNode, 'mobile');
		this.hMobile = XmlHelper.getFirstChildValue(mobileNode, this.hMobile);

		var webNode = XmlHelper.getFirstChildNodeByName(personalNode, 'web');
		this.hWeb = XmlHelper.getFirstChildValue(webNode, this.hWeb);

		var hasHomeData = (this.hStreet != '' || this.hCity != '' || this.hState != ''
			|| this.hZip != '' || this.hCountry != '' || this.hFax != '' || this.hPhone != ''
			|| this.hMobile != '' || this.hWeb != '');
		if (hasHomeData) this.onlyMainData = false;
	},

	_readBusiness: function (businessNode)
	{
		if (businessNode === null) return;
		this.hasBusinessData = true;

		var emailNode = XmlHelper.getFirstChildNodeByName(businessNode, 'email');
		this.bEmail = XmlHelper.getFirstChildValue(emailNode, this.bEmail);
		if (this.bEmail != '' && this.primaryEmail != PRIMARY_BUS_EMAIL) this.onlyMainData = false;

		var companyNode = XmlHelper.getFirstChildNodeByName(businessNode, 'company');
		this.bCompany = XmlHelper.getFirstChildValue(companyNode, this.bCompany);

		var jobTitleNode = XmlHelper.getFirstChildNodeByName(businessNode, 'job_title');
		this.bJobTitle = XmlHelper.getFirstChildValue(jobTitleNode, this.bJobTitle);

		var departmentNode = XmlHelper.getFirstChildNodeByName(businessNode, 'department');
		this.bDepartment = XmlHelper.getFirstChildValue(departmentNode, this.bDepartment);

		var officeNode = XmlHelper.getFirstChildNodeByName(businessNode, 'office');
		this.bOffice = XmlHelper.getFirstChildValue(officeNode, this.bOffice);

		var streetNode = XmlHelper.getFirstChildNodeByName(businessNode, 'street');
		this.bStreet = XmlHelper.getFirstChildValue(streetNode, this.bStreet);

		var cityNode = XmlHelper.getFirstChildNodeByName(businessNode, 'city');
		this.bCity = XmlHelper.getFirstChildValue(cityNode, this.bCity);

		var stateNode = XmlHelper.getFirstChildNodeByName(businessNode, 'state');
		this.bState = XmlHelper.getFirstChildValue(stateNode, this.bState);

		var zipNode = XmlHelper.getFirstChildNodeByName(businessNode, 'zip');
		this.bZip = XmlHelper.getFirstChildValue(zipNode, this.bZip);

		var countryNode = XmlHelper.getFirstChildNodeByName(businessNode, 'country');
		this.bCountry = XmlHelper.getFirstChildValue(countryNode, this.bCountry);

		var faxNode = XmlHelper.getFirstChildNodeByName(businessNode, 'fax');
		this.bFax = XmlHelper.getFirstChildValue(faxNode, this.bFax);

		var phoneNode = XmlHelper.getFirstChildNodeByName(businessNode, 'phone');
		this.bPhone = XmlHelper.getFirstChildValue(phoneNode, this.bPhone);

		var mobileNode = XmlHelper.getFirstChildNodeByName(businessNode, 'mobile');
		this.bMobile = XmlHelper.getFirstChildValue(mobileNode, this.bMobile);

		var webNode = XmlHelper.getFirstChildNodeByName(businessNode, 'web');
		this.bWeb = XmlHelper.getFirstChildValue(webNode, this.bWeb);

		var hasHomeData = (this.bCompany != '' || this.bJobTitle != '' || this.bDepartment != ''
			|| this.bOffice != '' || this.bStreet != '' || this.bCity != '' || this.bState != ''
			|| this.bZip != '' || this.bCountry != '' || this.bFax != '' || this.bPhone != ''
			|| this.bMobile != '' || this.bWeb != '');
		if (hasHomeData) this.onlyMainData = false;
	},

	_readOther: function (otherNode)
	{
		var emailNode = XmlHelper.getFirstChildNodeByName(otherNode, 'email');
		this.otherEmail = XmlHelper.getFirstChildValue(emailNode, this.otherEmail);
		if (this.otherEmail != '' && this.primaryEmail != PRIMARY_OTHER_EMAIL) this.onlyMainData = false;

		var notesNode = XmlHelper.getFirstChildNodeByName(otherNode, 'notes');
		this.notes = XmlHelper.getFirstChildValue(notesNode, this.notes);
		if (this.notes != '') this.onlyMainData = false;
	},

	_readGroups: function (groupsNode)
	{
		this.groups = [];
		if (groupsNode === null) return;
		var groupsChilds = groupsNode.childNodes;
		for (var i = 0; i < groupsChilds.length; i++) {
			var groupNode = groupsChilds[i];
			if ('group' === groupNode.tagName) {
				var sGroupId = XmlHelper.getAttributeByName(groupNode, 'id', '');
				var nameNode = XmlHelper.getFirstChildNodeByName(groupNode, 'name');
				var name = XmlHelper.getFirstChildValue(nameNode, '');
				this.groups.push({ sGroupId: sGroupId, name: name });
			}
		}
	}
};

function CContacts(sGroupId, lookFor, lookFirstChar, searchType)
{
	this.type = TYPE_CONTACTS;
	this.groupsCount = 0;
	this.contactsCount = 0;
	this.count = 0;
	this.sortField = null;
	this.sortOrder = null;
	this.page = null;
	this.sGroupId = (sGroupId == undefined) ? '' : sGroupId;
	this.lookFor = (lookFor == undefined) ? '' : lookFor;
	this.lookFirstChar = (lookFirstChar == undefined) ? '' : lookFirstChar;
	this.searchType = (searchType == undefined) ? CONTACTS_SEARCH_TYPE_STANDARD : searchType;
	this.sAddedContactId = '';
	this.list = [];
}

CContacts.prototype = {
	getStringDataKeys: function()
	{
		var arDataKeys = [ this.page, this.sortField, this.sortOrder, this.sGroupId, this.lookFor,
			this.lookFirstChar, this.searchType];
		return arDataKeys.join(STR_SEPARATOR);
	},
	
	getInXml: function ()
	{
		var xml = '<param name="id_group" value="' + this.sGroupId + '"/>';
		xml += '<param name="look_first_character" value="' + this.lookFirstChar + '"/>';
		xml += '<look_for type="' + this.searchType + '">' + GetCData(this.lookFor) + '</look_for>';
		return xml;
	},

	getFromXml: function(rootElement)
	{
		this.groupsCount = XmlHelper.getIntAttributeByName(rootElement, 'groups_count', this.groupsCount);
		this.contactsCount = XmlHelper.getIntAttributeByName(rootElement, 'contacts_count', this.contactsCount);
		this.count = this.groupsCount + this.contactsCount;
		this.page = XmlHelper.getIntAttributeByName(rootElement, 'page', this.page);
		this.sortField = XmlHelper.getIntAttributeByName(rootElement, 'sort_field', this.sortField);
		this.sortOrder = XmlHelper.getIntAttributeByName(rootElement, 'sort_order', this.sortOrder);
		this.sGroupId = XmlHelper.getAttributeByName(rootElement, 'id_group', this.sGroupId);
		if (this.sGroupId === '0' || this.sGroupId === '-1') {
			this.sGroupId = '';
		}
		this.lookFirstChar = XmlHelper.getAttributeByName(rootElement, 'look_first_character', this.lookFirstChar);
		this.sAddedContactId = XmlHelper.getAttributeByName(rootElement, 'added_contact_id', this.sAddedContactId);

		var lookForNode = XmlHelper.getFirstChildNodeByName(rootElement, 'look_for');
		this.searchType = XmlHelper.getIntAttributeByName(lookForNode, 'type', this.searchType);
		this.lookFor = XmlHelper.getFirstChildValue(lookForNode, this.lookFor);

		var contactsChilds = rootElement.childNodes;
		for (var i = 0; i < contactsChilds.length; i++) {
			var contactNode = contactsChilds[i];
			if (contactNode.tagName == 'contact_group') {
				this._readContactOrGroup(contactNode);
			}
		}
	},

	_readContactOrGroup: function (contactNode)
	{
		var sContactId = XmlHelper.getAttributeByName(contactNode, 'id', '');
		var isGroup = XmlHelper.getIntAttributeByName(contactNode, 'is_group', 0);

		var nameNode = XmlHelper.getFirstChildNodeByName(contactNode, 'name');
		var clearName = XmlHelper.getFirstChildValue(nameNode, '');

		var emailNode = XmlHelper.getFirstChildNodeByName(contactNode, 'email');
		var clearEmail = XmlHelper.getFirstChildValue(emailNode, '');

		var encodedLookFor = HtmlEncode(this.lookFor);
		switch (this.searchType) {
			case CONTACTS_SEARCH_TYPE_FREQUENCY:
				var displayText = '';
				var replaceText = '';
				if (isGroup) {
					displayText = (encodedLookFor.length > 0)
						? clearName.replaceStr(encodedLookFor, HighlightContactLine) : clearName;

					replaceText = HtmlDecode(clearEmail);
				}
				else if (clearName.length > 0) {
					displayText = (encodedLookFor.length > 0)
						? '"' + clearName.replaceStr(encodedLookFor, HighlightContactLine) + '" &lt;' + clearEmail.replaceStr(encodedLookFor, HighlightContactLine) + '&gt;'
						: '"' + clearName + '" &lt;' + clearEmail + '&gt;';

					replaceText = HtmlDecode('"' + clearName + '" <' + clearEmail + '>');
				}
				else {
					displayText = (encodedLookFor.length > 0)
						? clearEmail.replaceStr(encodedLookFor, HighlightContactLine) : clearEmail;

					replaceText = HtmlDecode(clearEmail);
				}
				this.list.push({ sContactId: sContactId, isGroup: isGroup, displayText: displayText,
					replaceText: replaceText, clearEmail: clearEmail, clearName: clearName
				});
				break;
			case CONTACTS_SEARCH_TYPE_STANDARD:
				var name = clearName;
				var email = clearEmail;
				if (encodedLookFor.length > 0) {
					name = clearName.replaceStr(encodedLookFor, HighlightMessageLine);
					email = clearEmail.replaceStr(encodedLookFor, HighlightMessageLine);
				}
			default:
				if (isGroup && email.length > 0) {
					email = '<span class="wm_secondary_info">' + Lang.GroupMembers + ': </span>' + email;
				}
				this.list.push({ sContactId: sContactId, isGroup: isGroup, name: name, email: email,
					clearEmail: clearEmail, clearName: clearName
				});
				break;
		}
	}
};

function CGroups()
{
	this.type = TYPE_GROUPS;
	this.items = [];
}

CGroups.prototype = {
	getStringDataKeys: function()
	{
		return '';
	},

	getFromXml: function(rootElement)
	{
		var groupParts = rootElement.childNodes;
		for (var i = 0; i < groupParts.length; i++) {
			var groupNode = groupParts[i];
			if (groupNode.tagName == 'group') {
				var sGroupId = XmlHelper.getAttributeByName(groupNode, 'id', '');
				var nameNode = XmlHelper.getFirstChildNodeByName(groupNode, 'name');
				var name = XmlHelper.getFirstChildValue(nameNode, '');
				this.items.push({ sGroupId: sGroupId, name: name });
			}
		}
	}
};

function CGroup()
{
	this.type = TYPE_GROUP;
	this.sGroupId = '';
	this.name = '';
	this.contacts = [];
	this.isOrganization = false;
	this.email = '';
	this.company = '';
	this.street = '';
	this.city = '';
	this.state = '';
	this.zip = '';
	this.country = '';
	this.fax = '';
	this.phone = '';
	this.web = '';
}

CGroup.prototype = {
	getStringDataKeys: function()
	{
		return this.sGroupId;
	},

	getInXml: function (params)
	{
		var attrs = (this.sGroupId.length > 0) ? ' id="' + this.sGroupId + '"' : '';
		attrs += (this.isOrganization) ? ' organization="1"' : ' organization="0"';

		var contacts = '';
		var iCount = this.contacts.length;
		for (var i = 0; i < iCount; i++) {
			contacts += '<contact id="' + this.contacts[i].sContactId + '"/>';
		}
		var xml = params + '<group' + attrs + '>';
		xml += '<name>' + GetCData(this.name) + '</name>';
		xml += '<email>' + GetCData(this.email) + '</email>';
		xml += '<company>' + GetCData(this.company) + '</company>';
		xml += '<street>' + GetCData(this.street) + '</street>';
		xml += '<city>' + GetCData(this.city) + '</city>';
		xml += '<state>' + GetCData(this.state) + '</state>';
		xml += '<zip>' + GetCData(this.zip) + '</zip>';
		xml += '<country>' + GetCData(this.country) + '</country>';
		xml += '<fax>' + GetCData(this.fax) + '</fax>';
		xml += '<phone>' + GetCData(this.phone) + '</phone>';
		xml += '<web>' + GetCData(this.web) + '</web>';
		xml += '<contacts>' + contacts + '</contacts>';
		xml += '</group>';
		return xml;
	},
	
	getFromXml: function(rootElement)
	{
		this.sGroupId = XmlHelper.getAttributeByName(rootElement, 'id', this.sGroupId);
		this.isOrganization = XmlHelper.getBoolAttributeByName(rootElement, 'organization', this.isOrganization);

		var nameNode = XmlHelper.getFirstChildNodeByName(rootElement, 'name');
		this.name = XmlHelper.getFirstChildValue(nameNode, this.name);

		var emailNode = XmlHelper.getFirstChildNodeByName(rootElement, 'email');
		this.email = XmlHelper.getFirstChildValue(emailNode, this.email);

		var companyNode = XmlHelper.getFirstChildNodeByName(rootElement, 'company');
		this.company = XmlHelper.getFirstChildValue(companyNode, this.company);

		var streetNode = XmlHelper.getFirstChildNodeByName(rootElement, 'street');
		this.street = XmlHelper.getFirstChildValue(streetNode, this.street);

		var cityNode = XmlHelper.getFirstChildNodeByName(rootElement, 'city');
		this.city = XmlHelper.getFirstChildValue(cityNode, this.city);

		var stateNode = XmlHelper.getFirstChildNodeByName(rootElement, 'state');
		this.state = XmlHelper.getFirstChildValue(stateNode, this.state);

		var zipNode = XmlHelper.getFirstChildNodeByName(rootElement, 'zip');
		this.zip = XmlHelper.getFirstChildValue(zipNode, this.zip);

		var countryNode = XmlHelper.getFirstChildNodeByName(rootElement, 'country');
		this.country = XmlHelper.getFirstChildValue(countryNode, this.country);

		var faxNode = XmlHelper.getFirstChildNodeByName(rootElement, 'fax');
		this.fax = XmlHelper.getFirstChildValue(faxNode, this.fax);

		var phoneNode = XmlHelper.getFirstChildNodeByName(rootElement, 'phone');
		this.phone = XmlHelper.getFirstChildValue(phoneNode, this.phone);

		var webNode = XmlHelper.getFirstChildNodeByName(rootElement, 'web');
		this.web = XmlHelper.getFirstChildValue(webNode, this.web);

		var contactsNode = XmlHelper.getFirstChildNodeByName(rootElement, 'contacts');
		this._readContacts(contactsNode);
	},

	_readContacts: function (contactsNode)
	{
		var contactsChilds = contactsNode.childNodes;
		for (var i = 0; i < contactsChilds.length; i++) {
			var contactNode = contactsChilds[i];
			if (contactNode.tagName === 'contact') {
				var sContactId = XmlHelper.getAttributeByName(contactNode, 'id', '');
				var nameNode = XmlHelper.getFirstChildNodeByName(contactNode, 'fullname');
				var name = XmlHelper.getFirstChildValue(nameNode, '');
				var emailNode = XmlHelper.getFirstChildNodeByName(contactNode, 'email');
				var email = XmlHelper.getFirstChildValue(emailNode, '');
				this.contacts.push({ sContactId: sContactId, name: name, email: email });
			}
		}
	}
};

if (typeof window.JSFileLoaded != 'undefined') {
	JSFileLoaded();
}
