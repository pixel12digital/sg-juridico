(function() {
	'use strict';

	const navigation = document.querySelector('.site-navigation');
	const toggle = document.querySelector('.menu-toggle');

	if (!toggle || !navigation) {
		return;
	}

	toggle.addEventListener('click', function() {
		const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
		toggle.setAttribute('aria-expanded', !isExpanded);
		navigation.classList.toggle('active');
	});

	// Close menu when clicking outside
	document.addEventListener('click', function(e) {
		if (!navigation.contains(e.target) && !toggle.contains(e.target)) {
			navigation.classList.remove('active');
			toggle.setAttribute('aria-expanded', 'false');
		}
	});

	// Close menu on escape key
	document.addEventListener('keydown', function(e) {
		if (e.key === 'Escape') {
			navigation.classList.remove('active');
			toggle.setAttribute('aria-expanded', 'false');
		}
	});

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

