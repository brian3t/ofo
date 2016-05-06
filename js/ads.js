
function calculatePostPrice()
{
	var price = 0;
	var fObj = document.record;
	var categoriesIds = "";
	var currentCategoriesIds = "";
	if (fObj.categories_ids) {
		categoriesIds = fObj.categories_ids.value;
		if (fObj.current_categories_ids) {
			currentCategoriesIds = fObj.current_categories_ids.value;
		}
	} else if (fObj.category_id.options) {
		categoriesIds = fObj.category_id.options[fObj.category_id.selectedIndex].value;
		if (fObj.current_category_id) {
			currentCategoriesIds = fObj.current_category_id.value;
		}
	}
	if (categoriesIds != currentCategoriesIds) {
		var ids = categoriesIds.split(",");
		for (var i = 0; i < ids.length; i++) {
			if (categories[ids[i]]) {
				var publishPrice = parseFloat(categories[ids[i]]);
				price += publishPrice;
			}
		}
	}
	var daysRun = ""; var currentDaysRun = "";
	var dateStart = ""; var currentDateStart = "";
	if (fObj.days_run) { daysRun = fObj.days_run.options[fObj.days_run.selectedIndex].value; }
	if (fObj.current_days_run) { currentDaysRun = fObj.current_days_run.value; }
	if (fObj.date_start) { dateStart = fObj.date_start.value; }
	if (fObj.current_date_start) { currentDateStart = fObj.current_date_start.value; }
	if (daysRun != "" && (daysRun != currentDaysRun || dateStart != currentDateStart)) {
		if (days[daysRun]) {
			var publishPrice = parseFloat(days[daysRun]);
			price += publishPrice;
		}
	}
	var hotDaysRun = ""; var currentHotDaysRun = "";
	var hotDateStart = ""; var currentHotDateStart = "";
	if (fObj.hot_days_run) { hotDaysRun = fObj.hot_days_run.options[fObj.hot_days_run.selectedIndex].value; }
	if (fObj.current_hot_days_run) { currentHotDaysRun = fObj.current_hot_days_run.value; }
	if (fObj.hot_date_start) { hotDateStart = fObj.hot_date_start.value; }
	if (fObj.current_hot_date_start) { currentHotDateStart = fObj.current_hot_date_start.value; }
	if (hotDaysRun != "" && (hotDaysRun != currentHotDaysRun || hotDateStart != currentHotDateStart)) {
		if (hotDays[hotDaysRun]) {
			var publishPrice = parseFloat(hotDays[hotDaysRun]);
			price += publishPrice;
		}
	}
	var specialDaysRun = ""; var currentSpecialDaysRun = "";
	var specialDateStart = ""; var currentSpecialDateStart = "";
	if (fObj.special_days_run) { specialDaysRun = fObj.special_days_run.options[fObj.special_days_run.selectedIndex].value; }
	if (fObj.current_special_days_run) { currentSpecialDaysRun = fObj.current_special_days_run.value; }
	if (fObj.special_date_start) { specialDateStart = fObj.special_date_start.value; }
	if (fObj.current_special_date_start) { currentSpecialDateStart = fObj.current_special_date_start.value; }
	if (specialDaysRun != "" && (specialDaysRun != currentSpecialDaysRun || specialDateStart != currentSpecialDateStart)) {
		if (specialDays[specialDaysRun]) {
			var publishPrice = parseFloat(specialDays[specialDaysRun]);
			price += publishPrice;
		}
	}

	var saveNoteButtons = new Array("saveNoteGeneral", "saveNoteAdDesc", "saveNoteLocation", "saveNoteImages", "saveNoteAdHot", "saveNoteAdSpecial");
	for (var b = 0; b < saveNoteButtons.length; b++) {
		var saveButtonId = saveNoteButtons[b];
		var saveNoteObj = document.getElementById(saveButtonId);
		if (saveNoteObj) {
			if (price > 0) {
				saveNoteObj.innerHTML = "&nbsp;" + publishPriceMsg + ": " + currencyFormat(price);
				saveNoteObj.style.display = "block";
			} else {
				saveNoteObj.innerHTML = "";
				saveNoteObj.style.display = "none";
			}
		}
	}

}

function currencyFormat(numberValue)
{
	var fObj = document.record;
	var currencyLeft = fObj.currency_left.value;
	var currencyRight = fObj.currency_right.value;
	var currencyRate = fObj.currency_rate.value;
	var currencyDecimals = fObj.currency_decimals.value;
	var currencyPoint = fObj.currency_point.value;
	var currencySeparator = fObj.currency_separator.value;
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
