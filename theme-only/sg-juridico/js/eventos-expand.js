/**
 * Expansão de detalhes de eventos
 */
(function() {
	'use strict';
	
	document.addEventListener('DOMContentLoaded', function() {
		// Adicionar event listeners aos botões de toggle
		const toggleButtons = document.querySelectorAll('.evento-card-toggle');
		
		toggleButtons.forEach(function(button) {
			button.addEventListener('click', function() {
				const eventId = this.dataset.eventId;
				const detailsDiv = document.getElementById('evento-details-' + eventId);
				const cardDiv = document.getElementById('evento-card-' + eventId);
				const toggleText = this.querySelector('.toggle-text');
				
				if (!detailsDiv) return;
				
				// Toggle da classe active
				this.classList.toggle('active');
				
				// Toggle da exibição
				if (detailsDiv.style.display === 'none' || detailsDiv.style.display === '') {
					detailsDiv.style.display = 'block';
					toggleText.textContent = 'Ocultar detalhes';
					
					// Adicionar classe expanded ao card
					if (cardDiv) {
						cardDiv.classList.add('expanded');
					}
					
					// Scroll suave até o conteúdo expandido
					setTimeout(function() {
						detailsDiv.scrollIntoView({ 
							behavior: 'smooth', 
							block: 'nearest',
							inline: 'nearest'
						});
					}, 100);
				} else {
					detailsDiv.style.display = 'none';
					toggleText.textContent = 'Ver detalhes';
					
					// Remover classe expanded do card
					if (cardDiv) {
						cardDiv.classList.remove('expanded');
					}
				}
			});
		});
		
		// Fechar outros detalhes quando um novo é aberto (opcional - comportamento acordeão)
		const eventosCards = document.querySelectorAll('.evento-card');
		
		toggleButtons.forEach(function(button) {
			button.addEventListener('click', function() {
				const eventId = this.dataset.eventId;
				const detailsDiv = document.getElementById('evento-details-' + eventId);
				
				// Se está abrindo este detalhe
				if (detailsDiv && detailsDiv.style.display !== 'none') {
					// Fechar outros (opcional - comentar se quiser manter múltiplos abertos)
					// toggleButtons.forEach(function(otherButton) {
					// 	if (otherButton !== button) {
					// 		const otherId = otherButton.dataset.eventId;
					// 		const otherDetails = document.getElementById('evento-details-' + otherId);
					// 		if (otherDetails) {
					// 			otherDetails.style.display = 'none';
					// 			otherButton.classList.remove('active');
					// 			otherButton.querySelector('.toggle-text').textContent = 'Ver detalhes';
					// 		}
					// 	}
					// });
				}
			});
		});
	});
})();

