jQuery(function($) {
	$.fn.extend({
		expand_me: function(options) {
			var settings = jQuery.extend({
				'text'		: 'Expand',
				'class'		: 'expand-link',
				'href'		: '#expand',
				'increase'	: 150
			},options);
		
			return this.each(
				function() {
					var $el = $(this);
					var $el_parent = $el.parent();
					var o = $.metadata ? $.extend({}, settings, $el.metadata()) : settings;
					
					$('<a/>').text(o['text']).addClass(o['class']).attr('href',o['href']).click(function(){
						$el.animate({
							height: $el.height()+o['increase']
						}, 'slow');
						$(this).get(0).blur();
						return false;
					}).appendTo($el_parent);
				}
			);
		}
	});
});