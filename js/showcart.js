// Show Cart
$(document).ready(function(){
			 $('#cartContents').html($('#cartDiv').html());
});

$(function() {
	$('#viewCart').hover(
		function () {
			 $('#cartContents').css("display", "block");
		},
		function () {
			 $('#cartContents').css("display", "none");
		}
	);
});