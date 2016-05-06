// javacript for editing product

	function countSalesPrice()
	{
		var price = document.record.price.value;
		var discount = document.record.discount_percent.value;
		var sales_price = 0;
		if (!isNaN(price) && !isNaN(discount) && price != "" && discount != "") {
			sales_price = Math.round(price * 100 - (price * discount)) / 100;
		}
		document.record.real_sales_price.value = "(!) Actual 'Our Price' is " + sales_price;						 
	}

	function countDiscountPercent()
	{
		var price = document.record.price.value;
		var sales_price = document.record.sales_price.value;
		var discount = "";
		if(!isNaN(price) && !isNaN(sales_price) && price != "" && sales_price != "")
			discount = 100 - (sales_price * 100) / price;
		document.record.real_discount_percent.value = "(!) Actual Discount is " + discount + "%";
	}

	function openWindow(pagename, filetype, controlName)
	{
		var winUrl = pagename + '?filetype=' + filetype;
		if (controlName) {
			winUrl += '&image_index='+controlName;
			winUrl += '&control_name='+controlName;
		}
		var uploadWin = window.open(winUrl, 'uploadWindow', 'toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=540,height=400');
		uploadWin.focus();
	}

	function openTermsWindow(pagename)
	{
		var termsWin = window.open(pagename, 'termsWindow', 'toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=500,height=400');
		termsWin.focus();
	}

	function setFileName(filename, filetype, fileIndex, siteUrl)
	{ 	
		if (siteUrl == null) {
			siteUrl = "";
		}
		if(filename != "")
		{
			if(filetype == "tiny_image") {
				document.record.tiny_image.value = siteUrl + "images/tiny/" + filename;
				document.record.tiny_image.focus();
			} else if(filetype == "small_image") {
				document.record.small_image.value = siteUrl + "images/small/" + filename;
				document.record.small_image.focus();
			} else if(filetype == "big_image") {
				document.record.big_image.value = siteUrl + "images/big/" + filename;
				document.record.big_image.focus();
			} else if(filetype == "super_image") {
				document.record.super_image.value = siteUrl + "images/super/" + filename;
				document.record.super_image.focus();
			} else if(filetype == "product_tiny") {
				document.record.tiny_image.value = siteUrl + "images/products/tiny/" + filename;
				document.record.tiny_image.focus();
			} else if(filetype == "product_small") {
				document.record.small_image.value = siteUrl + "images/products/small/" + filename;
				document.record.small_image.focus();
			} else if(filetype == "product_large") {
				document.record.big_image.value = siteUrl + "images/products/large/" + filename;
				document.record.big_image.focus();
			} else if(filetype == "product_super") {
				document.record.super_image.value = siteUrl + "images/products/super/" + filename;
				document.record.super_image.focus();
			} else if(filetype == "payment_small") {
				document.record.small_image.value = siteUrl + "images/payments/small/" + filename;
				document.record.small_image.focus();
			} else if(filetype == "payment_large") {
				document.record.big_image.value = siteUrl + "images/payments/large/" + filename;
				document.record.big_image.focus();
			} else if (filetype == "downloads") {
				var pathObj = document.record.elements["download_path_"+fileIndex];
				if (pathObj) {
					pathObj.value = filename;
					pathObj.focus();
				}
			} else if (filetype == "previews") {
				var pathObj = document.record.elements["preview_path_"+fileIndex];
				if (pathObj) {
					pathObj.value = "previews/" + filename;
					pathObj.focus();
				}
			} else if (filetype == "preview_image") {
				var imageObj = document.record.elements["preview_image_"+fileIndex];
				if (imageObj) {
					imageObj.value = "images/previews/" + filename;
					imageObj.focus();
				}
			}
		}
	}

	function setFilePath(filepath, filetype, fileIndex)
	{
		if(filepath != "")
		{
			if (filetype == "product_tiny") {
				if (document.record.tiny_image) {
					document.record.tiny_image.value = filepath;
					document.record.tiny_image.focus();
				} else if (document.record.tiny_image_hidden) {
					document.record.tiny_image_hidden.value = filepath;
				}
			} else if (filetype == "product_small") {
				if (document.record.small_image) {
					document.record.small_image.value = filepath;
					document.record.small_image.focus();
				} else if (document.record.small_image_hidden) {
					document.record.small_image_hidden.value = filepath;
				}
			} else if(filetype == "product_large") {
				if (document.record.big_image) {
					document.record.big_image.value = filepath;
					document.record.big_image.focus();
				} else if (document.record.big_image_hidden) {
					document.record.big_image_hidden.value = filepath;
				}
			} else if(filetype == "product_super") {
				document.record.super_image.value = filepath;
				document.record.super_image.focus();
			} else if (filetype == "downloads") {
				var pathObj = document.record.elements["download_path_"+fileIndex];
				if (pathObj) {
					pathObj.value = filepath;
					pathObj.focus();
				}
			} else if (filetype == "previews") {
				var pathObj = document.record.elements["preview_path_"+fileIndex];
				if (pathObj) {
					pathObj.value = filepath;
					pathObj.focus();
				}
			} else if (filetype == "preview_image") {
				var imageObj = document.record.elements["preview_image_"+fileIndex];
				if (imageObj) {
					imageObj.value = filepath;
					imageObj.focus();
				}
			}
		}
	}

	function updateStockProperty()
	{
		if (!document.record.use_stock_level.checked) {
			document.record.hide_out_of_stock.checked = false;
			document.record.disable_out_of_stock.checked = false;
		}
	}

	function checkUseStock()
	{
		if (!document.record.use_stock_level.checked) {
			document.record.hide_out_of_stock.checked = false;
			document.record.disable_out_of_stock.checked = false;
			alert("You need activate Stock Level before use this option.");
		}
	}

	function pricePreview() 
	{
		var currencyLeft = document.record.currency_left.value;
		var currencyRight = document.record.currency_right.value;
		var currencyRate = document.record.currency_rate.value;
		var inlinePrice = document.getElementById("inline_price");
		var detailPrice = document.getElementById("detail_price");
		var isSales = false;
		if (document.record.is_sales) {
			isSales = document.record.is_sales.checked;
		}
		if (detailPrice && isSales) {
			var price = parseFloat(document.record.price.value);
			if (isNaN(price)) { price = 0; }
			var salesPrice = parseFloat(document.record.sales_price.value);
			if (isNaN(salesPrice)) { salesPrice = 0; }
			var discountPercent = parseFloat(document.record.discount_percent.value);
			if (isNaN(discountPercent)) { discountPercent = 0; }
			if (discountPercent == 0 && salesPrice != 0) {
				discountPercent = Math.round((price - salesPrice) / (price / 100));
			}
			document.record.preview_list_price.value = currencyLeft + formatNumber(price * currencyRate) + currencyRight;
			document.record.preview_sales_price.value = currencyLeft + formatNumber(salesPrice * currencyRate) + currencyRight;
			document.record.preview_discount.value = currencyLeft + formatNumber(Math.round((price - salesPrice) * currencyRate * 100) / 100) + currencyRight + " (" + discountPercent + "%)";
			inlinePrice.style.display = 'none'; 
			detailPrice.style.display = 'block';
		} else if (inlinePrice) {
			var price = parseFloat(document.record.price.value);
			if (isNaN(price)) { price = 0; }
			document.record.preview_price.value = currencyLeft + formatNumber(price * currencyRate) + currencyRight;
			detailPrice.style.display = 'none'; 
			inlinePrice.style.display = 'block'; 
		}
	}

	function formatNumber(numberValue)
	{
		var numberText = new String(numberValue);
		if(numberText.indexOf(".") == -1) {
			numberText += ".00";
		} else if (numberText.indexOf(".") == (numberText.length - 2)) {
			numberText += "0";
		} else {
			var numberParts = numberText.split(".");
			if(numberParts[1].length > 2) {
				numberText = numberParts[0] + "." + numberParts[1].substring(0, 2);
			}
		}
		return numberText;
	}

	function changePane(newTabName)
	{
		var currentTabName = document.record.current_tab.value;

		if (currentTabName != newTabName) {
			currentTabTD = document.getElementById("td_tab_" + currentTabName);
			newTabTD = document.getElementById("td_tab_" + newTabName);
			currentTab = document.getElementById("tab_" + currentTabName);
			newTab = document.getElementById("tab_" + newTabName);
    
			if (currentTabTD) {
				currentTabTD.className = "adminTab";
				newTabTD.className = "adminTabActive";
			}
			currentTab.className = "adminTab";
			newTab.className = "adminTabActive";

			currentData = document.getElementById("data_" + currentTabName);
			newData = document.getElementById("data_" + newTabName);

			currentData.style.display = "none";
			newData.style.display = "block";

			document.record.current_tab.value = newTabName;

			// check if we need change the rows
			var rowObj = newTab.parentNode;
			if (rowObj && rowObj.id && rowObj.id.substring(0, 7) == "tab_row") {
				var tabs = "";
				var activeRowId = rowObj.id;
				var rowId = 1;
				while ((rowObj = document.getElementById("tab_row_" + rowId))) {
					if (rowObj.id == activeRowId) {
						tabs += "<div id='"+rowObj.id+"' class='tabRow'>" + rowObj.innerHTML + "</div>";
					} else {
						tabs = "<div id='"+rowObj.id+"' class='tabRow'>" + rowObj.innerHTML + "</div>" + tabs;
					}
					rowId++;
				}
				var tabsObj = document.getElementById("tabs");
				if (tabsObj && tabs != "") {
					tabsObj.innerHTML = tabs;
				}
			}
		}

		if (newTabName == 'special_offer') {
			activateEditor('editor_so');
		} else if (newTabName == 'other') {
			activateEditor('editor_n');
		} else if (newTabName == 'desc') {
			activateEditors(Array('editor_sd','editor_f','editor_fd'));
		}
	}
