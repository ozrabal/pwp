var ds = ds || {};
var b;
var d;
/**
 * Demo 3
 */




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
var mime;

	//alert(mime);
//			if ( this._frame )
//				return this._frame;

			this._frame = wp.media( {
				title: data_attributes.title,
				button: {
					text: data_attributes.select
				},
				multiple: false,
				library: {
                                    type : data_attributes.mime
//					type: function(){
//                                            mime=$(media.buttonId).data('mime');
//                                            alert();
//                                            return mime;
//                                        },
                                        //type: wp.media.query( { type: obj.SomeMIMETypesfromLocalizedScriptOBJ } )
				}
			} );

			this._frame.on( 'ready', this.ready );

			this._frame.state( 'library' ).on( 'select', this.select );

			return this._frame;
		},

		ready: function() {
			//$( '.media-modal' ).addClass( 'no-sidebar smaller' );
                        ds.media.frame().close(mime);

		},

		select: function() {
			var settings = wp.media.view.settings,
			selection = this.get( 'selection' ).single();

			media.showAttachmentDetails( selection );

		},

		showAttachmentDetails: function( attachment ) {
	    //alert(ds.media.detailsContainerId);

			var details = $( '#'+media.detailsContainerId );
			console.log(attachment);
console.log(media.detailsContainerId);

	$( 'input', details ).each( function() {
				var key = $( this ).attr( 'id' ).replace( 'attachment-', '' );
				$( this ).val( attachment.get( key ) );
				media.frame(mime).close(mime);
			} );
//ds.media.frame().close();

media.frame(mime).close(mime);

			var sizes = attachment.get( 'sizes' );
if(sizes){
			//console.log($( details ));

			$( 'img',details ).attr( 'src', sizes.thumbnail.url );
}



			//$( 'textarea', details ).val( JSON.stringify( attachment, null, 2 ) );
		},

		init: function() {
                  //alert(ds.media.buttonId);
			$( ds.media.buttonId ).on( 'click', function( e ) {
 //media.detailsContainerId = $(this).attr('id');

//			    var   buttonId = b;
//		var detailsContainerId = d;


                            data_attributes=$(this).data();
                            console.log(data_attributes);
                           //mime=$(this).data('mime');
                           //title=$(this).data('title');
                            //alert(mime);

                            //media.type= mime;
                                 // media.init;
				//e.preventDefault();
var t = $(this).parent().find('div').attr('id');
 //alert(t);
 media.detailsContainerId = t;
 //media.detailsContainerId = 'm_'+t+'';
//alert(media.detailsContainerId);


				media.frame(mime).open(mime);
			});


			$( '.remove-media-button' ).on( 'click', function( e ) {
				e.preventDefault();



var default_image = $(this).parent().find('img').attr('data-src-default');



$(this).parent().find('img').attr('src',default_image);

//$(this).parent().find('img').attr('src',null);

//$(this).parent().find('#attachment-id').attr('value',0);

$(this).parent().find('input').attr('value','');
//$(this).hide();
			});

		}
	};





$( media.init );






} )( jQuery );


