jQuery(function($) {
	$.fn.extend({
		image_window_viewer: function(options) {
			var settings = jQuery.extend({
				'height'	: '250px',
				'width'		: '250px',
				'class'		: 'image_window_viewer',
				'mouse'		: {
					'enabled'	: true
				}
			},options);
		
			return this.each(
				function() {
					var $inside = $(this);
					var sel_ins_x = $inside.width();
					var sel_ins_y = $inside.height();
					var auto = {'height' : false, 'width' : false};
					if(settings.width=='auto') {
						auto.width = true;
						settings.width = sel_ins_x;
					}
					if(settings.height=='auto') {
						auto.height = true;
						settings.height = sel_ins_y;
					}
					var $container = $('<div/>').css({'position' : 'relative', 'overflow' : 'hidden', 'height' : settings['height'], 'width' : settings['width'] }).addClass(settings['class']);
					$inside.wrap($container);
					$inside.css({'position' : 'absolute'});
					var $outside = $inside.parent();
					var sel_x = $outside.width();
					var sel_y = $outside.height();
					
					// mouse movement
					if(settings['mouse']['enabled']==true) {
						$outside.mousemove(function(e){
							var x = e.pageX;
							var y = e.pageY;
	
							$outside.one('click',function(e){
								x -= this.offsetLeft;
								y -= this.offsetTop;
							}).trigger('click');
								
							// % of cursor across the selector
							var x_percent = Math.round((x/(sel_x))*100)-50;
							var y_percent = Math.round((y/(sel_y))*100)-50;
							
							var pos_x = (x/sel_x*sel_ins_x)-(x/sel_x*sel_x);
							var pos_y = (y/sel_y*sel_ins_y)-(y/sel_y*sel_y);
							$inside.css({'left' : -pos_x+'px', 'right' : 'auto', 'top' : -pos_y+'px', 'bottom' : 'auto'});
						});
					}
					
				}
			);
		}
	});
});