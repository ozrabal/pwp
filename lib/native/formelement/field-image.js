var ds = ds || {};
var b;
var d;

( function( $ ) {
    var data_attributes;
    var mime;
    var title;
    var media;
    b = '.open-media-button';
    d = $(b).next();
    ds.media = media = {
	buttonId: b,
	detailsContainerId: d,
	frame: function(mime) {
	    this._frame = wp.media( {
		title: data_attributes.title,
		button: {
		    text: data_attributes.select
		},
		multiple: false,
		library: {
		    type : data_attributes.mime
		}
	    } );
	    this._frame.on( 'ready', this.ready );
	    this._frame.state( 'library' ).on( 'select', this.select );
	    return this._frame;
	},
	ready: function() {
	    ds.media.frame().close(mime);
	},
	select: function() {
	    var settings = wp.media.view.settings,
	    selection = this.get( 'selection' ).single();
	    media.showAttachmentDetails( selection );
	},
	showAttachmentDetails: function( attachment ) {
	    var details = $( '#'+media.detailsContainerId );
	    $( 'input', details ).each( function() {
		var key = $( this ).attr( 'id' ).replace( 'attachment-', '' );
		$( this ).val( attachment.get( key ) );
		media.frame(mime).close(mime);
	    } );
	    media.frame(mime).close(mime);
	    var sizes = attachment.get( 'sizes' );
	    if(sizes){
		$( 'img',details ).attr( 'src', sizes.thumbnail.url );
	    }
	},
	init: function() {
	    $( ds.media.buttonId ).on( 'click', function( e ) {
		data_attributes=$(this).data();
                var t = $(this).parent().find('div').attr('id');
		media.detailsContainerId = t;
		media.frame(mime).open(mime);
	    });
	    $( '.remove-media-button' ).on( 'click', function( e ) {
		e.preventDefault();
		var default_image = $(this).parent().find('img').attr('data-src-default');
		$(this).parent().find('img').attr('src',default_image);
		$(this).parent().find('input').attr('value','');
	    });
	}
    };
    $( media.init );
} )( jQuery );