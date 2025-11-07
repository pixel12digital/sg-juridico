<?php
/**
 * Theme functions and definitions
 *
 * @package SG_Juridico
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SG_VERSION', '1.0.0' );

/**
 * Setup theme defaults
 */
function sg_setup() {
	load_theme_textdomain( 'sg-juridico', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'script',
		'style',
	) );

	add_theme_support( 'custom-logo', array(
		'height'      => 100,
		'width'       => 350,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'sg-juridico' ),
		'footer'  => esc_html__( 'Footer Menu', 'sg-juridico' ),
	) );

	if ( class_exists( 'WooCommerce' ) ) {
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}
}
add_action( 'after_setup_theme', 'sg_setup' );

/**
 * ============================================
 * SISTEMA PRÓPRIO DE EVENTOS - SG JURÍDICO
 * ============================================
 * Sistema completo de gerenciamento de eventos
 * sem dependência de plugins externos
 */

/**
 * Registrar Custom Post Type: sg_eventos
 */
function sg_register_eventos_post_type() {
	$labels = array(
		'name'                  => _x( 'Eventos', 'Post Type General Name', 'sg-juridico' ),
		'singular_name'         => _x( 'Evento', 'Post Type Singular Name', 'sg-juridico' ),
		'menu_name'             => __( 'Eventos', 'sg-juridico' ),
		'name_admin_bar'        => __( 'Evento', 'sg-juridico' ),
		'archives'              => __( 'Arquivo de Eventos', 'sg-juridico' ),
		'attributes'            => __( 'Atributos do Evento', 'sg-juridico' ),
		'parent_item_colon'     => __( 'Evento Pai:', 'sg-juridico' ),
		'all_items'             => __( 'Todos os Eventos', 'sg-juridico' ),
		'add_new_item'          => __( 'Adicionar Novo Evento', 'sg-juridico' ),
		'add_new'               => __( 'Adicionar Novo', 'sg-juridico' ),
		'new_item'              => __( 'Novo Evento', 'sg-juridico' ),
		'edit_item'             => __( 'Editar Evento', 'sg-juridico' ),
		'update_item'           => __( 'Atualizar Evento', 'sg-juridico' ),
		'view_item'             => __( 'Ver Evento', 'sg-juridico' ),
		'view_items'            => __( 'Ver Eventos', 'sg-juridico' ),
		'search_items'          => __( 'Buscar Eventos', 'sg-juridico' ),
		'not_found'             => __( 'Não encontrado', 'sg-juridico' ),
		'not_found_in_trash'    => __( 'Não encontrado na lixeira', 'sg-juridico' ),
		'featured_image'        => __( 'Imagem do Evento', 'sg-juridico' ),
		'set_featured_image'    => __( 'Definir imagem do evento', 'sg-juridico' ),
		'remove_featured_image' => __( 'Remover imagem do evento', 'sg-juridico' ),
		'use_featured_image'    => __( 'Usar como imagem do evento', 'sg-juridico' ),
		'insert_into_item'      => __( 'Inserir no evento', 'sg-juridico' ),
		'uploaded_to_this_item' => __( 'Carregado para este evento', 'sg-juridico' ),
		'items_list'            => __( 'Lista de eventos', 'sg-juridico' ),
		'items_list_navigation' => __( 'Navegação da lista de eventos', 'sg-juridico' ),
		'filter_items_list'     => __( 'Filtrar lista de eventos', 'sg-juridico' ),
	);

	$args = array(
		'label'                 => __( 'Evento', 'sg-juridico' ),
		'description'           => __( 'Gerenciamento de eventos do calendário', 'sg-juridico' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		'taxonomies'            => array( 'sg_evento_categoria' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 25,
		'menu_icon'             => 'dashicons-calendar-alt',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'rewrite'               => array(
			'slug'       => 'eventos',
			'with_front' => false,
		),
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'show_in_rest'          => true,
	);

	register_post_type( 'sg_eventos', $args );
}
add_action( 'init', 'sg_register_eventos_post_type', 0 );

/**
 * Registrar Taxonomia: Categorias de Eventos
 */
function sg_register_evento_categoria_taxonomy() {
	$labels = array(
		'name'              => _x( 'Categorias de Eventos', 'taxonomy general name', 'sg-juridico' ),
		'singular_name'     => _x( 'Categoria de Evento', 'taxonomy singular name', 'sg-juridico' ),
		'search_items'      => __( 'Buscar Categorias', 'sg-juridico' ),
		'all_items'         => __( 'Todas as Categorias', 'sg-juridico' ),
		'parent_item'       => __( 'Categoria Pai', 'sg-juridico' ),
		'parent_item_colon' => __( 'Categoria Pai:', 'sg-juridico' ),
		'edit_item'         => __( 'Editar Categoria', 'sg-juridico' ),
		'update_item'       => __( 'Atualizar Categoria', 'sg-juridico' ),
		'add_new_item'      => __( 'Adicionar Nova Categoria', 'sg-juridico' ),
		'new_item_name'     => __( 'Nome da Nova Categoria', 'sg-juridico' ),
		'menu_name'         => __( 'Categorias', 'sg-juridico' ),
	);

	$args = array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => false,
		'rewrite'           => array( 'slug' => 'categoria-evento' ),
		'show_in_rest'      => true,
	);

	register_taxonomy( 'sg_evento_categoria', array( 'sg_eventos' ), $args );
}
add_action( 'init', 'sg_register_evento_categoria_taxonomy', 0 );

/**
 * Adicionar Meta Boxes para campos de eventos
 */
function sg_add_eventos_meta_boxes() {
	// Adicionar meta box para sg_eventos
	add_meta_box(
		'sg_evento_detalhes',
		__( 'Detalhes do Evento', 'sg-juridico' ),
		'sg_evento_detalhes_callback',
		'sg_eventos',
		'normal',
		'high'
	);
	
	// Adicionar meta box para tribe_events
	add_meta_box(
		'sg_evento_detalhes',
		__( 'Detalhes do Evento', 'sg-juridico' ),
		'sg_evento_detalhes_callback',
		'tribe_events',
		'normal',
		'high'
	);
	
	// Adicionar meta box para etn
	add_meta_box(
		'sg_evento_detalhes',
		__( 'Detalhes do Evento', 'sg-juridico' ),
		'sg_evento_detalhes_callback',
		'etn',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'sg_add_eventos_meta_boxes' );

/**
 * Callback do Meta Box de Detalhes
 */
function sg_evento_detalhes_callback( $post ) {
	wp_nonce_field( 'sg_evento_detalhes_nonce', 'sg_evento_detalhes_nonce' );
	
	$post_type = get_post_type( $post->ID );
	
	// Detectar meta keys baseado no tipo de post
	if ( $post_type === 'sg_eventos' ) {
		$meta_key_start = '_sg_evento_data_inicio';
		$meta_key_end = '_sg_evento_data_fim';
		$meta_key_location = '_sg_evento_local';
		$meta_key_address = '_sg_evento_endereco';
	} elseif ( $post_type === 'tribe_events' ) {
		$meta_key_start = '_EventStartDate';
		$meta_key_end = '_EventEndDate';
		$meta_key_location = '_EventVenue';
		$meta_key_address = '_sg_evento_endereco'; // Usar nosso próprio campo
	} else {
		// ETN
		$meta_key_start = 'etn_start_date';
		$meta_key_end = 'etn_end_date';
		$meta_key_location = 'etn_location';
		$meta_key_address = '_sg_evento_endereco'; // Usar nosso próprio campo
	}
	
	$data_inicio = get_post_meta( $post->ID, $meta_key_start, true );
	$data_fim = get_post_meta( $post->ID, $meta_key_end, true );
	$local = get_post_meta( $post->ID, $meta_key_location, true );
	$endereco = get_post_meta( $post->ID, $meta_key_address, true );
	$link_externo = get_post_meta( $post->ID, '_sg_evento_link_externo', true );
	
	// Se não encontrou valores, tentar outras meta keys como fallback
	if ( empty( $data_inicio ) ) {
		// Tentar outras meta keys possíveis
		$possible_keys = array( '_sg_evento_data_inicio', '_EventStartDate', 'etn_start_date' );
		foreach ( $possible_keys as $key ) {
			if ( $key !== $meta_key_start ) {
				$value = get_post_meta( $post->ID, $key, true );
				if ( ! empty( $value ) ) {
					$data_inicio = $value;
					break;
				}
			}
		}
	}
	
	if ( empty( $data_fim ) ) {
		$possible_keys = array( '_sg_evento_data_fim', '_EventEndDate', 'etn_end_date' );
		foreach ( $possible_keys as $key ) {
			if ( $key !== $meta_key_end ) {
				$value = get_post_meta( $post->ID, $key, true );
				if ( ! empty( $value ) ) {
					$data_fim = $value;
					break;
				}
			}
		}
	}
	
	if ( empty( $local ) ) {
		$possible_keys = array( '_sg_evento_local', '_EventVenue', 'etn_location' );
		foreach ( $possible_keys as $key ) {
			if ( $key !== $meta_key_location ) {
				$value = get_post_meta( $post->ID, $key, true );
				if ( ! empty( $value ) ) {
					$local = $value;
					break;
				}
			}
		}
	}
	
	// Para tribe_events, converter timestamp ou datetime para date
	if ( $post_type === 'tribe_events' ) {
		if ( ! empty( $data_inicio ) ) {
			// Tribe Events geralmente usa formato datetime (YYYY-MM-DD HH:MM:SS)
			if ( strpos( $data_inicio, ' ' ) !== false ) {
				// Se for datetime string, extrair apenas a data
				$data_inicio = date( 'Y-m-d', strtotime( $data_inicio ) );
			} elseif ( is_numeric( $data_inicio ) ) {
				// Se for timestamp numérico
				$data_inicio = date( 'Y-m-d', $data_inicio );
			} elseif ( strtotime( $data_inicio ) !== false ) {
				// Se for string de data válida
				$data_inicio = date( 'Y-m-d', strtotime( $data_inicio ) );
			}
		}
		if ( ! empty( $data_fim ) ) {
			if ( strpos( $data_fim, ' ' ) !== false ) {
				// Se for datetime string, extrair apenas a data
				$data_fim = date( 'Y-m-d', strtotime( $data_fim ) );
			} elseif ( is_numeric( $data_fim ) ) {
				// Se for timestamp numérico
				$data_fim = date( 'Y-m-d', $data_fim );
			} elseif ( strtotime( $data_fim ) !== false ) {
				// Se for string de data válida
				$data_fim = date( 'Y-m-d', strtotime( $data_fim ) );
			}
		}
	}
	
	// Para ETN, garantir formato correto se necessário
	if ( $post_type === 'etn' && ! empty( $data_inicio ) ) {
		// Verificar se está em formato correto para input date (YYYY-MM-DD)
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $data_inicio ) ) {
			// Tentar converter para formato YYYY-MM-DD
			$timestamp = strtotime( $data_inicio );
			if ( $timestamp !== false ) {
				$data_inicio = date( 'Y-m-d', $timestamp );
			}
		}
	}
	if ( $post_type === 'etn' && ! empty( $data_fim ) ) {
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $data_fim ) ) {
			$timestamp = strtotime( $data_fim );
			if ( $timestamp !== false ) {
				$data_fim = date( 'Y-m-d', $timestamp );
			}
		}
	}
	
	// Extrair hora se houver timestamp
	$hora_inicio = '';
	$hora_fim = '';
	// Carregar horários para todos os tipos de evento
	$hora_inicio = get_post_meta( $post->ID, '_sg_evento_hora_inicio', true );
	$hora_fim = get_post_meta( $post->ID, '_sg_evento_hora_fim', true );
	
	// Carregar datas de inscrição
	$data_inscricao_inicio = get_post_meta( $post->ID, '_sg_evento_inscricao_inicio', true );
	$data_inscricao_fim = get_post_meta( $post->ID, '_sg_evento_inscricao_fim', true );
	
	// Formatar datas de inscrição se necessário
	if ( ! empty( $data_inscricao_inicio ) && ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $data_inscricao_inicio ) ) {
		$timestamp = strtotime( $data_inscricao_inicio );
		if ( $timestamp !== false ) {
			$data_inscricao_inicio = date( 'Y-m-d', $timestamp );
		}
	}
	if ( ! empty( $data_inscricao_fim ) && ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $data_inscricao_fim ) ) {
		$timestamp = strtotime( $data_inscricao_fim );
		if ( $timestamp !== false ) {
			$data_inscricao_fim = date( 'Y-m-d', $timestamp );
		}
	}
	
	// Detectar categoria
	$categoria_atual = '';
	if ( $post_type === 'sg_eventos' ) {
		$terms = get_the_terms( $post->ID, 'sg_evento_categoria' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$categoria_atual = $terms[0]->slug;
		}
	} else {
		// Para tribe_events e etn, buscar categoria salva em meta
		$categoria_atual = get_post_meta( $post->ID, '_sg_evento_categoria', true );
		// Se não encontrar no meta, tentar detectar pelo título
		if ( empty( $categoria_atual ) ) {
			$categoria_atual = sg_detect_event_category( get_the_title( $post->ID ) );
		}
	}

	?>
	<table class="form-table">
		<?php if ( $post_type !== 'sg_eventos' ) : ?>
		<tr>
			<th><label for="sg_evento_categoria"><?php _e( 'Categoria', 'sg-juridico' ); ?></label></th>
			<td>
				<select id="sg_evento_categoria" name="sg_evento_categoria" class="regular-text">
					<option value=""><?php _e( 'Selecione uma categoria', 'sg-juridico' ); ?></option>
					<?php
					$categorias = array(
						'ministerio-publico' => 'Ministério Público',
						'magistratura' => 'Magistratura',
						'delegado' => 'Delegado',
						'enam' => 'ENAM',
						'procuradoria' => 'Procuradoria',
					);
					foreach ( $categorias as $slug => $nome ) :
					?>
						<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $categoria_atual, $slug ); ?>>
							<?php echo esc_html( $nome ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<p class="description"><?php _e( 'Categoria do evento', 'sg-juridico' ); ?></p>
			</td>
		</tr>
		<?php endif; ?>
		<tr>
			<th><label for="sg_evento_data_inicio"><?php _e( 'Data da Realização', 'sg-juridico' ); ?></label></th>
			<td>
				<input type="date" id="sg_evento_data_inicio" name="sg_evento_data_inicio" value="<?php echo esc_attr( $data_inicio ); ?>" class="regular-text" required />
				<p class="description"><?php _e( 'Data em que o candidato irá prestar a prova', 'sg-juridico' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="sg_evento_hora_inicio"><?php _e( 'Horário Início', 'sg-juridico' ); ?></label></th>
			<td>
				<input type="time" id="sg_evento_hora_inicio" name="sg_evento_hora_inicio" value="<?php echo esc_attr( $hora_inicio ); ?>" class="regular-text" />
				<p class="description"><?php _e( 'Horário de início do evento (opcional)', 'sg-juridico' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="sg_evento_hora_fim"><?php _e( 'Horário Término', 'sg-juridico' ); ?></label></th>
			<td>
				<input type="time" id="sg_evento_hora_fim" name="sg_evento_hora_fim" value="<?php echo esc_attr( $hora_fim ); ?>" class="regular-text" />
				<p class="description"><?php _e( 'Horário de término do evento (opcional)', 'sg-juridico' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="sg_evento_inscricao_inicio"><?php _e( 'Data de Início das Inscrições', 'sg-juridico' ); ?></label></th>
			<td>
				<input type="date" id="sg_evento_inscricao_inicio" name="sg_evento_inscricao_inicio" value="<?php echo esc_attr( $data_inscricao_inicio ); ?>" class="regular-text" />
				<p class="description"><?php _e( 'Data de início do período de inscrições (opcional)', 'sg-juridico' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="sg_evento_inscricao_fim"><?php _e( 'Data de Término das Inscrições', 'sg-juridico' ); ?></label></th>
			<td>
				<input type="date" id="sg_evento_inscricao_fim" name="sg_evento_inscricao_fim" value="<?php echo esc_attr( $data_inscricao_fim ); ?>" class="regular-text" />
				<p class="description"><?php _e( 'Data de término do período de inscrições (opcional)', 'sg-juridico' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="sg_evento_local"><?php _e( 'Local', 'sg-juridico' ); ?></label></th>
			<td>
				<input type="text" id="sg_evento_local" name="sg_evento_local" value="<?php echo esc_attr( $local ); ?>" class="regular-text" />
				<p class="description"><?php _e( 'Nome do local do evento', 'sg-juridico' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="sg_evento_endereco"><?php _e( 'Endereço', 'sg-juridico' ); ?></label></th>
			<td>
				<textarea id="sg_evento_endereco" name="sg_evento_endereco" rows="3" class="large-text"><?php echo esc_textarea( $endereco ); ?></textarea>
				<p class="description"><?php _e( 'Endereço completo do evento', 'sg-juridico' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="sg_evento_link_externo"><?php _e( 'Link Externo', 'sg-juridico' ); ?></label></th>
			<td>
				<input type="url" id="sg_evento_link_externo" name="sg_evento_link_externo" value="<?php echo esc_url( $link_externo ); ?>" class="regular-text" />
				<p class="description"><?php _e( 'Link externo relacionado ao evento (opcional)', 'sg-juridico' ); ?></p>
			</td>
		</tr>
	</table>
	<?php
}

/**
 * Salvar Meta Boxes
 */
function sg_save_evento_meta_boxes( $post_id ) {
	// Verificar nonce
	if ( ! isset( $_POST['sg_evento_detalhes_nonce'] ) || ! wp_verify_nonce( $_POST['sg_evento_detalhes_nonce'], 'sg_evento_detalhes_nonce' ) ) {
		return;
	}

	// Verificar autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Verificar permissões
	$post_type = isset( $_POST['post_type'] ) ? $_POST['post_type'] : get_post_type( $post_id );
	if ( ! in_array( $post_type, array( 'sg_eventos', 'etn', 'tribe_events' ) ) ) {
		return;
	}
	
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	
	// Detectar meta keys baseado no tipo de post
	if ( $post_type === 'sg_eventos' ) {
		$meta_key_start = '_sg_evento_data_inicio';
		$meta_key_end = '_sg_evento_data_fim';
		$meta_key_location = '_sg_evento_local';
	} elseif ( $post_type === 'tribe_events' ) {
		$meta_key_start = '_EventStartDate';
		$meta_key_end = '_EventEndDate';
		$meta_key_location = '_EventVenue';
	} else {
		// ETN
		$meta_key_start = 'etn_start_date';
		$meta_key_end = 'etn_end_date';
		$meta_key_location = 'etn_location';
	}

	// Salvar campos de data
	if ( isset( $_POST['sg_evento_data_inicio'] ) ) {
		$data_inicio = sanitize_text_field( $_POST['sg_evento_data_inicio'] );
		if ( $post_type === 'tribe_events' && ! empty( $data_inicio ) ) {
			// Converter para timestamp para tribe_events
			$timestamp = strtotime( $data_inicio );
			update_post_meta( $post_id, $meta_key_start, $timestamp );
		} else {
			update_post_meta( $post_id, $meta_key_start, $data_inicio );
		}
	}
	
	// Salvar campos de hora para todos os tipos de evento
	if ( isset( $_POST['sg_evento_hora_inicio'] ) ) {
		update_post_meta( $post_id, '_sg_evento_hora_inicio', sanitize_text_field( $_POST['sg_evento_hora_inicio'] ) );
	}
	if ( isset( $_POST['sg_evento_hora_fim'] ) ) {
		update_post_meta( $post_id, '_sg_evento_hora_fim', sanitize_text_field( $_POST['sg_evento_hora_fim'] ) );
	}
	
	// Salvar datas de inscrição
	if ( isset( $_POST['sg_evento_inscricao_inicio'] ) ) {
		update_post_meta( $post_id, '_sg_evento_inscricao_inicio', sanitize_text_field( $_POST['sg_evento_inscricao_inicio'] ) );
	}
	if ( isset( $_POST['sg_evento_inscricao_fim'] ) ) {
		update_post_meta( $post_id, '_sg_evento_inscricao_fim', sanitize_text_field( $_POST['sg_evento_inscricao_fim'] ) );
	}

	// Salvar local
	if ( isset( $_POST['sg_evento_local'] ) ) {
		update_post_meta( $post_id, $meta_key_location, sanitize_text_field( $_POST['sg_evento_local'] ) );
	}

	// Salvar endereço (sempre usar nosso próprio campo)
	if ( isset( $_POST['sg_evento_endereco'] ) ) {
		update_post_meta( $post_id, '_sg_evento_endereco', sanitize_textarea_field( $_POST['sg_evento_endereco'] ) );
	}

	// Salvar link externo
	if ( isset( $_POST['sg_evento_link_externo'] ) ) {
		update_post_meta( $post_id, '_sg_evento_link_externo', esc_url_raw( $_POST['sg_evento_link_externo'] ) );
	}
	
	// Salvar categoria (para tribe_events e etn)
	if ( $post_type !== 'sg_eventos' && isset( $_POST['sg_evento_categoria'] ) ) {
		$categoria = sanitize_text_field( $_POST['sg_evento_categoria'] );
		// Salvar como meta para uso futuro
		update_post_meta( $post_id, '_sg_evento_categoria', $categoria );
	}

	// Limpar cache de eventos
	sg_clear_sg_eventos_cache();
}
add_action( 'save_post', 'sg_save_evento_meta_boxes' );

/**
 * Limpar cache de eventos SG
 */
function sg_clear_sg_eventos_cache() {
	wp_cache_delete( 'sg_all_calendar_events', 'sg_events' );
	wp_cache_delete( 'sg_concurso_events_' . md5( serialize( array( 10, null ) ) ), 'sg_events' );
}

/**
 * Adicionar colunas customizadas na listagem de eventos
 */
function sg_eventos_admin_columns( $columns ) {
	$new_columns = array();
	$new_columns['cb'] = $columns['cb'];
	$new_columns['title'] = $columns['title'];
	$new_columns['sg_evento_data'] = __( 'Data do Evento', 'sg-juridico' );
	$new_columns['sg_evento_categoria'] = __( 'Categoria', 'sg-juridico' );
	$new_columns['sg_evento_local'] = __( 'Local', 'sg-juridico' );
	$new_columns['date'] = $columns['date'];
	return $new_columns;
}
add_filter( 'manage_sg_eventos_posts_columns', 'sg_eventos_admin_columns' );

/**
 * Preencher colunas customizadas
 */
function sg_eventos_admin_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'sg_evento_data':
			$data_inicio = get_post_meta( $post_id, '_sg_evento_data_inicio', true );
			$hora_inicio = get_post_meta( $post_id, '_sg_evento_hora_inicio', true );
			if ( $data_inicio ) {
				$data_formatada = date_i18n( 'd/m/Y', strtotime( $data_inicio ) );
				if ( $hora_inicio ) {
					$data_formatada .= ' ' . $hora_inicio;
				}
				echo esc_html( $data_formatada );
			} else {
				echo '<span style="color: #d63638;">—</span>';
			}
			break;

		case 'sg_evento_categoria':
			$terms = get_the_terms( $post_id, 'sg_evento_categoria' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$term_names = array();
				foreach ( $terms as $term ) {
					$term_names[] = $term->name;
				}
				echo esc_html( implode( ', ', $term_names ) );
			} else {
				echo '<span style="color: #999;">—</span>';
			}
			break;

		case 'sg_evento_local':
			$local = get_post_meta( $post_id, '_sg_evento_local', true );
			if ( $local ) {
				echo esc_html( $local );
			} else {
				echo '<span style="color: #999;">—</span>';
			}
			break;
	}
}
add_action( 'manage_sg_eventos_posts_custom_column', 'sg_eventos_admin_column_content', 10, 2 );

/**
 * Tornar colunas ordenáveis
 */
function sg_eventos_sortable_columns( $columns ) {
	$columns['sg_evento_data'] = 'sg_evento_data';
	return $columns;
}
add_filter( 'manage_edit-sg_eventos_sortable_columns', 'sg_eventos_sortable_columns' );

/**
 * Ordenar por data do evento
 */
function sg_eventos_orderby( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( 'sg_evento_data' === $query->get( 'orderby' ) ) {
		$query->set( 'meta_key', '_sg_evento_data_inicio' );
		$query->set( 'orderby', 'meta_value' );
	}
}
add_action( 'pre_get_posts', 'sg_eventos_orderby' );

/**
 * Configurar archive para eventos SG
 */
function sg_setup_sg_eventos_archive() {
	global $wp_post_types;
	if ( isset( $wp_post_types['sg_eventos'] ) ) {
		$wp_post_types['sg_eventos']->has_archive = true;
	}
}
add_action( 'init', 'sg_setup_sg_eventos_archive', 20 );

/**
 * Ajustar query para posts sg_eventos
 */
function sg_pre_get_posts_sg_eventos( $query ) {
	if ( ! is_admin() && $query->is_main_query() ) {
		if ( $query->is_post_type_archive( 'sg_eventos' ) ) {
			$query->set( 'post_type', 'sg_eventos' );
			$query->set( 'post_status', 'publish' );
			$query->set( 'posts_per_page', 20 );
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'meta_key', '_sg_evento_data_inicio' );
			$query->set( 'order', 'ASC' );
			$query->set( 'meta_type', 'DATE' );
		}
	}
}
add_action( 'pre_get_posts', 'sg_pre_get_posts_sg_eventos' );

/**
 * Configurar archive para eventos ETN
 */
function sg_setup_etn_archive() {
	if ( post_type_exists( 'etn' ) ) {
		global $wp_post_types;
		if ( isset( $wp_post_types['etn'] ) ) {
			$wp_post_types['etn']->has_archive = true;
			$wp_post_types['etn']->rewrite = array(
				'slug'       => 'eventos',
				'with_front' => false,
			);
		}
	}
}
add_action( 'init', 'sg_setup_etn_archive', 20 );

/**
 * Ajustar query para posts ETN
 */
function sg_pre_get_posts_etn( $query ) {
	if ( ! is_admin() && $query->is_main_query() ) {
		// Se acessando via ?p= e é um post ETN
		if ( isset( $_GET['p'] ) ) {
			$post_id = intval( $_GET['p'] );
			if ( $post_id ) {
				$detected_type = get_post_type( $post_id );
				if ( $detected_type === 'etn' ) {
					$query->set( 'post_type', 'etn' );
					$query->set( 'p', $post_id );
					$query->is_singular = true;
					$query->is_single = true;
					$query->is_page = false;
					$query->is_404 = false;
				}
			}
		}
		
		// Se é archive de ETN, ajustar query
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
		
		if ( $query->is_post_type_archive( 'etn' ) || 
			 ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'etn' ) ||
			 ( strpos( $request_uri, '/eventos' ) !== false && ! is_single() && ! is_admin() ) ) {
			$query->set( 'post_type', 'etn' );
			$query->set( 'post_status', 'publish' );
			$query->set( 'posts_per_page', 20 );
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'meta_key', 'etn_start_date' );
			$query->set( 'order', 'ASC' );
			$query->set( 'meta_type', 'DATE' );
		}
	}
}
add_action( 'pre_get_posts', 'sg_pre_get_posts_etn' );

/**
 * Incluir eventos na busca do WordPress
 */
function sg_include_events_in_search( $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
		// Obter post types de eventos disponíveis
		$event_post_types = array( 'post', 'page' ); // Incluir posts e páginas padrão
		
		// Verificar quais tipos de eventos existem
		if ( post_type_exists( 'sg_eventos' ) ) {
			$event_post_types[] = 'sg_eventos';
		}
		if ( post_type_exists( 'etn' ) ) {
			$event_post_types[] = 'etn';
		}
		if ( post_type_exists( 'tribe_events' ) ) {
			$event_post_types[] = 'tribe_events';
		}
		
		// Se nenhum post type de evento está registrado, verificar no banco
		if ( ! in_array( 'sg_eventos', $event_post_types ) || ! in_array( 'etn', $event_post_types ) || ! in_array( 'tribe_events', $event_post_types ) ) {
			global $wpdb;
			$post_types_to_check = array( 'sg_eventos', 'etn', 'tribe_events' );
			foreach ( $post_types_to_check as $pt ) {
				if ( ! in_array( $pt, $event_post_types ) ) {
					$count = $wpdb->get_var( $wpdb->prepare(
						"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish'",
						$pt
					) );
					if ( $count > 0 ) {
						$event_post_types[] = $pt;
					}
				}
			}
		}
		
		$query->set( 'post_type', $event_post_types );
	}
}
add_action( 'pre_get_posts', 'sg_include_events_in_search' );

/**
 * Buscar produtos relacionados para a busca
 */
function sg_get_related_products_for_search( $search_query ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return array();
	}
	
	// Limpar query de busca
	$search_terms = sanitize_text_field( $search_query );
	if ( empty( $search_terms ) ) {
		return array();
	}
	
	// Buscar produtos que contenham os termos de busca
	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 4, // Limitar a 4 produtos
		's'              => $search_terms,
		'orderby'        => 'relevance',
		'order'          => 'DESC',
	);
	
	$products_query = new WP_Query( $args );
	$product_ids = array();
	
	if ( $products_query->have_posts() ) {
		while ( $products_query->have_posts() ) {
			$products_query->the_post();
			$product_ids[] = get_the_ID();
		}
		wp_reset_postdata();
	}
	
	// Se não encontrou produtos relacionados, buscar produtos em destaque
	if ( empty( $product_ids ) ) {
		$featured_args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => 4,
			'tax_query'      => array(
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'featured',
				),
			),
		);
		
		$featured_query = new WP_Query( $featured_args );
		if ( $featured_query->have_posts() ) {
			while ( $featured_query->have_posts() ) {
				$featured_query->the_post();
				$product_ids[] = get_the_ID();
			}
			wp_reset_postdata();
		}
	}
	
	return $product_ids;
}

/**
 * Obter permalink confiável para eventos (funciona mesmo com post types não registrados)
 */
function sg_get_event_permalink( $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post || $post->post_status !== 'publish' ) {
		return '';
	}
	
	$post_type = $post->post_type;
	
	// Tentar usar get_permalink primeiro
	$permalink = get_permalink( $post_id );
	
	// Se get_permalink retornou válido e não é false, usar
	if ( ! empty( $permalink ) && $permalink !== false && filter_var( $permalink, FILTER_VALIDATE_URL ) ) {
		// Verificar se a URL não contém apenas ?p=ID sem outros parâmetros úteis
		// Se o post type está registrado e tem rewrite, usar o permalink
		if ( post_type_exists( $post_type ) ) {
			return $permalink;
		}
		// Se não está registrado mas get_permalink retornou algo válido, usar
		if ( strpos( $permalink, '?p=' ) === false ) {
			return $permalink;
		}
	}
	
	// Se não funcionou ou é um post type não registrado, usar ?p=ID
	// Isso funciona porque temos hooks que garantem que o WordPress reconheça isso
	return home_url( '/?p=' . $post_id );
}

