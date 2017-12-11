;(function ( $, window, document, undefined ) {
	var pluginName = "searchwpTabs",
		defaults = {
			tabs_parent: ".searchwp-tabs-nav",
			panels_parent: ".searchwp-tabs-content",
			class_prefix: "searchwp-tabs-"
		};

	function searchwpTabs ( element, options ) {
		this.element = element;
		this.settings = $.extend( {}, defaults, options );
		this._defaults = defaults;
		this._name = pluginName;
		this.init();
	}

	$.extend(searchwpTabs.prototype, {
		init: function () {
			this.settings.tabs_parent = $(this.element).find(this.settings.tabs_parent);
			this.settings.panels_parent = $(this.element).find(this.settings.panels_parent);
			this.setPanelHeight(this.element, this.settings);
			this.bindTabs(this.element, this.settings);
			this.settings.tabs_parent.find('a:first').click();
		},
		setPanelHeight: function () {
			var max_height = 0,
				this_height = 0;
			this.settings.panels_parent.children().each(function(){
				this_height = $(this).outerHeight();
				if(this_height>max_height){
					max_height=this_height;
				}
			});
			this.settings.panels_parent.height(max_height);
		},
		bindTabs: function () {
			var settings = this.settings;
			settings.tabs_parent.find('a').click(function(){
				settings.panels_parent.children().hide();
				settings.tabs_parent.find('.'+settings.class_prefix+'active').removeClass(settings.class_prefix+'active');
				$($(this).attr('href')).show();
				$(this).parent().addClass(settings.class_prefix+'active')
				return false;
			});
		}
	});

	$.fn[ pluginName ] = function ( options ) {
		this.each(function() {
			if ( !$.data( this, "plugin_" + pluginName ) ) {
				$.data( this, "plugin_" + pluginName, new searchwpTabs( this, options ) );
			}
		});

		return this;
	};

})( jQuery, window, document );
