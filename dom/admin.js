$(document).ready(
	function() {
		// .Sortable()
		//$('#news-article-update .multiple').find('.position.select').addClass('hidden'); // hide the position select boxes
		$('.view-online a[@rel=bookmark]').external_links();
		/*
		$('form.save').autosave({
			'delay' : 20000
		});
		*/
		
		if($.fn.pstrength) {
			$('#password-required').focus().pstrength({
				verdects : ['very weak', 'weak', 'medium', 'strong', 'stronger'],
				scores : [8,16,25,35,45]
			});
		}
		/*
		$('div.info-box ul li:not(.active)').parents('ul').hide().prev('h4').click(function(){
			$(this).next('ul').slideToggle('fast');
		});
		*/
		$('body.comments div.info-box h4 + ul').hide().prev('h4').addClass('clickable').click(function(){
			$(this).next('ul').slideToggle('fast');
		});
		$('body.comments div.info-box ul li.active').parents('ul').show();

	}
);