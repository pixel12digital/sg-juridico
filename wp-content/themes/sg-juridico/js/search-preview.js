(function() {
	'use strict';
	
	var searchInput = document.getElementById('header-search-input');
	var searchPreview = document.getElementById('search-preview');
	var searchPreviewContent = document.getElementById('search-preview-content');
	var searchTimeout;
	var ajaxRequest;
	
	if (!searchInput || !searchPreview || !searchPreviewContent) {
		return;
	}
	
	function hidePreview() {
		searchPreview.style.display = 'none';
		searchPreviewContent.innerHTML = '';
	}
	
	function showPreview() {
		searchPreview.style.display = 'block';
	}
	
	function formatDate(dateString) {
		if (!dateString) return '';
		// Se já está formatado, retornar como está
		if (dateString.match(/^\d{2}\/\d{2}\/\d{4}$/)) {
			return dateString;
		}
		// Tentar formatar (aceita vários formatos)
		var date;
		if (dateString.match(/^\d{4}-\d{2}-\d{2}/)) {
			// Formato YYYY-MM-DD
			var parts = dateString.split(' ');
			var datePart = parts[0].split('-');
			if (datePart.length === 3) {
				return datePart[2] + '/' + datePart[1] + '/' + datePart[0];
			}
		}
		date = new Date(dateString);
		if (isNaN(date.getTime())) return dateString;
		var day = String(date.getDate()).padStart(2, '0');
		var month = String(date.getMonth() + 1).padStart(2, '0');
		var year = date.getFullYear();
		return day + '/' + month + '/' + year;
	}
	
	function renderResults(results) {
		if (!results || results.length === 0) {
			searchPreviewContent.innerHTML = '<div style="padding: 20px; text-align: center; color: #666;">Nenhum resultado encontrado</div>';
			showPreview();
			return;
		}
		
		var html = '<div style="padding: 10px;">';
		
		// Separar eventos e produtos
		var events = results.filter(function(r) { return r.type === 'event'; });
		var products = results.filter(function(r) { return r.type === 'product'; });
		
		// Eventos
		if (events.length > 0) {
			html += '<div style="margin-bottom: 15px;"><h3 style="margin: 0 0 10px 0; font-size: 14px; font-weight: bold; color: #333; text-transform: uppercase;">Eventos</h3>';
			events.forEach(function(result) {
				html += '<a href="' + result.url + '" style="display: block; padding: 12px; border-bottom: 1px solid #eee; text-decoration: none; color: #333; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor=\'#f5f5f5\'" onmouseout="this.style.backgroundColor=\'transparent\'">';
				html += '<div style="display: flex; gap: 12px;">';
				if (result.thumbnail) {
					html += '<img src="' + result.thumbnail + '" alt="" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; flex-shrink: 0;">';
				}
				html += '<div style="flex: 1;">';
				html += '<div style="font-weight: bold; margin-bottom: 5px; font-size: 15px;">' + result.title + '</div>';
				if (result.date) {
					html += '<div style="font-size: 12px; color: #0ea5e9; margin-bottom: 5px;">📅 ' + formatDate(result.date) + '</div>';
				}
				if (result.excerpt) {
					html += '<div style="font-size: 13px; color: #666; line-height: 1.4;">' + result.excerpt + '</div>';
				}
				html += '</div>';
				html += '</div>';
				html += '</a>';
			});
			html += '</div>';
		}
		
		// Produtos
		if (products.length > 0) {
			html += '<div><h3 style="margin: 0 0 10px 0; font-size: 14px; font-weight: bold; color: #333; text-transform: uppercase;">Produtos</h3>';
			products.forEach(function(result) {
				html += '<a href="' + result.url + '" style="display: block; padding: 12px; border-bottom: 1px solid #eee; text-decoration: none; color: #333; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor=\'#f5f5f5\'" onmouseout="this.style.backgroundColor=\'transparent\'">';
				html += '<div style="display: flex; gap: 12px;">';
				if (result.thumbnail) {
					html += '<img src="' + result.thumbnail + '" alt="" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; flex-shrink: 0;">';
				}
				html += '<div style="flex: 1;">';
				html += '<div style="font-weight: bold; margin-bottom: 5px; font-size: 15px;">' + result.title + '</div>';
				if (result.price) {
					html += '<div style="font-size: 14px; color: #0ea5e9; font-weight: bold;">' + result.price + '</div>';
				}
				html += '</div>';
				html += '</div>';
				html += '</a>';
			});
			html += '</div>';
		}
		
		html += '</div>';
		
		searchPreviewContent.innerHTML = html;
		showPreview();
	}
	
	function performSearch() {
		var searchTerm = searchInput.value.trim();
		
		if (searchTerm.length < 2) {
			hidePreview();
			return;
		}
		
		// Cancelar requisição anterior se existir
		if (ajaxRequest && ajaxRequest.readyState !== 4) {
			ajaxRequest.abort();
		}
		
		// Mostrar loading
		searchPreviewContent.innerHTML = '<div style="padding: 20px; text-align: center; color: #666;">Buscando...</div>';
		showPreview();
		
		// Fazer requisição AJAX
		ajaxRequest = new XMLHttpRequest();
		ajaxRequest.open('POST', sgSearchPreview.ajaxurl, true);
		ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
		
		ajaxRequest.onreadystatechange = function() {
			if (ajaxRequest.readyState === 4) {
				if (ajaxRequest.status === 200) {
					try {
						var response = JSON.parse(ajaxRequest.responseText);
						if (response.success && response.data && response.data.results) {
							renderResults(response.data.results);
						} else {
							searchPreviewContent.innerHTML = '<div style="padding: 20px; text-align: center; color: #666;">Nenhum resultado encontrado</div>';
							showPreview();
						}
					} catch (e) {
						console.error('Erro ao processar resposta:', e);
						hidePreview();
					}
				} else {
					hidePreview();
				}
			}
		};
		
		var data = 'action=sg_search_preview&nonce=' + sgSearchPreview.nonce + '&s=' + encodeURIComponent(searchTerm);
		ajaxRequest.send(data);
	}
	
	// Event listeners
	searchInput.addEventListener('input', function() {
		clearTimeout(searchTimeout);
		searchTimeout = setTimeout(performSearch, 300); // Aguardar 300ms após parar de digitar
	});
	
	searchInput.addEventListener('focus', function() {
		if (searchInput.value.trim().length >= 2) {
			performSearch();
		}
	});
	
	// Esconder preview ao clicar fora
	document.addEventListener('click', function(e) {
		// Se clicou em um link dentro do preview, deixar navegar normalmente
		if (searchPreview.contains(e.target) && e.target.closest('a')) {
			// Permitir navegação normal
			hidePreview();
			return;
		}
		
		// Se clicou fora do preview e do input, esconder
		if (!searchInput.contains(e.target) && !searchPreview.contains(e.target)) {
			hidePreview();
		}
	});
	
	// Esconder preview ao pressionar ESC
	searchInput.addEventListener('keydown', function(e) {
		if (e.key === 'Escape') {
			hidePreview();
			searchInput.blur();
		}
	});
})();

