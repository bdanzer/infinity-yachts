jQuery(document).ready(function($) {

	$('#ylocations').change(function(event) {


		// define value
		var ylocations = $(this).val();

		if (ylocations == 0) {
			$('.ajax-response').html('<p>Select a value in the dropdown</p>');
			return false;
		}
		
		// add loading message
		$('.ajax-response').html('<img style="display:block;margin:auto;max-width: 90px;margin-top:20px;" src="https://testing.bdanzer.com/wp-content/uploads/2018/03/ajax-loader-gif-6.gif">');
		

		console.log(ylocations);
		
		// submit the data
		$.post(ajaxurl, {
			
			nonce:  ajax_admin.nonce,
			action: 'admin_hook',
			ylocations
			
		}, function(data) {
			
			// log data
			// console.log(data);
			
			// display data
			$('.ajax-response').html(data);
			
		});

	});

	$('#yacht-form').on( 'submit', function(event) {

		event.preventDefault();

		//$("input#submit").attr("disabled","disabled");

		var ylocations = $('#ylocations').val();
		var inputCheckbox = $('input[type=checkbox]');
		var arrNumber = new Array();

		$('input[type=checkbox]:checked').each(function() {
			arrNumber.push($(this).val());
		});

		$(inputCheckbox).each(function(){
		    if ($(this).prop('checked')==false){ 
		        console.log(arrNumber.push($(this).val() + 'unchecked'));
		    }
		});

		console.log(arrNumber);

		if (ylocations == 0) {
			$('.danzerpress-save').html('<p>Error! Empty Value.</p>').show().fadeIn();
			$('.danzerpress-save').addClass('danzerpress-error');
			$('.danzerpress-save').delay(5000).fadeOut();
			$("input#submit").removeAttr("disabled");
			return false;
		}
		
		var settings =  $(this).serialize();
		// console.log(settings);
		// submit the data

		// add loading message
		$('.danzerpress-save').html('<h2>Changes Pending:</h2><img style="display:block;margin:auto;max-width: 90px;margin-top:20px;" src="https://testing.bdanzer.com/wp-content/uploads/2018/03/ajax-loader-gif-6.gif">').show().fadeIn();
		
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl, 
			data: { 
				nonce:  ajax_admin.nonce,
				action: 'test_handler', 
				array: arrNumber 
			},
		}).error( 
                function() {
					$('.danzerpress-save').addClass('danzerpress-error');
                    $('.danzerpress-save').html('<p>An Error Occurred</p>').show().fadeIn();
                	$('.danzerpress-save').delay(3000).fadeOut();
                }).success( function() {
                	$('.danzerpress-save').html('<p>Changes Saved!</p>').show().fadeIn();
                	$('.danzerpress-save').delay(3000).fadeOut();

                });
                $("input#submit").removeAttr("disabled");
                return false;

	});

});

