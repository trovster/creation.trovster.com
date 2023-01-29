jQuery(function($) {
	$.fn.extend({
		external_links: function(options) {
			var settings = jQuery.extend({
				'class'	: 'external',
				'rel'	: 'external',
				'title'	: '[Opens in a new window or tab]'
			},options);
			
			return this.each(
				function() {
					var $this_anchor = $(this);
					if($this_anchor.is('a')) {
						$this_anchor.attr({'target':'_blank', 'rel':settings['rel']}).addClass(settings['class']);
						
						if($this_anchor.is('[@title]')) $this_anchor.attr({'title':$this_anchor.attr('title')+' '+settings['title']});
						else $this_anchor.attr({'title':settings['title']});
					}
				}
			);
		}
	});
});

$(document).ready(
	function() {
		$('body').addClass('javascript-enabled');
		
		$('a.scrollto, #footer .top a').click(function(){
			var $my_id = $($(this).attr('href'));
			$my_id.ScrollTo('normal');
			// need the callback function to apply the following below, when scrolling is complete.
			//$my_id.find(' > form:first-child :input:first').focus();
			//$my_id.blur();
			return false;
		});
		
		if($(window).width()>1024) {
			$('body').addClass('wide');
			$.cookie('c_uk_size', 'wide', {expires: 30, path: '/'});
		}
		else {
			$('body').removeClass('wide');
			$.cookie('c_uk_size', null);
		}
		$(window).resize(function(){
			if($(window).width()>1024) {
				$('body').addClass('wide');
				$.cookie('c_uk_size', 'wide', {expires: 30, path: '/'});
			}
			else {
				$('body').removeClass('wide');
				$.cookie('c_uk_size', null);
			}
		});
		
		if($('#hello').length && $.fn.flash) {
			$('<div>').addClass('flash').appendTo($('#hello')).flash({
				src: '/css/images/hello.swf',
				width: 720,
				height: 124
			},{
				update: false
			});
		}
		
		$('input[@type=submit]').hover(
			function(){
				$(this).parents('li').addClass('hover');
			},
			function(){
				$(this).parents('li').removeClass('hover');
			}
		);
		
		/*
		if($.tableSorter) {
			$('#sitemap-table,#pricelist').tableSorter({
				sortClassAsc: 'headerSortUp',
				sortClassDesc: 'headerSortDown',
				highlightClass: 'highlight',
				headerClass: 'header',
				stripingRowClass: ['even','odd']
			});
		}
		*/
		if($.fn.quicksearch) {
			$('table#telephone-numbers tbody tr').quicksearch({
				stripeRowClass: ['even', 'odd'],
				position: 'before',
				attached: 'table',
				formId: 'search-telephone',
				labelText: 'Search the table:',
				focusOnLoad: true
			});
			$('#search-telephone input[type=text]').attr('type','search').addClass('safari');
		}
		
		if($('body').is('.eshot')) {
			
			function eshot_img_details(this_el) {
				if(this_el.is('dd')) $parent_dd = this_el.prev('dt');
				else $parent_dd = this_el;
				
				el_id = $parent_dd.attr('class').match(/es_([0-9])+/)[1];
				href_attr = $parent_dd.find('a').attr('href').match( /\/([^\/]+)\/?$/)[1];
				img_src = '['+el_id+']_'+href_attr+'_thumb.gif';
				img_path = '/images/eshots/';
				img_id = 'img_'+el_id;
				return {'src':img_src,'path':img_path,'id':img_id};
			}
			function eshot_img_on() {
				img_map = eshot_img_details($(this));
				$('#'+img_map['id']).parent('li').show();
			}
			function eshot_img_off() {
				img_map = eshot_img_details($(this));
				$('#'+img_map['id']).parent('li').hide();
			}
		
			$('<div>').addClass('column last').attr('id','eshots-preview').insertAfter($('#eshots-archive'));
			$('<h3>Eshot <em>Preview</em></h3>').appendTo($('#eshots-preview'));
			$('<ul>').appendTo($('#eshots-preview'));
			$('#eshots-archive dd').each(function(){
				img_map = eshot_img_details($(this));
				img_element = $('<img>').attr({
					'src':img_map['path']+img_map['src'],
					'id':img_map['id']
				});
				li_element = $('<li>').hide();
				img_element.appendTo(li_element);
				li_element.appendTo($('#eshots-preview ul'));
				
			}).hover(eshot_img_on,eshot_img_off).prev('dt').hover(eshot_img_on,eshot_img_off);
		}
		
		$('div.selection dt').hover(
			function() {
				$this_anchor = $(this).find('a');
				$(this).addClass('next_to_hover').next('dd').addClass('next_to_hover').attr('title',$this_anchor.attr('title'));
				window.status = $this_anchor.attr('href');
			},
			function() {
				$(this).removeClass('next_to_hover').next('dd').removeClass('next_to_hover');
				window.status = '';
			}
		).click(
			function() {
				window.location = $(this).find('a').attr('href');
			}
		);
		$('div.selection dd').hover(
			function() {
				$this_anchor = $(this).prev('dt').find('a')
				$(this).addClass('next_to_hover').attr('title',$this_anchor.attr('title')).prev('dt').addClass('next_to_hover');
				window.status = $this_anchor.attr('href');
			},
			function() {
				$(this).removeClass('next_to_hover').prev('dt').removeClass('next_to_hover');
				window.status = '';
			}
		).click(
			function() {
				window.location = $(this).prev('dt').find('a').attr('href');
			}
		);
		$('div.selection dt.active dd').addClass('active');
	}
);