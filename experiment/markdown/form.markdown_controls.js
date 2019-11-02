jQuery(function($) {
	$.fn.extend({
		markdown_controls: function(options) {
			var settings = jQuery.extend({
				'class'		: 'markdown-wysiwyg-enabled',
				'controls'	: ['Strong','Emphasis','Underline','|','Unordered','Ordered','|','Link'],
				//'headings'	: [1,2,3,4,5,6],
				'help'		: {
					'text'	: 'Markdown',
					'title'	: 'Help?',
					'link'	: 'http://daringfireball.net/projects/markdown/syntax'
				}
			},options);
		
			return this.each(
				function() {
					var $el = $(this);
					var o = $.metadata ? $.extend({}, settings, $el.metadata()) : settings;
					var $div = $('<div></div>').addClass(o['class']).attr('id','markdown-wysiwyg-'+$el.attr('id'));
					var $control_ul = $('<ul></ul>');
					
					var control_length = o['controls'].length;
					for(i=0; i<control_length; i++) {
						var $control_li = $('<li></li>');
						if(o['controls'][i]=='|' || o['controls'][i].toLowerCase=='pipe') {
							$control_li.text('|');
							$control_li.addClass('separator');
						}
						else {
							var $control_a = $('<a></a>').text(o['controls'][i]).attr({'href':'#'+o['controls'][i].toLowerCase(), 'title':o['controls'][i]});
							var li_class = o['controls'][i].toLowerCase();
							$control_li.append($control_a);
							$control_li.addClass('replaced');
							$control_li.addClass('markdown-'+li_class);
						}
						$control_ul.append($control_li);
					}
					
					if(o['headings']) {
						var $control_li = $('<li></li>').text('Headings').addClass('markdown-headings');
						var $headings_ul = $('<ul></ul>');
						var headings_length = o['headings'].length;
						for(i=0; i<headings_length; i++) {
							var $headings_li = $('<li></li>').addClass('h'+o['headings'][i]).addClass('replaced');
							var $headings_a = $('<a></a>').text('H'+o['headings'][i]).attr({'href':'#h'+o['headings'][i], 'title':'Heading '+o['headings'][i]});
							$headings_li.append($headings_a);
							$headings_ul.append($headings_li);
						}
						$control_li.append($headings_ul);
						$control_ul.append($control_li);
					}
					
					
					$div.prepend($control_ul).find('ul li a').click(function(){
						var $control_click = $(this);
						var $control_area = $control_click.parents('div').next('textarea');
						var text_value = $control_area.val();
						var text_selection = $.trim(text_value.substring($control_area.get(0).selectionStart,$control_area.get(0).selectionEnd));  
						var control_type = $control_click.text().toLowerCase();
						var text_output = $control_click.text();
						var text_update = '';
						
						if(text_selection=='') {
							// the selection is empty.
							// if prompt: ask first for TEXT then URL
							// else just place the correct syntax with cursor inbetween them. eg. **|** for strong
							if(control_type=='link') {
								var link_text_prompt = prompt('Enter text','');
								text_update = '['+link_text_prompt+']';
							}
							else if(control_type=='emphasis') {
								text_update = '**';
							}
							else if(control_type=='strong') {
								text_update = '****';
							}
						}
						else {
							// if prompt: ask for URL
							// else place syntax around selected text. eg. **selected text**
							if(control_type=='link') {
								text_update = '['+text_selection+']';
							}
							else if(control_type=='emphasis') {
								text_update = '*'+text_selection+'*';
							}
							else if(control_type=='strong') {
								text_update = '**'+text_selection+'**';
							}
						}
						
						if(control_type=='link') {
							var link_href_prompt = prompt('Enter link','');
							text_update += '('+link_href_prompt+')';
						}

						$control_area.val(text_value + text_update);
						$control_click.get(0).blur();
						return false;
					});
					
					if(o['help'] && o['help']['text']) {
						var $help_div = $('<div></div>').addClass('help');
						if(o['help']['link']) {
							var $help_a = $('<a></a>').text(o['help']['text']).attr('href',o['help']['link']);
							$help_div.append($help_a);
						}
						else $help_div.text(o['help']['text']);
						if(o['help']['title']) $help_div.attr('title',o['help']['title']);
						$div.prepend($help_div);
					}
					//$el.wrap($div);
					$el.before($div);
					
				}
			);
		}
	});
});