jQuery(function($) {
	$.fn.extend({
		lavamenu: function(options) {
			var settings = jQuery.extend({
				'class'		: 'lava-menu',
			},options);
		
			return this.each(
				function() {
					var $el = $(this);
					var o = $.metadata ? $.extend({}, settings, $el.metadata()) : settings;
					
					$el.addClass()
				}
			);
		}
	});
});