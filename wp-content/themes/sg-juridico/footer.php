<footer id="colophon" class="site-footer">
	<div class="footer-main">
		<div class="container">
			<div class="footer-widgets-wrapper">
				<?php if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' ) || is_active_sidebar( 'footer-4' ) ) : ?>
					<div class="footer-widgets">
						<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
							<div class="footer-column footer-column-1">
								<?php dynamic_sidebar( 'footer-1' ); ?>
							</div>
						<?php endif; ?>
						
						<?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
							<div class="footer-column footer-column-2">
								<?php dynamic_sidebar( 'footer-2' ); ?>
							</div>
						<?php endif; ?>
						
						<?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
							<div class="footer-column footer-column-3">
								<?php dynamic_sidebar( 'footer-3' ); ?>
							</div>
						<?php endif; ?>
						
						<?php if ( is_active_sidebar( 'footer-4' ) ) : ?>
							<div class="footer-column footer-column-4">
								<?php dynamic_sidebar( 'footer-4' ); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<!-- Menu de Links Rápidos -->
				<?php if ( has_nav_menu( 'footer' ) ) : ?>
					<nav class="footer-navigation" aria-label="<?php esc_attr_e( 'Footer Menu', 'sg-juridico' ); ?>">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer',
								'menu_class'     => 'footer-menu',
								'depth'          => 1,
								'fallback_cb'    => false,
							)
						);
						?>
					</nav>
				<?php endif; ?>

				<!-- Links Úteis e Categorias da Loja -->
				<div class="footer-links-sections">
					<!-- Links Úteis -->
					<div class="footer-links-column">
						<h3 class="footer-links-title"><?php esc_html_e( 'Links Úteis', 'sg-juridico' ); ?></h3>
						<ul class="footer-links-list">
							<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Início', 'sg-juridico' ); ?></a></li>
							<?php if ( class_exists( 'WooCommerce' ) ) : ?>
								<li><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Loja', 'sg-juridico' ); ?></a></li>
							<?php endif; ?>
							<?php
							$blog_page = get_option( 'page_for_posts' );
							if ( $blog_page ) :
								?>
								<li><a href="<?php echo esc_url( get_permalink( $blog_page ) ); ?>"><?php esc_html_e( 'Blog', 'sg-juridico' ); ?></a></li>
							<?php endif; ?>
							<?php
							$sobre_page = get_page_by_path( 'sobre' );
							if ( $sobre_page ) :
								?>
								<li><a href="<?php echo esc_url( get_permalink( $sobre_page->ID ) ); ?>"><?php esc_html_e( 'Sobre', 'sg-juridico' ); ?></a></li>
							<?php endif; ?>
							<?php
							$contato_page = get_page_by_path( 'contato' );
							if ( $contato_page ) :
								?>
								<li><a href="<?php echo esc_url( get_permalink( $contato_page->ID ) ); ?>"><?php esc_html_e( 'Contato', 'sg-juridico' ); ?></a></li>
							<?php endif; ?>
							<?php
							$privacy_page = get_privacy_policy_url();
							if ( $privacy_page ) :
								?>
								<li><a href="<?php echo esc_url( $privacy_page ); ?>"><?php esc_html_e( 'Política de Privacidade', 'sg-juridico' ); ?></a></li>
							<?php endif; ?>
							<?php
							$terms_page = get_page_by_path( 'termos-de-uso' );
							if ( $terms_page ) :
								?>
								<li><a href="<?php echo esc_url( get_permalink( $terms_page->ID ) ); ?>"><?php esc_html_e( 'Termos de Uso', 'sg-juridico' ); ?></a></li>
							<?php endif; ?>
						</ul>
					</div>

					<!-- Categorias da Loja -->
					<?php if ( class_exists( 'WooCommerce' ) ) : ?>
						<div class="footer-links-column">
							<h3 class="footer-links-title"><?php esc_html_e( 'Categorias', 'sg-juridico' ); ?></h3>
							<ul class="footer-links-list">
								<?php
								$categories = get_terms( array(
									'taxonomy'   => 'product_cat',
									'hide_empty' => true,
									'parent'     => 0,
									'number'     => 10,
									'orderby'    => 'count',
									'order'      => 'DESC',
								) );
								
								if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
									foreach ( $categories as $category ) :
										$cat_link = get_term_link( $category, 'product_cat' );
										?>
										<li><a href="<?php echo esc_url( $cat_link ); ?>"><?php echo esc_html( $category->name ); ?></a></li>
										<?php
									endforeach;
								endif;
								?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Barra Inferior com Copyright e Links Legais -->
	<div class="footer-bottom">
		<div class="container">
			<div class="footer-bottom-wrapper">
				<div class="footer-bottom-left">
					<div class="footer-copyright">
						<p>&copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Todos os direitos reservados.', 'sg-juridico' ); ?></p>
					</div>
					<div class="footer-company-info">
						<?php 
						$cnpj = sg_get_company_info( 'cnpj' );
						?>
						<?php if ( $cnpj ) : ?>
							<p class="footer-cnpj">CNPJ: <span><?php echo esc_html( $cnpj ); ?></span></p>
						<?php endif; ?>
						
						<div class="footer-social-contacts">
							<?php 
							$instagram = sg_get_company_info( 'instagram' );
							$whatsapp = sg_get_company_info( 'whatsapp' );
							$whatsapp_display = sg_get_company_info( 'whatsapp_display' );
							?>
							
							<?php if ( $instagram ) : ?>
								<a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-link footer-instagram" aria-label="<?php esc_attr_e( 'Instagram', 'sg-juridico' ); ?>">
									<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="currentColor"/>
									</svg>
									<span><?php esc_html_e( 'Instagram', 'sg-juridico' ); ?></span>
								</a>
							<?php endif; ?>
							
							<?php if ( $whatsapp && $whatsapp_display ) : ?>
								<a href="<?php echo esc_url( sg_get_whatsapp_link() ); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-link footer-whatsapp" aria-label="<?php esc_attr_e( 'WhatsApp', 'sg-juridico' ); ?>">
									<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" fill="currentColor"/>
									</svg>
									<span><?php echo esc_html( $whatsapp_display ); ?></span>
								</a>
							<?php endif; ?>
						</div>
					</div>
					<div class="footer-credits">
						<p><?php esc_html_e( 'Desenvolvido por', 'sg-juridico' ); ?> <a href="https://pixel12digital.com.br" target="_blank" rel="noopener noreferrer" class="credits-link">Pixel12Digital</a></p>
					</div>
				</div>

				<div class="footer-bottom-center">
					<div class="footer-legal-links">
						<?php
						// Páginas legais importantes
						$privacy_page = get_privacy_policy_url();
						$terms_page = get_page_by_path( 'termos-de-uso' );
						$termos_url = $terms_page ? get_permalink( $terms_page->ID ) : '#';
						?>
						<ul class="legal-menu">
							<?php if ( $privacy_page ) : ?>
								<li><a href="<?php echo esc_url( $privacy_page ); ?>"><?php esc_html_e( 'Política de Privacidade', 'sg-juridico' ); ?></a></li>
							<?php endif; ?>
							<?php if ( $termos_url ) : ?>
								<li><a href="<?php echo esc_url( $termos_url ); ?>"><?php esc_html_e( 'Termos de Uso', 'sg-juridico' ); ?></a></li>
							<?php endif; ?>
							<?php if ( class_exists( 'WooCommerce' ) ) : ?>
								<li><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Loja', 'sg-juridico' ); ?></a></li>
							<?php endif; ?>
						</ul>
					</div>
				</div>

				<div class="footer-back-to-top">
					<a href="#top" class="back-to-top-btn" aria-label="<?php esc_attr_e( 'Voltar ao topo', 'sg-juridico' ); ?>">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M5 15l7-7 7 7" stroke="#000000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
						</svg>
					</a>
				</div>
			</div>
		</div>
	</div>
</footer>
</div>

<?php if ( is_home() || is_front_page() ) : ?>
<script>
(function() {
	function hideCategoriesSection() {
		var container = document.querySelector('.posts-container .entry-content');
		if (!container) return;
		
		var headings = container.querySelectorAll('h2');
		headings.forEach(function(h2) {
			var text = h2.textContent.trim();
			if (text.includes('Compre por categorias') || text.includes('Compre por categoria')) {
				var current = h2;
				while (current && current.nextSibling) {
					current = current.nextSibling;
					if (current.nodeType === 1) {
						if (current.tagName === 'H2' || current.tagName === 'H1' || current.classList.contains('entry-content')) break;
						if (current.querySelector('a[href*="categoria-produto"]') || current.querySelector('figure')) {
							current.style.display = 'none';
						}
					}
				}
				h2.style.display = 'none';
			}
		});
		
		var figures = container.querySelectorAll('figure a[href*="categoria-produto"]');
		figures.forEach(function(fig) {
			var parent = fig.closest('figure');
			if (parent) parent.style.display = 'none';
		});
	}
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', hideCategoriesSection);
	} else {
		hideCategoriesSection();
	}
})();
</script>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>

