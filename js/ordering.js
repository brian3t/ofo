// shopping javacript
function checkOrder(orderForm)
{
	var prMessage = "{REQUIRED_PROPERTY_MSG}";
	var prIDs = orderForm.properties.value;
	if (prIDs != "") {
		var properties = prIDs.split(",");
		for ( var i = 0; i < properties.length; i++) {
			var prID = properties[i];
			var cp = prID.split("_");
			var cartID = "";
			if (cp.length == 4) {
				cartID = cp[0] + "_" + cp[1] + "_" + cp[2];
			} else {
				cartID = cp[0];
			}
			if (orderForm.elements["property_required_" + prID] && orderForm.elements["property_required_" + prID].value == 1) {
				var productName = orderForm.elements["item_name_" + cartID].value;
				var prValue = "";
				var prControl = orderForm.elements["property_control_" + prID].value;
				if (prControl == "LISTBOX") {
					prValue = orderForm.elements["property_" + prID].options[orderForm.elements["property_" + prID].selectedIndex].value;
				} else if (prControl == "RADIOBUTTON") {
					var radioControl = orderForm.elements["property_" + prID];
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
				} else if (prControl == "CHECKBOXLIST") {
					if (orderForm.elements["property_total_" + prID]) {
						var totalOptions = parseInt(orderForm.elements["property_total_" + prID].value);
						for ( var ci = 1; ci <= totalOptions; ci++) {
							if (orderForm.elements["property_" + prID + "_" + ci].checked) {
								prValue = 1;
								break;
							}
						}
					}
				} else {
					prValue = orderForm.elements["property_" + prID].value;
				}
				if (prValue == "") {
					var propertyName = orderForm.elements["property_name_" + prID].value;
					prMessage = prMessage.replace("\{property_name\}", propertyName);
					prMessage = prMessage.replace("\{product_name\}", productName);
					alert(prMessage);
					if (prControl != "RADIOBUTTON" && prControl != "CHECKBOXLIST") {
						orderForm.elements["property_" + prID].focus();
					}
					return false;
				}
			}
		}
	}
	
	//**Egghead Ventures Add
	if (orderForm.delivery_state_id.selectedIndex == 0  && orderForm.delivery_country_id.selectedIndex == 2) {
		alert("Please Select A Delivery State");
		return false;
	}

	if (orderForm.shipping_type_id
		&& orderForm.shipping_type_id.length
		&& orderForm.shipping_type_id.length > 1) {
		var shippingError = "{REQUIRED_DELIVERY_MSG}";
		var shippingChecked = false;
		for(var i = 0; i < orderForm.shipping_type_id.length; i++) {
			shippingChecked = (shippingChecked || orderForm.shipping_type_id[i].checked);
		}
		if (orderForm.shipping_type_id.selectedIndex && orderForm.shipping_type_id.selectedIndex != 0) {
			shippingChecked = true;
		}
		if (!shippingChecked)
		{
			alert(shippingError);
			if (orderForm.shipping_type_id.options) {
				// shipping control is listbox
				orderForm.shipping_type_id.focus();
			} else {
				// shipping control is radio button
				orderForm.shipping_type_id[0].focus();
			}
			return false;
		}
	}

	orderForm.operation.value = 'save';
	return true;
}


function changeProperty()
{
	calculateOrder();
}

function changeShipping()
{
	calculateOrder();
}

function changeShippingList()
{
	calculateOrder();
}

function calculateItems()
{
	calculateOrder();
}

