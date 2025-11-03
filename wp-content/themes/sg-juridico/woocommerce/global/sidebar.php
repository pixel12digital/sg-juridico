<?php
/**
 * WooCommerce Sidebar - Filtros da Loja
 *
 * Estrutura enxuta e acessível para filtros de e-commerce modernos,
 * com fallback para widgets padrão caso a área 'shop-filters' esteja vazia.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Mostrar apenas nas telas de listagem (loja, categorias, tags, atributos)
if ( ! ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) ) {
	return;
}
?>


<aside id="shop-sidebar" class="shop-sidebar" aria-label="Filtros da loja">
	<button class="shop-filters-toggle" data-toggle="shop-filters" aria-expanded="false" aria-controls="shop-filters-panel">
		<?php echo esc_html__( 'Filtrar', 'sg-juridico' ); ?>
	</button>

	<div id="shop-filters-panel" class="shop-filters">
		<?php
		// Construir URL "limpar filtros" levando SEMPRE ao catálogo completo (página da loja)
		$shop_page_id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : 0;
		$clear_url    = $shop_page_id ? get_permalink( $shop_page_id ) : home_url( '/' );
		// Remover quaisquer parâmetros residuais
		$clear_url = remove_query_arg( array_keys( $_GET ), $clear_url ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		?>
		<a class="shop-clear-btn" href="<?php echo esc_url( $clear_url ); ?>" aria-label="<?php esc_attr_e( 'Limpar filtros', 'sg-juridico' ); ?>"><?php esc_html_e( 'Limpar filtros', 'sg-juridico' ); ?></a>
		<?php
		// 1) Se o usuário configurar widgets em "Filtros da Loja", usar essa área
		if ( is_active_sidebar( 'shop-filters' ) ) {
			dynamic_sidebar( 'shop-filters' );
		} else {
			// 2) Fallback: exibir widgets comuns de e-commerce (categorias, preço e atributos)
			if ( class_exists( 'WC_Widget_Product_Categories' ) ) {
				the_widget( 'WC_Widget_Product_Categories', array( 'title' => __( 'Categorias', 'sg-juridico' ), 'count' => 1, 'hierarchical' => 1, 'dropdown' => 0 ) );
			}
			if ( class_exists( 'WC_Widget_Price_Filter' ) ) {
				the_widget( 'WC_Widget_Price_Filter', array( 'title' => __( 'Filtrar por preço', 'sg-juridico' ) ) );
			}
			// Descobrir atributos públicos para filtros (ex.: plataforma, modalidade, banca etc.)
			if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
				$taxonomies = wc_get_attribute_taxonomies();
				if ( ! empty( $taxonomies ) ) {
					foreach ( $taxonomies as $tax ) {
						if ( ! empty( $tax->attribute_public ) ) {
							$taxonomy = wc_attribute_taxonomy_name( $tax->attribute_name );
							if ( taxonomy_exists( $taxonomy ) && class_exists( 'WC_Widget_Layered_Nav' ) ) {
								// Exibir um widget Layered Nav para cada atributo público
								the_widget( 'WC_Widget_Layered_Nav', array(
									'title'        => wc_attribute_label( $taxonomy ),
									'attribute'    => $tax->attribute_name,
									'display_type' => 'list',
									'query_type'   => 'and',
								) );
							}
						}
					}
				}
			}
		}
		?>
	</div>
</aside>

<script>
(function(){
	var btn = document.querySelector('.shop-filters-toggle');
	var panel = document.getElementById('shop-filters-panel');
	if(!btn || !panel) return;
	btn.addEventListener('click', function(){
		var expanded = this.getAttribute('aria-expanded') === 'true';
		this.setAttribute('aria-expanded', String(!expanded));
		panel.classList.toggle('is-open');
	});
})();
</script>


