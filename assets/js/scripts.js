( function() {
  var initContactModal = function() {
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
  };

  var initPhotoLoadMore = function() {
    var section = document.querySelector( '.home-photo-grid[data-load-more="true"]' );

    if ( ! section || typeof TrinityLoadMore === 'undefined' ) {
      return;
    }

    var button = section.querySelector( '#photo-load-more' );
    var grid = section.querySelector( '#photo-grid' );
    var filtersForm = section.querySelector( '#photo-filters' );

    if ( ! grid ) {
      return;
    }

    var message = section.querySelector( '.home-photo-grid__message' );

    if ( ! message ) {
      message = document.createElement( 'p' );
      message.className = 'home-photo-grid__message';
      message.setAttribute( 'aria-live', 'polite' );
      if ( button && button.parentNode ) {
        button.parentNode.insertBefore( message, button.nextSibling );
      } else if ( grid.parentNode ) {
        grid.parentNode.appendChild( message );
      }
    }

    var perPage = parseInt( section.getAttribute( 'data-per-page' ), 10 ) || 8;
    var currentPage = parseInt( section.getAttribute( 'data-current-page' ), 10 ) || 1;
    var maxPages = parseInt( section.getAttribute( 'data-max-pages' ), 10 ) || 1;
    var isLoading = false;
    var currentFilters = {
      category: '',
      format: '',
      order: 'desc',
    };

    var filterWidgets = [];

    var closeAllFilters = function( exception ) {
      filterWidgets.forEach( function( widget ) {
        if ( exception && widget === exception ) {
          return;
        }
        widget.classList.remove( 'is-open' );
        var widgetToggle = widget.querySelector( '.photo-filter__toggle' );
        if ( widgetToggle ) {
          widgetToggle.setAttribute( 'aria-expanded', 'false' );
        }
      } );
    };

    var resetGrid = function() {
      currentPage = 0;
      maxPages = Number.MAX_SAFE_INTEGER;
      grid.innerHTML = '';
      section.setAttribute( 'data-current-page', '0' );
      section.setAttribute( 'data-max-pages', String( maxPages ) );
      clearMessage();
      if ( button ) {
        button.classList.remove( 'is-hidden' );
        button.removeAttribute( 'hidden' );
      }
    };

    var setLoadingState = function( state ) {
      isLoading = state;
      if ( button ) {
        button.disabled = state;
        button.classList.toggle( 'is-loading', state );
        if ( state ) {
          button.setAttribute( 'aria-busy', 'true' );
        } else {
          button.removeAttribute( 'aria-busy' );
        }
      }
    };

    var updateButtonVisibility = function() {
      if ( ! button ) {
        return;
      }
      if ( currentPage >= maxPages ) {
        button.classList.add( 'is-hidden' );
        button.setAttribute( 'hidden', 'hidden' );
      } else {
        button.classList.remove( 'is-hidden' );
        button.removeAttribute( 'hidden' );
      }
    };

    updateButtonVisibility();

    var handleError = function( text ) {
      message.textContent = text;
      message.classList.add( 'is-error' );
    };

    var clearMessage = function() {
      message.textContent = '';
      message.classList.remove( 'is-error' );
    };

    var loadMore = function( nextPage, options ) {
      if ( isLoading || nextPage > maxPages ) {
        return;
      }

      setLoadingState( true );
      clearMessage();

      var formData = new FormData();
      formData.append( 'action', 'trinity_load_more_photos' );
      formData.append( 'nonce', TrinityLoadMore.nonce );
      formData.append( 'page', nextPage );
      formData.append( 'per_page', perPage );
      formData.append( 'category', options && options.category ? options.category : currentFilters.category );
      formData.append( 'format', options && options.format ? options.format : currentFilters.format );
      formData.append( 'order', options && options.order ? options.order : currentFilters.order );

      fetch( TrinityLoadMore.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        body: formData,
      } )
        .then( function( response ) {
          if ( ! response.ok ) {
            throw new Error( 'network_error' );
          }
          return response.json();
        } )
        .then( function( data ) {
          if ( ! data || ! data.success ) {
            throw new Error( data && data.data && data.data.message ? data.data.message : 'unknown_error' );
          }

          if ( data.data && data.data.html ) {
            grid.insertAdjacentHTML( 'beforeend', data.data.html );
          }

          currentPage = nextPage;
          section.setAttribute( 'data-current-page', String( currentPage ) );

          if ( data.data && typeof data.data.maxPages !== 'undefined' ) {
            maxPages = parseInt( data.data.maxPages, 10 ) || maxPages;
            section.setAttribute( 'data-max-pages', String( maxPages ) );
          }

          updateButtonVisibility();
        } )
        .catch( function( error ) {
          var text = TrinityLoadMore.strings && TrinityLoadMore.strings.error ? TrinityLoadMore.strings.error : 'Error';

          if ( error && error.message && error.message !== 'network_error' && error.message !== 'unknown_error' ) {
            text = error.message;
          }

          maxPages = currentPage;
          section.setAttribute( 'data-max-pages', String( maxPages ) );
          updateButtonVisibility();
          handleError( text );
        } )
        .finally( function() {
          setLoadingState( false );
        } );
    };

    if ( button ) {
      button.addEventListener( 'click', function() {
        if ( isLoading ) {
          return;
        }
        loadMore( currentPage + 1 );
      } );
    }

    if ( filtersForm ) {
      filterWidgets = Array.prototype.slice.call( filtersForm.querySelectorAll( '.photo-filter' ) );

      var hiddenInputs = filtersForm.querySelectorAll( 'input[type="hidden"]' );

      hiddenInputs.forEach( function( input ) {
        if ( input && input.name ) {
          var normalized = input.value || ( input.name === 'order' ? 'desc' : '' );
          currentFilters[ input.name ] = normalized;
        }
      } );

      filterWidgets.forEach( function( widget ) {
        var toggle = widget.querySelector( '.photo-filter__toggle' );
        var options = widget.querySelectorAll( '.photo-filter__option' );
        var hiddenInput = widget.querySelector( 'input[type="hidden"]' );
        var textSpan = toggle ? toggle.querySelector( '.photo-filter__text' ) : null;
        var defaultLabel = textSpan ? textSpan.getAttribute( 'data-default-label' ) || textSpan.textContent : '';
        var filterName = hiddenInput ? hiddenInput.name : '';

        var setSelectedState = function( option ) {
          options.forEach( function( item ) {
            item.classList.remove( 'is-selected' );
            item.setAttribute( 'aria-selected', 'false' );
          } );
          option.classList.add( 'is-selected' );
          option.setAttribute( 'aria-selected', 'true' );
        };

        var updateLabel = function( option, value ) {
          if ( ! textSpan ) {
            return;
          }
          if ( value ) {
            textSpan.textContent = option.textContent;
          } else {
            textSpan.textContent = defaultLabel || option.textContent;
          }
        };

        var applySelection = function( option ) {
          if ( ! hiddenInput ) {
            return;
          }

          var value = option.getAttribute( 'data-value' ) || '';
          var previousValue = hiddenInput.value;

          hiddenInput.value = value;
          setSelectedState( option );
          updateLabel( option, value );
          widget.classList.remove( 'is-open' );
          if ( toggle ) {
            toggle.setAttribute( 'aria-expanded', 'false' );
            toggle.focus();
          }

          var normalized = value || ( filterName === 'order' ? 'desc' : '' );
          currentFilters[ filterName ] = normalized;

          if ( value === previousValue ) {
            return;
          }

          resetGrid();
          loadMore( 1, currentFilters );
        };

        var initializeSelection = function() {
          if ( ! hiddenInput ) {
            return;
          }

          var currentValue = hiddenInput.value || '';
          var matched = null;

          options.forEach( function( option ) {
            var optionValue = option.getAttribute( 'data-value' ) || '';
            if ( matched === null && optionValue === currentValue ) {
              matched = option;
            }
          } );

          if ( ! matched && options.length ) {
            matched = options[0];
          }

          if ( matched ) {
            hiddenInput.value = matched.getAttribute( 'data-value' ) || '';
            setSelectedState( matched );
            updateLabel( matched, hiddenInput.value );
            currentFilters[ filterName ] = hiddenInput.value || ( filterName === 'order' ? 'desc' : '' );
          }
        };

        if ( toggle ) {
          toggle.addEventListener( 'click', function( event ) {
            event.preventDefault();
            var isOpen = widget.classList.contains( 'is-open' );
            if ( isOpen ) {
              widget.classList.remove( 'is-open' );
              toggle.setAttribute( 'aria-expanded', 'false' );
            } else {
              closeAllFilters( widget );
              widget.classList.add( 'is-open' );
              toggle.setAttribute( 'aria-expanded', 'true' );
              var selectedOption = widget.querySelector( '.photo-filter__option.is-selected' );
              if ( selectedOption ) {
                selectedOption.focus();
              }
            }
          } );
        }

        options.forEach( function( option ) {
          option.addEventListener( 'click', function( event ) {
            event.preventDefault();
            applySelection( option );
          } );

          option.addEventListener( 'keydown', function( event ) {
            if ( event.key === 'Enter' || event.key === ' ' ) {
              event.preventDefault();
              applySelection( option );
            }
          } );
        } );

        initializeSelection();
      } );

      document.addEventListener( 'click', function( event ) {
        if ( filtersForm && ! filtersForm.contains( event.target ) ) {
          closeAllFilters();
        }
      } );

      document.addEventListener( 'keydown', function( event ) {
        if ( event.key === 'Escape' ) {
          closeAllFilters();
        }
      } );
    }
  };

  var onReady = function() {
    initContactModal();
    initPhotoLoadMore();
  };

  if ( document.readyState === 'loading' ) {
    document.addEventListener( 'DOMContentLoaded', onReady );
  } else {
    onReady();
  }
}() );