function calculateOrder()
{
	var orderForm = document.order_info;
	// initiliaze variables with shop settings
	var pricesType = parseFloat(orderForm.tax_prices_type.value);
	if (isNaN(pricesType)) { pricesType = 0; }
	var pointsRate = parseFloat(orderForm.points_rate.value);
	if (isNaN(pointsRate)) { pointsRate = 1; }
	var priceObj = "";

	// initialize array for total values
	var totalValues = new Array();

	// get tax rates 
	var taxRates = prepareData("tax_rates", "tax_id=");

	// calculate order goods
	var goodsTotal = 0; var goodsPoints = 0;

	// get all order items 
	var orderItems = prepareData("order_items", "cart_item_id=");
	if (orderItems instanceof Array) {
		for (cartId in orderItems) {
			var orderItem = orderItems[cartId];
			var subcomponentsShowType = orderItem["subcomponents_show_type"];
			var parentCartId = orderItem["parent_cart_id"];
			var quantity = orderItem["quantity"];
			// check pay points variable
			var payPoints = 0;
			if (subcomponentsShowType == 1 && parentCartId != "") {
				if (orderForm.elements["pay_points_" + parentCartId] && orderForm.elements["pay_points_" + parentCartId].checked) {
					payPoints = 1;
				}
			} else if (orderForm.elements["pay_points_" + cartId] && orderForm.elements["pay_points_" + cartId].checked) {
				payPoints = 1;
			}
			if (payPoints != 1) {
				var price = orderItem["price"];
				var itemTypeId = orderItem["item_type_id"];
				var taxFreeOption = orderItem["tax_free"];

				var priceTotal = Math.round(price * quantity * 100) / 100;
				var itemTaxes = getTaxAmount(taxRates, itemTypeId, priceTotal, taxFreeOption, 2) 
				var priceTax = getTaxAmount(taxRates, itemTypeId, priceTotal, taxFreeOption, 1) 
				taxRates = addTaxValues(taxRates, itemTaxes, "goods");

				goodsTotal += priceTotal;
			} else {
				var pointsPrice = orderItem["points_price"];
				goodsPoints += (pointsPrice * quantity);
			}
		}
		// check total values
		totalValues = calculateTotals(totalValues, goodsTotal, taxRates, "goods")
		var goodsTotalControl = document.getElementById("goods_total_excl_tax");
		if (goodsTotalControl) {
			goodsTotalControl.innerHTML = currencyFormat(totalValues["goods_excl_tax"]);
		}
		var goodsTaxControl = document.getElementById("goods_tax_total");
		if (goodsTaxControl) {
			goodsTaxControl .innerHTML = currencyFormat(totalValues["goods_tax"]);
		}
		var goodsTotalInclTaxControl = document.getElementById("goods_total_incl_tax");
		if (goodsTotalInclTaxControl) {
			goodsTotalInclTaxControl.innerHTML = currencyFormat(totalValues["goods_incl_tax"]);
		}
	}
	// end of order goods calculations

	// calculate order properties
	var totalPropertiesPrice = 0; var totalPropertiesPoints = 0; var orderProperties = "";
	if (orderForm.order_properties) { orderProperties = orderForm.order_properties.value; }
	if (orderProperties != "") {

		var properties = orderProperties.split(",");
		for ( var i = 0; i < properties.length; i++) {
			var prID = properties[i];
			var prValue = "";
			var propertyPrice = 0;
			var prPayPoints = 0;
			if (orderForm.elements["property_pay_points_" + prID] && orderForm.elements["property_pay_points_" + prID].checked) {
				prPayPoints = 1;
			}
			var prControl = orderForm.elements["op_control_" + prID].value;
			var taxFreeOption = parseInt(orderForm.elements["op_tax_free_" + prID].value);
			if (prControl == "LISTBOX") {
				prValue = orderForm.elements["op_" + prID].options[orderForm.elements["op_" + prID].selectedIndex].value;
			} else if (prControl == "RADIOBUTTON") {
				var radioControl = orderForm.elements["op_" + prID];
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
			} else if (prControl == "CHECKBOXLIST") {
				if (orderForm.elements["op_total_" + prID]) {
					var totalOptions = parseInt(orderForm.elements["op_total_" + prID].value);
					for ( var ci = 1; ci <= totalOptions; ci++) {
						if (orderForm.elements["op_" + prID + "_" + ci].checked) {
							var checkedValue = orderForm.elements["op_" + prID + "_" + ci].value;
							if (orderForm.elements["op_option_price_" + checkedValue]) {
								var checkedPrice = parseFloat(orderForm.elements["op_option_price_" + checkedValue].value);
								if (!isNaN(checkedPrice) && checkedPrice!= 0) {
									propertyPrice += parseFloat(checkedPrice);
								}
							}
						}
					}
				}
			}
			if (prValue != "") {
				if (orderForm.elements["op_option_price_" + prValue]) {
					var optionPrice = orderForm.elements["op_option_price_" + prValue].value;
					if (optionPrice != "") {
						propertyPrice = parseFloat(optionPrice);
						if (prPayPoints == 1) {
							if (propertyPrice > 0) {
								totalPropertiesPoints += (propertyPrice * pointsRate);
							}
							propertyPrice = 0;
						}
					}
				}
			}
			
			if (isNaN(propertyPrice)) { propertyPrice = 0; }
			var propertiesTaxes = getTaxAmount(taxRates, "properties", propertyPrice, taxFreeOption, 2) 
			var propertyTax = getTaxAmount(taxRates, "properties", propertyPrice, taxFreeOption, 1) 
			taxRates = addTaxValues(taxRates, propertiesTaxes, "properties");
			totalPropertiesPrice += propertyPrice;
			var propertyPrices = calculatePrices(propertyPrice, propertyTax);

			var priceControl = document.getElementById("op_price_excl_tax_" + prID);
			if (priceControl) {
				if (propertyPrice == 0) {
					priceControl.innerHTML = "";
				} else {
					priceControl.innerHTML = currencyFormat(propertyPrices["excl_tax"]);
				}
			}
			var taxControl = document.getElementById("op_tax_" + prID);
			if (taxControl) {
				if (propertyPrice == 0) {
					taxControl.innerHTML = "";
				} else {
					taxControl.innerHTML = currencyFormat(propertyTax);
				}
			}
			var priceInclTaxControl = document.getElementById("op_price_incl_tax_" + prID);
			if (priceInclTaxControl) {
				if (propertyPrice == 0) {
					priceInclTaxControl.innerHTML = "";
				} else {
					priceInclTaxControl.innerHTML = currencyFormat(propertyPrices["incl_tax"]);
				}
			}
		}
		// check total values
		totalValues = calculateTotals(totalValues, totalPropertiesPrice, taxRates, "properties")
	}
	// end of properties calculations

	// calculate shipping
	var shippingCost = 0;
	var shippingTaxable = 1;
	var shippingPayPoints = 0;
	var shippingPoints = 0;
	if (orderForm.shipping_pay_points && orderForm.shipping_pay_points.checked) {
		shippingPayPoints = 1;
	}
	var shippingTypeId  = ""; var shippingObjName = ""; var shippingObj = "";
	var shippingControl = orderForm.shipping_control.value;
	var shippingMethods = prepareData("shipping_methods", "shipping_id=");

	if (shippingControl == "HIDDEN") {
		shippingTypeId = orderForm.shipping_type_id.value;
	} else if (shippingControl == "RADIO") {
		for(var i = 0; i < orderForm.shipping_type_id.length; i++) {
			var radioShippingId = orderForm.shipping_type_id[i].value;
			if (orderForm.shipping_type_id[i].checked) {
				shippingTypeId = radioShippingId;
				shippingObjName = radioShippingId;
			} else {
				// hide shipping values for non-checked methods
				shippingObj = document.getElementById("shipping_cost_excl_tax_" + radioShippingId);
				if (shippingObj) { shippingObj.innerHTML = ""; }
				shippingObj = document.getElementById("shipping_tax_" + radioShippingId);
				if (shippingObj) { shippingObj.innerHTML = ""; }
				shippingObj= document.getElementById("shipping_cost_incl_tax_" + radioShippingId);
				if (shippingObj) { shippingObj.innerHTML = ""; }
			}
		}
	} else if (shippingControl == "LISTBOX") {
		shippingTypeId = orderForm.shipping_type_id.options[orderForm.shipping_type_id.selectedIndex].value;
		shippingObjName = "selected";
	}
	if (shippingObjName != "") {
		if (shippingTypeId == "") {
			// hide shipping values
			shippingObj = document.getElementById("shipping_cost_excl_tax_" + shippingObjName);
			if (shippingObj) { shippingObj.innerHTML = ""; }
			shippingObj = document.getElementById("shipping_tax_" + shippingObjName);
			if (shippingObj) { shippingObj.innerHTML = ""; }
			shippingObj= document.getElementById("shipping_cost_incl_tax_" + shippingObjName);
			if (shippingObj) { shippingObj.innerHTML = ""; }
		} else {
			// show shipping values
			shippingCost = parseFloat(shippingMethods[shippingTypeId]["cost"]);
			if (shippingPayPoints == 1) {
				shippingPoints = shippingCost * pointsRate;
				shippingCost = 0;
			}
			shippingTaxable = parseInt(shippingMethods[shippingTypeId]["taxable"]);
			var shippingTaxFree = (shippingTaxable == 1) ? 0 : 1;
			var shippingTaxes = getTaxAmount(taxRates, "shipping", shippingCost, shippingTaxFree, 2) 
			var shippingTax = getTaxAmount(taxRates, "shipping", shippingCost, shippingTaxFree, 1) 
			taxRates = addTaxValues(taxRates, shippingTaxes, "shipping");
			var shippingPrices = calculatePrices(shippingCost, shippingTax);

			shippingObj = document.getElementById("shipping_cost_excl_tax_" + shippingObjName);
			if (shippingObj) {
				shippingObj.innerHTML = currencyFormat(shippingPrices["excl_tax"]);
			}
			shippingObj = document.getElementById("shipping_tax_" + shippingObjName);
			if (shippingObj) {
				shippingObj.innerHTML = currencyFormat(shippingTax);
			}
			shippingObj = document.getElementById("shipping_cost_incl_tax_" + shippingObjName);
			if (shippingObj) {
				shippingObj.innerHTML = currencyFormat(shippingPrices["incl_tax"]);
			}
		}
	}
	// calculate total values
	totalValues = calculateTotals(totalValues, shippingCost, taxRates, "shipping")
	// end shipping calculations

	// calculate discounts
	var maxDiscount = goodsTotal; var totalDiscount = 0; var totalDiscountTax = 0;
	var coupons = prepareData("order_coupons", "coupon_id=");
	if (coupons instanceof Array) {
		for (var couponId in coupons) {
			var coupon = coupons[couponId];
			var couponType = coupon["type"];
			var couponAmount = coupon["amount"];
			var couponTaxFree = coupon["tax_free"];
			var discountAmount = 0;
			var discountTax = 0;
			if (couponType == 1) {
				discountAmount = Math.round(goodsTotal * couponAmount) / 100;
			} else {
				discountAmount = couponAmount;
			}
			if (discountAmount > maxDiscount) {
				discountAmount = maxDiscount;
			}
			maxDiscount -= discountAmount;
			var discountTaxes = getDiscountTaxes(taxRates, totalValues, discountAmount, couponTaxFree, 2)
			var discountTax = getDiscountTaxes(taxRates, totalValues, discountAmount, couponTaxFree, 1)
			taxRates = addTaxValues(taxRates, discountTaxes, "discount");
			var discountPrices = calculatePrices(discountAmount, discountTax);
			
			totalDiscount += discountAmount;
			totalDiscountTax += discountTax;
  
			priceObj = document.getElementById("coupon_amount_excl_tax_" + couponId);
			if (priceObj) {
				priceObj.innerHTML = "- " + currencyFormat(discountPrices["excl_tax"]);
			}
			priceObj = document.getElementById("coupon_tax_" + couponId);
			if (priceObj) {
				priceObj.innerHTML = "- " + currencyFormat(discountTax);
			}
			priceObj = document.getElementById("coupon_amount_incl_tax_" + couponId);
			if (priceObj) {
				priceObj.innerHTML = "- " + currencyFormat(discountPrices["incl_tax"]);
			}
		}
		// show discount and goods cost after discount total values
		totalValues = calculateTotals(totalValues, discountAmount, taxRates, "discount")

		priceObj = document.getElementById("total_discount_excl_tax");
		if (priceObj) {
			priceObj.innerHTML = "- " + currencyFormat(totalValues["discount_excl_tax"]);
		}
		priceObj = document.getElementById("total_discount_tax");
		if (priceObj) {
			priceObj.innerHTML = "- " + currencyFormat(totalValues["discount_tax"]);
		}
		priceObj = document.getElementById("total_discount_incl_tax");
		if (priceObj) {
			priceObj.innerHTML = "- " + currencyFormat(totalValues["discount_incl_tax"]);
		}

		priceObj = document.getElementById("discounted_amount_excl_tax");
		if (priceObj) {
			priceObj.innerHTML = currencyFormat(totalValues["goods_excl_tax"] - totalValues["discount_excl_tax"]);
		}
		priceObj = document.getElementById("discounted_tax_amount");
		if (priceObj) {
			priceObj.innerHTML = currencyFormat(totalValues["goods_tax"] - totalValues["discount_tax"]);
		}
		priceObj = document.getElementById("discounted_amount_incl_tax");
		if (priceObj) {
			priceObj.innerHTML = currencyFormat(totalValues["goods_incl_tax"] - totalValues["discount_incl_tax"]);
		}
		
	}

	// calculate and show taxes
	var taxesTotal = 0;
	for (var taxId in taxRates) {
		var taxObj = document.getElementById("tax_" + taxId);
		var taxTotal = 0;
		if (taxRates[taxId]["tax_total"]) {
			taxTotal = Math.round(taxRates[taxId]["tax_total"] * 100) / 100;
		}
		taxesTotal += taxTotal;
		if (taxObj) {
			taxObj.innerHTML = currencyFormat(taxTotal);
		}
	}

	//var goodsTotal = parseFloat(orderForm.goods_value.value); todo delete

	// calculate order total
	var orderTotal = goodsTotal - totalDiscount + totalPropertiesPrice + shippingCost;
	if (pricesType != 1) {
		orderTotal += taxesTotal;
	}

	// calculate gift vouchers
	var vouchers = prepareData("order_vouchers", "voucher_id=");
	if (vouchers instanceof Array) {
		for (var voucherId in vouchers) {
			var voucher = vouchers[voucherId];
			var voucherTitle = voucher["title"];
			var voucherMaxAmount = voucher["max_amount"];
			var voucherAmount = voucherMaxAmount;
			if (voucherAmount > orderTotal) {
				voucherAmount = orderTotal;
			}
			orderTotal -= voucherAmount;
			priceObj = document.getElementById("voucher_amount_" + voucherId);
			if (priceObj) {
				if (voucherAmount > 0) {
					priceObj.innerHTML = "- " + currencyFormat(voucherAmount);
				} else {
					priceObj.innerHTML = "";
				}
			}
		}
	}
	// calculate processing fee
	var processingFees = ""; var processingFee = 0;
	if (orderForm.processing_fees && orderForm.processing_fee) {
		processingFees = orderForm.processing_fees.value;
		var feeType = 0;
		var feeValue = 0;
		if (processingFees != "") {
			var feesValues = processingFees.split(",");
			if (feesValues.length == 3) {
				feeType = parseInt(feesValues[1]);
				feeValue = parseFloat(feesValues[2]);
			} else if (feesValues.length > 3) {
				var paymentId = "";
				if (orderForm.payment_id)	{
					if (orderForm.payment_id.options) {
						paymentId = orderForm.payment_id.options[orderForm.payment_id.selectedIndex].value;
					} else if (orderForm.payment_id.length > 0) {
						for (var i = 0; i < orderForm.payment_id.length; i++) {
								if (orderForm.payment_id[i].checked) {
									paymentId = orderForm.payment_id[i].value;
									break;
								}
						}
					}
				}
				for (var f = 0; f < feesValues.length; f = f + 3) {
					feePayment = feesValues[f];
					if (paymentId == feePayment) {
						feeType = parseInt(feesValues[f + 1]);
						feeValue = parseFloat(feesValues[f + 2]);
						break;
					}
				}
			}
		}

		if (feeType == 1) {
			processingFee = Math.round(orderTotal * feeValue) / 100;
		} else {
			processingFee = feeValue;
		}
		orderForm.processing_fee.value = currencyFormat(processingFee);
	}
	orderTotal += processingFee;

	var orderTotalControl = document.getElementById("order_total_desc");
	if (orderTotalControl) {
		orderTotalControl.innerHTML = currencyFormat(orderTotal);
	}

	// calculate points if available
	var pointsBalance = parseFloat(orderForm.points_balance_value.value);
	var pointsDecimals = 0;
	if (orderForm.points_decimals && orderForm.points_decimals.value != "") {
		pointsDecimals = parseFloat(orderForm.points_decimals.value);
		if (isNaN(pointsDecimals)) { pointsDecimals = 0; }
	}

	var orderTotalPoints = goodsPoints + totalPropertiesPoints + shippingPoints;
	var totalPointsControl = document.getElementById("total_points_amount");
	if (totalPointsControl) {
		totalPointsControl.innerHTML = formatNumber(orderTotalPoints, pointsDecimals);
	}
	var remainingPointsControl = document.getElementById("remaining_points");
	if (remainingPointsControl) {
		remainingPointsControl.innerHTML = formatNumber(pointsBalance - orderTotalPoints, pointsDecimals);
	}

}

