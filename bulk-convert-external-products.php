<?php
/**
 * Script para converter produtos externos em produtos simples (compra interna).
 *
 * Execute pela linha de comando:
 *   php bulk-convert-external-products.php --dry-run
 *   php bulk-convert-external-products.php
 *
 * O modo --dry-run apenas relata as mudanças sem aplicá-las.
 */

define( 'WP_USE_THEMES', false );
require_once __DIR__ . '/wp-load.php';

if ( ! function_exists( 'wc_get_product' ) ) {
	fwrite( STDERR, "WooCommerce não está carregado.\n" );
	exit( 1 );
}

$dry_run = in_array( '--dry-run', $argv, true );

$query = new WP_Query(
	[
		'post_type'      => 'product',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'tax_query'      => [
			[
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => 'external',
			],
		],
	]
);

$count = $query->post_count;

printf( "Encontrados %d produtos externos.\n", $count );

if ( 0 === $count ) {
	exit( 0 );
}

if ( $dry_run ) {
	printf( "Modo --dry-run ativo: nenhuma alteração será gravada.\n" );
}

foreach ( $query->posts as $post ) {
	$product = wc_get_product( $post->ID );

	if ( ! $product || 'external' !== $product->get_type() ) {
		printf( "Ignorado #%-5d: tipo atual %s\n", $post->ID, $product ? $product->get_type() : 'desconhecido' );
		continue;
	}

	printf( "Convertendo #%-5d | %s\n", $product->get_id(), $product->get_name() );

	if ( $dry_run ) {
		continue;
	}

	wp_set_object_terms( $product->get_id(), 'simple', 'product_type', false );
	delete_post_meta( $product->get_id(), '_product_url' );
	delete_post_meta( $product->get_id(), '_button_text' );

	if ( '' === get_post_meta( $product->get_id(), '_stock_status', true ) ) {
		update_post_meta( $product->get_id(), '_stock_status', 'instock' );
	}

	if ( '' === get_post_meta( $product->get_id(), '_manage_stock', true ) ) {
		update_post_meta( $product->get_id(), '_manage_stock', 'no' );
	}

	wc_delete_product_transients( $product->get_id() );
}

wp_cache_flush();

if ( ! $dry_run ) {
	printf( "Conversão concluída. Revise alguns produtos e teste o fluxo de compra.\n" );
} else {
	printf( "Dry run finalizado. Rode novamente sem --dry-run para aplicar.\n" );
}


