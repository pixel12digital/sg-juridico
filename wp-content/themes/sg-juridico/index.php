<?php
/**
 * Main template file
 *
 * @package SG_Juridico
 */

get_header();
?>

<main id="main" class="site-main">
	<div class="container">

		<div class="site-main-wrapper">
			<div class="posts-container">
				<?php if ( is_home() || is_front_page() ) : ?>
					<section class="home-banner home-carousel" aria-label="Destaques">
						<div class="carousel-track">
							<article class="carousel-slide is-active banner-slide-text">
								<div class="banner-content-wrapper">
									<h1 class="banner-title">DIRECIONE SEU ESTUDO COM O MÉTODO SG!</h1>
									<p class="banner-subtitle">Um método eficaz para alcançar sua aprovação!</p>
									<p>Conheça abaixo os materiais que vão transformar sua maneira de estudar e acelerar sua aprovação nos principais concursos jurídicos.</p>
									<div class="banner-cta-wrapper">
										<?php if ( class_exists( 'WooCommerce' ) ) : ?>
											<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="banner-cta-primary">Explorar Materiais</a>
										<?php else : ?>
											<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="banner-cta-primary">Explorar Materiais</a>
										<?php endif; ?>
									</div>
								</div>
							</article>
							<?php if ( class_exists( 'WooCommerce' ) ) : ?>
							<article class="carousel-slide banner-categories-slide">
								<div class="banner-content-wrapper">
									<h2 class="banner-title">Compre por categorias</h2>
									<div class="banner-categories-grid">
										<?php
										// A função sg_cat_icon_svg está definida no functions.php
										$cat_names = array( 'ministerio-publico', 'magistratura', 'delegado', 'enam', 'procuradorias' );
										$categories = array();
										foreach ( $cat_names as $slug ) {
											$term = get_term_by( 'slug', $slug, 'product_cat' );
											if ( $term && ! is_wp_error( $term ) ) {
												$categories[] = $term;
											}
										}
										if ( empty( $categories ) ) {
											$categories = get_terms( array(
												'taxonomy'   => 'product_cat',
												'hide_empty' => false,
												'parent'     => 0,
												'number'     => 5,
											) );
										}
										foreach ( $categories as $category ) :
											$cat_link = get_term_link( $category, 'product_cat' );
											?>
											<a href="<?php echo esc_url( $cat_link ); ?>" class="banner-category-card">
												<div class="category-icon" data-cat="<?php echo esc_attr( $category->slug ); ?>">
													<?php echo sg_cat_icon_svg( $category->slug ); ?>
												</div>
												<h3 class="category-name"><?php echo esc_html( $category->name ); ?></h3>
											</a>
											<?php
										endforeach;
										?>
										<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="banner-category-card banner-category-view-all">
											<div class="category-icon category-icon-view-all">
												<span>+</span>
											</div>
											<h3 class="category-name">Ver tudo</h3>
										</a>
									</div>
								</div>
							</article>
							<?php endif; ?>
                            <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                                <?php
                                $products = wc_get_products( array(
                                    'limit'   => 3,
                                    'status'  => 'publish',
                                    'featured'=> true,
                                ) );
                                if ( empty( $products ) ) {
                                    $products = wc_get_products( array(
                                        'limit'  => 3,
                                        'status' => 'publish',
                                        'orderby'=> 'date',
                                        'order'  => 'DESC',
                                    ) );
                                }
                                foreach ( $products as $product ) :
                                    $post_object = get_post( $product->get_id() );
                                    setup_postdata( $GLOBALS['post'] =& $post_object );
                                    $permalink = get_permalink( $product->get_id() );
                                    ?>
                                    <article class="carousel-slide banner-product-slide">
                                        <div class="banner-product">
                                            <div class="product-thumb">
                                                <?php echo $product->get_image( 'woocommerce_thumbnail' ); ?>
                                            </div>
                                            <div class="product-info">
                                                <h3 class="product-title"><?php echo esc_html( $product->get_name() ); ?></h3>
                                                <div class="product-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
                                                <?php 
                                                    $short = wp_strip_all_tags( $product->get_short_description() );
                                                    if ( empty( $short ) ) { $short = wp_trim_words( wp_strip_all_tags( get_the_content() ), 20 ); }
                                                ?>
                                                <?php if ( $short ) : ?>
                                                    <p class="product-excerpt"><?php echo esc_html( $short ); ?></p>
                                                <?php endif; ?>
                                                <div class="product-cta">
                                                    <a href="<?php echo esc_url( $permalink ); ?>" class="banner-cta-primary">Ver detalhes</a>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                    <?php
                                endforeach;
                                wp_reset_postdata();
                                ?>
                            <?php endif; ?>
							<article class="carousel-slide banner-slide-text">
								<div class="banner-content-wrapper">
									<h2 class="banner-title">Calendário de Concursos sempre atualizado</h2>
									<p class="banner-subtitle">Visualize prazos, provas e inscrições em um só lugar.</p>
									<p>Planeje seus estudos com antecedência e maximize resultados. Acompanhe todas as datas importantes dos principais concursos jurídicos e não perca nenhuma oportunidade.</p>
									<div class="banner-cta-wrapper">
										<a href="<?php echo esc_url( home_url( '/eventos' ) ); ?>" class="banner-cta-primary">Ver Calendário</a>
									</div>
								</div>
							</article>
						</div>
						<button class="carousel-prev" aria-label="Anterior">‹</button>
						<button class="carousel-next" aria-label="Próximo">›</button>
						<div class="carousel-dots" role="tablist" aria-label="Navegação do carrossel"></div>
					</section>
					<script>
					(function(){
						function initCarousel() {
							var root=document.querySelector('.home-carousel');
							if(!root) return;
							var track=root.querySelector('.carousel-track');
							var slides=[].slice.call(root.querySelectorAll('.carousel-slide'));
							var prev=root.querySelector('.carousel-prev');
							var next=root.querySelector('.carousel-next');
							var dots=root.querySelector('.carousel-dots');
							if(!track || !slides.length || !prev || !next || !dots) return;
							var idx=0, timer=null, DUR=6000;
							function renderDots(){
								dots.innerHTML='';
								slides.forEach(function(_,i){
									var b=document.createElement('button');
									b.className='carousel-dot'+(i===idx?' is-active':'');
									b.setAttribute('aria-label','Ir para slide '+(i+1));
									b.setAttribute('type','button');
									b.addEventListener('click',function(){go(i)});
									dots.appendChild(b);
								});
							}
							function go(i){
								idx=(i+slides.length)%slides.length;
								track.style.transform='translateX(' + (-idx*100) + '%)';
								slides.forEach(function(s,j){ 
									s.classList.toggle('is-active', j===idx); 
								});
								renderDots();
								reset();
							}
							function nextFn(){ go(idx+1); }
							function prevFn(){ go(idx-1); }
							function reset(){ clearInterval(timer); timer=setInterval(nextFn, DUR); }
							prev.addEventListener('click', prevFn);
							next.addEventListener('click', nextFn);
							root.addEventListener('mouseenter', function(){ clearInterval(timer); });
							root.addEventListener('mouseleave', function(){ reset(); });
							window.addEventListener('visibilitychange', function(){ 
								document.hidden?clearInterval(timer):reset();
							});
							renderDots(); 
							reset();
						}
						if(document.readyState==='loading'){
							document.addEventListener('DOMContentLoaded',initCarousel);
						}else{
							initCarousel();
						}
					})();
					</script>
				<?php endif; ?>
				<?php if ( have_posts() ) : ?>
					<?php if ( is_singular() ) : ?>
						<?php
						while ( have_posts() ) :
							the_post();
							get_template_part( 'template-parts/content', get_post_type() );
						endwhile;
						?>
					<?php else : ?>
						<div class="posts-list">
							<?php
							while ( have_posts() ) :
								the_post();
								get_template_part( 'template-parts/content', get_post_type() );
							endwhile;
							?>
						</div>

						<?php
						// Pagination (somente em listas)
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
					<?php endif; ?>
				<?php else : ?>
					<?php get_template_part( 'template-parts/content', 'none' ); ?>
				<?php endif; ?>
			</div>

				<?php get_sidebar(); ?>
		</div>
	</div>
</main>

<?php
get_footer();

