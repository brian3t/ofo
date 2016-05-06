
function confirmOrder() 
{

	if (!document.order_confirmation.confirm.disabled) {
		document.order_confirmation.confirm.disabled = true;
		document.order_confirmation.action.value = 'save';
		document.order_confirmation.submit();
	}

}