function getDiscountTaxes(taxRates, totalValues, discountAmount, taxFreeOption, returnType)
{
	var goodsTotal = totalValues["goods_total"];
	var taxAmount = 0;
	var taxesValues = new Array();
	if (taxFreeOption != 1) {
		if (taxRates instanceof Array) {
			for (taxId in taxRates) {
				var goodsTax = taxRates[taxId]["goods"];
				var discountTax = Math.round((discountAmount * goodsTax * 100) / goodsTotal) / 100;
				taxesValues[taxId] = new Array();
				taxesValues[taxId]["tax_amount"] = discountTax;
				taxesValues[taxId]["price_amount"] = discountAmount;
				taxAmount += discountTax;
			}
		}
	}

	if (returnType == 2) {
		return taxesValues;
	} else {
		return taxAmount;
	}
}

function getTaxAmount(taxRates, taxType, amount, taxFreeOption, returnType) 
{
	var taxRound = 1;
	if (document.order_info.tax_round) {
		taxRound = parseInt(document.order_info.tax_round.value);
		if (isNaN(taxRound)) { taxRound = 1; }
	}

	var taxAmount = 0;
	var taxesValues = new Array();
	var pricesType = parseFloat(document.order_info.tax_prices_type.value);
	if (isNaN(pricesType)) { pricesType = 0; }

	var taxPercent = 0;
	if (taxFreeOption != 1) {
		// calculate summary tax
		if (taxRates instanceof Array) {
			for (taxId in taxRates) {
				var taxRate = taxRates[taxId];
				var currentTaxPercent = 0; var currentTaxAmount = 0;
				if (taxRate["types"] && taxRate["types"][taxType] && taxRate["types"][taxType] != "") {
					currentTaxPercent = parseFloat(taxRate["types"][taxType]);
				} else {
					if (taxType == "shipping" && taxRate["shipping_tax_percent"] && taxRate["shipping_tax_percent"] != "") {
						currentTaxPercent = parseFloat(taxRate["shipping_tax_percent"]);
					} else {
						currentTaxPercent = parseFloat(taxRate["tax_percent"]);
					}
				}
				// calculate tax amount for each tax
				if (pricesType == 1) { // prices includes tax
					currenTaxAmount = (Math.round(amount * 100) - Math.round(amount * 10000 / ( 100 + currentTaxPercent))) / 100; 
				} else {
					currenTaxAmount = Math.round(amount * currentTaxPercent) / 100;
				}
				if (taxRound == 1) {
					currenTaxAmount = Math.round(currenTaxAmount * 100) / 100;
				}
				taxesValues[taxId] = new Array();
				taxesValues[taxId]["tax_percent"] = currentTaxPercent;
				taxesValues[taxId]["tax_amount"] = currenTaxAmount;
				taxesValues[taxId]["price_amount"] = amount;
				taxPercent += currentTaxPercent;
				taxAmount += currenTaxAmount;
			}
		}
	} else {
		taxPercent = 0;
	}

	if (returnType == 2) {
		return taxesValues;
	} else {
		return taxAmount;
	}
}

