jQuery(function($) {
	$.fn.extend({
		fitted_boxes: function(options) {
			var settings = jQuery.extend({
				'container': '.fitt_c',
				'class': {
					'body': 'fitt_true',
					'container': 'fitt_enabled',
					'hover': 'fitt_hover'
				},
				'title': true,
				'status': false
			},options);

			$('body').addClass(settings['class']['body']);
			
			return this.each(
				function() {
					var $a = $(this);
					var o = $.metadata ? $.extend({}, settings, $a.metadata()) : settings;
					
					if($a.is('a') && $a.attr('href')) {
						if(o['container']=='parent') var $container = $a.parent();
						else var $container = $a.parents(o['container']+':first');
						$container.addClass(o['class']['container']).hover(
							function(){
								$(this).addClass(o['class']['hover']);
								if(settings.title && $a.attr('title')) {
									$(this).attr('title',$a.attr('title'));
								}
								if(o['status']) {
									if($.browser.safari && $a.attr('title')) window.status = "'"+$a.attr('title')+"'"+$a.attr('href');
									else window.status = $a.attr('href');
								}
							},
							function(){
								$(this).removeAttr('title').removeClass(o['class']['hover']);
								if(o['status']) window.status = '';
							}
						).click(
							function(){
								window.location.href = $a.attr('href');
							}
						);
					}
				}
			);
		}
	});
});

/*
// $() must be an anchor
$('div.fitt_c a.fitt_a').fitted_boxes({
	container : 'div.fitt_c',
	class : {
		'body' : 'fitt_true',
		'container' : 'fitt_enabled',
		'hover' : 'fitt_hover'
	},
	title : true
});
*/