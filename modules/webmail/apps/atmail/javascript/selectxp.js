// Check if we are using Mozilla
var agt=navigator.userAgent.toLowerCase();
var moz;
moz = agt.indexOf('gecko');

var ShiftKey;
var Users = new Array();
var Gender = new Array();
var GroupNames = new Array();
var Groups = new Array();
var ShiftSelectID = new Array();
var GroupState = new Array();
var SelectedUsers = new Array();
var SelectedGroups = new Array();
var AddedUsers = new Array();
var AddedGroups = new Array();
var FrameNames = new Array();
var TableStart = "<html><head><link rel=\"stylesheet\" href=\"html/english/xp/css/gui.css\" type=\"text/css\"></head><body bgcolor=\"#FFFFFF\" topmargin=\"0\" bottommargin=\"0\" leftmargin=\"0\" rightmargin=\"0\" marginheight=\"0\" marginwidth=\"0\"><table id=\"MainTable\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\"  border=\"0\">";
var RowDividerSelect = "<tr><td colspan=\"2\" bgcolor=\"#9EC6E8\"><img src=\"imgs/xp/shim.gif\" width=\"1\" height=\"1\" border=\"0\"></td></tr>";
var RowDividerUnselect = "<tr><td colspan=\"2\" bgcolor=\"#D8D2BD\"><img src=\"imgs/xp/shim.gif\" width=\"1\" height=\"1\" border=\"0\"></td></tr>";
var RowGroupP1 = "<tr style=\"cursor : hand;\"><td colspan=\"2\" width=\"100%\" bgcolor=\"#F7F7EF\"><table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\" ><tr><td onclick=\"parent.ToggleGroupSelect('"
var RowGroupP2 = "');\" width=\"29\"><img src=\"imgs/xp/shim.gif\" width=\"5\"  height=\"1\" border=\"0\"><img src=\"imgs/xp/status_bar_group.gif\" width=\"19\"  border=\"0\"><img src=\"imgs/xp/shim.gif\" width=\"5\" height=\"1\" border=\"0\"></td><td onclick=\"parent.ToggleGroupSelect('"
var RowGroupAllUsersP2 = "');\" width=\"29\"><img src=\"imgs/xp/shim.gif\" width=\"5\" height=\"1\" border=\"0\"><img src=\"imgs/xp/status_bar_all_users.gif\" width=\"19\"  border=\"0\"><img src=\"imgs/xp/shim.gif\" width=\"5\" height=\"1\" border=\"0\"></td><td onclick=\"parent.ToggleGroupSelect('"
var RowGroupP3 = "');\" width=\"100%\"><small>";
var RowGroupP4 = "</small></td><td width=\"23\" onclick=\"parent.ToggleGroup('"
var RowGroupP5 = "');\"><img src=\"imgs/xp/shim.gif\" width=\"5\" height=\"1\" border=\"0\"><img src=\"imgs/xp/object_"
var RowGroupP6 = ".gif\" width=\"13\" height=\"13\" border=\"0\"><img src=\"imgs/xp/shim.gif\" width=\"5\" height=\"1\" border=\"0\"></td></tr></table></td></tr>";
var RowGroupedUserP1Select = "<tr bgcolor=\"#DEEBF6\" style=\"cursor : hand;\" onclick=\"parent.ToggleGroupSelect('";
var RowGroupedUserP1Unselect = "<tr style=\"cursor : hand;\" onclick=\"if (event.shiftKey) parent.ShiftKey = true; parent.ToggleGroupSelect('";
//var RowGroupedUserP1Unselect = "<tr style=\"cursor : hand;\" onclick=\"parent.ToggleGroupSelect('";
var RowGroupedUserP2 = "');\"><td width=\"20\"><img src=\"imgs/xp/shim.gif\" width=\"20\" height=\"1\" border=\"0\"></td><td width=\"100%\"><table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\"><tr><td width=\"23\"><img src=\"imgs/xp/shim.gif\" width=\"5\" height=\"1\" border=\"0\"><img src=\"imgs/xp/status_bar_";
var RowGroupedUserP3 = ".gif\" width=\"13\"  border=\"0\"><img src=\"imgs/xp/shim.gif\" width=\"5\" height=\"1\" border=\"0\"></td><td width=\"100%\"><small>";
var RowGroupedUserP4 = "</small></td></tr></table></td></tr>";
var RowUserP1Select = "<tr bgcolor=\"#DEEBF6\" style=\"cursor : hand;\" onclick=\"parent.ToggleUserSelect('";
var RowNextP1Select = "<tr bgcolor=\"#DEEBF6\" style=\"cursor : hand;\" onclick=\"parent.JumpRecords('next', '";