/**
 * Endpoint AJAX para busca em tempo real
 */
function sg_ajax_search_preview() {
	check_ajax_referer( 'sg_search_preview', 'nonce' );
	
	$search_term = isset( $_POST['s'] ) ? sanitize_text_field( $_POST['s'] ) : '';
	
	if ( empty( $search_term ) || strlen( $search_term ) < 2 ) {
		wp_send_json_error( array( 'message' => 'Digite pelo menos 2 caracteres' ) );
		return;
	}
	
	// Buscar eventos
	$event_post_types = array();
	global $wpdb;
	
	$post_types_to_check = array( 'sg_eventos', 'etn', 'tribe_events' );
	foreach ( $post_types_to_check as $pt ) {
		if ( post_type_exists( $pt ) ) {
			$event_post_types[] = $pt;
		} else {
			$count = $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish'",
				$pt
			) );
			if ( $count > 0 ) {
				$event_post_types[] = $pt;
			}
		}
	}
	
	$results = array();
	
	// Buscar eventos
	if ( ! empty( $event_post_types ) ) {
		$events_query = new WP_Query( array(
			'post_type'      => $event_post_types,
			'post_status'    => 'publish',
			'posts_per_page' => 5,
			's'              => $search_term,
			'orderby'        => 'relevance',
			'order'          => 'DESC',
		) );
		
		if ( $events_query->have_posts() ) {
			while ( $events_query->have_posts() ) {
				$events_query->the_post();
				$post_id = get_the_ID();
				$post_type = get_post_type();
				
				// Detectar meta keys
				if ( $post_type === 'sg_eventos' ) {
					$meta_key_start = '_sg_evento_data_inicio';
				} elseif ( $post_type === 'tribe_events' ) {
					$meta_key_start = '_EventStartDate';
				} else {
					$meta_key_start = 'etn_start_date';
				}
				
				$start_date = get_post_meta( $post_id, $meta_key_start, true );
				if ( $post_type === 'tribe_events' && ! empty( $start_date ) && strpos( $start_date, ' ' ) !== false ) {
					$start_date = date( 'd/m/Y', strtotime( $start_date ) );
				} elseif ( ! empty( $start_date ) && ! preg_match( '/^\d{2}\/\d{2}\/\d{4}$/', $start_date ) ) {
					$start_date = date_i18n( 'd/m/Y', strtotime( $start_date ) );
				}
				
				// Usar função auxiliar para obter permalink confiável
				$event_url = sg_get_event_permalink( $post_id );
				
				$results[] = array(
					'type'       => 'event',
					'id'         => $post_id,
					'title'      => get_the_title(),
					'url'        => $event_url,
					'excerpt'    => wp_trim_words( get_the_excerpt() ?: get_the_content(), 15 ),
					'date'       => $start_date ?: '',
					'thumbnail'  => get_the_post_thumbnail_url( $post_id, 'thumbnail' ),
				);
			}
			wp_reset_postdata();
		}
	}
	
	// Buscar produtos
	if ( class_exists( 'WooCommerce' ) ) {
		$products_query = new WP_Query( array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			's'              => $search_term,
			'orderby'        => 'relevance',
			'order'          => 'DESC',
		) );
		
		if ( $products_query->have_posts() ) {
			while ( $products_query->have_posts() ) {
				$products_query->the_post();
				$product = wc_get_product( get_the_ID() );
				
				$results[] = array(
					'type'       => 'product',
					'id'         => get_the_ID(),
					'title'      => get_the_title(),
					'url'        => get_permalink(),
					'price'      => $product->get_price_html(),
					'thumbnail'  => get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ),
				);
			}
			wp_reset_postdata();
		}
	}
	
	wp_send_json_success( array( 'results' => $results ) );
}
add_action( 'wp_ajax_sg_search_preview', 'sg_ajax_search_preview' );
add_action( 'wp_ajax_nopriv_sg_search_preview', 'sg_ajax_search_preview' );
function sg_flush_rewrite_rules() {
	if ( ! get_option( 'sg_flushed_rewrite_rules' ) ) {
		flush_rewrite_rules();
		update_option( 'sg_flushed_rewrite_rules', true );
	}
}
add_action( 'after_switch_theme', 'sg_flush_rewrite_rules' );

/**
 * Forçar flush de rewrite rules quando necessário (útil após mudanças de URL)
 * Adicionar ?sg_flush_rules=1 na URL do admin para forçar flush
 */
function sg_admin_flush_rewrite_rules() {
	if ( isset( $_GET['sg_flush_rules'] ) && current_user_can( 'manage_options' ) ) {
		flush_rewrite_rules( true );
		wp_redirect( admin_url( 'options-permalink.php?settings-updated=true' ) );
		exit;
	}
}
add_action( 'admin_init', 'sg_admin_flush_rewrite_rules' );

/**
 * Garantir que posts de eventos sejam carregados corretamente via ?p=ID ou ?post_type=slug
 */
function sg_force_load_event_posts( $query ) {
	// Apenas na query principal do frontend
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	
	// Verificar se está usando ?p=ID
	if ( isset( $_GET['p'] ) ) {
		$post_id = intval( $_GET['p'] );
		if ( $post_id ) {
			$post_type = get_post_type( $post_id );
			
			// Se for um tipo de evento, garantir que seja carregado
			if ( in_array( $post_type, array( 'etn', 'tribe_events', 'sg_eventos' ) ) ) {
				$post = get_post( $post_id );
				
				// Se o post existe e está publicado, forçar carregamento
				if ( $post && $post->post_status === 'publish' ) {
					$query->set( 'p', $post_id );
					$query->is_singular = true;
					$query->is_single = true;
					$query->is_page = false;
					$query->is_home = false;
					$query->is_archive = false;
					
					// Forçar o post type para que WordPress reconheça
					if ( ! post_type_exists( $post_type ) ) {
						// Temporariamente registrar o post type se necessário
						if ( ! isset( $GLOBALS['wp_post_types'][ $post_type ] ) ) {
							register_post_type( $post_type, array(
								'public' => true,
								'publicly_queryable' => true,
								'show_ui' => false,
								'show_in_menu' => false,
								'query_var' => true,
								'rewrite' => false,
								'capability_type' => 'post',
								'has_archive' => false,
								'hierarchical' => false,
								'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
							) );
						}
					}
					
					$query->set( 'post_type', $post_type );
				}
			}
		}
	}
	
	// Verificar se está usando ?post_type=slug (ex: ?tribe_events=slug)
	$event_post_types = array( 'etn', 'tribe_events', 'sg_eventos' );
	foreach ( $event_post_types as $pt ) {
		$slug = get_query_var( $pt );
		if ( empty( $slug ) && isset( $_GET[ $pt ] ) ) {
			$slug = sanitize_text_field( $_GET[ $pt ] );
		}
		
		if ( ! empty( $slug ) ) {
			// Buscar post pelo slug
			$post_obj = get_page_by_path( $slug, OBJECT, $pt );
			
			// Se não encontrou e o post type não está registrado, buscar diretamente no banco
			if ( ! $post_obj && ! post_type_exists( $pt ) ) {
				global $wpdb;
				$post_obj = $wpdb->get_row( $wpdb->prepare(
					"SELECT * FROM {$wpdb->posts} 
					WHERE post_name = %s 
					AND post_type = %s 
					AND post_status = 'publish' 
					LIMIT 1",
					$slug,
					$pt
				), OBJECT );
				
				if ( $post_obj ) {
					$post_obj = get_post( $post_obj->ID );
				}
			}
			
			if ( $post_obj && $post_obj->post_status === 'publish' ) {
				$query->set( 'p', $post_obj->ID );
				$query->set( 'post_type', $pt );
				$query->is_singular = true;
				$query->is_single = true;
				$query->is_page = false;
				$query->is_home = false;
				$query->is_archive = false;
				$query->is_front_page = false;
				
				// Forçar o post type para que WordPress reconheça
				if ( ! post_type_exists( $pt ) ) {
					if ( ! isset( $GLOBALS['wp_post_types'][ $pt ] ) ) {
						register_post_type( $pt, array(
							'public' => true,
							'publicly_queryable' => true,
							'show_ui' => false,
							'show_in_menu' => false,
							'query_var' => true,
							'rewrite' => false,
							'capability_type' => 'post',
							'has_archive' => false,
							'hierarchical' => false,
							'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
						) );
					}
				}
				
				return; // Importante: retornar aqui para não continuar processando
			}
		}
	}
}
add_action( 'pre_get_posts', 'sg_force_load_event_posts', 1 );

/**
 * Evitar 404 para eventos acessados via ?p=ID ou ?post_type=slug
 */
function sg_prevent_404_for_events() {
	if ( is_admin() ) {
		return;
	}
	
	global $wp_query;
	
	// Verificar se está usando ?p=ID
	if ( isset( $_GET['p'] ) ) {
		$post_id = intval( $_GET['p'] );
		if ( $post_id ) {
			$post_type = get_post_type( $post_id );
			
			if ( in_array( $post_type, array( 'etn', 'tribe_events', 'sg_eventos' ) ) ) {
				$post = get_post( $post_id );
				
				if ( $post && $post->post_status === 'publish' ) {
					// Forçar o WordPress a reconhecer como singular
					$wp_query->is_404 = false;
					$wp_query->is_singular = true;
					$wp_query->is_single = true;
					$wp_query->is_home = false;
					$wp_query->is_archive = false;
					
					// Garantir que o post está carregado
					if ( ! $wp_query->post || $wp_query->post->ID !== $post_id ) {
						$wp_query->post = $post;
						$wp_query->posts = array( $post );
						$wp_query->post_count = 1;
						$wp_query->found_posts = 1;
					}
					
					// Limpar qualquer flag de erro
					status_header( 200 );
					return;
				}
			}
		}
	}
	
	// Verificar se está usando ?post_type=slug (ex: ?tribe_events=slug)
	$event_post_types = array( 'etn', 'tribe_events', 'sg_eventos' );
	foreach ( $event_post_types as $pt ) {
		$slug = get_query_var( $pt );
		if ( ! empty( $slug ) ) {
			$post_obj = get_page_by_path( $slug, OBJECT, $pt );
			if ( $post_obj && $post_obj->post_status === 'publish' ) {
				$wp_query->is_404 = false;
				$wp_query->is_singular = true;
				$wp_query->is_single = true;
				$wp_query->is_home = false;
				$wp_query->is_archive = false;
				
				if ( ! $wp_query->post || $wp_query->post->ID !== $post_obj->ID ) {
					$wp_query->post = $post_obj;
					$wp_query->posts = array( $post_obj );
					$wp_query->post_count = 1;
					$wp_query->found_posts = 1;
				}
				
				status_header( 200 );
				return;
			}
		}
	}
}
add_action( 'template_redirect', 'sg_prevent_404_for_events', 1 );

/**
 * Forçar uso de templates single-etn.php e archive-etn.php
 * PRIORIDADE ALTA para garantir que seja executado antes de outros filtros
 */
function sg_template_include_etn( $template ) {
	// Verificar via query vars ou post global
	global $wp_query, $post;
	
	// Garantir que $request_uri esteja sempre definido para verificações posteriores
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
	
	// PRIORIDADE 1: Verificar via query vars de post types (ex: ?tribe_events=slug)
	$event_post_types = array( 'etn', 'tribe_events', 'sg_eventos' );
	foreach ( $event_post_types as $pt ) {
		// Verificar tanto via get_query_var quanto diretamente via $_GET
		$slug = get_query_var( $pt );
		if ( empty( $slug ) && isset( $_GET[ $pt ] ) ) {
			$slug = sanitize_text_field( $_GET[ $pt ] );
		}
		
		if ( ! empty( $slug ) ) {
			// Buscar post pelo slug - tentar múltiplas formas
			$post_obj = get_page_by_path( $slug, OBJECT, $pt );
			
			// Se não encontrou e o post type não está registrado, buscar diretamente no banco
			if ( ! $post_obj && ! post_type_exists( $pt ) ) {
				global $wpdb;
				$post_obj = $wpdb->get_row( $wpdb->prepare(
					"SELECT * FROM {$wpdb->posts} 
					WHERE post_name = %s 
					AND post_type = %s 
					AND post_status = 'publish' 
					LIMIT 1",
					$slug,
					$pt
				), OBJECT );
				
				if ( $post_obj ) {
					$post_obj = get_post( $post_obj->ID );
				}
			}
			
			if ( $post_obj && $post_obj->post_status === 'publish' ) {
				$post = $post_obj;
				
				// CRÍTICO: Forçar o WordPress a reconhecer como singular ANTES de escolher template
				$wp_query->post = $post;
				$wp_query->posts = array( $post );
				$wp_query->post_count = 1;
				$wp_query->found_posts = 1;
				$wp_query->is_singular = true;
				$wp_query->is_single = true;
				$wp_query->is_page = false;
				$wp_query->is_home = false;
				$wp_query->is_archive = false;
				$wp_query->is_404 = false;
				$wp_query->is_search = false;
				
				// Forçar query vars
				$wp_query->set( 'p', $post->ID );
				$wp_query->set( 'post_type', $pt );
				$wp_query->set( 'name', $post->post_name );
				
				// Garantir que o post global está correto
				setup_postdata( $post );
				
				// Usar o template imediatamente
				$single_etn = get_template_directory() . '/single-etn.php';
				if ( file_exists( $single_etn ) ) {
					return $single_etn;
				}
			}
		}
	}
	
	// Verificar se é um post do tipo etn
	$post_type = get_query_var( 'post_type' );
	$is_etn_singular = false;
	
	// Verificar via URL - se contém /eventos
	if ( empty( $post_type ) ) {
		if ( strpos( $request_uri, '/eventos' ) !== false && ! is_single() ) {
			$post_type = 'etn';
		}
	}
	
	// Verificar via ID na URL
	if ( empty( $post_type ) && isset( $_GET['p'] ) ) {
		$post_id = intval( $_GET['p'] );
		if ( $post_id ) {
			$detected_type = get_post_type( $post_id );
			if ( in_array( $detected_type, array( 'etn', 'tribe_events', 'sg_eventos' ) ) ) {
				$post_type = $detected_type;
				$is_etn_singular = true;
				
				// Garantir que o post global está correto
				if ( ! $post || $post->ID !== $post_id ) {
					$post = get_post( $post_id );
					$wp_query->post = $post;
					$wp_query->posts = array( $post );
					$wp_query->post_count = 1;
					$wp_query->is_singular = true;
					$wp_query->is_single = true;
					$wp_query->is_404 = false;
				}
			}
		}
	}
	
	// Verificar no post global após query
	if ( empty( $post_type ) && $post && isset( $post->post_type ) ) {
		$post_type = $post->post_type;
		if ( in_array( $post_type, array( 'etn', 'tribe_events', 'sg_eventos' ) ) ) {
			$is_etn_singular = true;
		}
	}
	
	// Verificar se é singular de qualquer tipo de evento
	if ( is_singular( array( 'etn', 'tribe_events', 'sg_eventos' ) ) || $is_etn_singular ) {
		// Tentar usar single-etn.php para todos os tipos de eventos
		$single_etn = get_template_directory() . '/single-etn.php';
		if ( file_exists( $single_etn ) ) {
			return $single_etn;
		}
	}
	
	// Verificar se há um post carregado que é um evento, mesmo que WordPress não reconheça como singular
	if ( $post && isset( $post->post_type ) && in_array( $post->post_type, array( 'etn', 'tribe_events', 'sg_eventos' ) ) ) {
		// Garantir que não é 404
		if ( ! $wp_query->is_404 ) {
			$single_etn = get_template_directory() . '/single-etn.php';
			if ( file_exists( $single_etn ) ) {
				return $single_etn;
			}
		}
	}
	
	// Última tentativa: verificar se estamos em uma URL que deveria ser um evento
	if ( isset( $_GET['p'] ) ) {
		$post_id = intval( $_GET['p'] );
		if ( $post_id ) {
			$detected_type = get_post_type( $post_id );
			if ( in_array( $detected_type, array( 'etn', 'tribe_events', 'sg_eventos' ) ) ) {
				$single_etn = get_template_directory() . '/single-etn.php';
				if ( file_exists( $single_etn ) ) {
					return $single_etn;
				}
			}
		}
	}
	
	// Verificar query vars de post types
	$event_post_types = array( 'etn', 'tribe_events', 'sg_eventos' );
	foreach ( $event_post_types as $pt ) {
		$slug = get_query_var( $pt );
		if ( ! empty( $slug ) ) {
			$single_etn = get_template_directory() . '/single-etn.php';
			if ( file_exists( $single_etn ) ) {
				return $single_etn;
			}
		}
	}
	
	// Verificar se é archive ETN - mais agressivo
	if ( is_post_type_archive( 'etn' ) || 
		 ( is_archive() && $post_type === 'etn' ) ||
		 ( strpos( $request_uri, '/eventos' ) !== false && ! is_single() ) ||
		 ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'etn' ) ) {
		
		$archive_etn = get_template_directory() . '/archive-etn.php';
		if ( file_exists( $archive_etn ) ) {
			return $archive_etn;
		}
	}
	
	return $template;
}
add_filter( 'template_include', 'sg_template_include_etn', 1 );

/**
 * Set content width
 */
function sg_content_width() {
	$GLOBALS['content_width'] = 1200;
}
add_action( 'after_setup_theme', 'sg_content_width', 0 );

/**
 * Enqueue scripts and styles
 */
function sg_scripts() {
	// Enqueue styles
	wp_enqueue_style( 'sg-style', get_stylesheet_uri(), array(), SG_VERSION );
	wp_enqueue_style( 'sg-palette', get_template_directory_uri() . '/css/palette.css', array(), SG_VERSION );

	// Carregar estilização específica da página Minha Conta com versionamento anti-cache
	$sg_is_my_account = (
		function_exists( 'is_account_page' ) && is_account_page()
	) || (
		function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url()
	) || (
		is_page( get_option( 'woocommerce_myaccount_page_id' ) )
	) || is_page( 'minha-conta' ) || is_page( 'my-account' );

	if ( $sg_is_my_account ) {
		$my_account_css = get_template_directory() . '/css/my-account.css';
		$version = file_exists( $my_account_css ) ? filemtime( $my_account_css ) : ( SG_VERSION . '-' . time() );
		wp_enqueue_style( 'sg-my-account', get_template_directory_uri() . '/css/my-account.css', array( 'sg-style', 'sg-palette' ), $version );

		// Forçar estrutura de duas colunas via JS quando necessário
		$sg_my_account_inline_js = "(function(){\n  function init(){\n    var container = document.querySelector('.woocommerce');\n    var nav = document.querySelector('nav.woocommerce-MyAccount-navigation');\n    var content = document.querySelector('.woocommerce-MyAccount-content');\n    if(!container || !nav || !content){ return; }\n    if(!container.querySelector('.sg-my-account-layout')){\n      var wrapper = document.createElement('div');\n      wrapper.className = 'sg-my-account-layout';\n      container.insertBefore(wrapper, container.firstChild);\n      wrapper.appendChild(nav);\n      wrapper.appendChild(content);\n    }\n    container.style.display = 'block';\n  }\n  if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded', init);}else{init();}\n  setTimeout(init, 400);\n})();";
		wp_add_inline_script( 'sg-navigation', $sg_my_account_inline_js );

		// Force button text color via JS to defeat conflicting rules
		$sg_btn_fix_js = "(function(){\n  function apply(){\n    var selectors = [\n      '.woocommerce .woocommerce-info a.button',\n      '.woocommerce .woocommerce-message a.button',\n      '.woocommerce table.my_account_orders .button',\n      '.woocommerce a.button.wc-forward'\n    ];\n    selectors.forEach(function(sel){\n      document.querySelectorAll(sel).forEach(function(el){\n        try {\n          el.style.setProperty('color', '#111', 'important');\n          el.style.setProperty('-webkit-text-fill-color', '#111', 'important');\n          el.addEventListener('mouseenter', function(){\n            el.style.setProperty('color', '#111', 'important');\n            el.style.setProperty('-webkit-text-fill-color', '#111', 'important');\n          });\n          el.addEventListener('mouseleave', function(){\n            el.style.setProperty('color', '#111', 'important');\n            el.style.setProperty('-webkit-text-fill-color', '#111', 'important');\n          });\n        } catch(e){}\n      });\n    });\n  }\n  if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded', apply);}else{apply();}\n  setTimeout(apply, 400);\n})();";
		wp_add_inline_script( 'sg-navigation', $sg_btn_fix_js );
	}

	// Enqueue scripts
	wp_enqueue_script( 'sg-navigation', get_template_directory_uri() . '/js/navigation.js', array(), SG_VERSION, true );
	wp_enqueue_script( 'sg-calendario', get_template_directory_uri() . '/js/calendario.js', array(), SG_VERSION, true );
	
	// Script de busca em tempo real
	wp_enqueue_script( 'sg-search-preview', get_template_directory_uri() . '/js/search-preview.js', array(), SG_VERSION, true );
	wp_localize_script( 'sg-search-preview', 'sgSearchPreview', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'sg_search_preview' ),
	) );
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
	if ( is_post_type_archive( 'etn' ) || 
		 ( is_archive() && get_query_var( 'post_type' ) === 'etn' ) ||
		 ( strpos( $request_uri, '/eventos' ) !== false && ! is_single() ) ||
		 ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'etn' ) ) {
		wp_enqueue_script( 'sg-eventos-expand', get_template_directory_uri() . '/js/eventos-expand.js', array(), SG_VERSION, true );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Estilos do layout da loja e filtros (WooCommerce) - carregar DEPOIS do WooCommerce
	// Incluir TODOS os casos possíveis de páginas de produtos
	if ( class_exists( 'WooCommerce' ) ) {
		$is_shop_page = (
			is_shop() || 
			is_product_taxonomy() || 
			is_product_category() || 
			is_product_tag() || 
			is_post_type_archive( 'product' ) ||
			is_page( 'materiais' ) ||
			( is_page() && strpos( strtolower( get_the_title() ), 'materiais' ) !== false )
		);
		
		if ( $is_shop_page ) {
			// Usar timestamp para forçar reload e evitar cache
			$shop_css_file = get_template_directory() . '/css/shop-filters.css';
			$shop_css_version = file_exists( $shop_css_file ) ? filemtime( $shop_css_file ) : time();
			wp_enqueue_style( 'sg-shop-filters', get_template_directory_uri() . '/css/shop-filters.css', array( 'woocommerce-general', 'woocommerce-layout' ), $shop_css_version, 'all' );
		}
	}

	// Carregar estilização específica da página Contato
	$sg_is_contact = (
		is_page_template( 'page-contato.php' ) || 
		is_page( 'contato' ) || 
		( is_page() && strpos( strtolower( get_the_title() ), 'contato' ) !== false )
	);
	if ( $sg_is_contact ) {
		$contact_css = get_template_directory() . '/css/contact.css';
		$version = file_exists( $contact_css ) ? filemtime( $contact_css ) : ( SG_VERSION . '-' . time() );
		wp_enqueue_style( 'sg-contact', get_template_directory_uri() . '/css/contact.css', array( 'sg-style', 'sg-palette' ), $version );
		
		// Enqueue script do formulário de contato
		$contact_js = get_template_directory() . '/js/contact-form.js';
		$js_version = file_exists( $contact_js ) ? filemtime( $contact_js ) : SG_VERSION;
		wp_enqueue_script( 'sg-contact-form', get_template_directory_uri() . '/js/contact-form.js', array(), $js_version, true );
		wp_localize_script( 'sg-contact-form', 'sgContactAjax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'sg_contact_form' ),
		) );
	}
}
add_action( 'wp_enqueue_scripts', 'sg_scripts', 20 );

/**
 * Usar sidebar personalizada do WooCommerce nas páginas de loja
 */
function sg_override_woocommerce_sidebar() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	// Incluir TODOS os casos possíveis de páginas de produtos
	$is_shop_page = (
		is_shop() || 
		is_product_taxonomy() || 
		is_product_category() || 
		is_product_tag() || 
		is_post_type_archive( 'product' ) ||
		is_page( 'materiais' ) ||
		( is_page() && strpos( strtolower( get_the_title() ), 'materiais' ) !== false )
	);
	
	if ( $is_shop_page ) {
		// Remover sidebar padrão
		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

		// Abrir layout + renderizar filtros antes de todo conteúdo da loja
		add_action( 'woocommerce_before_main_content', function() {
			echo '<div class="sg-shop-layout">';
			wc_get_template( 'global/sidebar.php' );
			echo '<div class="sg-shop-content">';
		}, 5 );

		// Fechar wrappers após o conteúdo
		add_action( 'woocommerce_after_main_content', function() {
			echo '</div></div>';
		}, 50 );

		// Barra sticky com título + contagem + ordenação
		add_action( 'woocommerce_before_shop_loop', function() {
			echo '<div class="sg-shop-toolbar">';
			// Título da página/termo
			if ( function_exists( 'woocommerce_page_title' ) ) {
				$title = woocommerce_page_title( false );
				if ( $title ) {
					echo '<h1 class="shop-title">' . esc_html( $title ) . '</h1>';
				}
			}
		}, 1 );

		add_action( 'woocommerce_before_shop_loop', function() {
			echo '</div>';
		}, 99 );
	}
}
add_action( 'wp', 'sg_override_woocommerce_sidebar' );

/**
 * Script para garantir que o grid seja aplicado e remover larguras fixas
 */
function sg_force_shop_grid() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	
	// Incluir TODOS os casos possíveis de páginas de produtos
	$is_shop_page = (
		is_shop() || 
		is_product_taxonomy() || 
		is_product_category() || 
		is_product_tag() || 
		is_post_type_archive( 'product' ) ||
		is_page( 'materiais' ) ||
		( is_page() && strpos( strtolower( get_the_title() ), 'materiais' ) !== false )
	);
	
	if ( $is_shop_page ) {
		?>
		<script>
		(function() {
			var isApplying = false;
			
			function forceShopGrid() {
				if (isApplying) return;
				isApplying = true;
				
				// 1. REMOVER LISTAS VAZIAS DO DOM
				var allLists = document.querySelectorAll('ul.products');
				var productsList = null;
				
				allLists.forEach(function(list) {
					var items = list.querySelectorAll('li.product');
					if (items.length === 0) {
						// Remover lista vazia completamente
						try {
							list.remove();
						} catch(e) {
							list.style.display = 'none';
						}
					} else if (!productsList) {
						productsList = list; // Primeira lista com produtos
					}
				});
				
				if (!productsList) {
					isApplying = false;
					return;
				}
				
				// 2. LIBERAR TODOS OS CONTAINERS PAIS
				var containers = [
					'.site-main-wrapper',
					'.site-main .container', 
					'.posts-container',
					'.sg-shop-content',
					'.sg-shop-layout',
					'.woocommerce'
				];
				
				containers.forEach(function(selector) {
					var el = document.querySelector(selector);
					if (el && !el.classList.contains('shop-sidebar')) {
						el.style.setProperty('max-width', selector === '.sg-shop-layout' ? '1200px' : '100%', 'important');
						el.style.setProperty('width', '100%', 'important');
						if (selector === '.site-main-wrapper') {
							el.style.setProperty('display', 'block', 'important');
						}
					}
				});
				
				// 3. APLICAR GRID NA LISTA REAL
				var windowWidth = window.innerWidth;
				productsList.style.setProperty('display', 'grid', 'important');
				
				if (windowWidth >= 992) {
					productsList.style.setProperty('grid-template-columns', 'repeat(3, minmax(0, 1fr))', 'important');
					productsList.style.setProperty('gap', '24px', 'important');
				} else if (windowWidth >= 768) {
					productsList.style.setProperty('grid-template-columns', 'repeat(2, minmax(0, 1fr))', 'important');
					productsList.style.setProperty('gap', '20px', 'important');
				} else {
					productsList.style.setProperty('grid-template-columns', '1fr', 'important');
					productsList.style.setProperty('gap', '16px', 'important');
				}
				
				productsList.style.setProperty('width', '100%', 'important');
				productsList.style.setProperty('justify-content', 'space-between', 'important');
				
				// 4. REMOVER LARGURAS FIXAS DOS CARDS
				var products = productsList.querySelectorAll('li.product');
				products.forEach(function(product) {
					// Limpar style inline completamente
					product.removeAttribute('style');
					
					// Forçar valores corretos
					product.style.setProperty('width', 'auto', 'important');
					product.style.setProperty('max-width', '300px', 'important');
					product.style.setProperty('min-width', '0', 'important');
					product.style.setProperty('flex', '1 1 auto', 'important');
					product.style.setProperty('flex-basis', 'auto', 'important');
					product.style.setProperty('float', 'none', 'important');
					product.style.setProperty('box-sizing', 'border-box', 'important');
					product.style.setProperty('margin', '0', 'important');
					product.style.setProperty('padding', '0', 'important');
				});
				
				isApplying = false;
			}
			
			// 5. PROTEGER CONTRA SCRIPTS QUE REAPLIQUEM LARGURAS
			function setupProtection() {
				var allLists = document.querySelectorAll('ul.products');
				var productsList = null;
				
				for (var i = 0; i < allLists.length; i++) {
					var items = allLists[i].querySelectorAll('li.product');
					if (items.length > 0) {
						productsList = allLists[i];
						break;
					}
				}
				
				if (!productsList) return;
				
				// MutationObserver para interceptar mudanças inline
				var products = productsList.querySelectorAll('li.product');
				products.forEach(function(product) {
					var observer = new MutationObserver(function(mutations) {
						mutations.forEach(function(mutation) {
							if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
								var style = product.getAttribute('style') || '';
								// Detectar largura fixa pequena (ex: 171px)
								if (/width\s*:\s*[12]\d{2}px/i.test(style) || 
								    /flex-basis\s*:\s*[12]\d{2}px/i.test(style)) {
									setTimeout(function() {
										product.style.setProperty('width', 'auto', 'important');
										product.style.setProperty('max-width', '300px', 'important');
										product.style.setProperty('flex-basis', 'auto', 'important');
									}, 0);
								}
							}
						});
					});
					
					observer.observe(product, {
						attributes: true,
						attributeFilter: ['style']
					});
				});
			}
			
			// EXECUTAR
			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', function() {
					forceShopGrid();
					setTimeout(setupProtection, 300);
				});
			} else {
				forceShopGrid();
				setTimeout(setupProtection, 300);
			}
			
			// Executar múltiplas vezes para garantir aplicação
			setTimeout(forceShopGrid, 100);
			setTimeout(forceShopGrid, 500);
			setTimeout(forceShopGrid, 1000);
			setTimeout(setupProtection, 1500);
			
			// Reaplicar em resize
			var resizeTimeout;
			window.addEventListener('resize', function() {
				clearTimeout(resizeTimeout);
				resizeTimeout = setTimeout(forceShopGrid, 250);
			});
			
			// Reaplicar em mudanças do DOM (AJAX)
			var domObserver = new MutationObserver(function() {
				setTimeout(forceShopGrid, 100);
			});
			
			domObserver.observe(document.body, {
				childList: true,
				subtree: true
			});
			
			console.log('✓ Shop Grid Fix carregado');
		})();
		</script>
		<?php
	}
}
add_action( 'wp_footer', 'sg_force_shop_grid', 999 );

