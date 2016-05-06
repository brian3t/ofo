// shopping javacript

function confirmBuy(itemForm, buttonType)
{
	if (buttonType == "wishlist") {
		itemForm.cart.value = "WISHLIST";
	} else {
		itemForm.cart.value = "ADD";
	}
	var params = getProductParams(itemForm);
	var basePrice = params["base_price"];
	// check what options were selected and what options is active
	var returnedValues = checkOptions(itemForm);
	var selectedOptions = returnedValues[0];
	var activeOptions = returnedValues[1];
	// check options for requirements
	// var prMessage = requiredProperty;
	var productName = params["item_name"];
	for (prID in activeOptions) {
		if (itemForm.elements["property_control_" + prID]) { // check if it is property control
			var prRequired = itemForm.elements["property_required_" + prID].value;
			var prControl = itemForm.elements["property_control_" + prID].value;
			if (prRequired == 1 && activeOptions[prID] && !selectedOptions[prID]) {
				var propertyName = itemForm.elements["property_name_" + prID].value;
				prMessage = prMessage.replace("\{property_name\}", propertyName);
				prMessage = prMessage.replace("\{product_name\}", productName);
				alert(prMessage);	
				if (prControl != "RADIOBUTTON" && prControl != "CHECKBOXLIST" && prControl != "TEXTBOXLIST" && prControl != "LABEL") {
					itemForm.elements["property_" + prID].focus();
				}
				return false;
			}
		}
	}
	// calculate price for selected options
	var propertiesPrice = calculateOptionsPrice(itemForm, selectedOptions);

	var productPrice = basePrice + params["comp_price"] + propertiesPrice;
	if (params["zero_product_action"] == 2 && productPrice == 0) {
		alert(params["zero_product_warn"]);
		return false;
	}

		return true;

}

function confirmSubscription(itemForm)
{
	if (confirmAdd == "1") {
		return confirm(addSubscription);
	} else {
		return true;
	}
}

function addToWishlist()
{
	var formId = document.saved_types.form_id.value;
	if (formId != "") {
		var formName = "form_" + formId
		var itemForm = document.forms[formName];
		var typesTotal = parseInt(document.saved_types.saved_types_total.value);
		var typeId = "";
		if (typesTotal == 1) {
			var typeId = document.saved_types.type_id.value;
		} else if (typesTotal > 1) {
			var typeId = document.saved_types.type_id.options[document.saved_types.type_id.selectedIndex].value;
		}
		if (typeId != "") {
			itemForm.saved_type_id.value = typeId;
			hideSavedTypes();
			confirmBuy(itemForm, "wishlist");
		} else {
			alert("Please select a type");
		}
	} else {
		alert("Product wasn't selected");
	}
}

function popupSavedTypes(itemForm)
{                              	
	var params = getProductParams(itemForm);
	var formId = params["form_id"];
	document.saved_types.form_id.value = formId;
	var savedTypesShadow = document.getElementById("saved_types_shadow");
	var savedTypesWin = document.getElementById("saved_types_win");
	if (formId != "") {
		var wishlistButton = document.getElementById("wishlist_" + formId);
		savedTypesWin.style.left = (findPosX(wishlistButton, 0) - 150) + "px";
		savedTypesWin.style.top = (findPosY(wishlistButton, 0) - 100) + "px";
		var arrayPageSizeWithScroll = getPageSizeWithScroll();
		savedTypesShadow.style.height = arrayPageSizeWithScroll[1] + "px";
	}

	savedTypesWin.style.display = "block";			
	savedTypesShadow.style.display = "block";			
	hideSelectBoxes("saved_types_win", new Array("type_id"));
}

function hideSavedTypes()
{                              	
	document.saved_types.form_id.value = "";
	var savedTypesShadow = document.getElementById("saved_types_shadow");
	var savedTypesWin = document.getElementById("saved_types_win");
	savedTypesWin.style.display = "none";			
	savedTypesShadow.style.display = "none";			
	showSelectBoxes("saved_types_win");
}

