jQuery(function($) {
	$.fn.extend({
		autosave: function(options) {
			var settings = jQuery.extend({
				'delay'		: 30000,
				'message'	: {
					'text'		: 'Saved',
					'id'		: 'autosave-feedback',
					'append'	: 'body'
				}
			},options);
			
			function form_save_function($this_form,form_attrs) {
				var url_update_string = '';
				var send_form_attrs = {};
				
				if(form_attrs['method']=='update' && form_attrs['id']) url_update_string = '&update='+form_attrs['id'];
				
				$.ajax({
					'type'		: form_attrs['type'],
					'url'		: form_attrs['action']+'?save=true&language_response='+form_attrs['datatype']+url_update_string,
					'dataType'	: form_attrs['datatype'],
					'data'		: $this_form.find(':input').serialize(),
					'success'	: function(xml) {
						var identifier = $('response identifier',xml).text();
						var edit_link = $('response edit',xml).text();
						//console.log(xml);
						//console.log(identifier);
						send_form_attrs['sent'] = true;
						if(send_form_attrs['sent']==true && form_attrs['method']=='new' && form_attrs['id']==null) {
							// response ID of the saved post (if POST[action]==add)
							send_form_attrs['id'] = identifier; // returned ID from update...
							send_form_attrs['method'] = 'update';
							
							// append the returned ID to the form, and change the POST[action] = 'update'
							$('<input>').attr({'type':'hidden','name':'identifier'}).addClass('hidden').val(send_form_attrs['id']).appendTo($this_form.find('fieldset.submit-fieldset'));
							$this_form.find('fieldset.submit-fieldset input[@type=hidden][@name=action]').val(send_form_attrs['method']);
							$this_form.find('fieldset.submit-fieldset input[@type=hidden][@name=edit-link]').val(edit_link);
							// append a link to this in the sidebar...
							//edit_link
						}
						$('#'+settings['message']['id']).animate({top: 0, opacity: 1}, 1250).wait(4).then.animate({top: -150, opacity: 0}, 1250);
					}
				});
				send_form_attrs = jQuery.extend(send_form_attrs,form_attrs);
				return send_form_attrs;
			}
			
			return this.each(function() {
				var $this_form = $(this);
				var jquery_selector_id = 'fieldset.submit-fieldset input[@type=hidden][@name=identifier]';
				var jquery_selector_method = 'fieldset.submit-fieldset input[@type=hidden][@name=action]';
				var this_form_attrs = {
					'action'	: $this_form.attr('action'),
					'type'		: $this_form.attr('method').toLowerCase(),
					'datatype'	: 'xml',
					'sent'		: false,
					'id'		: ($this_form.find(jquery_selector_id)) ? $this_form.find(jquery_selector_id).val() : null,
					'method'	: $this_form.find(jquery_selector_method).val().toLowerCase()
				};
				
				//this_form_attrs['action'] = '/_admin/_update/profile_hot-topic.php';
				//alert(settings['delay']);
				$('<div>').attr('id',settings['message']['id']).text(settings['message']['text']).appendTo(settings['message']['append']);				
				
				$this_form.find('dl :input').one('focus', function(){
					// we've focused an input, unset the input binding for this form (need to namespace this).
					$this_form.find('dl :input').unbind('focus');
					// now set the timer, and post data after every xx seconds
					$this_form.save_timer = setInterval(function() {
						this_form_attrs = form_save_function($this_form,this_form_attrs);
					}, settings['delay']);
				});
			});
		}
	});
});