function addTaxValues(taxRates, taxValues, amountType)
{
	var taxRound = 1;
	if (document.order_info.tax_round) {
		taxRound = parseInt(document.order_info.tax_round.value);
		if (isNaN(taxRound)) { taxRound = 1; }
	}

	if (taxValues instanceof Array) {
		for (taxId in taxValues) {
			var taxInfo = taxValues[taxId];
			var taxAmount = parseFloat(taxInfo["tax_amount"]);
			if (taxRound == 1) {
				taxAmount = Math.round(taxAmount * 100) / 100;
			}
			if (!taxRates[taxId][amountType]) {
				taxRates[taxId][amountType] = 0;
			}
			if (!taxRates[taxId]["tax_total"]) {
				taxRates[taxId]["tax_total"] = 0;
			}
			taxRates[taxId][amountType] += taxAmount;
			if (amountType == "discount") {
				taxRates[taxId]["tax_total"] -= taxAmount;
			} else {
				taxRates[taxId]["tax_total"] += taxAmount;
			}
		}
	}
	return taxRates;
}


function calculateTotals(totalValues, totalAmount, taxRates, amountType)
{
	var pricesType = parseFloat(document.order_info.tax_prices_type.value);
	if (isNaN(pricesType)) { pricesType = 0; }

	totalValues[amountType+"_total"] = totalAmount;
	totalValues[amountType+"_excl_tax"] = 0;
	totalValues[amountType+"_tax"] = 0;
	totalValues[amountType+"_incl_tax"] = 0;
	for (taxId in taxRates) {
		if (taxRates[taxId][amountType]) {
			totalValues[amountType+"_tax"] += taxRates[taxId][amountType];
		}
	}
	if (pricesType == 1) {
		totalValues[amountType+"_excl_tax"] += (totalAmount - totalValues[amountType+"_tax"]);
		totalValues[amountType+"_incl_tax"] += totalAmount;
	} else {
		totalValues[amountType+"_excl_tax"] += totalAmount;
		totalValues[amountType+"_incl_tax"] += 1 * totalAmount + totalValues[amountType+"_tax"];
	}
	//alert(totalValues[amountType+"_excl_tax"] + ", " + totalValues[amountType+"_tax"] + ", " + totalValues[amountType+"_incl_tax"])

	return totalValues;
}

