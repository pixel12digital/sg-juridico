<?php
/**
 * Template part for displaying the home page carousel
 * This carousel is fed by admin panel settings
 *
 * @package SG_Juridico
 */

// Obter configurações do carrossel do painel
$carousel_data = get_option( 'sg_home_carousel_items', array() );
?>

<section class="home-banner home-carousel" aria-label="Destaques">
	<div class="carousel-track">
		<?php if ( ! empty( $carousel_data ) && is_array( $carousel_data ) ) : ?>
			<?php foreach ( $carousel_data as $index => $item ) : ?>
				<?php
				$slide_type = isset( $item['type'] ) ? $item['type'] : 'text';
				$is_active = $index === 0 ? 'is-active' : '';
				?>
				
				<?php if ( $slide_type === 'text' ) : ?>
					<article class="carousel-slide <?php echo esc_attr( $is_active ); ?> banner-slide-text">
						<div class="banner-content-wrapper">
							<?php if ( ! empty( $item['title'] ) ) : ?>
								<h1 class="banner-title"><?php echo esc_html( $item['title'] ); ?></h1>
							<?php endif; ?>
							
							<?php if ( ! empty( $item['subtitle'] ) ) : ?>
								<p class="banner-subtitle"><?php echo esc_html( $item['subtitle'] ); ?></p>
							<?php endif; ?>
							
							<?php if ( ! empty( $item['description'] ) ) : ?>
								<p><?php echo esc_html( $item['description'] ); ?></p>
							<?php endif; ?>
							
							<?php if ( ! empty( $item['button_text'] ) && ! empty( $item['button_link'] ) ) : ?>
								<div class="banner-cta-wrapper">
									<a href="<?php echo esc_url( $item['button_link'] ); ?>" class="banner-cta-primary"><?php echo esc_html( $item['button_text'] ); ?></a>
								</div>
							<?php endif; ?>
						</div>
					</article>
				
				<?php elseif ( $slide_type === 'categories' && class_exists( 'WooCommerce' ) ) : ?>
					<article class="carousel-slide <?php echo esc_attr( $is_active ); ?> banner-categories-slide">
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
				
				<?php elseif ( $slide_type === 'product' && class_exists( 'WooCommerce' ) ) : ?>
					<?php
					$product_id = isset( $item['product_id'] ) ? intval( $item['product_id'] ) : 0;
					if ( $product_id > 0 ) {
						$product = wc_get_product( $product_id );
						if ( $product ) {
							?>
							<article class="carousel-slide <?php echo esc_attr( $is_active ); ?> banner-product-slide">
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
											<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="banner-cta-primary">Ver detalhes</a>
										</div>
									</div>
								</div>
							</article>
							<?php
						}
					}
					?>
				
				<?php elseif ( $slide_type === 'image' ) : ?>
					<?php
					$image_id = isset( $item['image_id'] ) ? intval( $item['image_id'] ) : 0;
					$image_url = wp_get_attachment_image_url( $image_id, 'full' );
					$image_link = isset( $item['image_link'] ) ? $item['image_link'] : '';
					if ( $image_id > 0 && $image_url ) {
						?>
						<article class="carousel-slide <?php echo esc_attr( $is_active ); ?> banner-slide-image">
							<?php if ( ! empty( $image_link ) ) : ?>
								<a href="<?php echo esc_url( $image_link ); ?>">
							<?php endif; ?>
							<div class="banner-image-wrapper">
								<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( isset( $item['title'] ) ? $item['title'] : '' ); ?>" loading="lazy" />
								<?php if ( ! empty( $item['title'] ) || ! empty( $item['subtitle'] ) ) : ?>
									<div class="banner-image-overlay">
										<?php if ( ! empty( $item['title'] ) ) : ?>
											<h2 class="banner-title"><?php echo esc_html( $item['title'] ); ?></h2>
										<?php endif; ?>
										<?php if ( ! empty( $item['subtitle'] ) ) : ?>
											<p class="banner-subtitle"><?php echo esc_html( $item['subtitle'] ); ?></p>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
							<?php if ( ! empty( $image_link ) ) : ?>
								</a>
							<?php endif; ?>
						</article>
						<?php
					}
					?>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php else : ?>
			<?php
			// Fallback: slides padrão caso não haja configuração no painel
			?>
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
		<?php endif; ?>
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

