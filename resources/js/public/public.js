jQuery(document).ready(function($) 
{
	$.noConflict();
	"use strict";

	function search_page() {
		var data = {};

		if ($('.load-boats')) {
			$('.load-boats').removeClass('load-boats');
			load_boats()
		}
	
		$('#yacht-form').on('submit', function(event) {
			load_boats(event);
		});
	
		function load_more_boats(event) {
			event.preventDefault();

			data.action = 'load_more';
			data.paged = $('#load-more-boats').data('paged');

			$('#load-more-boats').hide();
			$('.iyc-spinner').show();

			jQuery.ajax({
				type: 'POST',
				url: ajax_url.ajaxurl,
				data: data,
			}).error(function() {
				alert('error');
				$('.iyc-spinner').hide();
			}).success(function(data) {
				$('.iyc-spinner').hide();
				$('#load-more-boats').before(data);	
				$('#load-more-boats').data('paged', $('#load-more-boats').data('paged') + 1);
				$('#load-more-boats').show();
			});
		}
		
		function load_boats(event = null) {
			if (event) {
				event.preventDefault();
			}
				
			//vars
			data.ylocations = $('#ylocations').val();
			data.guests = $('#guests').val();
			data.price = $('#price').val();
			data.boatType = $('#type').val();
			data.staterooms = $('#staterooms').val();
			data.yachtName = $('#yacht-name').val();
			data.yachtLen = $('#len').val();
			data.security = ajax_url.security;
			data.action = 'form_start';
	
			$("input#submit").attr("disabled");
	
			// add loading message
			$('.ajax-response').html('<div class="danzerpress-col-1"><h2 class="danzerpress-title">Searching for Yachts</h2><img style="display:block;margin:auto;max-width: 90px;margin-top:20px;" src="/wp-content/uploads/2018/03/ajax-loader-gif-6.gif"></div>').show().fadeIn();
			
			jQuery.ajax({
				type: 'POST',
				url: ajax_url.ajaxurl,
				data: data,
			}).error(function() {
				alert('error');
			}).success(function(data) {
				$('.ajax-response').html(data);	
				$('#load-more-boats').on('click', function(event) {
					load_more_boats(event);
				});
			});
	
			$("input#submit").removeAttr("disabled");
			return false;
		}
	}

	new search_page();
});