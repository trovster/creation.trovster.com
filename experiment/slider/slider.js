jQuery(function($) {
	$.fn.extend({
		slider: function(options) {
			var settings = jQuery.extend({
				'default'	: 'close', 		// default container view, open or close
				'open'		: {
					'speed'	: 'slow',
					'text'	: 'Close'		// when the element is OPEN, show the closed text
				},
				'close'		: {
					'speed'	: 'normal',
					'text'	: 'Open'		// when the element is CLOSED, show the open text
				},
				'class'		: {
					'slider': 'slider',
					'toggle': 'toggle'
				},
				'href'		: '#slider',	// the href anchor...
				'event'		: 'click', 		// click, mouseout or dblclick
				'cookie'	: false,			// true or false
			},options);
		
			return this.each(
				function() {
					var $el = $(this);
					var $el_parent = $el.parent();
					var o = $.metadata ? $.extend({}, settings, $el.metadata()) : settings;
					
					if(o['cookie']==true && $.cookie && $.cookie('jquery_slider')) {
						// check whether cookie is enabled, set the value.
						o['default'] = $.cookie('jquery_slider');
					}

					var $slider = $('<div/>').addClass(o['class']['slider']);
					if(o['default']=='close') $el.hide();
					$el.wrap($slider);
					
					var $toggle = $('<div/>').addClass(o['class']['toggle']);
					var $toggle_a = $('<a/>').attr({'href':o['href']}).text(o[o['default']]['text']).addClass('slider_'+o['default']);
					$toggle.append($toggle_a);
					$toggle.insertAfter($el);
					
					$toggle_a.bind(o['event'],function(){
						var $clicked = $(this);
						if(o['default']=='open') {
							var clicked_to = 'close';
							$clicked.addClass('slider_closing');
							$el.slideUp(o[clicked_to]['speed'],function(){
								// when complete
								$clicked.removeClass('slider_open').removeClass('slider_closing').addClass('slider_close').text(o[clicked_to]['text']);
								o['default'] = clicked_to;
							});
						}
						else {
							var clicked_to = 'open';
							$clicked.addClass('slider_opening');
							$el.slideDown(o[clicked_to]['speed'],function(){
								// when complete
								$clicked.removeClass('slider_close').removeClass('slider_openning').addClass('slider_open').text(o[clicked_to]['text']);
								o['default'] = clicked_to;
							});
						}
						if(o['cookie']==true && $.cookie) {
							// check whether cookie is enabled, set the value.
							$.cookie('jquery_slider', clicked_to);
						}
						$clicked.blur();
						return false;
					})
				}
			);
		}
	});
});