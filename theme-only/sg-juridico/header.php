<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'sg-juridico' ); ?></a>

	<header id="masthead" class="site-header">
		<div class="container">
			<div class="site-header-wrapper">
				<!-- Logo -->
				<div class="site-branding">
					<?php
					if ( has_custom_logo() ) :
						the_custom_logo();
					else :
						?>
						<h1 class="site-title">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
								<?php bloginfo( 'name' ); ?>
							</a>
						</h1>
						<?php
						$description = get_bloginfo( 'description', 'display' );
						if ( $description || is_customize_preview() ) :
							?>
							<p class="site-description"><?php echo $description; ?></p>
							<?php
						endif;
					endif;
					?>
				</div>

				<!-- Barra de Pesquisa -->
				<div class="header-search">
					<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<input type="search" name="s" class="search-field" placeholder="<?php esc_attr_e( 'Buscar cursos...', 'sg-juridico' ); ?>" value="<?php echo get_search_query(); ?>">
						<button type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Buscar', 'sg-juridico' ); ?>">
							<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16zM18 18l-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</button>
					</form>
				</div>

				<!-- Botões de Acesso / CTA / Carrinho -->
				<div class="header-actions">
					<?php if ( is_user_logged_in() ) : ?>
						<!-- Carrinho de Compras -->
						<?php if ( class_exists( 'WooCommerce' ) ) : ?>
							<?php
							$cart_count = WC()->cart->get_cart_contents_count();
							$cart_url = wc_get_cart_url();
							?>
							<a href="<?php echo esc_url( $cart_url ); ?>" class="cart-icon" aria-label="<?php esc_attr_e( 'Carrinho de compras', 'sg-juridico' ); ?>">
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M9 22C9.55228 22 10 21.5523 10 21C10 20.4477 9.55228 20 9 20C8.44772 20 8 20.4477 8 21C8 21.5523 8.44772 22 9 22Z" fill="currentColor"/>
									<path d="M20 22C20.5523 22 21 21.5523 21 21C21 20.4477 20.5523 20 20 20C19.4477 20 19 20.4477 19 21C19 21.5523 19.4477 22 20 22Z" fill="currentColor"/>
									<path d="M1 1H5L7.68 14.39C7.77144 14.8504 8.02191 15.264 8.38755 15.5583C8.75318 15.8526 9.2107 16.009 9.68 16H19C19.5304 16 20.0391 15.7893 20.4142 15.4142C20.7893 15.0391 21 14.5304 21 14H9.9L9.36 11H19L22 4H6.28L5.28 2H1V1Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
								<?php if ( $cart_count > 0 ) : ?>
									<span class="cart-count"><?php echo esc_html( $cart_count ); ?></span>
								<?php endif; ?>
							</a>
						<?php endif; ?>

						<!-- Dropdown do Perfil -->
						<div class="user-profile-dropdown">
							<button class="user-profile-btn" aria-expanded="false" aria-haspopup="true">
								<?php echo get_avatar( get_current_user_id(), 32 ); ?>
								<span class="user-name"><?php echo esc_html( wp_get_current_user()->display_name ); ?></span>
								<svg class="dropdown-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</button>
							<div class="dropdown-menu">
								<a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										<circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
									<?php esc_html_e( 'Minha Conta', 'sg-juridico' ); ?>
								</a>
								<?php if ( class_exists( 'WooCommerce' ) ) : ?>
									<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M9 11l3 3L21 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
										<?php esc_html_e( 'Meus Cursos', 'sg-juridico' ); ?>
									</a>
								<?php endif; ?>
								<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="logout-link">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
									<?php esc_html_e( 'Sair', 'sg-juridico' ); ?>
								</a>
							</div>
						</div>
					<?php else : ?>
						<!-- Usuário Não Logado -->
						<a href="<?php echo esc_url( wp_login_url() ); ?>" class="btn-login">
							<?php esc_html_e( 'Entrar', 'sg-juridico' ); ?>
						</a>
						<?php if ( class_exists( 'WooCommerce' ) && get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>
							<a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>" class="btn-register">
								<?php esc_html_e( 'Cadastrar', 'sg-juridico' ); ?>
						</a>
						<?php endif; ?>
						<a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>" class="btn-cta">
							<?php esc_html_e( 'Comece Agora', 'sg-juridico' ); ?>
						</a>
					<?php endif; ?>
				</div>

				<!-- Menu Mobile Toggle -->
				<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
					<span class="screen-reader-text"><?php esc_html_e( 'Primary Menu', 'sg-juridico' ); ?></span>
					<span class="menu-icon">☰</span>
				</button>
			</div>

		</div>

		<!-- Menu de Navegação Principal -->
		<nav id="site-navigation" class="site-navigation primary-navigation">
			<div class="container">
				<?php
				// Verificar se existe menu atribuído
				$menu_locations = get_nav_menu_locations();
				$has_menu = false;
				
				if ( isset( $menu_locations['primary'] ) && $menu_locations['primary'] > 0 ) {
					$menu_items = wp_get_nav_menu_items( $menu_locations['primary'] );
					$has_menu = ! empty( $menu_items );
				}
				
				if ( $has_menu ) {
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'menu_id'        => 'primary-menu',
							'container'      => false,
							'menu_class'     => 'nav-menu',
							'depth'          => 2,
							'fallback_cb'    => false,
						)
					);
				} else {
					// Menu padrão organizado se nenhum menu for atribuído
					?>
					<ul class="nav-menu">
						<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Início</a></li>
						<li class="menu-item-has-children">
							<a href="#">Cursos</a>
							<ul class="sub-menu">
								<?php if ( class_exists( 'WooCommerce' ) ) : ?>
									<li><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Todos os Cursos</a></li>
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
								<?php endif; ?>
							</ul>
						</li>
						<?php if ( class_exists( 'WooCommerce' ) ) : ?>
							<li><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">Loja</a></li>
						<?php endif; ?>
						<li><a href="<?php echo esc_url( home_url( '/blog' ) ); ?>">Blog</a></li>
						<?php
						$sobre_page = get_page_by_path( 'sobre' );
						$contato_page = get_page_by_path( 'contato' );
						?>
						<?php if ( $sobre_page && $sobre_page->post_status === 'publish' ) : ?>
							<li><a href="<?php echo esc_url( get_permalink( $sobre_page->ID ) ); ?>">Sobre</a></li>
						<?php else : ?>
							<li><a href="#" onclick="alert('Página não encontrada. Vá em Páginas → Adicionar Nova e crie a página \"Sobre\" com o slug \"sobre\"'); return false;">Sobre</a></li>
						<?php endif; ?>
						
						<?php if ( $contato_page && $contato_page->post_status === 'publish' ) : ?>
							<li><a href="<?php echo esc_url( get_permalink( $contato_page->ID ) ); ?>">Contato</a></li>
						<?php else : ?>
							<li><a href="#" onclick="alert('Página não encontrada. Vá em Páginas → Adicionar Nova e crie a página \"Contato\" com o slug \"contato\"'); return false;">Contato</a></li>
						<?php endif; ?>
					</ul>
					<?php
				}
				?>
			</div>
		</nav>
	</header>

