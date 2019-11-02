$(document).ready(
	function() {
		$('.hentry[@id] .entry-content, .hentry[@id] .entry-summary').editable('/_admin/editable_save.php', {
			'type'			: 'textarea',
			'submit'		: 'OK',
			'cancel'		: 'Cancel',
			'cssclass'		: 'editable-area',
			'loadurl'		: '/_admin/editable_load.php',
			'loadtype'		: 'POST',
			'submitdata'	: function(original, settings) {
				var editable 		= $(this).parents('.hentry').attr('id');
				var editable_type 	= editable.replace(/(_[0-9]+)/,'');
				var editable_id 	= editable.replace(/([ht_|e_])+/,'');
				var editable_area 	= $(this).attr('class').match('(entry-[content|summary|title]+)')[1];
				return {
					'id'	: editable_id,
					'area'	: editable_area,
					'type'	: editable_type
				}
			},
			'loaddata'		: function(original, settings) {
				var editable 		= $(this).parents('.hentry').attr('id');
				var editable_type 	= editable.replace(/(_[0-9]+)/,'');
				var editable_id 	= editable.replace(/([ht_|e_])+/,'');
				var editable_area 	= $(this).attr('class').match('(entry-[content|summary|title]+)')[1];
				return {
					'id'	: editable_id,
					'area'	: editable_area,
					'type'	: editable_type
				}
			}
		});
	}
);