var RowUserP1Unselect = "<tr style=\"cursor : hand;\" onClick=\"if (event.shiftKey) parent.ShiftKey = true; parent.ToggleUserSelect('";
//var RowUserP1Unselect = "<tr style=\"cursor : hand;\" onClick=\"parent.ToggleUserSelect('";
var RowUserP2 = "');\"><td colspan=\"2\" width=\"100%\"><table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\"><tr><td width=\"29\"><img src=\"imgs/xp/shim.gif\" height=\"1\" width=\"8\"  border=\"0\"><img src=\"imgs/xp/status_bar_";
var RowUserP3 = ".gif\" width=\"13\"  border=\"0\"><img src=\"imgs/xp/shim.gif\" width=\"8\" height=\"1\" border=\"0\"></td><td width=\"100%\"><small>";
var RowUserP4 = "</small></td></tr></table></td></tr>"
var TableEnd = "</table><script>document.onselectstart = filterv; function filterv() { return false; } </" + "script></body></html>";
var GroupsUsersName = 'GroupsUsersFrame';

function LoadSelectXP(Names) {

	LoadSelectXPVars();

	FrameNames = Names.split(/,/);

	ShiftSelectID["Main"] = false;
	for (var i in FrameNames) {
		eval("ShiftSelectID['" + FrameNames[i] + "'] = false");
	}
	for (var i in GroupNames) {
		GroupState[i] = "hidden";
	}
	for (var i in Users) {
		SelectedUsers[i] = false;
	}
	for (var i in GroupNames) {
		SelectedGroups[i] = false;
	}
	for (var i in Users) {

		// Add the user into the main frame if required
		if(!AddedUsers[i])
		AddedUsers[i] = false;

	}
	for (var i in GroupNames) {

		if(!AddedGroups[i])
		AddedGroups[i] = false;
	}
	DrawUnselectedFrame();

	// Draw each frame with the user list if specified
	for (var i in FrameNames) {
		eval("DrawSelectedFrame('" + FrameNames[i] + "')");
	}
}

function ToggleUserSelect(UserID) {
	var ShiftSelectFrame = AddedUsers[UserID];
	if (ShiftSelectFrame == false) {
		ShiftSelectFrame = "Main";
	}
	if (SelectedUsers[UserID] == true) {
		SelectedUsers[UserID] = false;
	} else {
		SelectedUsers[UserID] = true;
	}
	if (ShiftKey == true) {
		ShiftKey = false;
		if (ShiftSelectID[ShiftSelectFrame] != false) {
			var ShiftSelectGoA = false;
			var ShiftSelectGoB = false;
			var ShiftSelectDone = false;
			for (var i in Users) {
				var TmpFrameName = AddedUsers[i];
				if (TmpFrameName == false) {
					TmpFrameName = "Main";
				}
				if (TmpFrameName == ShiftSelectFrame) {
					if (ShiftSelectGoA == true && ShiftSelectGoB == false) {
						SelectedUsers[i] = true;
						ShiftSelectDone = true;
					}
					if (i == ShiftSelectID[ShiftSelectFrame]) {
						ShiftSelectGoA = true;
					} else if (i == UserID) {
						ShiftSelectGoB = true;
					}
				}
			}
			if (ShiftSelectDone == false) {
				Users.reverse();
				ShiftSelectGoA = false;
				ShiftSelectGoB = false;
				for (var i in Users) {
					var TmpFrameName = AddedUsers[i];
					if (TmpFrameName == false) {
						TmpFrameName = "Main";
					}
					if (TmpFrameName == ShiftSelectFrame) {
						if (ShiftSelectGoA == true && ShiftSelectGoB == false) {
							SelectedUsers[i] = true;
							ShiftSelectDone = true;
						}
						if (i == ShiftSelectID[ShiftSelectFrame]) {
							ShiftSelectGoB = true;
						} else if (i == UserID) {
							ShiftSelectGoA = true;
						}
					}
				}
				Users.reverse();
			}
		}
	}
	ShiftSelectID[ShiftSelectFrame] = UserID;
	if (ShiftSelectFrame == "Main") {
		DrawUnselectedFrame();
	} else {
		DrawSelectedFrame(ShiftSelectFrame);
	}
}

