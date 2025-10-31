/**
 * Calendário Dinâmico de Concursos
 */
(function() {
	'use strict';
	
	// Obter dados dos eventos
	const eventsDataEl = document.getElementById('calendario-events-data');
	if (!eventsDataEl) return;
	
	let allEvents = JSON.parse(eventsDataEl.textContent);
	let currentMonth = parseInt(document.querySelector('.calendario-grid').dataset.month);
	let currentYear = parseInt(document.querySelector('.calendario-grid').dataset.year);
	let selectedCategory = 'todos';
	
	// Converter chaves de data para formato usado (YYYY-MM-DD)
	const eventsByDate = {};
	Object.keys(allEvents).forEach(date => {
		// Garantir formato YYYY-MM-DD
		const normalizedDate = date.includes('T') ? date.split('T')[0] : date;
		eventsByDate[normalizedDate] = allEvents[date];
	});
	
	// Meses em português
	const mesesPT = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
	
	// Dias da semana em português (já no HTML)
	
	/**
	 * Gerar calendário
	 */
	function renderCalendar() {
		const calendarDays = document.getElementById('calendario-days');
		if (!calendarDays) return;
		
		// Primeiro dia do mês
		const firstDay = new Date(currentYear, currentMonth - 1, 1);
		// Último dia do mês
		const lastDay = new Date(currentYear, currentMonth, 0);
		// Dia da semana do primeiro dia (0 = domingo)
		const firstDayOfWeek = firstDay.getDay();
		// Quantos dias no mês
		const daysInMonth = lastDay.getDate();
		// Dia atual
		const today = new Date();
		const todayDate = today.getDate();
		const todayMonth = today.getMonth() + 1;
		const todayYear = today.getFullYear();
		
		// Limpar calendário
		calendarDays.innerHTML = '';
		
		// Dias do mês anterior (para preencher início)
		const prevMonth = currentMonth === 1 ? 12 : currentMonth - 1;
		const prevYear = currentMonth === 1 ? currentYear - 1 : currentYear;
		const prevLastDay = new Date(prevYear, prevMonth, 0).getDate();
		
		// Preencher início com dias do mês anterior
		for (let i = firstDayOfWeek - 1; i >= 0; i--) {
			const day = prevLastDay - i;
			const dayEl = createDayElement(day, true);
			calendarDays.appendChild(dayEl);
		}
		
		// Dias do mês atual
		for (let day = 1; day <= daysInMonth; day++) {
			const dateStr = `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
			const isToday = (day === todayDate && currentMonth === todayMonth && currentYear === todayYear);
			const dayEvents = getEventsForDate(dateStr);
			
			const dayEl = createDayElement(day, false, isToday, dayEvents.length > 0);
			
			// Destacar datas com eventos
			if (dayEvents.length > 0) {
				dayEl.classList.add('has-events-highlight');
				dayEl.addEventListener('click', () => showDayEvents(dateStr, dayEvents));
			}
			
			// Adicionar indicador de eventos
			if (dayEvents.length > 0) {
				const indicator = document.createElement('div');
				indicator.className = dayEvents.length > 1 ? 'calendario-event-indicator multiple' : 'calendario-event-indicator';
				
				if (dayEvents.length > 1) {
					const count = Math.min(dayEvents.length, 3);
					for (let i = 0; i < count; i++) {
						const span = document.createElement('span');
						indicator.appendChild(span);
					}
				}
				
				dayEl.appendChild(indicator);
				
				// Adicionar número de eventos se houver mais de um
				if (dayEvents.length > 1) {
					const eventCount = document.createElement('span');
					eventCount.className = 'calendario-event-count';
					eventCount.textContent = dayEvents.length;
					dayEl.appendChild(eventCount);
				}
			}
			
			calendarDays.appendChild(dayEl);
		}
		
		// Preencher fim com dias do próximo mês
		const totalCells = firstDayOfWeek + daysInMonth;
		const remainingCells = 42 - totalCells; // 6 semanas * 7 dias
		const nextMonth = currentMonth === 12 ? 1 : currentMonth + 1;
		
		for (let day = 1; day <= Math.min(remainingCells, 14); day++) {
			const dayEl = createDayElement(day, true);
			calendarDays.appendChild(dayEl);
		}
		
		// Atualizar mês/ano exibido
		updateMonthYearDisplay();
	}
	
	/**
	 * Criar elemento de dia
	 */
	function createDayElement(day, isOtherMonth, isToday = false, hasEvents = false) {
		const dayEl = document.createElement('div');
		dayEl.className = 'calendario-day';
		
		if (isOtherMonth) {
			dayEl.classList.add('other-month');
		}
		if (isToday) {
			dayEl.classList.add('today');
		}
		if (hasEvents) {
			dayEl.classList.add('has-events');
		}
		
		const dayNumber = document.createElement('span');
		dayNumber.className = 'calendario-day-number';
		dayNumber.textContent = day;
		dayEl.appendChild(dayNumber);
		
		// Posicionar relativo para o contador de eventos
		dayEl.style.position = 'relative';
		
		return dayEl;
	}
	
	/**
	 * Obter eventos para uma data
	 */
	function getEventsForDate(dateStr) {
		if (!eventsByDate[dateStr]) return [];
		
		let events = eventsByDate[dateStr];
		
		// Filtrar por categoria
		if (selectedCategory !== 'todos') {
			events = events.filter(event => event.categoria === selectedCategory);
		}
		
		return events;
	}
	
	/**
	 * Mostrar eventos de um dia em modal
	 */
	function showDayEvents(dateStr, events) {
		// Criar ou obter modal
		let modal = document.getElementById('calendario-day-modal');
		if (!modal) {
			modal = document.createElement('div');
			modal.id = 'calendario-day-modal';
			modal.className = 'calendario-day-modal';
			
			const content = document.createElement('div');
			content.className = 'calendario-day-modal-content';
			
			const closeBtn = document.createElement('button');
			closeBtn.className = 'calendario-day-modal-close';
			closeBtn.textContent = '×';
			closeBtn.addEventListener('click', () => {
				modal.classList.remove('active');
			});
			
			const title = document.createElement('h3');
			title.className = 'calendario-day-modal-title';
			
			const eventsContainer = document.createElement('div');
			eventsContainer.className = 'calendario-day-modal-events';
			
			content.appendChild(closeBtn);
			content.appendChild(title);
			content.appendChild(eventsContainer);
			modal.appendChild(content);
			
			// Fechar ao clicar fora
			modal.addEventListener('click', (e) => {
				if (e.target === modal) {
					modal.classList.remove('active');
				}
			});
			
			document.body.appendChild(modal);
		}
		
		const title = modal.querySelector('.calendario-day-modal-title');
		const eventsContainer = modal.querySelector('.calendario-day-modal-events');
		
		// Formatar data
		const date = new Date(dateStr);
		const day = date.getDate();
		const month = mesesPT[date.getMonth()];
		const year = date.getFullYear();
		
		title.textContent = `Eventos de ${day} de ${month} de ${year}`;
		
		// Limpar eventos anteriores
		eventsContainer.innerHTML = '';
		
		// Adicionar eventos
		if (events.length === 0) {
			const empty = document.createElement('p');
			empty.textContent = 'Nenhum evento encontrado para esta data.';
			empty.style.textAlign = 'center';
			empty.style.color = 'var(--sg-color-text-light)';
			eventsContainer.appendChild(empty);
		} else {
			events.forEach(event => {
				const eventEl = document.createElement('div');
				eventEl.className = 'calendario-day-modal-event';
				
				const eventTitle = document.createElement('div');
				eventTitle.className = 'calendario-day-modal-event-title';
				
				const link = document.createElement('a');
				link.href = event.permalink;
				link.textContent = event.title;
				eventTitle.appendChild(link);
				
				const meta = document.createElement('div');
				meta.className = 'calendario-day-modal-event-meta';
				const dateObj = new Date(event.date);
				const dateFormatted = `${String(dateObj.getDate()).padStart(2, '0')}/${String(dateObj.getMonth() + 1).padStart(2, '0')}/${dateObj.getFullYear()}`;
				meta.textContent = `📅 ${dateFormatted}`;
				
				eventEl.appendChild(eventTitle);
				eventEl.appendChild(meta);
				eventsContainer.appendChild(eventEl);
			});
		}
		
		modal.classList.add('active');
	}
	
	/**
	 * Atualizar exibição do mês/ano
	 */
	function updateMonthYearDisplay() {
		const monthName = document.querySelector('.calendario-month-name');
		const yearEl = document.querySelector('.calendario-year');
		
		if (monthName) {
			monthName.textContent = mesesPT[currentMonth - 1];
		}
		if (yearEl) {
			yearEl.textContent = currentYear;
		}
	}
	
	/**
	 * Filtrar por categoria
	 */
	function filterByCategory(category) {
		selectedCategory = category;
		
		// Atualizar filtros ativos
		document.querySelectorAll('.filtro-categoria').forEach(filter => {
			if (filter.dataset.categoria === category) {
				filter.classList.add('active');
			} else {
				filter.classList.remove('active');
			}
		});
		
		// Re-renderizar calendário
		renderCalendar();
		
		// Atualizar lista de eventos abaixo
		updateEventsList();
	}
	
	/**
	 * Atualizar lista de eventos
	 */
	function updateEventsList() {
		const eventsList = document.getElementById('eventos-lista');
		if (!eventsList) return;
		
		// Obter eventos filtrados
		let filteredEvents = [];
		Object.keys(eventsByDate).forEach(date => {
			const events = getEventsForDate(date);
			events.forEach(event => {
				filteredEvents.push({ ...event, date });
			});
		});
		
		// Ordenar por data
		filteredEvents.sort((a, b) => {
			return new Date(a.date) - new Date(b.date);
		});
		
		// Limitar a 5 eventos
		filteredEvents = filteredEvents.slice(0, 5);
		
		eventsList.innerHTML = '';
		
		if (filteredEvents.length === 0) {
			const empty = document.createElement('div');
			empty.className = 'calendario-empty';
			empty.innerHTML = '<p>Nenhum evento encontrado para esta categoria.</p>';
			eventsList.appendChild(empty);
			return;
		}
		
		filteredEvents.forEach(event => {
			const date = new Date(event.date);
			const day = date.getDate();
			const monthIdx = date.getMonth();
			const monthAbbr = mesesPT[monthIdx].substring(0, 3).toUpperCase();
			const fullDate = `${String(day).padStart(2, '0')}/${String(monthIdx + 1).padStart(2, '0')}/${date.getFullYear()}`;
			
			const item = document.createElement('div');
			item.className = 'calendario-item';
			
			item.innerHTML = `
				<div class="calendario-date">
					<span class="calendario-day">${day}</span>
					<span class="calendario-month">${monthAbbr}</span>
				</div>
				<div class="calendario-content">
					<a href="${event.permalink}" class="calendario-title">
						${event.title.length > 50 ? event.title.substring(0, 50) + '...' : event.title}
					</a>
					<span class="calendario-date-text">${fullDate}</span>
				</div>
			`;
			
			eventsList.appendChild(item);
		});
	}
	
	// Event listeners
	document.addEventListener('DOMContentLoaded', function() {
		// Filtros de categoria
		document.querySelectorAll('.filtro-categoria').forEach(filter => {
			filter.addEventListener('click', function() {
				const category = this.dataset.categoria;
				filterByCategory(category);
			});
		});
		
		// Navegação do calendário
		document.querySelectorAll('.calendario-prev, .calendario-next, .calendario-today').forEach(btn => {
			btn.addEventListener('click', function() {
				if (this.dataset.action === 'prev') {
					currentMonth--;
					if (currentMonth < 1) {
						currentMonth = 12;
						currentYear--;
					}
				} else if (this.dataset.action === 'next') {
					currentMonth++;
					if (currentMonth > 12) {
						currentMonth = 1;
						currentYear++;
					}
				} else if (this.dataset.action === 'today') {
					const now = new Date();
					currentMonth = now.getMonth() + 1;
					currentYear = now.getFullYear();
				}
				
				// Atualizar data attributes
				document.querySelector('.calendario-grid').dataset.month = currentMonth;
				document.querySelector('.calendario-grid').dataset.year = currentYear;
				
				renderCalendar();
			});
		});
		
		// Renderizar calendário inicial
		renderCalendar();
		updateEventsList();
		
		// Marcar primeiro filtro disponível como ativo, se houver filtros
		const firstFilter = document.querySelector('.filtro-categoria');
		if (firstFilter) {
			firstFilter.classList.add('active');
		}
	});
})();
