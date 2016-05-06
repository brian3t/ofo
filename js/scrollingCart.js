// Scrolling Cart

$(function() {
	var $scrollingDiv = $("#cartDiv");

	$(window).scroll(function(){			
		$scrollingDiv
			.stop()
			.animate({"marginTop": ($(window).scrollTop() + 30) + "px"}, "slow" );			
	});
});
