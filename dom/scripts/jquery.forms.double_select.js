/**
  * Double Select plugin v0.1
  * Generates two selects, based upon one select with optgroup
  *
  * @name   double_select
  * @author Trevor Morris (trovster)
  * @type   jQuery
  * 
  */

jQuery(function($) {
	$.fn.extend({
		double_select: function(options) {
			
			var settings = jQuery.extend({
				'text': 'Select…'
			},options);
			
			return this.each(
				function() {
					var $double_select = $(this);
					var double_select_id = $double_select.attr('id');
					var double_select_optgroup_id = 'optgroup-'+double_select_id;
					var selected_optgroup = $double_select.find('option[@selected]').parents('optgroup').attr('label');
										
					// optgroup select setup...
					var optgroup_select = document.createElement('select');
					optgroup_select.id = double_select_optgroup_id; optgroup_select.name = double_select_optgroup_id;
					$double_select.parents('dd').append(optgroup_select);

					var optgroup_option = document.createElement('option');
					var optgroup_option_text = document.createTextNode(settings['text']);
					optgroup_option.value = '0';
					optgroup_option.appendChild(optgroup_option_text);
					optgroup_select.appendChild(optgroup_option);

					$double_select.find('optgroup').each(function(i) {
						var $double_select_optgroup = $(this);
						var optgroup_option = document.createElement('option');
						var optgroup_option_text = document.createTextNode($double_select_optgroup.attr('label'));
						optgroup_option.value = $double_select_optgroup.attr('label');
						optgroup_option.appendChild(optgroup_option_text);
						optgroup_select.appendChild(optgroup_option);
						if(selected_optgroup==optgroup_option.value) {
							optgroup_option.selected = 'selected';
						}
					});
					
					// chosen select setup...
					var chosen_select = document.createElement('select');
					chosen_select.id = double_select_id; chosen_select.name = double_select_id;
					$double_select.parents('dd').append(chosen_select);
					
					var optgroup_option = document.createElement('option');
					var optgroup_option_text = document.createTextNode($double_select.find('option:first').text());
					optgroup_option.value = $double_select.find('option:first').attr('value');
					optgroup_option.appendChild(optgroup_option_text);
					chosen_select.appendChild(optgroup_option);
					
					$(chosen_select).change(function(){
						$(this).parents('form:first').submit();
					})
					
					$double_select.find('optgroup[@label="'+selected_optgroup+'"] option').each(function(i) {
						var $option = $(this);
						var optgroup_option = document.createElement('option');
						var optgroup_option_text = document.createTextNode($option.text());
						optgroup_option.value = $option.val();
						optgroup_option.appendChild(optgroup_option_text);
						chosen_select.appendChild(optgroup_option);
					});
					$('#'+double_select_optgroup_id).change(function(){
						var $select = $(this);
						$(chosen_select).empty();
						
						var optgroup_option = document.createElement('option');
						var optgroup_option_text = document.createTextNode($double_select.find('option:first').text());
						optgroup_option.value = $double_select.find('option:first').attr('value');
						optgroup_option.appendChild(optgroup_option_text);
						chosen_select.appendChild(optgroup_option);
						chosen_select.focus();
						
						now_selected_optgroup = $select.val();
						$double_select.find('optgroup[@label="'+now_selected_optgroup+'"] option').each(function(i) {
							var $option = $(this);
							var optgroup_option = document.createElement('option');
							var optgroup_option_text = document.createTextNode($option.text());
							optgroup_option.value = $option.val();
							optgroup_option.appendChild(optgroup_option_text);
							chosen_select.appendChild(optgroup_option);
						});
					});
					
					$(this).hide();
					$double_select.hide();
					$(this).remove();
				}
			);
		}
	});
});