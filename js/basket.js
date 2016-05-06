// basket javacript
function confirmDelete(itemType)
{
	var confirmMessage = removeFromCart;
	confirmMessage = confirmMessage.replace("\{item_type\}", itemType);
	return confirm(confirmMessage);
}

function confirmAllDelete(confirmMessage)
{
	return confirm(confirmMessage);
}

function changeListbox(cart_id, control, product_name, old_quantity)
{
	if(confirmChanges(cart_id, control.options[control.selectedIndex].text, product_name, old_quantity)) {
		document.basket.submit();
	} else {
		for (var i = 0; i < control.options.length; i++) {
			if (control.options[i].value == old_quantity) {
				control.options[i].selected = true;
			} else {
				control.options[i].selected = false;
			}
		}
	}
}

function changeTextbox(cart_id, control, product_name, old_quantity)
{
	if(confirmChanges(cart_id, control.value, product_name, old_quantity)) {
		document.basket.submit();
	}	else	{
		control.value = old_quantity;
	}
}

function checkChanges(e, cart_id, control, product_name, old_quantity)
{
	var key;
	if (window.event) {
		key = window.event.keyCode; //IE
	} else {
		key = e.which; //firefox
	}

	if (key == 13) {
		if (confirmChanges(cart_id, control.value, product_name, old_quantity)) {
			document.basket.submit();
		} else {
			control.value = old_quantity;
		}
		return false;
	} else {
		return true;
	}
}


function confirmChanges(cart_id, new_quantity, product_name, old_quantity)
{
	var isConfirm = false;
	var confirmMessage = "";
  if (new_quantity < 1)
  {
		confirmMessage = cartQtyZero;
		confirmMessage = confirmMessage.replace("\{old_quantity\}", old_quantity);
		confirmMessage = confirmMessage.replace("\{product_name\}", product_name);
		if (confirm(confirmMessage))
		{
			isConfirm = true;
			document.basket.cart.value = "RM";
			document.basket.cart_id.value = cart_id;
			document.basket.new_quantity.value = 0;
		}
  }
  else
  {
		confirmMessage = alterCartQty;
		confirmMessage = confirmMessage.replace("\{old_quantity\}", old_quantity);
		confirmMessage = confirmMessage.replace("\{new_quantity\}", new_quantity);
		confirmMessage = confirmMessage.replace("\{product_name\}", product_name);
    if (confirm(confirmMessage))
		{
			isConfirm = true;
			document.basket.cart.value = "QTY";
			document.basket.cart_id.value = cart_id;
			document.basket.new_quantity.value = new_quantity;
		}
  }
	return isConfirm;
}

function checkFastCheckoutDetails()
{
	requiredMessage = requiredMessage.replace("<b>", "");
	requiredMessage = requiredMessage.replace("</b>", "");
	var orderForm = document.fast_checkout;
	var errorMessage = ""; var controlObj = ""; var controlName = "";
	if (orderForm.country_required.value == "*") {
		if (orderForm.fast_checkout_country_id.options[orderForm.fast_checkout_country_id.selectedIndex].value == "") {
			controlName = "";
			controlObj = document.getElementById("fast_checkout_country_name");
			if (controlObj) { controlName = controlObj.innerHTML; }
			errorMessage += requiredMessage.replace("\{field_name\}", controlName) + ".\n";
		}
	}
	if (orderForm.state_required.value == "*") {
		if (orderForm.fast_checkout_state_id.options[orderForm.fast_checkout_state_id.selectedIndex].value == "") {
			controlName = "";
			controlObj = document.getElementById("fast_checkout_state_name");
			if (controlObj) { controlName = controlObj.innerHTML; }
			errorMessage += requiredMessage.replace("\{field_name\}", controlName) + ".\n";
		}
	}
	if (orderForm.postcode_required.value == "*" && orderForm.fast_checkout_postcode.value == "") {
		controlName = "";
		controlObj = document.getElementById("fast_checkout_postcode_name");
		if (controlObj) { controlName = controlObj.innerHTML; }
		errorMessage += requiredMessage.replace("\{field_name\}", controlName) + ".\n";
	}

	if (errorMessage != "") {
		alert(errorMessage);
		return false;
	} else {
		return true;
	}
}