function changeSavedType()
{
	var prevTypeId = document.saved_types.prev_type_id.value;
	var typeIdControl = document.saved_types.type_id;
	var selectedTypeId = typeIdControl.options[typeIdControl.selectedIndex].value;
	document.saved_types.prev_type_id.value = selectedTypeId;
	if (prevTypeId != selectedTypeId) {
		if (prevTypeId != "") {
			var typeDescBlock = document.getElementById("type_desc_" + prevTypeId);
			typeDescBlock.style.display = "none";			
		}
		if (selectedTypeId != "") {
			var typeDescBlock = document.getElementById("type_desc_" + selectedTypeId);
			typeDescBlock.style.display = "block";			
		}
	}
}

function changeProperty(itemForm)
{
	var selectedOptions = new Array();
	var priceControl = "";
	var htmlControl = false;
	var itemId = "";
	var taxPercent = 0;

	var params = getProductParams(itemForm);
	var taxNote = params["tax_note"];
	var pointsBase = params["base_points_price"];
	var prIDs = params["properties_ids"];
	var formId = params["form_id"];

	if (params["pe"] == "1") {
		return;
	}

	if (itemForm.tax_percent && itemForm.tax_percent.value != "") {
		taxPercent = parseFloat(itemForm.tax_percent.value);
		if (isNaN(taxPercent)) { taxPercent = 0; }
	}

	if (itemForm.add_id) {
		itemId = itemForm.add_id.value;
	} else if (itemForm.item_id) {
		itemId = itemForm.item_id.value;
	}
	if (itemId != "" && document.getElementById) {
		priceControl = document.getElementById("sales_price_" + itemId);
		if (!priceControl) {
			priceControl = document.getElementById("price_" + itemId);
		}
	} 
	var pointsPriceControl = document.getElementById("points_price_" + itemId);

	// check what options were selected and what options is active
	var returnedValues = checkOptions(itemForm);
	var selectedOptions = returnedValues[0];
	var activeOptions = returnedValues[1];
	// calculate price for selected options
	var totalAdditionalPrice = calculateOptionsPrice(itemForm, selectedOptions);

	// hide or show property blocks
	for (prID in activeOptions) {
		if (itemForm.elements["property_control_" + prID]) { // check if it is property control
			var propertyBlock = document.getElementById("pr_" + formId + "_" + prID);
			if (activeOptions[prID]) {
				propertyBlock.style.display = "block";				
			} else {
				propertyBlock.style.display = "none";				
			}
		}
	}

	// show hide image for subcomponents
	for (prID in activeOptions) {
		if (itemForm.elements["property_control_" + prID]) { // check if it is property control
			var prControl = itemForm.elements["property_control_" + prID].value;
			if (activeOptions[prID] && (prControl == "LISTBOX" || prControl == "RADIOBUTTON")) {
				var prValue = selectedOptions[prID];
	  
				var objId = formId + "_" + prID; // id for current product option
				if (prValue != "") {
					var image_button = document.getElementById("option_image_action_" + objId);
					if (!image_button) {
						var image_button       = document.createElement('a');				
						image_button.id        = "option_image_action_" + objId;
						image_button.href      = "#";
						image_button.onclick   = popupImage;
						image_button.style.display = "none";
						image_button.innerHTML = "<img src='images/icons/view_page.gif' alt='View' border='0'/>";
						var propertyObj = document.getElementById("pr_" + objId);
						if (propertyObj) { propertyObj.appendChild(image_button); }
					}				
					if (itemForm.elements["option_image_" + prValue]) {
						var image = itemForm.elements["option_image_" + prValue].value;
						if (itemForm.elements["option_image_action_" + prValue]) {
							image_button.onclick = (itemForm.elements["option_image_action_" + prValue].onclick);
						}					
						image_button.style.display = "inline";
						image_button.href  = image;
						image_button.title = itemForm.elements["property_" + prID].options[itemForm.elements["property_" + prID].selectedIndex].text;
					} else {
						image_button.style.display = "none";
					}
				} else {
					var image_button = document.getElementById("option_image_action_" + objId);
					if (image_button) {
						image_button.style.display = "none";
					}
				}
			}
		}
	}

	var basePrice = params["base_price"];
	var baseTax = 0;
	// check product quantity
	var quantity = 1;
	if (itemForm.quantity) {
		if (itemForm.quantity.selectedIndex) {
			quantity = parseInt(itemForm.quantity.options[itemForm.quantity.selectedIndex].value);
		} else {
			quantity = parseInt(itemForm.quantity.value);
		}
		if (isNaN(quantity)) { quantity = 1; } 
	}
	var isQuantityPrice = false;
	if(params["quantity_price"]) { 
		var prices = params["quantity_price"]; 
		if (prices != "") {
			prices = prices.split(",");
			for (var p = 0; p < prices.length; p = p + 5) {
				var minQuantity = parseInt(prices[p]);
				var maxQuantity = parseInt(prices[p + 1]);
				if (quantity >= minQuantity && quantity <= maxQuantity) {
					isQuantityPrice = true;
					basePrice = parseFloat(prices[p + 2]);
					baseTax = parseFloat(prices[p + 3]);
					var propertiesDiscount = parseFloat(prices[p + 4]);
					if (propertiesDiscount > 0) {
						totalAdditionalPrice -= (Math.round(totalAdditionalPrice * propertiesDiscount) / 100);
					}
					break;
				}
			}
		}
	}
	
	var price = basePrice + totalAdditionalPrice;
	var taxAmount = 0; var productPrice = 0; var taxPrice = 0; var priceExcl = 0;
	if (params["tax_prices_type"] == 1) {
		// price already includes tax
		if (isQuantityPrice) {
			taxPrice = Math.round((price) * 100) / 100; 
			taxAmount = baseTax; 
		} else {
			taxPrice = Math.round((price + params["comp_price"]) * 100) / 100; 
			taxAmount = (Math.round(price * 100) - Math.round(price * 10000 / ( 100 + taxPercent))) / 100; 
		}
		if (isQuantityPrice) {
			productPrice = Math.round((price - taxAmount) * 100) / 100;
		} else {
			productPrice = Math.round((price - taxAmount + params["comp_price"] - params["comp_tax"]) * 100) / 100;
		}
		priceExcl = productPrice;
	} else {
		if (isQuantityPrice) {
			taxAmount = baseTax; 
			productPrice = Math.round((price) * 100) / 100;
			taxPrice = Math.round((productPrice + taxAmount) * 100) / 100; 
		} else {
			taxAmount = Math.round(price * taxPercent) / 100; 
			productPrice = Math.round((price + params["comp_price"]) * 100) / 100;
			taxPrice = Math.round((productPrice + taxAmount + params["comp_tax"]) * 100) / 100; 
		}
		priceExcl = productPrice;
	}

	if (params["show_prices"] == 2) {
		productPrice = taxPrice;
		taxPrice = priceExcl;
	} else if (params["show_prices"] == 3) {
		productPrice = taxPrice;
	}

	if (priceControl) {
		if (params["zero_price_type"] != 0 && productPrice == 0) {
			if (params["zero_price_type"] == 1) { params["zero_price_message"] = ""; }
			priceControl.innerHTML = params["zero_price_message"];
		} else {
			priceControl.innerHTML = params["cleft"] + formatNumber(productPrice * params["crate"], params["cdecimals"], params["cpoint"], params["cseparator"]) + params["cright"];
		}
		priceBlockControl = document.getElementById("price_block_" + itemId);
		if (priceBlockControl) {
			if (params["zero_price_type"] == 1 && productPrice == 0) {
				priceBlockControl.style.display = "none";
			} else {
				priceBlockControl.style.display = "block";
			}
		}
	}
	taxPriceControl = document.getElementById("tax_price_" + itemId);
	if (taxPriceControl) {
		if (params["zero_price_type"] != 0 && taxPrice == 0) {
			taxPriceControl.innerHTML = "";
		} else {
			if (taxNote != "") { taxNote = " " + taxNote; }
			taxPriceControl.innerHTML = "(" + params["cleft"] + formatNumber(taxPrice * params["crate"], params["cdecimals"], params["cpoint"], params["cseparator"]) + params["cright"] + taxNote + ")";
		}
	}
	if (pointsPriceControl) {
		var pointsPrice = pointsBase + (totalAdditionalPrice * params["points_rate"]);
		pointsPriceControl.innerHTML = formatNumber(pointsPrice, params["points_decimals"]);
	}
}

