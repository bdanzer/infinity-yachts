jQuery(document).ready(function($) {

	$.noConflict();
    "use strict";

    $('#yacht-form').on( 'submit', function(event) {

	    event.preventDefault();
	    
		//vars
		var ylocations = $('#ylocations').val();
		var guests = $('#guests').val();
		var price = $('#price').val();
		var boatType = $('#type').val();
		var staterooms = $('#staterooms').val();
		var yachtName = $('#yacht-name').val();
		var yachtLen = $('#len').val();

		$("input#submit").attr("disabled");

		// add loading message
		$('.ajax-response').html('<div class="danzerpress-col-1"><h2 class="danzerpress-title">Searching for Yachts</h2><img style="display:block;margin:auto;max-width: 90px;margin-top:20px;" src="/wp-content/uploads/2018/03/ajax-loader-gif-6.gif"></div>').show().fadeIn();
		
		jQuery.ajax({
			type: 'POST',
			url: ajax_url.ajaxurl,
			data: { 
				security: ajax_url.security,
				action: 'form_start', 
				ylocations: ylocations, 
				price: price, 
				guests: guests, 
				boatType: boatType, 
				staterooms: staterooms, 
				yachtName: yachtName,
				yachtLen: yachtLen,
			},
		}).error( 
	            function() {
	                alert('error');
	            }).success( function(data) {
			
					// log data
					// console.log(data);
					
					// display data
					$('.ajax-response').html(data);
					
				});
	            $("input#submit").removeAttr("disabled");
	            return false;

	});
	
});