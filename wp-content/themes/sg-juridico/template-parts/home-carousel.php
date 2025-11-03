<?php
/**
 * Template part for displaying the home page carousel
 * This carousel is fed by banner images from admin panel settings
 *
 * @package SG_Juridico
 */

// Obter imagens do banner do painel diretamente da opção
$banner_images_str = get_option( 'sg_home_banner_images', '' );
$banner_image_ids = array();

if ( ! empty( $banner_images_str ) ) {
	$ids = explode( ',', $banner_images_str );
	foreach ( $ids as $id ) {
		$id = absint( trim( $id ) );
		if ( $id > 0 ) {
			// Verificar se a imagem ainda existe
			$image_url = wp_get_attachment_image_url( $id, 'full' );
			if ( $image_url ) {
				$banner_image_ids[] = $id;
			}
		}
	}
}

// Não renderizar o carrossel quando não houver banners cadastrados no painel
if ( empty( $banner_image_ids ) || ! is_array( $banner_image_ids ) ) {
	return;
}
?>

<section class="home-banner home-carousel" aria-label="Destaques">
	<div class="carousel-track">
		<?php foreach ( $banner_image_ids as $index => $image_id ) : ?>
			<?php
			$image_url = wp_get_attachment_image_url( $image_id, 'full' );
			$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
			
			if ( empty( $image_alt ) ) {
				$image_alt = get_the_title( $image_id );
			}
			
			if ( empty( $image_alt ) ) {
				$image_alt = sprintf( __( 'Banner %d', 'sg-juridico' ), $index + 1 );
			}
			
			$is_active = $index === 0 ? 'is-active' : '';
			
			if ( $image_url ) :
				?>
				<article class="carousel-slide <?php echo esc_attr( $is_active ); ?> banner-slide-image">
					<div class="banner-image-wrapper">
						<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>" />
					</div>
				</article>
				<?php
			endif;
		?>
		<?php endforeach; ?>
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