function checkOptions(itemForm)
{
	var params = getProductParams(itemForm);
	var prIDs = params["properties_ids"];
	var selectedOptions = new Array();
	var activeOptions = new Array();
	var returnValues = new Array();

	// check first all selected options
	var prIDs = params["properties_ids"];
	if (prIDs != "") {
		var properties = prIDs.split(",");
		for ( var i = 0; i < properties.length; i++) {
			var prID = properties[i];
			var prValue = ""; 
			var prControl = itemForm.elements["property_control_" + prID].value;
			if (prControl == "LISTBOX") {
				prValue = itemForm.elements["property_" + prID].options[itemForm.elements["property_" + prID].selectedIndex].value;
				if (prValue != "") {
					selectedOptions[prID] = prValue;
				}
			} else if (prControl == "RADIOBUTTON") {
				var radioControl = itemForm.elements["property_" + prID];
				if (radioControl.length) {
					for ( var ri = 0; ri < radioControl.length; ri++) {
						if (radioControl[ri].checked) {
							prValue = radioControl[ri].value;
							break;
						}
					}
				} else {
					if (radioControl.checked) {
						prValue = radioControl.value;
					}
				}
				if (prValue != "") {
					selectedOptions[prID] = prValue;
				}
			} else if (prControl == "CHECKBOXLIST") {
				if (itemForm.elements["property_total_" + prID]) {
					var totalOptions = parseInt(itemForm.elements["property_total_" + prID].value);
					for ( var ci = 1; ci <= totalOptions; ci++) {
						if (itemForm.elements["property_" + prID + "_" + ci].checked) {
							var checkedValue = itemForm.elements["property_" + prID + "_" + ci].value;
							if (!selectedOptions[prID]) {
								selectedOptions[prID] = new Array();
							}
							selectedOptions[prID][checkedValue] = 1;
						}
					}
				} 
			} else if (prControl == "TEXTBOXLIST") {
				if (itemForm.elements["property_total_" + prID]) {
					var totalOptions = parseInt(itemForm.elements["property_total_" + prID].value);
					for ( var ci = 1; ci <= totalOptions; ci++) {
						if (itemForm.elements["property_" + prID + "_" + ci].value != "") {
							var valueId = itemForm.elements["property_value_" + prID + "_" + ci].value;
							var valueText = itemForm.elements["property_" + prID + "_" + ci].value;
							if (!selectedOptions[prID]) {
								selectedOptions[prID] = new Array();
							}
							selectedOptions[prID][valueId] = valueText;
						}
					}
				} 
			} else if (prControl == "LABEL"){
				// get from hidden control
				if (itemForm.elements["property_" + prID]) {
					prValue = itemForm.elements["property_" + prID].value;
					if (prValue != "") {
						selectedOptions[prID] = prValue;
					}
				}
			} else {
				prValue = itemForm.elements["property_" + prID].value;
				if (prValue != "") {
					selectedOptions[prID] = prValue;
				}
			}
		}
	}

	// second check for active options and correct selected options if necessary
	if (prIDs != "") {
		do {
			// save how many selected options we have at start
			var startSelectedNumber = selectedOptions.length;
			// check availability of parent options		
			var properties = prIDs.split(",");
			for ( var i = 0; i < properties.length; i++) {
				var prID = properties[i];
				var parentPropertyId = itemForm.elements["property_parent_id_" + prID].value;
				var parentValueId = itemForm.elements["property_parent_value_id_" + prID].value;
				var showProperty = true;
				if (parentPropertyId != "") {
					if (!selectedOptions[parentPropertyId]) {
						showProperty = false;
					} else if (parentValueId != "") {
						if (!selectedOptions[parentPropertyId][parentValueId] && selectedOptions[parentPropertyId] != parentValueId) {
							showProperty = false;
						}
					}
				}
				activeOptions[prID] = showProperty;
				if (!showProperty) {
					// delete from selected
					if (selectedOptions[prID]) {
						delete selectedOptions[prID];
					}
	  
					// clear all options
					var prControl = itemForm.elements["property_control_" + prID].value;
					if (prControl == "LISTBOX") {
						var selectedIndex = itemForm.elements["property_" + prID].selectedIndex;
						if (selectedIndex > 0) {
							itemForm.elements["property_" + prID].options[0].selected = true;
						}
					} else if (prControl == "RADIOBUTTON") {
						var radioControl = itemForm.elements["property_" + prID];
						if (radioControl.length) {
							for ( var ri = 0; ri < radioControl.length; ri++) {
								radioControl[ri].checked = false;
							}
						} else {
							radioControl.checked = false;
						}
	  
					} else if (prControl == "CHECKBOXLIST") {
						var totalOptions = parseInt(itemForm.elements["property_total_" + prID].value);
						for ( var ci = 1; ci <= totalOptions; ci++) {
							itemForm.elements["property_" + prID + "_" + ci].checked = false;
						}
					} else if (prControl == "TEXTBOXLIST") {
						var totalOptions = parseInt(itemForm.elements["property_total_" + prID].value);
						for ( var ci = 1; ci <= totalOptions; ci++) {
							itemForm.elements["property_" + prID + "_" + ci].value = "";
						}
					} else if (prControl == "TEXTBOX" || prControl == "TEXTAREA") {
						itemForm.elements["property_" + prID].value = "";
					}
				}
			}
		} while (startSelectedNumber != selectedOptions.length);
	}

	returnValues[0] = selectedOptions;
	returnValues[1] = activeOptions;

	return returnValues;
}