/**
 * Forçar uso do template "Minha Conta (Sidebar)" quando estiver na página Minha Conta
 */
function sg_force_my_account_template( $template ) {
    if ( function_exists( 'is_account_page' ) && is_account_page() ) {
        $custom_template = get_template_directory() . '/page-templates/account-sidebar.php';
        if ( file_exists( $custom_template ) ) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter( 'template_include', 'sg_force_my_account_template', 98 );

/**
 * Register widget areas
 */
function sg_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'sg-juridico' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'sg-juridico' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer 1', 'sg-juridico' ),
		'id'            => 'footer-1',
		'description'   => esc_html__( 'Coluna 1 do Footer - Sobre a empresa', 'sg-juridico' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer 2', 'sg-juridico' ),
		'id'            => 'footer-2',
		'description'   => esc_html__( 'Coluna 2 do Footer - Links rápidos', 'sg-juridico' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer 3', 'sg-juridico' ),
		'id'            => 'footer-3',
		'description'   => esc_html__( 'Coluna 3 do Footer - Informações e contato', 'sg-juridico' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer 4', 'sg-juridico' ),
		'id'            => 'footer-4',
		'description'   => esc_html__( 'Coluna 4 do Footer - Redes sociais e newsletter', 'sg-juridico' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	// Filtros da Loja (WooCommerce)
	if ( class_exists( 'WooCommerce' ) ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Filtros da Loja (WooCommerce)', 'sg-juridico' ),
			'id'            => 'shop-filters',
			'description'   => esc_html__( 'Widgets de filtros (preço, categorias, atributos).', 'sg-juridico' ),
			'before_widget' => '<section id="%1$s" class="widget shop-filter %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}
}
add_action( 'widgets_init', 'sg_widgets_init' );

/**
 * Customizer additions
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Template Functions
 */
function sg_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() )
	);

	printf( '<span class="posted-on">%s</span>', $time_string );
}

function sg_posted_by() {
	printf(
		'<span class="byline"> %s <span class="author vcard"><a class="url fn n" href="%s">%s</a></span></span>',
		_x( 'by', 'post author', 'sg-juridico' ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_html( get_the_author() )
	);
}

function sg_post_thumbnail() {
	if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
		return;
	}

	if ( is_singular() ) :
		?>
		<div class="post-thumbnail">
			<?php the_post_thumbnail(); ?>
		</div>
		<?php
	else :
		?>
		<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php
			the_post_thumbnail( 'post-thumbnail', array(
				'alt' => the_title_attribute( array(
					'echo' => false,
				) ),
			) );
			?>
		</a>
		<?php
	endif;
}

function sg_entry_footer() {
	if ( 'post' === get_post_type() ) {
		$categories_list = get_the_category_list( esc_html__( ', ', 'sg-juridico' ) );
		if ( $categories_list ) {
			printf( '<span class="cat-links">%s %s</span>', esc_html__( 'Posted in', 'sg-juridico' ), $categories_list );
		}

		$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'sg-juridico' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">%s %s</span>', esc_html__( 'Tagged', 'sg-juridico' ), $tags_list );
		}
	}
}

/**
 * Add cart icon with item count to header
 */
function sg_cart_fragments_count( $fragments ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return $fragments;
	}

	ob_start();
	$cart_count = WC()->cart->get_cart_contents_count();
	$cart_url = wc_get_cart_url();
	?>
	<a href="<?php echo esc_url( $cart_url ); ?>" class="cart-icon" aria-label="<?php esc_attr_e( 'Carrinho de compras', 'sg-juridico' ); ?>">
		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M9 22C9.55228 22 10 21.5523 10 21C10 20.4477 9.55228 20 9 20C8.44772 20 8 20.4477 8 21C8 21.5523 8.44772 22 9 22Z" fill="currentColor"/>
			<path d="M20 22C20.5523 22 21 21.5523 21 21C21 20.4477 20.5523 20 20 20C19.4477 20 19 20.4477 19 21C19 21.5523 19.4477 22 20 22Z" fill="currentColor"/>
			<path d="M1 1H5L7.68 14.39C7.77144 14.8504 8.02191 15.264 8.38755 15.5583C8.75318 15.8526 9.2107 16.009 9.68 16H19C19.5304 16 20.0391 15.7893 20.4142 15.4142C20.7893 15.0391 21 14.5304 21 14H9.9L9.36 11H19L22 4H6.28L5.28 2H1V1Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
		<?php if ( $cart_count > 0 ) : ?>
			<span class="cart-count"><?php echo esc_html( $cart_count ); ?></span>
		<?php endif; ?>
	</a>
	<?php
	$fragments['a.cart-icon'] = ob_get_clean();
	
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'sg_cart_fragments_count' );

/**
 * Redirect non-logged users to my-account page
 */
function sg_redirect_to_my_account() {
	if ( ! is_admin() && ! is_user_logged_in() && ! is_page( 'my-account' ) && ! is_admin() && ! wp_is_xml_request() ) {
		global $wp;
		$request = $wp->request;
		
		// Don't redirect for specific pages
		$excluded_pages = array( 'wp-login.php', 'wp-admin', 'wp-content', 'wp-includes', 'feed' );
		foreach ( $excluded_pages as $excluded ) {
			if ( false !== strpos( $request, $excluded ) ) {
				return;
			}
		}
	}
}
// Uncomment if you want to force login
// add_action( 'template_redirect', 'sg_redirect_to_my_account' );

/**
 * Add custom body classes for header styling
 */
function sg_header_body_classes( $classes ) {
	if ( is_user_logged_in() ) {
		$classes[] = 'user-logged-in';
	} else {
		$classes[] = 'user-not-logged-in';
	}
	
	if ( class_exists( 'WooCommerce' ) ) {
		$classes[] = 'woocommerce-active';
	}
	
	return $classes;
}
add_filter( 'body_class', 'sg_header_body_classes' );

/**
 * Get page by slug helper function
 */
function sg_get_page_url_by_slug( $slug, $default_url = '#' ) {
	$page = get_page_by_path( $slug );
	
	if ( $page && $page->post_status === 'publish' ) {
		return get_permalink( $page->ID );
	}
	
	return $default_url;
}

/**
 * Get company information
 * You can customize these values via theme options or directly here
 */
function sg_get_company_info( $info = '' ) {
	$company_info = array(
		'cnpj'      => '00.000.000/0001-00', // Configure seu CNPJ aqui
		'instagram' => 'https://instagram.com/sgjuridico', // Configure seu Instagram aqui
		'whatsapp'  => '5511999999999', // Configure seu WhatsApp aqui (formato: 5511999999999)
		'whatsapp_display' => '(11) 99999-9999', // Formato para exibição
	);

	if ( ! empty( $info ) && isset( $company_info[ $info ] ) ) {
		return $company_info[ $info ];
	}

	return $company_info;
}

/**
 * Format WhatsApp link
 */
function sg_get_whatsapp_link( $phone = null, $message = '' ) {
	if ( ! $phone ) {
		$phone = sg_get_company_info( 'whatsapp' );
	}
	
	// Remove caracteres não numéricos
	$phone = preg_replace( '/[^0-9]/', '', $phone );
	
	// Se não começar com 55, adiciona
	if ( substr( $phone, 0, 2 ) !== '55' ) {
		$phone = '55' . $phone;
	}
	
	$default_message = 'Olá! Gostaria de saber mais sobre os cursos do SG Jurídico.';
	$text = urlencode( ! empty( $message ) ? $message : $default_message );
	
	return "https://wa.me/{$phone}?text={$text}";
}

/**
 * Remove widget de pesquisa do sidebar (já existe no header)
 */
function sg_remove_search_widget_from_sidebar( $sidebars_widgets ) {
	if ( isset( $sidebars_widgets['sidebar-1'] ) && is_array( $sidebars_widgets['sidebar-1'] ) ) {
		foreach ( $sidebars_widgets['sidebar-1'] as $key => $widget ) {
			if ( strpos( $widget, 'search' ) !== false ) {
				unset( $sidebars_widgets['sidebar-1'][ $key ] );
			}
		}
		// Reindexar array
		$sidebars_widgets['sidebar-1'] = array_values( $sidebars_widgets['sidebar-1'] );
	}
	return $sidebars_widgets;
}
add_filter( 'sidebars_widgets', 'sg_remove_search_widget_from_sidebar' );

/**
 * Desregistrar widget de comentários recentes
 */
function sg_unregister_recent_comments_widget() {
	unregister_widget( 'WP_Widget_Recent_Comments' );
}
add_action( 'widgets_init', 'sg_unregister_recent_comments_widget', 11 );

/**
 * Desregistrar widget de arquivos
 */
function sg_unregister_archives_widget() {
	unregister_widget( 'WP_Widget_Archives' );
}
add_action( 'widgets_init', 'sg_unregister_archives_widget', 11 );

/**
 * Widget customizado de posts recentes com thumbnails
 */
class SG_Recent_Posts_With_Thumbnails extends WP_Widget_Recent_Posts {
	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Posts' );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number ) {
			$number = 5;
		}

		$r = new WP_Query(
			apply_filters(
				'widget_posts_args',
				array(
					'posts_per_page'      => $number,
					'no_found_rows'       => true,
					'post_status'         => 'publish',
					'ignore_sticky_posts' => true,
				),
				$instance
			)
		);

		if ( ! $r->have_posts() ) {
			return;
		}
		?>

		<?php echo $args['before_widget']; ?>

		<?php
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$format = current_theme_supports( 'html5', 'navigation-widgets' ) ? 'html5' : 'xhtml';

		if ( 'html5' === $format ) {
			echo '<nav role="navigation" aria-label="' . esc_attr( $title ) . '">';
		} else {
			echo '<div>';
		}
		?>
		<ul class="recent-posts-list">
		<?php
		foreach ( $r->posts as $recent_post ) :
			$post_title = get_the_title( $recent_post->ID );
			$title      = ( ! empty( $post_title ) ) ? $post_title : __( '(no title)' );
			$permalink  = get_permalink( $recent_post->ID );
			
			// Obter thumbnail - tentar múltiplas fontes
			$thumbnail_html = '';
			$has_thumbnail = false;
			
			// 1. Tentar pegar featured image (thumbnail padrão)
			if ( has_post_thumbnail( $recent_post->ID ) ) {
				$thumbnail_id = get_post_thumbnail_id( $recent_post->ID );
				$thumb_url = wp_get_attachment_image_src( $thumbnail_id, 'medium' );
				if ( $thumb_url && ! empty( $thumb_url[0] ) ) {
					$thumbnail_html = '<div class="recent-post-thumbnail"><a href="' . esc_url( $permalink ) . '"><img src="' . esc_url( $thumb_url[0] ) . '" alt="' . esc_attr( $title ) . '" width="' . esc_attr( $thumb_url[1] ) . '" height="' . esc_attr( $thumb_url[2] ) . '" loading="lazy" /></a></div>';
					$has_thumbnail = true;
				}
			}
			
			// 2. Se não tiver featured image, tentar pegar primeira imagem do conteúdo
			if ( ! $has_thumbnail ) {
				$content = get_post_field( 'post_content', $recent_post->ID );
				$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches );
				if ( ! empty( $matches[1][0] ) ) {
					$first_img = $matches[1][0];
					
					// Lista de imagens que sabemos que não existem (evitar 404)
					$problematic_images = array(
						'MPMA.png',
						'TJTO.png',
						'Delegado-da-Policia-Federal.png',
						'Magistratura-Federal-TRF.png',
						'TJCE.png',
						'TJDFT.png',
						'cropped-a4-212x300-Photoroom',
					);
					
					// Verificar se a URL contém alguma imagem problemática
					$is_problematic = false;
					foreach ( $problematic_images as $problematic ) {
						if ( strpos( $first_img, $problematic ) !== false ) {
							$is_problematic = true;
							break;
						}
					}
					
					// Só adicionar se não for problemática
					if ( ! $is_problematic ) {
						// Se a imagem começar com //, adicionar http:
						if ( substr( $first_img, 0, 2 ) === '//' ) {
							$first_img = 'http:' . $first_img;
						}
						$thumbnail_html = '<div class="recent-post-thumbnail"><a href="' . esc_url( $permalink ) . '"><img src="' . esc_url( $first_img ) . '" alt="' . esc_attr( $title ) . '" loading="lazy" /></a></div>';
						$has_thumbnail = true;
					}
				}
			}
			
			// 3. Se ainda não tiver imagem, usar placeholder
			if ( ! $has_thumbnail ) {
				$thumbnail_html = '<div class="recent-post-thumbnail recent-post-placeholder"><a href="' . esc_url( $permalink ) . '"><span class="placeholder-icon">📄</span></a></div>';
			}
			?>
			<li class="recent-post-with-thumbnail">
				<?php echo $thumbnail_html; ?>
				<div class="recent-post-content">
					<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
				</div>
			</li>
			<?php
		endforeach;
		?>
		</ul>
		<?php
		if ( 'html5' === $format ) {
			echo '</nav>';
		} else {
			echo '</div>';
		}

		echo $args['after_widget'];
	}
}

/**
 * Registrar widget customizado de posts recentes com thumbnails
 */
function sg_register_recent_posts_with_thumbnails() {
	unregister_widget( 'WP_Widget_Recent_Posts' );
	register_widget( 'SG_Recent_Posts_With_Thumbnails' );
}
add_action( 'widgets_init', 'sg_register_recent_posts_with_thumbnails', 11 );

/**
 * Adicionar thumbnails aos posts recentes usando JavaScript
 * Solução garantida que funciona mesmo com widget padrão
 */
function sg_add_thumbnails_via_javascript() {
	if ( is_admin() ) {
		return;
	}
	
	// Passar dados dos posts para JavaScript
	$recent_posts_data = array();
	
	// Obter posts recentes - abordagem mais direta
	// Primeiro, tentar obter dos widgets configurados
	if ( is_active_sidebar( 'sidebar-1' ) ) {
		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( ! empty( $sidebars_widgets['sidebar-1'] ) ) {
			foreach ( $sidebars_widgets['sidebar-1'] as $widget_id ) {
				if ( strpos( $widget_id, 'recent-posts' ) !== false || strpos( $widget_id, 'recent_posts' ) !== false ) {
					// Tentar extrair número do widget
					$widget_instances = get_option( 'widget_recent-posts' );
					$widget_number = intval( preg_replace( '/[^0-9]/', '', $widget_id ) );
					
					$number = 5; // padrão
					if ( ! empty( $widget_instances[ $widget_number ] ) && ! empty( $widget_instances[ $widget_number ]['number'] ) ) {
						$number = intval( $widget_instances[ $widget_number ]['number'] );
					}
					
					break;
				}
			}
		}
	}
	
	// Buscar os posts mais recentes
	$posts = get_posts( array(
		'posts_per_page' => ! empty( $number ) ? $number : 5,
		'post_status' => 'publish',
		'ignore_sticky_posts' => true,
		'orderby' => 'date',
		'order' => 'DESC',
	) );
	
	foreach ( $posts as $post ) {
		$post_url = get_permalink( $post->ID );
		$post_url_normalized = rtrim( $post_url, '/' );
		
		$post_data = array(
			'id' => $post->ID,
			'url' => $post_url,
			'thumbnail' => '',
		);
		
		// Obter thumbnail - tentar featured image primeiro
		if ( has_post_thumbnail( $post->ID ) ) {
			$thumb_id = get_post_thumbnail_id( $post->ID );
			$thumb_url = wp_get_attachment_image_src( $thumb_id, 'medium' );
			if ( $thumb_url && ! empty( $thumb_url[0] ) ) {
				// A URL já será corrigida automaticamente pelo filtro wp_get_attachment_image_src
				$post_data['thumbnail'] = $thumb_url[0];
			}
		}
		
		// Se não tiver thumbnail, tentar primeira imagem do conteúdo
		if ( empty( $post_data['thumbnail'] ) ) {
			$content = $post->post_content;
			preg_match( '/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $matches );
			if ( ! empty( $matches[1] ) ) {
				$first_img = $matches[1];
				
				// Lista de imagens que sabemos que não existem (evitar 404)
				$problematic_images = array(
					'MPMA.png',
					'TJTO.png',
					'Delegado-da-Policia-Federal.png',
					'Magistratura-Federal-TRF.png',
					'TJCE.png',
					'TJDFT.png',
					'cropped-a4-212x300-Photoroom',
				);
				
				// Verificar se a URL contém alguma imagem problemática
				$is_problematic = false;
				foreach ( $problematic_images as $problematic ) {
					if ( strpos( $first_img, $problematic ) !== false ) {
						$is_problematic = true;
						break;
					}
				}
				
				// Só adicionar se não for problemática
				if ( ! $is_problematic ) {
					// Converter URL relativa para absoluta
					if ( substr( $first_img, 0, 2 ) === '//' ) {
						$first_img = 'http:' . $first_img;
					} elseif ( substr( $first_img, 0, 1 ) === '/' && substr( $first_img, 0, 2 ) !== '//' ) {
						$first_img = home_url( $first_img );
					}
					// A URL será corrigida automaticamente pelo sistema híbrido de URLs
					$post_data['thumbnail'] = $first_img;
				}
			}
		}
		
		// Armazenar com múltiplas chaves para facilitar busca
		$recent_posts_data[ $post_url ] = $post_data;
		$recent_posts_data[ $post_url_normalized ] = $post_data;
		if ( $post_url !== $post_url_normalized ) {
			$recent_posts_data[ $post_url_normalized ] = $post_data;
		}
	}
	?>
	<script type="text/javascript">
	(function() {
		var postsData = <?php echo json_encode( $recent_posts_data ); ?>;
		
		function addThumbnails() {
			// Procurar na sidebar (#secondary)
			var sidebar = document.getElementById('secondary') || document.querySelector('.widget-area, aside#secondary');
			
			if (!sidebar) {
				console.log('Sidebar não encontrada');
				return;
			}
			
			// Procurar por seções que possam conter "Posts recentes"
			var widgets = sidebar.querySelectorAll('section.widget, .widget, [class*="widget"]');
			var targetWidget = null;
			
			// Procurar por widget que tenha título "Posts recentes" ou classe relacionada
			for (var w = 0; w < widgets.length; w++) {
				var widget = widgets[w];
				var title = widget.querySelector('.widget-title, h2, h3');
				
				if (title && (
					title.textContent.indexOf('Posts recentes') !== -1 ||
					title.textContent.indexOf('Recent Posts') !== -1 ||
					widget.classList.contains('widget_recent_entries') ||
					widget.id && widget.id.indexOf('recent') !== -1
				)) {
					targetWidget = widget;
					break;
				}
			}
			
			// Se não encontrou por título, procurar por classe
			if (!targetWidget) {
				targetWidget = sidebar.querySelector('.widget_recent_entries, [class*="recent"]');
			}
			
			if (!targetWidget) {
				console.log('Widget de posts recentes não encontrado');
				return;
			}
			
			// Procurar todos os <li> que contenham links dentro do widget
			var listItems = targetWidget.querySelectorAll('li');
			
			if (listItems.length === 0) {
				console.log('Nenhum item de lista encontrado');
				return;
			}
			
			console.log('Encontrados ' + listItems.length + ' posts para processar');
			console.log('Dados disponíveis:', Object.keys(postsData).length);
			
			// Processar cada item
			for (var i = 0; i < listItems.length; i++) {
				var li = listItems[i];
				
				// Pular se já foi processado
				if (li.classList.contains('recent-post-with-thumbnail') || li.querySelector('.recent-post-thumbnail')) {
					continue;
				}
				
				var link = li.querySelector('a');
				if (!link) {
					continue;
				}
				
				var url = link.getAttribute('href');
				if (!url) {
					continue;
				}
				
				// Normalizar URL (remover trailing slash, etc)
				var normalizedUrl = url.replace(/\/$/, '');
				
				// Tentar encontrar dados do post
				var postData = postsData[url] || postsData[normalizedUrl];
				
				// Se não encontrou, tentar buscar por qualquer URL que contenha parte do caminho
				if (!postData) {
					for (var dataUrl in postsData) {
						if (dataUrl.indexOf(url) !== -1 || url.indexOf(dataUrl) !== -1) {
							postData = postsData[dataUrl];
							break;
						}
					}
				}
				
				if (postData && postData.thumbnail) {
					// Adicionar thumbnail com imagem
					addThumbnail(li, url, postData.thumbnail);
				} else {
					// Adicionar placeholder (sempre adiciona para ter consistência visual)
					addPlaceholder(li, url);
				}
			}
		}
		
		function addThumbnail(li, url, thumbnailUrl) {
			// Validar URL antes de tentar carregar
			var problematicImages = [
				'MPMA.png',
				'TJTO.png',
				'Delegado-da-Policia-Federal.png',
				'Magistratura-Federal-TRF.png',
				'TJCE.png',
				'TJDFT.png',
				'cropped-a4-212x300-Photoroom',
				'/2025/05/'
			];
			
			var isProblematic = problematicImages.some(function(pattern) {
				return thumbnailUrl.indexOf(pattern) !== -1;
			});
			
			// Se for uma imagem problemática, usar placeholder direto
			if (isProblematic) {
				addPlaceholder(li, url);
				return;
			}
			
			li.classList.add('recent-post-with-thumbnail');
			
			var thumbnail = document.createElement('div');
			thumbnail.className = 'recent-post-thumbnail';
			
			var link = document.createElement('a');
			link.href = url;
			
			var img = document.createElement('img');
			img.src = thumbnailUrl;
			img.alt = '';
			img.loading = 'lazy';
			
			// Adicionar tratamento de erro para imagens que não carregam
			img.onerror = function(e) {
				// Prevenir propagação do erro para evitar 404 no console
				e.preventDefault();
				e.stopPropagation();
				// Se a imagem falhar ao carregar, usar placeholder
				this.style.display = 'none';
				var placeholder = document.createElement('span');
				placeholder.className = 'placeholder-icon';
				placeholder.textContent = '📄';
				link.innerHTML = '';
				link.appendChild(placeholder);
				return false;
			};
			
			link.appendChild(img);
			thumbnail.appendChild(link);
			
			var content = document.createElement('div');
			content.className = 'recent-post-content';
			content.innerHTML = li.innerHTML;
			
			li.innerHTML = '';
			li.appendChild(thumbnail);
			li.appendChild(content);
		}
		
		function addPlaceholder(li, url) {
			li.classList.add('recent-post-with-thumbnail');
			
			var thumbnail = document.createElement('div');
			thumbnail.className = 'recent-post-thumbnail recent-post-placeholder';
			
			var link = document.createElement('a');
			link.href = url || '#';
			
			var icon = document.createElement('span');
			icon.className = 'placeholder-icon';
			icon.textContent = '📄';
			
			link.appendChild(icon);
			thumbnail.appendChild(link);
			
			var content = document.createElement('div');
			content.className = 'recent-post-content';
			content.innerHTML = li.innerHTML;
			
			li.innerHTML = '';
			li.appendChild(thumbnail);
			li.appendChild(content);
		}
		
		// Iniciar quando DOM estiver pronto
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', addThumbnails);
		} else {
			addThumbnails();
		}
		
		// Também tentar depois de um pequeno delay para garantir
		setTimeout(addThumbnails, 500);
	})();
	
	// Script global para tratar erros de imagens 404 em toda a página
	(function() {
		'use strict';
		
		// Lista de imagens problemáticas
		var problematicImages = [
			'Magistratura-Federal-TRF.png',
			'TJDFT.png',
			'MPMA.png',
			'TJCE.png',
			'TJTO.png',
			'Delegado-da-Policia-Federal.png',
			'cropped-a4-212x300-Photoroom',
			'/2025/05/'
		];
		
		// Interceptar erros de carregamento de imagens ANTES de serem exibidos no console
		window.addEventListener('error', function(e) {
			if (e.target && e.target.tagName === 'IMG') {
				var img = e.target;
				
				// Pular imagens do banner - não ocultar ou tratar erros delas
				if (img.hasAttribute('data-banner-image')) {
					return;
				}
				
				var imgSrc = img.src || '';
				
				var isProblematic = problematicImages.some(function(pattern) {
					return imgSrc.indexOf(pattern) !== -1;
				});
				
				if (isProblematic) {
					// Ocultar imagem que falhou
					img.style.display = 'none';
					// Prevenir que o erro apareça no console
					e.preventDefault();
					e.stopPropagation();
					return false;
				}
			}
		}, true);
		
		// Adicionar onerror handler a todas as imagens da página
		function addErrorHandlers() {
			var images = document.querySelectorAll('img');
			images.forEach(function(img) {
				// Pular imagens do banner - não aplicar handlers de erro a elas
				if (img.hasAttribute('data-banner-image')) {
					return;
				}
				
				if (!img.hasAttribute('data-error-handled')) {
					img.setAttribute('data-error-handled', 'true');
					
					// Verificar se é uma imagem problemática ANTES de adicionar handler
					var imgSrc = img.src || img.getAttribute('src') || '';
					var isProblematic = problematicImages.some(function(pattern) {
						return imgSrc.indexOf(pattern) !== -1;
					});
					
					if (isProblematic) {
						// Ocultar imagem problemática imediatamente sem tentar carregar
						img.style.display = 'none';
						// Remover atributo src para evitar requisição
						if (img.hasAttribute('src')) {
							img.removeAttribute('src');
						}
						return;
					}
					
					img.addEventListener('error', function(e) {
						e.preventDefault();
						e.stopPropagation();
						var imgSrc = this.src || '';
						
						var isProblematic = problematicImages.some(function(pattern) {
							return imgSrc.indexOf(pattern) !== -1;
						});
						
						if (isProblematic) {
							this.style.display = 'none';
							return false;
						}
					}, true);
				}
			});
		}
		
		// Executar quando DOM estiver pronto
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', addErrorHandlers);
		} else {
			addErrorHandlers();
		}
		
		// Observar mudanças no DOM (para imagens carregadas dinamicamente)
		if (window.MutationObserver) {
			var observer = new MutationObserver(function(mutations) {
				addErrorHandlers();
			});
			
			observer.observe(document.body, {
				childList: true,
				subtree: true
			});
		}
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'sg_add_thumbnails_via_javascript' );

/**
 * AJAX handler para obter thumbnail do post
 */
function sg_ajax_get_post_thumbnail() {
	$post_url = isset( $_POST['post_url'] ) ? esc_url_raw( $_POST['post_url'] ) : '';
	
	if ( empty( $post_url ) ) {
		wp_send_json_error( array( 'message' => 'URL não fornecida' ) );
	}
	
	$post_id = url_to_postid( $post_url );
	
	if ( ! $post_id ) {
		wp_send_json_error( array( 'message' => 'Post não encontrado' ) );
	}
	
	$thumbnail_html = sg_get_post_thumbnail_html( $post_id, $post_url );
	
	wp_send_json_success( array( 'html' => $thumbnail_html ) );
}
add_action( 'wp_ajax_sg_get_post_thumbnail', 'sg_ajax_get_post_thumbnail' );
add_action( 'wp_ajax_nopriv_sg_get_post_thumbnail', 'sg_ajax_get_post_thumbnail' );

/**
 * Remover imagens problemáticas do conteúdo das páginas antes de exibir
 */
function sg_remove_problematic_images_from_content( $content ) {
	// Apenas processar se não estiver no admin e for conteúdo de post/page
	if ( is_admin() ) {
		return $content;
	}
	
	// Lista de imagens problemáticas
	$problematic_images = array(
		'MPMA.png',
		'TJTO.png',
		'TJCE.png',
		'TJDFT.png',
		'Delegado-da-Policia-Federal.png',
		'Magistratura-Federal-TRF.png',
		'cropped-a4-212x300-Photoroom',
		'/2025/05/',
	);
	
	$modified = $content;
	
	foreach ( $problematic_images as $problematic ) {
		$escaped = preg_quote( $problematic, '/' );
		
		// Padrão 1: <img...> dentro de <a>...</a>
		$pattern1 = '/(<a\s[^>]*href=[\'"]([^\'"]*)[\'"][^>]*>)\s*(<img\s[^>]*src=[\'"][^\'"]*' . $escaped . '[^\'"]*[\'"][^>]*>)\s*(<\/a>)/is';
		$modified = preg_replace( $pattern1, '$1<!-- Imagem removida: arquivo não existe -->$4', $modified );
		
		// Padrão 2: <img...> standalone
		$pattern2 = '/(<img\s[^>]*src=[\'"][^\'"]*' . $escaped . '[^\'"]*[\'"][^>]*>)/is';
		$modified = preg_replace( $pattern2, '<!-- Imagem removida: arquivo não existe -->', $modified );
		
		// Padrão 3: srcset também pode conter a imagem problemática
		$pattern3 = '/(srcset=[\'"][^\'"]*' . $escaped . '[^\'"]*[\'"])/is';
		$modified = preg_replace( $pattern3, '', $modified );
	}
	
	return $modified;
}
add_filter( 'the_content', 'sg_remove_problematic_images_from_content', 20 );

/**
 * Modificar output do widget usando filtro de sidebar params
 */
function sg_modify_recent_posts_widget_html( $params ) {
	global $wp_registered_widgets;
	
	if ( ! isset( $params[0]['widget_id'] ) ) {
		return $params;
	}
	
	$widget_id = $params[0]['widget_id'];
	
	// Verificar se é widget de posts recentes
	if ( strpos( $widget_id, 'recent-posts' ) !== false || strpos( $widget_id, 'recent_posts' ) !== false ) {
		// Interceptar o callback do widget
		if ( isset( $wp_registered_widgets[ $widget_id ] ) && isset( $wp_registered_widgets[ $widget_id ]['callback'] ) ) {
			$original_callback = $wp_registered_widgets[ $widget_id ]['callback'];
			
			// Substituir callback por um que adiciona thumbnails
			$wp_registered_widgets[ $widget_id ]['callback'] = function() use ( $params, $original_callback ) {
				// Executar callback original dentro de output buffer
				ob_start();
				if ( is_callable( $original_callback ) ) {
					call_user_func_array( $original_callback, func_get_args() );
				}
				$output = ob_get_clean();
				
				// Processar output para adicionar thumbnails
				$output = sg_process_recent_posts_output( $output );
				echo $output;
			};
		}
	}
	
	return $params;
}
add_filter( 'dynamic_sidebar_params', 'sg_modify_recent_posts_widget_html', 20 );

/**
 * Processar output HTML e adicionar thumbnails
 */
function sg_process_recent_posts_output( $html ) {
	// Verificar se contém widget de posts recentes
	if ( strpos( $html, 'widget_recent_entries' ) === false ) {
		return $html;
	}
	
	// Encontrar todas as tags <li> dentro do widget
	preg_match_all( '/<li(?:\s+[^>]*)?>(.*?)<\/li>/is', $html, $li_matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE );
	
	if ( empty( $li_matches ) ) {
		return $html;
	}
	
	// Processar de trás para frente para manter os offsets corretos
	$offset = 0;
	foreach ( array_reverse( $li_matches ) as $match ) {
		$full_match = $match[0];
		$content = $match[1];
		$match_pos = $match[0][1] + $offset;
		
		// Extrair URL do link
		preg_match( '/<a\s+href=["\']([^"\']+)["\']/', $content[0], $url_match );
		
		if ( ! empty( $url_match[1] ) ) {
			$url = $url_match[1];
			$post_id = url_to_postid( $url );
			
			if ( $post_id > 0 ) {
				$thumbnail_html = sg_get_post_thumbnail_html( $post_id, $url );
				$new_li = '<li class="recent-post-with-thumbnail">' . $thumbnail_html . '<div class="recent-post-content">' . $content[0] . '</div></li>';
				$html = substr_replace( $html, $new_li, $match_pos, strlen( $full_match[0] ) );
				$offset += strlen( $new_li ) - strlen( $full_match[0] );
			}
		}
	}
	
	return $html;
}

/**
 * Obter HTML do thumbnail do post
 */
function sg_get_post_thumbnail_html( $post_id, $permalink ) {
	$title = get_the_title( $post_id );
	$thumbnail_html = '';
	$has_thumbnail = false;
	
	// 1. Tentar featured image
	if ( has_post_thumbnail( $post_id ) ) {
		$thumbnail_id = get_post_thumbnail_id( $post_id );
		$thumb_url = wp_get_attachment_image_src( $thumbnail_id, 'medium' );
		if ( $thumb_url && ! empty( $thumb_url[0] ) ) {
			$thumbnail_html = '<div class="recent-post-thumbnail"><a href="' . esc_url( $permalink ) . '"><img src="' . esc_url( $thumb_url[0] ) . '" alt="' . esc_attr( $title ) . '" width="' . esc_attr( $thumb_url[1] ) . '" height="' . esc_attr( $thumb_url[2] ) . '" loading="lazy" /></a></div>';
			$has_thumbnail = true;
		}
	}
	
	// 2. Tentar primeira imagem do conteúdo
	if ( ! $has_thumbnail ) {
		$content = get_post_field( 'post_content', $post_id );
		$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches );
		if ( ! empty( $matches[1][0] ) ) {
			$first_img = $matches[1][0];
			if ( substr( $first_img, 0, 2 ) === '//' ) {
				$first_img = 'http:' . $first_img;
			}
			$thumbnail_html = '<div class="recent-post-thumbnail"><a href="' . esc_url( $permalink ) . '"><img src="' . esc_url( $first_img ) . '" alt="' . esc_attr( $title ) . '" loading="lazy" /></a></div>';
			$has_thumbnail = true;
		}
	}
	
	// 3. Placeholder
	if ( ! $has_thumbnail ) {
		$thumbnail_html = '<div class="recent-post-thumbnail recent-post-placeholder"><a href="' . esc_url( $permalink ) . '"><span class="placeholder-icon">📄</span></a></div>';
	}
	
	return $thumbnail_html;
}

