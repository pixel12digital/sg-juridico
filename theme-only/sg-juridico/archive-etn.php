<?php
/**
 * Template para listar todos os eventos (ETN)
 *
 * @package SG_Juridico
 */

get_header();

// Forçar que este template seja usado mesmo se o WordPress não detectar
if ( ! is_post_type_archive( 'etn' ) && ! isset( $_GET['post_type'] ) ) {
	// Se estiver na URL /eventos, forçar query
	global $wp_query;
	if ( strpos( $_SERVER['REQUEST_URI'], '/eventos' ) !== false ) {
		$wp_query->is_post_type_archive = true;
		$wp_query->is_archive = true;
		$wp_query->query_vars['post_type'] = 'etn';
	}
}
?>

<main id="main" class="site-main">
	<div class="container">
		<header class="archive-header">
			<h1 class="archive-title">Calendário Completo de Concursos</h1>
			<p class="archive-description">Confira todos os concursos públicos programados e não perca nenhuma data importante.</p>
		</header>
		
		<div class="eventos-archive-wrapper">
			<div class="eventos-filters">
				<h3>Filtros</h3>
				<form method="get" class="filtros-form">
					<div class="filter-group">
						<label for="categoria">Filtrar por Categoria:</label>
						<select name="categoria" id="categoria">
							<option value="">Todas as categorias</option>
							<?php
							$categorias = array(
								'ministerio-publico' => 'Ministério Público',
								'magistratura' => 'Magistratura',
								'delegado' => 'Delegado',
								'enam' => 'ENAM',
								'procuradoria' => 'Procuradoria',
							);
							$selected_categoria = isset( $_GET['categoria'] ) ? sanitize_text_field( $_GET['categoria'] ) : '';
							foreach ( $categorias as $slug => $nome ) :
							?>
								<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $selected_categoria, $slug ); ?>>
									<?php echo esc_html( $nome ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					
					<div class="filter-group">
						<label for="mes">Filtrar por Mês:</label>
						<select name="mes" id="mes">
							<option value="">Todos os meses</option>
							<?php
							$meses = array(
								1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
								5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
								9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
							);
							$selected_mes = isset( $_GET['mes'] ) ? intval( $_GET['mes'] ) : '';
							foreach ( $meses as $num => $nome ) :
							?>
								<option value="<?php echo esc_attr( $num ); ?>" <?php selected( $selected_mes, $num ); ?>>
									<?php echo esc_html( $nome ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					
					<div class="filter-group">
						<label for="ano">Filtrar por Ano:</label>
						<select name="ano" id="ano">
							<option value="">Todos os anos</option>
							<?php
							$current_year = date( 'Y' );
							$selected_ano = isset( $_GET['ano'] ) ? intval( $_GET['ano'] ) : '';
							for ( $ano = $current_year; $ano <= $current_year + 2; $ano++ ) :
							?>
								<option value="<?php echo esc_attr( $ano ); ?>" <?php selected( $selected_ano, $ano ); ?>>
									<?php echo esc_html( $ano ); ?>
								</option>
							<?php endfor; ?>
						</select>
					</div>
					
					<div class="filter-group">
						<label>
							<input type="checkbox" name="apenas_futuros" value="1" <?php checked( isset( $_GET['apenas_futuros'] ) && $_GET['apenas_futuros'] == 1 ); ?>>
							Apenas eventos futuros
						</label>
					</div>
					
					<button type="submit" class="btn-filtrar">Filtrar</button>
					<?php if ( isset( $_GET['categoria'] ) || isset( $_GET['mes'] ) || isset( $_GET['ano'] ) || isset( $_GET['apenas_futuros'] ) ) : ?>
						<a href="<?php echo esc_url( get_post_type_archive_link( 'etn' ) ); ?>" class="btn-limpar">Limpar Filtros</a>
					<?php endif; ?>
				</form>
			</div>
			
			<div class="eventos-list">
				<?php
				// DEBUG: Verificar se template está sendo carregado
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( 'Archive ETN template carregado!' );
				}
				
				// Query personalizada baseada nos filtros
				global $wp_query;
				$today = current_time( 'Y-m-d' );
				$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
				
				$args = array(
					'post_type'      => 'etn',
					'post_status'    => 'publish',
					'posts_per_page' => 20,
					'paged'          => $paged,
					'orderby'        => 'meta_value',
					'meta_key'       => 'etn_start_date',
					'meta_type'      => 'DATE',
					'order'          => 'ASC',
				);
				
				// Buscar todos os posts primeiro para filtrar por categoria
				$all_events_query = new WP_Query( array(
					'post_type'      => 'etn',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'orderby'        => 'meta_value',
					'meta_key'       => 'etn_start_date',
					'meta_type'      => 'DATE',
					'order'          => 'ASC',
				) );
				
				$filtered_post_ids = array();
				
				// Filtro por categoria
				if ( ! empty( $_GET['categoria'] ) ) {
					$selected_categoria = sanitize_text_field( $_GET['categoria'] );
					if ( $all_events_query->have_posts() ) {
						while ( $all_events_query->have_posts() ) {
							$all_events_query->the_post();
							$post_categoria = sg_detect_event_category( get_the_title() );
							if ( $post_categoria === $selected_categoria ) {
								$filtered_post_ids[] = get_the_ID();
							}
						}
						wp_reset_postdata();
					}
				} else {
					// Se não tem filtro de categoria, pegar todos os IDs
					if ( $all_events_query->have_posts() ) {
						while ( $all_events_query->have_posts() ) {
							$all_events_query->the_post();
							$filtered_post_ids[] = get_the_ID();
						}
						wp_reset_postdata();
					}
				}
				
				// Aplicar filtros de data aos posts filtrados por categoria
				$final_filtered_ids = array();
				
				if ( empty( $filtered_post_ids ) && ! empty( $_GET['categoria'] ) ) {
					// Se foi filtrado por categoria mas não encontrou nada, resultado vazio
					$args['post__in'] = array( 0 );
				} else {
					// Se não tem filtro de categoria, usar todos os IDs encontrados
					$ids_to_filter = ! empty( $filtered_post_ids ) ? $filtered_post_ids : array();
					
					// Se não tem filtro de categoria, buscar todos os posts ETN
					if ( empty( $ids_to_filter ) && empty( $_GET['categoria'] ) ) {
						$temp_query = new WP_Query( array(
							'post_type'      => 'etn',
							'post_status'    => 'publish',
							'posts_per_page' => -1,
							'fields'         => 'ids',
						) );
						$ids_to_filter = $temp_query->posts;
						wp_reset_postdata();
					}
					
					// Aplicar filtros de data
					foreach ( $ids_to_filter as $post_id ) {
						$start_date = get_post_meta( $post_id, 'etn_start_date', true );
						
						// Filtro por mês
						$match_mes = true;
						if ( ! empty( $_GET['mes'] ) ) {
							$mes = intval( $_GET['mes'] );
							if ( $start_date ) {
								$event_mes = intval( date( 'n', strtotime( $start_date ) ) );
								$match_mes = ( $event_mes === $mes );
							} else {
								$match_mes = false;
							}
						}
						
						// Filtro por ano
						$match_ano = true;
						if ( ! empty( $_GET['ano'] ) ) {
							$ano = intval( $_GET['ano'] );
							if ( $start_date ) {
								$event_ano = intval( date( 'Y', strtotime( $start_date ) ) );
								$match_ano = ( $event_ano === $ano );
							} else {
								$match_ano = false;
							}
						}
						
						// Apenas futuros
						$match_futuro = true;
						if ( isset( $_GET['apenas_futuros'] ) && $_GET['apenas_futuros'] == 1 ) {
							if ( $start_date ) {
								$match_futuro = ( strtotime( $start_date ) >= strtotime( $today ) );
							} else {
								$match_futuro = false;
							}
						}
						
						if ( $match_mes && $match_ano && $match_futuro ) {
							$final_filtered_ids[] = $post_id;
						}
					}
					
					// Atualizar query com IDs finais filtrados
					if ( ! empty( $final_filtered_ids ) ) {
						$args['post__in'] = $final_filtered_ids;
					} else {
						$args['post__in'] = array( 0 );
					}
				}
					
					$events_query = new WP_Query( $args );
				
				if ( $events_query->have_posts() ) :
				?>
					<div class="eventos-grid">
						<?php while ( $events_query->have_posts() ) : $events_query->the_post(); 
							$start_date = get_post_meta( get_the_ID(), 'etn_start_date', true );
							$end_date = get_post_meta( get_the_ID(), 'etn_end_date', true );
							
							$date_timestamp = $start_date ? strtotime( $start_date ) : false;
							if ( $date_timestamp ) {
								$day = date_i18n( 'd', $date_timestamp );
								$month_abrev = strtoupper( date_i18n( 'M', $date_timestamp ) );
								$meses_en = array(
									'JAN' => 'JAN', 'FEB' => 'FEV', 'MAR' => 'MAR', 'APR' => 'ABR',
									'MAY' => 'MAI', 'JUN' => 'JUN', 'JUL' => 'JUL', 'AUG' => 'AGO',
									'SEP' => 'SET', 'OCT' => 'OUT', 'NOV' => 'NOV', 'DEC' => 'DEZ'
								);
								$month = isset( $meses_en[ $month_abrev ] ) ? $meses_en[ $month_abrev ] : $month_abrev;
								$full_date = date_i18n( 'd/m/Y', $date_timestamp );
							} else {
								$day = '';
								$month = '';
								$full_date = $start_date ?: 'Data não informada';
							}
						?>
							<article class="evento-card" id="evento-card-<?php the_ID(); ?>">
								<?php if ( has_post_thumbnail() ) : ?>
									<div class="evento-card-thumbnail">
										<a href="<?php the_permalink(); ?>">
											<?php the_post_thumbnail( 'medium' ); ?>
										</a>
									</div>
								<?php endif; ?>
								
								<div class="evento-card-content">
									<div class="evento-card-date">
										<span class="evento-card-day"><?php echo esc_html( $day ); ?></span>
										<span class="evento-card-month"><?php echo esc_html( $month ); ?></span>
									</div>
									
									<div class="evento-card-text">
										<h3 class="evento-card-title">
											<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
										</h3>
										<div class="evento-card-meta">
											<span class="evento-card-date-text">📅 <?php echo esc_html( $full_date ); ?></span>
											<?php if ( $end_date && $end_date !== $start_date ) : 
												$end_full_date = date_i18n( 'd/m/Y', strtotime( $end_date ) );
											?>
												<span class="evento-card-end-date">até <?php echo esc_html( $end_full_date ); ?></span>
											<?php endif; ?>
										</div>
										<div class="evento-card-excerpt">
											<?php echo wp_trim_words( get_the_excerpt() ?: get_the_content(), 20 ); ?>
										</div>
										<button class="evento-card-toggle" data-event-id="<?php the_ID(); ?>">
											<span class="toggle-text">Ver detalhes</span>
											<span class="toggle-icon">▼</span>
										</button>
										
										<!-- Conteúdo expandível -->
										<div class="evento-card-details" id="evento-details-<?php the_ID(); ?>" style="display: none;">
											<?php
											$event_location = get_post_meta( get_the_ID(), 'etn_location', true );
											$event_organizer = get_post_meta( get_the_ID(), 'etn_organizer', true );
											$event_schedule = get_post_meta( get_the_ID(), 'etn_event_schedule', true );
											$event_category = sg_detect_event_category( get_the_title() );
											$categorias_nomes = array(
												'ministerio-publico' => 'Ministério Público',
												'magistratura' => 'Magistratura',
												'delegado' => 'Delegado',
												'enam' => 'ENAM',
												'procuradoria' => 'Procuradoria',
											);
											$categoria_nome = isset( $categorias_nomes[ $event_category ] ) ? $categorias_nomes[ $event_category ] : '';
											?>
											<div class="evento-details-content">
												<h4>Informações do Concurso</h4>
												
												<?php if ( $categoria_nome ) : ?>
													<div class="evento-detail-item">
														<strong>Categoria:</strong> <?php echo esc_html( $categoria_nome ); ?>
													</div>
												<?php endif; ?>
												
												<?php if ( $start_date ) : ?>
													<div class="evento-detail-item">
														<strong>📅 Data de Início:</strong> <?php echo esc_html( $full_date ); ?>
													</div>
												<?php endif; ?>
												
												<?php if ( $end_date && $end_date !== $start_date ) : 
													$end_full_date = date_i18n( 'd/m/Y', strtotime( $end_date ) );
												?>
													<div class="evento-detail-item">
														<strong>📅 Data de Término:</strong> <?php echo esc_html( $end_full_date ); ?>
													</div>
												<?php endif; ?>
												
												<?php if ( $event_location ) : ?>
													<div class="evento-detail-item">
														<strong>📍 Local:</strong> <?php echo esc_html( $event_location ); ?>
													</div>
												<?php endif; ?>
												
												<?php if ( $event_organizer ) : ?>
													<div class="evento-detail-item">
														<strong>👤 Organizador:</strong> <?php echo esc_html( $event_organizer ); ?>
													</div>
												<?php endif; ?>
												
												<div class="evento-detail-description">
													<?php 
													$content = get_the_content();
													if ( empty( $content ) ) {
														$content = get_the_excerpt();
													}
													if ( ! empty( $content ) ) {
														echo wp_kses_post( wpautop( $content ) );
													}
													?>
												</div>
												
												<?php if ( $event_schedule && is_array( $event_schedule ) ) : ?>
													<div class="evento-detail-schedule">
														<h5>Programação</h5>
														<ul>
															<?php foreach ( $event_schedule as $schedule_item ) : ?>
																<li><?php echo esc_html( $schedule_item ); ?></li>
															<?php endforeach; ?>
														</ul>
													</div>
												<?php endif; ?>
												
												<a href="<?php the_permalink(); ?>" class="evento-detail-link" target="_blank">
													Ver página completa →
												</a>
											</div>
										</div>
									</div>
								</div>
							</article>
						<?php endwhile; ?>
					</div>
					
					<?php
					// Paginação
					$big = 999999999;
					echo paginate_links( array(
						'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
						'format'  => '?paged=%#%',
						'current' => max( 1, $paged ),
						'total'   => $events_query->max_num_pages,
					) );
					?>
					
					<?php wp_reset_postdata(); ?>
				<?php else : ?>
					<div class="no-events">
						<p>Nenhum evento encontrado.</p>
						<?php if ( ! empty( $_GET['mes'] ) || ! empty( $_GET['ano'] ) || isset( $_GET['apenas_futuros'] ) ) : ?>
							<a href="<?php echo esc_url( get_post_type_archive_link( 'etn' ) ); ?>" class="btn-limpar">Ver todos os eventos</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</main>

<?php
get_footer();
?>
