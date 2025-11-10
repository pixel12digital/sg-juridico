<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * @package SG_Juridico
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<?php
$price_html      = $product->get_price_html();
$price_readable  = wp_strip_all_tags( $price_html );
$product_name    = $product->get_name();
$product_link    = $product->get_permalink();
$details_label   = sprintf(
	/* translators: %s: product name */
	__( 'Ver detalhes do produto %s', 'sg-juridico' ),
	$product_name
);
?>
<li <?php wc_product_class( 'product-item', $product ); ?>>
	<a href="<?php echo esc_url( $product_link ); ?>" class="product-link">
		<div class="product-image">
			<?php echo $product->get_image( 'woocommerce_thumbnail' ); ?>
			<?php if ( $product->is_on_sale() ) : ?>
				<span class="product-badge sale">Oferta</span>
			<?php endif; ?>
			<?php if ( $product->is_featured() ) : ?>
				<span class="product-badge featured">Destaque</span>
			<?php endif; ?>
		</div>
		<div class="product-info">
			<h3 class="product-title"><?php echo esc_html( $product_name ); ?></h3>
			<div
				class="product-price"
				data-price="<?php echo esc_attr( $price_readable ); ?>"
				aria-label="<?php echo esc_attr( sprintf( __( 'Preço: %s', 'sg-juridico' ), $price_readable ) ); ?>"
			>
				<span class="screen-reader-text"><?php esc_html_e( 'Preço:', 'sg-juridico' ); ?></span>
				<?php echo wp_kses_post( $price_html ); ?>
			</div>
			<?php if ( $product->get_short_description() ) : ?>
				<p class="product-excerpt"><?php echo wp_trim_words( wp_strip_all_tags( $product->get_short_description() ), 15 ); ?></p>
			<?php endif; ?>
		</div>
	</a>
	<a
		href="<?php echo esc_url( $product_link ); ?>"
		class="product-add-to-cart-btn"
		aria-label="<?php echo esc_attr( $details_label ); ?>"
	>
		<span><?php esc_html_e( 'Ver detalhes', 'sg-juridico' ); ?></span>
	</a>
</li>


