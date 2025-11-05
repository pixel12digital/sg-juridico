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
			// Verificar se a imagem ainda existe usando wp_get_attachment_image_src
			$image_data = wp_get_attachment_image_src( $id, 'full' );
			if ( $image_data && ! empty( $image_data[0] ) ) {
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
			// Usar wp_get_attachment_image_src para obter informações mais completas
			$image_data = wp_get_attachment_image_src( $image_id, 'full' );
			
			if ( ! $image_data || empty( $image_data[0] ) ) {
				continue;
			}
			
			$image_url = $image_data[0];
			$image_width = isset( $image_data[1] ) ? $image_data[1] : '';
			$image_height = isset( $image_data[2] ) ? $image_data[2] : '';
			
			$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
			
			if ( empty( $image_alt ) ) {
				$image_alt = get_the_title( $image_id );
			}
			
			if ( empty( $image_alt ) ) {
				$image_alt = sprintf( __( 'Banner %d', 'sg-juridico' ), $index + 1 );
			}
			
			$is_active = $index === 0 ? 'is-active' : '';
			?>
			<article class="carousel-slide <?php echo esc_attr( $is_active ); ?> banner-slide-image">
				<div class="banner-image-wrapper">
					<img src="<?php echo esc_url( $image_url ); ?>" 
						 alt="<?php echo esc_attr( $image_alt ); ?>" 
						 <?php if ( $image_width ) : ?>width="<?php echo esc_attr( $image_width ); ?>"<?php endif; ?>
						 <?php if ( $image_height ) : ?>height="<?php echo esc_attr( $image_height ); ?>"<?php endif; ?>
						 loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>" 
						 data-banner-image="true" 
						 onerror="this.style.display='block'; this.style.visibility='visible'; this.style.opacity='1';" />
				</div>
			</article>
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
		
		// Função para converter URLs de produção para localhost
		function convertProductionUrlToLocalhost(url) {
			if (!url) return url;
			var productionDomains = ['https://sgjuridico.com.br', 'http://sgjuridico.com.br', 'sgjuridico.com.br'];
			var currentHost = window.location.origin;
			var isLocal = currentHost.indexOf('localhost') !== -1 || currentHost.indexOf('127.0.0.1') !== -1 || currentHost.indexOf('local') !== -1;
			
			if (!isLocal) return url;
			
			for (var i = 0; i < productionDomains.length; i++) {
				if (url.indexOf(productionDomains[i]) !== -1) {
					url = url.replace(productionDomains[i], currentHost);
					break;
				}
			}
			return url;
		}
		
		// Garantir que imagens do banner sejam exibidas
		var bannerImages = root.querySelectorAll('img[data-banner-image]');
		bannerImages.forEach(function(img) {
			// Converter URL de produção para localhost se necessário
			if (img.src) {
				var convertedSrc = convertProductionUrlToLocalhost(img.src);
				if (convertedSrc !== img.src) {
					img.src = convertedSrc;
				}
			}
			
			// Remover qualquer estilo que possa estar ocultando a imagem
			img.style.display = 'block';
			img.style.visibility = 'visible';
			img.style.opacity = '1';
			
			// Garantir que o atributo data-error-handled não interfira
			img.removeAttribute('data-error-handled');
			
			// Verificar se a imagem carregou corretamente
			if (!img.complete || img.naturalHeight === 0) {
				// Se a imagem não carregou, tentar recarregar
				var originalSrc = img.src;
				img.onerror = function() {
					// Tentar converter URL novamente e recarregar
					var retrySrc = convertProductionUrlToLocalhost(originalSrc);
					if (this.dataset.retry !== 'true') {
						this.dataset.retry = 'true';
						this.src = '';
						setTimeout(function() {
							img.src = retrySrc;
						}, 100);
					} else {
						// Se ainda falhar, garantir que a imagem seja visível mesmo assim
						this.style.display = 'block';
						this.style.visibility = 'visible';
						this.style.opacity = '1';
					}
				};
				
				// Forçar reload se necessário
				if (img.src && !img.complete) {
					var forceReload = img.src.split('?')[0] + '?t=' + new Date().getTime();
					img.src = forceReload;
				}
			}
		});
		
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