/**
 * Remove widgets de comentários e arquivos do sidebar (backup)
 */
function sg_remove_comments_widget_from_sidebar( $sidebars_widgets ) {
	if ( ! is_array( $sidebars_widgets ) ) {
		return $sidebars_widgets;
	}
	
	// Verificar todas as sidebars
	foreach ( $sidebars_widgets as $sidebar_id => $widgets ) {
		if ( is_array( $widgets ) ) {
			$removed = false;
			foreach ( $widgets as $key => $widget ) {
				// Remover widgets relacionados a comentários
				if ( strpos( $widget, 'recent-comments' ) !== false || 
				     strpos( $widget, 'recent_comments' ) !== false ||
				     ( strpos( $widget, 'comments' ) !== false && strpos( $widget, 'recent' ) !== false ) ||
				     // Remover widgets relacionados a arquivos
				     strpos( $widget, 'archives' ) !== false ||
				     strpos( $widget, 'archive' ) !== false ) {
					unset( $sidebars_widgets[ $sidebar_id ][ $key ] );
					$removed = true;
				}
			}
			// Reindexar array apenas se algo foi removido
			if ( $removed && isset( $sidebars_widgets[ $sidebar_id ] ) ) {
				$sidebars_widgets[ $sidebar_id ] = array_values( $sidebars_widgets[ $sidebar_id ] );
			}
		}
	}
	return $sidebars_widgets;
}
add_filter( 'sidebars_widgets', 'sg_remove_comments_widget_from_sidebar', 99 );

/**
 * Ocultar seções de comentários e arquivos no sidebar via JavaScript (backup do CSS)
 */
function sg_hide_comments_sidebar_script() {
	?>
	<script>
	(function() {
		// Aguardar o DOM estar pronto
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', hideSidebarWidgets);
		} else {
			hideSidebarWidgets();
		}
		
		function hideSidebarWidgets() {
			var sidebar = document.getElementById('secondary');
			if (!sidebar) return;
			
			// Lista de termos para ocultar
			var hideTerms = ['Comentários', 'Comments', 'Arquivos', 'Archives'];
			
			// Procurar por widgets
			var widgets = sidebar.querySelectorAll('section.widget, .widget');
			widgets.forEach(function(widget) {
				var title = widget.querySelector('.widget-title, h2.widget-title, h2');
				
				// Verificar se o título contém algum dos termos
				if (title) {
					var titleText = title.textContent.trim();
					for (var i = 0; i < hideTerms.length; i++) {
						if (titleText === hideTerms[i] || titleText.includes(hideTerms[i])) {
							widget.style.display = 'none';
							return;
						}
					}
				}
				
				// Verificar por classes e IDs relacionados a comentários
				var widgetId = widget.id ? widget.id.toLowerCase() : '';
				var widgetClass = widget.className ? widget.className.toLowerCase() : '';
				
				if (widgetId.includes('comment') || widgetId.includes('comments') ||
				    widgetClass.includes('comment') || widgetClass.includes('comments') ||
				    widgetId.includes('archive') || widgetId.includes('archives') ||
				    widgetClass.includes('archive') || widgetClass.includes('archives') ||
				    widgetClass.includes('arquivo')) {
					widget.style.display = 'none';
				}
			});
		}
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'sg_hide_comments_sidebar_script' );

/**
 * Detectar categoria do evento pelo título
 */
function sg_detect_event_category( $title ) {
	$title_lower = strtolower( $title );
	
	$categorias = array(
		'ministério público' => 'ministerio-publico',
		'ministerio publico' => 'ministerio-publico',
		'mp' => 'ministerio-publico',
		'mpsp' => 'ministerio-publico',
		'mpmg' => 'ministerio-publico',
		'pge' => 'procuradoria',
		'procurador' => 'procuradoria',
		'procuradoria' => 'procuradoria',
		'magistratura' => 'magistratura',
		'magistrado' => 'magistratura',
		'tj' => 'magistratura',
		'trf' => 'magistratura',
		'delegado' => 'delegado',
		'policia' => 'delegado',
		'enam' => 'enam',
		'enan' => 'enam',
	);
	
	foreach ( $categorias as $keyword => $categoria ) {
		if ( strpos( $title_lower, $keyword ) !== false ) {
			return $categoria;
		}
	}
	
	return 'outros';
}

/**
 * Inline CSS de alta prioridade para estilizar a página "Minha Conta"
 * Útil quando estilos de plugins sobrepõem o tema.
 */
function sg_inline_styles_my_account() {
    $sg_is_my_account = (
        function_exists( 'is_account_page' ) && is_account_page()
    ) || (
        function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url()
    ) || (
        is_page( get_option( 'woocommerce_myaccount_page_id' ) )
    ) || is_page( 'minha-conta' ) || is_page( 'my-account' );

    if ( $sg_is_my_account ) {
        echo '<style id="sg-my-account-inline" type="text/css">'
            // Navegação vertical como sidebar por padrão (desktop)
            . 'body.woocommerce-account nav.woocommerce-MyAccount-navigation ul,'
            . '.woocommerce nav.woocommerce-MyAccount-navigation ul,'
            . 'body.woocommerce-account nav[aria-label*="conta"] ul,'
            . 'body.woocommerce-account nav[aria-label*="account"] ul{list-style:none!important;margin:0 0 16px 0!important;padding:0!important;display:flex!important;flex-direction:column!important;align-items:stretch!important;gap:8px!important;overflow:visible!important}'
            . 'body.woocommerce-account nav.woocommerce-MyAccount-navigation li,'
            . '.woocommerce nav.woocommerce-MyAccount-navigation li,'
            . 'body.woocommerce-account nav[aria-label*="conta"] li,'
            . 'body.woocommerce-account nav[aria-label*="account"] li{list-style:none!important;display:flex!important;align-items:center!important;margin:0!important;min-height:40px!important}'
            . 'body.woocommerce-account nav.woocommerce-MyAccount-navigation li::marker,'
            . '.woocommerce nav.woocommerce-MyAccount-navigation li::marker,'
            . 'body.woocommerce-account nav[aria-label*="conta"] li::marker,'
            . 'body.woocommerce-account nav[aria-label*="account"] li::marker{content:""!important}'
            . 'body.woocommerce-account nav.woocommerce-MyAccount-navigation ul li a,'
            . '.woocommerce nav.woocommerce-MyAccount-navigation ul li a,'
            . 'body.woocommerce-account nav[aria-label*="conta"] ul li a,'
            . 'body.woocommerce-account nav[aria-label*="account"] ul li a{display:inline-flex!important;align-items:center!important;justify-content:flex-start!important;gap:10px!important;padding:10px 12px!important;line-height:1.3!important;font-size:14px!important;height:auto!important;box-sizing:border-box!important;background:#fff!important;border:1px solid var(--sg-color-border)!important;border-radius:10px!important;text-decoration:none!important;color:var(--sg-color-text)!important;font-weight:600!important;box-shadow:0 2px 8px rgba(0,0,0,.05)!important}'
            . 'body.woocommerce-account nav.woocommerce-MyAccount-navigation ul li.is-active > a{padding:10px 12px!important;line-height:1.3!important;height:auto!important}'
            . 'body.woocommerce-account nav.woocommerce-MyAccount-navigation ul li a:hover,'
            . '.woocommerce nav.woocommerce-MyAccount-navigation ul li a:hover,'
            . 'body.woocommerce-account nav[aria-label*="conta"] ul li a:hover,'
            . 'body.woocommerce-account nav[aria-label*="account"] ul li a:hover{background:var(--sg-color-primary)!important;border-color:var(--sg-color-primary)!important;color:#000!important}'
            // Forçar cor de texto dos botões de ação
            . 'body.woocommerce-account .woocommerce a.button,body.woocommerce-account .woocommerce .button{color:#111!important;-webkit-text-fill-color:#111!important}'
            . 'body.woocommerce-account .woocommerce a.button:hover,body.woocommerce-account .woocommerce .button:hover{color:#111!important;-webkit-text-fill-color:#111!important;text-shadow:none!important}'
            // Esconder lista duplicada de atalhos dentro do conteúdo
            . 'body.woocommerce-account .woocommerce-MyAccount-content > ul,'
            . '.woocommerce .woocommerce-MyAccount-content > ul,'
            . '.woocommerce .woocommerce-MyAccount-content ul,'
            . 'body.woocommerce-account .entry-content > ul{display:none!important}'
            // Parágrafos compactos
            . 'body.woocommerce-account .woocommerce-MyAccount-content p{margin:6px 0 8px!important}'
            . 'body.woocommerce-account .woocommerce-MyAccount-content p:first-child{margin-top:12px!important}'
            . 'body.woocommerce-account .woocommerce-MyAccount-content p:last-child{margin-bottom:0!important}'
            // Mobile: voltar para navegação horizontal rolável
            . '@media(max-width:782px){'
            . 'body.woocommerce-account nav.woocommerce-MyAccount-navigation ul{display:flex!important;flex-direction:row!important;gap:12px!important;overflow-x:auto!important;-webkit-overflow-scrolling:touch!important}'
            . 'body.woocommerce-account nav.woocommerce-MyAccount-navigation li{min-height:36px!important}'
            . 'body.woocommerce-account nav.woocommerce-MyAccount-navigation ul li a,'
            . 'body.woocommerce-account nav.woocommerce-MyAccount-navigation ul li.is-active > a{padding:0 10px!important;line-height:1!important;height:100%!important;justify-content:center!important}'
            . '}'
            . '</style>';
    }
}
add_action( 'wp_head', 'sg_inline_styles_my_account', 999 );

/**
 * Buscar eventos de concursos (ETN e The Events Calendar)
 * OTIMIZADO: Usa JOIN para evitar múltiplas consultas ao banco
 * Com cache para reduzir conexões ao banco
 */
function sg_get_concurso_events( $limit = 10, $categoria = null ) {
	global $wpdb;
	
	// Cache de 5 minutos para reduzir consultas ao banco
	$cache_key = 'sg_concurso_events_' . md5( serialize( array( $limit, $categoria ) ) );
	$cached = wp_cache_get( $cache_key, 'sg_events' );
	
	if ( false !== $cached ) {
		return $cached;
	}
	
	$today = current_time( 'Y-m-d' );
	$events = array();
	
	// PRIMEIRO: Buscar eventos próprios (sg_eventos) - PRIORIDADE
	$sg_eventos_query = $wpdb->get_results( $wpdb->prepare( "
		SELECT p.ID, p.post_title, p.post_name,
		       pm1.meta_value as start_date,
		       pm2.meta_value as end_date
		FROM {$wpdb->posts} p
		LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_sg_evento_data_inicio'
		LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_sg_evento_data_fim'
		WHERE p.post_type = 'sg_eventos'
		AND p.post_status = 'publish'
		AND pm1.meta_value IS NOT NULL
		ORDER BY pm1.meta_value ASC
		LIMIT %d
	", $limit * 2 ) );
	
	if ( ! empty( $sg_eventos_query ) ) {
		foreach ( $sg_eventos_query as $row ) {
			$event_date = ! empty( $row->start_date ) ? $row->start_date : $row->end_date;
			
			if ( $event_date ) {
				// Se for evento futuro ou recente (últimos 30 dias), incluir
				$event_timestamp = strtotime( $event_date );
				$days_diff = ( $event_timestamp - strtotime( $today ) ) / ( 60 * 60 * 24 );
				
				if ( $days_diff >= -30 ) { // Eventos dos últimos 30 dias ou futuros
					$event_title = $row->post_title;
					$event_categoria = sg_detect_event_category( $event_title );
					
					// Verificar categoria se especificada
					if ( $categoria && $event_categoria !== $categoria ) {
						continue;
					}
					
					$events[] = array(
						'id'        => $row->ID,
						'title'     => $event_title,
						'date'      => $event_date,
						'end_date'  => $row->end_date,
						'permalink' => get_permalink( $row->ID ),
						'type'      => 'sg_eventos',
						'categoria' => $event_categoria,
					);
				}
			}
		}
	}
	
	// Buscar eventos ETN usando SQL direto com JOIN para melhor performance
	// Evita múltiplas chamadas de get_post_meta() dentro de loops
	$etn_query = $wpdb->get_results( $wpdb->prepare( "
		SELECT p.ID, p.post_title, p.post_name,
		       pm1.meta_value as start_date,
		       pm2.meta_value as end_date
		FROM {$wpdb->posts} p
		LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = 'etn_start_date'
		LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = 'etn_end_date'
		WHERE p.post_type = 'etn'
		AND p.post_status = 'publish'
		AND (pm1.meta_value IS NOT NULL OR pm2.meta_value IS NOT NULL)
		ORDER BY p.post_date DESC
		LIMIT %d
	", $limit * 2 ) );
	
	if ( ! empty( $etn_query ) ) {
		foreach ( $etn_query as $row ) {
			// Aceitar eventos futuros ou recentes (últimos 30 dias)
			$event_date = ! empty( $row->start_date ) ? $row->start_date : $row->end_date;
			
			if ( $event_date ) {
				// Se for evento futuro ou recente (últimos 30 dias), incluir
				$event_timestamp = strtotime( $event_date );
				$days_diff = ( $event_timestamp - strtotime( $today ) ) / ( 60 * 60 * 24 );
				
				if ( $days_diff >= -30 ) { // Eventos dos últimos 30 dias ou futuros
					$event_title = $row->post_title;
					$event_categoria = sg_detect_event_category( $event_title );
					
					// Filtrar por categoria se especificada
					if ( $categoria && $event_categoria !== $categoria ) {
						continue;
					}
					
					$events[] = array(
						'id'        => $row->ID,
						'title'     => $event_title,
						'date'      => $event_date,
						'end_date'  => $row->end_date,
						'permalink' => get_permalink( $row->ID ),
						'type'      => 'etn',
						'categoria' => $event_categoria,
					);
				}
			}
		}
	}
	
	// Buscar eventos The Events Calendar (tribe_events)
	if ( post_type_exists( 'tribe_events' ) ) {
		$tribe_query = $wpdb->get_results( $wpdb->prepare( "
			SELECT p.ID, p.post_title, 
			       pm1.meta_value as start_date,
			       pm2.meta_value as end_date
			FROM {$wpdb->posts} p
			LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_EventStartDate'
			LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_EventEndDate'
			WHERE p.post_type = 'tribe_events'
			AND p.post_status = 'publish'
			AND pm1.meta_value >= %s
			ORDER BY pm1.meta_value ASC
			LIMIT %d
		", $today, $limit ) );
		
		foreach ( $tribe_query as $row ) {
			if ( ! empty( $row->start_date ) ) {
				$start_date_str = is_numeric( $row->start_date ) ? date( 'Y-m-d', $row->start_date ) : date( 'Y-m-d', strtotime( $row->start_date ) );
				$end_date_str = null;
				if ( ! empty( $row->end_date ) ) {
					$end_date_str = is_numeric( $row->end_date ) ? date( 'Y-m-d', $row->end_date ) : date( 'Y-m-d', strtotime( $row->end_date ) );
				}
				
				$events[] = array(
					'id'        => $row->ID,
					'title'     => $row->post_title,
					'date'      => $start_date_str,
					'end_date'  => $end_date_str,
					'permalink' => get_permalink( $row->ID ),
					'type'      => 'tribe_events',
				);
			}
		}
	}
	
	// Se não encontrou eventos futuros, buscar os mais recentes (para teste)
	if ( empty( $events ) ) {
		$recent_etn = $wpdb->get_results( $wpdb->prepare( "
			SELECT p.ID, p.post_title, 
			       pm1.meta_value as start_date,
			       pm2.meta_value as end_date
			FROM {$wpdb->posts} p
			LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = 'etn_start_date'
			LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = 'etn_end_date'
			WHERE p.post_type = 'etn'
			AND p.post_status = 'publish'
			AND (pm1.meta_value IS NOT NULL OR pm2.meta_value IS NOT NULL)
			ORDER BY p.post_date DESC
			LIMIT %d
		", $limit ) );
		
		foreach ( $recent_etn as $row ) {
			$event_date = ! empty( $row->start_date ) ? $row->start_date : ( ! empty( $row->end_date ) ? $row->end_date : date( 'Y-m-d' ) );
			$events[] = array(
				'id'        => $row->ID,
				'title'     => $row->post_title,
				'date'      => $event_date,
				'end_date'  => $row->end_date,
				'permalink' => get_permalink( $row->ID ),
				'type'      => 'etn',
			);
		}
	}
	
	// Ordenar por data
	usort( $events, function( $a, $b ) {
		$date_a = strtotime( $a['date'] );
		$date_b = strtotime( $b['date'] );
		return $date_a - $date_b;
	} );
	
	$result = array_slice( $events, 0, $limit );
	
	// Armazenar no cache por 5 minutos
	wp_cache_set( $cache_key, $result, 'sg_events', 300 );
	
	return $result;
}

/**
 * Buscar todos os eventos para o calendário dinâmico
 * OTIMIZADO: Usa JOIN para evitar múltiplas consultas ao banco
 * Com cache para reduzir conexões ao banco
 */
function sg_get_all_calendar_events() {
	global $wpdb;
	
	// Cache de 10 minutos para reduzir consultas ao banco
	$cache_key = 'sg_all_calendar_events';
	$cached = wp_cache_get( $cache_key, 'sg_events' );
	
	if ( false !== $cached ) {
		return $cached;
	}
	
	$today = current_time( 'Y-m-d' );
	$events = array();
	
	// PRIMEIRO: Buscar eventos próprios (sg_eventos) - PRIORIDADE
	$sg_eventos_query = $wpdb->get_results( "
		SELECT p.ID, p.post_title, p.post_name,
		       pm1.meta_value as start_date,
		       pm2.meta_value as end_date
		FROM {$wpdb->posts} p
		LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_sg_evento_data_inicio'
		LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_sg_evento_data_fim'
		WHERE p.post_type = 'sg_eventos'
		AND p.post_status = 'publish'
		AND pm1.meta_value IS NOT NULL
		ORDER BY pm1.meta_value ASC
	" );
	
	if ( ! empty( $sg_eventos_query ) ) {
		foreach ( $sg_eventos_query as $row ) {
			$event_date = ! empty( $row->start_date ) ? $row->start_date : $row->end_date;
			
			if ( $event_date ) {
				// Incluir todos os eventos, independentemente da data (incluindo passados)
				$event_title = $row->post_title;
				$event_categoria = sg_detect_event_category( $event_title );
				
				$events[] = array(
					'id'        => $row->ID,
					'title'     => $event_title,
					'date'      => $event_date,
					'end_date'  => $row->end_date,
					'permalink' => get_permalink( $row->ID ),
					'type'      => 'sg_eventos',
					'categoria' => $event_categoria,
				);
			}
		}
	}
	
	// Buscar todos os eventos ETN usando JOIN para evitar múltiplas consultas
	$etn_query = $wpdb->get_results( "
		SELECT p.ID, p.post_title, p.post_name,
		       pm1.meta_value as start_date,
		       pm2.meta_value as end_date
		FROM {$wpdb->posts} p
		LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = 'etn_start_date'
		LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = 'etn_end_date'
		WHERE p.post_type = 'etn'
		AND p.post_status = 'publish'
		AND (pm1.meta_value IS NOT NULL OR pm2.meta_value IS NOT NULL)
		ORDER BY p.post_date DESC
	" );
	
	if ( ! empty( $etn_query ) ) {
		foreach ( $etn_query as $row ) {
			$event_date = ! empty( $row->start_date ) ? $row->start_date : $row->end_date;
			
			if ( $event_date ) {
				// Incluir todos os eventos, independentemente da data (incluindo passados)
				$event_title = $row->post_title;
				$event_categoria = sg_detect_event_category( $event_title );
				
				$events[] = array(
					'id'        => $row->ID,
					'title'     => $event_title,
					'date'      => $event_date,
					'end_date'  => $row->end_date,
					'permalink' => get_permalink( $row->ID ),
					'type'      => 'etn',
					'categoria' => $event_categoria,
				);
			}
		}
	}
	
	// Buscar eventos The Events Calendar também usando JOIN
	if ( post_type_exists( 'tribe_events' ) ) {
		$tribe_query = $wpdb->get_results( "
			SELECT p.ID, p.post_title, p.post_name,
			       pm1.meta_value as start_date,
			       pm2.meta_value as end_date
			FROM {$wpdb->posts} p
			LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_EventStartDate'
			LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_EventEndDate'
			WHERE p.post_type = 'tribe_events'
			AND p.post_status = 'publish'
			AND pm1.meta_value IS NOT NULL
			ORDER BY p.post_date DESC
		" );
		
		foreach ( $tribe_query as $row ) {
			if ( $row->start_date ) {
				$start_date_str = is_numeric( $row->start_date ) ? date( 'Y-m-d', $row->start_date ) : date( 'Y-m-d', strtotime( $row->start_date ) );
				$event_title = $row->post_title;
				$event_categoria = sg_detect_event_category( $event_title );
				
				$events[] = array(
					'id'        => $row->ID,
					'title'     => $event_title,
					'date'      => $start_date_str,
					'end_date'  => $row->end_date ? ( is_numeric( $row->end_date ) ? date( 'Y-m-d', $row->end_date ) : date( 'Y-m-d', strtotime( $row->end_date ) ) ) : null,
					'permalink' => get_permalink( $row->ID ),
					'type'      => 'tribe_events',
					'categoria' => $event_categoria,
				);
			}
		}
	}
	
	// Ordenar por data
	usort( $events, function( $a, $b ) {
		$date_a = strtotime( $a['date'] );
		$date_b = strtotime( $b['date'] );
		return $date_a - $date_b;
	} );
	
	// Armazenar no cache por 10 minutos
	wp_cache_set( $cache_key, $events, 'sg_events', 600 );
	
	return $events;
}

/**
 * Limpar cache de eventos quando um evento for atualizado
 */
function sg_clear_events_cache( $post_id ) {
	$post_type = get_post_type( $post_id );
	
	// Se for um evento (sg_eventos, ETN ou tribe_events), limpar cache
	if ( in_array( $post_type, array( 'sg_eventos', 'etn', 'tribe_events' ) ) ) {
		// Limpar cache principal
		wp_cache_delete( 'sg_all_calendar_events', 'sg_events' );
		// Limpar possíveis caches de consultas específicas
		// Tentamos limpar os padrões mais comuns de cache key
		for ( $limit = 10; $limit <= 50; $limit += 10 ) {
			wp_cache_delete( 'sg_concurso_events_' . md5( serialize( array( $limit, null ) ) ), 'sg_events' );
			wp_cache_delete( 'sg_concurso_events_' . md5( serialize( array( $limit, 'ministerio-publico' ) ) ), 'sg_events' );
			wp_cache_delete( 'sg_concurso_events_' . md5( serialize( array( $limit, 'magistratura' ) ) ), 'sg_events' );
			wp_cache_delete( 'sg_concurso_events_' . md5( serialize( array( $limit, 'delegado' ) ) ), 'sg_events' );
		}
	}
}
add_action( 'save_post', 'sg_clear_events_cache' );
add_action( 'delete_post', 'sg_clear_events_cache' );

/**
 * Contar eventos por categoria
 */
function sg_count_events_by_category() {
	$events = sg_get_all_calendar_events();
	$counts = array(
		'ministerio-publico' => 0,
		'magistratura' => 0,
		'delegado' => 0,
		'enam' => 0,
		'procuradoria' => 0,
		'outros' => 0,
	);
	
	foreach ( $events as $event ) {
		$cat = isset( $event['categoria'] ) ? $event['categoria'] : 'outros';
		if ( isset( $counts[ $cat ] ) ) {
			$counts[ $cat ]++;
		} else {
			$counts['outros']++;
		}
	}
	
	return $counts;
}

/**
 * Adicionar categorias da loja ao menu "Cursos" quando há menu atribuído
 */
function sg_add_categories_to_cursos_menu( $items, $args ) {
	if ( ! isset( $args->theme_location ) || $args->theme_location !== 'primary' ) {
		return $items;
	}
	
	if ( ! class_exists( 'WooCommerce' ) ) {
		return $items;
	}
	
	// Verificar se já há itens dinâmicos adicionados (evitar duplicação e loop infinito)
	foreach ( $items as $item ) {
		if ( isset( $item->ID ) && ( $item->ID == 999999 || $item->ID > 1000000 ) ) {
			// Já foi processado, retornar sem modificar
			return $items;
		}
	}
	
	// Buscar o item do menu "Cursos"
	foreach ( $items as $item ) {
		// Verificar se é o item "Cursos" (pode ser por título ou URL)
		if ( ( stripos( $item->title, 'curso' ) !== false || stripos( $item->url, '#cursos' ) !== false || $item->url === '#' ) && $item->menu_item_parent == 0 ) {
			// Buscar categorias
			$categories = get_terms( array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'parent'     => 0,
				'number'     => 10,
				'orderby'    => 'count',
				'order'      => 'DESC',
			) );
			
			if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
				// Primeiro adicionar "Todos os Cursos"
				$shop_url = wc_get_page_permalink( 'shop' );
				if ( $shop_url ) {
					$todos_item = new stdClass();
					$todos_item->ID = 999999;
					$todos_item->db_id = 999999;
					$todos_item->menu_item_parent = $item->ID;
					$todos_item->object_id = 999999;
					$todos_item->object = 'custom';
					$todos_item->type = 'custom';
					$todos_item->type_label = 'Link Personalizado';
					$todos_item->url = $shop_url;
					$todos_item->title = 'Todos os Cursos';
					$todos_item->target = '';
					$todos_item->attr_title = '';
					$todos_item->description = '';
					$todos_item->classes = array( '' );
					$todos_item->xfn = '';
					$todos_item->current = false;
					$todos_item->current_item_ancestor = false;
					$todos_item->current_item_parent = false;
					$todos_item->post_parent = 0;
					$todos_item->post_type = 'nav_menu_item';
					
					// Adicionar após o item "Cursos"
					$item_index = array_search( $item, $items );
					if ( $item_index !== false ) {
						array_splice( $items, $item_index + 1, 0, array( $todos_item ) );
						
						// Adicionar categorias
						$menu_order = $item_index + 2;
						foreach ( $categories as $category ) {
							$cat_link = get_term_link( $category, 'product_cat' );
							
							// Verificar se houve erro ao obter o link
							if ( is_wp_error( $cat_link ) ) {
								continue;
							}
							
							$cat_item = new stdClass();
							$cat_item->ID = $category->term_id + 1000000;
							$cat_item->db_id = $category->term_id + 1000000;
							$cat_item->menu_item_parent = $item->ID;
							$cat_item->object_id = $category->term_id;
							$cat_item->object = 'product_cat';
							$cat_item->type = 'taxonomy';
							$cat_item->type_label = 'Categoria';
							$cat_item->url = $cat_link;
							$cat_item->title = $category->name;
							$cat_item->target = '';
							$cat_item->attr_title = '';
							$cat_item->description = '';
							$cat_item->classes = array( '' );
							$cat_item->xfn = '';
							$cat_item->current = false;
							$cat_item->current_item_ancestor = false;
							$cat_item->current_item_parent = false;
							$cat_item->post_parent = 0;
							$cat_item->post_type = 'nav_menu_item';
							
							array_splice( $items, $menu_order, 0, array( $cat_item ) );
							$menu_order++;
						}
					}
				}
			}
			break; // Sair do loop após encontrar o item "Cursos"
		}
	}
	
	return $items;
}
add_filter( 'wp_nav_menu_objects', 'sg_add_categories_to_cursos_menu', 10, 2 );

/**
 * Helper para ícones SVG por slug de categoria
 */
function sg_cat_icon_svg( $slug ) {
	$slug_lower = strtolower( $slug );
	$svg = '';
	
	// Remover width/height do SVG para permitir redimensionamento via CSS
	// Detectar padrões no slug ou nome
	if ( strpos( $slug_lower, 'ministerio-publico' ) !== false || strpos( $slug_lower, 'mp' ) !== false ) {
		// Ícone prédio/edifício
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="10" y="28" width="44" height="22" rx="2" fill="currentColor"/><rect x="14" y="18" width="36" height="8" rx="2" fill="currentColor"/><rect x="20" y="34" width="6" height="12" fill="#fff"/><rect x="29" y="34" width="6" height="12" fill="#fff"/><rect x="38" y="34" width="6" height="12" fill="#fff"/></svg>';
	} elseif ( strpos( $slug_lower, 'magistratura' ) !== false ) {
		// Ícone martelo
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="8" y="42" width="48" height="6" rx="3" fill="currentColor"/><rect x="22" y="12" width="16" height="8" rx="2" fill="currentColor"/><rect x="16" y="20" width="16" height="8" rx="2" transform="rotate(45 16 20)" fill="currentColor"/></svg>';
	} elseif ( strpos( $slug_lower, 'delegado' ) !== false ) {
		// Ícone escudo
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M32 8c6 6 12 4 20 4 0 22-8 32-20 36C20 44 12 34 12 12c8 0 14 2 20-4z" fill="currentColor"/><path d="M32 22l3 6 6 1-4 4 1 6-6-3-6 3 1-6-4-4 6-1 3-6z" fill="#fff"/></svg>';
	} elseif ( strpos( $slug_lower, 'enam' ) !== false ) {
		// Ícone documento
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="18" y="12" width="28" height="40" rx="3" fill="currentColor"/><rect x="24" y="20" width="16" height="4" fill="#fff"/><rect x="24" y="28" width="16" height="4" fill="#fff"/><rect x="24" y="36" width="12" height="4" fill="#fff"/></svg>';
	} elseif ( strpos( $slug_lower, 'procuradorias' ) !== false || strpos( $slug_lower, 'procuradoria' ) !== false ) {
		// Ícone balança
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="30" y="12" width="4" height="30" fill="currentColor"/><rect x="18" y="20" width="28" height="4" fill="currentColor"/><path d="M18 24l-6 10h12l-6-10zM46 24l-6 10h12l-6-10z" fill="currentColor"/></svg>';
	} elseif ( strpos( $slug_lower, 'analista' ) !== false || strpos( $slug_lower, 'juridica' ) !== false ) {
		// Ícone pasta/documento jurídico
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 10h24l8 8v32H20V10z" fill="currentColor"/><path d="M28 10v8h8l-8-8z" fill="#fff" opacity="0.3"/><rect x="24" y="26" width="16" height="2" fill="#fff"/><rect x="24" y="32" width="12" height="2" fill="#fff"/></svg>';
	} elseif ( strpos( $slug_lower, 'defensoria' ) !== false || strpos( $slug_lower, 'defensor' ) !== false ) {
		// Ícone escudo com cruz
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M32 8c6 6 12 4 20 4 0 22-8 32-20 36C20 44 12 34 12 12c8 0 14 2 20-4z" fill="currentColor"/><path d="M32 18v16M24 26h16" stroke="#fff" stroke-width="3" stroke-linecap="round"/></svg>';
	} elseif ( strpos( $slug_lower, 'lancamento' ) !== false || strpos( $slug_lower, 'novo' ) !== false ) {
		// Ícone estrela
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M32 8l4 12h12l-10 8 4 12-10-8-10 8 4-12-10-8h12l4-12z" fill="currentColor"/></svg>';
	} elseif ( strpos( $slug_lower, 'sg-juridico' ) !== false || strpos( $slug_lower, 'sg juridico' ) !== false || strpos( $slug_lower, 'sgjuridico' ) !== false ) {
		// Ícone livro/código
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="16" y="10" width="32" height="44" rx="2" fill="currentColor"/><path d="M24 18h16M24 26h12M24 34h16" stroke="#fff" stroke-width="2" stroke-linecap="round"/><circle cx="32" cy="44" r="3" fill="#fff"/></svg>';
	} elseif ( strpos( $slug_lower, 'confira' ) !== false ) {
		// Ícone olho/visualização
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M32 16c-12 0-20 8-20 16s8 16 20 16 20-8 20-16-8-16-20-16z" fill="currentColor"/><circle cx="32" cy="32" r="6" fill="#fff"/></svg>';
	} else {
		// Ícone padrão: livro/documento
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="18" y="12" width="28" height="40" rx="3" fill="currentColor"/><rect x="24" y="20" width="16" height="4" fill="#fff"/><rect x="24" y="28" width="16" height="4" fill="#fff"/><rect x="24" y="36" width="12" height="4" fill="#fff"/></svg>';
	}
	
	return $svg;
}

/**
 * Exibir calendário dinâmico de eventos de concursos no sidebar
 */
function sg_display_concurso_calendar() {
	$all_events = sg_get_all_calendar_events();
	$category_counts = sg_count_events_by_category();
	
	// Organizar eventos por data para o calendário
	$events_by_date = array();
	foreach ( $all_events as $event ) {
		$date_key = $event['date'];
		if ( ! isset( $events_by_date[ $date_key ] ) ) {
			$events_by_date[ $date_key ] = array();
		}
		$events_by_date[ $date_key ][] = $event;
	}
	
	// Definir mês e ano atual
	$current_month = isset( $_GET['cal_month'] ) ? intval( $_GET['cal_month'] ) : date( 'n' );
	$current_year = isset( $_GET['cal_year'] ) ? intval( $_GET['cal_year'] ) : date( 'Y' );
	
	// Nomes das categorias
	$categorias_nomes = array(
		'ministerio-publico' => 'Ministério Público',
		'magistratura' => 'Magistratura',
		'delegado' => 'Delegado',
		'enam' => 'ENAM',
		'procuradoria' => 'Procuradoria',
	);
	
	$is_home = is_home() && is_front_page();
	?>
	<div class="widget widget-concursos-calendar" id="calendario-concursos">
		<h3 class="widget-title">Calendário de Concursos</h3>
		
		<!-- Filtros por Categoria (ocultar na home) -->
		<?php if ( ! $is_home ) : ?>
		<div class="calendario-filtros" role="tablist" aria-label="Filtros de categoria">
			<div class="filtro-categoria" data-categoria="todos" aria-selected="true">
				<span class="filtro-nome">Todos</span>
			</div>
			<?php foreach ( $categorias_nomes as $slug => $nome ) : 
				$count = isset( $category_counts[ $slug ] ) ? $category_counts[ $slug ] : 0;
				if ( $count > 0 ) :
			?>
				<div class="filtro-categoria" data-categoria="<?php echo esc_attr( $slug ); ?>">
					<span class="filtro-nome"><?php echo esc_html( $nome ); ?></span>
					<span class="filtro-count"><?php echo esc_html( $count ); ?></span>
				</div>
			<?php 
				endif;
			endforeach; ?>
		</div>
		<?php endif; ?>
		
		<!-- Navegação do Calendário -->
		<div class="calendario-nav">
			<button class="calendario-prev" data-action="prev" aria-label="Mês anterior">‹</button>
			<div class="calendario-month-year" aria-live="polite">
				<span class="calendario-month-name"><?php echo date_i18n( 'F', mktime( 0, 0, 0, $current_month, 1, $current_year ) ); ?></span>
				<span class="calendario-year"><?php echo esc_html( $current_year ); ?></span>
			</div>
			<button class="calendario-today" data-action="today" aria-label="Voltar para hoje">Hoje</button>
			<button class="calendario-next" data-action="next" aria-label="Próximo mês">›</button>
		</div>
		
		<!-- Calendário Visual -->
		<div class="calendario-grid" data-month="<?php echo esc_attr( $current_month ); ?>" data-year="<?php echo esc_attr( $current_year ); ?>">
			<!-- Cabeçalho dos dias da semana -->
			<div class="calendario-weekdays">
				<div class="calendario-weekday">Dom</div>
				<div class="calendario-weekday">Seg</div>
				<div class="calendario-weekday">Ter</div>
				<div class="calendario-weekday">Qua</div>
				<div class="calendario-weekday">Qui</div>
				<div class="calendario-weekday">Sex</div>
				<div class="calendario-weekday">Sáb</div>
			</div>
			
			<!-- Dias do calendário serão gerados via JavaScript -->
			<div class="calendario-days" id="calendario-days">
				<!-- Preenchido via JavaScript -->
			</div>
		</div>
		
		<!-- Lista de Eventos (substituída pelo calendário, mas mantida como fallback) -->
		<div class="concursos-calendar-list" id="eventos-lista">
			<!-- Será preenchida via JavaScript baseado na categoria selecionada -->
		</div>
		
		<?php
		// Lista dos 5 eventos mais próximos - mostrar sempre na home
		$today = current_time( 'Y-m-d' );
		$upcoming_events = array_filter( $all_events, function( $event ) use ( $today ) {
			return $event['date'] >= $today;
		} );
		
		// Ordenar por data (já devem estar ordenados, mas garantir)
		usort( $upcoming_events, function( $a, $b ) {
			$date_a = strtotime( $a['date'] );
			$date_b = strtotime( $b['date'] );
			return $date_a - $date_b;
		} );
		
		// Pegar os 5 primeiros
		$upcoming_events = array_slice( $upcoming_events, 0, 5 );
		
		if ( ! empty( $upcoming_events ) ) :
		?>
		<!-- Lista dos Próximos Eventos -->
		<div class="proximos-eventos-lista">
			<h4 class="proximos-eventos-title">Próximos Eventos</h4>
			<ul class="proximos-eventos">
				<?php foreach ( $upcoming_events as $event ) : 
					$event_date = strtotime( $event['date'] );
					$day = date( 'd', $event_date );
					$month = date_i18n( 'M', $event_date );
					?>
					<li class="calendario-item">
						<div class="calendario-date">
							<span class="calendario-day"><?php echo esc_html( $day ); ?></span>
							<span class="calendario-month"><?php echo esc_html( strtoupper( $month ) ); ?></span>
						</div>
						<div class="calendario-content">
							<a href="<?php echo esc_url( $event['permalink'] ); ?>" class="calendario-title">
								<?php echo esc_html( $event['title'] ); ?>
							</a>
							<div class="calendario-date-text">
								<?php echo date_i18n( 'd/m/Y', $event_date ); ?>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>
		
		<div class="calendario-view-all">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'etn' ) ?: home_url( '/eventos' ) ); ?>">Ver todos os eventos →</a>
		</div>
		
		<!-- Dados para JavaScript -->
		<script type="application/json" id="calendario-events-data">
			<?php echo json_encode( $events_by_date, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?>
		</script>
	</div>
	<?php
}

/**
 * Handler para processar formulário de contato
 */
function sg_handle_contact_form() {
	// Verificar nonce
	if ( ! isset( $_POST['sg_contact_nonce'] ) || ! wp_verify_nonce( $_POST['sg_contact_nonce'], 'sg_contact_form' ) ) {
		wp_send_json_error( array( 'message' => 'Erro de segurança. Por favor, recarregue a página e tente novamente.' ) );
	}

	// Sanitizar dados
	$name = isset( $_POST['contact_name'] ) ? sanitize_text_field( $_POST['contact_name'] ) : '';
	$email = isset( $_POST['contact_email'] ) ? sanitize_email( $_POST['contact_email'] ) : '';
	$phone = isset( $_POST['contact_phone'] ) ? sanitize_text_field( $_POST['contact_phone'] ) : '';
	$subject = isset( $_POST['contact_subject'] ) ? sanitize_text_field( $_POST['contact_subject'] ) : '';
	$message = isset( $_POST['contact_message'] ) ? sanitize_textarea_field( $_POST['contact_message'] ) : '';

	// Validar campos obrigatórios
	if ( empty( $name ) || empty( $email ) || empty( $subject ) || empty( $message ) ) {
		wp_send_json_error( array( 'message' => 'Por favor, preencha todos os campos obrigatórios.' ) );
	}

	// Validar email
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'Por favor, insira um e-mail válido.' ) );
	}

	// Prevenir spam básico
	if ( strpos( $message, 'http://' ) !== false || strpos( $message, 'https://' ) !== false || strpos( $message, 'www.' ) !== false ) {
		wp_send_json_error( array( 'message' => 'Mensagem contém links não permitidos.' ) );
	}

	// Traduzir assunto
	$subject_labels = array(
		'duvida' => 'Dúvida sobre cursos',
		'suporte' => 'Suporte técnico',
		'parceria' => 'Parcerias',
		'outro' => 'Outro'
	);
	$subject_label = isset( $subject_labels[ $subject ] ) ? $subject_labels[ $subject ] : 'Contato do Site';

	// Preparar email
	$to = get_option( 'admin_email' );
	$email_subject = '[' . get_bloginfo( 'name' ) . '] ' . $subject_label . ' - ' . $name;
	
	$email_message = "Novo contato recebido através do formulário do site.\n\n";
	$email_message .= "Nome: $name\n";
	$email_message .= "E-mail: $email\n";
	if ( ! empty( $phone ) ) {
		$email_message .= "Telefone: $phone\n";
	}
	$email_message .= "Assunto: $subject_label\n\n";
	$email_message .= "Mensagem:\n$message\n";

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'From: ' . get_bloginfo( 'name' ) . ' <' . $to . '>',
		'Reply-To: ' . $name . ' <' . $email . '>'
	);

	// Enviar email
	$sent = wp_mail( $to, $email_subject, $email_message, $headers );

	if ( $sent ) {
		wp_send_json_success( array( 'message' => 'Mensagem enviada com sucesso! Entraremos em contato em breve.' ) );
	} else {
		wp_send_json_error( array( 'message' => 'Erro ao enviar mensagem. Por favor, tente novamente ou entre em contato diretamente por e-mail.' ) );
	}
}
add_action( 'wp_ajax_sg_send_contact_form', 'sg_handle_contact_form' );
add_action( 'wp_ajax_nopriv_sg_send_contact_form', 'sg_handle_contact_form' );

/**
 * ============================================
 * PERSONALIZAÇÃO DO PAINEL ADMIN DO WORDPRESS
 * ============================================
 */

/**
 * Carregar estilos customizados do admin
 */
function sg_admin_styles() {
	wp_enqueue_style(
		'sg-admin-style',
		get_template_directory_uri() . '/css/admin-style.css',
		array(),
		SG_VERSION
	);
}
add_action( 'admin_enqueue_scripts', 'sg_admin_styles' );

/**
 * Carregar estilos customizados no login também
 */
function sg_admin_login_styles() {
	wp_enqueue_style(
		'sg-admin-login-style',
		get_template_directory_uri() . '/css/admin-style.css',
		array(),
		SG_VERSION
	);
	
	// Adicionar CSS para logo personalizado no login
	$sg_admin_logo_id = get_option( 'sg_admin_bar_logo_id', '' );
	$logo_url = '';
	
	if ( $sg_admin_logo_id ) {
		$logo_data = wp_get_attachment_image_src( $sg_admin_logo_id, 'full' );
		if ( $logo_data && ! empty( $logo_data[0] ) ) {
			$logo_url = $logo_data[0];
		}
	}
	
	// Se não houver logo nas configurações, verificar logo do Customizer
	if ( empty( $logo_url ) ) {
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		if ( $custom_logo_id ) {
			$logo_data = wp_get_attachment_image_src( $custom_logo_id, 'full' );
			if ( $logo_data && ! empty( $logo_data[0] ) ) {
				$logo_url = $logo_data[0];
			}
		}
	}
	
	if ( ! empty( $logo_url ) ) {
		?>
		<style type="text/css">
			.login h1 a {
				background-image: url('<?php echo esc_url( $logo_url ); ?>') !important;
				background-size: contain;
				background-repeat: no-repeat;
				background-position: center center;
				width: 200px;
				height: 80px;
				margin: 0 auto 25px;
				padding: 0;
				text-indent: -9999px;
			}
		</style>
		<?php
	}
}
add_action( 'login_enqueue_scripts', 'sg_admin_login_styles' );

/**
 * Personalizar URL do logo no login
 */
function sg_login_logo_url() {
	return home_url( '/' );
}
add_filter( 'login_headerurl', 'sg_login_logo_url' );

/**
 * Personalizar título do logo no login
 */
function sg_login_logo_url_title() {
	return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'sg_login_logo_url_title' );

/**
 * Remover widgets desnecessários do dashboard
 */
function sg_remove_dashboard_widgets() {
	// Remover widget de "Atividade" (comentários recentes)
	remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
	
	// Remover widget de "Notícias e eventos do WordPress"
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
	
	// Remover widget de "Links rápidos" (Quick Draft)
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	
	// Remover widget de "Boas-vindas" (Welcome Panel)
	remove_action( 'welcome_panel', 'wp_welcome_panel' );
	
	// Remover widget de "Status de diagnóstico" do WooCommerce (se existir)
	remove_meta_box( 'woocommerce_dashboard_status', 'dashboard', 'normal' );
	
	// Remover widget de "Site Health Status" (Status de saúde do site)
	remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
	remove_action( 'wp_dashboard_setup', 'wp_dashboard_site_health', 999 );
	
	// Remover widget de configuração do WooCommerce
	if ( class_exists( 'WooCommerce' ) ) {
		remove_meta_box( 'woocommerce_dashboard_setup', 'dashboard', 'normal' );
		remove_meta_box( 'woocommerce_dashboard_quick_setup', 'dashboard', 'normal' );
		remove_meta_box( 'wc_admin_dashboard_setup', 'dashboard', 'normal' );
		
		// Remover instância da classe WC_Admin_Dashboard_Setup se existir
		global $wp_filter;
		if ( isset( $wp_filter['wp_dashboard_setup'] ) ) {
			foreach ( $wp_filter['wp_dashboard_setup']->callbacks as $priority => $callbacks ) {
				foreach ( $callbacks as $key => $callback ) {
					if ( is_array( $callback['function'] ) && is_object( $callback['function'][0] ) ) {
						$class_name = get_class( $callback['function'][0] );
						if ( $class_name === 'WC_Admin_Dashboard_Setup' ) {
							remove_action( 'wp_dashboard_setup', $callback['function'], $priority );
						}
					}
				}
			}
		}
	}
	
	// Remover widget de "Rascunho rápido" se não necessário
	// remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
}
add_action( 'wp_dashboard_setup', 'sg_remove_dashboard_widgets' );

/**
 * Personalizar texto do rodapé do admin
 */
function sg_admin_footer_text() {
	$text = sprintf(
		/* translators: %s: Site name */
		__( 'Obrigado por criar com <a href="%s">WordPress</a> | Tema: <strong>SG Jurídico</strong>' ),
		__( 'https://wordpress.org/' )
	);
	return $text;
}
add_filter( 'admin_footer_text', 'sg_admin_footer_text' );

/**
 * Remover versão do WordPress do rodapé
 */
function sg_remove_footer_version() {
	return '';
}
add_filter( 'update_footer', 'sg_remove_footer_version', 11 );

/**
 * Remover notificações de atualização desnecessárias
 */
function sg_remove_update_notifications() {
	if ( ! current_user_can( 'update_core' ) ) {
		remove_action( 'admin_notices', 'update_nag', 3 );
	}
}
add_action( 'admin_head', 'sg_remove_update_notifications' );

/**
 * Personalizar título do admin
 */
function sg_admin_title( $admin_title, $title ) {
	return $title . ' &lsaquo; ' . get_bloginfo( 'name' ) . ' &mdash; WordPress';
}
add_filter( 'admin_title', 'sg_admin_title', 10, 2 );

/**
 * Remover banner de configuração do WooCommerce (se não necessário)
 */
function sg_remove_woocommerce_setup_notice() {
	if ( class_exists( 'WooCommerce' ) ) {
		// Remover aviso sobre conexão não segura (HTTPS) se não for crítico
		// Este aviso pode ser útil, então vamos apenas customizá-lo via CSS
	}
}
add_action( 'admin_init', 'sg_remove_woocommerce_setup_notice' );

/**
 * Ocultar widget de status do WooCommerce do dashboard
 */
function sg_remove_woocommerce_dashboard_widgets() {
	if ( class_exists( 'WooCommerce' ) ) {
		remove_meta_box( 'woocommerce_dashboard_status', 'dashboard', 'normal' );
		remove_meta_box( 'woocommerce_dashboard_recent_reviews', 'dashboard', 'normal' );
		remove_meta_box( 'woocommerce_dashboard_recent_orders', 'dashboard', 'normal' );
		
		// Remover widget de configuração do WooCommerce (múltiplos IDs possíveis)
		remove_meta_box( 'woocommerce_dashboard_setup', 'dashboard', 'normal' );
		remove_meta_box( 'woocommerce_dashboard_quick_setup', 'dashboard', 'normal' );
		remove_meta_box( 'wc_admin_dashboard_setup', 'dashboard', 'normal' );
		
		// Remover widget de "Status do diagnóstico" / "WooCommerce Status"
		remove_meta_box( 'woocommerce_dashboard_status', 'dashboard', 'side' );
		remove_meta_box( 'woocommerce_dashboard_status', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
		
		// Tentar remover via hook se existir
		if ( class_exists( 'WC_Admin_Dashboard' ) ) {
			remove_action( 'wp_dashboard_setup', array( 'WC_Admin_Dashboard', 'init' ), 10 );
		}
	}
}
add_action( 'wp_dashboard_setup', 'sg_remove_woocommerce_dashboard_widgets', 1 );

/**
 * Personalizar cores do admin via CSS inline para garantir prioridade
 */
function sg_admin_inline_styles() {
	?>
	<style type="text/css">
		/* Garantir que os estilos tenham prioridade */
		#wpadminbar {
			background: #484848 !important;
			border-bottom: 2px solid #5CE1E6 !important;
		}
		
		#adminmenu,
		#adminmenuback,
		#adminmenuwrap {
			background: #484848 !important;
		}
		
		#adminmenu li:hover,
		#adminmenu li.opensub > a.menu-top,
		#adminmenu li > a.menu-top:focus {
			background: #5CE1E6 !important;
			color: #000 !important;
		}
		
		/* Personalizar logo/branding na admin bar */
		#wpadminbar #wp-admin-bar-site-name .ab-item {
			color: #fff !important;
		}
		
		/* Ocultar badge "Ao vivo" do WooCommerce */
		#wpadminbar #wp-admin-bar-woocommerce-site-visibility-badge,
		#wpadminbar .woocommerce-site-status-badge-live,
		#wpadminbar li.woocommerce-site-status-badge-live,
		#wpadminbar li[id*="woocommerce-site-visibility"],
		#wpadminbar li[class*="woocommerce-site-status"] {
			display: none !important;
		}
		
		/* Ocultar widgets específicos desnecessários */
		.postbox#dashboard_primary,
		.postbox#dashboard_secondary,
		.postbox#dashboard_activity {
			display: none !important;
		}
		
		/* Ocultar banner de atualização do WordPress */
		.update-nag {
			display: none !important;
		}
		
		/* Personalizar botões primários */
		.button-primary,
		.wp-core-ui .button-primary {
			background: #5CE1E6 !important;
			border-color: #5CE1E6 !important;
			color: #000 !important;
			text-shadow: none !important;
			box-shadow: none !important;
		}
		
		.button-primary:hover,
		.wp-core-ui .button-primary:hover {
			background: #4BC4C8 !important;
			border-color: #4BC4C8 !important;
			color: #000 !important;
		}
	</style>
	<?php
}
add_action( 'admin_head', 'sg_admin_inline_styles', 999 );

