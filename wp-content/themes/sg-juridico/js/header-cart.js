(function ( $ ) {
	'use strict';

	var HOVER_MEDIA = window.matchMedia( '(hover: hover) and (pointer: fine)' );
	var MOBILE_BREAKPOINT = window.matchMedia( '(max-width: 768px)' );
	var focusableSelectors = 'a[href], area[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
	var $backdrop = null;

	function debounce( fn, delay ) {
		var timer;
		return function () {
			var context = this;
			var args = arguments;
			clearTimeout( timer );
			timer = setTimeout( function () {
				fn.apply( context, args );
			}, delay );
		};
	}

	function getBackdrop() {
		if ( ! $backdrop ) {
			$backdrop = $( '<div class="header-cart-backdrop" aria-hidden="true"></div>' ).appendTo( document.body );
		}
		return $backdrop;
	}

	function isDesktopMode() {
		return HOVER_MEDIA.matches && ! MOBILE_BREAKPOINT.matches;
	}

	function getFocusable( $container ) {
		return $container.find( focusableSelectors ).filter( ':visible' );
	}

	function trapFocus( $container ) {
		var focusable = getFocusable( $container );
		if ( ! focusable.length ) {
			return;
		}
		var first = focusable[0];
		var last = focusable[ focusable.length - 1 ];
		function handleKey( event ) {
			if ( event.key === 'Tab' ) {
				if ( event.shiftKey ) {
					if ( document.activeElement === first ) {
						event.preventDefault();
						last.focus();
					}
				} else if ( document.activeElement === last ) {
					event.preventDefault();
					first.focus();
				}
			}
			if ( event.key === 'Escape' ) {
				closeWrapper( $container.closest( '.header-mini-cart-wrapper' ) );
			}
		}
		$container.data( 'sgCartTrap', handleKey );
		$container.on( 'keydown.sgCartTrap', handleKey );
	}

	function releaseFocus( $container ) {
		var handler = $container.data( 'sgCartTrap' );
		if ( handler ) {
			$container.off( 'keydown.sgCartTrap', handler );
			$container.removeData( 'sgCartTrap' );
		}
	}

	function closeWrapper( $wrapper ) {
		if ( ! $wrapper || ! $wrapper.length ) {
			return;
		}
		var closeFn = $wrapper.data( 'sgCartClose' );
		if ( typeof closeFn === 'function' ) {
			closeFn();
		}
	}

	function closeAllCarts( except ) {
		$( '.header-mini-cart-wrapper.is-open' ).each( function () {
			var $wrapper = $( this );
			if ( except && $wrapper.is( except ) ) {
				return;
			}
			closeWrapper( $wrapper );
		} );
	}

	function setupMiniCart( $wrapper ) {
		if ( $wrapper.data( 'sgCartInit' ) ) {
			return;
		}

		var $toggle = $wrapper.find( '.header-cart-toggle' );
		var $dropdown = $wrapper.find( '[data-cart-dropdown]' );
		var $close = $wrapper.find( '.mini-cart-close' );

		if ( ! $toggle.length || ! $dropdown.length ) {
			return;
		}

		var isOpen = false;
		var currentMode = null;
		var lastFocused = null;
		var hoverOpenTimer = null;
		var hoverCloseTimer = null;

		function applyMode( mode ) {
			currentMode = mode;
			var $bd = getBackdrop();
			if ( mode === 'mobile' ) {
				$wrapper.addClass( 'is-mobile-open' );
				$dropdown.attr( 'aria-modal', 'true' );
				$('body').addClass( 'header-cart-overlay-open' );
				$bd.addClass( 'is-visible' ).off( 'click' ).on( 'click', function () {
					close();
				} );
			} else {
				$wrapper.removeClass( 'is-mobile-open' );
				$dropdown.attr( 'aria-modal', 'false' );
				$bd.removeClass( 'is-visible' ).off( 'click' );
				if ( ! $( '.header-mini-cart-wrapper.is-mobile-open' ).not( $wrapper ).length ) {
					$('body').removeClass( 'header-cart-overlay-open' );
				}
			}
		}

		function focusFirstElement() {
			var focusable = getFocusable( $dropdown );
			if ( focusable.length ) {
				focusable[0].focus();
			} else {
				$dropdown.focus();
			}
		}

		function open( mode ) {
			clearTimeout( hoverOpenTimer );
			clearTimeout( hoverCloseTimer );
			mode = mode || ( isDesktopMode() ? 'desktop' : 'mobile' );
			if ( ! isOpen ) {
				closeAllCarts( $wrapper );
				isOpen = true;
				lastFocused = document.activeElement;
				$wrapper.addClass( 'is-open' );
				$toggle.attr( 'aria-expanded', 'true' );
				$dropdown.removeAttr( 'hidden' );
				trapFocus( $dropdown );
				setTimeout( focusFirstElement, 20 );
			}
			if ( currentMode !== mode ) {
				applyMode( mode );
			}
		}

		function close() {
			if ( ! isOpen ) {
				return;
			}
			clearTimeout( hoverOpenTimer );
			clearTimeout( hoverCloseTimer );
			isOpen = false;
			$wrapper.removeClass( 'is-open is-mobile-open' );
			$toggle.attr( 'aria-expanded', 'false' );
			$dropdown.attr( 'hidden', 'hidden' );
			releaseFocus( $dropdown );
			applyMode( null );
			if ( lastFocused && typeof lastFocused.focus === 'function' ) {
				lastFocused.focus();
			}
			lastFocused = null;
		}

		function scheduleOpen() {
			clearTimeout( hoverOpenTimer );
			hoverOpenTimer = setTimeout( function () {
				open( 'desktop' );
			}, 140 );
		}

		function scheduleClose() {
			clearTimeout( hoverOpenTimer );
			clearTimeout( hoverCloseTimer );
			hoverCloseTimer = setTimeout( function () {
				close();
			}, 180 );
		}

		$toggle.on( 'click', function ( event ) {
			event.preventDefault();
			if ( isOpen ) {
				close();
			} else {
				open();
			}
		} );

		if ( $close.length ) {
			$close.on( 'click', function ( event ) {
				event.preventDefault();
				close();
			} );
		}

		$wrapper.on( 'mouseenter', function () {
			if ( ! isDesktopMode() ) {
				return;
			}
			clearTimeout( hoverCloseTimer );
			scheduleOpen();
		} );

		$wrapper.on( 'mouseleave', function () {
			if ( ! isDesktopMode() ) {
				return;
			}
			scheduleClose();
		} );

		$wrapper.on( 'focusin', function () {
			if ( ! isDesktopMode() ) {
				return;
			}
			clearTimeout( hoverCloseTimer );
			open( 'desktop' );
		} );

		$wrapper.on( 'focusout', function ( event ) {
			if ( ! isDesktopMode() ) {
				return;
			}
			if ( ! $wrapper.has( event.relatedTarget ).length ) {
				scheduleClose();
			}
		} );

		$wrapper.data( 'sgCartInit', true );
		$wrapper.data( 'sgCartOpen', open );
		$wrapper.data( 'sgCartClose', close );
	}

	function initAllMiniCarts() {
		$( '.header-mini-cart-wrapper' ).each( function () {
			setupMiniCart( $( this ) );
		} );
	}

	$( document ).on( 'mousedown', function ( event ) {
		var $target = $( event.target );
		if ( ! $target.closest( '.header-mini-cart-wrapper' ).length ) {
			closeAllCarts();
		}
	} );

	$( document ).on( 'keyup', function ( event ) {
		if ( event.key === 'Escape' ) {
			closeAllCarts();
		}
	} );

	var refreshModes = debounce( function () {
		$( '.header-mini-cart-wrapper.is-open' ).each( function () {
			var $wrapper = $( this );
			var openFn = $wrapper.data( 'sgCartOpen' );
			if ( typeof openFn === 'function' ) {
				openFn();
			}
		} );
	}, 160 );

	if ( typeof MOBILE_BREAKPOINT.addEventListener === 'function' ) {
		MOBILE_BREAKPOINT.addEventListener( 'change', refreshModes );
	} else if ( MOBILE_BREAKPOINT.addListener ) {
		MOBILE_BREAKPOINT.addListener( refreshModes );
	}

	if ( typeof HOVER_MEDIA.addEventListener === 'function' ) {
		HOVER_MEDIA.addEventListener( 'change', refreshModes );
	} else if ( HOVER_MEDIA.addListener ) {
		HOVER_MEDIA.addListener( refreshModes );
	}

	$( window ).on( 'resize', refreshModes );

	$( document ).ready( initAllMiniCarts );
	$( document.body ).on( 'wc_fragments_loaded wc_fragments_refreshed', function () {
		initAllMiniCarts();
		refreshModes();
	} );

})( jQuery );

