$(document).ready(
	function() {
		// flash replacement on the headings
		$('.permalink.hentry h3.entry-title, .hentry.summary h3.entry-title, .permalink h3.entry-title, .my-profile h3.fn, body.services h3.entry-title').flash(
			{ 
				src: '/dom/scripts/fonts/insterstate-lt-cond.swf',
				flashvars: { 
					css: [
						'* { color: #990000; }',
						'em { color: #777777; }'
					].join(' ')
				}
			},
			{
				version: 7
			},
			function(htmlOptions) {
				htmlOptions.flashvars.txt = this.innerHTML;
				this.innerHTML = '<span>'+this.innerHTML+'</span>';
				var $alt = $(this.firstChild);
				htmlOptions.height = $alt.height();
				htmlOptions.width = $alt.width();
				$alt.addClass('alt');
				$(this).addClass('flash-replaced').prepend($.fn.flash.transform(htmlOptions));						
			}
		);
		$('body.services h4.subtitle').flash(
			{ 
				src: '/dom/scripts/fonts/insterstate-lt-cond.swf',
				flashvars: { 
					css: [
						'* { color: #999999; }',
					].join(' ')
				}
			},
			{
				version: 7
			},
			function(htmlOptions) {
				htmlOptions.flashvars.txt = this.innerHTML;
				this.innerHTML = '<span>'+this.innerHTML+'</span>';
				var $alt = $(this.firstChild);
				htmlOptions.height = $alt.height();
				htmlOptions.width = $alt.width();
				$alt.addClass('alt');
				$(this).addClass('flash-replaced').prepend($.fn.flash.transform(htmlOptions));						
			}
		); 
});