/**
 * Ocultar widget de "Status de diagnóstico" do WooCommerce completamente
 */
function sg_hide_woocommerce_diagnostic_widget() {
	if ( class_exists( 'WooCommerce' ) ) {
		?>
		<style type="text/css">
			.postbox#woocommerce_dashboard_status {
				display: none !important;
			}
			
			/* Ocultar widget de configuração do WooCommerce */
			.postbox#woocommerce_dashboard_setup,
			.postbox#woocommerce_dashboard_quick_setup,
			.postbox#woocommerce_dashboard_setup_slider,
			.postbox#wc_admin_dashboard_setup,
			.postbox.widget-handle[data-id*="woocommerce_dashboard_setup"],
			.postbox.widget-handle[data-id*="woocommerce_dashboard_quick_setup"],
			.postbox.widget-handle[data-id*="wc_admin_dashboard_setup"],
			.postbox[id*="woocommerce"][id*="setup"],
			.postbox[id*="wc_admin"][id*="setup"] {
				display: none !important;
			}
			
			/* Ocultar widget "Status do diagnóstico" */
			.postbox#woocommerce_dashboard_status,
			.postbox#dashboard_site_health,
			.postbox[id*="diagnostic"],
			.postbox[id*="diagnóstico"],
			.postbox[id*="site_health"],
			.postbox[aria-label*="diagnóstico"],
			.postbox[aria-label*="diagnostic"],
			.postbox[aria-label*="WooCommerce Status"],
			.postbox[aria-label*="Status do WooCommerce"],
			.postbox[aria-label*="Site Health"],
			.postbox[aria-label*="Status de saúde"] {
				display: none !important;
			}
			
			
			/* Ocultar banner de HTTPS não seguro se quiser */
			/* .woocommerce-message.is-dismissible {
				display: none !important;
			} */
		</style>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			// Ocultar widget de configuração do WooCommerce por título
			$('.postbox').each(function() {
				var $postbox = $(this);
				var title = $postbox.find('h2, h3').text().trim();
				var id = $postbox.attr('id') || '';
				
				if (title.includes('WooCommerce Setup') || 
				    title.includes('Configuração do WooCommerce') ||
				    title.includes('Status do diagnóstico') ||
				    title.includes('Diagnostic Status') ||
				    title.includes('Diagnóstico') ||
				    title.includes('Site Health Status') ||
				    title.includes('Status de saúde') ||
				    $postbox.attr('id') && (
				    	$postbox.attr('id').includes('woocommerce_dashboard_setup') ||
				    	$postbox.attr('id').includes('wc_admin_dashboard_setup') ||
				    	$postbox.attr('id').includes('diagnostic') ||
				    	$postbox.attr('id').includes('diagnóstico') ||
				    	$postbox.attr('id').includes('site_health') ||
				    	$postbox.attr('id').includes('woocommerce_dashboard_status')
				    )) {
					$postbox.hide();
				}
			});
			
			// Observar mudanças no DOM para ocultar widgets adicionados dinamicamente
			var observer = new MutationObserver(function(mutations) {
				$('.postbox').each(function() {
					var $postbox = $(this);
					var title = $postbox.find('h2, h3').text().trim();
					var id = $postbox.attr('id') || '';
					
					if (title.includes('WooCommerce Setup') || 
					    title.includes('Configuração do WooCommerce') ||
					    title.includes('Status do diagnóstico') ||
					    title.includes('Diagnostic Status') ||
					    title.includes('Diagnóstico') ||
					    title.includes('Site Health Status') ||
					    title.includes('Status de saúde') ||
					    id.includes('woocommerce_dashboard_setup') ||
					    id.includes('wc_admin_dashboard_setup') ||
					    id.includes('diagnostic') ||
					    id.includes('diagnóstico') ||
					    id.includes('site_health') ||
					    id.includes('woocommerce_dashboard_status')) {
						$postbox.hide();
					}
				});
			});
			
			if (document.getElementById('dashboard-widgets')) {
				observer.observe(document.getElementById('dashboard-widgets'), {
					childList: true,
					subtree: true
				});
			}
		});
		</script>
		<?php
	}
}
add_action( 'admin_head', 'sg_hide_woocommerce_diagnostic_widget' );

/**
 * Personalizar mensagens e textos do admin
 */
function sg_customize_admin_texts( $translated_text, $text, $domain ) {
	if ( $domain === 'default' && is_admin() ) {
		switch ( $text ) {
			case 'Welcome to WordPress':
				$translated_text = 'Bem-vindo ao SG Jurídico';
				break;
			case 'Dashboard':
				$translated_text = 'Painel';
				break;
		}
	}
	return $translated_text;
}
add_filter( 'gettext', 'sg_customize_admin_texts', 20, 3 );

/**
 * Remover itens do menu admin que não são necessários
 */
function sg_remove_admin_menu_items() {
	// Remover "Ferramentas" se não necessário
	// remove_menu_page( 'tools.php' );
	
	// Remover "Comentários" se não necessário
	// remove_menu_page( 'edit-comments.php' );
	
	// Remover "Plugins" para usuários não-administradores
	if ( ! current_user_can( 'manage_options' ) ) {
		remove_menu_page( 'plugins.php' );
		remove_menu_page( 'themes.php' );
		remove_menu_page( 'tools.php' );
	}
}
add_action( 'admin_menu', 'sg_remove_admin_menu_items', 999 );

/**
 * Adicionar CSS para ocultar elementos desnecessários
 */
function sg_hide_unnecessary_admin_elements() {
	?>
	<style type="text/css">
		/* Ocultar widget de notícias do WordPress */
		#dashboard-widgets .postbox#dashboard_primary,
		#dashboard-widgets .postbox#dashboard_secondary {
			display: none !important;
		}
		
		/* Ocultar widget de atividade se não tiver comentários */
		.postbox#dashboard_activity {
			display: none !important;
		}
		
		/* Simplificar mensagens do WooCommerce */
		.woocommerce-message.is-dismissible .notice-dismiss {
			top: 0;
		}
		
		/* Ocultar avisos de configuração se já estiver configurado */
		.woocommerce-message.woocommerce-tracker,
		.wc-connect-notice {
			/* Mantém visível, apenas personaliza */
		}
	</style>
	<?php
}
add_action( 'admin_head', 'sg_hide_unnecessary_admin_elements' );