function ToggleGroupSelect(GroupID) {
	if (SelectedGroups[GroupID] == true) {
		SelectedGroups[GroupID] = false;
	} else {
		SelectedGroups[GroupID] = true;
	}
	ToggleGroup(GroupID, "Expand");
}

function ToggleGroup(GroupID, State) {
	if (SelectedGroups[GroupID] == true) {
		GroupState[GroupID] = "visible";
	} else {
		if (State == "Expand") {
			GroupState[GroupID] = "visible";
		} else if (State == "Detract") {
			GroupState[GroupID] = "hidden";
		} else {
			if (GroupState[GroupID] == "visible") {
				GroupState[GroupID] = "hidden";
			} else {
				GroupState[GroupID] = "visible";
			}
		}
	}
	var Frame = AddedGroups[GroupID];
	if (Frame == false) {
		DrawUnselectedFrame();
	} else {
		DrawSelectedFrame(Frame);
	}
}

function AddSelected(Frame) {
	for (var i in SelectedUsers) {
		if (SelectedUsers[i] == true && AddedUsers[i] == false) {
			SelectedUsers[i] = false;
			AddedUsers[i] = Frame;
		}
	}
	for (var i in SelectedGroups) {
		if (SelectedGroups[i] == true && AddedGroups[i] == false) {
			SelectedGroups[i] = false;
			AddedGroups[i] = Frame;
		}
	}
	DrawUnselectedFrame();
	DrawSelectedFrame(Frame);
}

function RemoveAdded(Frame) {
	for (var i in SelectedUsers) {
		if (SelectedUsers[i] == true && AddedUsers[i] == Frame) {
			SelectedUsers[i] = false;
			AddedUsers[i] = false;
		}
	}
	for (var i in SelectedGroups) {
		if (SelectedGroups[i] == true && AddedGroups[i] == Frame) {
			SelectedGroups[i] = false;
			AddedGroups[i] = false;
		}
	}
	DrawUnselectedFrame();
	DrawSelectedFrame(Frame);
}

function DrawUnselectedFrame() {
	var RowCounter = 0;
	var HTMLString = TableStart;

	for (var i in GroupNames) {
		if (AddedGroups[i] == false) {
			RowCounter ++;
			if (GroupNames[i] == "All Users") {
				HTMLString += RowGroupP1 + i + RowGroupAllUsersP2 + i + RowGroupP3 + GroupNames[i] + RowGroupP4 + i + RowGroupP5 + ( GroupState[i] || 'hidden' ) + RowGroupP6 + RowDividerUnselect;
			} else {
				HTMLString += RowGroupP1 + i + RowGroupP2 + i + RowGroupP3 + GroupNames[i] + RowGroupP4 + i + RowGroupP5 + ( GroupState[i] || 'hidden' ) + RowGroupP6 + RowDividerUnselect;
			}
			if (GroupState[i] == "visible") {
				GroupArray = Groups[i].split(",");
				for (var x in GroupArray) {
					RowCounter ++;
					if (SelectedGroups[i] == true) {
						HTMLString += RowGroupedUserP1Select;
					} else {
						HTMLString += RowGroupedUserP1Unselect;
					}
					HTMLString += i + RowGroupedUserP2 + 'male' + RowGroupedUserP3 + GroupArray[x] + RowGroupedUserP4;
					if (SelectedGroups[i] == true) {
						HTMLString += RowDividerSelect;
					} else {
						HTMLString += RowDividerUnselect;
					}
				}
			}
		}
	}

	var Counter1 = 0;
	var Counter2 = 0;
	for (var i in Users) {
		if (AddedUsers[i] == false) {
			Counter1 ++
		}
	}
	for (var i in Users) {
		if (AddedUsers[i] == false) {
			Counter2 ++
			RowCounter ++;

			if(Users[i] == 'Next Records')	{
				HTMLString += RowNextP1Select;
				//alert(Gender[i] + ":" + Users[i]);
			} else if (SelectedUsers[i] == true) {
				HTMLString += RowUserP1Select;
			} else {
				HTMLString += RowUserP1Unselect;
			}

			if(!Gender[i])
			Gender[i] = "male"

			//if(Gender[i] == 'Next')	{
			//HTMLString += [i] + RowUserP2 + Gender[i] + RowUserP3 + Users[i] + RowUserP4;
			//} else	{
			HTMLString += [i] + RowUserP2 + Gender[i] + RowUserP3 + Users[i] + RowUserP4;
			//}

			if (Counter1 != Counter2) {
				if (SelectedUsers[i] == true) {
					HTMLString += RowDividerSelect;
				} else {
					HTMLString += RowDividerUnselect;
				}
			}
		}
	}
	HTMLString += TableEnd;

	var GroupsUsersFrameScrollPos = 0;

	// Only run if the window is init
//	if( eval(GroupsUsersName + ".document.body") )	{
//	GroupsUsersFrameScrollPos = eval(GroupsUsersName + ".document.body.scrollTop");
//	}

	try {
	GroupsUsersFrameScrollPos = eval(GroupsUsersName + ".document.body.scrollTop");
	} catch(e)      {
	GroupsUsersFrameScrollPos = 0;
	//alert(e);
	}

	try {
	eval(GroupsUsersName + ".document.open()");
	eval(GroupsUsersName + ".document.write(HTMLString)");
	eval(GroupsUsersName + ".document.close()");

	if (RowCounter >= 16) {

		if(moz < 0)	{
		eval(GroupsUsersName + ".document.getElementById('MainTable').style.borderRight = '1px solid #D8D2BD'");
		eval(GroupsUsersName + ".document.body.scroll = 'yes'");
		}

	} else {

		if(moz < 0)	{
		eval(GroupsUsersName + ".MainTable.style.borderRight = ''");
		eval(GroupsUsersName + ".document.body.scroll = 'no'");
		}
	}
	eval(GroupsUsersName + ".scrollTo(0,GroupsUsersFrameScrollPos)");
    } catch(e) {}
}

