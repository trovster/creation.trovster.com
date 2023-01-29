$(document).ready(
	function() {
		if($.browser.safari) $('input[@type=text]').attr('type','search').addClass('safari');

		$('<a>').text('Expand textarea').addClass('expand-link').attr('href','#extend').appendTo('dd.extend');
		$('dd.extend').addClass('expand-container');
		$('a.expand-link').click(
			function() {
				var $ta = $(this).prev('textarea.extend');
				$ta.animate({
					height: $ta.innerHeight()+150
				}, 'slow');
				$(this).get(0).blur();
				return false;
			}
		);
		$(':input').focus(
			function(){
				$(this).parent('dd').prev('dt').addClass('focus');
			}
		).blur(
			function(){
				$(this).parent('dd').prev('dt').removeClass('focus');
			}
		);
		
		if($.fn.validate) {
			$("#contact-feedback form").validate({
				event: 'blur',
				rules: {
					'name-required': 'required',
					'email-required': {
						required: true,
						email: true
					},
					'message-required': 'required'
				},
				messages: {
					'name-required': '<em class="error">Missing</em>',
					'email-required': {
						required: '<em class="error">Missing</em>',
						email: '<em class="error invalid">Invalid</em>'
					},
					'message-required': '<em class="error">Missing</em>'
				},
				errorPlacement: function(error, element) {
					error.appendTo($('label[@for="'+element.attr('id')+'"]').parent('dt'));
				}
			});
	
			$("#eshots-unsubscribe form").validate({
				event: 'blur',
				rules: {
					'email-required': {
						required: true,
						email: true
					}
				},
				messages: {
					'email-required': {
						required: '<em class="error">Missing</em>',
						email: '<em class="error invalid">Invalid</em>'
					}
				},
				errorPlacement: function(error, element) {
					error.appendTo($('label[@for="'+element.attr('id')+'"]').parent('dt'));
				}
			});
		}
	}
);