/**
 * Limpar widgets padrão do dashboard que são desnecessários
 */
function sg_clean_dashboard() {
	global $wp_meta_boxes;
	
	// Remover widgets padrão
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary'] );
	
		// Remover widgets do WooCommerce
		if ( class_exists( 'WooCommerce' ) ) {
			unset( $wp_meta_boxes['dashboard']['normal']['woocommerce']['woocommerce_dashboard_status'] );
			unset( $wp_meta_boxes['dashboard']['normal']['woocommerce']['woocommerce_dashboard_setup'] );
			unset( $wp_meta_boxes['dashboard']['normal']['woocommerce']['woocommerce_dashboard_quick_setup'] );
			unset( $wp_meta_boxes['dashboard']['normal']['woocommerce']['woocommerce_dashboard_recent_orders'] );
			unset( $wp_meta_boxes['dashboard']['normal']['woocommerce']['woocommerce_dashboard_recent_reviews'] );
			unset( $wp_meta_boxes['dashboard']['normal']['woocommerce']['wc_admin_dashboard_setup'] );
			
			// Remover de todas as prioridades possíveis
			if ( isset( $wp_meta_boxes['dashboard']['normal'] ) ) {
				foreach ( $wp_meta_boxes['dashboard']['normal'] as $context => $widgets ) {
					if ( is_array( $widgets ) ) {
						foreach ( $widgets as $id => $widget ) {
							if ( strpos( $id, 'woocommerce_dashboard_setup' ) !== false || 
							     strpos( $id, 'wc_admin_dashboard_setup' ) !== false ||
							     strpos( $id, 'woocommerce_dashboard_quick_setup' ) !== false ||
							     strpos( $id, 'diagnostic' ) !== false ||
							     strpos( $id, 'diagnóstico' ) !== false ) {
								unset( $wp_meta_boxes['dashboard']['normal'][ $context ][ $id ] );
							}
						}
					}
				}
			}
			
			// Remover também da sidebar se existir
			if ( isset( $wp_meta_boxes['dashboard']['side'] ) ) {
				foreach ( $wp_meta_boxes['dashboard']['side'] as $context => $widgets ) {
					if ( is_array( $widgets ) ) {
						foreach ( $widgets as $id => $widget ) {
							if ( strpos( $id, 'diagnostic' ) !== false || 
							     strpos( $id, 'diagnóstico' ) !== false ||
							     strpos( $id, 'woocommerce_dashboard_status' ) !== false ) {
								unset( $wp_meta_boxes['dashboard']['side'][ $context ][ $id ] );
							}
						}
					}
				}
			}
		}
}
add_action( 'wp_dashboard_setup', 'sg_clean_dashboard', 999 );

/**
 * Remover ícone do WordPress da admin bar
 */
function sg_remove_wp_logo_from_admin_bar( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'wp-logo' );
	
	// Remover badge "Ao vivo" do WooCommerce
	$wp_admin_bar->remove_node( 'woocommerce-site-visibility-badge' );
}
add_action( 'admin_bar_menu', 'sg_remove_wp_logo_from_admin_bar', 999 );

/**
 * Remover hook do WooCommerce que adiciona badge "Ao vivo"
 */
function sg_remove_woocommerce_live_badge_hook() {
	if ( class_exists( 'WooCommerce' ) ) {
		// Tentar remover hook da classe ComingSoonAdminBarBadge
		remove_action( 'admin_bar_menu', array( 'Automattic\WooCommerce\Internal\ComingSoon\ComingSoonAdminBarBadge', 'site_visibility_badge' ), 31 );
		
		// Remover via filtro de feature se possível
		add_filter( 'woocommerce_get_feature_config', function( $features ) {
			if ( isset( $features['site_visibility_badge'] ) ) {
				$features['site_visibility_badge'] = false;
			}
			return $features;
		}, 999 );
		
		// Desabilitar feature diretamente
		if ( class_exists( 'Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			// Tentar desabilitar via opção
			update_option( 'woocommerce_feature_site_visibility_badge_enabled', 'no' );
		}
	}
}
add_action( 'init', 'sg_remove_woocommerce_live_badge_hook', 5 );
add_action( 'admin_init', 'sg_remove_woocommerce_live_badge_hook', 5 );

/**
 * Remover badge "Ao vivo" diretamente no hook admin_bar_menu (antes do WooCommerce)
 */
function sg_remove_live_badge_from_admin_bar_early( $wp_admin_bar ) {
	if ( class_exists( 'WooCommerce' ) ) {
		$wp_admin_bar->remove_node( 'woocommerce-site-visibility-badge' );
	}
}
add_action( 'admin_bar_menu', 'sg_remove_live_badge_from_admin_bar_early', 30 );

/**
 * Remover badge "Ao vivo" após WooCommerce adicionar (backup)
 */
function sg_remove_live_badge_from_admin_bar_late( $wp_admin_bar ) {
	if ( class_exists( 'WooCommerce' ) ) {
		$wp_admin_bar->remove_node( 'woocommerce-site-visibility-badge' );
	}
}
add_action( 'admin_bar_menu', 'sg_remove_live_badge_from_admin_bar_late', 32 );

/**
 * Substituir logo do WordPress na admin bar pelo logo do tema
 */
function sg_replace_admin_bar_logo( $wp_admin_bar ) {
	// Logo padrão do SG Jurídico
	$logo_url = '';
	
	// PRIORIDADE 1: Verificar se há logo nas Configurações Gerais do SG Jurídico
	$sg_admin_logo_id = get_option( 'sg_admin_bar_logo_id', '' );
	if ( $sg_admin_logo_id ) {
		$logo = wp_get_attachment_image_src( $sg_admin_logo_id, 'full' );
		if ( $logo && ! empty( $logo[0] ) ) {
			$logo_url = $logo[0];
		}
	}
	
	// PRIORIDADE 2: Verificar se há logo customizado no Customizer
	if ( empty( $logo_url ) ) {
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		if ( $custom_logo_id ) {
			$logo = wp_get_attachment_image_src( $custom_logo_id, 'full' );
			if ( $logo && ! empty( $logo[0] ) ) {
				$logo_url = $logo[0];
			}
		}
	}
	
	// PRIORIDADE 3: Se não houver logo nas configurações, usar o logo padrão
	if ( empty( $logo_url ) ) {
		// Tentar diferentes caminhos possíveis
		$possible_paths = array(
			WP_CONTENT_DIR . '/uploads/2023/09/cropped-Santo-Graal-Juridico-1.png',
			ABSPATH . 'wp-content/uploads/2023/09/cropped-Santo-Graal-Juridico-1.png',
		);
		
		$found_path = false;
		foreach ( $possible_paths as $logo_path ) {
			if ( file_exists( $logo_path ) ) {
				$found_path = true;
				break;
			}
		}
		
		if ( $found_path ) {
			$logo_url = content_url( '/uploads/2023/09/cropped-Santo-Graal-Juridico-1.png' );
		}
	}
	
	// Se temos um logo, usar ele
	if ( ! empty( $logo_url ) ) {
		// Remover o nó padrão do site
		$wp_admin_bar->remove_node( 'site-name' );
		
		// Adicionar novo nó com o logo na mesma posição do wp-logo
		$wp_admin_bar->add_node( array(
			'id'    => 'site-name-logo',
			'title' => '<img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" class="sg-admin-logo" />',
			'href'  => home_url( '/' ),
			'meta'  => array(
				'title' => get_bloginfo( 'name' ),
			),
			'parent' => false,
			'group'  => false,
		) );
	} else {
		// Se não houver logo, personalizar apenas o texto
		$node = $wp_admin_bar->get_node( 'site-name' );
		if ( $node ) {
			$wp_admin_bar->remove_node( 'site-name' );
			$wp_admin_bar->add_node( array(
				'id'    => 'site-name',
				'title' => '<span class="sg-admin-site-name">' . esc_html( get_bloginfo( 'name' ) ) . '</span>',
				'href'  => home_url( '/' ),
				'meta'  => array(
					'title' => get_bloginfo( 'name' ),
				),
			) );
		}
	}
}
add_action( 'admin_bar_menu', 'sg_replace_admin_bar_logo', 50 );

/**
 * Adicionar CSS para o logo na admin bar
 */
function sg_admin_bar_logo_styles() {
	$has_logo = false;
	
	// Verificar se há logo nas Configurações Gerais do SG Jurídico
	$sg_admin_logo_id = get_option( 'sg_admin_bar_logo_id', '' );
	if ( $sg_admin_logo_id ) {
		$logo = wp_get_attachment_image_src( $sg_admin_logo_id, 'full' );
		if ( $logo && ! empty( $logo[0] ) ) {
			$has_logo = true;
		}
	}
	
	// Se não houver, verificar logo do Customizer
	if ( ! $has_logo ) {
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		if ( $custom_logo_id ) {
			$logo = wp_get_attachment_image_src( $custom_logo_id, 'full' );
			if ( $logo && ! empty( $logo[0] ) ) {
				$has_logo = true;
			}
		}
	}
	
	// Se não houver logo do Customizer, verificar se o logo padrão existe
	if ( ! $has_logo ) {
		$possible_paths = array(
			WP_CONTENT_DIR . '/uploads/2023/09/cropped-Santo-Graal-Juridico-1.png',
			ABSPATH . 'wp-content/uploads/2023/09/cropped-Santo-Graal-Juridico-1.png',
		);
		
		foreach ( $possible_paths as $logo_path ) {
			if ( file_exists( $logo_path ) ) {
				$has_logo = true;
				break;
			}
		}
	}
	
	if ( $has_logo ) {
		?>
		<style type="text/css">
			/* Ocultar ícone do WordPress completamente */
			#wpadminbar #wp-admin-bar-site-name > .ab-item:before,
			#wpadminbar #wp-admin-bar-site-name > .ab-item .ab-icon,
			#wpadminbar #wp-admin-bar-site-name > .ab-item .ab-icon:before {
				display: none !important;
				content: none !important;
			}
			
			/* Estilizar logo na admin bar */
			#wpadminbar #wp-admin-bar-site-name-logo .sg-admin-logo {
				max-height: 20px !important;
				width: auto !important;
				height: auto !important;
				vertical-align: middle !important;
				margin-right: 0 !important;
				display: block !important;
			}
			
			#wpadminbar #wp-admin-bar-site-name-logo > .ab-item {
				padding: 6px 12px !important;
				display: flex !important;
				align-items: center !important;
				justify-content: center !important;
				height: 32px !important;
				line-height: 1 !important;
			}
			
			#wpadminbar #wp-admin-bar-site-name-logo > .ab-item:before {
				display: none !important;
				content: none !important;
			}
			
			#wpadminbar #wp-admin-bar-site-name-logo:hover > .ab-item {
				background: #5CE1E6 !important;
			}
			
			/* Ocultar ícone padrão do WordPress se existir */
			#wpadminbar #wp-admin-bar-site-name-logo .ab-icon,
			#wpadminbar #wp-admin-bar-site-name-logo .ab-icon:before {
				display: none !important;
			}
		</style>
		<?php
	} else {
		// Mesmo sem logo, remover o ícone do WordPress
		?>
		<style type="text/css">
			#wpadminbar #wp-admin-bar-site-name > .ab-item:before {
				display: none !important;
				content: none !important;
			}
		</style>
		<?php
	}
}
add_action( 'admin_head', 'sg_admin_bar_logo_styles' );
add_action( 'wp_head', 'sg_admin_bar_logo_styles' );

/**
 * Remover badge "Ao vivo" do WooCommerce via JavaScript
 */
function sg_hide_woocommerce_live_badge() {
	?>
	<script type="text/javascript">
	(function() {
		function hideLiveBadge() {
			// Remover badge "Ao vivo" por ID
			var badge = document.getElementById('wp-admin-bar-woocommerce-site-visibility-badge');
			if (badge) {
				badge.style.display = 'none';
				badge.remove();
			}
			
			// Remover por classe
			var badges = document.querySelectorAll('.woocommerce-site-status-badge-live, li[class*="woocommerce-site-status"]');
			badges.forEach(function(badge) {
				badge.style.display = 'none';
				badge.remove();
			});
			
			// Remover qualquer elemento com texto "Ao vivo" na admin bar
			var allItems = document.querySelectorAll('#wpadminbar .ab-item');
			allItems.forEach(function(item) {
				if (item.textContent.trim() === 'Ao vivo' || item.textContent.trim().includes('Ao vivo')) {
					var parent = item.closest('li');
					if (parent && (parent.id.includes('woocommerce-site-visibility') || parent.classList.contains('woocommerce-site-status-badge-live'))) {
						parent.style.display = 'none';
						parent.remove();
					}
				}
			});
		}
		
		// Executar imediatamente
		hideLiveBadge();
		
		// Executar quando DOM estiver pronto
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', hideLiveBadge);
		}
		
		// Observar mudanças na admin bar
		var adminBar = document.getElementById('wpadminbar');
		if (adminBar) {
			var observer = new MutationObserver(function(mutations) {
				hideLiveBadge();
			});
			
			observer.observe(adminBar, {
				childList: true,
				subtree: true
			});
			
			// Executar após um pequeno delay para garantir
			setTimeout(hideLiveBadge, 500);
			setTimeout(hideLiveBadge, 1000);
		}
	})();
	</script>
	<?php
}
add_action( 'admin_head', 'sg_hide_woocommerce_live_badge', 999 );
add_action( 'wp_head', 'sg_hide_woocommerce_live_badge', 999 );

/**
 * ============================================
 * WIDGET: CONFIGURAÇÕES RÁPIDAS DO SITE
 * ============================================
 */

/**
 * Widget customizado: Configurações Rápidas
 * Permite acesso rápido a todas as configurações do site
 */
class SG_Quick_Settings_Widget {
	
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
	}
	
	/**
	 * Adicionar widget ao dashboard
	 */
	public function add_dashboard_widget() {
		wp_add_dashboard_widget(
			'sg_quick_settings',
			'⚙️ Configurações Rápidas - SG Jurídico',
			array( $this, 'render_widget' )
		);
	}
	
	/**
	 * Renderizar conteúdo do widget
	 */
	public function render_widget() {
		?>
		<div class="sg-quick-settings-widget">
			<style>
				.sg-quick-settings-widget {
					padding: 0;
				}
				.sg-settings-section {
					margin-bottom: 20px;
					padding-bottom: 15px;
					border-bottom: 1px solid #e0e0e0;
				}
				.sg-settings-section:last-child {
					border-bottom: none;
					margin-bottom: 0;
					padding-bottom: 0;
				}
				.sg-settings-title {
					font-size: 15px;
					font-weight: 700;
					color: #000000;
					margin: 0 0 14px 0;
					display: flex;
					align-items: center;
					gap: 8px;
					letter-spacing: -0.2px;
				}
				.sg-settings-title svg {
					width: 18px;
					height: 18px;
					color: #5CE1E6;
					stroke-width: 2.5;
				}
				.sg-settings-links {
					display: grid;
					grid-template-columns: repeat(2, 1fr);
					gap: 10px;
				}
				.sg-settings-link {
					display: flex;
					align-items: center;
					gap: 10px;
					padding: 10px 14px;
					background: #ffffff;
					border: 1px solid #666666;
					border-radius: 6px;
					text-decoration: none;
					color: #000000 !important;
					font-size: 14px;
					font-weight: 500;
					line-height: 1.4;
					transition: all 0.2s ease;
					min-height: 44px;
				}
				.sg-settings-link *:not(.status-badge) {
					color: #000000 !important;
				}
				.sg-settings-link:focus {
					outline: 2px solid #5CE1E6;
					outline-offset: 2px;
					color: #000000 !important;
				}
				.sg-settings-link:focus *:not(.status-badge) {
					color: #000000 !important;
				}
				.sg-settings-link:hover {
					background: #5CE1E6;
					color: #000000 !important;
					border-color: #5CE1E6;
					transform: translateY(-1px);
					box-shadow: 0 3px 6px rgba(0,0,0,0.12);
				}
				.sg-settings-link:hover *:not(.status-badge) {
					color: #000000 !important;
				}
				.sg-settings-link:active {
					transform: translateY(0);
					box-shadow: 0 1px 3px rgba(0,0,0,0.1);
					color: #000000 !important;
				}
				.sg-settings-link:active *:not(.status-badge) {
					color: #000000 !important;
				}
				.sg-settings-link svg {
					width: 18px;
					height: 18px;
					flex-shrink: 0;
					stroke: #000000 !important;
					stroke-width: 2;
					fill: none;
				}
				.sg-settings-link:hover svg {
					stroke: #000000 !important;
				}
				.sg-settings-link .status-badge {
					margin-left: auto;
					font-size: 11px;
					font-weight: 600;
					padding: 4px 8px;
					border-radius: 4px;
					background: #2e7d32;
					color: #ffffff;
					letter-spacing: 0.3px;
					white-space: nowrap;
				}
				.sg-settings-link .status-badge.missing {
					background: #f57c00;
					color: #ffffff;
				}
				/* Melhor contraste para textos em diferentes estados */
				.sg-settings-link:visited {
					color: #000000 !important;
				}
				.sg-settings-link:visited *:not(.status-badge) {
					color: #000000 !important;
				}
				/* Garantir que todos os textos dentro dos botões sejam pretos (exceto badges) */
				.sg-settings-link span:not(.status-badge),
				.sg-settings-link div:not(.status-badge),
				.sg-settings-link text {
					color: #000000 !important;
				}
				/* Melhorar legibilidade em telas menores */
				@media (max-width: 1200px) {
					.sg-settings-links {
						grid-template-columns: 1fr;
					}
					.sg-settings-link {
						font-size: 14px;
						padding: 12px 14px;
					}
				}
				/* Aumentar área de toque para mobile */
				@media (max-width: 782px) {
					.sg-settings-link {
						min-height: 48px;
						padding: 12px 16px;
					}
					.sg-settings-title {
						font-size: 16px;
					}
				}
			</style>
			
			<!-- Header -->
			<div class="sg-settings-section">
				<h3 class="sg-settings-title">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<rect x="3" y="3" width="18" height="18" rx="2"/>
						<path d="M9 3v18M3 9h18"/>
					</svg>
					Header
				</h3>
				<div class="sg-settings-links">
					<?php
					$custom_logo_id = get_theme_mod( 'custom_logo' );
					$logo_status = $custom_logo_id ? '✓ Criado' : '✗ Não configurado';
					$logo_status_class = $custom_logo_id ? '' : 'missing';
					?>
					<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[control]=custom_logo' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<rect x="3" y="3" width="18" height="18" rx="2"/>
							<circle cx="8.5" cy="8.5" r="1.5"/>
							<polyline points="21 15 16 10 5 21"/>
						</svg>
						Logo do Site
						<span class="status-badge <?php echo esc_attr( $logo_status_class ); ?>"><?php echo esc_html( $logo_status ); ?></span>
					</a>
					
					<a href="<?php echo esc_url( admin_url( 'nav-menus.php?action=edit&menu=0' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<line x1="8" y1="6" x2="21" y2="6"/>
							<line x1="8" y1="12" x2="21" y2="12"/>
							<line x1="8" y1="18" x2="21" y2="18"/>
							<line x1="3" y1="6" x2="3.01" y2="6"/>
							<line x1="3" y1="12" x2="3.01" y2="12"/>
							<line x1="3" y1="18" x2="3.01" y2="18"/>
						</svg>
						Menu Principal
					</a>
					
					<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=nav_menus' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<circle cx="12" cy="12" r="10"/>
							<path d="M12 6v6l4 2"/>
						</svg>
						Personalizar Menu
					</a>
					
					<a href="<?php echo esc_url( admin_url( 'widgets.php' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<rect x="3" y="3" width="7" height="7"/>
							<rect x="14" y="3" width="7" height="7"/>
							<rect x="14" y="14" width="7" height="7"/>
							<rect x="3" y="14" width="7" height="7"/>
						</svg>
						Widgets do Header
					</a>
				</div>
			</div>
			
			<!-- Footer -->
			<div class="sg-settings-section">
				<h3 class="sg-settings-title">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<polyline points="22 6 13.5 15.5 8.5 10.5 2 17"/>
						<polyline points="16 6 22 6 22 12"/>
					</svg>
					Footer
				</h3>
				<div class="sg-settings-links">
					<a href="<?php echo esc_url( admin_url( 'widgets.php#footer-1' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<rect x="3" y="3" width="7" height="7"/>
							<rect x="14" y="3" width="7" height="7"/>
							<rect x="14" y="14" width="7" height="7"/>
							<rect x="3" y="14" width="7" height="7"/>
						</svg>
						Widgets do Footer (4 colunas)
					</a>
					
					<a href="<?php echo esc_url( admin_url( 'nav-menus.php?action=locations' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<line x1="8" y1="6" x2="21" y2="6"/>
							<line x1="8" y1="12" x2="21" y2="12"/>
							<line x1="8" y1="18" x2="21" y2="18"/>
							<line x1="3" y1="6" x2="3.01" y2="6"/>
							<line x1="3" y1="12" x2="3.01" y2="12"/>
							<line x1="3" y1="18" x2="3.01" y2="18"/>
						</svg>
						Menu do Footer
					</a>
					
					<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=title_tagline' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
							<polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
							<line x1="12" y1="22.08" x2="12" y2="12"/>
						</svg>
						Informações da Empresa
					</a>
				</div>
			</div>
			
			<!-- Páginas Institucionais -->
			<div class="sg-settings-section">
				<h3 class="sg-settings-title">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
						<polyline points="14 2 14 8 20 8"/>
						<line x1="16" y1="13" x2="8" y2="13"/>
						<line x1="16" y1="17" x2="8" y2="17"/>
						<polyline points="10 9 9 9 8 9"/>
					</svg>
					Páginas Institucionais
				</h3>
				<div class="sg-settings-links">
					<?php
					// Sobre
					$sobre_page = get_page_by_path( 'sobre' );
					$sobre_url = $sobre_page ? get_edit_post_link( $sobre_page->ID ) : admin_url( 'post-new.php?post_type=page&title=Sobre&template=page-sobre.php' );
					$sobre_status = $sobre_page ? '✓ Criada' : '✗ Criar';
					$sobre_status_class = $sobre_page ? '' : 'missing';
					?>
					<a href="<?php echo esc_url( $sobre_url ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<circle cx="12" cy="12" r="10"/>
							<path d="M12 16v-4M12 8h.01"/>
						</svg>
						Sobre Nós
						<span class="status-badge <?php echo esc_attr( $sobre_status_class ); ?>"><?php echo esc_html( $sobre_status ); ?></span>
					</a>
					
					<?php
					// Contato
					$contato_page = get_page_by_path( 'contato' );
					$contato_url = $contato_page ? get_edit_post_link( $contato_page->ID ) : admin_url( 'post-new.php?post_type=page&title=Contato&template=page-contato.php' );
					$contato_status = $contato_page ? '✓ Criada' : '✗ Criar';
					$contato_status_class = $contato_page ? '' : 'missing';
					?>
					<a href="<?php echo esc_url( $contato_url ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
							<polyline points="22,6 12,13 2,6"/>
						</svg>
						Contato
						<span class="status-badge <?php echo esc_attr( $contato_status_class ); ?>"><?php echo esc_html( $contato_status ); ?></span>
					</a>
					
					<?php
					// Política de Privacidade
					$privacy_page_id = get_option( 'wp_page_for_privacy_policy' );
					$privacy_url = $privacy_page_id ? get_edit_post_link( $privacy_page_id ) : admin_url( 'options-privacy.php' );
					$privacy_status = $privacy_page_id ? '✓ Criada' : '✗ Criar';
					$privacy_status_class = $privacy_page_id ? '' : 'missing';
					?>
					<a href="<?php echo esc_url( $privacy_url ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
						</svg>
						Política de Privacidade
						<span class="status-badge <?php echo esc_attr( $privacy_status_class ); ?>"><?php echo esc_html( $privacy_status ); ?></span>
					</a>
					
					<?php
					// Termos de Uso
					$terms_page = get_page_by_path( 'termos-de-uso' );
					$terms_url = $terms_page ? get_edit_post_link( $terms_page->ID ) : admin_url( 'post-new.php?post_type=page&title=Termos+de+Uso' );
					$terms_status = $terms_page ? '✓ Criada' : '✗ Criar';
					$terms_status_class = $terms_page ? '' : 'missing';
					?>
					<a href="<?php echo esc_url( $terms_url ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
							<polyline points="14 2 14 8 20 8"/>
							<line x1="16" y1="13" x2="8" y2="13"/>
							<line x1="16" y1="17" x2="8" y2="17"/>
							<polyline points="10 9 9 9 8 9"/>
						</svg>
						Termos de Uso
						<span class="status-badge <?php echo esc_attr( $terms_status_class ); ?>"><?php echo esc_html( $terms_status ); ?></span>
					</a>
				</div>
			</div>
			
			<!-- Ações Rápidas -->
			<div class="sg-settings-section">
				<h3 class="sg-settings-title">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<circle cx="12" cy="12" r="10"/>
						<polyline points="12 6 12 12 16 14"/>
					</svg>
					Ações Rápidas
				</h3>
				<div class="sg-settings-links">
					<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=page' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<line x1="12" y1="5" x2="12" y2="19"/>
							<line x1="5" y1="12" x2="19" y2="12"/>
						</svg>
						Criar Nova Página
					</a>
					
					<a href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<line x1="12" y1="5" x2="12" y2="19"/>
							<line x1="5" y1="12" x2="19" y2="12"/>
						</svg>
						Criar Novo Post
					</a>
					
					<?php if ( class_exists( 'WooCommerce' ) ) : ?>
						<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=product' ) ); ?>" class="sg-settings-link">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<line x1="12" y1="5" x2="12" y2="19"/>
								<line x1="5" y1="12" x2="19" y2="12"/>
							</svg>
							Adicionar Produto
						</a>
					<?php endif; ?>
					
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
							<polyline points="15 3 21 3 21 9"/>
							<line x1="10" y1="14" x2="21" y2="3"/>
						</svg>
						Ver Site
					</a>
				</div>
			</div>
			
			<!-- Seção: Configurações Gerais -->
			<div class="sg-settings-section">
				<h3 class="sg-settings-title">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<circle cx="12" cy="12" r="3"/>
						<path d="M12 1v6m0 6v6M5.64 5.64l4.24 4.24m4.24 4.24l4.24 4.24M1 12h6m6 0h6M5.64 18.36l4.24-4.24m4.24-4.24l4.24-4.24"/>
					</svg>
					Configurações Gerais
				</h3>
				<div class="sg-settings-links">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=sg-juridico-settings' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
							<circle cx="9" cy="9" r="2"/>
							<path d="M21 15l-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
						</svg>
						Logo Painel
					</a>
					
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=sg-juridico-settings#sg_home_banner_images' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
							<rect x="7" y="7" width="10" height="10"/>
						</svg>
						Imagens do Banner
					</a>
				</div>
			</div>
		</div>
		<?php
	}
}

// Inicializar widget
new SG_Quick_Settings_Widget();

/**
 * ============================================
 * WIDGET: GERENCIAMENTO DE CALENDÁRIO DE EVENTOS
 * ============================================
 */

/**
 * Widget customizado: Gerenciamento de Calendário de Eventos
 * Permite acesso rápido a todas as funcionalidades de eventos
 */
