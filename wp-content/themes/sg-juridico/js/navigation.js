(function() {
	'use strict';

	const navigation = document.querySelector('.site-navigation');
	const toggle = document.querySelector('.menu-toggle');
	const backdrop = document.querySelector('.site-navigation-backdrop');
	const closeBtn = navigation ? navigation.querySelector('.mobile-nav-close') : null;
	const header = document.querySelector('.site-header');
	const focusableSelectors = 'a[href], area[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
	const desktopBreakpoint = window.matchMedia('(min-width: 992px)');
	let trapHandler = null;
	let lastFocused = null;

	if (!toggle || !navigation) {
		return;
	}

	function getFocusable(container) {
		return Array.from(container.querySelectorAll(focusableSelectors))
			.filter(function(el) {
				return el.offsetParent !== null || window.getComputedStyle(el).position === 'fixed';
			});
	}

	function trapFocus() {
		const focusable = getFocusable(navigation);
		if (!focusable.length) {
			return;
		}
		const first = focusable[0];
		const last = focusable[focusable.length - 1];

		function handleKey(event) {
			if (event.key === 'Tab') {
				if (event.shiftKey && document.activeElement === first) {
					event.preventDefault();
					last.focus();
				} else if (!event.shiftKey && document.activeElement === last) {
					event.preventDefault();
					first.focus();
				}
			}

			if (event.key === 'Escape') {
				closeNav();
			}
		}

		trapHandler = handleKey;
		navigation.addEventListener('keydown', trapHandler);
	}

	function releaseFocus() {
		if (trapHandler) {
			navigation.removeEventListener('keydown', trapHandler);
			trapHandler = null;
		}
	}

	function openNav() {
		if (navigation.classList.contains('is-open')) {
			return;
		}
		lastFocused = document.activeElement;
		navigation.classList.add('is-open', 'active');
		navigation.setAttribute('aria-hidden', 'false');
		navigation.setAttribute('aria-modal', 'true');
		toggle.setAttribute('aria-expanded', 'true');
		document.body.classList.add('mobile-nav-open');
		trapFocus();
		const focusable = getFocusable(navigation);
		if (focusable.length) {
			window.setTimeout(function() {
				focusable[0].focus();
			}, 20);
		}
	}

	function closeNav() {
		if (!navigation.classList.contains('is-open')) {
			return;
		}
		navigation.classList.remove('is-open', 'active');
		navigation.setAttribute('aria-hidden', 'true');
		navigation.setAttribute('aria-modal', 'false');
		toggle.setAttribute('aria-expanded', 'false');
		document.body.classList.remove('mobile-nav-open');
		releaseFocus();
		if (lastFocused && typeof lastFocused.focus === 'function') {
			lastFocused.focus();
		}
		lastFocused = null;
	}

	toggle.addEventListener('click', function() {
		if (navigation.classList.contains('is-open')) {
			closeNav();
		} else {
			openNav();
		}
	});

	if (backdrop) {
		backdrop.addEventListener('click', closeNav);
	}

	if (closeBtn) {
		closeBtn.addEventListener('click', function() {
			closeNav();
		});
	}

	document.addEventListener('keydown', function(event) {
		if (event.key === 'Escape') {
			closeNav();
		}
	});

	document.addEventListener('click', function(event) {
		if (!navigation.classList.contains('is-open')) {
			return;
		}
		const target = event.target;
		if (navigation.contains(target) || toggle.contains(target)) {
			return;
		}
		closeNav();
	});

	const COMPACT_HEADER_OFFSET = 140;

	function updateHeaderState() {
		if (!header) {
			return;
		}

		if (window.scrollY > COMPACT_HEADER_OFFSET) {
			header.classList.add('is-compact');
		} else {
			header.classList.remove('is-compact');
		}
	}

	updateHeaderState();
	window.addEventListener('scroll', updateHeaderState, { passive: true });

	navigation.addEventListener('click', function(event) {
		if (!navigation.classList.contains('is-open')) {
			return;
		}
		if (event.defaultPrevented) {
			return;
		}
		const target = event.target;
		if (target && target.tagName === 'A') {
			closeNav();
		}
	});

	function handleBreakpointChange(e) {
		if (e.matches) {
			closeNav();
		}
	}

	if (typeof desktopBreakpoint.addEventListener === 'function') {
		desktopBreakpoint.addEventListener('change', handleBreakpointChange);
	} else if (desktopBreakpoint.addListener) {
		desktopBreakpoint.addListener(handleBreakpointChange);
	}

	// Dropdown do perfil de usuário
	const userProfileDropdown = document.querySelector('.user-profile-dropdown');
	const userProfileBtn = document.querySelector('.user-profile-btn');

	if (userProfileDropdown && userProfileBtn) {
		userProfileBtn.addEventListener('click', function(e) {
			e.stopPropagation();
			const isActive = userProfileDropdown.classList.contains('active');
			
			if (isActive) {
				userProfileDropdown.classList.remove('active');
				userProfileBtn.setAttribute('aria-expanded', 'false');
			} else {
				userProfileDropdown.classList.add('active');
				userProfileBtn.setAttribute('aria-expanded', 'true');
			}
		});

		// Fechar dropdown ao clicar fora
		document.addEventListener('click', function(e) {
			if (!userProfileDropdown.contains(e.target)) {
				userProfileDropdown.classList.remove('active');
				userProfileBtn.setAttribute('aria-expanded', 'false');
			}
		});

		// Fechar dropdown ao pressionar Escape
		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape' && userProfileDropdown.classList.contains('active')) {
				userProfileDropdown.classList.remove('active');
				userProfileBtn.setAttribute('aria-expanded', 'false');
			}
		});
	}

	// Toggle de submenu no mobile
	const menuItemsWithChildren = document.querySelectorAll('.menu-item-has-children > a');

	menuItemsWithChildren.forEach(function(item) {
		item.addEventListener('click', function(e) {
			// Só prevenir default no mobile
			if (window.innerWidth <= 768) {
				e.preventDefault();
				const parentItem = this.parentElement;
				parentItem.classList.toggle('menu-item-open');
				
				// Fechar outros submenus
				menuItemsWithChildren.forEach(function(otherItem) {
					if (otherItem !== item) {
						otherItem.parentElement.classList.remove('menu-item-open');
					}
				});
			}
		});
	});

	// Botão Voltar ao Topo
	const backToTopBtn = document.querySelector('.back-to-top-btn');
	
	if (backToTopBtn) {
		backToTopBtn.addEventListener('click', function(e) {
			e.preventDefault();
			window.scrollTo({
				top: 0,
				behavior: 'smooth'
			});
		});

		// Mostrar/ocultar botão baseado no scroll
		window.addEventListener('scroll', function() {
			if (window.pageYOffset > 300) {
				backToTopBtn.style.opacity = '1';
				backToTopBtn.style.visibility = 'visible';
			} else {
				backToTopBtn.style.opacity = '0';
				backToTopBtn.style.visibility = 'hidden';
			}
		});
	}
})();

