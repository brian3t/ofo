function openUsersWindow(windowUrl, formName, fieldName, selectionType)
{
	var queryString = "";
  if (formName != "") {
		queryString = "?form_name=" + formName;
	}
  if (fieldName != "") {
		queryString += ((queryString == "") ? "?" : "&");
		queryString += "field_name=" + fieldName;
	}
  if (selectionType != "") {
		queryString += ((queryString == "") ? "?" : "&");
		queryString += "selection_type=" + selectionType;
	}
	var usersWin = window.open (windowUrl + queryString, 'usersWin', 'toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=600,height=500');
	usersWin.focus();
}

function selectUser(userId, userName)
{
	if (window.opener) {
		var formName  = document.users_list.form_name.value;
		var fieldName  = document.users_list.field_name.value;
		var selectionType = document.users_list.selection_type.value;
		window.opener.setUser(userId, userName, formName, fieldName, selectionType);
		window.opener.focus();
	}
	window.close();
}

function closeUsersWindow()
{
	window.opener.focus();
	window.close();
}

function setUser(userId, userName, formName, fieldName, selectionType)
{
	if (selectionType == "single") {
		var idControl = document.forms[formName].elements[fieldName];
		var userNameObj = document.getElementById("userName");
		idControl.value = userId;
		var userInfo = "<a href=\""+userViewLink+"?user_id="+userId+"\" class=\"title\" target=\"_blank\">"+userName+"</a>";
		userInfo += " (#"+userId+") - <a href=\"#\" onClick=\"removeSingleUser('" + id + "', '" + formName + "', '" + fieldName + "'); return false;\">" + removeButton + "</a> | ";
		userNameObj.innerHTML = userInfo;
	} else {
		var userAdded = false;
		var usersArray = "";
		if (fieldName == "friends_ids") {
			usersArray = friends;
		} else {
			usersArray = users;
		}
		for(var id in usersArray)
		{
			if (id == userId) {
				userAdded = true;
			}
		}
		
		if (!userAdded) {
			if (fieldName == "friends_ids") {
				friends[userId] = new Array(userName);
			} else {
				users[userId] = new Array(userName);
			}
			generateUsersList(formName, fieldName);
		}
	}
}

function removeSingleUser(userId, formName, fieldName)
{
	var idControl = document.forms[formName].elements[fieldName];
	var userNameObj = document.getElementById("userName");
	idControl.value = "";
	userNameObj.innerHTML = "";
}


function removeUser(userId, formName, fieldName)
{
	if (fieldName == "friends_ids") {
		delete friends[userId];
	} else {
		delete users[userId];
	}
	generateUsersList(formName, fieldName);
}

function generateUsersList(formName, fieldName)
{
	var selectedDiv = ""; var usersArray = ""; 
	var idsControl = ""; var usersIds = "";
	if (fieldName == "friends_ids") {
		selectedDiv = document.getElementById("selectedFriends");
		usersArray = friends;
	} else {
		selectedDiv = document.getElementById("selectedUsers");
		usersArray = users;
	}
	selectedDiv.innerHTML = "";
	for(var id in usersArray)
	{
		var userName = usersArray[id];
		var userInfo = "<li class=selectedCategory>" + userName;
		userInfo += " - <a href=\"#\" onClick=\"removeUser('" + id + "', '" + formName + "', '" + fieldName + "'); return false;\">" + removeButton + "</a>";
		if (selectedDiv.insertAdjacentHTML) {
			selectedDiv.insertAdjacentHTML("beforeEnd", userInfo);
		} else {
			selectedDiv.innerHTML += userInfo;
		}
		if (usersIds != "") { usersIds += "," }
		usersIds += id;
	}
	idsControl = document.forms[formName].elements[fieldName];
	idsControl.value = usersIds;
}
