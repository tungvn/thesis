jQuery(document).ready(function($) {
	/* Set window height */
	// Default set map height = windows height
	setLeftMenuHeight();

	// Set map height = windows height 
	function setLeftMenuHeight() {
		var window_height = $('#body .main_body').height();
		$('#body .left_menu').css({'height': (window_height+100)+'px'});
	}

	// Window resize
	$(window).resize(function() {
		setLeftMenuHeight();
	});
	/*---------------------*/
	/* Submit form */
	$('#object').find('form.add-new-object').submit(function(event) {
		event.preventDefault();
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
			setTimeout(location.reload(), 2000);
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
	});
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
	$('#object').find('form.list-object').submit(function(event) {
		event.preventDefault();
		var data = $(this).serializeArray();
		var request = {};
		if(data[0].name == 'action' && data[0].value != '') {
			$('#notification').find('span').each(function() { $(this).css('display', 'none'); });
			$('#notification').find('p').html('');
			
			if(data[1].name == 'select-all' || data[1].name != '')
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

});