<?php
/**
 * Template para exibir evento individual (ETN)
 *
 * @package SG_Juridico
 * 
 * Template Name: Single Event (ETN)
 */

get_header();

// Verificar se é realmente um evento (ETN, Tribe Events ou SG Eventos)
global $post;
$post_type = $post ? get_post_type( $post ) : '';
if ( ! $post || ! in_array( $post_type, array( 'etn', 'tribe_events', 'sg_eventos' ) ) ) {
	// Se não for um evento, usar template padrão
	if ( have_posts() ) {
		while ( have_posts() ) : the_post();
			get_template_part( 'template-parts/content', get_post_type() );
		endwhile;
	} else {
		get_template_part( 'template-parts/content', 'none' );
	}
	get_footer();
	return;
}
?>

<main id="main" class="site-main">
	<div class="container">
		<?php 
		// Garantir que temos o post correto
		if ( have_posts() ) :
			while ( have_posts() ) : the_post();
		?>
			
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'evento-single' ); ?>>
				
				<!-- Cabeçalho do Evento -->
				<header class="evento-header">
					<div class="evento-header-content">
						<?php
						// Detectar meta keys baseado no tipo de post
						$post_id = get_the_ID();
						if ( $post_type === 'sg_eventos' ) {
							$meta_key_start = '_sg_evento_data_inicio';
							$meta_key_end = '_sg_evento_data_fim';
							$meta_key_location = '_sg_evento_local';
							$meta_key_organizer = '';
						} elseif ( $post_type === 'tribe_events' ) {
							$meta_key_start = '_EventStartDate';
							$meta_key_end = '_EventEndDate';
							$meta_key_location = '_EventVenue';
							$meta_key_organizer = '_EventOrganizer';
						} else {
							// ETN
							$meta_key_start = 'etn_start_date';
							$meta_key_end = 'etn_end_date';
							$meta_key_location = 'etn_location';
							$meta_key_organizer = 'etn_organizer';
						}
						
						$start_date = get_post_meta( $post_id, $meta_key_start, true );
						$end_date = get_post_meta( $post_id, $meta_key_end, true );
						$event_location = get_post_meta( $post_id, $meta_key_location, true );
						$event_organizer = ! empty( $meta_key_organizer ) ? get_post_meta( $post_id, $meta_key_organizer, true ) : '';
						
						// Carregar horários e datas de inscrição
						$hora_inicio = get_post_meta( $post_id, '_sg_evento_hora_inicio', true );
						$hora_fim = get_post_meta( $post_id, '_sg_evento_hora_fim', true );
						$data_inscricao_inicio = get_post_meta( $post_id, '_sg_evento_inscricao_inicio', true );
						$data_inscricao_fim = get_post_meta( $post_id, '_sg_evento_inscricao_fim', true );
						$event_address = get_post_meta( $post_id, '_sg_evento_endereco', true );
						
						// Formatar horários para exibição (HH:MM)
						if ( ! empty( $hora_inicio ) && strlen( $hora_inicio ) >= 5 ) {
							$hora_inicio = substr( $hora_inicio, 0, 5 ); // Garantir formato HH:MM
						}
						if ( ! empty( $hora_fim ) && strlen( $hora_fim ) >= 5 ) {
							$hora_fim = substr( $hora_fim, 0, 5 ); // Garantir formato HH:MM
						}
						
						// Formatar datas de inscrição
						$data_inscricao_inicio_formatted = '';
						$data_inscricao_fim_formatted = '';
						if ( ! empty( $data_inscricao_inicio ) ) {
							$data_inscricao_inicio_formatted = date_i18n( 'd/m/Y', strtotime( $data_inscricao_inicio ) );
						}
						if ( ! empty( $data_inscricao_fim ) ) {
							$data_inscricao_fim_formatted = date_i18n( 'd/m/Y', strtotime( $data_inscricao_fim ) );
						}
						
						// Converter Tribe Events timestamp para formato legível
						if ( $post_type === 'tribe_events' && is_numeric( $start_date ) ) {
							$start_date = date( 'Y-m-d', $start_date );
						}
						if ( $post_type === 'tribe_events' && is_numeric( $end_date ) ) {
							$end_date = date( 'Y-m-d', $end_date );
						}
						// Se Tribe Events retornar datetime string, extrair apenas a data
						if ( $post_type === 'tribe_events' && strpos( $start_date, ' ' ) !== false ) {
							$start_date = date( 'Y-m-d', strtotime( $start_date ) );
						}
						if ( $post_type === 'tribe_events' && strpos( $end_date, ' ' ) !== false ) {
							$end_date = date( 'Y-m-d', strtotime( $end_date ) );
						}
						?>
						
						<?php if ( $start_date ) : 
							$date_timestamp = strtotime( $start_date );
							$day = date_i18n( 'd', $date_timestamp );
							$month_abrev = strtoupper( date_i18n( 'M', $date_timestamp ) );
							$meses_en = array(
								'JAN' => 'JAN', 'FEB' => 'FEV', 'MAR' => 'MAR', 'APR' => 'ABR',
								'MAY' => 'MAI', 'JUN' => 'JUN', 'JUL' => 'JUL', 'AUG' => 'AGO',
								'SEP' => 'SET', 'OCT' => 'OUT', 'NOV' => 'NOV', 'DEC' => 'DEZ'
							);
							$month = isset( $meses_en[ $month_abrev ] ) ? $meses_en[ $month_abrev ] : $month_abrev;
							$full_date = date_i18n( 'd/m/Y', $date_timestamp );
						?>
							<div class="evento-date-badge">
								<span class="evento-day"><?php echo esc_html( $day ); ?></span>
								<span class="evento-month"><?php echo esc_html( $month ); ?></span>
							</div>
						<?php endif; ?>
						
						<div class="evento-header-text">
							<h1 class="evento-title"><?php the_title(); ?></h1>
							
							<div class="evento-meta">
								<?php if ( $start_date ) : ?>
									<div class="evento-meta-item">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M15 2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h2m0 0V0m0 2h6m-6 0V0m6 0v2m0 0h-6" stroke="currentColor" stroke-width="1.5"/>
											<path d="M3 7h14" stroke="currentColor" stroke-width="1.5"/>
										</svg>
										<span><strong>Data da Realização:</strong> <?php echo esc_html( $full_date ); ?>
										<?php if ( ! empty( $hora_inicio ) ) : ?>
											 às <?php echo esc_html( $hora_inicio ); ?>
										<?php endif; ?>
										</span>
									</div>
								<?php endif; ?>
								
								<?php if ( ! empty( $hora_fim ) ) : ?>
									<div class="evento-meta-item">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M10 6v4l3 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
											<circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"/>
										</svg>
										<span><strong>Horário de Término:</strong> <?php echo esc_html( $hora_fim ); ?></span>
									</div>
								<?php endif; ?>
								
								<?php if ( ! empty( $data_inscricao_inicio ) || ! empty( $data_inscricao_fim ) ) : ?>
									<div class="evento-meta-item">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M15 2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h2m0 0V0m0 2h6m-6 0V0m6 0v2m0 0h-6" stroke="currentColor" stroke-width="1.5"/>
											<path d="M3 7h14" stroke="currentColor" stroke-width="1.5"/>
										</svg>
										<span><strong>Período de Inscrições:</strong>
										<?php if ( ! empty( $data_inscricao_inicio ) ) : ?>
											<?php echo esc_html( $data_inscricao_inicio_formatted ); ?>
										<?php endif; ?>
										<?php if ( ! empty( $data_inscricao_inicio ) && ! empty( $data_inscricao_fim ) ) : ?>
											 até 
										<?php endif; ?>
										<?php if ( ! empty( $data_inscricao_fim ) ) : ?>
											<?php echo esc_html( $data_inscricao_fim_formatted ); ?>
										<?php endif; ?>
										</span>
									</div>
								<?php endif; ?>
								
								<?php if ( $end_date && $end_date !== $start_date ) : 
									$end_timestamp = strtotime( $end_date );
									$end_full_date = date_i18n( 'd/m/Y', $end_timestamp );
								?>
									<div class="evento-meta-item">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M15 2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h2m0 0V0m0 2h6m-6 0V0m6 0v2m0 0h-6" stroke="currentColor" stroke-width="1.5"/>
											<path d="M3 7h14" stroke="currentColor" stroke-width="1.5"/>
										</svg>
										<span><strong>Data de Término:</strong> <?php echo esc_html( $end_full_date ); ?></span>
									</div>
								<?php endif; ?>
								
								<?php if ( $event_location ) : ?>
									<div class="evento-meta-item">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M10 10.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z" stroke="currentColor" stroke-width="1.5"/>
											<path d="M10 2C6.5 2 3.75 4.75 3.75 8.25c0 3.5 4.375 7.875 4.375 7.875S10 19.25 10 19.25s1.875-3.125 1.875-3.125S16.25 11.75 16.25 8.25C16.25 4.75 13.5 2 10 2z" stroke="currentColor" stroke-width="1.5"/>
										</svg>
										<span><strong>Local:</strong> <?php echo esc_html( $event_location ); ?></span>
									</div>
								<?php endif; ?>
								
								<?php if ( ! empty( $event_address ) ) : ?>
									<div class="evento-meta-item">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M10 10.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z" stroke="currentColor" stroke-width="1.5"/>
											<path d="M10 2C6.5 2 3.75 4.75 3.75 8.25c0 3.5 4.375 7.875 4.375 7.875S10 19.25 10 19.25s1.875-3.125 1.875-3.125S16.25 11.75 16.25 8.25C16.25 4.75 13.5 2 10 2z" stroke="currentColor" stroke-width="1.5"/>
										</svg>
										<span><strong>Endereço:</strong> <?php echo esc_html( $event_address ); ?></span>
									</div>
								<?php endif; ?>
								
								<?php if ( $event_organizer ) : ?>
									<div class="evento-meta-item">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7z" stroke="currentColor" stroke-width="1.5"/>
										</svg>
										<span><strong>Organizador:</strong> <?php echo esc_html( $event_organizer ); ?></span>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
					
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="evento-thumbnail">
							<?php the_post_thumbnail( 'large' ); ?>
						</div>
					<?php endif; ?>
				</header>
				
				<!-- Conteúdo do Evento -->
				<div class="evento-content-wrapper">
					<div class="evento-main-content">
						<div class="evento-content">
							<h2>Informações do Concurso</h2>
							<?php the_content(); ?>
							
							<?php
							// Informações adicionais do evento
							$event_schedule = get_post_meta( get_the_ID(), 'etn_event_schedule', true );
							if ( $event_schedule && is_array( $event_schedule ) ) :
							?>
								<div class="evento-schedule">
									<h3>Programação</h3>
									<div class="schedule-list">
										<?php foreach ( $event_schedule as $schedule_item ) : ?>
											<div class="schedule-item">
												<div class="schedule-time">
													<?php echo isset( $schedule_item['time'] ) ? esc_html( $schedule_item['time'] ) : ''; ?>
												</div>
												<div class="schedule-content">
													<strong><?php echo isset( $schedule_item['title'] ) ? esc_html( $schedule_item['title'] ) : ''; ?></strong>
													<?php if ( isset( $schedule_item['description'] ) ) : ?>
														<p><?php echo esc_html( $schedule_item['description'] ); ?></p>
													<?php endif; ?>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							<?php endif; ?>
						</div>
						
						<!-- Calendário Completo de Concursos -->
						<div class="calendario-completo-section">
							<h2>Próximos Concursos</h2>
							<?php
							// Buscar todos os eventos futuros
							$all_events = sg_get_concurso_events( 20 );
							if ( ! empty( $all_events ) ) :
							?>
								<div class="calendario-completo-list">
									<?php foreach ( $all_events as $event_item ) : 
										// Pular o evento atual
										if ( $event_item['id'] == get_the_ID() ) {
											continue;
										}
										
										$item_date = strtotime( $event_item['date'] );
										if ( $item_date ) {
											$item_day = date_i18n( 'd', $item_date );
											$item_month_abrev = strtoupper( date_i18n( 'M', $item_date ) );
											$meses_en = array(
												'JAN' => 'JAN', 'FEB' => 'FEV', 'MAR' => 'MAR', 'APR' => 'ABR',
												'MAY' => 'MAI', 'JUN' => 'JUN', 'JUL' => 'JUL', 'AUG' => 'AGO',
												'SEP' => 'SET', 'OCT' => 'OUT', 'NOV' => 'NOV', 'DEC' => 'DEZ'
											);
											$item_month = isset( $meses_en[ $item_month_abrev ] ) ? $meses_en[ $item_month_abrev ] : $item_month_abrev;
											$item_full_date = date_i18n( 'd/m/Y', $item_date );
										} else {
											$item_day = '';
											$item_month = '';
											$item_full_date = $event_item['date'];
										}
									?>
										<div class="calendario-item">
											<div class="calendario-date">
												<span class="calendario-day"><?php echo esc_html( $item_day ); ?></span>
												<span class="calendario-month"><?php echo esc_html( $item_month ); ?></span>
											</div>
											<div class="calendario-content">
												<a href="<?php echo esc_url( $event_item['permalink'] ); ?>" class="calendario-title">
													<?php echo esc_html( wp_trim_words( $event_item['title'], 12 ) ); ?>
												</a>
												<span class="calendario-date-text"><?php echo esc_html( $item_full_date ); ?></span>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							<?php else : ?>
								<p>Nenhum outro evento programado no momento.</p>
							<?php endif; ?>
						</div>
					</div>
					
					<!-- Sidebar do Evento -->
					<?php get_sidebar(); ?>
				</div>
			</article>
			
		<?php 
			endwhile;
		else :
			// Se não encontrou posts
		?>
			<div class="no-event-found">
				<h1>Evento não encontrado</h1>
				<p>O evento que você está procurando não foi encontrado ou não está mais disponível.</p>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'etn' ) ?: home_url() ); ?>" class="btn-voltar">
					← Voltar para o calendário
				</a>
			</div>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
?>

