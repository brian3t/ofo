// credit card check
function changePassCC(orderForm)
{
	if(orderForm.pass_cc.checked == true) {
		orderForm.pay_without_cc.focus();
	}
}