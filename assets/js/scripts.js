( function() {
  var lightboxApi = null;

  var initContactModal = function() {
    var modal = document.getElementById( 'contact-modal' );

    if ( ! modal ) {
      return;
    }

    var body = document.body;
    var dialog = modal.querySelector( '.contact-modal__dialog' );
    var closeButtons = modal.querySelectorAll( '[data-contact-modal-close]' );
    var lastFocusedElement = null;
    var currentReference = '';
    var currentTitle = '';
    var contactQueryKey = modal.getAttribute( 'data-contact-query-key' ) || '';
    var contactQueryValue = modal.getAttribute( 'data-contact-query-value' ) || '';

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

    var getQueryParam = function( key ) {
      if ( ! key ) {
        return '';
      }

      if ( typeof URLSearchParams !== 'undefined' ) {
        var params = new URLSearchParams( window.location.search );
        return params.get( key ) || '';
      }

      var query = window.location.search.replace( '?', '' ).split( '&' );

      for ( var i = 0; i < query.length; i++ ) {
        var parts = query[ i ].split( '=' );
        if ( parts[0] === key ) {
          return decodeURIComponent( parts[1] || '' );
        }
      }

      return '';
    };

    var getFocusableElements = function() {
      return dialog ? dialog.querySelectorAll(
        'a[href], area[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), ' +
        'select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
      ) : [];
    };

    var findReferenceInput = function() {
      var scope = modal.querySelector( '.contact-modal__content' ) || modal;

      if ( ! scope ) {
        return null;
      }

      var form = scope.querySelector( 'form' ) || scope;
      var selectors = [
        '[data-contact-reference-input]',
        'input[name*="ref"]',
        'input[name*="reference"]',
        'input[aria-label*="réf" i]',
        'input[placeholder*="réf" i]',
      ];

      for ( var i = 0; i < selectors.length; i++ ) {
        var input = form.querySelector( selectors[ i ] );

        if ( input ) {
          return input;
        }
      }

      return null;
    };

    var dispatchNativeEvents = function( element ) {
      if ( ! element ) {
        return;
      }

      try {
        if ( typeof Event === 'function' ) {
          element.dispatchEvent( new Event( 'input', { bubbles: true } ) );
          element.dispatchEvent( new Event( 'change', { bubbles: true } ) );
          return;
        }
      } catch ( error ) {
        // Continue vers le fallback legacy.
      }

      if ( document.createEvent ) {
        var inputEvent = document.createEvent( 'Event' );
        inputEvent.initEvent( 'input', true, true );
        element.dispatchEvent( inputEvent );

        var changeEvent = document.createEvent( 'Event' );
        changeEvent.initEvent( 'change', true, true );
        element.dispatchEvent( changeEvent );
      }
    };

    var applyReferencePrefill = function() {
      var target = findReferenceInput();

      if ( ! target ) {
        return;
      }

      target.value = currentReference || '';
      dispatchNativeEvents( target );
    };

    var setReferenceData = function( reference, title ) {
      currentReference = reference || '';
      currentTitle = title || '';

      if ( currentReference ) {
        modal.setAttribute( 'data-photo-reference', currentReference );
      } else {
        modal.removeAttribute( 'data-photo-reference' );
      }

      if ( currentTitle ) {
        modal.setAttribute( 'data-photo-title', currentTitle );
      } else {
        modal.removeAttribute( 'data-photo-title' );
      }

      applyReferencePrefill();
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

      applyReferencePrefill();
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

      if ( window.location.hash === '#contact-modal' ) {
        if ( window.history && typeof window.history.replaceState === 'function' ) {
          window.history.replaceState( '', document.title, window.location.pathname + window.location.search );
        } else {
          window.location.hash = '';
        }
      }

      if ( lastFocusedElement && typeof lastFocusedElement.focus === 'function' ) {
        lastFocusedElement.focus();
      }
    };

    collectTriggers().forEach( function( trigger ) {
      trigger.addEventListener( 'click', function( event ) {
        event.preventDefault();
        var reference = trigger.getAttribute( 'data-contact-ref' ) || trigger.getAttribute( 'data-photo-reference' ) || '';
        var title = trigger.getAttribute( 'data-contact-title' ) || trigger.getAttribute( 'data-photo-title' ) || '';
        var href = trigger.getAttribute( 'href' );

        if ( contactQueryKey && reference ) {
          var currentQueryValue = getQueryParam( contactQueryKey );
          var needsReload = currentQueryValue !== reference;

          if ( needsReload && href ) {
            window.location.href = href;
            return;
          }
        }

        setReferenceData( reference, title );
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

    setReferenceData( '', '' );

    var initialReference = contactQueryValue || getQueryParam( contactQueryKey );

    if ( initialReference ) {
      setReferenceData( initialReference, '' );
    }

    if ( window.location.hash === '#contact-modal' ) {
      openModal();
    }
  };

  var initPhotoLightbox = function() {
    var container = document.getElementById( 'photo-lightbox' );

    if ( ! container ) {
      return null;
    }

    var body = document.body || document.documentElement;
    var imageElement = container.querySelector( '[data-lightbox-image]' );
    var referenceElement = container.querySelector( '[data-lightbox-reference]' );
    var categoryElement = container.querySelector( '[data-lightbox-category]' );
    var closeElements = container.querySelectorAll( '[data-lightbox-close]' );
    var prevButton = container.querySelector( '[data-lightbox-prev]' );
    var nextButton = container.querySelector( '[data-lightbox-next]' );

    var items = [];
    var currentIndex = -1;

    var collectItems = function() {
      items = Array.prototype.slice.call( document.querySelectorAll( '.photo-card' ) );
      items.forEach( function( card, index ) {
        card.dataset.photoIndex = index;
      } );
    };

    var updateMeta = function( value, element ) {
      if ( ! element ) {
        return;
      }

      if ( value ) {
        element.textContent = value;
        element.classList.remove( 'is-empty' );
      } else {
        element.textContent = '';
        element.classList.add( 'is-empty' );
      }
    };

    var updateNavState = function() {
      var atStart = currentIndex <= 0;
      var atEnd = currentIndex >= items.length - 1;

      if ( prevButton ) {
        prevButton.disabled = atStart;
        prevButton.classList.toggle( 'is-disabled', atStart );
      }

      if ( nextButton ) {
        nextButton.disabled = atEnd;
        nextButton.classList.toggle( 'is-disabled', atEnd );
      }
    };

    var setIndex = function( index ) {
      if ( index < 0 || index >= items.length ) {
        return;
      }

      currentIndex = index;
      var card = items[ currentIndex ];

      if ( ! card ) {
        return;
      }

      var data = card.dataset || {};

      if ( imageElement ) {
        if ( data.photoImage ) {
          imageElement.src = data.photoImage;
        }

        imageElement.alt = data.photoTitle || '';
      }

      updateMeta( data.photoReference, referenceElement );
      updateMeta( data.photoCategory, categoryElement );
      updateNavState();
    };

    var handleKeydown = function( event ) {
      if ( event.key === 'Escape' ) {
        close();
      } else if ( event.key === 'ArrowLeft' ) {
        event.preventDefault();
        goPrevious();
      } else if ( event.key === 'ArrowRight' ) {
        event.preventDefault();
        goNext();
      }
    };

    var openFromCard = function( card ) {
      if ( ! card ) {
        return;
      }

      collectItems();

      var index = items.indexOf( card );

      if ( index === -1 ) {
        return;
      }

      container.classList.add( 'is-open' );
      container.setAttribute( 'aria-hidden', 'false' );
      if ( body ) {
        body.classList.add( 'photo-lightbox-open' );
      }

      setIndex( index );
      document.addEventListener( 'keydown', handleKeydown );
    };

    var close = function() {
      if ( ! container.classList.contains( 'is-open' ) ) {
        return;
      }

      container.classList.remove( 'is-open' );
      container.setAttribute( 'aria-hidden', 'true' );

      if ( body ) {
        body.classList.remove( 'photo-lightbox-open' );
      }

      document.removeEventListener( 'keydown', handleKeydown );
      currentIndex = -1;
    };

    var goPrevious = function() {
      if ( currentIndex <= 0 ) {
        return;
      }

      setIndex( currentIndex - 1 );
    };

    var goNext = function() {
      if ( currentIndex >= items.length - 1 ) {
        return;
      }

      setIndex( currentIndex + 1 );
    };

    closeElements.forEach( function( element ) {
      element.addEventListener( 'click', function( event ) {
        event.preventDefault();
        close();
      } );
    } );

    if ( prevButton ) {
      prevButton.addEventListener( 'click', function( event ) {
        event.preventDefault();
        goPrevious();
      } );
    }

    if ( nextButton ) {
      nextButton.addEventListener( 'click', function( event ) {
        event.preventDefault();
        goNext();
      } );
    }

    container.addEventListener( 'click', function( event ) {
      if ( event.target === container ) {
        close();
      }
    } );

    document.addEventListener( 'click', function( event ) {
      var trigger = event.target.closest( '[data-lightbox-trigger]' );

      if ( ! trigger ) {
        return;
      }

      event.preventDefault();

      var card = trigger.closest( '.photo-card' );

      if ( card ) {
        openFromCard( card );
      }
    } );

    collectItems();

    return {
      refresh: collectItems,
      close: close,
    };
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
      if ( lightboxApi && typeof lightboxApi.refresh === 'function' ) {
        lightboxApi.refresh();
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
      var loadedCards = document.querySelectorAll( '.photo-card[data-photo-id]' );
      if ( loadedCards.length ) {
        var excludeIds = [];
        loadedCards.forEach( function( card ) {
          var id = card.getAttribute( 'data-photo-id' );
          if ( id && excludeIds.indexOf( id ) === -1 ) {
            excludeIds.push( id );
          }
        } );
        if ( excludeIds.length ) {
          formData.append( 'exclude', excludeIds.join( ',' ) );
        }
      }

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

          var payload = data.data || {};

          if ( payload.html ) {
            grid.insertAdjacentHTML( 'beforeend', payload.html );
          }

          currentPage += 1;
          section.setAttribute( 'data-current-page', String( currentPage ) );

          if ( typeof payload.maxPages !== 'undefined' ) {
            var parsedMax = parseInt( payload.maxPages, 10 );
            if ( ! Number.isNaN( parsedMax ) && parsedMax >= currentPage ) {
              maxPages = parsedMax;
            }
          } else if ( payload.hasMore === false ) {
            maxPages = currentPage;
          } else if ( maxPages < currentPage + 1 ) {
            maxPages = currentPage + 1;
          }

          if ( payload.hasMore === false ) {
            maxPages = currentPage;
          }

          section.setAttribute( 'data-max-pages', String( maxPages ) );

          updateButtonVisibility();

          if ( payload.hasMore === false && button ) {
            button.classList.add( 'is-hidden' );
            button.setAttribute( 'hidden', 'hidden' );
          }

          if ( lightboxApi && typeof lightboxApi.refresh === 'function' ) {
            lightboxApi.refresh();
          }
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
    lightboxApi = initPhotoLightbox();
    initPhotoLoadMore();
  };

  if ( document.readyState === 'loading' ) {
    document.addEventListener( 'DOMContentLoaded', onReady );
  } else {
    onReady();
  }
}() );
