( function( api, $ ) {
  api.bind( 'ready', function() {
    var control = api.control( 'trinity_logo_max_height' );

    if ( ! control ) {
      return;
    }

    var ensureValueDisplay = function( value ) {
      var $input = control.container.find( 'input[type="range"]' );

      if ( ! $input.length ) {
        return;
      }

      var $display = control.container.find( '.trinity-range-value' );

      if ( ! $display.length ) {
        $display = $( '<span class="trinity-range-value"></span>' );
        $display.insertAfter( $input );
      }

      $display.text( value + 'px' );
    };

    control.container.on( 'input change', 'input[type="range"]', function() {
      ensureValueDisplay( $( this ).val() );
    } );

    ensureValueDisplay( control.setting.get() );
  } );
} )( wp.customize, jQuery );
