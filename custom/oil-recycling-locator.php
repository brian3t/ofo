<script type="text/javascript" src="http://www.google.com/jsapi?key=ABQIAAAA5ZySsfbOdrtpzhRp2ghGTBQ9_My28vhZKTB0Cm2JOOIcKoNUthTHa__lfO0U4pgpQR_wmfEZn3-__A"></script>
<script type="text/javascript">
	google.load("maps", "3", {other_params: "sensor=true"});
</script>
<script type="text/javascript" charset="utf-8">
	$(function(){
		var latlng = new google.maps.LatLng(37.300275,-98.173828);
		var markers = [];
		var locationSelect;
		var bounds = new google.maps.LatLngBounds();
	
		var myOptions = {
		  zoom: 4,
		  center: latlng,
		  mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		
		var map = new google.maps.Map(document.getElementById("map"), myOptions);
		var infoWindow = new google.maps.InfoWindow();
	
		$("#add-point").submit(function(){
			getLocations();
			return false;
		});
		
		function getLocations() {
			$("#status").html('<img src="images/loading.gif" />');
			var data = $("#address").val();
			$.getJSON("map-service.php?action=savepoint&address=" + data, function(json) {
				if (json.Locations.length > 0) {
					for (i=0; i<json.Locations.length; i++) {
						var location = json.Locations[i];
						geoEncode(location);
					}
					$("#status").html('');
				}
			});
		}
		
		function geoEncode(location) {
			var address = location.address + "," + location.city + "," + location.state + "," + location.zip;
			var geocoder = new google.maps.Geocoder();
			geocoder.geocode({ address: address }, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK && results.length) {	
					if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
					geocode = results[0].geometry.location;
					createMarker(geocode, location);
					}
				}
			});
		}
		
		function createMarker(latlng, location) {
			var html = "<b>" + location.name + "</b> <br/>" + location.address + "<br/>" + location.city + ", " + location.state + " " + location.state + "<br/>" + location.phone;
			var marker = new google.maps.Marker({
				map: map,
				position: latlng
			});
			google.maps.event.addListener(marker, 'click', function() {
				infoWindow.setContent(html);
				infoWindow.open(map, marker);
			});			
			markers.push(marker);
			bounds.extend(latlng);
			map.fitBounds(bounds);
			$("<li />")
			.html("<strong>" + location.name + "</strong><br/>" + location.address + "<br/>" + location.city + ", " + location.state + " " + location.zip + "<br/>" + location.phone)
			.attr('id', 'loc' + i)
			.click(function(){
				showMarker(marker);
			})
			.appendTo("#list");
		}
		
		function showMarker(marker) {
			google.maps.event.trigger(marker,'click');
		}
		
		$(document).ready(function(){

			var clearMePrevious = '';
		
			// clear input on focus
			$('.clearMeFocus').focus(function()	{
				if($(this).val()==$(this).attr('title')) {
					clearMePrevious = $(this).val();
					$(this).val('');
				}
			});
			
			// if field is empty afterward, add text again
			$('.clearMeFocus').blur(function() {
				if($(this).val()=='') {
					$(this).val(clearMePrevious);
				}
			});
		});

});
</script>

<div class="titleTopCenter">
	<h1>Oil Recycling Locations</h1>
</div>
<div id="recycleDesc"> Recycling your used oil is very important! Be sure to dispose of your oil properly by transporting it to a recylcling center near you.
	When transporting your used oil, make sure you have a safe container. You can find containers in the Oil Supplies section of our store. It is usually a good idea to call the recycling center before you go. </div>
<div class="right">
	<h2>Recommended Item</h2>
	<a href="product_details.php?item_id=7">
		<img src="/images/oil-filters/recommended-product.jpg" width="225" height="109" border="0">
	</a>
</div>
<div class="clear">
	<div id="leftContainer">
		<div id="addressInput">
			<form id="add-point" action="map-service.php" method="POST">
				<input type="hidden" name="action" value="savepoint" id="action">
				<div class="input">
					<input type="text" class="clearMeFocus" name="address" id="address" title="Zip, City, or State" value="Zip, City, or State">
					<button class="button" type="submit">Locate</button>
				</div>
			</form>
		</div>
		<div id="status" class="center"></div>
		<ul id="list">
		</ul>
	</div>
	<div id="map"></div>
</div>