class SG_Calendar_Events_Widget {
	
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
	}
	
	/**
	 * Adicionar widget ao dashboard
	 */
	public function add_dashboard_widget() {
		wp_add_dashboard_widget(
			'sg_calendar_events',
			'📅 Gerenciamento de Calendário de Eventos',
			array( $this, 'render_widget' )
		);
	}
	
	/**
	 * Contar eventos por tipo
	 */
	private function count_events() {
		$counts = array(
			'sg_eventos' => 0,
			'etn' => 0,
			'tribe_events' => 0,
			'total' => 0,
			'upcoming' => 0,
			'past' => 0
		);
		
		global $wpdb;
		$today = current_time( 'Y-m-d' );
		
		// PRIMEIRO: Contar eventos próprios (sg_eventos)
		if ( post_type_exists( 'sg_eventos' ) ) {
			$sg_count = wp_count_posts( 'sg_eventos' );
			$counts['sg_eventos'] = isset( $sg_count->publish ) ? (int) $sg_count->publish : 0;
			$counts['total'] += $counts['sg_eventos'];
			
			// Contar eventos futuros sg_eventos
			if ( $counts['sg_eventos'] > 0 ) {
				$sg_future = $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(DISTINCT p.ID) 
					FROM {$wpdb->posts} p
					INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
					WHERE p.post_type = 'sg_eventos' 
					AND p.post_status = 'publish'
					AND pm.meta_key = '_sg_evento_data_inicio'
					AND pm.meta_value >= %s
					AND pm.meta_value != ''",
					$today
				) );
				$counts['upcoming'] += (int) $sg_future;
			}
		}
		
		// Contar eventos ETN - VERIFICAR SE EXISTE NA TABELA
		// Primeiro verificar se o tipo existe na tabella wp_posts
		$etn_exists = $wpdb->get_var(
			"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'etn' LIMIT 1"
		);
		
		if ( $etn_exists > 0 || post_type_exists( 'etn' ) ) {
			// Contar total de eventos ETN publicados usando SQL direto
			$etn_total = $wpdb->get_var(
				"SELECT COUNT(DISTINCT p.ID) 
				FROM {$wpdb->posts} p
				WHERE p.post_type = 'etn' 
				AND p.post_status = 'publish'"
			);
			$counts['etn'] = (int) $etn_total;
			$counts['total'] += $counts['etn'];
			
			// Contar eventos ETN futuros
			if ( $counts['etn'] > 0 ) {
				// Buscar eventos com data
				$etn_future = $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(DISTINCT p.ID) 
					FROM {$wpdb->posts} p
					INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
					WHERE p.post_type = 'etn' 
					AND p.post_status = 'publish'
					AND pm.meta_key = 'etn_start_date'
					AND pm.meta_value >= %s
					AND pm.meta_value != ''
					AND pm.meta_value IS NOT NULL",
					$today
				) );
				
				// Se retornou null ou false, tentar sem filtro de data
				if ( $etn_future === null || $etn_future === false ) {
					$etn_future = 0;
				}
				
				$counts['upcoming'] += (int) $etn_future;
			}
		}
		
		// Contar eventos The Events Calendar (compatibilidade)
		if ( post_type_exists( 'tribe_events' ) ) {
			$tribe_count = wp_count_posts( 'tribe_events' );
			$counts['tribe_events'] = isset( $tribe_count->publish ) ? (int) $tribe_count->publish : 0;
			$counts['total'] += $counts['tribe_events'];
			
			// Contar eventos futuros Tribe
			if ( $counts['tribe_events'] > 0 ) {
				$tribe_future = $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(DISTINCT p.ID) 
					FROM {$wpdb->posts} p
					INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
					WHERE p.post_type = 'tribe_events' 
					AND p.post_status = 'publish'
					AND pm.meta_key = '_EventStartDate'
					AND pm.meta_value >= %s
					AND pm.meta_value != ''",
					$today
				) );
				
				// Tribe Events pode ter formato de data diferente (timestamp ou datetime)
				if ( $tribe_future === null || $tribe_future === false ) {
					$tribe_future = $wpdb->get_var( $wpdb->prepare(
						"SELECT COUNT(DISTINCT p.ID) 
						FROM {$wpdb->posts} p
						INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
						WHERE p.post_type = 'tribe_events' 
						AND p.post_status = 'publish'
						AND pm.meta_key = '_EventStartDate'
						AND (CAST(pm.meta_value AS DATE) >= %s OR CAST(pm.meta_value AS UNSIGNED) >= %d)",
						$today,
						strtotime( $today )
					) );
				}
				
				$counts['upcoming'] += (int) $tribe_future;
			}
		}
		
		// Calcular eventos passados
		$counts['past'] = max( 0, $counts['total'] - $counts['upcoming'] );
		
		return $counts;
	}
	
	/**
	 * Renderizar conteúdo do widget
	 */
	public function render_widget() {
		$counts = $this->count_events();
		$has_sg_eventos = post_type_exists( 'sg_eventos' );
		$has_etn = post_type_exists( 'etn' );
		$has_tribe = post_type_exists( 'tribe_events' );
		
		// DEBUG: Verificar valores (remover após resolver)
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && isset( $_GET['sg_debug'] ) ) {
			error_log( 'SG Eventos Debug - Total: ' . $counts['total'] . ', ETN: ' . $counts['etn'] . ', SG: ' . $counts['sg_eventos'] );
		}
		
		// Mostrar mensagem de importação se houver
		if ( isset( $_GET['sg_import'] ) ) {
			if ( $_GET['sg_import'] === 'success' ) {
				$imported = isset( $_GET['imported'] ) ? intval( $_GET['imported'] ) : 0;
				$skipped = isset( $_GET['skipped'] ) ? intval( $_GET['skipped'] ) : 0;
				echo '<div class="notice notice-success is-dismissible" style="margin: 0 0 15px 0;"><p>';
				printf( 
					__( 'Importação concluída! %d eventos importados, %d ignorados (já existentes).', 'sg-juridico' ),
					$imported,
					$skipped
				);
				echo '</p></div>';
			} elseif ( $_GET['sg_import'] === 'error' ) {
				echo '<div class="notice notice-error is-dismissible" style="margin: 0 0 15px 0;"><p>';
				_e( 'Erro na importação. Verifique se os tipos de post estão disponíveis.', 'sg-juridico' );
				echo '</p></div>';
			}
		}
		?>
		<div class="sg-calendar-events-widget">
			<style>
				.sg-calendar-events-widget {
					padding: 0;
				}
				.sg-events-section {
					margin-bottom: 20px;
					padding-bottom: 15px;
					border-bottom: 1px solid #e0e0e0;
				}
				.sg-events-section:last-child {
					border-bottom: none;
					margin-bottom: 0;
					padding-bottom: 0;
				}
				.sg-events-title {
					font-size: 15px;
					font-weight: 700;
					color: #000000;
					margin: 0 0 14px 0;
					display: flex;
					align-items: center;
					gap: 8px;
					letter-spacing: -0.2px;
				}
				.sg-events-title svg {
					width: 18px;
					height: 18px;
					color: #5CE1E6;
					stroke-width: 2.5;
				}
				.sg-events-stats {
					display: grid;
					grid-template-columns: repeat(2, 1fr);
					gap: 10px;
					margin-bottom: 15px;
				}
				.sg-stat-card {
					background: #f8f9fa;
					border: 1px solid #e0e0e0;
					border-radius: 6px;
					padding: 12px;
					text-align: center;
				}
				.sg-stat-number {
					font-size: 24px;
					font-weight: 700;
					color: #5CE1E6;
					display: block;
					margin-bottom: 4px;
				}
				.sg-stat-label {
					font-size: 12px;
					color: #666;
					text-transform: uppercase;
					letter-spacing: 0.5px;
				}
				.sg-events-links {
					display: grid;
					grid-template-columns: repeat(2, 1fr);
					gap: 10px;
				}
				.sg-events-link {
					display: flex;
					align-items: center;
					gap: 10px;
					padding: 10px 14px;
					background: #ffffff;
					border: 1px solid #666666;
					border-radius: 6px;
					text-decoration: none;
					color: #000000 !important;
					font-size: 14px;
					font-weight: 500;
					line-height: 1.4;
					transition: all 0.2s ease;
					min-height: 44px;
				}
				.sg-events-link *:not(.status-badge) {
					color: #000000 !important;
				}
				.sg-events-link:focus {
					outline: 2px solid #5CE1E6;
					outline-offset: 2px;
					color: #000000 !important;
				}
				.sg-events-link:focus *:not(.status-badge) {
					color: #000000 !important;
				}
				.sg-events-link:hover {
					background: #5CE1E6;
					color: #000000 !important;
					border-color: #5CE1E6;
					transform: translateY(-1px);
					box-shadow: 0 3px 6px rgba(0,0,0,0.12);
				}
				.sg-events-link:hover *:not(.status-badge) {
					color: #000000 !important;
				}
				.sg-events-link:active {
					transform: translateY(0);
					box-shadow: 0 1px 3px rgba(0,0,0,0.1);
					color: #000000 !important;
				}
				.sg-events-link:active *:not(.status-badge) {
					color: #000000 !important;
				}
				.sg-events-link svg {
					width: 18px;
					height: 18px;
					flex-shrink: 0;
					stroke: #000000 !important;
					stroke-width: 2;
					fill: none;
				}
				.sg-events-link:hover svg {
					stroke: #000000 !important;
				}
				.sg-events-link .status-badge {
					margin-left: auto;
					font-size: 11px;
					font-weight: 600;
					padding: 4px 8px;
					border-radius: 4px;
					background: #2e7d32;
					color: #ffffff;
					letter-spacing: 0.3px;
					white-space: nowrap;
				}
				.sg-events-link .status-badge.missing {
					background: #f57c00;
					color: #ffffff;
				}
				.sg-events-link:visited {
					color: #000000 !important;
				}
				.sg-events-link:visited *:not(.status-badge) {
					color: #000000 !important;
				}
				@media (max-width: 1200px) {
					.sg-events-links,
					.sg-events-stats {
						grid-template-columns: 1fr;
					}
					.sg-events-link {
						font-size: 14px;
						padding: 12px 14px;
					}
				}
				@media (max-width: 782px) {
					.sg-events-link {
						min-height: 48px;
						padding: 12px 16px;
					}
					.sg-events-title {
						font-size: 16px;
					}
				}
			</style>
			
			<!-- Estatísticas -->
			<div class="sg-events-section">
				<h3 class="sg-events-title">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M9 11l3 3L22 4"/>
						<path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
					</svg>
					Estatísticas
				</h3>
				<div class="sg-events-stats">
					<div class="sg-stat-card">
						<span class="sg-stat-number"><?php echo esc_html( number_format_i18n( $counts['total'] ) ); ?></span>
						<span class="sg-stat-label">Total de Eventos</span>
						<?php if ( current_user_can( 'manage_options' ) ) : ?>
							<div style="font-size: 10px; margin-top: 5px; color: #666; opacity: 0.7;">
								<?php 
								global $wpdb;
								$etn_check = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'etn' AND post_status = 'publish'" );
								$sg_check = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'sg_eventos' AND post_status = 'publish'" );
								?>
								SG: <?php echo (int) $sg_check; ?> | ETN: <?php echo (int) $etn_check; ?>
							</div>
						<?php endif; ?>
					</div>
					<div class="sg-stat-card">
						<span class="sg-stat-number"><?php echo esc_html( number_format_i18n( $counts['upcoming'] ) ); ?></span>
						<span class="sg-stat-label">Eventos Futuros</span>
					</div>
				</div>
			</div>
			
			<!-- Criar Eventos -->
			<div class="sg-events-section">
				<h3 class="sg-events-title">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<line x1="12" y1="5" x2="12" y2="19"/>
						<line x1="5" y1="12" x2="19" y2="12"/>
					</svg>
					Criar Eventos
				</h3>
				<div class="sg-events-links">
					<?php if ( $has_sg_eventos ) : ?>
						<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=sg_eventos' ) ); ?>" class="sg-events-link">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<line x1="12" y1="5" x2="12" y2="19"/>
								<line x1="5" y1="12" x2="19" y2="12"/>
							</svg>
							Novo Evento
							<span class="status-badge">Criar</span>
						</a>
					<?php endif; ?>
					
					<?php if ( $has_etn ) : ?>
						<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=etn' ) ); ?>" class="sg-events-link">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
								<line x1="16" y1="2" x2="16" y2="6"/>
								<line x1="8" y1="2" x2="8" y2="6"/>
								<line x1="3" y1="10" x2="21" y2="10"/>
							</svg>
							Novo Evento (ETN)
						</a>
					<?php endif; ?>
					
					<?php if ( $has_tribe ) : ?>
						<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=tribe_events' ) ); ?>" class="sg-events-link">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
								<line x1="16" y1="2" x2="16" y2="6"/>
								<line x1="8" y1="2" x2="8" y2="6"/>
								<line x1="3" y1="10" x2="21" y2="10"/>
							</svg>
							Novo Evento (Tribe)
						</a>
					<?php endif; ?>
					
					<?php if ( ! $has_sg_eventos && ! $has_etn && ! $has_tribe ) : ?>
						<div class="sg-events-link" style="opacity: 0.6; cursor: not-allowed;">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<circle cx="12" cy="12" r="10"/>
								<line x1="12" y1="8" x2="12" y2="12"/>
								<line x1="12" y1="16" x2="12.01" y2="16"/>
							</svg>
							Sistema de eventos não disponível
						</div>
					<?php endif; ?>
				</div>
			</div>
			
			<!-- Gerenciar Eventos -->
			<div class="sg-events-section">
				<h3 class="sg-events-title">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
						<polyline points="14 2 14 8 20 8"/>
						<line x1="16" y1="13" x2="8" y2="13"/>
						<line x1="16" y1="17" x2="8" y2="17"/>
					</svg>
					Gerenciar Eventos
				</h3>
				<div class="sg-events-links">
					<?php if ( $has_sg_eventos || $has_etn ) : ?>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=sg-todos-eventos' ) ); ?>" class="sg-events-link">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
								<line x1="16" y1="2" x2="16" y2="6"/>
								<line x1="8" y1="2" x2="8" y2="6"/>
								<line x1="3" y1="10" x2="21" y2="10"/>
								<line x1="8" y1="14" x2="16" y2="14"/>
							</svg>
							Todos os Eventos
							<?php if ( $counts['total'] > 0 ) : ?>
								<span class="status-badge"><?php echo esc_html( $counts['total'] ); ?></span>
							<?php endif; ?>
						</a>
					<?php endif; ?>
					
					<?php if ( $has_sg_eventos ) : ?>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=sg_eventos' ) ); ?>" class="sg-events-link">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
								<line x1="16" y1="2" x2="16" y2="6"/>
								<line x1="8" y1="2" x2="8" y2="6"/>
								<line x1="3" y1="10" x2="21" y2="10"/>
								<line x1="8" y1="14" x2="16" y2="14"/>
							</svg>
							Eventos Próprios
							<?php if ( $counts['sg_eventos'] > 0 ) : ?>
								<span class="status-badge"><?php echo esc_html( $counts['sg_eventos'] ); ?></span>
							<?php endif; ?>
						</a>
					<?php endif; ?>
					
					<?php if ( $has_etn ) : ?>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=etn' ) ); ?>" class="sg-events-link">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
								<line x1="16" y1="2" x2="16" y2="6"/>
								<line x1="8" y1="2" x2="8" y2="6"/>
								<line x1="3" y1="10" x2="21" y2="10"/>
								<line x1="8" y1="14" x2="16" y2="14"/>
							</svg>
							Eventos ETN
							<?php if ( $counts['etn'] > 0 ) : ?>
								<span class="status-badge"><?php echo esc_html( $counts['etn'] ); ?></span>
							<?php endif; ?>
						</a>
					<?php endif; ?>
					
					<?php if ( $has_tribe ) : ?>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=tribe_events' ) ); ?>" class="sg-events-link">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
								<line x1="16" y1="2" x2="16" y2="6"/>
								<line x1="8" y1="2" x2="8" y2="6"/>
								<line x1="3" y1="10" x2="21" y2="10"/>
								<line x1="8" y1="14" x2="16" y2="14"/>
							</svg>
							Eventos Tribe
							<?php if ( $counts['tribe_events'] > 0 ) : ?>
								<span class="status-badge"><?php echo esc_html( $counts['tribe_events'] ); ?></span>
							<?php endif; ?>
						</a>
					<?php endif; ?>
					
					<?php
					// Link para página de eventos no frontend (prioridade para sg_eventos)
					$events_page_url = get_post_type_archive_link( 'sg_eventos' );
					if ( ! $events_page_url && $has_etn ) {
						$events_page_url = get_post_type_archive_link( 'etn' );
					}
					if ( ! $events_page_url && $has_tribe ) {
						$events_page_url = get_post_type_archive_link( 'tribe_events' );
					}
					if ( ! $events_page_url ) {
						$events_page_url = home_url( '/eventos' );
					}
					?>
					<a href="<?php echo esc_url( $events_page_url ); ?>" target="_blank" class="sg-events-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
							<polyline points="15 3 21 3 21 9"/>
							<line x1="10" y1="14" x2="21" y2="3"/>
						</svg>
						Ver Calendário no Site
					</a>
				</div>
			</div>
			
			<!-- Configurações e Ferramentas -->
			<div class="sg-events-section">
				<h3 class="sg-events-title">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<circle cx="12" cy="12" r="3"/>
						<path d="M12 1v6m0 6v6M5.64 5.64l4.24 4.24m4.24 4.24l4.24 4.24M1 12h6m6 0h6M5.64 18.36l4.24-4.24m4.24-4.24l4.24-4.24"/>
					</svg>
					Configurações e Ferramentas
				</h3>
				<div class="sg-events-links">
					<?php if ( $has_sg_eventos ) : ?>
						<a href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=sg_evento_categoria&post_type=sg_eventos' ) ); ?>" class="sg-events-link">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
								<line x1="7" y1="7" x2="7.01" y2="7"/>
							</svg>
							Categorias de Eventos
						</a>
					<?php endif; ?>
					
					<?php if ( $has_etn ) : ?>
						<a href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=etn_category&post_type=etn' ) ); ?>" class="sg-events-link">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
								<line x1="7" y1="7" x2="7.01" y2="7"/>
							</svg>
							Categorias ETN
						</a>
					<?php endif; ?>
					
					<?php if ( $has_tribe ) : ?>
						<a href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=tribe_events_cat&post_type=tribe_events' ) ); ?>" class="sg-events-link">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
								<line x1="7" y1="7" x2="7.01" y2="7"/>
							</svg>
							Categorias Tribe
						</a>
					<?php endif; ?>
					
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=sg-juridico-settings' ) ); ?>" class="sg-events-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<circle cx="12" cy="12" r="3"/>
							<path d="M12 1v6m0 6v6M5.64 5.64l4.24 4.24m4.24 4.24l4.24 4.24M1 12h6m6 0h6M5.64 18.36l4.24-4.24m4.24-4.24l4.24-4.24"/>
						</svg>
						Configurações do Tema
					</a>
					
					<?php if ( $has_etn && $has_sg_eventos ) : 
						$etn_count = wp_count_posts( 'etn' );
						$etn_total = isset( $etn_count->publish ) ? (int) $etn_count->publish : 0;
					?>
						<?php if ( $etn_total > 0 ) : ?>
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="sg-import-form" style="margin-top: 10px;">
								<?php wp_nonce_field( 'sg_import_etn_events', 'sg_import_nonce' ); ?>
								<input type="hidden" name="action" value="sg_import_etn_events">
								<button type="submit" class="sg-events-link" style="width: 100%; background: #f57c00; border-color: #f57c00;" onclick="return confirm('Importar todos os eventos ETN para o novo sistema? Isso criará cópias, não removerá os originais.');">
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
										<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
										<polyline points="7 10 12 15 17 10"/>
										<line x1="12" y1="15" x2="12" y2="3"/>
									</svg>
									Importar Eventos ETN (<?php echo esc_html( $etn_total ); ?>)
								</button>
							</form>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
}

// Inicializar widget
new SG_Calendar_Events_Widget();

/**
 * ============================================
 * PÁGINA DE LISTAGEM UNIFICADA DE EVENTOS
 * ============================================
 */

/**
 * Adicionar página de listagem unificada de eventos
 */
function sg_add_unified_events_page() {
	// Adicionar como submenu apenas uma vez, preferindo sg_eventos
	// Verificar se pelo menos um tipo de evento existe no banco ou está registrado
	global $wpdb;
	static $menu_added = false; // Prevenir duplicação
	
	if ( $menu_added ) {
		return; // Já foi adicionado
	}
	
	$has_sg_eventos = post_type_exists( 'sg_eventos' );
	$has_etn = post_type_exists( 'etn' );
	
	// Verificar se há eventos no banco mesmo que não estejam registrados
	$count_sg = $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish'",
		'sg_eventos'
	) );
	$count_etn = $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish'",
		'etn'
	) );
	
	// Adicionar apenas uma vez, priorizando sg_eventos
	if ( $has_sg_eventos || $count_sg > 0 ) {
		add_submenu_page(
			'edit.php?post_type=sg_eventos',
			__( 'Todos os Eventos', 'sg-juridico' ),
			__( 'Todos os Eventos', 'sg-juridico' ),
			'edit_posts',
			'sg-todos-eventos',
			'sg_render_unified_events_page',
			1 // Prioridade alta para aparecer no topo
		);
		$menu_added = true;
	} elseif ( $has_etn || $count_etn > 0 ) {
		// Se sg_eventos não existir mas etn existir, adicionar ao menu etn
		add_submenu_page(
			'edit.php?post_type=etn',
			__( 'Todos os Eventos', 'sg-juridico' ),
			__( 'Todos os Eventos', 'sg-juridico' ),
			'edit_posts',
			'sg-todos-eventos',
			'sg_render_unified_events_page',
			1
		);
		$menu_added = true;
	}
}
add_action( 'admin_menu', 'sg_add_unified_events_page', 99 );

/**
 * Remover submenu padrão "Todos os Eventos" do WordPress
 * Executar depois que os menus padrão foram criados
 */
function sg_remove_default_events_submenu() {
	global $submenu;
	
	// Remover o submenu padrão que o WordPress cria automaticamente
	if ( isset( $submenu['edit.php?post_type=sg_eventos'] ) ) {
		foreach ( $submenu['edit.php?post_type=sg_eventos'] as $key => $item ) {
			// Remover apenas o submenu padrão (slug igual ao parent), não o nosso customizado
			if ( isset( $item[2] ) && $item[2] === 'edit.php?post_type=sg_eventos' ) {
				unset( $submenu['edit.php?post_type=sg_eventos'][$key] );
			}
		}
	}
	
	if ( isset( $submenu['edit.php?post_type=etn'] ) ) {
		foreach ( $submenu['edit.php?post_type=etn'] as $key => $item ) {
			// Remover apenas o submenu padrão (slug igual ao parent), não o nosso customizado
			if ( isset( $item[2] ) && $item[2] === 'edit.php?post_type=etn' ) {
				unset( $submenu['edit.php?post_type=etn'][$key] );
			}
		}
	}
}
add_action( 'admin_menu', 'sg_remove_default_events_submenu', 101 );

/**
 * Permitir edição de posts ETN e Tribe Events mesmo quando não estão registrados
 */
