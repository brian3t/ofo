// Part Finder Home Page Effects

$(document).ready(function(){
						   
	$(".showHide,.currentVehicle").live('click', function() {
		if($(".finder-wrapper").hasClass("hide")) {
			$("#vehicleSelector").animate({
				width: "200px"
			}, 300);
		} else {
			$("#vehicleSelector").animate({
				width: "225px"
			}, 300);
		}
	});
	
	$(".deleteVehicle").live('click', function(){
		$("#vehicleSelector").animate({
				width: "225px"
			}, 300);
	});
});