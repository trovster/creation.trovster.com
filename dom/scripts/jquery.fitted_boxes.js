/**
  * Fitted Boxes plugin v0.5
  * Based upon the concept outlined at http://www.digital-web.com/articles/the_pinball_effect/
  *
  * @name   fitted_boxes
  * @author Trevor Morris (trovster)
  * @type   jQuery
  * @param  Hash     options                     additional options 
  * @param  String   options[container]          container selector string, default '.fitt_c'
  * @param  Hash     options[class]              hash of classes used within the plugin
  * @param  String   options[class][body]        applied to the body element, default: 'fitt_true'
  * @param  String   options[class][container]   applied to the container, default: 'fitt_enabled'
  * @param  String   options[class][hover]       applied to the container when hovered, default: 'fitt_hover'
  * @param  boolean  options[title]              if true applies the link title to the container when container is hovered, default: true
  * @param  boolean  options[status]             if true changes the window.status to the link href value when container is hovered, default: false
  * 
  */

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
					if($a.is('a') && $a.attr('href')) {
						$a.parents(settings['container']+':first').addClass(settings['class']['container']).hover(
							function(){
								$(this).addClass(settings['class']['hover']);
								if(settings.title && $a.attr('title')) {
									$(this).attr('title',$a.attr('title'));
								}
								if(settings['status']) {
									if($.browser.safari && $a.attr('title')) window.status = "'"+$a.attr('title')+"'"+$a.attr('href');
									else window.status = $a.attr('href');
								}
							},
							function(){
								$(this).removeAttr('title').removeClass(settings['class']['hover']);
								if(settings['status']) window.status = '';
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