function calculateOptionsPrice(itemForm, selectedOptions)
{
	var params = getProductParams(itemForm);
	var propertiesPrice = 0;
	var prPrice = 0;
	for (prID in selectedOptions) {
		if (itemForm.elements["property_control_" + prID]) { // check if it is property control
			var usedControls = 0; var controlText = ""; var freeLetters = 0;
			var priceType = parseInt(itemForm.elements["property_price_type_" + prID].value);
			var priceAmount = parseFloat(itemForm.elements["property_price_" + prID].value);
			if (isNaN(priceAmount)) { priceAmount = 0; }
			var freePriceType = parseInt(itemForm.elements["property_free_price_type_" + prID].value);
			var freePriceAmount = itemForm.elements["property_free_price_amount_" + prID].value;
			var freeControls = 0;
			if (freePriceType == 1) {
				freePriceAmount = parseFloat(freePriceAmount);
			} else {
				freePriceAmount = parseInt(freePriceAmount);
			}
			if (isNaN(freePriceAmount)) { freePriceAmount = 0; }
			if (freePriceType == 2) {
				freeControls = freePriceAmount;
			} else if (freePriceType == 3 || freePriceType == 4) {
				freeLetters = freePriceAmount;
			}
	    
			var prControl = itemForm.elements["property_control_" + prID].value;
			if (prControl == "LISTBOX" || prControl == "RADIOBUTTON") {
				usedControls++;
				prPrice = getOptionPrice(itemForm, selectedOptions[prID]);
				propertiesPrice += prPrice;
			} else if (prControl == "CHECKBOXLIST" || prControl == "TEXTBOXLIST") {
				var values = selectedOptions[prID];
				for (valueId in values) {
					usedControls++;
					prPrice = getOptionPrice(itemForm, valueId);
					propertiesPrice += prPrice;
					if (prControl == "TEXTBOXLIST") {
						controlText += selectedOptions[prID][valueId];
						if (freeControls >= usedControls) {
							if (priceType == 3) {
								freeLetters = controlText.length;
							} else if (priceType == 4) {
								freeLetters = controlText.replace(/[\n\r\t\s]/g, "").length;
							}
						}
					}
				}	
			} else {
				usedControls++;
				if (prControl == "TEXTAREA" || prControl == "TEXTBOX") {
					controlText = selectedOptions[prID];
					if (freeControls >= usedControls) {
						if (priceType == 3) {
							freeLetters = controlText.length;
						} else if (priceType == 4) {
							freeLetters = controlText.replace(/[\n\r\t\s]/g, "").length;
						}
					}
				}
			}
			if (priceType == 1) {
				propertiesPrice += priceAmount;
			} else if (priceType == 2) {
				if (usedControls > freeControls) {
					propertiesPrice += (priceAmount * (usedControls - freeControls));
				}
			} else if (priceType == 3) {
				var textLength = controlText.length;
				if (textLength > freeLetters) {
					propertiesPrice += (priceAmount * (textLength - freeLetters));
				}
			} else if (priceType == 4) {
				var textLength = controlText.replace(/[\n\r\t\s]/g, "").length;
				if (textLength > freeLetters) {
					propertiesPrice += (priceAmount * (textLength - freeLetters));
				}
			}
			if (freePriceType == 1) {
				propertiesPrice -= freePriceAmount;
			}
		}
	}	
	return propertiesPrice;
}

