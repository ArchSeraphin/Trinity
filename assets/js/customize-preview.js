( function( api ) {
  api( 'trinity_logo_max_height', function( value ) {
    value.bind( function( newValue ) {
      var height = parseInt( newValue, 10 );

      if ( Number.isNaN( height ) || height <= 0 ) {
        return;
      }

      document.querySelectorAll( '.site-logo img' ).forEach( function( img ) {
        img.style.height = height + 'px';
        img.style.width = 'auto';
        img.style.maxHeight = 'none';
      } );
    } );
  } );

  api( 'display_title_and_tagline', function( value ) {
    value.bind( function( isVisible ) {
      document.querySelectorAll( '.site-title-wrapper' ).forEach( function( wrapper ) {
        wrapper.style.display = isVisible ? '' : 'none';
      } );
    } );
  } );
} )( wp.customize );
