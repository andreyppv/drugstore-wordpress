/**
 * Lattice Main JavaScript File
 */
(function ($) {
    "use strict";

    $(function () {
    	document.documentElement.className ='js';

		var modal = (function () {
			var method = {},
				overlay,
				$modal,
				content,
				close;

			// Center the modal in the viewport
			method.center = function () {
				var top, left;

				top = Math.max($(window).height() - $modal.outerHeight(), 0) / 2;
				left = Math.max($(window).width() - $modal.outerWidth(), 0) / 2;

				$modal.css({
					top:top + $(window).scrollTop(),
					left:left + $(window).scrollLeft()
				});
			};

			// Open the modal
			method.open = function (settings) {
				content.empty().append(settings.content);

				$modal.css({
					width: settings.width || 'auto',
					height: settings.height || 'auto'
				});

				method.center();
				$(window).bind('resize.modal', method.center);
				$modal.fadeIn();
				overlay.fadeIn();
			};

			// Close the modal
			method.close = function () {
				$modal.fadeOut();
				overlay.fadeOut(500, function() {
					content.empty();
				});
				$(window).unbind('resize.modal');
			};

			// Generate the HTML and add it to the document
			overlay = $('<div id="overlay"></div>');
			$modal  = $('<div id="modal"></div>');
			content = $('<div id="content"></div>');
			close   = $('<a id="modal-close" href="#"><i class="fa fa-times-circle-o"></i></a>');

			$modal.hide();
			overlay.hide();
			$modal.append(content, close);

			$('body').append(overlay, $modal);

			close.click(function(e){
				e.preventDefault();
				method.close();
			});

			return method;
		}());

        /** Mobile Menu */
        $('.mobile-menu-toggle').on('click', function (e) {
            $('#primary').slideToggle();
            e.preventDefault();
        });

		/** Open a modal when add to cart is clicked for variable priced downloads */
		$('.edd-add-to-cart-trigger').on('click', function (e) {
			var data = $(this).parents('.download-image').find('.edd_download_purchase_form');
			data.prepend('<h2>' + lattice_vars.purchase_text + '</h2>');
			modal.open({content: data });
			e.preventDefault();
		});

		/** Open a modal when the shopping cart (in the header) is clicked */
		$('.shopping-cart-trigger').on('click', function (e) {
			var data = $('#shopping-cart-modal').html();
			modal.open({content: data });
			e.preventDefault();
		});

		/** Display expanded search form when the search icon (in the header) is clicked */
		$('.header .search-form button').on('click', function (e) {
			if ($('.header .search-form .search-field' ).val() == '') {
				$('.header .top .search-form .search-field').toggle();
				e.preventDefault();
			}
		});

		/** Custom radio buttons for variable priced downloads */
		$('form.edd_price_option_single .edd_price_options label').unwrap();

		$('form.edd_price_option_single .edd_price_options label').each(function () {
			$(this).removeClass('selected');
			$(this).prepend('<span class="bullet"><i class="fa fa-dot-circle-o"></i></span>');
		});

		$('form.edd_price_option_single .edd_price_options label').on('click', function () {
			$('form.edd_price_option_single .edd_price_options label').each(function () {
				$(this).removeClass('selected');
			});

			$(this).addClass('selected');
		});

		if ($('form.edd_price_option_single .edd_price_options input').prop('checked')) {
			$('form.edd_price_option_single .edd_price_options input:checked').parent().addClass('selected');
		}

		$('.comment_form_rating .edd_reviews_rating_box a').on('click', function (e) {
			$('.comment_form_rating .edd_reviews_rating_box a').removeClass('active');
			$(this).addClass('active');
			e.preventDefault();
		});
	});
}(jQuery));