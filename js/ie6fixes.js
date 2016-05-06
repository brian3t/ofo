// JavaScript Document

$(function() {
	$("#navigation > li").hover(
		function () {
			$("ul", this).css("display", "block");
		},
		function () {
			$("ul", this).css("display", "none");
		}
	);
});