/**
 * Script para formulário de contato
 * SG Jurídico Theme
 */
(function() {
	'use strict';

	const contactForm = document.getElementById('contact-form');
	if (!contactForm) return;

	const phoneInput = document.getElementById('contact-phone');
	const submitBtn = contactForm.querySelector('.contact-submit-btn');
	const btnText = submitBtn.querySelector('.btn-text');
	const btnLoading = submitBtn.querySelector('.btn-loading');
	const formMessage = document.getElementById('form-message');

	// Máscara para telefone
	if (phoneInput) {
		phoneInput.addEventListener('input', function(e) {
			let value = e.target.value.replace(/\D/g, '');
			if (value.length <= 10) {
				value = value.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
			} else {
				value = value.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
			}
			e.target.value = value;
		});
	}

	// Validação de campos
	function validateField(field) {
		const value = field.value.trim();
		const isRequired = field.hasAttribute('required');
		
		if (isRequired && !value) {
			field.classList.add('error');
			return false;
		}

		if (field.type === 'email' && value) {
			const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
			if (!emailRegex.test(value)) {
				field.classList.add('error');
				return false;
			}
		}

		field.classList.remove('error');
		return true;
	}

	// Validar todos os campos
	function validateForm() {
		const fields = contactForm.querySelectorAll('input[required], select[required], textarea[required]');
		let isValid = true;

		fields.forEach(field => {
			if (!validateField(field)) {
				isValid = false;
			}
		});

		return isValid;
	}

	// Adicionar validação em tempo real
	const fields = contactForm.querySelectorAll('input, select, textarea');
	fields.forEach(field => {
		field.addEventListener('blur', function() {
			validateField(field);
		});

		field.addEventListener('input', function() {
			if (field.classList.contains('error')) {
				validateField(field);
			}
		});
	});

	// Enviar formulário
	contactForm.addEventListener('submit', function(e) {
		e.preventDefault();

		// Validar antes de enviar
		if (!validateForm()) {
			showMessage('Por favor, preencha todos os campos obrigatórios corretamente.', 'error');
			return;
		}

		// Desabilitar botão
		submitBtn.disabled = true;
		btnText.style.display = 'none';
		btnLoading.style.display = 'inline';

		// Preparar dados
		const formData = new FormData(contactForm);
		formData.append('action', 'sg_send_contact_form');
		formData.append('ajax_url', contactForm.dataset.ajaxUrl || '/wp-admin/admin-ajax.php');

		// Enviar via AJAX
		const ajaxUrl = typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php';
		fetch(ajaxUrl, {
			method: 'POST',
			body: formData
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				showMessage(data.data.message || 'Mensagem enviada com sucesso! Entraremos em contato em breve.', 'success');
				contactForm.reset();
				
				// Scroll para mensagem
				formMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
			} else {
				showMessage(data.data.message || 'Erro ao enviar mensagem. Tente novamente ou entre em contato por e-mail.', 'error');
			}
		})
		.catch(error => {
			console.error('Erro:', error);
			showMessage('Erro ao enviar mensagem. Verifique sua conexão e tente novamente.', 'error');
		})
		.finally(() => {
			// Reabilitar botão
			submitBtn.disabled = false;
			btnText.style.display = 'inline';
			btnLoading.style.display = 'none';
		});
	});

	// Mostrar mensagem
	function showMessage(message, type) {
		formMessage.textContent = message;
		formMessage.className = 'form-message ' + type;
		formMessage.style.display = 'block';

		// Ocultar mensagem de erro após 5 segundos
		if (type === 'error') {
			setTimeout(() => {
				formMessage.style.display = 'none';
			}, 5000);
		}
	}

})();

