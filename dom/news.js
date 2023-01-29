$(document).ready(
	function() {
		var news_id = $('#content-primary > .permalink.hentry').attr('id');
		var total_images = $('#position-navigation li a').size();

		if($('#'+news_id+' div.image img').size()==0 || total_images==0) return false;

		$next = $('#'+news_id+' div.image ul.pagination li.next a');
		$prev = $('#'+news_id+' div.image ul.pagination li.prev a');
		$next_accesskey = $next.size() ? $next.attr('accesskey') : 'x';
		$prev_accesskey = $prev.size() ? $prev.attr('accesskey') : 'z';


		var current_image = $('#'+news_id+' div.image img').attr('title'); // current image
		var current_image_pos = parseInt($('#'+news_id+' div.image img').attr('id').match(/\d+$/)); // current image position 1+

		if(!$.browser.safari) {
		$.ajax({
			type: 'GET',
			url: '/_compile/a_news_images/',
			data: 'id='+news_id+'&image='+current_image,
			dataType: 'xml',
			success: function(xml) {
				var ul = $('<ul>');
				$('img', xml).each(
					function() {
						var image = $('<img>').attr({
							src:	$('src', this).text(),
							alt:	$('alt', this).text(),
							title:	$('title', this).text(),
							height:	$('height', this).text(),
							width:	$('width', this).text(),
							id:	$('id', this).text()
						});
						$('#'+news_id+' div.image').append(ul.append($('<li>').append(image)));
					}
				);
				$('#'+news_id+' div.image > img').remove();

				function itemFirstInHandler(carousel, li, idx, state) {
					if(state!='init') return;
					$('#'+news_id+' #image-navigation a').click(function() {
						clicked = $(this);
						clicked.blur();
						carousel.scroll(parseInt(clicked.find('img').attr('id').match(/\d+$/)));
						return false;
					});
					$('#'+news_id+' #position-navigation a').click(function() {
						clicked = $(this);
						clicked.blur();
						carousel.scroll(parseInt(clicked.parent('li').attr('id').match(/\d+$/)));
						return false;
					});
				}

				$('#'+news_id+' div.image').jcarousel({
					itemVisible: 1,
					itemScroll: 1,
					itemStart: current_image_pos,
					wrap: true,
					wrapPrev: true,
					scrollAnimation: 'slow',
					autoScroll: 0,
					buttonNextHTML: '<a rel="next" href="#next" class="next" accesskey="'+$next_accesskey+'">Next</a>',
					buttonPrevHTML: '<a rel="prev" href="#prev" class="prev" accesskey="'+$prev_accesskey+'">Previous</a>',
					itemVisibleInHandler: {
						onBeforeAnimation: function(carousel,li,idx,state) {
							if(state!='init') return;
							if(idx==current_image_pos) {
								if(total_images==current_image_pos) {
									// add pulsate to the previous
									$(li).parents('div.image').find('a[@rel=prev]').addClass('pulsate-prev');
								}
								else {
									// we're not at the end, so add the pulsate to the next link
									$(li).parents('div.image').find('a[@rel=next]').addClass('pulsate-next');
								}
								$('div.image a.pulsate-next, div.image a.pulsate-prev').Pulsate(200,3,function(){
									$(this).removeClass('pulsate-next').removeClass('pulsate-prev');
								});
							}
						},
						onAfterAnimation: function(carousel,li,idx,state) {
							image_id = parseInt($(li).find('img').attr('id').match(/\d+$/));
							$('#image-navigation,#position-navigation').find('li').each(function() {
								$(this).removeClass('active');
							});
							$('#image-navigation img#s'+image_id).parents('li').addClass('active');
							$('#position-navigation li#p'+image_id).addClass('active');

							if(idx==1) {
								$(li).addClass('start').addClass('first');
								$('#content-primary div.image a.jcarousel-prev').addClass('start').addClass('fast-forward');
								$('#content-primary div.image a.jcarousel-next').removeClass('reset').removeClass('end').removeClass('rewind');
							}
							else if(idx==total_images) {
								$(li).addClass('end').addClass('last');
								$('#content-primary div.image a.jcarousel-next').addClass('reset').addClass('end').addClass('rewind');
								$('#content-primary div.image a.jcarousel-prev').removeClass('start').removeClass('fast-forward');
							}
							else {
								$(li).removeClass('end').removeClass('last').removeClass('start').removeClass('first');
								$('#content-primary div.image a.jcarousel-next').removeClass('reset').removeClass('end').removeClass('rewind');
								$('#content-primary div.image a.jcarousel-prev').removeClass('start').removeClass('fast-forward');
							}
						}
					},
					itemFirstInHandler: itemFirstInHandler
				});

				function mycarousel_initCallback(carousel) {
					// Pause autoscrolling if the user moves with the cursor over the clip.
					carousel.clip.hover(function() {
						carousel.stopAuto();
					}, function() {
						carousel.startAuto();
					});
				};
				$('#entry-meta-related-photos').jcarousel({
					itemVisible: 2,
					itemScroll: 1,
					wrap: false,
					wrapPrev: false,
					scrollAnimation: 'slow',
					autoScroll: 1,
					auto: 10,
					buttonNextHTML: '<a rel="next" href="#next" class="next">Next</a>',
					buttonPrevHTML: '<a rel="prev" href="#prev" class="prev">Previous</a>',
					initCallback: mycarousel_initCallback
				});
			}
		});
		}
	}
);
