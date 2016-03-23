( function( $ ){
	wp.customize( 'lattice_homepage_text', function( value ) {
        value.bind( function( to ) {
            $('.home .intro .inside .page-title').html(to);
        });
    });
} )( jQuery )