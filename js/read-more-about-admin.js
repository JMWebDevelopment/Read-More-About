jQuery( document ).ready( function( $ ) {
	$( '#read-more-add-row' ).on( 'click', function() {
		var row = $( '.read-more-empty-row.screen-reader-text' ).clone( true );
		row.addClass( 'new-row read-more-link-fields' );
		row.removeClass( 'read-more-empty-row screen-reader-text' );
		row.insertAfter( '.read-more-link-fields:last' );
		$( '.new-row .new-field' ).attr( "disabled",false );
		return false;
	} );
  	
	$( '.read-more-remove-row' ).on( 'click', function() {
		$( this ).parents( 'section' ).remove();
		return false;
	} );

	$( ".read_more_about_in_ex" ).change( function() {
        if ( $( this ).val() == "internal" ) {
            $( this ).parents( 'section' ).find( ".internal-link" ).show();
            $( this ).parents( 'section' ).find( ".external-link" ).hide();
            $( this ).parents( 'section' ).find( ".external-title" ).hide();
        } else {
        	$( this ).parents( 'section' ).find( ".internal-link" ).hide();
            $( this ).parents( 'section' ).find( ".external-link" ).show();
            $( this ).parents( 'section' ).find( ".external-title" ).show();
        }
    } );

	$( '#read-more-repeatable-fieldset-one' ).sortable( {
		opacity: 0.6,
		revert: true,
		cursor: 'move',
		items: 'section'
	} );
} );