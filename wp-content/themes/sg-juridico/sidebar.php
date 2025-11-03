<?php
/**
 * Sidebar template
 *
 * @package SG_Juridico
 */

// Verificar se é página de contato
$is_contact_page = (
	is_page_template( 'page-contato.php' ) || 
	is_page( 'contato' ) || 
	( is_page() && strpos( strtolower( get_the_title() ), 'contato' ) !== false )
);

// Ocultar completamente o sidebar na Finalização, na página Minha Conta (WooCommerce) e na página de Contato
if ( ( function_exists( 'is_checkout' ) && is_checkout() ) ||
     ( function_exists( 'is_account_page' ) && is_account_page() ) ||
     $is_contact_page ) {
    return;
}

// Exibir sidebar se houver calendário, widgets ou categorias com produtos
$has_calendar = function_exists( 'sg_display_concurso_calendar' );
$has_widgets = is_active_sidebar( 'sidebar-1' );
$has_woocommerce = class_exists( 'WooCommerce' );

// Verificar se há categorias com produtos para exibir
$has_category_sections = false;
if ( $has_woocommerce ) {
	$category_slugs_check = array( 'magistratura', 'defensoria-publica', 'ministerio-publico', 'enam', 'procuradorias' );
	foreach ( $category_slugs_check as $slug ) {
		$category = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $category && ! is_wp_error( $category ) ) {
			$products = wc_get_products( array(
				'limit'      => 1,
				'status'     => 'publish',
				'tax_query'  => array(
					array(
						'taxonomy' => 'product_cat',
						'field'    => 'term_id',
						'terms'    => $category->term_id,
					),
				),
			) );
			if ( ! empty( $products ) ) {
				$has_category_sections = true;
				break;
			}
		}
	}
}

if ( ! $has_calendar && ! $has_widgets && ! $has_category_sections ) {
	return;
}
?>

<aside id="secondary" class="widget-area">
	<?php
	// Exibir calendário de concursos antes dos widgets
	if ( $has_calendar ) {
		sg_display_concurso_calendar();
	}
	
	// Exibir widgets do sidebar
	if ( $has_widgets ) {
		dynamic_sidebar( 'sidebar-1' );
	}
	
	// Seções de categorias com produtos
	if ( class_exists( 'WooCommerce' ) ) {
		$category_slugs = array(
			'magistratura' => 'Magistratura',
			'defensoria-publica' => 'Defensoria Pública',
			'ministerio-publico' => 'Ministério Público',
			'enam' => 'ENAM',
			'procuradorias' => 'Procuradorias',
		);
		
		foreach ( $category_slugs as $slug => $title ) {
			$category = get_term_by( 'slug', $slug, 'product_cat' );
			if ( $category && ! is_wp_error( $category ) ) {
				$products = wc_get_products( array(
					'limit'      => 2,
					'status'     => 'publish',
					'tax_query'  => array(
						array(
							'taxonomy' => 'product_cat',
							'field'    => 'term_id',
							'terms'    => $category->term_id,
						),
					),
				) );
				
				if ( ! empty( $products ) ) {
					$category_link = get_term_link( $category, 'product_cat' );
					?>
					<div class="sidebar-category-section">
						<h3 class="sidebar-category-title">
							<a href="<?php echo esc_url( $category_link ); ?>"><?php echo esc_html( $title ); ?></a>
						</h3>
						<div class="sidebar-products-grid">
							<?php foreach ( $products as $product ) : ?>
								<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="sidebar-product-box">
									<div class="sidebar-product-image">
										<?php echo $product->get_image( 'woocommerce_thumbnail' ); ?>
									</div>
									<div class="sidebar-product-title"><?php echo esc_html( $product->get_name() ); ?></div>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
					<?php
				}
			}
		}
	}
	?>
</aside>

