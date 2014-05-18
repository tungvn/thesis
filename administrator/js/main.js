jQuery(document).ready(function($) {
	/* Set window height */
	// Default set map height = windows height
	setLeftMenuHeight();

	// Set map height = windows height 
	function setLeftMenuHeight() {
		var window_height = $('#body .main_body').height() + 100;
		var left_menu = $('#body .left_menu').height();
		if(left_menu > window_height)
			$('#body .main_body').height(left_menu);
		else 
			$('#body .left_menu').height(window_height);
	}

	// Window resize
	$(window).resize(function() {
		setLeftMenuHeight();
	});
	/*---------------------*/
	/* Submit form */
	if($('body').find('#object').attr('for') == 'object') {
		var file;
		var fileInput = $('#object').find('input#shpfile');
		$('#object').find('input#shpfile').change(function(event) {
			file = fileInput.files[0];
		});
		//$('#object').find('form.add-new-object').submit(function(event) {
			/*event.preventDefault();
			var name = $(this).find('input#name').val();
			var data = $(this).serializeArray();
			var slug = toSlug(name);
			var type = $(this).find('input#type').val();
			if($(this).find('input#slug').val() == '') {
				data[1].value = slug;
			}
			var request = {
				data: data,
				action: 'submit_add_new_object'
			}

			$('#notification').find('span').each(function() { $(this).css('display', 'none'); });
			$('#notification').find('span.info').css('display', 'block');
			$('#notification').find('p').css('color', 'green').html('Creating...');

			$.ajax({
				url: 'functions.php',
				type: 'POST',
				data: request
			})
			.done(function(response) {
				$('#notification').find('span').each(function() { $(this).css('display', 'none'); });
				$('#notification').find('p').css('color', 'green').html('');
				if(response == 'success') {
					$('#notification').find('span.info').css('display', 'block');
					$('#notification').find('p').css('color', 'green').html('Create successfully');
				}
				else {
					$('#notification').find('span.error').css('display', 'block');
					$('#notification').find('p').css('color', 'red').html('Cannot create new object!');
				}
				console.log(response);
			})
			.fail(function() {
				console.log("error");
			})
			.always(function() {
				console.log("complete");
				//setTimeout(location.reload(), 2000);
			});*/
		//});
	}
	/* Auto create slug */
	function toSlug(str) {
		return str
				.toLowerCase()
				.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, 'a')
				.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, 'e')
				.replace(/ì|í|ị|ỉ|ĩ/g, 'i')
				.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, 'o')
				.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, 'u')
				.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, 'y')
				.replace(/đ/g, 'd')
				.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_/g, '')
				.replace(/[^\w ]+/g,'')
				.replace(/ +/g,'');
	}
	/*------------------*/
	/* Select/Unselect all */
	$('input#select-all').change(function(event) {
		if($(this).is(':checked')) {
			$('form.list-object .tbody').find('input[type="checkbox"]').each(function() {
				$(this).prop('checked', true);
			});
		}
		else {
			$('form.list-object .tbody').find('input[type="checkbox"]').each(function() {
				$(this).prop('checked', false);
			});
		}
	});
	$('form.list-object .tbody').find('input[type="checkbox"]').each(function() {
		$(this).change(function(event) {
			if($('form.list-object .tbody').find('input[type="checkbox"]').length == $('form.list-object .tbody').find('input[type="checkbox"]:checked').length)
				$('input#select-all').prop('checked', true);
			else
				$('input#select-all').prop('checked', false);
		});
	});
	
	/* Form action list objects */
	if($('body').find('#object').attr('for') == 'object') {
		$('#object').find('form.list-object').submit(function(event) {
			event.preventDefault();
			var data = $(this).serializeArray();
			console.log(data);
			var request = {};
			if(data[0].name == 'action' && data[0].value != '') {
				$('#notification').find('span').each(function() { $(this).css('display', 'none'); });
				$('#notification').find('p').html('');
				
				request = {
					data: data,
					action: 'submit_' + data[0].value +'_object'
				}
				if(data[0].value == 'delete')
					$.ajax({
						url: 'functions.php',
						type: 'POST',
						data: request
					})
					.done(function(response) {
						console.log(response);
						$('#notification').find('span').each(function() { $(this).css('display', 'none'); });
						$('#notification').find('p').html('');
						if(response == 'success') {
							$('#notification').find('span.info').css('display', 'block');
							$('#notification').find('p').css('color', 'green').html('Delete successfully');
						}
						else {
							$('#notification').find('span.error').css('display', 'block');
							$('#notification').find('p').css('color', 'red').html('Cannot delete objects!');
						}
					})
					.fail(function() {
						$('#notification').find('span.error').css('display', 'block');
						$('#notification').find('p').css('color', 'red').html('Cannot delete objects!');
					})
					.always(function() {
						setTimeout(location.reload(), 2000);
					});
			}
			else {
				$('#notification').find('span.error').css('display', 'block');
				$('#notification').find('p').css('color', 'red').html('You must select actions!');
				return false;
			}
		});
	}
	
	// Users
	if($('body').find('#object').attr('for') == 'users') {
		$('#object').find('form.list-users').submit(function(event) {
			event.preventDefault();
			$('#notification').find('span').each(function() { $(this).css('display', 'none'); });
			$('#notification').find('p').html('');
			var data = $(this).serializeArray();
			var request = {};
			console.log(data);
			if(data[0].value != '' && data[1].value != '') {
				$('#notification').find('span.error').show();
				$('#notification').find('p').html('You only choose an action!');
				return false;
			}
			else if(data[0].value == '' && data[1].value == '') {
				$('#notification').find('span.error').css('display', 'block');
				$('#notification').find('p').css('color', 'red').html('You must select actions!');
				return false;
			}
			else if(data[0].name == 'action' && data[0].value != '') {
				if(data.length < 3) {
					$('#notification').find('span.error').css('display', 'block');
					$('#notification').find('p').css('color', 'red').html('You must choose users to delete!');
				}
				else {
					var result = confirm('You are deleting users. Are you sure?');
					if(!result) {
						location.reload();
					}
					request = {
						data: data,
						action: 'delete_users'
					}
				}
			}
			else if(data[1].name == 'change_role' && data[1].value != '') {
				if(data.length < 3) {
					$('#notification').find('span.error').css('display', 'block');
					$('#notification').find('p').css('color', 'red').html('You must choose users to delete!');
				}
				else {
					var result = confirm('You are changing users\' role. Are you sure?');
					if(!result) {
						location.reload();
					}
					request = {
						data: data,
						action: 'change_role_users'
					}
				}
			}
			
			$.ajax({
				url: 'functions.php',
				type: 'POST',
				data: request
			})
			.done(function(response) {
				//console.log(response);
				if(response == 'success_del_users') {
					$('#notification').find('span.info').css('display', 'block');
					$('#notification').find('p').css('color', 'green').html('Delete successfully!');
				}
				else if(response == 'success_change_role') {
					$('#notification').find('span.info').css('display', 'block');
					$('#notification').find('p').css('color', 'green').html('Users\' role has been changed!');
				}
				else if(response == 'fail_del_users') {
					$('#notification').find('span.error').css('display', 'block');
					$('#notification').find('p').css('color', 'red').html('Cannot delete users!');
				}
				else if(response == 'fail_change_role_users') {
					$('#notification').find('span.error').css('display', 'block');
					$('#notification').find('p').css('color', 'red').html('Cannot change users\' role!');
				}
				else {
					$('#notification').find('span.error').css('display', 'block');
					$('#notification').find('p').css('color', 'red').html('Something was wrong. Try again');
				}
			})
			.fail(function() {
				$('#notification').find('span.error').css('display', 'block');
				$('#notification').find('p').css('color', 'red').html('Cannot delete users!');
			})
			.always(function(response) {
				console.log(response);
				setTimeout(location.reload(), 2000);
			});
		});
	}


	// Register
	$('a#redirect_register').click(function(e) {
		e.preventDefault();
		$('div#login_box').slideUp();
		$('div#register_box').slideDown();
	});
	$('a#redirect_login').click(function(e) {
		e.preventDefault();
		$('div#login_box').slideDown();
		$('div#register_box').slideUp();
	});
	$('form#register_form').submit(function(event) {
		event.preventDefault();
		if($(this).find('input#rg_p').val() != $(this).find('input#rg_rep').val())
			$(this).find('p.register_error').html('Password and re-password must be same!');
	});

	// Add new option -- Settings.php
	//if($('body').find('#setting').) {
		
		$('#setting').find('input#add_new_option').click(function(event) {
			event.preventDefault();
			var table = $('#setting').find('table');
			var html = '<tr><td class="grid-1"><input type="text" class="has-border has-border-radius" name="add_new_option_name" required placeholder="Enter option name (only a-z, 0-9, _)" style="width: 100%;"></td><td class="grid-1"><input type="text" class="has-border has-border-radius" name="add_new_option_value" placeholder="Enter your value"></td><td class="grid-1"><input type="text" name="add_new_option_desc" class="has-border has-border-radius" placeholder="Enter option description" style="width: 100%"></td></tr>';
			table.append(html);
			$(this).hide();
		});
	//}
	
	// Search form
	$('form.search_box').find('input#s').click(function(event) {
		$('form.search_box').find('div.advanced_search').slideDown('fast', function() {
			var advanced_search = $(this);
			$(this).show();
			$(this).find('span.close').click(function(event) {
				advanced_search.slideUp('fast', function() {
					$(this).hide();
				})
			});
		});
	});
	// Search Tool Ajax
	// Prevent submit event
	$('form.search_box').submit(function(event) {
		var data = $.trim($(this).val());
		if (data != '') {
			data = convertU2T(data);
			var layers = getLayers2Search();
			if(layers !== {}) {
				var request = {
					keyword: data,
					group: layers,
					action: 'search_tool'
				}
			}
			else {
				alert('You must choose layers to search!');
			}
		}
	});

	// Get group workspace and layers have been choosen
	function getLayers2Search() {
		var layers = new Array();

		$('.workspace').has('input:checked').each(function() {
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

	/* Handle changing font */
	function convertU2T(xau) {
		var srcid1 = "UNICODE";
		var srcmap1 = initCharMap(srcid1);
		var destmap1 = initCharMap(parseMapID("TCVN-3"));
		var xau1 = srcmap1.convertTxtTo( xau, destmap1 )	;
		return xau1;
	}
	function convertT2U(xau) {
		var srcid1 = "TCVN-3";
		var srcmap1 = initCharMap(srcid1);
		var destmap1 = initCharMap(parseMapID("UNICODE"));
		var xau1 = srcmap1.convertTxtTo( xau, destmap1 )	;
		return xau1;
	}

	$('body').find('.tab-title').click(function(event) {
		var show = $(this).attr('for');

		$('body').find('.tab-title').each(function() {
			$(this).removeClass('tab-on');
		});
		if(show != 'view_map')
			$('body').find('.tab-title[for="view_map"]').addClass('hidden');

		$(this).addClass('tab-on');

		$('body').find('.tab-content').each(function() {
			$(this).removeClass('tab-show');
		});
		$('body').find('.tab-content').each(function() {
			if($(this).attr('for') == show) {
				$(this).addClass('tab-show');
			}
		});
	});
});

// Show map in single.php
function showResult(wp, layer, lat, lng) {
	$('body').find('.tab-title').each(function() {
		if($(this).attr('for') != 'view_map')
			$(this).removeClass('tab-on');
		else {
			$(this).removeClass('hidden');
			$(this).addClass('tab-on');
		}

		$('body').find('.tab-content').each(function() {
			if($(this).attr('for') == 'view_map')
				$(this).addClass('tab-show');
			else
				$(this).removeClass('tab-show');
		});
	});
	var layers = new Array(wp + ':' + layer);

	layerInput(layers, lat, lng);
	
}