function DrawSelectedFrame(Frame) {

	if(!Frame)
	return;

	var SelectedGroupsField = new Array();
	var SelectedUsersField = new Array();
	var RowCounter = 0;
	var HTMLString = TableStart;

	for (var i in GroupNames) {
		if (AddedGroups[i] == Frame) {
			RowCounter ++;
			SelectedGroupsField.push(i);
			if (GroupNames[i] == "All Users") {
				HTMLString += RowGroupP1 + i + RowGroupAllUsersP2 + i + RowGroupP3 + GroupNames[i] + RowGroupP4 + i + RowGroupP5 + GroupState[i] + RowGroupP6 + RowDividerUnselect;
			} else {
				HTMLString += RowGroupP1 + i + RowGroupP2 + i + RowGroupP3 + GroupNames[i] + RowGroupP4 + i + RowGroupP5 + GroupState[i] + RowGroupP6 + RowDividerUnselect;
			}
			if (GroupState[i] == "visible") {
				GroupArray = Groups[i].split(",");
				for (var x in GroupArray) {
					RowCounter ++;
					if (SelectedGroups[i] == true) {
						HTMLString += RowGroupedUserP1Select;
					} else {
						HTMLString += RowGroupedUserP1Unselect;
					}
					HTMLString += i + RowGroupedUserP2 + 'male' + RowGroupedUserP3 + GroupArray[x] + RowGroupedUserP4;
					if (SelectedGroups[i] == true) {
						HTMLString += RowDividerSelect;
					} else {
						HTMLString += RowDividerUnselect;
					}
				}
			}
		}
	}

	var Counter1 = 0;
	var Counter2 = 0;
	for (var i in Users) {
		if (AddedUsers[i] == Frame) {
			Counter1 ++;
		}
	}
	for (var i in Users) {
		if (AddedUsers[i] == Frame) {
			Counter2++;
			RowCounter ++;
			SelectedUsersField.push(i);
			if (SelectedUsers[i] == true) {
				HTMLString += RowUserP1Select;
			} else {
				HTMLString += RowUserP1Unselect;
			}

			if(!Gender[i])
			Gender[i] = "male";

			HTMLString += [i] + RowUserP2 + Gender[i] + RowUserP3 + Users[i] + RowUserP4;
			if (Counter1 != Counter2) {
				if (SelectedUsers[i] == true) {
					HTMLString += RowDividerSelect;
				} else {
					HTMLString += RowDividerUnselect;
				}
			}
		}
	}
	HTMLString += TableEnd;
	eval("document.abook." + Frame + "SelectedGroups.value = '" + SelectedGroupsField + "'");
	eval("document.abook." + Frame + "SelectedUsers.value = '" + SelectedUsersField + "'");

	var SelectedFrameScrollPos = 0;

	try {
	SelectedFrameScrollPos = eval(Frame + "SelectedFrame.document.body.scrollTop");
	} catch(e)      {
	SelectedFrameScrollPos = 0;
	}

	eval(Frame + "SelectedFrame.document.open()");
	eval(Frame + "SelectedFrame.document.write(HTMLString)");
	eval(Frame + "SelectedFrame.document.close()");
	if (RowCounter >= 5) {

		if(moz < 0)	{
		eval(Frame + "SelectedFrame.MainTable.style.borderRight = '1px solid #D8D2BD'");
		eval(Frame + "SelectedFrame.document.body.scroll = 'yes'");
		}

	} else {

		if(moz < 0)	{
		eval(Frame + "SelectedFrame.MainTable.style.borderRight = ''");
		eval(Frame + "SelectedFrame.document.body.scroll = 'no'");
		}

	}
	eval(Frame + "SelectedFrame.scrollTo(0,SelectedFrameScrollPos)");
}

