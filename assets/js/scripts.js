( function() {
  var modal = document.getElementById( 'contact-modal' );

  if ( ! modal ) {
    return;
  }

  var body = document.body;
  var dialog = modal.querySelector( '.contact-modal__dialog' );
  var closeButtons = modal.querySelectorAll( '[data-contact-modal-close]' );
  var lastFocusedElement = null;

  var collectTriggers = function() {
    var selectors = [ '[data-contact-modal-open]', '.js-contact-modal', '[href="#contact-modal"]', '#contact-modal-trigger' ];
    var nodes = [];

    selectors.forEach( function( selector ) {
      var found = document.querySelectorAll( selector );

      Array.prototype.forEach.call( found, function( element ) {
        if ( nodes.indexOf( element ) === -1 ) {
          nodes.push( element );
        }
      } );
    } );

    return nodes;
  };

  var getFocusableElements = function() {
    return dialog ? dialog.querySelectorAll(
      'a[href], area[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), ' +
      'select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
    ) : [];
  };

  var trapFocus = function( event ) {
    if ( event.key !== 'Tab' ) {
      return;
    }

    var focusable = getFocusableElements();

    if ( ! focusable.length ) {
      event.preventDefault();
      return;
    }

    var first = focusable[0];
    var last = focusable[ focusable.length - 1 ];

    if ( event.shiftKey && document.activeElement === first ) {
      event.preventDefault();
      last.focus();
    } else if ( ! event.shiftKey && document.activeElement === last ) {
      event.preventDefault();
      first.focus();
    }
  };

  var handleKeydown = function( event ) {
    if ( event.key === 'Escape' ) {
      closeModal();
      return;
    }

    trapFocus( event );
  };

  var openModal = function() {
    if ( modal.classList.contains( 'is-open' ) ) {
      return;
    }

    lastFocusedElement = document.activeElement;
    modal.classList.add( 'is-open' );
    modal.setAttribute( 'aria-hidden', 'false' );
    body.classList.add( 'contact-modal-open' );

    var focusable = getFocusableElements();

    if ( focusable.length ) {
      focusable[0].focus();
    } else if ( dialog ) {
      dialog.focus();
    }

    document.addEventListener( 'keydown', handleKeydown );
  };

  var closeModal = function() {
    if ( ! modal.classList.contains( 'is-open' ) ) {
      return;
    }

    modal.classList.remove( 'is-open' );
    modal.setAttribute( 'aria-hidden', 'true' );
    body.classList.remove( 'contact-modal-open' );
    document.removeEventListener( 'keydown', handleKeydown );

    if ( lastFocusedElement && typeof lastFocusedElement.focus === 'function' ) {
      lastFocusedElement.focus();
    }
  };

  collectTriggers().forEach( function( trigger ) {
    trigger.addEventListener( 'click', function( event ) {
      event.preventDefault();
      openModal();
    } );
  } );

  Array.prototype.forEach.call( closeButtons, function( button ) {
    button.addEventListener( 'click', function( event ) {
      event.preventDefault();
      closeModal();
    } );
  } );

  modal.addEventListener( 'click', function( event ) {
    if ( event.target === modal ) {
      closeModal();
    }
  } );

  if ( dialog ) {
    dialog.addEventListener( 'click', function( event ) {
      event.stopPropagation();
    } );
  }
}() );
