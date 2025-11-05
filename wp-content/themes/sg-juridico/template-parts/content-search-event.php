<?php
/**
 * Template part for displaying event results in search pages
 *
 * @package SG_Juridico
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_type = get_post_type();
$post_id = get_the_ID();

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
	$meta_key_address = '_sg_evento_endereco';
} else {
	// ETN
	$meta_key_start = 'etn_start_date';
	$meta_key_end = 'etn_end_date';
	$meta_key_location = 'etn_location';
	$meta_key_address = '_sg_evento_endereco';
}

$start_date = get_post_meta( $post_id, $meta_key_start, true );
$end_date = get_post_meta( $post_id, $meta_key_end, true );
$location = get_post_meta( $post_id, $meta_key_location, true );
$address = get_post_meta( $post_id, $meta_key_address, true );

// Carregar horários e datas de inscrição
$hora_inicio = get_post_meta( $post_id, '_sg_evento_hora_inicio', true );
$hora_fim = get_post_meta( $post_id, '_sg_evento_hora_fim', true );
$data_inscricao_inicio = get_post_meta( $post_id, '_sg_evento_inscricao_inicio', true );
$data_inscricao_fim = get_post_meta( $post_id, '_sg_evento_inscricao_fim', true );

// Converter datas para formato legível
if ( $post_type === 'tribe_events' && ! empty( $start_date ) ) {
	if ( strpos( $start_date, ' ' ) !== false ) {
		$start_date = date( 'd/m/Y', strtotime( $start_date ) );
	} elseif ( is_numeric( $start_date ) ) {
		$start_date = date( 'd/m/Y', $start_date );
	}
}
if ( $post_type === 'tribe_events' && ! empty( $end_date ) ) {
	if ( strpos( $end_date, ' ' ) !== false ) {
		$end_date = date( 'd/m/Y', strtotime( $end_date ) );
	} elseif ( is_numeric( $end_date ) ) {
		$end_date = date( 'd/m/Y', $end_date );
	}
}

if ( ! empty( $start_date ) && ! preg_match( '/^\d{2}\/\d{2}\/\d{4}$/', $start_date ) ) {
	$start_date = date_i18n( 'd/m/Y', strtotime( $start_date ) );
}
if ( ! empty( $end_date ) && ! preg_match( '/^\d{2}\/\d{2}\/\d{4}$/', $end_date ) ) {
	$end_date = date_i18n( 'd/m/Y', strtotime( $end_date ) );
}

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

// Detectar categoria
$event_category = '';
if ( $post_type === 'sg_eventos' ) {
	$terms = get_the_terms( $post_id, 'sg_evento_categoria' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		$event_category = $terms[0]->name;
	}
} else {
	$categoria_slug = get_post_meta( $post_id, '_sg_evento_categoria', true );
	$categorias = array(
		'ministerio-publico' => 'Ministério Público',
		'magistratura' => 'Magistratura',
		'delegado' => 'Delegado',
		'enam' => 'ENAM',
		'procuradoria' => 'Procuradoria',
	);
	
	if ( ! empty( $categoria_slug ) && isset( $categorias[ $categoria_slug ] ) ) {
		$event_category = $categorias[ $categoria_slug ];
	} else {
		// Tentar detectar pelo título
		$detected_slug = sg_detect_event_category( get_the_title() );
		if ( isset( $categorias[ $detected_slug ] ) ) {
			$event_category = $categorias[ $detected_slug ];
		}
	}
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'search-result-event' ); ?> style="margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: white;">
	<header class="entry-header" style="margin-bottom: 15px;">
		<?php the_title( sprintf( '<h2 class="entry-title" style="margin-bottom: 10px; font-size: 24px;"><a href="%s" rel="bookmark" style="text-decoration: none; color: #333;">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
		
		<?php if ( ! empty( $event_category ) ) : ?>
			<span class="event-category" style="display: inline-block; padding: 5px 12px; background-color: #0ea5e9; color: white; border-radius: 3px; font-size: 12px; font-weight: bold;">
				<?php echo esc_html( $event_category ); ?>
			</span>
		<?php endif; ?>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="event-thumbnail" style="margin-bottom: 15px;">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'medium', array( 'style' => 'max-width: 100%; height: auto; border-radius: 5px;' ) ); ?>
			</a>
		</div>
	<?php endif; ?>

	<div class="entry-summary" style="margin-bottom: 15px;">
		<?php the_excerpt(); ?>
	</div>

	<div class="event-details" style="margin-bottom: 20px; padding: 15px; background-color: #f5f5f5; border-radius: 5px;">
		<?php if ( $start_date ) : ?>
			<div class="event-detail-item" style="margin-bottom: 8px;">
				<strong>📅 Data da Realização:</strong> <?php echo esc_html( $start_date ); ?>
				<?php if ( ! empty( $hora_inicio ) ) : ?>
					 às <?php echo esc_html( $hora_inicio ); ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		
		<?php if ( ! empty( $hora_fim ) ) : ?>
			<div class="event-detail-item" style="margin-bottom: 8px;">
				<strong>⏰ Horário de Término:</strong> <?php echo esc_html( $hora_fim ); ?>
			</div>
		<?php endif; ?>
		
		<?php if ( ! empty( $data_inscricao_inicio ) || ! empty( $data_inscricao_fim ) ) : ?>
			<div class="event-detail-item" style="margin-bottom: 8px;">
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
		
		<?php if ( $end_date && $end_date !== $start_date ) : ?>
			<div class="event-detail-item" style="margin-bottom: 8px;">
				<strong>📅 Data de Término:</strong> <?php echo esc_html( $end_date ); ?>
			</div>
		<?php endif; ?>
		
		<?php if ( $location ) : ?>
			<div class="event-detail-item" style="margin-bottom: 8px;">
				<strong>📍 Local:</strong> <?php echo esc_html( $location ); ?>
			</div>
		<?php endif; ?>
		
		<?php if ( $address ) : ?>
			<div class="event-detail-item" style="margin-bottom: 8px;">
				<strong>📍 Endereço:</strong> <?php echo esc_html( $address ); ?>
			</div>
		<?php endif; ?>
	</div>

	<footer class="entry-footer">
		<a href="<?php the_permalink(); ?>" class="btn-read-more" style="display: inline-block; padding: 10px 25px; background-color: #0ea5e9; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
			<?php esc_html_e( 'Ver detalhes completos', 'sg-juridico' ); ?>
		</a>
	</footer>
</article>