function calculatePrices(amount, tax)
{
	var prices = new Array();
	var pricesType = parseFloat(document.order_info.tax_prices_type.value);
	if (isNaN(pricesType)) { pricesType = 0; }

	prices["base"] = amount;
	prices["tax"] = tax;
	if (pricesType == 1) {                           
		prices["excl_tax"] = (amount - tax);
		prices["incl_tax"] = amount;
	} else {
		prices["excl_tax"] = amount;
		prices["incl_tax"] = 1 * amount + tax;
	}

	return prices;
}


function totalTaxValue(taxValues)
{
	var taxRound = 1;
	if (document.order_info.tax_round) {
		taxRound = parseInt(document.order_info.tax_round.value);
		if (isNaN(taxRound)) { taxRound = 1; }
	}

	var totalTax = 0;
	if (taxValues instanceof Array) {
		for (taxId in taxValues) {
			var taxInfo = taxValues[taxId];
			var taxAmount = parseFloat(taxInfo["tax_amount"]);
			if (taxRound == 1) {
				taxAmount = Math.round(taxAmount * 100) / 100;
			}
			totalTax += taxAmount;
		}
	}
	return totalTax;
}


function getTaxAmountOld(amount, taxPercent, taxFree, pricesType) 
{
	var taxAmount = 0;
	if (taxFree != 1) {
		if (pricesType == 1) {
			taxAmount = (Math.round(amount * 100) - Math.round(amount * 10000 / ( 100 + taxPercent))) / 100; 
		} else {
			taxAmount = Math.round(amount * taxPercent) / 100;
		}
	}
	return taxAmount;
}

