// change value
jQuery(document).ready(function() {
	// Default set map height = windows height
	setMapHeight();
	setResponseTextWidth();

	// Set map height = windows height 
	function setMapHeight() {
		var window_height = $(window).height();
		$('#content > .left_col, #content > .main_col, #content > .right_col').height(window_height+100);
	}
	function setResponseTextWidth() {
		var map_width = $('#map').width();
		$('#responseText').width(map_width);
	}
	// Window resize
	$(window).resize(function() {
		setMapHeight();
		setResponseTextWidth();
	});



	// Show and hide charts
	$('#charts_link').click(function() {

		$('#content .main_col').stop().animate({width: '50%'}, 400);
		$('#responseText').animate({width: '50%'}, 400);
		$('#content .right_col').stop().animate({right: 0}, 400);

		$('#close_charts_col').click(function() {
			hide_charts_col();
		});

		function hide_charts_col() {
			$('#content .main_col').stop().animate({width: '80%'}, 400);
			$('#content .right_col').stop().animate({right: '-30%'}, 400);
			$('#responseText').animate({width: '100%'}, 400);
		}
	});

	// map type option
	var type = new Array();
	// list of layers and workspace
	var layers = new Object();
	// default map is 'roadmap + customlayer'
	//setMapTypeId(type);
	// update 'type' array
	function updateTypeArray(obj) {
		var type = new Array(); // empty 'type'
		// update
		$(obj).parents('div.map_option_block').find('input:checked').each(function() {
			type[type.length] = $(this).val();
		});
		return type;
	}
	// change map when change select input
	$('#map_control input').on('change', function() {
		// uncheck other 'input' has the same 'name' attribute
		var input_name = $(this).attr('name');
		$(this).parents('div.map_option_block').find('input[name="' + input_name + '"]').not(this).each(function() {
			$(this).prop('checked', false);
		});
		layers = getLayers2Search();
		// update 'type'
		type = updateTypeArray(this);
		
		layerInput(type);
		// set map type id follow input
		//setMapTypeId(type);
	});

	// Search Tool Ajax
	// Prevent submit event
	$('form.search_box').submit(function(event) {
		event.preventDefault();
	});
	// Search when users press space button
	$('input#s').keyup(function(event) {
		event.preventDefault();
		if(event.keyCode == 13) {
			//setTimeout(500);
			var data = $.trim($(this).val());
			if (data != '') {
				data = convertU2T(data);
				if(layers !== {}) {
					var request = {
						keyword: data,
						group: layers,
						action: 'search_tool'
					}

					$.ajax({
						url: 'functions.php',
						type: 'POST',
						data: request,
						
					})
					.done(function(response) {
						response = convertT2U(response);
						$('#map_control .map_option_block').has('ul#search-result').fadeIn();
						$("ul#search-result").html(response);
					})
					.fail(function() {
						console.log("error");
					})
					.always(function() {
						console.log("complete");
					});
				}
			}
		}
	});
	// Get group workspace and layers have been choosen
	function getLayers2Search() {
		var layers = new Array();

		$('#map_control .workspace').has('input:checked').each(function() {
			var wp = $(this).attr('for');
			var layer = new Array();
			$(this).find('input:checked').each(function() {
				layer[layer.length] = $(this).val();
			});
			
			layers.push({
				workspace: wp,
				layers: layer
			});
		});
		return layers;
	}
	
	
});
// Get latlng to show map from search
function showResult(lat, lng) {
	cur_map.setCenter(new OpenLayers.LonLat(lat, lng), 4);
}