function sg_allow_editing_unregistered_post_types() {
	// Verificar se estamos na página de edição
	if ( ! isset( $_GET['action'] ) || $_GET['action'] !== 'edit' ) {
		return;
	}
	
	if ( ! isset( $_GET['post'] ) ) {
		return;
	}
	
	$post_id = absint( $_GET['post'] );
	$post = get_post( $post_id );
	
	if ( ! $post ) {
		return;
	}
	
	$post_type = $post->post_type;
	
	// Se o post type não está registrado mas é um dos tipos de evento que queremos suportar
	if ( ! post_type_exists( $post_type ) && in_array( $post_type, array( 'etn', 'tribe_events' ) ) ) {
		// Registrar temporariamente o post type para permitir edição
		if ( $post_type === 'etn' ) {
			// Criar um post type básico para ETN
			register_post_type( 'etn', array(
				'public' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'capability_type' => 'post',
				'map_meta_cap' => true,
				'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			) );
		} elseif ( $post_type === 'tribe_events' ) {
			// Criar um post type básico para Tribe Events
			register_post_type( 'tribe_events', array(
				'public' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'capability_type' => 'post',
				'map_meta_cap' => true,
				'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			) );
		}
		
		// Remover suporte para comentários e trackbacks
		remove_post_type_support( $post_type, 'comments' );
		remove_post_type_support( $post_type, 'trackbacks' );
	}
}
add_action( 'admin_init', 'sg_allow_editing_unregistered_post_types', 1 );

/**
 * Otimizar página de edição de eventos - remover meta boxes desnecessários
 */
function sg_optimize_event_edit_page() {
	$screen = get_current_screen();
	
	// Verificar se estamos editando um evento
	if ( ! $screen || ! in_array( $screen->post_type, array( 'sg_eventos', 'etn', 'tribe_events' ) ) ) {
		return;
	}
	
	// Remover meta boxes desnecessários
	remove_meta_box( 'commentsdiv', $screen->post_type, 'normal' ); // Comentários
	remove_meta_box( 'commentstatusdiv', $screen->post_type, 'normal' ); // Status de comentários
	remove_meta_box( 'trackbacksdiv', $screen->post_type, 'normal' ); // Trackbacks
	remove_meta_box( 'slugdiv', $screen->post_type, 'normal' ); // Slug (já está no permalink)
	remove_meta_box( 'authordiv', $screen->post_type, 'normal' ); // Autor
	remove_meta_box( 'postcustom', $screen->post_type, 'normal' ); // Campos personalizados (se não necessário)
	remove_meta_box( 'revisionsdiv', $screen->post_type, 'normal' ); // Revisões (opcional)
	
	// Remover meta boxes de plugins comuns
	remove_meta_box( 'litespeed_meta_box', $screen->post_type, 'normal' ); // LiteSpeed Cache
	remove_meta_box( 'wpseo_meta', $screen->post_type, 'normal' ); // Yoast SEO (se existir)
	
	// Remover meta boxes de categorias/tags se não forem necessários (manter apenas se o evento usar)
	// remove_meta_box( 'tagsdiv-post_tag', $screen->post_type, 'side' ); // Tags padrão
	// remove_meta_box( 'categorydiv', $screen->post_type, 'side' ); // Categorias padrão
	
	// Remover Excerpt se não for necessário
	remove_meta_box( 'postexcerpt', $screen->post_type, 'normal' );
	
	// Remover Featured Image se não for necessário (comentar se precisar)
	// remove_meta_box( 'postimagediv', $screen->post_type, 'side' );
}
add_action( 'add_meta_boxes', 'sg_optimize_event_edit_page', 99 );

/**
 * Remover colunas desnecessárias da lista de eventos
 */
function sg_optimize_event_columns( $columns ) {
	global $typenow;
	
	if ( ! in_array( $typenow, array( 'sg_eventos', 'etn', 'tribe_events' ) ) ) {
		return $columns;
	}
	
	// Remover colunas desnecessárias se existirem
	unset( $columns['comments'] );
	unset( $columns['author'] );
	
	return $columns;
}
add_filter( 'manage_posts_columns', 'sg_optimize_event_columns' );

/**
 * Desabilitar discussão (comentários) para eventos
 */
function sg_disable_comments_for_events( $post_types ) {
	$event_post_types = array( 'sg_eventos', 'etn', 'tribe_events' );
	
	foreach ( $event_post_types as $post_type ) {
		if ( post_type_exists( $post_type ) ) {
			remove_post_type_support( $post_type, 'comments' );
			remove_post_type_support( $post_type, 'trackbacks' );
		}
	}
}
add_action( 'admin_init', 'sg_disable_comments_for_events' );

/**
 * Simplificar barra de ferramentas do editor para eventos
 */
function sg_simplify_event_editor( $toolbars ) {
	global $typenow;
	
	if ( ! in_array( $typenow, array( 'sg_eventos', 'etn', 'tribe_events' ) ) ) {
		return $toolbars;
	}
	
	// Simplificar toolbar - manter apenas ferramentas essenciais
	// Isso pode ser customizado conforme necessário
	return $toolbars;
}
// add_filter( 'tiny_mce_before_init', 'sg_simplify_event_editor' ); // Descomentar se quiser simplificar toolbar

/**
 * Remover widgets desnecessários do dashboard relacionados a eventos
 */
function sg_remove_event_dashboard_widgets() {
	// Remover widgets padrão do WordPress que não são necessários
	// remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
	// remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
}
// add_action( 'wp_dashboard_setup', 'sg_remove_event_dashboard_widgets' ); // Descomentar se necessário

/**
 * Renderizar página de listagem unificada
 */
function sg_render_unified_events_page() {
	global $wpdb;
	
	// Tipos de post possíveis para eventos
	$possible_post_types = array( 'sg_eventos', 'etn', 'tribe_events' );
	
	// Verificar quais tipos de post têm eventos no banco de dados
	// Isso funciona mesmo se o post type não estiver registrado no momento
	$post_types = array();
	foreach ( $possible_post_types as $pt ) {
		$count = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM {$wpdb->posts} 
			WHERE post_type = %s AND post_status = 'publish'",
			$pt
		) );
		
		// Incluir se tiver eventos OU se o post type estiver registrado
		if ( $count > 0 || post_type_exists( $pt ) ) {
			$post_types[] = $pt;
		}
	}
	
	if ( empty( $post_types ) ) {
		echo '<div class="wrap"><h1>' . esc_html__( 'Todos os Eventos', 'sg-juridico' ) . '</h1>';
		echo '<p>' . esc_html__( 'Nenhum sistema de eventos configurado.', 'sg-juridico' ) . '</p></div>';
		return;
	}
	
	// Buscar eventos
	$paged = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
	$per_page = 20;
	
	// Sempre buscar diretamente do banco para garantir que funcione mesmo com post types não registrados
	$post_types_escaped = array_map( 'esc_sql', $post_types );
	$post_types_sql = "'" . implode( "','", $post_types_escaped ) . "'";
	
	// Contar total de eventos
	$db_count = (int) $wpdb->get_var(
		"SELECT COUNT(*) FROM {$wpdb->posts} 
		WHERE post_type IN ($post_types_sql) 
		AND post_status = 'publish'"
	);
	
	// Buscar IDs dos eventos para a página atual
	$offset = ( $paged - 1 ) * $per_page;
	// Construir query de forma segura - post_types já escapados, LIMIT e OFFSET serão preparados
	$query = "SELECT ID FROM {$wpdb->posts} 
		WHERE post_type IN ($post_types_sql) 
		AND post_status = 'publish'
		ORDER BY post_date DESC
		LIMIT " . absint( $per_page ) . " OFFSET " . absint( $offset );
	$results = $wpdb->get_results( $query );
	
	// Criar objeto WP_Query simulado
	$events_query = new WP_Query();
	$events_query->found_posts = $db_count;
	$events_query->max_num_pages = ceil( $db_count / $per_page );
	
	if ( ! empty( $results ) ) {
		$ids = array_map( 'intval', wp_list_pluck( $results, 'ID' ) );
		
		// Tentar carregar posts usando WP_Query com post__in
		// Especificar post_type explicitamente para forçar busca
		$args = array(
			'post__in'       => $ids,
			'post_type'      => $post_types, // Especificar tipos explicitamente
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'orderby'        => 'post__in',
			'order'          => 'ASC',
			'no_found_rows'  => false,
		);
		
		$events_query = new WP_Query( $args );
		
		// Se não carregou posts, tentar carregar diretamente usando get_post()
		if ( $events_query->post_count === 0 && ! empty( $ids ) ) {
			$posts = array();
			foreach ( $ids as $post_id ) {
				$post = get_post( $post_id );
				if ( $post && $post->post_status === 'publish' ) {
					$posts[] = $post;
				}
			}
			
			// Criar objeto WP_Query customizado com os posts carregados
			if ( ! empty( $posts ) ) {
				$events_query = new WP_Query();
				$events_query->posts = $posts;
				$events_query->post_count = count( $posts );
				$events_query->found_posts = $db_count;
				$events_query->max_num_pages = ceil( $db_count / $per_page );
				$events_query->current_post = -1;
			}
		} else {
			// Ajustar contadores se necessário
			$events_query->found_posts = $db_count;
			$events_query->max_num_pages = ceil( $db_count / $per_page );
		}
	} else {
		// Criar query vazia
		$events_query = new WP_Query( array( 'post__in' => array( 0 ) ) );
		$events_query->found_posts = 0;
		$events_query->max_num_pages = 0;
	}
	
	// DEBUG: Verificar se encontrou eventos
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log( 'SG Todos Eventos - Tipos: ' . implode( ', ', $post_types ) . ', Posts encontrados: ' . $events_query->post_count . ', Total: ' . $events_query->found_posts );
	}
	?>
	<style>
		/* Corrigir cor do cabeçalho "Título" e dos títulos de eventos */
		.wp-list-table th.column-title.column-primary span,
		.wp-list-table th.column-title.column-primary a,
		.wp-list-table th.manage-column.column-title.column-primary span,
		.wp-list-table th.manage-column.column-title.column-primary a {
			color: #23282d !important;
		}
		.wp-list-table td.column-title a.row-title,
		.wp-list-table td.title.column-title a.row-title {
			color: #23282d !important;
		}
	</style>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Todos os Eventos', 'sg-juridico' ); ?></h1>
		<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=sg_eventos' ) ); ?>" class="page-title-action">
			<?php esc_html_e( 'Adicionar Novo Evento', 'sg-juridico' ); ?>
		</a>
		<hr class="wp-header-end">
		
		<?php 
		// Debug visível para administradores
		if ( current_user_can( 'manage_options' ) && isset( $_GET['debug'] ) ) {
			echo '<div class="notice notice-info"><p><strong>Debug:</strong></p><ul>';
			echo '<li>Tipos de post verificados: ' . implode( ', ', $post_types ) . '</li>';
			echo '<li>Total encontrado pela query: ' . $events_query->found_posts . '</li>';
			echo '<li>Posts na página atual: ' . $events_query->post_count . '</li>';
			if ( ! empty( $results ) ) {
				$ids = array_map( 'intval', wp_list_pluck( $results, 'ID' ) );
				echo '<li>IDs encontrados no banco: ' . implode( ', ', array_slice( $ids, 0, 10 ) ) . ( count( $ids ) > 10 ? '...' : '' ) . '</li>';
			}
			global $wpdb;
			$post_types_sql = "'" . implode( "','", array_map( 'esc_sql', $post_types ) ) . "'";
			$db_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type IN ($post_types_sql) AND post_status = 'publish'" );
			echo '<li>Total no banco de dados: ' . $db_count . '</li>';
			if ( isset( $events_query->posts ) && is_array( $events_query->posts ) ) {
				echo '<li>Posts carregados: ' . count( $events_query->posts ) . '</li>';
			}
			echo '</ul></div>';
		}
		?>
		
		<?php if ( $events_query->have_posts() ) : ?>
			<table class="wp-list-table widefat fixed striped table-view-list posts">
				<thead>
					<tr>
						<td id="cb" class="manage-column column-cb check-column">
							<input id="cb-select-all-1" type="checkbox">
						</td>
						<th scope="col" class="manage-column column-title column-primary">
							<span><?php esc_html_e( 'Título', 'sg-juridico' ); ?></span>
						</th>
						<th scope="col" class="manage-column">
							<span><?php esc_html_e( 'Tipo', 'sg-juridico' ); ?></span>
						</th>
						<th scope="col" class="manage-column">
							<span><?php esc_html_e( 'Data do Evento', 'sg-juridico' ); ?></span>
						</th>
						<th scope="col" class="manage-column">
							<span><?php esc_html_e( 'Local', 'sg-juridico' ); ?></span>
						</th>
						<th scope="col" class="manage-column">
							<span><?php esc_html_e( 'Data de Publicação', 'sg-juridico' ); ?></span>
						</th>
					</tr>
				</thead>
				<tbody id="the-list">
					<?php while ( $events_query->have_posts() ) : $events_query->the_post(); 
						$post_type = get_post_type();
						
						// Detectar meta keys baseado no tipo
						if ( $post_type === 'sg_eventos' ) {
							$meta_key_start = '_sg_evento_data_inicio';
							$meta_key_end = '_sg_evento_data_fim';
							$meta_key_location = '_sg_evento_local';
						} elseif ( $post_type === 'tribe_events' ) {
							$meta_key_start = '_EventStartDate';
							$meta_key_end = '_EventEndDate';
							$meta_key_location = '_EventVenue';
						} else {
							// ETN
							$meta_key_start = 'etn_start_date';
							$meta_key_end = 'etn_end_date';
							$meta_key_location = 'etn_location';
						}
						
						$start_date = get_post_meta( get_the_ID(), $meta_key_start, true );
						$end_date = get_post_meta( get_the_ID(), $meta_key_end, true );
						$location = get_post_meta( get_the_ID(), $meta_key_location, true );
						
						// Tribe Events pode ter timestamp numérico
						if ( $post_type === 'tribe_events' && is_numeric( $start_date ) ) {
							$start_date = date( 'Y-m-d', $start_date );
						}
						if ( $post_type === 'tribe_events' && is_numeric( $end_date ) ) {
							$end_date = date( 'Y-m-d', $end_date );
						}
						
						$event_date_display = '';
						if ( $start_date ) {
							$event_date_display = date_i18n( 'd/m/Y', strtotime( $start_date ) );
							if ( $end_date && $end_date !== $start_date ) {
								$end_formatted = is_numeric( $end_date ) ? date_i18n( 'd/m/Y', $end_date ) : date_i18n( 'd/m/Y', strtotime( $end_date ) );
								$event_date_display .= ' até ' . $end_formatted;
							}
						} else {
							$event_date_display = '<span style="color: #d63638;">—</span>';
						}
						
						$type_label = '';
						if ( $post_type === 'sg_eventos' ) {
							$type_label = 'Próprio';
						} elseif ( $post_type === 'etn' ) {
							$type_label = 'ETN';
						} elseif ( $post_type === 'tribe_events' ) {
							$type_label = 'Tribe';
						}
						
						// Construir URL de edição manualmente se necessário
						$edit_link = get_edit_post_link( get_the_ID() );
						
						// Sempre construir a URL com post_type para garantir que funcione
						// mesmo se get_edit_post_link retornar vazio ou não incluir o post_type
						if ( empty( $edit_link ) || $post_type !== 'sg_eventos' ) {
							// Construir URL baseada no tipo de post
							if ( $post_type === 'sg_eventos' ) {
								$edit_link = admin_url( 'post.php?action=edit&post=' . get_the_ID() );
							} elseif ( $post_type === 'etn' ) {
								// Para ETN, usar a URL específica do post type
								$edit_link = admin_url( 'post.php?action=edit&post=' . get_the_ID() . '&post_type=etn' );
							} elseif ( $post_type === 'tribe_events' ) {
								// Para Tribe Events, usar a URL específica do post type
								$edit_link = admin_url( 'post.php?action=edit&post=' . get_the_ID() . '&post_type=tribe_events' );
							} else {
								// Fallback genérico
								$edit_link = admin_url( 'post.php?action=edit&post=' . get_the_ID() . '&post_type=' . $post_type );
							}
						} else {
							// Se já temos um link mas não inclui post_type, adicionar se necessário
							if ( $post_type !== 'sg_eventos' && strpos( $edit_link, 'post_type=' ) === false ) {
								$edit_link .= ( strpos( $edit_link, '?' ) !== false ? '&' : '?' ) . 'post_type=' . $post_type;
							}
						}
						
						// Construir URL de exclusão
						$delete_link = get_delete_post_link( get_the_ID() );
						if ( empty( $delete_link ) || $post_type !== 'sg_eventos' ) {
							// Construir URL de exclusão baseada no tipo de post
							$delete_link = admin_url( 'post.php?action=delete&post=' . get_the_ID() . '&_wpnonce=' . wp_create_nonce( 'delete-post_' . get_the_ID() ) );
							if ( $post_type !== 'sg_eventos' ) {
								$delete_link .= '&post_type=' . $post_type;
							}
						}
					?>
						<tr>
							<th scope="row" class="check-column">
								<input id="cb-select-<?php echo get_the_ID(); ?>" type="checkbox" name="post[]" value="<?php echo get_the_ID(); ?>">
							</th>
							<td class="title column-title has-row-actions column-primary" data-colname="Título">
								<strong>
									<a class="row-title" href="<?php echo esc_url( $edit_link ); ?>" aria-label="<?php echo esc_attr( get_the_title() . ' (Editar)' ); ?>">
										<?php the_title(); ?>
									</a>
								</strong>
								<div class="row-actions">
									<span class="edit">
										<a href="<?php echo esc_url( $edit_link ); ?>" aria-label="<?php echo esc_attr( 'Editar ' . get_the_title() ); ?>">
											<?php esc_html_e( 'Editar', 'sg-juridico' ); ?>
										</a> |
									</span>
									<span class="trash">
										<a href="<?php echo esc_url( $delete_link ); ?>" class="submitdelete" aria-label="<?php echo esc_attr( 'Excluir ' . get_the_title() ); ?>">
											<?php esc_html_e( 'Excluir', 'sg-juridico' ); ?>
										</a>
									</span>
								</div>
							</td>
							<td class="column-type" data-colname="Tipo">
								<?php echo esc_html( $type_label ); ?>
							</td>
							<td class="column-event-date" data-colname="Data do Evento">
								<?php echo $event_date_display; ?>
							</td>
							<td class="column-location" data-colname="Local">
								<?php echo $location ? esc_html( $location ) : '<span style="color: #999;">—</span>'; ?>
							</td>
							<td class="date column-date" data-colname="Data">
								<?php echo esc_html( get_the_date() ); ?>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
			
			<div class="tablenav bottom">
				<div class="tablenav-pages">
					<?php
					$big = 999999999;
					echo paginate_links( array(
						'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
						'format'  => '?paged=%#%',
						'current' => max( 1, $paged ),
						'total'   => $events_query->max_num_pages,
					) );
					?>
				</div>
			</div>
			
			<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<div class="notice notice-info">
				<p><?php esc_html_e( 'Nenhum evento encontrado. Verifique se existem eventos publicados.', 'sg-juridico' ); ?></p>
				<?php if ( current_user_can( 'manage_options' ) ) : ?>
					<p><strong><?php esc_html_e( 'Debug:', 'sg-juridico' ); ?></strong></p>
					<ul>
						<li><?php printf( esc_html__( 'Tipos de post buscados: %s', 'sg-juridico' ), implode( ', ', $post_types ) ); ?></li>
						<li><?php printf( esc_html__( 'Posts encontrados: %d', 'sg-juridico' ), $events_query->found_posts ); ?></li>
						<li><?php printf( esc_html__( 'Posts na página atual: %d', 'sg-juridico' ), $events_query->post_count ); ?></li>
					</ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * ============================================
 * FERRAMENTA DE IMPORTAÇÃO DE EVENTOS ETN
 * ============================================
 */

/**
 * Importar eventos ETN para sg_eventos
 */
function sg_import_etn_events() {
	// Verificar permissões
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Sem permissão para importar eventos.' );
	}

	// Verificar nonce
	if ( ! isset( $_POST['sg_import_nonce'] ) || ! wp_verify_nonce( $_POST['sg_import_nonce'], 'sg_import_etn_events' ) ) {
		wp_die( 'Verificação de segurança falhou.' );
	}

	if ( ! post_type_exists( 'etn' ) || ! post_type_exists( 'sg_eventos' ) ) {
		wp_redirect( admin_url( 'index.php?sg_import=error&message=tipos_nao_existem' ) );
		exit;
	}

	// Buscar todos os eventos ETN
	$etn_events = get_posts( array(
		'post_type'      => 'etn',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
	) );

	$imported = 0;
	$skipped = 0;

	foreach ( $etn_events as $etn_event ) {
		// Verificar se já existe um evento sg_eventos com mesmo título e data
		$start_date = get_post_meta( $etn_event->ID, 'etn_start_date', true );
		if ( ! $start_date ) {
			$skipped++;
			continue;
		}

		// Verificar se já existe evento com mesmo título e data
		$existing_query = new WP_Query( array(
			'post_type'      => 'sg_eventos',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			's'              => $etn_event->post_title,
			'meta_query'     => array(
				array(
					'key'   => '_sg_evento_data_inicio',
					'value' => $start_date,
				),
			),
		) );
		$existing = $existing_query->posts;
		wp_reset_postdata();

		if ( ! empty( $existing ) ) {
			$skipped++;
			continue;
		}

		// Criar novo evento sg_eventos
		$new_event_id = wp_insert_post( array(
			'post_title'   => $etn_event->post_title,
			'post_content' => $etn_event->post_content,
			'post_excerpt' => $etn_event->post_excerpt,
			'post_status'  => 'publish',
			'post_type'    => 'sg_eventos',
			'post_author'  => $etn_event->post_author,
		) );

		if ( $new_event_id ) {
			// Copiar meta fields
			$start_date = get_post_meta( $etn_event->ID, 'etn_start_date', true );
			$end_date = get_post_meta( $etn_event->ID, 'etn_end_date', true );
			$location = get_post_meta( $etn_event->ID, 'etn_location', true );

			if ( $start_date ) {
				update_post_meta( $new_event_id, '_sg_evento_data_inicio', $start_date );
			}
			if ( $end_date ) {
				update_post_meta( $new_event_id, '_sg_evento_data_fim', $end_date );
			}
			if ( $location ) {
				update_post_meta( $new_event_id, '_sg_evento_local', $location );
			}

			// Copiar featured image
			$thumbnail_id = get_post_thumbnail_id( $etn_event->ID );
			if ( $thumbnail_id ) {
				set_post_thumbnail( $new_event_id, $thumbnail_id );
			}

			// Detectar e atribuir categoria
			$category = sg_detect_event_category( $etn_event->post_title );
			if ( $category && $category !== 'outros' ) {
				$term = get_term_by( 'slug', $category, 'sg_evento_categoria' );
				if ( ! $term ) {
					// Criar termo se não existir
					$term_result = wp_insert_term( ucfirst( str_replace( '-', ' ', $category ) ), 'sg_evento_categoria', array( 'slug' => $category ) );
					if ( ! is_wp_error( $term_result ) ) {
						$term_id = $term_result['term_id'];
					}
				} else {
					$term_id = $term->term_id;
				}
				if ( isset( $term_id ) ) {
					wp_set_post_terms( $new_event_id, array( $term_id ), 'sg_evento_categoria' );
				}
			}

			$imported++;
		}
	}

	// Limpar cache
	sg_clear_sg_eventos_cache();

	wp_redirect( admin_url( 'index.php?sg_import=success&imported=' . $imported . '&skipped=' . $skipped ) );
	exit;
}
add_action( 'admin_post_sg_import_etn_events', 'sg_import_etn_events' );

/**
 * ============================================
 * PÁGINA DE CONFIGURAÇÕES GERAIS DO TEMA
 * ============================================
 */

/**
 * Adicionar página de configurações no menu do admin
 */
function sg_add_theme_settings_page() {
	add_menu_page(
		'Configurações Gerais - SG Jurídico',
		'SG Jurídico',
		'manage_options',
		'sg-juridico-settings',
		'sg_render_settings_page',
		'dashicons-admin-generic',
		30
	);
}
add_action( 'admin_menu', 'sg_add_theme_settings_page' );

/**
 * Registrar configurações
 */
function sg_register_settings() {
	// Registrar opção do logo da admin bar
	register_setting( 'sg_juridico_settings', 'sg_admin_bar_logo_id', array(
		'type' => 'integer',
		'sanitize_callback' => 'absint',
		'default' => ''
	) );
	
	// Registrar opções das imagens do banner (até 3)
	register_setting( 'sg_juridico_settings', 'sg_home_banner_images', array(
		'type' => 'string',
		'sanitize_callback' => 'sg_sanitize_banner_images',
		'default' => ''
	) );
}
add_action( 'admin_init', 'sg_register_settings' );

/**
 * Sanitizar IDs das imagens do banner
 */
function sg_sanitize_banner_images( $value ) {
	if ( empty( $value ) ) {
		return '';
	}
	
	$image_ids = explode( ',', $value );
	$sanitized = array();
	
	foreach ( $image_ids as $id ) {
		$id = absint( trim( $id ) );
		if ( $id > 0 ) {
			$sanitized[] = $id;
		}
	}
	
	return implode( ',', $sanitized );
}

/**
 * Renderizar página de configurações
 */
function sg_render_settings_page() {
	// Verificar permissões
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	
	// Salvar configurações
	if ( isset( $_POST['sg_settings_submit'] ) && check_admin_referer( 'sg_settings_save', 'sg_settings_nonce' ) ) {
		$logo_id = isset( $_POST['sg_admin_bar_logo_id'] ) ? absint( $_POST['sg_admin_bar_logo_id'] ) : '';
		update_option( 'sg_admin_bar_logo_id', $logo_id );
		
		// Salvar imagens do banner
		$banner_images = isset( $_POST['sg_home_banner_images'] ) ? sanitize_text_field( $_POST['sg_home_banner_images'] ) : '';
		update_option( 'sg_home_banner_images', $banner_images );
		
		echo '<div class="notice notice-success is-dismissible"><p>Configurações salvas com sucesso!</p></div>';
	}
	
	// Obter valor atual do logo
	$current_logo_id = get_option( 'sg_admin_bar_logo_id', '' );
	$current_logo_url = '';
	if ( $current_logo_id ) {
		$logo_data = wp_get_attachment_image_src( $current_logo_id, 'full' );
		if ( $logo_data ) {
			$current_logo_url = $logo_data[0];
		}
	}
	
	// Obter imagens do banner
	$banner_images_str = get_option( 'sg_home_banner_images', '' );
	$banner_image_ids = array();
	if ( ! empty( $banner_images_str ) ) {
		$ids = explode( ',', $banner_images_str );
		foreach ( $ids as $id ) {
			$id = absint( trim( $id ) );
			if ( $id > 0 ) {
				$banner_image_ids[] = $id;
			}
		}
	}
	// Limitar a 3 imagens
	$banner_image_ids = array_slice( $banner_image_ids, 0, 3 );
	?>
	<div class="wrap">
		<h1>Configurações Gerais - SG Jurídico</h1>
		
		<form method="post" action="" id="sg-settings-form">
			<?php wp_nonce_field( 'sg_settings_save', 'sg_settings_nonce' ); ?>
			
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="sg_admin_bar_logo">Logo Painel</label>
						</th>
						<td>
							<div class="sg-logo-upload-wrapper">
								<input type="hidden" id="sg_admin_bar_logo_id" name="sg_admin_bar_logo_id" value="<?php echo esc_attr( $current_logo_id ); ?>" />
								
								<div id="sg-logo-preview" style="margin-bottom: 15px;">
									<?php if ( $current_logo_url ) : ?>
										<img src="<?php echo esc_url( $current_logo_url ); ?>" alt="Logo Preview" style="max-width: 200px; max-height: 60px; display: block; border: 1px solid #ddd; padding: 5px; background: #fff;" />
									<?php else : ?>
										<div style="width: 200px; height: 60px; border: 1px dashed #ddd; display: flex; align-items: center; justify-content: center; background: #f9f9f9; color: #666;">
											Nenhuma imagem selecionada
										</div>
									<?php endif; ?>
								</div>
								
								<button type="button" id="sg-upload-logo-btn" class="button">
									<?php echo $current_logo_id ? 'Alterar Logo' : 'Selecionar Logo da Biblioteca'; ?>
								</button>
								<?php if ( $current_logo_id ) : ?>
									<button type="button" id="sg-remove-logo-btn" class="button" style="margin-left: 10px;">
										Remover Logo
									</button>
								<?php endif; ?>
								
								<p class="description">
									Selecione o logo que aparecerá na barra superior do WordPress (admin bar) e na página de login, substituindo o logo padrão do WordPress.
								</p>
							</div>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label>Imagens do Banner Principal</label>
						</th>
						<td>
							<div class="sg-banner-images-wrapper">
								<p class="description" style="margin-bottom: 15px;">
									<strong>Recomendações de formato:</strong> Para melhor visualização, use imagens no formato <strong>horizontal (landscape)</strong> com proporção <strong>16:9</strong> ou <strong>2:1</strong>. 
									Resolução recomendada: <strong>1200x675px</strong> ou <strong>1600x900px</strong>. Formatos aceitos: JPG, PNG ou WebP.
								</p>
								
								<input type="hidden" id="sg_home_banner_images" name="sg_home_banner_images" value="<?php echo esc_attr( implode( ',', $banner_image_ids ) ); ?>" />
								
								<div id="sg-banner-images-preview" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-bottom: 15px;">
									<?php 
									for ( $i = 0; $i < 3; $i++ ) :
										$img_id = isset( $banner_image_ids[$i] ) ? $banner_image_ids[$i] : 0;
										$img_url = '';
										if ( $img_id ) {
											$img_data = wp_get_attachment_image_src( $img_id, 'medium' );
											if ( $img_data ) {
												$img_url = $img_data[0];
											}
										}
										?>
										<div class="sg-banner-image-item" data-index="<?php echo $i; ?>" data-image-id="<?php echo $img_id; ?>">
											<label style="display: block; margin-bottom: 8px; font-weight: 600;">Banner <?php echo $i + 1; ?></label>
											<div class="sg-banner-image-preview" style="width: 100%; aspect-ratio: 16/9; border: 2px dashed #ddd; border-radius: 4px; background: #f9f9f9; display: flex; align-items: center; justify-content: center; margin-bottom: 8px; overflow: hidden;">
												<?php if ( $img_url ) : ?>
													<img src="<?php echo esc_url( $img_url ); ?>" alt="Banner <?php echo $i + 1; ?>" style="width: 100%; height: 100%; object-fit: cover;" />
												<?php else : ?>
													<span style="color: #666; font-size: 14px;">Nenhuma imagem</span>
												<?php endif; ?>
											</div>
											<div style="display: flex; gap: 8px;">
												<button type="button" class="button sg-upload-banner-btn" data-index="<?php echo $i; ?>">
													<?php echo $img_id ? 'Alterar' : 'Selecionar'; ?>
												</button>
												<?php if ( $img_id ) : ?>
													<button type="button" class="button sg-remove-banner-btn" data-index="<?php echo $i; ?>">
														Remover
													</button>
												<?php endif; ?>
											</div>
										</div>
									<?php endfor; ?>
								</div>
								
								<p class="description">
									Você pode adicionar até 3 imagens que serão exibidas na página inicial (home) em substituição ao carrossel.
								</p>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			
			<?php submit_button( 'Salvar Configurações', 'primary', 'sg_settings_submit', false ); ?>
		</form>
	</div>
	
	<style>
		.sg-logo-upload-wrapper {
			max-width: 600px;
		}
		#sg-logo-preview img {
			border-radius: 4px;
		}
	</style>
	
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		var mediaUploader;
		
		// Botão de upload
		$('#sg-upload-logo-btn').on('click', function(e) {
			e.preventDefault();
			
			// Se o uploader já existe, abrir
			if (mediaUploader) {
				mediaUploader.open();
				return;
			}
			
			// Criar novo uploader
			mediaUploader = wp.media({
				title: 'Selecionar Logo Painel',
				button: {
					text: 'Usar este logo'
				},
				multiple: false,
				library: {
					type: 'image'
				}
			});
			
			// Quando uma imagem é selecionada
			mediaUploader.on('select', function() {
				var attachment = mediaUploader.state().get('selection').first().toJSON();
				
				// Atualizar campo hidden
				$('#sg_admin_bar_logo_id').val(attachment.id);
				
				// Atualizar preview
				$('#sg-logo-preview').html(
					'<img src="' + attachment.url + '" alt="Logo Preview" style="max-width: 200px; max-height: 60px; display: block; border: 1px solid #ddd; padding: 5px; background: #fff; border-radius: 4px;" />'
				);
				
				// Atualizar texto do botão
				$('#sg-upload-logo-btn').text('Alterar Logo');
				
				// Mostrar botão de remover se não existir
				if ($('#sg-remove-logo-btn').length === 0) {
					$('#sg-upload-logo-btn').after(
						'<button type="button" id="sg-remove-logo-btn" class="button" style="margin-left: 10px;">Remover Logo</button>'
					);
					
					// Adicionar evento ao botão de remover
					$('#sg-remove-logo-btn').on('click', function() {
						$('#sg_admin_bar_logo_id').val('');
						$('#sg-logo-preview').html(
							'<div style="width: 200px; height: 60px; border: 1px dashed #ddd; display: flex; align-items: center; justify-content: center; background: #f9f9f9; color: #666;">Nenhuma imagem selecionada</div>'
						);
						$('#sg-upload-logo-btn').text('Selecionar Logo da Biblioteca');
						$(this).remove();
					});
				}
			});
			
			// Abrir uploader
			mediaUploader.open();
		});
		
		// Botão de remover
		$(document).on('click', '#sg-remove-logo-btn', function(e) {
			e.preventDefault();
			$('#sg_admin_bar_logo_id').val('');
			$('#sg-logo-preview').html(
				'<div style="width: 200px; height: 60px; border: 1px dashed #ddd; display: flex; align-items: center; justify-content: center; background: #f9f9f9; color: #666;">Nenhuma imagem selecionada</div>'
			);
			$('#sg-upload-logo-btn').text('Selecionar Logo da Biblioteca');
			$(this).remove();
		});
		
		// ========== GERENCIAMENTO DE IMAGENS DO BANNER ==========
		var bannerUploaders = {};
		
		// Função para atualizar o campo hidden com todas as imagens
		function updateBannerImagesField() {
			var imageIds = [];
			$('.sg-banner-image-item').each(function() {
				var $item = $(this);
				var imgId = $item.data('image-id');
				if (imgId && imgId > 0) {
					imageIds.push(imgId);
				}
			});
			$('#sg_home_banner_images').val(imageIds.join(','));
		}
		
		// Botão de upload de banner
		$(document).on('click', '.sg-upload-banner-btn', function(e) {
			e.preventDefault();
			var index = $(this).data('index');
			var $item = $('.sg-banner-image-item[data-index="' + index + '"]');
			
			// Criar ou reutilizar uploader
			if (!bannerUploaders[index]) {
				bannerUploaders[index] = wp.media({
					title: 'Selecionar Imagem do Banner ' + (parseInt(index) + 1),
					button: {
						text: 'Usar esta imagem'
					},
					multiple: false,
					library: {
						type: 'image'
					}
				});
				
				// Quando uma imagem é selecionada
				bannerUploaders[index].on('select', function() {
					var attachment = bannerUploaders[index].state().get('selection').first().toJSON();
					
					// Salvar ID da imagem no elemento
					$item.data('image-id', attachment.id);
					
					// Atualizar preview
					var $preview = $item.find('.sg-banner-image-preview');
					$preview.html('<img src="' + attachment.url + '" alt="Banner ' + (parseInt(index) + 1) + '" style="width: 100%; height: 100%; object-fit: cover;" />');
					
					// Atualizar texto do botão
					$(this).find('.sg-upload-banner-btn').text('Alterar');
					
					// Mostrar botão de remover se não existir
					if ($item.find('.sg-remove-banner-btn').length === 0) {
						$item.find('.sg-upload-banner-btn').after(
							'<button type="button" class="button sg-remove-banner-btn" data-index="' + index + '">Remover</button>'
						);
					}
					
					// Atualizar campo hidden
					updateBannerImagesField();
				});
			}
			
			// Abrir uploader
			bannerUploaders[index].open();
		});
		
		// Botão de remover banner
		$(document).on('click', '.sg-remove-banner-btn', function(e) {
			e.preventDefault();
			var index = $(this).data('index');
			var $item = $('.sg-banner-image-item[data-index="' + index + '"]');
			
			// Limpar dados
			$item.data('image-id', '');
			
			// Atualizar preview
			$item.find('.sg-banner-image-preview').html(
				'<span style="color: #666; font-size: 14px;">Nenhuma imagem</span>'
			);
			
			// Atualizar texto do botão
			$item.find('.sg-upload-banner-btn').text('Selecionar');
			
			// Remover botão de remover
			$(this).remove();
			
			// Atualizar campo hidden
			updateBannerImagesField();
		});
		
		// Carregar IDs existentes nos elementos ao carregar a página
		<?php if ( ! empty( $banner_image_ids ) ) : ?>
			<?php foreach ( $banner_image_ids as $idx => $img_id ) : ?>
				$('.sg-banner-image-item[data-index="<?php echo $idx; ?>"]').data('image-id', <?php echo $img_id; ?>);
			<?php endforeach; ?>
		<?php endif; ?>
	});
	</script>
	<?php
}

/**
 * Enqueue scripts e estilos para a página de configurações
 */
function sg_settings_enqueue_scripts( $hook ) {
	// Carregar apenas na página de configurações
	if ( 'toplevel_page_sg-juridico-settings' !== $hook ) {
		return;
	}
	
	// Enqueue WordPress media uploader
	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'sg_settings_enqueue_scripts' );

/**
 * Obter imagens do banner configuradas ou imagens padrão da biblioteca
 */
function sg_get_home_banner_images() {
	// Obter imagens configuradas
	$banner_images_str = get_option( 'sg_home_banner_images', '' );
	$banner_image_ids = array();
	
	if ( ! empty( $banner_images_str ) ) {
		$ids = explode( ',', $banner_images_str );
		foreach ( $ids as $id ) {
			$id = absint( trim( $id ) );
			if ( $id > 0 ) {
				// Verificar se a imagem ainda existe antes de adicionar
				$image_url = wp_get_attachment_image_url( $id, 'full' );
				if ( $image_url ) {
					$banner_image_ids[] = $id;
				}
			}
		}
	}
	
	// Limitar a 3 imagens
	$banner_image_ids = array_slice( $banner_image_ids, 0, 3 );
	
	return $banner_image_ids;
}

/**
 * Converter URLs de produção para localhost quando necessário
 * Isso resolve o problema de imagens não carregarem localmente quando o banco foi migrado de produção
 */
function sg_convert_production_urls_to_localhost( $url ) {
	// Se não for uma URL completa, retornar como está
	if ( ! is_string( $url ) || empty( $url ) ) {
		return $url;
	}
	
	// Verificar se estamos em ambiente local (localhost)
	$home_url = home_url();
	$is_local = (
		strpos( $home_url, 'localhost' ) !== false ||
		strpos( $home_url, '127.0.0.1' ) !== false ||
		strpos( $home_url, 'local' ) !== false ||
		( defined( 'WP_ENVIRONMENT_TYPE' ) && WP_ENVIRONMENT_TYPE === 'local' )
	);
	
	// Se não estiver em ambiente local, retornar URL original
	if ( ! $is_local ) {
		return $url;
	}
	
	// Converter URLs de produção para localhost
	$production_domains = array(
		'https://sgjuridico.com.br',
		'http://sgjuridico.com.br',
		'sgjuridico.com.br'
	);
	
	foreach ( $production_domains as $domain ) {
		if ( strpos( $url, $domain ) !== false ) {
			// Substituir pelo domínio local
			$url = str_replace( $domain, $home_url, $url );
			break;
		}
	}
	
	return $url;
}

// Aplicar filtro em todas as URLs de attachments
add_filter( 'wp_get_attachment_url', 'sg_convert_production_urls_to_localhost', 10, 1 );
add_filter( 'attachment_link', 'sg_convert_production_urls_to_localhost', 10, 1 );

// Filtrar URLs dentro de wp_get_attachment_image_src
add_filter( 'wp_get_attachment_image_src', function( $image, $attachment_id, $size, $icon ) {
	if ( is_array( $image ) && ! empty( $image[0] ) ) {
		$image[0] = sg_convert_production_urls_to_localhost( $image[0] );
	}
	return $image;
}, 10, 4 );

// Filtrar URLs retornadas por wp_get_attachment_image_url
add_filter( 'wp_get_attachment_image_url', 'sg_convert_production_urls_to_localhost', 10, 1 );