function currencyFormat(numberValue)
{
	var orderForm = document.order_info;
	var currencyLeft = orderForm.currency_left.value;
	var currencyRight = orderForm.currency_right.value;
	var currencyRate = orderForm.currency_rate.value;
	var currencyDecimals = orderForm.currency_decimals.value;
	var currencyPoint = orderForm.currency_point.value;
	var currencySeparator = orderForm.currency_separator.value;
	return currencyLeft + formatNumber(numberValue * currencyRate, currencyDecimals, currencyPoint, currencySeparator) + currencyRight;
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

function changeCountry(orderForm, controlType)
{
	var refreshPage = true;
	if (controlType == 'personal') {
		if (orderForm.delivery_country_id || orderForm.delivery_country_id) { refreshPage = false; }
	}
	if (refreshPage) {
		orderForm.operation.value = "refresh";
		orderForm.submit();
	}
}

function changeState(orderForm, controlType)
{
	var refreshPage = true;
	if (controlType == 'personal') {
		if (orderForm.delivery_state_id || orderForm.delivery_country_id || orderForm.delivery_state_id || orderForm.delivery_country_id) {
			refreshPage = false;
		} else if (orderForm.country_id) {
			if (orderForm.country_id.selectedIndex == 0) { refreshPage = false; }
		} else if (orderForm.country_id) {
			if (orderForm.country_id.selectedIndex == 0) { refreshPage = false; }
		}
	} else if (orderForm.delivery_country_id) {
		if (orderForm.delivery_country_id.selectedIndex == 0) { refreshPage = false; }
	} else if (orderForm.delivery_country_id) {
		if (orderForm.delivery_country_id.selectedIndex == 0) { refreshPage = false; }
	}
	if (refreshPage) {
		orderForm.operation.value = "refresh";
		orderForm.submit();
	}
}

function changeZip(orderForm, controlType)
{
	var refreshPage = true;
	if (controlType == 'personal') {
		if (orderForm.delivery_zip || orderForm.delivery_country_id || orderForm.delivery_country_id) {
			refreshPage = false;
		} else if (orderForm.country_id) {
			if (orderForm.country_id.selectedIndex == 0) { refreshPage = false; }
		} else if (orderForm.country_id) {
			if (orderForm.country_id.selectedIndex == 0) { refreshPage = false; }
		}
		
	} else if (orderForm.delivery_country_id) {
		if (orderForm.delivery_country_id.selectedIndex == 0) { refreshPage = false; }
	} else if (orderForm.delivery_country_id) {
		if (orderForm.delivery_country_id.selectedIndex == 0) { refreshPage = false; }
	}
	if (refreshPage) {
		orderForm.operation.value = "refresh";
		orderForm.submit();
	}
}

function checkSame()
{
	var refreshPage = false;
	var orderForm = document.order_info;
	var sameChecked = document.order_info.same_as_personal.checked;
	if (sameChecked) {
		var fieldName = "";
		var fields = new Array("name", "first_name", "last_name", "company_id", "company_name", "email",
			"address1", "address2", "city", "province", "address1",
			"phone", "daytime_phone", "evening_phone", "cell_phone", "fax");
		for (var i = 0; i < fields.length; i++) {
			fieldName = fields[i];
			if (orderForm.elements[fieldName] && orderForm.elements["delivery_" + fieldName]) {
				orderForm.elements["delivery_" + fieldName].value = orderForm.elements[fieldName].value;
			}
		}
		if (orderForm.country_id && orderForm.delivery_country_id) {
			if (orderForm.country_id.selectedIndex != orderForm.delivery_country_id.selectedIndex) {
				orderForm.delivery_country_id.selectedIndex = orderForm.country_id.selectedIndex;
				refreshPage = true;
			}
		}
		if (orderForm.country_id && orderForm.delivery_country_id) {
			if (orderForm.country_id.selectedIndex != orderForm.delivery_country_id.selectedIndex) {
				orderForm.delivery_country_id.selectedIndex = orderForm.country_id.selectedIndex;
				refreshPage = true;
			}
		}
		if (orderForm.state_id && orderForm.delivery_state_id) {
			if (orderForm.state_id.selectedIndex != orderForm.delivery_state_id.selectedIndex) {
				orderForm.delivery_state_id.selectedIndex = orderForm.state_id.selectedIndex;
				refreshPage = true;
			}
		}
		if (orderForm.state_id && orderForm.delivery_state_id) {
			if (orderForm.state_id.selectedIndex != orderForm.delivery_state_id.selectedIndex) {
				orderForm.delivery_state_id.selectedIndex = orderForm.state_id.selectedIndex;
				refreshPage = true;
			}
		}
		if (orderForm.zip && orderForm.delivery_zip) {
			if (orderForm.zip.value != orderForm.delivery_zip.value) {
				orderForm.delivery_zip.value = orderForm.zip.value;
				refreshPage = true;
			}
		}
	}
	if (refreshPage) {
		orderForm.operation.value = "refresh";
		orderForm.submit();
	}
}

function uncheckSame()
{
	if (document.order_info.same_as_personal) {
		document.order_info.same_as_personal.checked = false;
	}
}

function checkMaxLength(obj, maxLength)
{
  return (obj.value.length < maxLength);
}

function checkBoxesMaxLength(e, itemForm, cpID, maxLength)
{
	var key;
	if (window.event) {
		key = window.event.keyCode; //IE
	} else {
		key = e.which; //Firefox
	}

	if (key == 8 || key == 9 || key == 16 || key == 17 || key == 35 || key == 36 || key == 37 || key == 39 || key == 46 || key == 116) {
		return true;
	}

	var totalOptions = parseInt(itemForm.elements["property_total_" + cpID].value);
	var totalLength = 0;
	for ( var ci = 1; ci <= totalOptions; ci++) {
		if (itemForm.elements["property_" + cpID + "_" + ci].value != "") {
			var valueText = itemForm.elements["property_" + cpID + "_" + ci].value;
			totalLength += valueText.length;
		}
	}
  return (totalLength < maxLength);
}

function prepareData(dataName, dataDelimiter)
{
	var data = new Array();
	var dataValue = document.order_info.elements[dataName].value;
	if (dataValue != "") {
		var records = dataValue.split(dataDelimiter);
		for (var t = 0; t < records.length; t++) {
			var record = records[t];
			var ampPos = record.indexOf("&");
			if (ampPos != -1) {
				var dataId = record.substring(0, ampPos);
				var recordValue = record.substring(ampPos+1, record.length);
				data[dataId] = new Array();
				// get record parameters
				var paramsPairs = recordValue.split("&");
				for (var p = 0; p < paramsPairs.length; p++) {
					var paramPair = paramsPairs[p];
					var equalPos = paramPair.indexOf("=");
					if (equalPos != -1) {
						var paramName = paramPair.substring(0, equalPos);
						var paramValue = paramPair.substring(equalPos + 1, paramPair.length);
						if (paramName.substring(0, 13) == "item_type_id_") { // special condition for taxes
							var itemTypeId = paramName.substring(13, paramName.length);
							if(!data[dataId]["types"]) {
								data[dataId]["types"] = new Array();
							}
							data[dataId]["types"][itemTypeId] = decodeParamValue(paramValue);
						} else {
							data[dataId][paramName] = decodeParamValue(paramValue);
						}
					}
				} // end of record parameters cycle
			}
		}
	}
	return data;
}

function decodeParamValue(paramValue)
{
	paramValue = paramValue.replace(/%0D/g, "\r");
	paramValue = paramValue.replace(/%0A/g, "\n");
	paramValue = paramValue.replace(/%27/g, "'");
	paramValue = paramValue.replace(/%22/g, "\"");
	paramValue = paramValue.replace(/%26/g, "&");
	paramValue = paramValue.replace(/%2B/g, "+");
	paramValue = paramValue.replace(/%25/g, "%");
	paramValue = paramValue.replace(/%3D/g, "=");
	return paramValue;
}
