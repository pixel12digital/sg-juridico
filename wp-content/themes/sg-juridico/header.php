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

				<nav id="site-navigation" class="site-navigation primary-navigation" role="dialog" aria-modal="false" aria-hidden="true" aria-label="<?php esc_attr_e( 'Menu principal', 'sg-juridico' ); ?>">
					<div class="mobile-nav-header">
						<button type="button" class="mobile-nav-close header-icon-btn" aria-label="<?php esc_attr_e( 'Fechar menu', 'sg-juridico' ); ?>">
							<span class="screen-reader-text"><?php esc_html_e( 'Fechar menu', 'sg-juridico' ); ?></span>
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</button>
					</div>
					<div class="primary-nav-inner">
						<?php
						$menu_locations = get_nav_menu_locations();
						$has_menu       = false;

						if ( isset( $menu_locations['primary'] ) && $menu_locations['primary'] > 0 ) {
							$menu_items = wp_get_nav_menu_items( $menu_locations['primary'] );
							$has_menu   = ! empty( $menu_items );
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
							?>
							<ul class="nav-menu">
								<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Início</a></li>
								<li class="menu-item-has-children">
									<a href="#">Cursos</a>
									<ul class="sub-menu">
										<?php if ( class_exists( 'WooCommerce' ) ) : ?>
											<li><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Todos os Cursos</a></li>
											<?php
											$categories = get_terms(
												array(
													'taxonomy'   => 'product_cat',
													'hide_empty' => true,
													'parent'     => 0,
													'number'     => 10,
													'orderby'    => 'count',
													'order'      => 'DESC',
												)
											);

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
								$sobre_page   = get_page_by_path( 'sobre' );
								$contato_page = get_page_by_path( 'contato' );
								?>
								<?php if ( $sobre_page && 'publish' === $sobre_page->post_status ) : ?>
									<li><a href="<?php echo esc_url( get_permalink( $sobre_page->ID ) ); ?>">Sobre</a></li>
								<?php else : ?>
									<li><a href="#" onclick="alert('Página não encontrada. Vá em Páginas → Adicionar Nova e crie a página \"Sobre\" com o slug \"sobre\"'); return false;">Sobre</a></li>
								<?php endif; ?>

								<?php if ( $contato_page && 'publish' === $contato_page->post_status ) : ?>
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

				<div class="header-actions" role="group" aria-label="<?php esc_attr_e( 'Ações principais', 'sg-juridico' ); ?>">
					<button class="menu-toggle header-icon-btn" type="button" aria-controls="primary-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'Abrir menu', 'sg-juridico' ); ?>">
						<span class="screen-reader-text"><?php esc_html_e( 'Abrir menu', 'sg-juridico' ); ?></span>
						<svg class="icon-menu" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
							<path d="M3 6H21M3 12H21M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
						<svg class="icon-close" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="display: none;">
							<path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</button>

					<?php if ( class_exists( 'WooCommerce' ) ) : ?>
						<?php sg_render_header_cart(); ?>
					<?php endif; ?>

					<?php if ( is_user_logged_in() ) : ?>
						<div class="user-profile-dropdown">
							<button class="user-profile-btn header-icon-btn" aria-expanded="false" aria-haspopup="true" aria-label="<?php esc_attr_e( 'Abrir menu da conta', 'sg-juridico' ); ?>">
								<span class="user-avatar"><?php echo get_avatar( get_current_user_id(), 32 ); ?></span>
								<span class="user-name"><?php echo esc_html( wp_get_current_user()->display_name ); ?></span>
								<svg class="dropdown-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
									<path d="M4.47 5.47L8 9L11.53 5.47L13 6.94L8 11.94L3 6.94L4.47 5.47Z"/>
								</svg>
							</button>
							<div class="dropdown-menu">
								<a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>">
									<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
										<path d="M12 2C8.686 2 6 4.686 6 8C6 11.314 8.686 14 12 14C15.314 14 18 11.314 18 8C18 4.686 15.314 2 12 2ZM4 20C4 16.134 7.134 13 11 13H13C16.866 13 20 16.134 20 20V21H4V20Z"/>
									</svg>
									<?php esc_html_e( 'Minha Conta', 'sg-juridico' ); ?>
								</a>
								<?php if ( class_exists( 'WooCommerce' ) ) : ?>
									<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">
										<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
											<path d="M17 2H7C5.343 2 4 3.343 4 5V19C4 20.657 5.343 22 7 22H17C18.657 22 20 20.657 20 19V5C20 3.343 18.657 2 17 2ZM18 19C18 19.552 17.552 20 17 20H7C6.448 20 6 19.552 6 19V5C6 4.448 6.448 4 7 4H17C17.552 4 18 4.448 18 5V19ZM9 17H15V15H9V17ZM9 13H15V11H9V13ZM9 9H15V7H9V9Z"/>
										</svg>
										<?php esc_html_e( 'Meus Cursos', 'sg-juridico' ); ?>
									</a>
								<?php endif; ?>
								<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="logout-link">
									<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
										<path d="M5 3C3.346 3 2 4.346 2 6V18C2 19.654 3.346 21 5 21H12V19H5C4.449 19 4 18.552 4 18V6C4 5.448 4.449 5 5 5H12V3H5ZM15 5L13.586 6.414L17.172 10H9V12H17.172L13.586 15.586L15 17L21 11L15 5Z"/>
									</svg>
									<?php esc_html_e( 'Sair', 'sg-juridico' ); ?>
								</a>
							</div>
						</div>
					<?php else : ?>
						<a href="<?php echo esc_url( wp_login_url() ); ?>" class="header-icon-btn header-auth-link" aria-label="<?php esc_attr_e( 'Entrar ou criar conta', 'sg-juridico' ); ?>">
							<svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/>
								<path d="M4 20C4 16.6863 6.68629 14 10 14H14C17.3137 14 20 16.6863 20 20V21H4V20Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
							</svg>
							<span class="auth-label"><?php esc_html_e( 'Entrar', 'sg-juridico' ); ?></span>
						</a>
					<?php endif; ?>

					<?php if ( ! is_user_logged_in() ) : ?>
						<a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>" class="btn-cta">
							<?php esc_html_e( 'Comece Agora', 'sg-juridico' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>

			<div class="header-search header-search--mobile">
				<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label class="screen-reader-text" for="header-search-mobile"><?php esc_html_e( 'Buscar cursos', 'sg-juridico' ); ?></label>
					<input type="search" name="s" id="header-search-mobile" class="search-field" placeholder="<?php esc_attr_e( 'Buscar cursos...', 'sg-juridico' ); ?>" value="<?php echo get_search_query(); ?>" autocomplete="off" inputmode="search">
					<button type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Buscar', 'sg-juridico' ); ?>">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
							<circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="2"/>
							<path d="M15 15L21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
						</svg>
					</button>
				</form>
				<div id="search-preview" class="search-preview" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 1000; max-height: 400px; overflow-y: auto; margin-top: 5px;">
					<div id="search-preview-content"></div>
				</div>
			</div>
		</div>
	</header>

	<div class="header-search-bar header-search--desktop" role="search">
		<div class="container">
			<form method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="header-search-desktop"><?php esc_html_e( 'Buscar cursos', 'sg-juridico' ); ?></label>
				<input type="search" name="s" id="header-search-desktop" class="search-field" placeholder="<?php esc_attr_e( 'Buscar cursos...', 'sg-juridico' ); ?>" value="<?php echo get_search_query(); ?>" autocomplete="off" inputmode="search">
				<button type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Buscar', 'sg-juridico' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="2"/>
						<path d="M15 15L21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
					</svg>
				</button>
			</form>
		</div>
	</div>

	<div class="site-navigation-backdrop" aria-hidden="true"></div>
