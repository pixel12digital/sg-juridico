<?php
/**
 * Template for displaying the front page
 * This template has priority over index.php for the home page
 *
 * @package SG_Juridico
 */

get_header();
?>

<main id="main" class="site-main">
	<div class="container">

		<div class="site-main-wrapper">
			<div class="posts-container">
				<?php
				// Carrossel alimentado pelo painel
				get_template_part( 'template-parts/home', 'carousel' );
				?>

				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<!-- Categorias Principais -->
					<section class="home-categories">
						<div class="section-header">
							<h2 class="section-title">Explore por Categorias</h2>
						</div>
						<div class="categories-inline">
							<?php
							$main_categories = get_terms( array(
								'taxonomy'   => 'product_cat',
								'hide_empty' => false,
								'parent'     => 0,
								'number'     => 10,
							) );
							
							if ( ! empty( $main_categories ) && ! is_wp_error( $main_categories ) ) :
								foreach ( $main_categories as $category ) :
									$cat_link = get_term_link( $category, 'product_cat' );
									// Usar slug e nome para melhor detecção do ícone
									$icon_identifier = $category->slug . ' ' . strtolower( $category->name );
									?>
									<a href="<?php echo esc_url( $cat_link ); ?>" class="category-item-inline">
										<div class="category-icon-small">
											<?php echo sg_cat_icon_svg( $icon_identifier ); ?>
										</div>
										<div class="category-content-inline">
											<h3 class="category-title-small"><?php echo esc_html( $category->name ); ?></h3>
											<?php if ( $category->count > 0 ) : ?>
												<span class="category-count-small"><?php echo esc_html( $category->count ); ?> <?php echo $category->count === 1 ? 'curso' : 'cursos'; ?></span>
											<?php endif; ?>
										</div>
									</a>
									<?php
								endforeach;
							endif;
							?>
						</div>
					</section>

					<!-- Produtos em Destaque -->
					<section class="home-products-featured">
						<div class="section-header">
							<h2 class="section-title">Produtos em Destaque</h2>
							<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="section-view-all">Ver todos →</a>
						</div>
						<?php
						$featured_products = wc_get_products( array(
							'limit'    => 8,
							'status'   => 'publish',
							'featured' => true,
						) );
						
						if ( empty( $featured_products ) ) {
							$featured_products = wc_get_products( array(
								'limit'   => 8,
								'status'  => 'publish',
								'orderby' => 'date',
								'order'   => 'DESC',
							) );
						}
						
						if ( ! empty( $featured_products ) ) :
						?>
							<ul class="products-grid">
								<?php foreach ( $featured_products as $product ) : ?>
									<li class="product-item">
										<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="product-link">
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
												<h3 class="product-title"><?php echo esc_html( $product->get_name() ); ?></h3>
												<div class="product-price">
													<?php echo wp_kses_post( $product->get_price_html() ); ?>
												</div>
												<?php if ( $product->get_short_description() ) : ?>
													<p class="product-excerpt"><?php echo wp_trim_words( wp_strip_all_tags( $product->get_short_description() ), 12 ); ?></p>
												<?php endif; ?>
											</div>
										</a>
										<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="product-add-to-cart-btn">
											Ver detalhes
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</section>

					<!-- Produtos Mais Vendidos -->
					<section class="home-products-best-selling">
						<div class="section-header">
							<h2 class="section-title">Mais Vendidos</h2>
							<a href="<?php echo esc_url( add_query_arg( 'orderby', 'popularity', wc_get_page_permalink( 'shop' ) ) ); ?>" class="section-view-all">Ver todos →</a>
						</div>
						<?php
						$best_selling = wc_get_products( array(
							'limit'   => 3,
							'status'  => 'publish',
							'orderby' => 'popularity',
							'order'   => 'DESC',
						) );
						
						if ( ! empty( $best_selling ) ) :
						?>
							<ul class="products-grid products-grid-small">
								<?php foreach ( $best_selling as $product ) : ?>
									<li class="product-item">
										<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="product-link">
											<div class="product-image">
												<?php echo $product->get_image( 'woocommerce_thumbnail' ); ?>
											</div>
											<div class="product-info">
												<h3 class="product-title"><?php echo esc_html( $product->get_name() ); ?></h3>
												<div class="product-price">
													<?php echo wp_kses_post( $product->get_price_html() ); ?>
												</div>
											</div>
										</a>
										<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="product-add-to-cart-btn">
											Ver detalhes
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</section>

					<!-- Seção Confira -->
					<section class="home-products-confira">
						<div class="section-header">
							<h2 class="section-title">Confira</h2>
							<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="section-view-all">Ver todos →</a>
						</div>
						<?php
						$confira_products = wc_get_products( array(
							'limit'   => 3,
							'status'  => 'publish',
							'orderby' => 'date',
							'order'   => 'DESC',
						) );
						
						if ( ! empty( $confira_products ) ) :
						?>
							<ul class="products-grid">
								<?php foreach ( $confira_products as $product ) : ?>
									<li class="product-item">
										<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="product-link">
											<div class="product-image">
												<?php echo $product->get_image( 'woocommerce_thumbnail' ); ?>
											</div>
											<div class="product-info">
												<h3 class="product-title"><?php echo esc_html( $product->get_name() ); ?></h3>
												<div class="product-price">
													<?php echo wp_kses_post( $product->get_price_html() ); ?>
												</div>
											</div>
										</a>
										<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="product-add-to-cart-btn">
											Ver detalhes
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</section>

					<!-- Banner de Promoção/CTA -->
					<section class="home-cta-banner">
						<div class="cta-banner-content">
							<h2 class="cta-title">Transforme sua preparação para concursos</h2>
							<p class="cta-text">Acesse materiais exclusivos e estratégias comprovadas para sua aprovação.</p>
							<div class="cta-buttons">
								<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="cta-button cta-primary">Ver Materiais</a>
								<a href="<?php echo esc_url( home_url( '/eventos' ) ); ?>" class="cta-button cta-secondary">Calendário de Concursos</a>
							</div>
						</div>
					</section>

					<!-- Seção de Categoria 1 -->
					<?php
					// Buscar categorias "Ministério Público" e "Magistratura" para excluí-las
					$mp_term = get_term_by( 'slug', 'ministerio-publico', 'product_cat' );
					$magistratura_term = get_term_by( 'slug', 'magistratura', 'product_cat' );
					$exclude_ids = array();
					if ( $mp_term && ! is_wp_error( $mp_term ) ) {
						$exclude_ids[] = $mp_term->term_id;
					}
					if ( $magistratura_term && ! is_wp_error( $magistratura_term ) ) {
						$exclude_ids[] = $magistratura_term->term_id;
					}
					
					// Buscar duas primeiras categorias com produtos (ordenadas por quantidade de produtos)
					$categories_with_products = get_terms( array(
						'taxonomy'   => 'product_cat',
						'hide_empty' => true,
						'parent'     => 0,
						'number'     => 2,
						'orderby'    => 'count',
						'order'      => 'DESC',
						'exclude'    => $exclude_ids,
					) );
					
					if ( ! empty( $categories_with_products ) && ! is_wp_error( $categories_with_products ) && isset( $categories_with_products[0] ) ) :
						$category1 = $categories_with_products[0];
						$products_cat1 = wc_get_products( array(
							'limit'      => 3,
							'status'     => 'publish',
							'tax_query'  => array(
								array(
									'taxonomy' => 'product_cat',
									'field'    => 'term_id',
									'terms'    => $category1->term_id,
								),
							),
						) );
						
						if ( ! empty( $products_cat1 ) ) :
							$cat1_link = get_term_link( $category1, 'product_cat' );
					?>
						<section class="home-category-products">
							<div class="section-header">
								<h2 class="section-title"><?php echo esc_html( $category1->name ); ?></h2>
								<a href="<?php echo esc_url( $cat1_link ); ?>" class="section-view-all">Ver todos →</a>
							</div>
							<ul class="products-grid products-grid-category">
								<?php foreach ( $products_cat1 as $product ) : ?>
									<li class="product-item">
										<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="product-link">
											<div class="product-image">
												<?php echo $product->get_image( 'woocommerce_thumbnail' ); ?>
												<?php if ( $product->is_on_sale() ) : ?>
													<span class="product-badge sale">Oferta</span>
												<?php endif; ?>
											</div>
											<div class="product-info">
												<h3 class="product-title"><?php echo esc_html( $product->get_name() ); ?></h3>
												<div class="product-price">
													<?php echo wp_kses_post( $product->get_price_html() ); ?>
												</div>
												<?php if ( $product->get_short_description() ) : ?>
													<p class="product-excerpt"><?php echo wp_trim_words( wp_strip_all_tags( $product->get_short_description() ), 12 ); ?></p>
												<?php endif; ?>
											</div>
										</a>
										<?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
											<form class="cart" action="<?php echo esc_url( $product->add_to_cart_url() ); ?>" method="post" enctype="multipart/form-data">
												<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="product-add-to-cart-btn">
													Ver detalhes
												</button>
											</form>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</section>
					<?php
						endif;
					endif;
					?>

					<!-- Seção de Categoria 2 -->
					<?php
					if ( ! empty( $categories_with_products ) && ! is_wp_error( $categories_with_products ) && isset( $categories_with_products[1] ) ) :
						$category2 = $categories_with_products[1];
						$products_cat2 = wc_get_products( array(
							'limit'      => 3,
							'status'     => 'publish',
							'tax_query'  => array(
								array(
									'taxonomy' => 'product_cat',
									'field'    => 'term_id',
									'terms'    => $category2->term_id,
								),
							),
						) );
						
						if ( ! empty( $products_cat2 ) ) :
							$cat2_link = get_term_link( $category2, 'product_cat' );
					?>
						<section class="home-category-products">
							<div class="section-header">
								<h2 class="section-title"><?php echo esc_html( $category2->name ); ?></h2>
								<a href="<?php echo esc_url( $cat2_link ); ?>" class="section-view-all">Ver todos →</a>
							</div>
							<ul class="products-grid products-grid-category">
								<?php foreach ( $products_cat2 as $product ) : ?>
									<li class="product-item">
										<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="product-link">
											<div class="product-image">
												<?php echo $product->get_image( 'woocommerce_thumbnail' ); ?>
												<?php if ( $product->is_on_sale() ) : ?>
													<span class="product-badge sale">Oferta</span>
												<?php endif; ?>
											</div>
											<div class="product-info">
												<h3 class="product-title"><?php echo esc_html( $product->get_name() ); ?></h3>
												<div class="product-price">
													<?php echo wp_kses_post( $product->get_price_html() ); ?>
												</div>
												<?php if ( $product->get_short_description() ) : ?>
													<p class="product-excerpt"><?php echo wp_trim_words( wp_strip_all_tags( $product->get_short_description() ), 12 ); ?></p>
												<?php endif; ?>
											</div>
										</a>
										<?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
											<form class="cart" action="<?php echo esc_url( $product->add_to_cart_url() ); ?>" method="post" enctype="multipart/form-data">
												<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="product-add-to-cart-btn">
													Ver detalhes
												</button>
											</form>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</section>
					<?php
						endif;
					endif;
					?>

					<!-- Faixa de Benefícios -->
					<section class="home-benefits-bar">
						<div class="benefits-bar-content">
							<div class="benefit-item">
								<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2023/10/cadeado-1.png.webp' ) ); ?>" alt="Segurança" class="benefit-icon">
							</div>
							<div class="benefit-item">
								<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2023/10/devolucoes-1.png.webp' ) ); ?>" alt="Devoluções" class="benefit-icon">
							</div>
							<div class="benefit-item">
								<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2023/10/caminhao-de-entrega-4.png.webp' ) ); ?>" alt="Entrega" class="benefit-icon">
							</div>
							<div class="benefit-item">
								<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2023/10/cartoes-de-credito-1.png.webp' ) ); ?>" alt="Formas de Pagamento" class="benefit-icon">
							</div>
							<div class="benefit-item">
								<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2023/10/selossl.webp' ) ); ?>" alt="SSL Seguro" class="benefit-icon">
							</div>
							<div class="benefit-item">
								<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2023/10/formas.webp' ) ); ?>" alt="Formas de Pagamento" class="benefit-icon">
							</div>
						</div>
					</section>
				<?php endif; ?>
			</div>

			<?php get_sidebar(); ?>
		</div>
	</div>
</main>

<?php
get_footer();

