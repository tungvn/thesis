jQuery(document).ready(function($) {
	/* Set window height */
	// Default set map height = windows height
	setMapHeight();

	// Set map height = windows height 
	function setMapHeight() {
		var window_height = $(window).height();
		$('#body').css({'height': (window_height+100)+'px', 'padding-bottom': '-100px'});
	}

	// Window resize
	$(window).resize(function() {
		setMapHeight();
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
		$('#notification').find('span.info').css('display', 'block');
		$('#notification').find('p').css('color', 'green').html('Creating...');
		$.ajax({
			url: 'http://localhost/dadangsinhhoc/administrator/functions.php',
			type: 'POST',
			data: request
		})
		.done(function(response) {
			console.log(response);
			location.reload();
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
	/* Form action list objects */
	$('#object').find('form.list-object').submit(function(event) {
		event.preventDefault();
		var data = $(this).serializeArray();
		console.log(data.length);
		var request = {};
		if(data[0].name == 'action' && data[0].value != '') {
			$('#notification').find('span.error').css('display', 'none');
			$('#notification').find('p').css('color', 'red').html('');
			
			request = {
				data: data,
				action: 'submit_' + data[0].value +'_object'
			}
		}
		else {
			$('#notification').find('span.error').css('display', 'block');
			$('#notification').find('p').css('color', 'red').html('You must select actions!');
			return false;
		}
		console.log(request);

		$.ajax({
			url: 'http://localhost/dadangsinhhoc/administrator/functions.php',
			type: 'POST',
			data: request
		})
		.done(function(response) {
			console.log(response);
			//location.reload();
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
	});
});