$(document).ready(
	function() {
		if(window.GBrowserIsCompatible || GBrowserIsCompatible()) {
			// remove the map image
			if(GMap2==false) return;
			$('#contact-map img').remove();
			$('body').addClass('has-googlemaps'); // class to the body for styling
			
			// setup variables
			var lat = $('#contact-address div.geo .latitude').attr('title');
			var lon = $('#contact-address div.geo .longitude').attr('title');
			var zoom = parseInt($('#content-navigation li.active a').parent().attr('id').match(/\d+$/)); // default zoom value
			var text = $('#contact-address strong.fn').html();
			text += '<address class="adr">'+$('address.adr').html()+'</address>';
			
			// setup the map
			var map = new GMap2($('#contact-map').get(0));
			var point = new GLatLng(lat, lon);
			var marker = new GMarker(point);
			var current_zoom = map.getZoom();
			
			// map controls, center and add marker
			map.addControl(new GSmallMapControl());
			map.addControl(new GMapTypeControl());
			//map.enableScrollWheelZoom();
			map.setCenter(point, zoom);
			map.addOverlay(marker);
			
			//GEvent.addListener(marker, 'click', function() {marker.openInfoWindowHtml(text);});
			
			$('#content-navigation li a').each(
				function() {
					var $l = $(this);
					var $l_id = $(this).parent().attr('id');
					var $l_zoom = parseInt($l_id.match(/\d+$/));
						
					GEvent.addListener(map, 'zoomend', function(zoom,current_zoom) {
						if(current_zoom==$l_zoom) {
							$('#content-navigation li').removeClass('active');
							$('#z'+$l_zoom).addClass('active');
						}
					});
					
					$l.click(
						function() {
							var $c = $(this);
							var $c_id = $c.parent().attr('id');
							var $c_zoom = parseInt($c_id.match(/\d+$/));
							$('#content-navigation li').removeClass('active');
							$('#' + $c_id).addClass('active');
							$('#' + $c_id).find('a').get(0).blur();
							if($c_zoom!=current_zoom) map.setZoom($c_zoom);
							map.panTo(point);
							return false;
						}
					);
				}
			);

			
		}
	}
);
$(document).unload(GUnload);