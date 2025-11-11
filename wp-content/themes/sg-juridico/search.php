<?php
/**
 * Template de busca
 *
 * @package SG_Juridico
 */

get_header();
?>

<main id="main" class="site-main">
	<div class="container">
		<div class="site-main-wrapper">
			<div class="posts-container">
				<header class="page-header">
					<?php if ( have_posts() ) : ?>
						<h1 class="page-title">
							<?php
							printf(
								esc_html__( 'Resultados da pesquisa por: %s', 'sg-juridico' ),
								'<span>' . get_search_query() . '</span>'
							);
							?>
						</h1>
					<?php else : ?>
						<h1 class="page-title"><?php esc_html_e( 'Nada encontrado', 'sg-juridico' ); ?></h1>
					<?php endif; ?>
				</header>

				<?php if ( have_posts() ) : ?>
					<div class="search-results">
						<?php
						while ( have_posts() ) :
							the_post();
							$post_type = get_post_type();
							
							// Se for um evento, usar template específico
							if ( in_array( $post_type, array( 'sg_eventos', 'etn', 'tribe_events' ) ) ) {
								get_template_part( 'template-parts/content', 'search-event' );
							} else {
								get_template_part( 'template-parts/content', 'search' );
							}
						endwhile;
						?>
					</div>

					<?php
					// Botão "Ver todos os concursos" após os resultados
					?>
					<div class="search-actions" style="margin: 30px 0; text-align: center;">
						<a href="<?php echo esc_url( get_post_type_archive_link( 'etn' ) ?: home_url( '/eventos' ) ); ?>" class="btn btn-primary" style="display: inline-block; padding: 12px 30px; background-color: #0ea5e9; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
							<?php esc_html_e( 'Ver todos os concursos', 'sg-juridico' ); ?>
						</a>
					</div>

					<?php
					// Paginação
					the_posts_pagination(
						array(
							'mid_size'  => 2,
							'prev_text' => sprintf(
								'<span class="nav-prev-text">%s</span>',
								__( '← Anterior', 'sg-juridico' )
							),
							'next_text' => sprintf(
								'<span class="nav-next-text">%s</span>',
								__( 'Próximo →', 'sg-juridico' )
							),
						)
					);
					?>

					<?php
					// Produtos relacionados (WooCommerce)
					if ( class_exists( 'WooCommerce' ) ) {
						// Buscar produtos relacionados baseado nos termos de busca
						$search_query = get_search_query();
						$related_products = sg_get_related_products_for_search( $search_query );
						
						if ( ! empty( $related_products ) ) {
							?>
							<div class="related-products-section" style="margin-top: 50px;">
								<h2 class="section-title" style="margin-bottom: 30px; font-size: 24px; font-weight: bold;">
									<?php esc_html_e( 'Produtos Relacionados', 'sg-juridico' ); ?>
								</h2>
								<div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
									<?php
									foreach ( $related_products as $product_id ) {
										$product = wc_get_product( $product_id );
										if ( $product && $product->is_visible() ) {
											?>
											<div class="product-card" style="border: 1px solid #ddd; border-radius: 5px; padding: 15px; text-align: center;">
												<?php if ( $product->get_image() ) : ?>
													<div class="product-image" style="margin-bottom: 15px;">
														<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
															<?php echo $product->get_image( 'medium' ); ?>
														</a>
													</div>
												<?php endif; ?>
												<h3 class="product-title" style="margin-bottom: 10px; font-size: 16px;">
													<a href="<?php echo esc_url( $product->get_permalink() ); ?>" style="text-decoration: none; color: #333;">
														<?php echo esc_html( $product->get_name() ); ?>
													</a>
												</h3>
												<div class="product-price" style="font-size: 18px; font-weight: bold; color: #0ea5e9; margin-bottom: 15px;">
													<?php echo $product->get_price_html(); ?>
												</div>
												<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="btn-view-product" style="display: inline-block; padding: 8px 20px; background-color: #0ea5e9; color: white; text-decoration: none; border-radius: 5px;">
													<?php esc_html_e( 'Ver produto', 'sg-juridico' ); ?>
												</a>
											</div>
											<?php
										}
									}
									?>
								</div>
							</div>
							<?php
						}
					}
					?>

				<?php else : ?>
					<?php get_template_part( 'template-parts/content', 'none' ); ?>
					
					<div class="search-actions" style="margin: 30px 0; text-align: center;">
						<a href="<?php echo esc_url( get_post_type_archive_link( 'etn' ) ?: home_url( '/eventos' ) ); ?>" class="btn btn-primary" style="display: inline-block; padding: 12px 30px; background-color: #0ea5e9; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
							<?php esc_html_e( 'Ver todos os concursos', 'sg-juridico' ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>

			<?php get_sidebar(); ?>
		</div>
	</div>
</main>

<?php
get_footer();