function changeQuantity(itemForm)
{
	changeProperty(itemForm);
}

function properyImageUpload(uploadUrl)
{
	var uploadWin = window.open (uploadUrl, 'uploadWin', 'toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=400,height=300');
	uploadWin.focus();
}

function openPreviewWin(previewUrl, width, height)
{
	var previewWin = window.open (previewUrl, 'previewWin', 'left=0,top=0,toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=' + width + ',height=' + height);
	previewWin.focus();
	return false;
}

function openSuperImage(imageUrl, width, height)
{
	var scrollbars = "no";
	// add margins to image size
	if (width > 0 && height > 0) {
		width += 30; height += 30;
	}
	// check available sizes
	var availableHeight = window.screen.availHeight - 60;
	var availableWidth = window.screen.availWidth - 20;
	if (isNaN(availableHeight)) { availableHeight = 520; } 
	if (isNaN(availableWidth)) { availableWidth = 760; } 
	if (height > availableHeight || height == 0) { 
		height = availableHeight;
		scrollbars = "yes"; 
	}
	if (width > availableWidth || width == 0) {
		width = availableWidth;
		scrollbars = "yes";
	}
	var superImageWin = window.open (imageUrl, 'superImageWin', 'left=0,top=0,toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=' + scrollbars + ',resizable=yes,width=' + width + ',height=' + height);
	superImageWin.focus();
	return false;
}

