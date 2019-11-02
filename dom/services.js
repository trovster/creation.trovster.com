$(document).ready(
	function() {
		$('#services-listing dl').Accordion({
			active: false,
			selectedClass: 'selected',
			alwaysOpen: false
		});
		$('#services-listing dl dt').hover(
			function(){
				$(this).addClass('hovered');
			},
			function(){
				$(this).removeClass('hovered');
			}
		);
	}
);