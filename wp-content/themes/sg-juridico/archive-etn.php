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
				// ATUALIZADO: Buscar eventos próprios (sg_eventos) e ETN
				global $wp_query;
				$today = current_time( 'Y-m-d' );
				$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
				
				// Tipos de post a buscar (prioridade para sg_eventos)
				// Verificar diretamente no banco de dados se existem eventos, mesmo que post types não estejam registrados
				global $wpdb;
				$post_types = array();
				
				// Verificar se sg_eventos existe OU se há eventos sg_eventos no banco
				if ( post_type_exists( 'sg_eventos' ) ) {
					$post_types[] = 'sg_eventos';
				} else {
					$count_sg = $wpdb->get_var( $wpdb->prepare(
						"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish'",
						'sg_eventos'
					) );
					if ( $count_sg > 0 ) {
						$post_types[] = 'sg_eventos';
					}
				}
				
				// Verificar se etn existe OU se há eventos etn no banco
				if ( post_type_exists( 'etn' ) ) {
					$post_types[] = 'etn';
				} else {
					$count_etn = $wpdb->get_var( $wpdb->prepare(
						"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish'",
						'etn'
					) );
					if ( $count_etn > 0 ) {
						$post_types[] = 'etn';
					}
				}
				
				// Se não tiver nenhum tipo disponível, mostrar mensagem
				if ( empty( $post_types ) ) {
					echo '<div class="no-events"><p>Nenhum sistema de eventos configurado.</p></div>';
					get_footer();
					return;
				}
				
				// Para múltiplos tipos de post, usar meta_query mais complexo
				$args = array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => 20,
					'paged'          => $paged,
					'order'          => 'ASC',
					'fields'         => 'all',
					'update_post_term_cache' => true,
					'update_post_meta_cache' => true,
					'meta_query'     => array(
						'relation' => 'OR',
						array(
							'key'     => '_sg_evento_data_inicio',
							'compare' => 'EXISTS',
						),
						array(
							'key'     => 'etn_start_date',
							'compare' => 'EXISTS',
						),
					),
					'orderby'         => 'meta_value',
					'meta_key'       => '_sg_evento_data_inicio',
					'meta_type'      => 'DATE',
				);
				
				// Buscar todos os posts primeiro para filtrar por categoria
				// Tentar usar WP_Query primeiro
				$all_events_query = new WP_Query( array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'fields'         => 'all',
					'update_post_term_cache' => true,
					'update_post_meta_cache' => true,
					'meta_query'     => array(
						'relation' => 'OR',
						array(
							'key'     => '_sg_evento_data_inicio',
							'compare' => 'EXISTS',
						),
						array(
							'key'     => 'etn_start_date',
							'compare' => 'EXISTS',
						),
					),
					'orderby'         => 'meta_value',
					'meta_key'       => '_sg_evento_data_inicio',
					'meta_type'      => 'DATE',
					'order'          => 'ASC',
				) );
				
				// Se WP_Query não retornou posts mas sabemos que existem eventos no banco, buscar diretamente
				if ( $all_events_query->post_count === 0 ) {
					$post_types_escaped = array_map( 'esc_sql', $post_types );
					$post_types_sql = "'" . implode( "','", $post_types_escaped ) . "'";
					
					$results = $wpdb->get_results( "
						SELECT p.ID
						FROM {$wpdb->posts} p
						LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_sg_evento_data_inicio'
						LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = 'etn_start_date'
						WHERE p.post_type IN ($post_types_sql)
						AND p.post_status = 'publish'
						AND (pm1.meta_value IS NOT NULL OR pm2.meta_value IS NOT NULL)
						ORDER BY COALESCE(pm1.meta_value, pm2.meta_value) ASC
					" );
					
					if ( ! empty( $results ) ) {
						// Criar um objeto WP_Query simulado com os IDs encontrados
						$ids = array_map( 'intval', wp_list_pluck( $results, 'ID' ) );
						$all_events_query = new WP_Query( array(
							'post__in'       => $ids,
							'post_status'    => 'publish',
							'posts_per_page' => -1,
							'orderby'        => 'post__in',
							'order'          => 'ASC',
							'fields'         => 'all', // Definir fields explicitamente
							'update_post_term_cache' => true,
							'update_post_meta_cache' => true,
						) );
					}
				}
				
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
					
					// Se não tem filtro de categoria, buscar todos os posts
					if ( empty( $ids_to_filter ) && empty( $_GET['categoria'] ) ) {
						// Se já temos posts da query inicial, usar eles
						if ( $all_events_query->have_posts() ) {
							$ids_to_filter = wp_list_pluck( $all_events_query->posts, 'ID' );
						} else {
							// Se não temos posts, buscar diretamente do banco
							$post_types_escaped = array_map( 'esc_sql', $post_types );
							$post_types_sql = "'" . implode( "','", $post_types_escaped ) . "'";
							$temp_results = $wpdb->get_results( "
								SELECT p.ID
								FROM {$wpdb->posts} p
								LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_sg_evento_data_inicio'
								LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = 'etn_start_date'
								WHERE p.post_type IN ($post_types_sql)
								AND p.post_status = 'publish'
								AND (pm1.meta_value IS NOT NULL OR pm2.meta_value IS NOT NULL)
							" );
							if ( ! empty( $temp_results ) ) {
								$ids_to_filter = array_map( 'intval', wp_list_pluck( $temp_results, 'ID' ) );
							}
						}
					}
					
					// Aplicar filtros de data
					foreach ( $ids_to_filter as $post_id ) {
						$post_type = get_post_type( $post_id );
						// Detectar qual meta key usar baseado no tipo de post
						$meta_key_start = ( $post_type === 'sg_eventos' ) ? '_sg_evento_data_inicio' : 'etn_start_date';
						$start_date = get_post_meta( $post_id, $meta_key_start, true );
						
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
				
				// Se WP_Query não retornou posts mas temos IDs filtrados, usar busca direta
				if ( $events_query->post_count === 0 && ! empty( $final_filtered_ids ) ) {
					$posts = array();
					foreach ( $final_filtered_ids as $post_id ) {
						$post = get_post( $post_id );
						if ( $post && $post->post_status === 'publish' ) {
							$posts[] = $post;
						}
					}
					
					if ( ! empty( $posts ) ) {
						// Criar objeto WP_Query customizado com os posts carregados
						$events_query = new WP_Query( array(
							'post__in'                => wp_list_pluck( $posts, 'ID' ),
							'post_status'            => 'publish',
							'posts_per_page'         => 20,
							'paged'                  => $paged,
							'orderby'                => 'post__in',
							'order'                  => 'ASC',
							'fields'                 => 'all',
							'update_post_term_cache' => true,
							'update_post_meta_cache' => true,
						) );
						// Sobrescrever propriedades para manter compatibilidade
						$events_query->posts = $posts;
						$events_query->post_count = count( $posts );
						$events_query->found_posts = count( $posts );
						$events_query->max_num_pages = ceil( count( $posts ) / 20 );
						$events_query->current_post = -1;
					}
				}
				
				if ( $events_query->have_posts() ) :
				?>
					<div class="eventos-grid">
						<?php while ( $events_query->have_posts() ) : $events_query->the_post(); 
							$current_post_type = get_post_type();
							// Detectar qual meta key usar baseado no tipo de post
							$meta_key_start = ( $current_post_type === 'sg_eventos' ) ? '_sg_evento_data_inicio' : 'etn_start_date';
							$meta_key_end = ( $current_post_type === 'sg_eventos' ) ? '_sg_evento_data_fim' : 'etn_end_date';
							$meta_key_location = ( $current_post_type === 'sg_eventos' ) ? '_sg_evento_local' : 'etn_location';
							
							$start_date = get_post_meta( get_the_ID(), $meta_key_start, true );
							$end_date = get_post_meta( get_the_ID(), $meta_key_end, true );
							
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
											$event_location = get_post_meta( get_the_ID(), $meta_key_location, true );
											$event_address = ( $current_post_type === 'sg_eventos' ) ? get_post_meta( get_the_ID(), '_sg_evento_endereco', true ) : '';
											$event_organizer = ( $current_post_type === 'sg_eventos' ) ? '' : get_post_meta( get_the_ID(), 'etn_organizer', true );
											$event_schedule = get_post_meta( get_the_ID(), 'etn_event_schedule', true );
											$event_category = sg_detect_event_category( get_the_title() );
											
											// Carregar horários e datas de inscrição
											$hora_inicio = get_post_meta( get_the_ID(), '_sg_evento_hora_inicio', true );
											$hora_fim = get_post_meta( get_the_ID(), '_sg_evento_hora_fim', true );
											$data_inscricao_inicio = get_post_meta( get_the_ID(), '_sg_evento_inscricao_inicio', true );
											$data_inscricao_fim = get_post_meta( get_the_ID(), '_sg_evento_inscricao_fim', true );
											
											// Formatar horários
											if ( ! empty( $hora_inicio ) && strlen( $hora_inicio ) >= 5 ) {
												$hora_inicio = substr( $hora_inicio, 0, 5 );
											}
											if ( ! empty( $hora_fim ) && strlen( $hora_fim ) >= 5 ) {
												$hora_fim = substr( $hora_fim, 0, 5 );
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
											
											// Se for sg_eventos, tentar pegar categoria da taxonomia
											if ( $current_post_type === 'sg_eventos' ) {
												$tax_terms = get_the_terms( get_the_ID(), 'sg_evento_categoria' );
												if ( $tax_terms && ! is_wp_error( $tax_terms ) ) {
													$event_category = $tax_terms[0]->slug;
												}
											}
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
														<strong>📅 Data da Realização:</strong> <?php echo esc_html( $full_date ); ?>
														<?php if ( ! empty( $hora_inicio ) ) : ?>
															 às <?php echo esc_html( $hora_inicio ); ?>
														<?php endif; ?>
													</div>
												<?php endif; ?>
												
												<?php if ( ! empty( $hora_fim ) ) : ?>
													<div class="evento-detail-item">
														<strong>⏰ Horário de Término:</strong> <?php echo esc_html( $hora_fim ); ?>
													</div>
												<?php endif; ?>
												
												<?php if ( ! empty( $data_inscricao_inicio ) || ! empty( $data_inscricao_fim ) ) : ?>
													<div class="evento-detail-item">
														<strong>📝 Período de Inscrições:</strong>
														<?php if ( ! empty( $data_inscricao_inicio ) ) : ?>
															<?php echo esc_html( $data_inscricao_inicio_formatted ); ?>
														<?php endif; ?>
														<?php if ( ! empty( $data_inscricao_inicio ) && ! empty( $data_inscricao_fim ) ) : ?>
															 até 
														<?php endif; ?>
														<?php if ( ! empty( $data_inscricao_fim ) ) : ?>
															<?php echo esc_html( $data_inscricao_fim_formatted ); ?>
														<?php endif; ?>
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
												
												<?php if ( $event_address ) : ?>
													<div class="evento-detail-item">
														<strong>📍 Endereço:</strong> <?php echo esc_html( $event_address ); ?>
													</div>
												<?php endif; ?>
												
												<?php if ( $event_organizer ) : ?>
													<div class="evento-detail-item">
														<strong>👤 Organizador:</strong> <?php echo esc_html( $event_organizer ); ?>
													</div>
												<?php endif; ?>
												
												<?php
												// Buscar descrição/conteúdo do post
												$event_description = get_the_content();
												if ( empty( $event_description ) ) {
													$event_description = get_the_excerpt();
												}
												// Limpar shortcodes e tags HTML se necessário
												$event_description = apply_filters( 'the_content', $event_description );
												?>
												<?php if ( ! empty( $event_description ) ) : ?>
													<div class="evento-detail-description">
														<h5>Descrição</h5>
														<?php echo wp_kses_post( $event_description ); ?>
													</div>
												<?php endif; ?>
												
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
												
												<a href="<?php the_permalink(); ?>" class="evento-detail-link">
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