function setFilePath(filepath, filetype, controlName, formId)
{
	if(filepath != "" && controlName != "" && formId != "")
	{
		var formName = "form_" + formId;
		document.forms[formName].elements[controlName].value = filepath;
		document.forms[formName].elements[controlName].focus();
	}
}

function getOptionPrice(itemForm, prValue)
{
	var optionPrice = 0;
	if (prValue != "") {
		if(itemForm.elements["option_price_" + prValue]) {
			optionPrice = parseFloat(itemForm.elements["option_price_" + prValue].value);
			if(isNaN(optionPrice)) {
				optionPrice = 0;
			}
		}
	}
	return optionPrice;
}

function formatNumber(numberValue, decimals, decimalPoint, thousandsSeparator)
{
	if (decimals == undefined) {
		decimals = 0;
	}
	if (thousandsSeparator == undefined) {
		thousandsSeparator = ",";
	}

	var numberParts = "";
	var roundValue = 1;
	for (var d = 0; d < decimals; d++) {
		roundValue *= 10;
	}
	numberValue = Math.round(numberValue * roundValue) / roundValue;
	var numberSign = "";
	if (numberValue < 0) {
		numberSign = "-";
		numberValue = Math.abs(numberValue);
	} 

	var numberText = new String(numberValue);
	var numberParts = numberText.split(".");
	var beforeDecimal = numberParts[0];
	var afterDecimal = "";
	numberText = "";
	if (numberParts.length == 2) {
		afterDecimal = numberParts[1];
	}
	while (beforeDecimal.length > 0) {
		if (beforeDecimal.length > 3) {
			numberText = thousandsSeparator + beforeDecimal.substring(beforeDecimal.length - 3, beforeDecimal.length) + numberText;
			beforeDecimal = beforeDecimal.substring(0, beforeDecimal.length - 3);
		} else {
			numberText = beforeDecimal + numberText;
			beforeDecimal = "";
		}
	}
	if (decimals > 0) {
		while (afterDecimal.length < decimals) {
			afterDecimal += "0";
		}
		if (decimalPoint == undefined) {
			decimalPoint = ".";
		}
		numberText += decimalPoint + afterDecimal;
	}
	numberText = numberSign + numberText;

	return numberText;
}

