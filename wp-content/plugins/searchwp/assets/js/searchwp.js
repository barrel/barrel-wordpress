var searchwp_settings_handler = function(){
	(function($){

		var uniqid = function (prefix, more_entropy) {
			// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
			// +    revised by: Kankrelune (http://www.webfaktory.info/)
			// %        note 1: Uses an internal counter (in php_js global) to avoid collision
			// *     example 1: uniqid();
			// *     returns 1: 'a30285b160c14'
			// *     example 2: uniqid('foo');
			// *     returns 2: 'fooa30285b1cd361'
			// *     example 3: uniqid('bar', true);
			// *     returns 3: 'bara20285b23dfd1.31879087'
			if (typeof prefix === 'undefined') {
				prefix = "";
			}

			var retId;
			var formatSeed = function (seed, reqWidth) {
				seed = parseInt(seed, 10).toString(16); // to hex str
				if (reqWidth < seed.length) { // so long we split
					return seed.slice(seed.length - reqWidth);
				}
				if (reqWidth > seed.length) { // so short we pad
					return new Array(1 + (reqWidth - seed.length)).join('0') + seed;
				}
				return seed;
			};

			// BEGIN REDUNDANT
			if (!this.php_js) {
				this.php_js = {};
			}
			// END REDUNDANT
			if (!this.php_js.uniqidSeed) { // init seed with big random int
				this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
			}
			this.php_js.uniqidSeed++;

			retId = prefix; // start with prefix, add current milliseconds hex string
			retId += formatSeed(parseInt(new Date().getTime() / 1000, 10), 8);
			retId += formatSeed(this.php_js.uniqidSeed, 5); // add seed hex string
			if (more_entropy) {
				// for more entropy we add a float lower to 10
				retId += (Math.random() * 10).toFixed(8).toString();
			}

			return retId;
		};

		$(document).tooltip({
			items: ".swp-tooltip,.swp-tooltip-alt",
			content: function(){
				return $($(this).attr('href')).html();
			}
		});

		var excludeSelects = function() {

			function format_term (term) {
				var markup = "<div class='select2-result-repository clearfix'>" +
					"<div class='select2-result-tax__name'>" + $("<div>").text(term.text).html() + "</div></div>";

				return markup;
			}

			function format_term_selection (term) {
				return term.text;
			}

			$('select.swp-exclude-select').each(function(){
				var $el = $(this),
					options = {};

				if( $el.data('searchable') ) {
					options = {
						ajax: {
							url: ajaxurl,
							dataType: 'json',
							delay: 250,
							data: function (params) {
								return {
									q: params.term, // search term
									_ajax_nonce: $el.data('nonce'),
									tax: $el.data('tax'),
									engine: $el.data('engine'),
									action: 'searchwp_get_tax_terms'
								};
							},
							processResults: function (data, params) {
								return {
									results: data.items
								};
							},
							cache: true
						},
						escapeMarkup: function (markup) { return markup; },
						minimumInputLength: 1,
						language: {
							inputTooShort: function () {
								return $('#swp-search-placeholder').text();
							}
						},
						templateResult: format_term,
						templateSelection: format_term_selection
					}
				}

				$el.select2(options);
			});
		};

		excludeSelects();

		var customFieldSelects = function() {

			function format_key (term) {
				var markup = "<div class='select2-result-repository clearfix'>" +
					"<div class='select2-result-tax__name'>" + $("<div>").text(term.text).html() + "</div></div>";

				return markup;
			}

			function format_key_selection (term) {
				return term.text;
			}

			$('.swp-custom-field-select select').each(function(){
				var $el = $(this),
					options = {};

				if( $el.data('searchable') ) {
					options = {
						ajax: {
							url: ajaxurl,
							dataType: 'json',
							delay: 250,
							data: function (params) {
								return {
									q: params.term, // search term
									_ajax_nonce: $el.data('nonce'),
									action: 'searchwp_get_meta_keys'
								};
							},
							processResults: function (data, params) {
								return {
									results: data.items
								};
							},
							cache: true
						},
						escapeMarkup: function (markup) { return markup; },
						minimumInputLength: 1,
						language: {
							inputTooShort: function () {
								return $('#swp-search-placeholder').text();
							}
						},
						templateResult: format_key,
						templateSelection: format_key_selection
					}
				}

				$el.select2(options);
			});
		};

		customFieldSelects();

		var updateTabContentHeights = function( $parent ){
			// first check to make sure the tabs don't exceed the height
			var $parent_tab_content = $parent.find('.swp-tab-content');
			var $parent_tab_pane = $parent_tab_content.find('.swp-tab-pane');
			var $first_tab_pane = $parent_tab_pane.first();
			var $parent_nav = $parent.find('.swp-nav');
			if($parent_nav.height()>$first_tab_pane.height()){
				$first_tab_pane.height($parent_nav.height());
			}

			// make sure our tab content is at least the proper height
			// while doing that, hide each tab pane
			var tallest = 0;
			$parent_tab_pane.each(function(){
				if($(this).height()>tallest){
					tallest = $(this).height();
				}
			});
			$parent_tab_content.height(tallest+50);
		};

		var initTabs = function( $grandparent ){
			$grandparent .find('.swp-tabbable').each(function(){

				var $parent = $(this);

				// prevent clicking labels from toggling the checkbox
				$parent.find('.swp-tabs label').unbind('click').click(function(e){
					e.preventDefault();
				});

				updateTabContentHeights($parent);
				$parent.find('.swp-tab-content .swp-tab-pane').hide();

				// hook the clicks
				$parent.find('.swp-tabs > li').click(function(){
					$parent.find('.swp-tabs > li.swp-tab-active').removeClass('swp-tab-active');
					$parent.find('.swp-tab-content .swp-tab-pane').hide();
					$(this).addClass('swp-tab-active');
					$('#'+$(this).data('swp-engine')).show();
				});

				// make sure the first tab is active
				if(!$parent.find('.swp-tabs .swp-tab-active').length){
					$parent.find('.swp-tabs > li:eq(0)').trigger('click');
				}

			});
		};

		initTabs( $('.swp-default-engine') );
		$('.swp-supplemental-engine').each(function(){
			initTabs( $(this) );
		});

		var $body = $('body');

		// allow addition of custom fields
		$body.on('click','a.swp-add-custom-field', function(){
			_.templateSettings = {
				variable : 'swp',
				interpolate : /\{\{(.+?)\}\}/g
			};

			var template = _.template($('script#tmpl-swp-custom-fields').html());

			var swp = {
				arrayFlag: uniqid( 'swp', true).replace('.',''),
				postType: $(this).data('posttype'),
				engine: $(this).data('engine')
			};

			$(this).parents('tbody').find('tr:last').before(template(swp));

			// apply select2
			$(this).parents('tbody').find('.swp-custom-field:last .swp-custom-field-select select').select2({});

			updateTabContentHeights($(this).parents('.swp-tabbable'));

			return false;
		});

		$body.on('click','.swp-delete',function(){
			$(this).parents('tr').remove();
			return false;
		});

		$body.on('click','.swp-supplemental-engine-edit-trigger',function(){
			$(this).parents('.swp-supplemental-engine').addClass('swp-supplemental-engine-edit');
			updateTabContentHeights($(this).parents('.swp-supplemental-engine'));
			return false;
		});

		$body.on('click','.swp-del-supplemental-engine',function(){
			$(this).parents('.swp-supplemental-engine').remove();
			return false;
		});

		$body.on('click','.swp-add-supplemental-engine',function(e){
			e.preventDefault();
			_.templateSettings = {
				variable : 'swp',
				interpolate : /\{\{(.+?)\}\}/g
			};

			var engineSettingsTemplate = _.template($('script#tmpl-swp-engine').html());
			var supplementalTemplate = _.template($('script#tmpl-swp-supplemental-engine').html());

			var swp = {
				engine: uniqid( 'swpengine', true).replace('.',''),
				engineLabel: 'Supplemental'
			};

			swp.engineSettings = engineSettingsTemplate(swp);

			$(this).parents('.swp-supplemental-engines-wrapper').find('.swp-supplemental-engines').append(supplementalTemplate(swp));
			$(this).parents('.swp-supplemental-engines-wrapper').find('.swp-supplemental-engines .swp-supplemental-engine:last .swp-supplemental-engine-name > a').trigger('click');
			$(this).parents('.swp-supplemental-engines-wrapper').find('.swp-supplemental-engines .swp-supplemental-engine:last .swp-supplemental-engine-name > input').focus().select();
			initTabs( $('.swp-supplemental-engines .swp-supplemental-engine:last' ) );
			excludeSelects();
			customFieldSelects();
			return false;
		});

		// call out weights of -1 because they're usually unintended
		var maybe_weight_warning = function($el){
			if(parseFloat($el.val(),10)<0){
				$el.addClass('searchwp-weight-warning');
			}else{
				$el.removeClass('searchwp-weight-warning');
			}
		};
		$body.on('change keyup', '.swp-engine-weights input', function(){
			maybe_weight_warning($(this));
		});

	})(jQuery);
};
