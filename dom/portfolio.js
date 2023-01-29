$(document).ready(
	function() {
		var portfolio_id = $('#content-primary > .project').attr('id');
		$next = $('#content-primary .project .image ul.pagination li.next a');
		$prev = $('#content-primary .project .image ul.pagination li.prev a');
		$next_accesskey = $next.size() ? $next.attr('accesskey') : 'x';
		$prev_accesskey = $prev.size() ? $prev.attr('accesskey') : 'z';

		// only remove the elements IF the ajax was successful...
		$('#content-primary .project .image ul.pagination').remove();

		var category = $('#content-navigation li.active a').text().replace(/ /g,'-').toLowerCase(); //section
		var company = $('#content-primary .project h3 em.org').attr('id').replace(/^company-/g,'').replace(/ /g,'-').toLowerCase(); //company
		var detail	= $('div.selection dt.active a').text().replace(/ /g,'-').toLowerCase(); //detail
		var current_image = $('#content-primary .project div.image img').attr('title'); // current image
		var current_image_pos = parseInt($('#content-primary .project div.image img').attr('id').match(/\d+$/)); // current image position 1+
		var total_images = $('#position-navigation li a').size();

		if(!$.browser.safari) {
		$.ajax({
			type: 'GET',
			url: '/_compile/a_portfolio_images/',
			data: 'id='+portfolio_id+'&category='+category+'&company='+company+'&detail='+detail+'&image='+current_image,
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
						$('#content-primary .project div.image').append(ul.append($('<li>').append(image)));
					}
				);
				$('div#content-primary .project div.image > img').remove();
				var total_images = $('div#content-primary .project div.image ul li img').size();

				function itemFirstInHandler(carousel, li, idx, state) {
					if(state!='init') return;
					$('#position-navigation a').click(function() {
						clicked = $(this);
						clicked.blur();
						carousel.scroll(parseInt(clicked.parent('li').attr('id').match(/\d+$/)));
						return false;
					});
				}

				$('#content-primary .project div.image').jcarousel({
					itemVisible: 1,
					itemScroll: 1,
					itemStart: current_image_pos,
					wrap: true,
					wrapPrev: true,
					scrollAnimation: 'slow',
					autoScroll: 0,
					buttonNextHTML: '<a rel="next" href="#next" accesskey="'+$next_accesskey+'">Next</a>',
					buttonPrevHTML: '<a rel="prev" href="#prev" accesskey="'+$prev_accesskey+'">Previous</a>',
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
							$('#position-navigation li').removeClass('active').filter('#p'+image_id).addClass('active');

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
			}
		});
		}
	}
);