function getProductParams(itemForm)
{
	var params = new Array();
	var paramsList = itemForm.product_params.value; 
	var paramsPairs = paramsList.split("&");
	for (var p = 0; p < paramsPairs.length; p++) {
		var paramPair = paramsPairs[p];
		var equalPos = paramPair.indexOf("=");
		if(equalPos == -1) {
			params[paramPair] = "";
		} else {
			var paramName = paramPair.substring(0, equalPos);
			var paramValue = paramPair.substring(equalPos + 1, paramPair.length);
			paramValue = paramValue.replace(/%0D/g, "\r");
			paramValue = paramValue.replace(/%0A/g, "\n");
			paramValue = paramValue.replace(/%27/g, "'");
			paramValue = paramValue.replace(/%22/g, "\"");
			paramValue = paramValue.replace(/%26/g, "&");
			paramValue = paramValue.replace(/%2B/g, "+");
			paramValue = paramValue.replace(/%25/g, "%");
			paramValue = paramValue.replace(/%3D/g, "=");
			params[paramName] = paramValue;
		}
	}
	// check params values
	var checkParams = new Array();
	checkParams["base_price"] = 0;
	checkParams["crate"] = 1;
	checkParams["pe"] = 0;
	checkParams["zero_product_action"] = 1;
	checkParams["zero_price_type"] = 0;
	checkParams["show_prices"] = 1;
	checkParams["tax_prices_type"] = 0;
	checkParams["points_rate"] = 1;
	checkParams["points_decimals"] = 0;
	checkParams["points_decimals"] = 0;
	checkParams["comp_price"] = 0;
	checkParams["comp_tax"] = 0;
	checkParams["base_points_price"] = 0;
	checkParams["base_reward_points"] = 0;
	checkParams["base_reward_credits"] = 0;
	for (paramName in checkParams) {
		if (params[paramName]) {
			params[paramName] = parseFloat(params[paramName]);
			if (isNaN(params[paramName])) { params[paramName] = checkParams[checkParams]; }
		} else {
			params[paramName] = checkParams[checkParams];
		}
	}
	return params;
}

function checkMaxLength(e, obj, maxLength, limitType)
{
	var key;
	if (window.event) {
		key = window.event.keyCode; //IE
	} else {
		key = e.which; //Firefox
	}
	var objText = obj.value;
	var selectedText = "";
  if (obj.selectionEnd) {
    selectedText = objText.substring(obj.selectionStart, obj.selectionEnd);
  } else if (document.selection && document.selection.createRange) {
    selectedText = document.selection.createRange().text;
  } 
	if (limitType == 3 || limitType == 4) {
		selectedText = selectedText.replace(/[\n\r\t\s]/g, "");
	}
	if (selectedText.length > 0) {
		return true;
	}
	if (key == 0 || key == 8 || key == 9 || key == 16 || key == 17 || key == 35 || key == 36 || key == 37 || key == 39 || key == 46 || key == 116) {
		return true;
	}

	if (limitType == 3 || limitType == 4) {
		objText = objText.replace(/[\n\r\t\s]/g, "");
	}
  return (objText.length < maxLength);
}

function checkBoxesMaxLength(e, obj, itemForm, prID, maxLength, limitType)
{
	var key;
	if (window.event) {
		key = window.event.keyCode; //IE
	} else {
		key = e.which; //Firefox
	}

	var objText = obj.value;
	var selectedText = "";
	var selectedText = "";
  if (obj.selectionEnd) {
    selectedText = objText.substring(obj.selectionStart, obj.selectionEnd);
  } else if (document.selection && document.selection.createRange) {
    selectedText = document.selection.createRange().text;
  } 
	if (limitType == 3 || limitType == 4) {
		selectedText = selectedText.replace(/[\n\r\t\s]/g, "");
	}
	if (selectedText.length > 0) {
		return true;
	}

	if (key == 0 || key == 8 || key == 9 || key == 16 || key == 17 || key == 35 || key == 36 || key == 37 || key == 39 || key == 46 || key == 116) {
		return true;
	}

	var totalOptions = parseInt(itemForm.elements["property_total_" + prID].value);
	var totalLength = 0;
	for ( var ci = 1; ci <= totalOptions; ci++) {
		if (itemForm.elements["property_" + prID + "_" + ci].value != "") {
			var valueText = itemForm.elements["property_" + prID + "_" + ci].value;
			if (limitType == 3 || limitType == 4) {
				valueText = valueText.replace(/[\n\r\t\s]/g, "");
			}
			totalLength += valueText.length;
		}
	}
  return (totalLength < maxLength);
}
