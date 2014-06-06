/**
 * Gallery Settings
 */
(function($) {
	var media = wp.media;

	// Wrap the render() function to append controls.
	media.view.Settings.Gallery = media.view.Settings.Gallery.extend({
		render: function() {
			var $el = this.$el;

			media.view.Settings.prototype.render.apply( this, arguments );

			// Append the type template and update the settings.
			$el.append( media.template( 'pwp-gallery-settings' ) );
			media.gallery.defaults.type = 'fancybox'; // lil hack that lets media know there's a type attribute.
			this.update.apply( this, ['type'] );

			// Hide the Columns setting for all types except Default
			 $el.find('.gallery-description').hide();
                        var current = $el.find( 'select[name=type]' ).val();
                       $el.find('#'+current).show();
                         
		    $el.find( 'select[name=type]' ).on( 'change', function () {
				var columnSetting = $el.find( 'select[name=columns]' ).closest( 'label.setting' );
				var columnSetting2 = $el.find( '.link-to' ).closest( 'label.setting' );
				if ( 'fancybox' == $( this ).val() ){
					columnSetting.show();
					columnSetting2.show();
				}else{
					columnSetting.hide();
					columnSetting2.hide();
				}
                                
                                var c = $( this ).val();
                                $('.gallery-description').hide();
                                $('#'+c).show();
                                
			} ).change();


//var $s = this.$('select.columns');
//                if(!$s.find('option[value="0"]').length){
//                    $s.append('<option value="0">0</option>');
//                }



			return this;
		}
	});
})(jQuery);
