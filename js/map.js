// change value
jQuery(document).ready(function() {
	// map type option
	var type = new Array();
	// default map is 'roadmap + customlayer'
	layerInput(type);
	// update 'type' array
	function updateTypeArray(obj) {
		var type = new Array(); // empty 'type'
		// update
		$(obj).parents('div.map_option_block').find('input:checked').each(function() {
			type.push($(this).val());
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
		// update 'type'
		type = updateTypeArray(this);
		layerInput(type);
	});
});