function TestFrameValue(myField, Account, Gender)	 {

	for (var i in FrameNames) {
		// The account already exists in the selected table
		if(FrameNames[i] == AddedUsers[myField])
		return 0
	}

eval('Users["' + myField + '"] = Account');
eval('Gender["' + myField + '"] = Gender');
eval('AddedUsers["' + myField + '"] = false');
eval('SelectedUsers["' + myField + '"] = false');

}

function TestFrameGroupValue(myField, Account)	 {

	for (var i in FrameNames) {
		// The account already exists in the selected table
		if(FrameNames[i] == AddedGroups[myField])
		return 0
	}

eval('Groups["' + myField + '"] = Account');
eval('GroupNames["' + myField + '"] = Account');
eval('AddedGroups["' + myField + '"] = false');
eval('SelectedGroups["' + myField + '"] = false');

}

function NullFrameValue(myField)	 {

	for (var i in FrameNames) {
		// The account already exists in the selected table
		if(FrameNames[i] == AddedUsers[myField])
		return 0
	}


eval('Users["' + myField + '"] = null');
eval('Gender["' + myField + '"] = null');
eval('AddedUsers["' + myField + '"] = null');
eval('SelectedUsers["' + myField + '"] = null');

}

function NullFrameValueGroup(myField)	 {

	for (var i in FrameNames) {
		// The account already exists in the selected table
		if(FrameNames[i] == AddedGroups[myField])
		return 0
	}

eval('Groups["' + myField + '"] = null');
eval('AddedGroups["' + myField + '"] = null');
eval('SelectedGroups["' + myField + '"] = null');

}

// Match a string for an email address
function FindEmail(v) {
  var re = new RegExp(/\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+/);
  var m = re.exec(v);

  // Return the email only if the regex matches
  if(m)	{
  return m[0];
  }
}

function FindGroup(v) {
  var re = new RegExp(/(.*)\sGroup/);
  var m = re.exec(v);

  // Return the email only if the regex matches
  if(m)	{
  return m[1];
  }
}

// Generate a list of users that have read/write access
function GeneratePermissions(permstr)	{

var usersp = permstr.split(",");

// Split up the permissions string and display the results
for( i in usersp)	 {
	var user = usersp[i];
	var subuser = user.split(":");
	var email = subuser[0];

	if(email)	{

	var access;

		// Determine if a user has read or write access
		if(subuser[2] == '1')	{
		access = 'Write';
		} else	{
		access = 'Read';
		}

eval("Users['" + email + "'] = '" + subuser[0] + "'");
eval("Gender['" + email + "'] = 'male' ");
eval("AddedUsers['" + email + "'] = '" + access + "'");
eval("SelectedUsers['" + email + "'] = false");

	}

	}

}

function JumpRecords(Move, id)	{
	//alert('MOVE = ' + Move + ":" + " ID = " + id);

	parent.submit_search('To,Cc,Bcc', id);

}
