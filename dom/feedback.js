$(document).ready(
	function() {
		$('.rating ul').each(function(i){
			var $rating_radio = $(this).find('input[@type=radio]');
			if($rating_radio.length==5) {
				$rating_radio.parents('dd.radio:first').addClass('rating_toggle').prev('dt.radio:first').addClass('rating_toggle');
				$('<div></div>').addClass('rating_icon').appendTo($rating_radio.parents('dd.radio:first').prev('dt.radio:first'));
				$rating_radio.each(function(r){
					var $r = $(this);
					//$r.next('label').is(':contains("Satisfied")') || $r.next('label').is(':contains("OK")')
					if($r.is(':checked')) {
						var r_class = 'rating_'+(r+1);
						$r.parents('dd.radio:first').addClass(r_class).prev('dt.radio:first').addClass(r_class);
					}
				}).focus(function(){
					var $r = $(this);
					var rating_number = $r.parents('ul').find('input[@type=radio]').index($r[0])+1;
					var r_class = 'rating_'+rating_number;
					var r_old_class_array = $r.parents('dd.radio:first').attr('class').match(/\s?rating_([0-9]+)\s?/gi);
					var r_old_class = (r_old_class_array && r_old_class_array.length==1) ? r_old_class_array[0] : '';
					$r.blur().parents('dd.radio:first').removeClass(r_old_class).addClass(r_class).prev('dt.radio:first').removeClass(r_old_class).addClass(r_class);
				});
			}
		});
	}
);