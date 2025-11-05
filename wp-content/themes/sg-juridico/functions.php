<?php
/**
 * Theme functions and definitions
 *
 * @package SG_Juridico
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SG_VERSION', '1.0.0' );

/**
 * Setup theme defaults
 */
function sg_setup() {
	load_theme_textdomain( 'sg-juridico', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'script',
		'style',
	) );

	add_theme_support( 'custom-logo', array(
		'height'      => 100,
		'width'       => 350,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'sg-juridico' ),
		'footer'  => esc_html__( 'Footer Menu', 'sg-juridico' ),
	) );

	if ( class_exists( 'WooCommerce' ) ) {
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}
}
add_action( 'after_setup_theme', 'sg_setup' );

/**
 * Configurar archive para eventos ETN
 */
function sg_setup_etn_archive() {
	if ( post_type_exists( 'etn' ) ) {
		global $wp_post_types;
		if ( isset( $wp_post_types['etn'] ) ) {
			$wp_post_types['etn']->has_archive = true;
			$wp_post_types['etn']->rewrite = array(
				'slug'       => 'eventos',
				'with_front' => false,
			);
		}
	}
}
add_action( 'init', 'sg_setup_etn_archive', 20 );

/**
 * Ajustar query para posts ETN
 */
function sg_pre_get_posts_etn( $query ) {
	if ( ! is_admin() && $query->is_main_query() ) {
		// Se acessando via ?p= e é um post ETN
		if ( isset( $_GET['p'] ) ) {
			$post_id = intval( $_GET['p'] );
			if ( $post_id ) {
				$detected_type = get_post_type( $post_id );
				if ( $detected_type === 'etn' ) {
					$query->set( 'post_type', 'etn' );
					$query->set( 'p', $post_id );
					$query->is_singular = true;
					$query->is_single = true;
					$query->is_page = false;
					$query->is_404 = false;
				}
			}
		}
		
		// Se é archive de ETN, ajustar query
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
		
		if ( $query->is_post_type_archive( 'etn' ) || 
			 ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'etn' ) ||
			 ( strpos( $request_uri, '/eventos' ) !== false && ! is_single() && ! is_admin() ) ) {
			$query->set( 'post_type', 'etn' );
			$query->set( 'post_status', 'publish' );
			$query->set( 'posts_per_page', 20 );
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'meta_key', 'etn_start_date' );
			$query->set( 'order', 'ASC' );
			$query->set( 'meta_type', 'DATE' );
		}
	}
}
add_action( 'pre_get_posts', 'sg_pre_get_posts_etn' );

/**
 * Flush rewrite rules quando ativar tema (necessário para URLs funcionarem)
 */
function sg_flush_rewrite_rules() {
	if ( ! get_option( 'sg_flushed_rewrite_rules' ) ) {
		flush_rewrite_rules();
		update_option( 'sg_flushed_rewrite_rules', true );
	}
}
add_action( 'after_switch_theme', 'sg_flush_rewrite_rules' );

/**
 * Forçar flush de rewrite rules quando necessário (útil após mudanças de URL)
 * Adicionar ?sg_flush_rules=1 na URL do admin para forçar flush
 */
function sg_admin_flush_rewrite_rules() {
	if ( isset( $_GET['sg_flush_rules'] ) && current_user_can( 'manage_options' ) ) {
		flush_rewrite_rules( true );
		wp_redirect( admin_url( 'options-permalink.php?settings-updated=true' ) );
		exit;
	}
}
add_action( 'admin_init', 'sg_admin_flush_rewrite_rules' );

/**
 * Forçar uso de templates single-etn.php e archive-etn.php
 */
function sg_template_include_etn( $template ) {
	// Verificar via query vars ou post global
	global $wp_query, $post;
	
	// Garantir que $request_uri esteja sempre definido para verificações posteriores
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
	
	// Verificar se é um post do tipo etn
	$post_type = get_query_var( 'post_type' );
	$is_etn_singular = false;
	
	// Verificar via URL - se contém /eventos
	if ( empty( $post_type ) ) {
		if ( strpos( $request_uri, '/eventos' ) !== false && ! is_single() ) {
			$post_type = 'etn';
		}
	}
	
	// Verificar via ID na URL
	if ( empty( $post_type ) && isset( $_GET['p'] ) ) {
		$post_id = intval( $_GET['p'] );
		if ( $post_id ) {
			$detected_type = get_post_type( $post_id );
			if ( $detected_type === 'etn' ) {
				$post_type = 'etn';
				$is_etn_singular = true;
			}
		}
	}
	
	// Verificar no post global após query
	if ( empty( $post_type ) && $post && isset( $post->post_type ) ) {
		$post_type = $post->post_type;
		if ( $post_type === 'etn' ) {
			$is_etn_singular = true;
		}
	}
	
	// Verificar se é singular ETN
	if ( is_singular( 'etn' ) || $is_etn_singular ) {
		$single_etn = get_template_directory() . '/single-etn.php';
		if ( file_exists( $single_etn ) ) {
			return $single_etn;
		}
	}
	
	// Verificar se é archive ETN - mais agressivo
	if ( is_post_type_archive( 'etn' ) || 
		 ( is_archive() && $post_type === 'etn' ) ||
		 ( strpos( $request_uri, '/eventos' ) !== false && ! is_single() ) ||
		 ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'etn' ) ) {
		
		$archive_etn = get_template_directory() . '/archive-etn.php';
		if ( file_exists( $archive_etn ) ) {
			return $archive_etn;
		}
	}
	
	return $template;
}
add_filter( 'template_include', 'sg_template_include_etn', 99 );

/**
 * Set content width
 */
function sg_content_width() {
	$GLOBALS['content_width'] = 1200;
}
add_action( 'after_setup_theme', 'sg_content_width', 0 );

/**
 * Enqueue scripts and styles
 */
function sg_scripts() {
	// Enqueue styles
	wp_enqueue_style( 'sg-style', get_stylesheet_uri(), array(), SG_VERSION );
	wp_enqueue_style( 'sg-palette', get_template_directory_uri() . '/css/palette.css', array(), SG_VERSION );

	// Carregar estilização específica da página Minha Conta com versionamento anti-cache
	$sg_is_my_account = (
		function_exists( 'is_account_page' ) && is_account_page()
	) || (
		function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url()
	) || (
		is_page( get_option( 'woocommerce_myaccount_page_id' ) )
	) || is_page( 'minha-conta' ) || is_page( 'my-account' );

	if ( $sg_is_my_account ) {
		$my_account_css = get_template_directory() . '/css/my-account.css';
		$version = file_exists( $my_account_css ) ? filemtime( $my_account_css ) : ( SG_VERSION . '-' . time() );
		wp_enqueue_style( 'sg-my-account', get_template_directory_uri() . '/css/my-account.css', array( 'sg-style', 'sg-palette' ), $version );

		// Forçar estrutura de duas colunas via JS quando necessário
		$sg_my_account_inline_js = "(function(){\n  function init(){\n    var container = document.querySelector('.woocommerce');\n    var nav = document.querySelector('nav.woocommerce-MyAccount-navigation');\n    var content = document.querySelector('.woocommerce-MyAccount-content');\n    if(!container || !nav || !content){ return; }\n    if(!container.querySelector('.sg-my-account-layout')){\n      var wrapper = document.createElement('div');\n      wrapper.className = 'sg-my-account-layout';\n      container.insertBefore(wrapper, container.firstChild);\n      wrapper.appendChild(nav);\n      wrapper.appendChild(content);\n    }\n    container.style.display = 'block';\n  }\n  if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded', init);}else{init();}\n  setTimeout(init, 400);\n})();";
		wp_add_inline_script( 'sg-navigation', $sg_my_account_inline_js );

		// Force button text color via JS to defeat conflicting rules
		$sg_btn_fix_js = "(function(){\n  function apply(){\n    var selectors = [\n      '.woocommerce .woocommerce-info a.button',\n      '.woocommerce .woocommerce-message a.button',\n      '.woocommerce table.my_account_orders .button',\n      '.woocommerce a.button.wc-forward'\n    ];\n    selectors.forEach(function(sel){\n      document.querySelectorAll(sel).forEach(function(el){\n        try {\n          el.style.setProperty('color', '#111', 'important');\n          el.style.setProperty('-webkit-text-fill-color', '#111', 'important');\n          el.addEventListener('mouseenter', function(){\n            el.style.setProperty('color', '#111', 'important');\n            el.style.setProperty('-webkit-text-fill-color', '#111', 'important');\n          });\n          el.addEventListener('mouseleave', function(){\n            el.style.setProperty('color', '#111', 'important');\n            el.style.setProperty('-webkit-text-fill-color', '#111', 'important');\n          });\n        } catch(e){}\n      });\n    });\n  }\n  if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded', apply);}else{apply();}\n  setTimeout(apply, 400);\n})();";
		wp_add_inline_script( 'sg-navigation', $sg_btn_fix_js );
	}

	// Enqueue scripts
	wp_enqueue_script( 'sg-navigation', get_template_directory_uri() . '/js/navigation.js', array(), SG_VERSION, true );
	wp_enqueue_script( 'sg-calendario', get_template_directory_uri() . '/js/calendario.js', array(), SG_VERSION, true );
	
	// Carregar script de expansão de eventos apenas na página de eventos
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
	if ( is_post_type_archive( 'etn' ) || 
		 ( is_archive() && get_query_var( 'post_type' ) === 'etn' ) ||
		 ( strpos( $request_uri, '/eventos' ) !== false && ! is_single() ) ||
		 ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'etn' ) ) {
		wp_enqueue_script( 'sg-eventos-expand', get_template_directory_uri() . '/js/eventos-expand.js', array(), SG_VERSION, true );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Estilos do layout da loja e filtros (WooCommerce)
	if ( class_exists( 'WooCommerce' ) && ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) ) {
		wp_enqueue_style( 'sg-shop-filters', get_template_directory_uri() . '/css/shop-filters.css', array(), SG_VERSION );
	}

	// Carregar estilização específica da página Contato
	$sg_is_contact = (
		is_page_template( 'page-contato.php' ) || 
		is_page( 'contato' ) || 
		( is_page() && strpos( strtolower( get_the_title() ), 'contato' ) !== false )
	);
	if ( $sg_is_contact ) {
		$contact_css = get_template_directory() . '/css/contact.css';
		$version = file_exists( $contact_css ) ? filemtime( $contact_css ) : ( SG_VERSION . '-' . time() );
		wp_enqueue_style( 'sg-contact', get_template_directory_uri() . '/css/contact.css', array( 'sg-style', 'sg-palette' ), $version );
		
		// Enqueue script do formulário de contato
		$contact_js = get_template_directory() . '/js/contact-form.js';
		$js_version = file_exists( $contact_js ) ? filemtime( $contact_js ) : SG_VERSION;
		wp_enqueue_script( 'sg-contact-form', get_template_directory_uri() . '/js/contact-form.js', array(), $js_version, true );
		wp_localize_script( 'sg-contact-form', 'sgContactAjax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'sg_contact_form' ),
		) );
	}
}
add_action( 'wp_enqueue_scripts', 'sg_scripts', 20 );

/**
 * Usar sidebar personalizada do WooCommerce nas páginas de loja
 */
function sg_override_woocommerce_sidebar() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) {
		// Remover sidebar padrão
		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

		// Abrir layout + renderizar filtros antes de todo conteúdo da loja
		add_action( 'woocommerce_before_main_content', function() {
			echo '<div class="sg-shop-layout">';
			wc_get_template( 'global/sidebar.php' );
			echo '<div class="sg-shop-content">';
		}, 5 );

		// Fechar wrappers após o conteúdo
		add_action( 'woocommerce_after_main_content', function() {
			echo '</div></div>';
		}, 50 );

		// Barra sticky com título + contagem + ordenação
		add_action( 'woocommerce_before_shop_loop', function() {
			echo '<div class="sg-shop-toolbar">';
			// Título da página/termo
			if ( function_exists( 'woocommerce_page_title' ) ) {
				$title = woocommerce_page_title( false );
				if ( $title ) {
					echo '<h1 class="shop-title">' . esc_html( $title ) . '</h1>';
				}
			}
		}, 1 );

		add_action( 'woocommerce_before_shop_loop', function() {
			echo '</div>';
		}, 99 );
	}
}
add_action( 'wp', 'sg_override_woocommerce_sidebar' );

/**
 * Forçar uso do template "Minha Conta (Sidebar)" quando estiver na página Minha Conta
 */
function sg_force_my_account_template( $template ) {
    if ( function_exists( 'is_account_page' ) && is_account_page() ) {
        $custom_template = get_template_directory() . '/page-templates/account-sidebar.php';
        if ( file_exists( $custom_template ) ) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter( 'template_include', 'sg_force_my_account_template', 98 );

/**
 * Register widget areas
 */
function sg_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'sg-juridico' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'sg-juridico' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer 1', 'sg-juridico' ),
		'id'            => 'footer-1',
		'description'   => esc_html__( 'Coluna 1 do Footer - Sobre a empresa', 'sg-juridico' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer 2', 'sg-juridico' ),
		'id'            => 'footer-2',
		'description'   => esc_html__( 'Coluna 2 do Footer - Links rápidos', 'sg-juridico' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer 3', 'sg-juridico' ),
		'id'            => 'footer-3',
		'description'   => esc_html__( 'Coluna 3 do Footer - Informações e contato', 'sg-juridico' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer 4', 'sg-juridico' ),
		'id'            => 'footer-4',
		'description'   => esc_html__( 'Coluna 4 do Footer - Redes sociais e newsletter', 'sg-juridico' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	// Filtros da Loja (WooCommerce)
	if ( class_exists( 'WooCommerce' ) ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Filtros da Loja (WooCommerce)', 'sg-juridico' ),
			'id'            => 'shop-filters',
			'description'   => esc_html__( 'Widgets de filtros (preço, categorias, atributos).', 'sg-juridico' ),
			'before_widget' => '<section id="%1$s" class="widget shop-filter %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}
}
add_action( 'widgets_init', 'sg_widgets_init' );

/**
 * Customizer additions
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Template Functions
 */
function sg_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() )
	);

	printf( '<span class="posted-on">%s</span>', $time_string );
}

function sg_posted_by() {
	printf(
		'<span class="byline"> %s <span class="author vcard"><a class="url fn n" href="%s">%s</a></span></span>',
		_x( 'by', 'post author', 'sg-juridico' ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_html( get_the_author() )
	);
}

function sg_post_thumbnail() {
	if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
		return;
	}

	if ( is_singular() ) :
		?>
		<div class="post-thumbnail">
			<?php the_post_thumbnail(); ?>
		</div>
		<?php
	else :
		?>
		<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php
			the_post_thumbnail( 'post-thumbnail', array(
				'alt' => the_title_attribute( array(
					'echo' => false,
				) ),
			) );
			?>
		</a>
		<?php
	endif;
}

function sg_entry_footer() {
	if ( 'post' === get_post_type() ) {
		$categories_list = get_the_category_list( esc_html__( ', ', 'sg-juridico' ) );
		if ( $categories_list ) {
			printf( '<span class="cat-links">%s %s</span>', esc_html__( 'Posted in', 'sg-juridico' ), $categories_list );
		}

		$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'sg-juridico' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">%s %s</span>', esc_html__( 'Tagged', 'sg-juridico' ), $tags_list );
		}
	}
}

/**
 * Add cart icon with item count to header
 */
function sg_cart_fragments_count( $fragments ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return $fragments;
	}

	ob_start();
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
	<?php
	$fragments['a.cart-icon'] = ob_get_clean();
	
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'sg_cart_fragments_count' );

/**
 * Redirect non-logged users to my-account page
 */
function sg_redirect_to_my_account() {
	if ( ! is_admin() && ! is_user_logged_in() && ! is_page( 'my-account' ) && ! is_admin() && ! wp_is_xml_request() ) {
		global $wp;
		$request = $wp->request;
		
		// Don't redirect for specific pages
		$excluded_pages = array( 'wp-login.php', 'wp-admin', 'wp-content', 'wp-includes', 'feed' );
		foreach ( $excluded_pages as $excluded ) {
			if ( false !== strpos( $request, $excluded ) ) {
				return;
			}
		}
	}
}
// Uncomment if you want to force login
// add_action( 'template_redirect', 'sg_redirect_to_my_account' );

/**
 * Add custom body classes for header styling
 */
function sg_header_body_classes( $classes ) {
	if ( is_user_logged_in() ) {
		$classes[] = 'user-logged-in';
	} else {
		$classes[] = 'user-not-logged-in';
	}
	
	if ( class_exists( 'WooCommerce' ) ) {
		$classes[] = 'woocommerce-active';
	}
	
	return $classes;
}
add_filter( 'body_class', 'sg_header_body_classes' );

/**
 * Get page by slug helper function
 */
function sg_get_page_url_by_slug( $slug, $default_url = '#' ) {
	$page = get_page_by_path( $slug );
	
	if ( $page && $page->post_status === 'publish' ) {
		return get_permalink( $page->ID );
	}
	
	return $default_url;
}

/**
 * Get company information
 * You can customize these values via theme options or directly here
 */
function sg_get_company_info( $info = '' ) {
	$company_info = array(
		'cnpj'      => '00.000.000/0001-00', // Configure seu CNPJ aqui
		'instagram' => 'https://instagram.com/sgjuridico', // Configure seu Instagram aqui
		'whatsapp'  => '5511999999999', // Configure seu WhatsApp aqui (formato: 5511999999999)
		'whatsapp_display' => '(11) 99999-9999', // Formato para exibição
	);

	if ( ! empty( $info ) && isset( $company_info[ $info ] ) ) {
		return $company_info[ $info ];
	}

	return $company_info;
}

/**
 * Format WhatsApp link
 */
function sg_get_whatsapp_link( $phone = null, $message = '' ) {
	if ( ! $phone ) {
		$phone = sg_get_company_info( 'whatsapp' );
	}
	
	// Remove caracteres não numéricos
	$phone = preg_replace( '/[^0-9]/', '', $phone );
	
	// Se não começar com 55, adiciona
	if ( substr( $phone, 0, 2 ) !== '55' ) {
		$phone = '55' . $phone;
	}
	
	$default_message = 'Olá! Gostaria de saber mais sobre os cursos do SG Jurídico.';
	$text = urlencode( ! empty( $message ) ? $message : $default_message );
	
	return "https://wa.me/{$phone}?text={$text}";
}

/**
 * Remove widget de pesquisa do sidebar (já existe no header)
 */
function sg_remove_search_widget_from_sidebar( $sidebars_widgets ) {
	if ( isset( $sidebars_widgets['sidebar-1'] ) && is_array( $sidebars_widgets['sidebar-1'] ) ) {
		foreach ( $sidebars_widgets['sidebar-1'] as $key => $widget ) {
			if ( strpos( $widget, 'search' ) !== false ) {
				unset( $sidebars_widgets['sidebar-1'][ $key ] );
			}
		}
		// Reindexar array
		$sidebars_widgets['sidebar-1'] = array_values( $sidebars_widgets['sidebar-1'] );
	}
	return $sidebars_widgets;
}
add_filter( 'sidebars_widgets', 'sg_remove_search_widget_from_sidebar' );

/**
 * Desregistrar widget de comentários recentes
 */
function sg_unregister_recent_comments_widget() {
	unregister_widget( 'WP_Widget_Recent_Comments' );
}
add_action( 'widgets_init', 'sg_unregister_recent_comments_widget', 11 );

/**
 * Desregistrar widget de arquivos
 */
function sg_unregister_archives_widget() {
	unregister_widget( 'WP_Widget_Archives' );
}
add_action( 'widgets_init', 'sg_unregister_archives_widget', 11 );

/**
 * Widget customizado de posts recentes com thumbnails
 */
class SG_Recent_Posts_With_Thumbnails extends WP_Widget_Recent_Posts {
	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Posts' );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number ) {
			$number = 5;
		}

		$r = new WP_Query(
			apply_filters(
				'widget_posts_args',
				array(
					'posts_per_page'      => $number,
					'no_found_rows'       => true,
					'post_status'         => 'publish',
					'ignore_sticky_posts' => true,
				),
				$instance
			)
		);

		if ( ! $r->have_posts() ) {
			return;
		}
		?>

		<?php echo $args['before_widget']; ?>

		<?php
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$format = current_theme_supports( 'html5', 'navigation-widgets' ) ? 'html5' : 'xhtml';

		if ( 'html5' === $format ) {
			echo '<nav role="navigation" aria-label="' . esc_attr( $title ) . '">';
		} else {
			echo '<div>';
		}
		?>
		<ul class="recent-posts-list">
		<?php
		foreach ( $r->posts as $recent_post ) :
			$post_title = get_the_title( $recent_post->ID );
			$title      = ( ! empty( $post_title ) ) ? $post_title : __( '(no title)' );
			$permalink  = get_permalink( $recent_post->ID );
			
			// Obter thumbnail - tentar múltiplas fontes
			$thumbnail_html = '';
			$has_thumbnail = false;
			
			// 1. Tentar pegar featured image (thumbnail padrão)
			if ( has_post_thumbnail( $recent_post->ID ) ) {
				$thumbnail_id = get_post_thumbnail_id( $recent_post->ID );
				$thumb_url = wp_get_attachment_image_src( $thumbnail_id, 'medium' );
				if ( $thumb_url && ! empty( $thumb_url[0] ) ) {
					$thumbnail_html = '<div class="recent-post-thumbnail"><a href="' . esc_url( $permalink ) . '"><img src="' . esc_url( $thumb_url[0] ) . '" alt="' . esc_attr( $title ) . '" width="' . esc_attr( $thumb_url[1] ) . '" height="' . esc_attr( $thumb_url[2] ) . '" loading="lazy" /></a></div>';
					$has_thumbnail = true;
				}
			}
			
			// 2. Se não tiver featured image, tentar pegar primeira imagem do conteúdo
			if ( ! $has_thumbnail ) {
				$content = get_post_field( 'post_content', $recent_post->ID );
				$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches );
				if ( ! empty( $matches[1][0] ) ) {
					$first_img = $matches[1][0];
					
					// Lista de imagens que sabemos que não existem (evitar 404)
					$problematic_images = array(
						'MPMA.png',
						'TJTO.png',
						'Delegado-da-Policia-Federal.png',
						'Magistratura-Federal-TRF.png',
						'TJCE.png',
						'TJDFT.png',
						'cropped-a4-212x300-Photoroom',
					);
					
					// Verificar se a URL contém alguma imagem problemática
					$is_problematic = false;
					foreach ( $problematic_images as $problematic ) {
						if ( strpos( $first_img, $problematic ) !== false ) {
							$is_problematic = true;
							break;
						}
					}
					
					// Só adicionar se não for problemática
					if ( ! $is_problematic ) {
						// Se a imagem começar com //, adicionar http:
						if ( substr( $first_img, 0, 2 ) === '//' ) {
							$first_img = 'http:' . $first_img;
						}
						$thumbnail_html = '<div class="recent-post-thumbnail"><a href="' . esc_url( $permalink ) . '"><img src="' . esc_url( $first_img ) . '" alt="' . esc_attr( $title ) . '" loading="lazy" /></a></div>';
						$has_thumbnail = true;
					}
				}
			}
			
			// 3. Se ainda não tiver imagem, usar placeholder
			if ( ! $has_thumbnail ) {
				$thumbnail_html = '<div class="recent-post-thumbnail recent-post-placeholder"><a href="' . esc_url( $permalink ) . '"><span class="placeholder-icon">📄</span></a></div>';
			}
			?>
			<li class="recent-post-with-thumbnail">
				<?php echo $thumbnail_html; ?>
				<div class="recent-post-content">
					<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
				</div>
			</li>
			<?php
		endforeach;
		?>
		</ul>
		<?php
		if ( 'html5' === $format ) {
			echo '</nav>';
		} else {
			echo '</div>';
		}

		echo $args['after_widget'];
	}
}

/**
 * Registrar widget customizado de posts recentes com thumbnails
 */
function sg_register_recent_posts_with_thumbnails() {
	unregister_widget( 'WP_Widget_Recent_Posts' );
	register_widget( 'SG_Recent_Posts_With_Thumbnails' );
}
add_action( 'widgets_init', 'sg_register_recent_posts_with_thumbnails', 11 );

/**
 * Adicionar thumbnails aos posts recentes usando JavaScript
 * Solução garantida que funciona mesmo com widget padrão
 */
function sg_add_thumbnails_via_javascript() {
	if ( is_admin() ) {
		return;
	}
	
	// Passar dados dos posts para JavaScript
	$recent_posts_data = array();
	
	// Obter posts recentes - abordagem mais direta
	// Primeiro, tentar obter dos widgets configurados
	if ( is_active_sidebar( 'sidebar-1' ) ) {
		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( ! empty( $sidebars_widgets['sidebar-1'] ) ) {
			foreach ( $sidebars_widgets['sidebar-1'] as $widget_id ) {
				if ( strpos( $widget_id, 'recent-posts' ) !== false || strpos( $widget_id, 'recent_posts' ) !== false ) {
					// Tentar extrair número do widget
					$widget_instances = get_option( 'widget_recent-posts' );
					$widget_number = intval( preg_replace( '/[^0-9]/', '', $widget_id ) );
					
					$number = 5; // padrão
					if ( ! empty( $widget_instances[ $widget_number ] ) && ! empty( $widget_instances[ $widget_number ]['number'] ) ) {
						$number = intval( $widget_instances[ $widget_number ]['number'] );
					}
					
					break;
				}
			}
		}
	}
	
	// Buscar os posts mais recentes
	$posts = get_posts( array(
		'posts_per_page' => ! empty( $number ) ? $number : 5,
		'post_status' => 'publish',
		'ignore_sticky_posts' => true,
		'orderby' => 'date',
		'order' => 'DESC',
	) );
	
	foreach ( $posts as $post ) {
		$post_url = get_permalink( $post->ID );
		$post_url_normalized = rtrim( $post_url, '/' );
		
		$post_data = array(
			'id' => $post->ID,
			'url' => $post_url,
			'thumbnail' => '',
		);
		
		// Obter thumbnail - tentar featured image primeiro
		if ( has_post_thumbnail( $post->ID ) ) {
			$thumb_id = get_post_thumbnail_id( $post->ID );
			$thumb_url = wp_get_attachment_image_src( $thumb_id, 'medium' );
			if ( $thumb_url && ! empty( $thumb_url[0] ) ) {
				// A URL já será corrigida automaticamente pelo filtro wp_get_attachment_image_src
				$post_data['thumbnail'] = $thumb_url[0];
			}
		}
		
		// Se não tiver thumbnail, tentar primeira imagem do conteúdo
		if ( empty( $post_data['thumbnail'] ) ) {
			$content = $post->post_content;
			preg_match( '/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $matches );
			if ( ! empty( $matches[1] ) ) {
				$first_img = $matches[1];
				
				// Lista de imagens que sabemos que não existem (evitar 404)
				$problematic_images = array(
					'MPMA.png',
					'TJTO.png',
					'Delegado-da-Policia-Federal.png',
					'Magistratura-Federal-TRF.png',
					'TJCE.png',
					'TJDFT.png',
					'cropped-a4-212x300-Photoroom',
				);
				
				// Verificar se a URL contém alguma imagem problemática
				$is_problematic = false;
				foreach ( $problematic_images as $problematic ) {
					if ( strpos( $first_img, $problematic ) !== false ) {
						$is_problematic = true;
						break;
					}
				}
				
				// Só adicionar se não for problemática
				if ( ! $is_problematic ) {
					// Converter URL relativa para absoluta
					if ( substr( $first_img, 0, 2 ) === '//' ) {
						$first_img = 'http:' . $first_img;
					} elseif ( substr( $first_img, 0, 1 ) === '/' && substr( $first_img, 0, 2 ) !== '//' ) {
						$first_img = home_url( $first_img );
					}
					// A URL será corrigida automaticamente pelo sistema híbrido de URLs
					$post_data['thumbnail'] = $first_img;
				}
			}
		}
		
		// Armazenar com múltiplas chaves para facilitar busca
		$recent_posts_data[ $post_url ] = $post_data;
		$recent_posts_data[ $post_url_normalized ] = $post_data;
		if ( $post_url !== $post_url_normalized ) {
			$recent_posts_data[ $post_url_normalized ] = $post_data;
		}
	}
	?>
	<script type="text/javascript">
	(function() {
		var postsData = <?php echo json_encode( $recent_posts_data ); ?>;
		
		function addThumbnails() {
			// Procurar na sidebar (#secondary)
			var sidebar = document.getElementById('secondary') || document.querySelector('.widget-area, aside#secondary');
			
			if (!sidebar) {
				console.log('Sidebar não encontrada');
				return;
			}
			
			// Procurar por seções que possam conter "Posts recentes"
			var widgets = sidebar.querySelectorAll('section.widget, .widget, [class*="widget"]');
			var targetWidget = null;
			
			// Procurar por widget que tenha título "Posts recentes" ou classe relacionada
			for (var w = 0; w < widgets.length; w++) {
				var widget = widgets[w];
				var title = widget.querySelector('.widget-title, h2, h3');
				
				if (title && (
					title.textContent.indexOf('Posts recentes') !== -1 ||
					title.textContent.indexOf('Recent Posts') !== -1 ||
					widget.classList.contains('widget_recent_entries') ||
					widget.id && widget.id.indexOf('recent') !== -1
				)) {
					targetWidget = widget;
					break;
				}
			}
			
			// Se não encontrou por título, procurar por classe
			if (!targetWidget) {
				targetWidget = sidebar.querySelector('.widget_recent_entries, [class*="recent"]');
			}
			
			if (!targetWidget) {
				console.log('Widget de posts recentes não encontrado');
				return;
			}
			
			// Procurar todos os <li> que contenham links dentro do widget
			var listItems = targetWidget.querySelectorAll('li');
			
			if (listItems.length === 0) {
				console.log('Nenhum item de lista encontrado');
				return;
			}
			
			console.log('Encontrados ' + listItems.length + ' posts para processar');
			console.log('Dados disponíveis:', Object.keys(postsData).length);
			
			// Processar cada item
			for (var i = 0; i < listItems.length; i++) {
				var li = listItems[i];
				
				// Pular se já foi processado
				if (li.classList.contains('recent-post-with-thumbnail') || li.querySelector('.recent-post-thumbnail')) {
					continue;
				}
				
				var link = li.querySelector('a');
				if (!link) {
					continue;
				}
				
				var url = link.getAttribute('href');
				if (!url) {
					continue;
				}
				
				// Normalizar URL (remover trailing slash, etc)
				var normalizedUrl = url.replace(/\/$/, '');
				
				// Tentar encontrar dados do post
				var postData = postsData[url] || postsData[normalizedUrl];
				
				// Se não encontrou, tentar buscar por qualquer URL que contenha parte do caminho
				if (!postData) {
					for (var dataUrl in postsData) {
						if (dataUrl.indexOf(url) !== -1 || url.indexOf(dataUrl) !== -1) {
							postData = postsData[dataUrl];
							break;
						}
					}
				}
				
				if (postData && postData.thumbnail) {
					// Adicionar thumbnail com imagem
					addThumbnail(li, url, postData.thumbnail);
				} else {
					// Adicionar placeholder (sempre adiciona para ter consistência visual)
					addPlaceholder(li, url);
				}
			}
		}
		
		function addThumbnail(li, url, thumbnailUrl) {
			// Validar URL antes de tentar carregar
			var problematicImages = [
				'MPMA.png',
				'TJTO.png',
				'Delegado-da-Policia-Federal.png',
				'Magistratura-Federal-TRF.png',
				'TJCE.png',
				'TJDFT.png',
				'cropped-a4-212x300-Photoroom',
				'/2025/05/'
			];
			
			var isProblematic = problematicImages.some(function(pattern) {
				return thumbnailUrl.indexOf(pattern) !== -1;
			});
			
			// Se for uma imagem problemática, usar placeholder direto
			if (isProblematic) {
				addPlaceholder(li, url);
				return;
			}
			
			li.classList.add('recent-post-with-thumbnail');
			
			var thumbnail = document.createElement('div');
			thumbnail.className = 'recent-post-thumbnail';
			
			var link = document.createElement('a');
			link.href = url;
			
			var img = document.createElement('img');
			img.src = thumbnailUrl;
			img.alt = '';
			img.loading = 'lazy';
			
			// Adicionar tratamento de erro para imagens que não carregam
			img.onerror = function(e) {
				// Prevenir propagação do erro para evitar 404 no console
				e.preventDefault();
				e.stopPropagation();
				// Se a imagem falhar ao carregar, usar placeholder
				this.style.display = 'none';
				var placeholder = document.createElement('span');
				placeholder.className = 'placeholder-icon';
				placeholder.textContent = '📄';
				link.innerHTML = '';
				link.appendChild(placeholder);
				return false;
			};
			
			link.appendChild(img);
			thumbnail.appendChild(link);
			
			var content = document.createElement('div');
			content.className = 'recent-post-content';
			content.innerHTML = li.innerHTML;
			
			li.innerHTML = '';
			li.appendChild(thumbnail);
			li.appendChild(content);
		}
		
		function addPlaceholder(li, url) {
			li.classList.add('recent-post-with-thumbnail');
			
			var thumbnail = document.createElement('div');
			thumbnail.className = 'recent-post-thumbnail recent-post-placeholder';
			
			var link = document.createElement('a');
			link.href = url || '#';
			
			var icon = document.createElement('span');
			icon.className = 'placeholder-icon';
			icon.textContent = '📄';
			
			link.appendChild(icon);
			thumbnail.appendChild(link);
			
			var content = document.createElement('div');
			content.className = 'recent-post-content';
			content.innerHTML = li.innerHTML;
			
			li.innerHTML = '';
			li.appendChild(thumbnail);
			li.appendChild(content);
		}
		
		// Iniciar quando DOM estiver pronto
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', addThumbnails);
		} else {
			addThumbnails();
		}
		
		// Também tentar depois de um pequeno delay para garantir
		setTimeout(addThumbnails, 500);
	})();
	
	// Script global para tratar erros de imagens 404 em toda a página
	(function() {
		'use strict';
		
		// Lista de imagens problemáticas
		var problematicImages = [
			'Magistratura-Federal-TRF.png',
			'TJDFT.png',
			'MPMA.png',
			'TJCE.png',
			'TJTO.png',
			'Delegado-da-Policia-Federal.png',
			'cropped-a4-212x300-Photoroom',
			'/2025/05/'
		];
		
		// Interceptar erros de carregamento de imagens ANTES de serem exibidos no console
		window.addEventListener('error', function(e) {
			if (e.target && e.target.tagName === 'IMG') {
				var img = e.target;
				
				// Pular imagens do banner - não ocultar ou tratar erros delas
				if (img.hasAttribute('data-banner-image')) {
					return;
				}
				
				var imgSrc = img.src || '';
				
				var isProblematic = problematicImages.some(function(pattern) {
					return imgSrc.indexOf(pattern) !== -1;
				});
				
				if (isProblematic) {
					// Ocultar imagem que falhou
					img.style.display = 'none';
					// Prevenir que o erro apareça no console
					e.preventDefault();
					e.stopPropagation();
					return false;
				}
			}
		}, true);
		
		// Adicionar onerror handler a todas as imagens da página
		function addErrorHandlers() {
			var images = document.querySelectorAll('img');
			images.forEach(function(img) {
				// Pular imagens do banner - não aplicar handlers de erro a elas
				if (img.hasAttribute('data-banner-image')) {
					return;
				}
				
				if (!img.hasAttribute('data-error-handled')) {
					img.setAttribute('data-error-handled', 'true');
					
					// Verificar se é uma imagem problemática ANTES de adicionar handler
					var imgSrc = img.src || img.getAttribute('src') || '';
					var isProblematic = problematicImages.some(function(pattern) {
						return imgSrc.indexOf(pattern) !== -1;
					});
					
					if (isProblematic) {
						// Ocultar imagem problemática imediatamente sem tentar carregar
						img.style.display = 'none';
						// Remover atributo src para evitar requisição
						if (img.hasAttribute('src')) {
							img.removeAttribute('src');
						}
						return;
					}
					
					img.addEventListener('error', function(e) {
						e.preventDefault();
						e.stopPropagation();
						var imgSrc = this.src || '';
						
						var isProblematic = problematicImages.some(function(pattern) {
							return imgSrc.indexOf(pattern) !== -1;
						});
						
						if (isProblematic) {
							this.style.display = 'none';
							return false;
						}
					}, true);
				}
			});
		}
		
		// Executar quando DOM estiver pronto
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', addErrorHandlers);
		} else {
			addErrorHandlers();
		}
		
		// Observar mudanças no DOM (para imagens carregadas dinamicamente)
		if (window.MutationObserver) {
			var observer = new MutationObserver(function(mutations) {
				addErrorHandlers();
			});
			
			observer.observe(document.body, {
				childList: true,
				subtree: true
			});
		}
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'sg_add_thumbnails_via_javascript' );

/**
 * AJAX handler para obter thumbnail do post
 */
function sg_ajax_get_post_thumbnail() {
	$post_url = isset( $_POST['post_url'] ) ? esc_url_raw( $_POST['post_url'] ) : '';
	
	if ( empty( $post_url ) ) {
		wp_send_json_error( array( 'message' => 'URL não fornecida' ) );
	}
	
	$post_id = url_to_postid( $post_url );
	
	if ( ! $post_id ) {
		wp_send_json_error( array( 'message' => 'Post não encontrado' ) );
	}
	
	$thumbnail_html = sg_get_post_thumbnail_html( $post_id, $post_url );
	
	wp_send_json_success( array( 'html' => $thumbnail_html ) );
}
add_action( 'wp_ajax_sg_get_post_thumbnail', 'sg_ajax_get_post_thumbnail' );
add_action( 'wp_ajax_nopriv_sg_get_post_thumbnail', 'sg_ajax_get_post_thumbnail' );

/**
 * Remover imagens problemáticas do conteúdo das páginas antes de exibir
 */
function sg_remove_problematic_images_from_content( $content ) {
	// Apenas processar se não estiver no admin e for conteúdo de post/page
	if ( is_admin() ) {
		return $content;
	}
	
	// Lista de imagens problemáticas
	$problematic_images = array(
		'MPMA.png',
		'TJTO.png',
		'TJCE.png',
		'TJDFT.png',
		'Delegado-da-Policia-Federal.png',
		'Magistratura-Federal-TRF.png',
		'cropped-a4-212x300-Photoroom',
		'/2025/05/',
	);
	
	$modified = $content;
	
	foreach ( $problematic_images as $problematic ) {
		$escaped = preg_quote( $problematic, '/' );
		
		// Padrão 1: <img...> dentro de <a>...</a>
		$pattern1 = '/(<a\s[^>]*href=[\'"]([^\'"]*)[\'"][^>]*>)\s*(<img\s[^>]*src=[\'"][^\'"]*' . $escaped . '[^\'"]*[\'"][^>]*>)\s*(<\/a>)/is';
		$modified = preg_replace( $pattern1, '$1<!-- Imagem removida: arquivo não existe -->$4', $modified );
		
		// Padrão 2: <img...> standalone
		$pattern2 = '/(<img\s[^>]*src=[\'"][^\'"]*' . $escaped . '[^\'"]*[\'"][^>]*>)/is';
		$modified = preg_replace( $pattern2, '<!-- Imagem removida: arquivo não existe -->', $modified );
		
		// Padrão 3: srcset também pode conter a imagem problemática
		$pattern3 = '/(srcset=[\'"][^\'"]*' . $escaped . '[^\'"]*[\'"])/is';
		$modified = preg_replace( $pattern3, '', $modified );
	}
	
	return $modified;
}
add_filter( 'the_content', 'sg_remove_problematic_images_from_content', 20 );

/**
 * Modificar output do widget usando filtro de sidebar params
 */
function sg_modify_recent_posts_widget_html( $params ) {
	global $wp_registered_widgets;
	
	if ( ! isset( $params[0]['widget_id'] ) ) {
		return $params;
	}
	
	$widget_id = $params[0]['widget_id'];
	
	// Verificar se é widget de posts recentes
	if ( strpos( $widget_id, 'recent-posts' ) !== false || strpos( $widget_id, 'recent_posts' ) !== false ) {
		// Interceptar o callback do widget
		if ( isset( $wp_registered_widgets[ $widget_id ] ) && isset( $wp_registered_widgets[ $widget_id ]['callback'] ) ) {
			$original_callback = $wp_registered_widgets[ $widget_id ]['callback'];
			
			// Substituir callback por um que adiciona thumbnails
			$wp_registered_widgets[ $widget_id ]['callback'] = function() use ( $params, $original_callback ) {
				// Executar callback original dentro de output buffer
				ob_start();
				if ( is_callable( $original_callback ) ) {
					call_user_func_array( $original_callback, func_get_args() );
				}
				$output = ob_get_clean();
				
				// Processar output para adicionar thumbnails
				$output = sg_process_recent_posts_output( $output );
				echo $output;
			};
		}
	}
	
	return $params;
}
add_filter( 'dynamic_sidebar_params', 'sg_modify_recent_posts_widget_html', 20 );

/**
 * Processar output HTML e adicionar thumbnails
 */
function sg_process_recent_posts_output( $html ) {
	// Verificar se contém widget de posts recentes
	if ( strpos( $html, 'widget_recent_entries' ) === false ) {
		return $html;
	}
	
	// Encontrar todas as tags <li> dentro do widget
	preg_match_all( '/<li(?:\s+[^>]*)?>(.*?)<\/li>/is', $html, $li_matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE );
	
	if ( empty( $li_matches ) ) {
		return $html;
	}
	
	// Processar de trás para frente para manter os offsets corretos
	$offset = 0;
	foreach ( array_reverse( $li_matches ) as $match ) {
		$full_match = $match[0];
		$content = $match[1];
		$match_pos = $match[0][1] + $offset;
		
		// Extrair URL do link
		preg_match( '/<a\s+href=["\']([^"\']+)["\']/', $content[0], $url_match );
		
		if ( ! empty( $url_match[1] ) ) {
			$url = $url_match[1];
			$post_id = url_to_postid( $url );
			
			if ( $post_id > 0 ) {
				$thumbnail_html = sg_get_post_thumbnail_html( $post_id, $url );
				$new_li = '<li class="recent-post-with-thumbnail">' . $thumbnail_html . '<div class="recent-post-content">' . $content[0] . '</div></li>';
				$html = substr_replace( $html, $new_li, $match_pos, strlen( $full_match[0] ) );
				$offset += strlen( $new_li ) - strlen( $full_match[0] );
			}
		}
	}
	
	return $html;
}

/**
 * Obter HTML do thumbnail do post
 */
function sg_get_post_thumbnail_html( $post_id, $permalink ) {
	$title = get_the_title( $post_id );
	$thumbnail_html = '';
	$has_thumbnail = false;
	
	// 1. Tentar featured image
	if ( has_post_thumbnail( $post_id ) ) {
		$thumbnail_id = get_post_thumbnail_id( $post_id );
		$thumb_url = wp_get_attachment_image_src( $thumbnail_id, 'medium' );
		if ( $thumb_url && ! empty( $thumb_url[0] ) ) {
			$thumbnail_html = '<div class="recent-post-thumbnail"><a href="' . esc_url( $permalink ) . '"><img src="' . esc_url( $thumb_url[0] ) . '" alt="' . esc_attr( $title ) . '" width="' . esc_attr( $thumb_url[1] ) . '" height="' . esc_attr( $thumb_url[2] ) . '" loading="lazy" /></a></div>';
			$has_thumbnail = true;
		}
	}
	
	// 2. Tentar primeira imagem do conteúdo
	if ( ! $has_thumbnail ) {
		$content = get_post_field( 'post_content', $post_id );
		$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches );
		if ( ! empty( $matches[1][0] ) ) {
			$first_img = $matches[1][0];
			if ( substr( $first_img, 0, 2 ) === '//' ) {
				$first_img = 'http:' . $first_img;
			}
			$thumbnail_html = '<div class="recent-post-thumbnail"><a href="' . esc_url( $permalink ) . '"><img src="' . esc_url( $first_img ) . '" alt="' . esc_attr( $title ) . '" loading="lazy" /></a></div>';
			$has_thumbnail = true;
		}
	}
	
	// 3. Placeholder
	if ( ! $has_thumbnail ) {
		$thumbnail_html = '<div class="recent-post-thumbnail recent-post-placeholder"><a href="' . esc_url( $permalink ) . '"><span class="placeholder-icon">📄</span></a></div>';
	}
	
	return $thumbnail_html;
}

/**
 * Remove widgets de comentários e arquivos do sidebar (backup)
 */
function sg_remove_comments_widget_from_sidebar( $sidebars_widgets ) {
	if ( ! is_array( $sidebars_widgets ) ) {
		return $sidebars_widgets;
	}
	
	// Verificar todas as sidebars
	foreach ( $sidebars_widgets as $sidebar_id => $widgets ) {
		if ( is_array( $widgets ) ) {
			$removed = false;
			foreach ( $widgets as $key => $widget ) {
				// Remover widgets relacionados a comentários
				if ( strpos( $widget, 'recent-comments' ) !== false || 
				     strpos( $widget, 'recent_comments' ) !== false ||
				     ( strpos( $widget, 'comments' ) !== false && strpos( $widget, 'recent' ) !== false ) ||
				     // Remover widgets relacionados a arquivos
				     strpos( $widget, 'archives' ) !== false ||
				     strpos( $widget, 'archive' ) !== false ) {
					unset( $sidebars_widgets[ $sidebar_id ][ $key ] );
					$removed = true;
				}
			}
			// Reindexar array apenas se algo foi removido
			if ( $removed && isset( $sidebars_widgets[ $sidebar_id ] ) ) {
				$sidebars_widgets[ $sidebar_id ] = array_values( $sidebars_widgets[ $sidebar_id ] );
			}
		}
	}
	return $sidebars_widgets;
}
add_filter( 'sidebars_widgets', 'sg_remove_comments_widget_from_sidebar', 99 );

/**
 * Ocultar seções de comentários e arquivos no sidebar via JavaScript (backup do CSS)
 */
function sg_hide_comments_sidebar_script() {
	?>
	<script>
	(function() {
		// Aguardar o DOM estar pronto
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', hideSidebarWidgets);
		} else {
			hideSidebarWidgets();
		}
		
		function hideSidebarWidgets() {
			var sidebar = document.getElementById('secondary');
			if (!sidebar) return;
			
			// Lista de termos para ocultar
			var hideTerms = ['Comentários', 'Comments', 'Arquivos', 'Archives'];
			
			// Procurar por widgets
			var widgets = sidebar.querySelectorAll('section.widget, .widget');
			widgets.forEach(function(widget) {
				var title = widget.querySelector('.widget-title, h2.widget-title, h2');
				
				// Verificar se o título contém algum dos termos
				if (title) {
					var titleText = title.textContent.trim();
					for (var i = 0; i < hideTerms.length; i++) {
						if (titleText === hideTerms[i] || titleText.includes(hideTerms[i])) {
							widget.style.display = 'none';
							return;
						}
					}
				}
				
				// Verificar por classes e IDs relacionados a comentários
				var widgetId = widget.id ? widget.id.toLowerCase() : '';
				var widgetClass = widget.className ? widget.className.toLowerCase() : '';
				
				if (widgetId.includes('comment') || widgetId.includes('comments') ||
				    widgetClass.includes('comment') || widgetClass.includes('comments') ||
				    widgetId.includes('archive') || widgetId.includes('archives') ||
				    widgetClass.includes('archive') || widgetClass.includes('archives') ||
				    widgetClass.includes('arquivo')) {
					widget.style.display = 'none';
				}
			});
		}
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'sg_hide_comments_sidebar_script' );

/**
 * Detectar categoria do evento pelo título
 */
function sg_detect_event_category( $title ) {
	$title_lower = strtolower( $title );
	
	$categorias = array(
		'ministério público' => 'ministerio-publico',
		'ministerio publico' => 'ministerio-publico',
		'mp' => 'ministerio-publico',
		'mpsp' => 'ministerio-publico',
		'mpmg' => 'ministerio-publico',
		'pge' => 'procuradoria',
		'procurador' => 'procuradoria',
		'procuradoria' => 'procuradoria',
		'magistratura' => 'magistratura',
		'magistrado' => 'magistratura',
		'tj' => 'magistratura',
		'trf' => 'magistratura',
		'delegado' => 'delegado',
		'policia' => 'delegado',
		'enam' => 'enam',
		'enan' => 'enam',
	);
	
	foreach ( $categorias as $keyword => $categoria ) {
		if ( strpos( $title_lower, $keyword ) !== false ) {
			return $categoria;
		}
	}
	
	return 'outros';
}

/**
 * Inline CSS de alta prioridade para estilizar a página "Minha Conta"
 * Útil quando estilos de plugins sobrepõem o tema.
 */
function sg_inline_styles_my_account() {
    $sg_is_my_account = (
        function_exists( 'is_account_page' ) && is_account_page()
    ) || (
        function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url()
    ) || (
        is_page( get_option( 'woocommerce_myaccount_page_id' ) )
    ) || is_page( 'minha-conta' ) || is_page( 'my-account' );

    if ( $sg_is_my_account ) {
        echo '<style id="sg-my-account-inline" type="text/css">'
            // Navegação vertical como sidebar por padrão (desktop)
            . 'body.woocommerce-account nav.woocommerce-MyAccount-navigation ul,'
            . '.woocommerce nav.woocommerce-MyAccount-navigation ul,'
            . 'body.woocommerce-account nav[aria-label*="conta"] ul,'
            . 'body.woocommerce-account nav[aria-label*="account"] ul{list-style:none!important;margin:0 0 16px 0!important;padding:0!important;display:flex!important;flex-direction:column!important;align-items:stretch!important;gap:8px!important;overflow:visible!important}'
            . 'body.woocommerce-account nav.woocommerce-MyAccount-navigation li,'
            . '.woocommerce nav.woocommerce-MyAccount-navigation li,'
            . 'body.woocommerce-account nav[aria-label*="conta"] li,'
            . 'body.woocommerce-account nav[aria-label*="account"] li{list-style:none!important;display:flex!important;align-items:center!important;margin:0!important;min-height:40px!important}'
            . 'body.woocommerce-account nav.woocommerce-MyAccount-navigation li::marker,'
            . '.woocommerce nav.woocommerce-MyAccount-navigation li::marker,'
            . 'body.woocommerce-account nav[aria-label*="conta"] li::marker,'
            . 'body.woocommerce-account nav[aria-label*="account"] li::marker{content:""!important}'
            . 'body.woocommerce-account nav.woocommerce-MyAccount-navigation ul li a,'
            . '.woocommerce nav.woocommerce-MyAccount-navigation ul li a,'
            . 'body.woocommerce-account nav[aria-label*="conta"] ul li a,'
            . 'body.woocommerce-account nav[aria-label*="account"] ul li a{display:inline-flex!important;align-items:center!important;justify-content:flex-start!important;gap:10px!important;padding:10px 12px!important;line-height:1.3!important;font-size:14px!important;height:auto!important;box-sizing:border-box!important;background:#fff!important;border:1px solid var(--sg-color-border)!important;border-radius:10px!important;text-decoration:none!important;color:var(--sg-color-text)!important;font-weight:600!important;box-shadow:0 2px 8px rgba(0,0,0,.05)!important}'
            . 'body.woocommerce-account nav.woocommerce-MyAccount-navigation ul li.is-active > a{padding:10px 12px!important;line-height:1.3!important;height:auto!important}'
            . 'body.woocommerce-account nav.woocommerce-MyAccount-navigation ul li a:hover,'
            . '.woocommerce nav.woocommerce-MyAccount-navigation ul li a:hover,'
            . 'body.woocommerce-account nav[aria-label*="conta"] ul li a:hover,'
            . 'body.woocommerce-account nav[aria-label*="account"] ul li a:hover{background:var(--sg-color-primary)!important;border-color:var(--sg-color-primary)!important;color:#000!important}'
            // Forçar cor de texto dos botões de ação
            . 'body.woocommerce-account .woocommerce a.button,body.woocommerce-account .woocommerce .button{color:#111!important;-webkit-text-fill-color:#111!important}'
            . 'body.woocommerce-account .woocommerce a.button:hover,body.woocommerce-account .woocommerce .button:hover{color:#111!important;-webkit-text-fill-color:#111!important;text-shadow:none!important}'
            // Esconder lista duplicada de atalhos dentro do conteúdo
            . 'body.woocommerce-account .woocommerce-MyAccount-content > ul,'
            . '.woocommerce .woocommerce-MyAccount-content > ul,'
            . '.woocommerce .woocommerce-MyAccount-content ul,'
            . 'body.woocommerce-account .entry-content > ul{display:none!important}'
            // Parágrafos compactos
            . 'body.woocommerce-account .woocommerce-MyAccount-content p{margin:6px 0 8px!important}'
            . 'body.woocommerce-account .woocommerce-MyAccount-content p:first-child{margin-top:12px!important}'
            . 'body.woocommerce-account .woocommerce-MyAccount-content p:last-child{margin-bottom:0!important}'
            // Mobile: voltar para navegação horizontal rolável
            . '@media(max-width:782px){'
            . 'body.woocommerce-account nav.woocommerce-MyAccount-navigation ul{display:flex!important;flex-direction:row!important;gap:12px!important;overflow-x:auto!important;-webkit-overflow-scrolling:touch!important}'
            . 'body.woocommerce-account nav.woocommerce-MyAccount-navigation li{min-height:36px!important}'
            . 'body.woocommerce-account nav.woocommerce-MyAccount-navigation ul li a,'
            . 'body.woocommerce-account nav.woocommerce-MyAccount-navigation ul li.is-active > a{padding:0 10px!important;line-height:1!important;height:100%!important;justify-content:center!important}'
            . '}'
            . '</style>';
    }
}
add_action( 'wp_head', 'sg_inline_styles_my_account', 999 );

/**
 * Buscar eventos de concursos (ETN e The Events Calendar)
 * OTIMIZADO: Usa JOIN para evitar múltiplas consultas ao banco
 * Com cache para reduzir conexões ao banco
 */
function sg_get_concurso_events( $limit = 10, $categoria = null ) {
	global $wpdb;
	
	// Cache de 5 minutos para reduzir consultas ao banco
	$cache_key = 'sg_concurso_events_' . md5( serialize( array( $limit, $categoria ) ) );
	$cached = wp_cache_get( $cache_key, 'sg_events' );
	
	if ( false !== $cached ) {
		return $cached;
	}
	
	$today = current_time( 'Y-m-d' );
	$events = array();
	
	// Buscar eventos ETN usando SQL direto com JOIN para melhor performance
	// Evita múltiplas chamadas de get_post_meta() dentro de loops
	$etn_query = $wpdb->get_results( $wpdb->prepare( "
		SELECT p.ID, p.post_title, p.post_name,
		       pm1.meta_value as start_date,
		       pm2.meta_value as end_date
		FROM {$wpdb->posts} p
		LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = 'etn_start_date'
		LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = 'etn_end_date'
		WHERE p.post_type = 'etn'
		AND p.post_status = 'publish'
		AND (pm1.meta_value IS NOT NULL OR pm2.meta_value IS NOT NULL)
		ORDER BY p.post_date DESC
		LIMIT %d
	", $limit * 2 ) );
	
	if ( ! empty( $etn_query ) ) {
		foreach ( $etn_query as $row ) {
			// Aceitar eventos futuros ou recentes (últimos 30 dias)
			$event_date = ! empty( $row->start_date ) ? $row->start_date : $row->end_date;
			
			if ( $event_date ) {
				// Se for evento futuro ou recente (últimos 30 dias), incluir
				$event_timestamp = strtotime( $event_date );
				$days_diff = ( $event_timestamp - strtotime( $today ) ) / ( 60 * 60 * 24 );
				
				if ( $days_diff >= -30 ) { // Eventos dos últimos 30 dias ou futuros
					$event_title = $row->post_title;
					$event_categoria = sg_detect_event_category( $event_title );
					
					// Filtrar por categoria se especificada
					if ( $categoria && $event_categoria !== $categoria ) {
						continue;
					}
					
					$events[] = array(
						'id'        => $row->ID,
						'title'     => $event_title,
						'date'      => $event_date,
						'end_date'  => $row->end_date,
						'permalink' => get_permalink( $row->ID ),
						'type'      => 'etn',
						'categoria' => $event_categoria,
					);
				}
			}
		}
	}
	
	// Buscar eventos The Events Calendar (tribe_events)
	if ( post_type_exists( 'tribe_events' ) ) {
		$tribe_query = $wpdb->get_results( $wpdb->prepare( "
			SELECT p.ID, p.post_title, 
			       pm1.meta_value as start_date,
			       pm2.meta_value as end_date
			FROM {$wpdb->posts} p
			LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_EventStartDate'
			LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_EventEndDate'
			WHERE p.post_type = 'tribe_events'
			AND p.post_status = 'publish'
			AND pm1.meta_value >= %s
			ORDER BY pm1.meta_value ASC
			LIMIT %d
		", $today, $limit ) );
		
		foreach ( $tribe_query as $row ) {
			if ( ! empty( $row->start_date ) ) {
				$start_date_str = is_numeric( $row->start_date ) ? date( 'Y-m-d', $row->start_date ) : date( 'Y-m-d', strtotime( $row->start_date ) );
				$end_date_str = null;
				if ( ! empty( $row->end_date ) ) {
					$end_date_str = is_numeric( $row->end_date ) ? date( 'Y-m-d', $row->end_date ) : date( 'Y-m-d', strtotime( $row->end_date ) );
				}
				
				$events[] = array(
					'id'        => $row->ID,
					'title'     => $row->post_title,
					'date'      => $start_date_str,
					'end_date'  => $end_date_str,
					'permalink' => get_permalink( $row->ID ),
					'type'      => 'tribe_events',
				);
			}
		}
	}
	
	// Se não encontrou eventos futuros, buscar os mais recentes (para teste)
	if ( empty( $events ) ) {
		$recent_etn = $wpdb->get_results( $wpdb->prepare( "
			SELECT p.ID, p.post_title, 
			       pm1.meta_value as start_date,
			       pm2.meta_value as end_date
			FROM {$wpdb->posts} p
			LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = 'etn_start_date'
			LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = 'etn_end_date'
			WHERE p.post_type = 'etn'
			AND p.post_status = 'publish'
			AND (pm1.meta_value IS NOT NULL OR pm2.meta_value IS NOT NULL)
			ORDER BY p.post_date DESC
			LIMIT %d
		", $limit ) );
		
		foreach ( $recent_etn as $row ) {
			$event_date = ! empty( $row->start_date ) ? $row->start_date : ( ! empty( $row->end_date ) ? $row->end_date : date( 'Y-m-d' ) );
			$events[] = array(
				'id'        => $row->ID,
				'title'     => $row->post_title,
				'date'      => $event_date,
				'end_date'  => $row->end_date,
				'permalink' => get_permalink( $row->ID ),
				'type'      => 'etn',
			);
		}
	}
	
	// Ordenar por data
	usort( $events, function( $a, $b ) {
		$date_a = strtotime( $a['date'] );
		$date_b = strtotime( $b['date'] );
		return $date_a - $date_b;
	} );
	
	$result = array_slice( $events, 0, $limit );
	
	// Armazenar no cache por 5 minutos
	wp_cache_set( $cache_key, $result, 'sg_events', 300 );
	
	return $result;
}

/**
 * Buscar todos os eventos para o calendário dinâmico
 * OTIMIZADO: Usa JOIN para evitar múltiplas consultas ao banco
 * Com cache para reduzir conexões ao banco
 */
function sg_get_all_calendar_events() {
	global $wpdb;
	
	// Cache de 10 minutos para reduzir consultas ao banco
	$cache_key = 'sg_all_calendar_events';
	$cached = wp_cache_get( $cache_key, 'sg_events' );
	
	if ( false !== $cached ) {
		return $cached;
	}
	
	$today = current_time( 'Y-m-d' );
	$events = array();
	
	// Buscar todos os eventos ETN usando JOIN para evitar múltiplas consultas
	$etn_query = $wpdb->get_results( "
		SELECT p.ID, p.post_title, p.post_name,
		       pm1.meta_value as start_date,
		       pm2.meta_value as end_date
		FROM {$wpdb->posts} p
		LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = 'etn_start_date'
		LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = 'etn_end_date'
		WHERE p.post_type = 'etn'
		AND p.post_status = 'publish'
		AND (pm1.meta_value IS NOT NULL OR pm2.meta_value IS NOT NULL)
		ORDER BY p.post_date DESC
	" );
	
	if ( ! empty( $etn_query ) ) {
		foreach ( $etn_query as $row ) {
			$event_date = ! empty( $row->start_date ) ? $row->start_date : $row->end_date;
			
			if ( $event_date ) {
				// Incluir todos os eventos, independentemente da data (incluindo passados)
				$event_title = $row->post_title;
				$event_categoria = sg_detect_event_category( $event_title );
				
				$events[] = array(
					'id'        => $row->ID,
					'title'     => $event_title,
					'date'      => $event_date,
					'end_date'  => $row->end_date,
					'permalink' => get_permalink( $row->ID ),
					'type'      => 'etn',
					'categoria' => $event_categoria,
				);
			}
		}
	}
	
	// Buscar eventos The Events Calendar também usando JOIN
	if ( post_type_exists( 'tribe_events' ) ) {
		$tribe_query = $wpdb->get_results( "
			SELECT p.ID, p.post_title, p.post_name,
			       pm1.meta_value as start_date,
			       pm2.meta_value as end_date
			FROM {$wpdb->posts} p
			LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_EventStartDate'
			LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_EventEndDate'
			WHERE p.post_type = 'tribe_events'
			AND p.post_status = 'publish'
			AND pm1.meta_value IS NOT NULL
			ORDER BY p.post_date DESC
		" );
		
		foreach ( $tribe_query as $row ) {
			if ( $row->start_date ) {
				$start_date_str = is_numeric( $row->start_date ) ? date( 'Y-m-d', $row->start_date ) : date( 'Y-m-d', strtotime( $row->start_date ) );
				$event_title = $row->post_title;
				$event_categoria = sg_detect_event_category( $event_title );
				
				$events[] = array(
					'id'        => $row->ID,
					'title'     => $event_title,
					'date'      => $start_date_str,
					'end_date'  => $row->end_date ? ( is_numeric( $row->end_date ) ? date( 'Y-m-d', $row->end_date ) : date( 'Y-m-d', strtotime( $row->end_date ) ) ) : null,
					'permalink' => get_permalink( $row->ID ),
					'type'      => 'tribe_events',
					'categoria' => $event_categoria,
				);
			}
		}
	}
	
	// Ordenar por data
	usort( $events, function( $a, $b ) {
		$date_a = strtotime( $a['date'] );
		$date_b = strtotime( $b['date'] );
		return $date_a - $date_b;
	} );
	
	// Armazenar no cache por 10 minutos
	wp_cache_set( $cache_key, $events, 'sg_events', 600 );
	
	return $events;
}

/**
 * Limpar cache de eventos quando um evento for atualizado
 */
function sg_clear_events_cache( $post_id ) {
	$post_type = get_post_type( $post_id );
	
	// Se for um evento (ETN ou tribe_events), limpar cache
	if ( in_array( $post_type, array( 'etn', 'tribe_events' ) ) ) {
		// Limpar cache principal
		wp_cache_delete( 'sg_all_calendar_events', 'sg_events' );
		// Limpar possíveis caches de consultas específicas
		// Tentamos limpar os padrões mais comuns de cache key
		for ( $limit = 10; $limit <= 50; $limit += 10 ) {
			wp_cache_delete( 'sg_concurso_events_' . md5( serialize( array( $limit, null ) ) ), 'sg_events' );
			wp_cache_delete( 'sg_concurso_events_' . md5( serialize( array( $limit, 'ministerio-publico' ) ) ), 'sg_events' );
			wp_cache_delete( 'sg_concurso_events_' . md5( serialize( array( $limit, 'magistratura' ) ) ), 'sg_events' );
			wp_cache_delete( 'sg_concurso_events_' . md5( serialize( array( $limit, 'delegado' ) ) ), 'sg_events' );
		}
	}
}
add_action( 'save_post', 'sg_clear_events_cache' );
add_action( 'delete_post', 'sg_clear_events_cache' );

/**
 * Contar eventos por categoria
 */
function sg_count_events_by_category() {
	$events = sg_get_all_calendar_events();
	$counts = array(
		'ministerio-publico' => 0,
		'magistratura' => 0,
		'delegado' => 0,
		'enam' => 0,
		'procuradoria' => 0,
		'outros' => 0,
	);
	
	foreach ( $events as $event ) {
		$cat = isset( $event['categoria'] ) ? $event['categoria'] : 'outros';
		if ( isset( $counts[ $cat ] ) ) {
			$counts[ $cat ]++;
		} else {
			$counts['outros']++;
		}
	}
	
	return $counts;
}

/**
 * Adicionar categorias da loja ao menu "Cursos" quando há menu atribuído
 */
function sg_add_categories_to_cursos_menu( $items, $args ) {
	if ( ! isset( $args->theme_location ) || $args->theme_location !== 'primary' ) {
		return $items;
	}
	
	if ( ! class_exists( 'WooCommerce' ) ) {
		return $items;
	}
	
	// Verificar se já há itens dinâmicos adicionados (evitar duplicação e loop infinito)
	foreach ( $items as $item ) {
		if ( isset( $item->ID ) && ( $item->ID == 999999 || $item->ID > 1000000 ) ) {
			// Já foi processado, retornar sem modificar
			return $items;
		}
	}
	
	// Buscar o item do menu "Cursos"
	foreach ( $items as $item ) {
		// Verificar se é o item "Cursos" (pode ser por título ou URL)
		if ( ( stripos( $item->title, 'curso' ) !== false || stripos( $item->url, '#cursos' ) !== false || $item->url === '#' ) && $item->menu_item_parent == 0 ) {
			// Buscar categorias
			$categories = get_terms( array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'parent'     => 0,
				'number'     => 10,
				'orderby'    => 'count',
				'order'      => 'DESC',
			) );
			
			if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
				// Primeiro adicionar "Todos os Cursos"
				$shop_url = wc_get_page_permalink( 'shop' );
				if ( $shop_url ) {
					$todos_item = new stdClass();
					$todos_item->ID = 999999;
					$todos_item->db_id = 999999;
					$todos_item->menu_item_parent = $item->ID;
					$todos_item->object_id = 999999;
					$todos_item->object = 'custom';
					$todos_item->type = 'custom';
					$todos_item->type_label = 'Link Personalizado';
					$todos_item->url = $shop_url;
					$todos_item->title = 'Todos os Cursos';
					$todos_item->target = '';
					$todos_item->attr_title = '';
					$todos_item->description = '';
					$todos_item->classes = array( '' );
					$todos_item->xfn = '';
					$todos_item->current = false;
					$todos_item->current_item_ancestor = false;
					$todos_item->current_item_parent = false;
					$todos_item->post_parent = 0;
					$todos_item->post_type = 'nav_menu_item';
					
					// Adicionar após o item "Cursos"
					$item_index = array_search( $item, $items );
					if ( $item_index !== false ) {
						array_splice( $items, $item_index + 1, 0, array( $todos_item ) );
						
						// Adicionar categorias
						$menu_order = $item_index + 2;
						foreach ( $categories as $category ) {
							$cat_link = get_term_link( $category, 'product_cat' );
							
							// Verificar se houve erro ao obter o link
							if ( is_wp_error( $cat_link ) ) {
								continue;
							}
							
							$cat_item = new stdClass();
							$cat_item->ID = $category->term_id + 1000000;
							$cat_item->db_id = $category->term_id + 1000000;
							$cat_item->menu_item_parent = $item->ID;
							$cat_item->object_id = $category->term_id;
							$cat_item->object = 'product_cat';
							$cat_item->type = 'taxonomy';
							$cat_item->type_label = 'Categoria';
							$cat_item->url = $cat_link;
							$cat_item->title = $category->name;
							$cat_item->target = '';
							$cat_item->attr_title = '';
							$cat_item->description = '';
							$cat_item->classes = array( '' );
							$cat_item->xfn = '';
							$cat_item->current = false;
							$cat_item->current_item_ancestor = false;
							$cat_item->current_item_parent = false;
							$cat_item->post_parent = 0;
							$cat_item->post_type = 'nav_menu_item';
							
							array_splice( $items, $menu_order, 0, array( $cat_item ) );
							$menu_order++;
						}
					}
				}
			}
			break; // Sair do loop após encontrar o item "Cursos"
		}
	}
	
	return $items;
}
add_filter( 'wp_nav_menu_objects', 'sg_add_categories_to_cursos_menu', 10, 2 );

/**
 * Helper para ícones SVG por slug de categoria
 */
function sg_cat_icon_svg( $slug ) {
	$slug_lower = strtolower( $slug );
	$svg = '';
	
	// Remover width/height do SVG para permitir redimensionamento via CSS
	// Detectar padrões no slug ou nome
	if ( strpos( $slug_lower, 'ministerio-publico' ) !== false || strpos( $slug_lower, 'mp' ) !== false ) {
		// Ícone prédio/edifício
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="10" y="28" width="44" height="22" rx="2" fill="currentColor"/><rect x="14" y="18" width="36" height="8" rx="2" fill="currentColor"/><rect x="20" y="34" width="6" height="12" fill="#fff"/><rect x="29" y="34" width="6" height="12" fill="#fff"/><rect x="38" y="34" width="6" height="12" fill="#fff"/></svg>';
	} elseif ( strpos( $slug_lower, 'magistratura' ) !== false ) {
		// Ícone martelo
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="8" y="42" width="48" height="6" rx="3" fill="currentColor"/><rect x="22" y="12" width="16" height="8" rx="2" fill="currentColor"/><rect x="16" y="20" width="16" height="8" rx="2" transform="rotate(45 16 20)" fill="currentColor"/></svg>';
	} elseif ( strpos( $slug_lower, 'delegado' ) !== false ) {
		// Ícone escudo
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M32 8c6 6 12 4 20 4 0 22-8 32-20 36C20 44 12 34 12 12c8 0 14 2 20-4z" fill="currentColor"/><path d="M32 22l3 6 6 1-4 4 1 6-6-3-6 3 1-6-4-4 6-1 3-6z" fill="#fff"/></svg>';
	} elseif ( strpos( $slug_lower, 'enam' ) !== false ) {
		// Ícone documento
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="18" y="12" width="28" height="40" rx="3" fill="currentColor"/><rect x="24" y="20" width="16" height="4" fill="#fff"/><rect x="24" y="28" width="16" height="4" fill="#fff"/><rect x="24" y="36" width="12" height="4" fill="#fff"/></svg>';
	} elseif ( strpos( $slug_lower, 'procuradorias' ) !== false || strpos( $slug_lower, 'procuradoria' ) !== false ) {
		// Ícone balança
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="30" y="12" width="4" height="30" fill="currentColor"/><rect x="18" y="20" width="28" height="4" fill="currentColor"/><path d="M18 24l-6 10h12l-6-10zM46 24l-6 10h12l-6-10z" fill="currentColor"/></svg>';
	} elseif ( strpos( $slug_lower, 'analista' ) !== false || strpos( $slug_lower, 'juridica' ) !== false ) {
		// Ícone pasta/documento jurídico
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 10h24l8 8v32H20V10z" fill="currentColor"/><path d="M28 10v8h8l-8-8z" fill="#fff" opacity="0.3"/><rect x="24" y="26" width="16" height="2" fill="#fff"/><rect x="24" y="32" width="12" height="2" fill="#fff"/></svg>';
	} elseif ( strpos( $slug_lower, 'defensoria' ) !== false || strpos( $slug_lower, 'defensor' ) !== false ) {
		// Ícone escudo com cruz
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M32 8c6 6 12 4 20 4 0 22-8 32-20 36C20 44 12 34 12 12c8 0 14 2 20-4z" fill="currentColor"/><path d="M32 18v16M24 26h16" stroke="#fff" stroke-width="3" stroke-linecap="round"/></svg>';
	} elseif ( strpos( $slug_lower, 'lancamento' ) !== false || strpos( $slug_lower, 'novo' ) !== false ) {
		// Ícone estrela
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M32 8l4 12h12l-10 8 4 12-10-8-10 8 4-12-10-8h12l4-12z" fill="currentColor"/></svg>';
	} elseif ( strpos( $slug_lower, 'sg-juridico' ) !== false || strpos( $slug_lower, 'sg juridico' ) !== false || strpos( $slug_lower, 'sgjuridico' ) !== false ) {
		// Ícone livro/código
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="16" y="10" width="32" height="44" rx="2" fill="currentColor"/><path d="M24 18h16M24 26h12M24 34h16" stroke="#fff" stroke-width="2" stroke-linecap="round"/><circle cx="32" cy="44" r="3" fill="#fff"/></svg>';
	} elseif ( strpos( $slug_lower, 'confira' ) !== false ) {
		// Ícone olho/visualização
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M32 16c-12 0-20 8-20 16s8 16 20 16 20-8 20-16-8-16-20-16z" fill="currentColor"/><circle cx="32" cy="32" r="6" fill="#fff"/></svg>';
	} else {
		// Ícone padrão: livro/documento
		$svg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="18" y="12" width="28" height="40" rx="3" fill="currentColor"/><rect x="24" y="20" width="16" height="4" fill="#fff"/><rect x="24" y="28" width="16" height="4" fill="#fff"/><rect x="24" y="36" width="12" height="4" fill="#fff"/></svg>';
	}
	
	return $svg;
}

/**
 * Exibir calendário dinâmico de eventos de concursos no sidebar
 */
function sg_display_concurso_calendar() {
	$all_events = sg_get_all_calendar_events();
	$category_counts = sg_count_events_by_category();
	
	// Organizar eventos por data para o calendário
	$events_by_date = array();
	foreach ( $all_events as $event ) {
		$date_key = $event['date'];
		if ( ! isset( $events_by_date[ $date_key ] ) ) {
			$events_by_date[ $date_key ] = array();
		}
		$events_by_date[ $date_key ][] = $event;
	}
	
	// Definir mês e ano atual
	$current_month = isset( $_GET['cal_month'] ) ? intval( $_GET['cal_month'] ) : date( 'n' );
	$current_year = isset( $_GET['cal_year'] ) ? intval( $_GET['cal_year'] ) : date( 'Y' );
	
	// Nomes das categorias
	$categorias_nomes = array(
		'ministerio-publico' => 'Ministério Público',
		'magistratura' => 'Magistratura',
		'delegado' => 'Delegado',
		'enam' => 'ENAM',
		'procuradoria' => 'Procuradoria',
	);
	
	$is_home = is_home() && is_front_page();
	?>
	<div class="widget widget-concursos-calendar" id="calendario-concursos">
		<h3 class="widget-title">Calendário de Concursos</h3>
		
		<!-- Filtros por Categoria (ocultar na home) -->
		<?php if ( ! $is_home ) : ?>
		<div class="calendario-filtros" role="tablist" aria-label="Filtros de categoria">
			<div class="filtro-categoria" data-categoria="todos" aria-selected="true">
				<span class="filtro-nome">Todos</span>
			</div>
			<?php foreach ( $categorias_nomes as $slug => $nome ) : 
				$count = isset( $category_counts[ $slug ] ) ? $category_counts[ $slug ] : 0;
				if ( $count > 0 ) :
			?>
				<div class="filtro-categoria" data-categoria="<?php echo esc_attr( $slug ); ?>">
					<span class="filtro-nome"><?php echo esc_html( $nome ); ?></span>
					<span class="filtro-count"><?php echo esc_html( $count ); ?></span>
				</div>
			<?php 
				endif;
			endforeach; ?>
		</div>
		<?php endif; ?>
		
		<!-- Navegação do Calendário -->
		<div class="calendario-nav">
			<button class="calendario-prev" data-action="prev" aria-label="Mês anterior">‹</button>
			<div class="calendario-month-year" aria-live="polite">
				<span class="calendario-month-name"><?php echo date_i18n( 'F', mktime( 0, 0, 0, $current_month, 1, $current_year ) ); ?></span>
				<span class="calendario-year"><?php echo esc_html( $current_year ); ?></span>
			</div>
			<button class="calendario-today" data-action="today" aria-label="Voltar para hoje">Hoje</button>
			<button class="calendario-next" data-action="next" aria-label="Próximo mês">›</button>
		</div>
		
		<!-- Calendário Visual -->
		<div class="calendario-grid" data-month="<?php echo esc_attr( $current_month ); ?>" data-year="<?php echo esc_attr( $current_year ); ?>">
			<!-- Cabeçalho dos dias da semana -->
			<div class="calendario-weekdays">
				<div class="calendario-weekday">Dom</div>
				<div class="calendario-weekday">Seg</div>
				<div class="calendario-weekday">Ter</div>
				<div class="calendario-weekday">Qua</div>
				<div class="calendario-weekday">Qui</div>
				<div class="calendario-weekday">Sex</div>
				<div class="calendario-weekday">Sáb</div>
			</div>
			
			<!-- Dias do calendário serão gerados via JavaScript -->
			<div class="calendario-days" id="calendario-days">
				<!-- Preenchido via JavaScript -->
			</div>
		</div>
		
		<!-- Lista de Eventos (substituída pelo calendário, mas mantida como fallback) -->
		<div class="concursos-calendar-list" id="eventos-lista">
			<!-- Será preenchida via JavaScript baseado na categoria selecionada -->
		</div>
		
		<?php
		// Lista dos 5 eventos mais próximos - mostrar sempre na home
		$today = current_time( 'Y-m-d' );
		$upcoming_events = array_filter( $all_events, function( $event ) use ( $today ) {
			return $event['date'] >= $today;
		} );
		
		// Ordenar por data (já devem estar ordenados, mas garantir)
		usort( $upcoming_events, function( $a, $b ) {
			$date_a = strtotime( $a['date'] );
			$date_b = strtotime( $b['date'] );
			return $date_a - $date_b;
		} );
		
		// Pegar os 5 primeiros
		$upcoming_events = array_slice( $upcoming_events, 0, 5 );
		
		if ( ! empty( $upcoming_events ) ) :
		?>
		<!-- Lista dos Próximos Eventos -->
		<div class="proximos-eventos-lista">
			<h4 class="proximos-eventos-title">Próximos Eventos</h4>
			<ul class="proximos-eventos">
				<?php foreach ( $upcoming_events as $event ) : 
					$event_date = strtotime( $event['date'] );
					$day = date( 'd', $event_date );
					$month = date_i18n( 'M', $event_date );
					?>
					<li class="calendario-item">
						<div class="calendario-date">
							<span class="calendario-day"><?php echo esc_html( $day ); ?></span>
							<span class="calendario-month"><?php echo esc_html( strtoupper( $month ) ); ?></span>
						</div>
						<div class="calendario-content">
							<a href="<?php echo esc_url( $event['permalink'] ); ?>" class="calendario-title">
								<?php echo esc_html( $event['title'] ); ?>
							</a>
							<div class="calendario-date-text">
								<?php echo date_i18n( 'd/m/Y', $event_date ); ?>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>
		
		<div class="calendario-view-all">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'etn' ) ?: home_url( '/eventos' ) ); ?>">Ver todos os eventos →</a>
		</div>
		
		<!-- Dados para JavaScript -->
		<script type="application/json" id="calendario-events-data">
			<?php echo json_encode( $events_by_date, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?>
		</script>
	</div>
	<?php
}

/**
 * Handler para processar formulário de contato
 */
function sg_handle_contact_form() {
	// Verificar nonce
	if ( ! isset( $_POST['sg_contact_nonce'] ) || ! wp_verify_nonce( $_POST['sg_contact_nonce'], 'sg_contact_form' ) ) {
		wp_send_json_error( array( 'message' => 'Erro de segurança. Por favor, recarregue a página e tente novamente.' ) );
	}

	// Sanitizar dados
	$name = isset( $_POST['contact_name'] ) ? sanitize_text_field( $_POST['contact_name'] ) : '';
	$email = isset( $_POST['contact_email'] ) ? sanitize_email( $_POST['contact_email'] ) : '';
	$phone = isset( $_POST['contact_phone'] ) ? sanitize_text_field( $_POST['contact_phone'] ) : '';
	$subject = isset( $_POST['contact_subject'] ) ? sanitize_text_field( $_POST['contact_subject'] ) : '';
	$message = isset( $_POST['contact_message'] ) ? sanitize_textarea_field( $_POST['contact_message'] ) : '';

	// Validar campos obrigatórios
	if ( empty( $name ) || empty( $email ) || empty( $subject ) || empty( $message ) ) {
		wp_send_json_error( array( 'message' => 'Por favor, preencha todos os campos obrigatórios.' ) );
	}

	// Validar email
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'Por favor, insira um e-mail válido.' ) );
	}

	// Prevenir spam básico
	if ( strpos( $message, 'http://' ) !== false || strpos( $message, 'https://' ) !== false || strpos( $message, 'www.' ) !== false ) {
		wp_send_json_error( array( 'message' => 'Mensagem contém links não permitidos.' ) );
	}

	// Traduzir assunto
	$subject_labels = array(
		'duvida' => 'Dúvida sobre cursos',
		'suporte' => 'Suporte técnico',
		'parceria' => 'Parcerias',
		'outro' => 'Outro'
	);
	$subject_label = isset( $subject_labels[ $subject ] ) ? $subject_labels[ $subject ] : 'Contato do Site';

	// Preparar email
	$to = get_option( 'admin_email' );
	$email_subject = '[' . get_bloginfo( 'name' ) . '] ' . $subject_label . ' - ' . $name;
	
	$email_message = "Novo contato recebido através do formulário do site.\n\n";
	$email_message .= "Nome: $name\n";
	$email_message .= "E-mail: $email\n";
	if ( ! empty( $phone ) ) {
		$email_message .= "Telefone: $phone\n";
	}
	$email_message .= "Assunto: $subject_label\n\n";
	$email_message .= "Mensagem:\n$message\n";

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'From: ' . get_bloginfo( 'name' ) . ' <' . $to . '>',
		'Reply-To: ' . $name . ' <' . $email . '>'
	);

	// Enviar email
	$sent = wp_mail( $to, $email_subject, $email_message, $headers );

	if ( $sent ) {
		wp_send_json_success( array( 'message' => 'Mensagem enviada com sucesso! Entraremos em contato em breve.' ) );
	} else {
		wp_send_json_error( array( 'message' => 'Erro ao enviar mensagem. Por favor, tente novamente ou entre em contato diretamente por e-mail.' ) );
	}
}
add_action( 'wp_ajax_sg_send_contact_form', 'sg_handle_contact_form' );
add_action( 'wp_ajax_nopriv_sg_send_contact_form', 'sg_handle_contact_form' );

/**
 * ============================================
 * PERSONALIZAÇÃO DO PAINEL ADMIN DO WORDPRESS
 * ============================================
 */

/**
 * Carregar estilos customizados do admin
 */
function sg_admin_styles() {
	wp_enqueue_style(
		'sg-admin-style',
		get_template_directory_uri() . '/css/admin-style.css',
		array(),
		SG_VERSION
	);
}
add_action( 'admin_enqueue_scripts', 'sg_admin_styles' );

/**
 * Carregar estilos customizados no login também
 */
function sg_admin_login_styles() {
	wp_enqueue_style(
		'sg-admin-login-style',
		get_template_directory_uri() . '/css/admin-style.css',
		array(),
		SG_VERSION
	);
	
	// Adicionar CSS para logo personalizado no login
	$sg_admin_logo_id = get_option( 'sg_admin_bar_logo_id', '' );
	$logo_url = '';
	
	if ( $sg_admin_logo_id ) {
		$logo_data = wp_get_attachment_image_src( $sg_admin_logo_id, 'full' );
		if ( $logo_data && ! empty( $logo_data[0] ) ) {
			$logo_url = $logo_data[0];
		}
	}
	
	// Se não houver logo nas configurações, verificar logo do Customizer
	if ( empty( $logo_url ) ) {
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		if ( $custom_logo_id ) {
			$logo_data = wp_get_attachment_image_src( $custom_logo_id, 'full' );
			if ( $logo_data && ! empty( $logo_data[0] ) ) {
				$logo_url = $logo_data[0];
			}
		}
	}
	
	if ( ! empty( $logo_url ) ) {
		?>
		<style type="text/css">
			.login h1 a {
				background-image: url('<?php echo esc_url( $logo_url ); ?>') !important;
				background-size: contain;
				background-repeat: no-repeat;
				background-position: center center;
				width: 200px;
				height: 80px;
				margin: 0 auto 25px;
				padding: 0;
				text-indent: -9999px;
			}
		</style>
		<?php
	}
}
add_action( 'login_enqueue_scripts', 'sg_admin_login_styles' );

/**
 * Personalizar URL do logo no login
 */
function sg_login_logo_url() {
	return home_url( '/' );
}
add_filter( 'login_headerurl', 'sg_login_logo_url' );

/**
 * Personalizar título do logo no login
 */
function sg_login_logo_url_title() {
	return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'sg_login_logo_url_title' );

/**
 * Remover widgets desnecessários do dashboard
 */
function sg_remove_dashboard_widgets() {
	// Remover widget de "Atividade" (comentários recentes)
	remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
	
	// Remover widget de "Notícias e eventos do WordPress"
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
	
	// Remover widget de "Links rápidos" (Quick Draft)
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	
	// Remover widget de "Boas-vindas" (Welcome Panel)
	remove_action( 'welcome_panel', 'wp_welcome_panel' );
	
	// Remover widget de "Status de diagnóstico" do WooCommerce (se existir)
	remove_meta_box( 'woocommerce_dashboard_status', 'dashboard', 'normal' );
	
	// Remover widget de "Site Health Status" (Status de saúde do site)
	remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
	remove_action( 'wp_dashboard_setup', 'wp_dashboard_site_health', 999 );
	
	// Remover widget de configuração do WooCommerce
	if ( class_exists( 'WooCommerce' ) ) {
		remove_meta_box( 'woocommerce_dashboard_setup', 'dashboard', 'normal' );
		remove_meta_box( 'woocommerce_dashboard_quick_setup', 'dashboard', 'normal' );
		remove_meta_box( 'wc_admin_dashboard_setup', 'dashboard', 'normal' );
		
		// Remover instância da classe WC_Admin_Dashboard_Setup se existir
		global $wp_filter;
		if ( isset( $wp_filter['wp_dashboard_setup'] ) ) {
			foreach ( $wp_filter['wp_dashboard_setup']->callbacks as $priority => $callbacks ) {
				foreach ( $callbacks as $key => $callback ) {
					if ( is_array( $callback['function'] ) && is_object( $callback['function'][0] ) ) {
						$class_name = get_class( $callback['function'][0] );
						if ( $class_name === 'WC_Admin_Dashboard_Setup' ) {
							remove_action( 'wp_dashboard_setup', $callback['function'], $priority );
						}
					}
				}
			}
		}
	}
	
	// Remover widget de "Rascunho rápido" se não necessário
	// remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
}
add_action( 'wp_dashboard_setup', 'sg_remove_dashboard_widgets' );

/**
 * Personalizar texto do rodapé do admin
 */
function sg_admin_footer_text() {
	$text = sprintf(
		/* translators: %s: Site name */
		__( 'Obrigado por criar com <a href="%s">WordPress</a> | Tema: <strong>SG Jurídico</strong>' ),
		__( 'https://wordpress.org/' )
	);
	return $text;
}
add_filter( 'admin_footer_text', 'sg_admin_footer_text' );

/**
 * Remover versão do WordPress do rodapé
 */
function sg_remove_footer_version() {
	return '';
}
add_filter( 'update_footer', 'sg_remove_footer_version', 11 );

/**
 * Remover notificações de atualização desnecessárias
 */
function sg_remove_update_notifications() {
	if ( ! current_user_can( 'update_core' ) ) {
		remove_action( 'admin_notices', 'update_nag', 3 );
	}
}
add_action( 'admin_head', 'sg_remove_update_notifications' );

/**
 * Personalizar título do admin
 */
function sg_admin_title( $admin_title, $title ) {
	return $title . ' &lsaquo; ' . get_bloginfo( 'name' ) . ' &mdash; WordPress';
}
add_filter( 'admin_title', 'sg_admin_title', 10, 2 );

/**
 * Remover banner de configuração do WooCommerce (se não necessário)
 */
function sg_remove_woocommerce_setup_notice() {
	if ( class_exists( 'WooCommerce' ) ) {
		// Remover aviso sobre conexão não segura (HTTPS) se não for crítico
		// Este aviso pode ser útil, então vamos apenas customizá-lo via CSS
	}
}
add_action( 'admin_init', 'sg_remove_woocommerce_setup_notice' );

/**
 * Ocultar widget de status do WooCommerce do dashboard
 */
function sg_remove_woocommerce_dashboard_widgets() {
	if ( class_exists( 'WooCommerce' ) ) {
		remove_meta_box( 'woocommerce_dashboard_status', 'dashboard', 'normal' );
		remove_meta_box( 'woocommerce_dashboard_recent_reviews', 'dashboard', 'normal' );
		remove_meta_box( 'woocommerce_dashboard_recent_orders', 'dashboard', 'normal' );
		
		// Remover widget de configuração do WooCommerce (múltiplos IDs possíveis)
		remove_meta_box( 'woocommerce_dashboard_setup', 'dashboard', 'normal' );
		remove_meta_box( 'woocommerce_dashboard_quick_setup', 'dashboard', 'normal' );
		remove_meta_box( 'wc_admin_dashboard_setup', 'dashboard', 'normal' );
		
		// Remover widget de "Status do diagnóstico" / "WooCommerce Status"
		remove_meta_box( 'woocommerce_dashboard_status', 'dashboard', 'side' );
		remove_meta_box( 'woocommerce_dashboard_status', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
		
		// Tentar remover via hook se existir
		if ( class_exists( 'WC_Admin_Dashboard' ) ) {
			remove_action( 'wp_dashboard_setup', array( 'WC_Admin_Dashboard', 'init' ), 10 );
		}
	}
}
add_action( 'wp_dashboard_setup', 'sg_remove_woocommerce_dashboard_widgets', 1 );

/**
 * Personalizar cores do admin via CSS inline para garantir prioridade
 */
function sg_admin_inline_styles() {
	?>
	<style type="text/css">
		/* Garantir que os estilos tenham prioridade */
		#wpadminbar {
			background: #484848 !important;
			border-bottom: 2px solid #5CE1E6 !important;
		}
		
		#adminmenu,
		#adminmenuback,
		#adminmenuwrap {
			background: #484848 !important;
		}
		
		#adminmenu li:hover,
		#adminmenu li.opensub > a.menu-top,
		#adminmenu li > a.menu-top:focus {
			background: #5CE1E6 !important;
			color: #000 !important;
		}
		
		/* Personalizar logo/branding na admin bar */
		#wpadminbar #wp-admin-bar-site-name .ab-item {
			color: #fff !important;
		}
		
		/* Ocultar badge "Ao vivo" do WooCommerce */
		#wpadminbar #wp-admin-bar-woocommerce-site-visibility-badge,
		#wpadminbar .woocommerce-site-status-badge-live,
		#wpadminbar li.woocommerce-site-status-badge-live,
		#wpadminbar li[id*="woocommerce-site-visibility"],
		#wpadminbar li[class*="woocommerce-site-status"] {
			display: none !important;
		}
		
		/* Ocultar widgets específicos desnecessários */
		.postbox#dashboard_primary,
		.postbox#dashboard_secondary,
		.postbox#dashboard_activity {
			display: none !important;
		}
		
		/* Ocultar banner de atualização do WordPress */
		.update-nag {
			display: none !important;
		}
		
		/* Personalizar botões primários */
		.button-primary,
		.wp-core-ui .button-primary {
			background: #5CE1E6 !important;
			border-color: #5CE1E6 !important;
			color: #000 !important;
			text-shadow: none !important;
			box-shadow: none !important;
		}
		
		.button-primary:hover,
		.wp-core-ui .button-primary:hover {
			background: #4BC4C8 !important;
			border-color: #4BC4C8 !important;
			color: #000 !important;
		}
	</style>
	<?php
}
add_action( 'admin_head', 'sg_admin_inline_styles', 999 );

/**
 * Ocultar widget de "Status de diagnóstico" do WooCommerce completamente
 */
function sg_hide_woocommerce_diagnostic_widget() {
	if ( class_exists( 'WooCommerce' ) ) {
		?>
		<style type="text/css">
			.postbox#woocommerce_dashboard_status {
				display: none !important;
			}
			
			/* Ocultar widget de configuração do WooCommerce */
			.postbox#woocommerce_dashboard_setup,
			.postbox#woocommerce_dashboard_quick_setup,
			.postbox#woocommerce_dashboard_setup_slider,
			.postbox#wc_admin_dashboard_setup,
			.postbox.widget-handle[data-id*="woocommerce_dashboard_setup"],
			.postbox.widget-handle[data-id*="woocommerce_dashboard_quick_setup"],
			.postbox.widget-handle[data-id*="wc_admin_dashboard_setup"],
			.postbox[id*="woocommerce"][id*="setup"],
			.postbox[id*="wc_admin"][id*="setup"] {
				display: none !important;
			}
			
			/* Ocultar widget "Status do diagnóstico" */
			.postbox#woocommerce_dashboard_status,
			.postbox#dashboard_site_health,
			.postbox[id*="diagnostic"],
			.postbox[id*="diagnóstico"],
			.postbox[id*="site_health"],
			.postbox[aria-label*="diagnóstico"],
			.postbox[aria-label*="diagnostic"],
			.postbox[aria-label*="WooCommerce Status"],
			.postbox[aria-label*="Status do WooCommerce"],
			.postbox[aria-label*="Site Health"],
			.postbox[aria-label*="Status de saúde"] {
				display: none !important;
			}
			
			
			/* Ocultar banner de HTTPS não seguro se quiser */
			/* .woocommerce-message.is-dismissible {
				display: none !important;
			} */
		</style>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			// Ocultar widget de configuração do WooCommerce por título
			$('.postbox').each(function() {
				var $postbox = $(this);
				var title = $postbox.find('h2, h3').text().trim();
				var id = $postbox.attr('id') || '';
				
				if (title.includes('WooCommerce Setup') || 
				    title.includes('Configuração do WooCommerce') ||
				    title.includes('Status do diagnóstico') ||
				    title.includes('Diagnostic Status') ||
				    title.includes('Diagnóstico') ||
				    title.includes('Site Health Status') ||
				    title.includes('Status de saúde') ||
				    $postbox.attr('id') && (
				    	$postbox.attr('id').includes('woocommerce_dashboard_setup') ||
				    	$postbox.attr('id').includes('wc_admin_dashboard_setup') ||
				    	$postbox.attr('id').includes('diagnostic') ||
				    	$postbox.attr('id').includes('diagnóstico') ||
				    	$postbox.attr('id').includes('site_health') ||
				    	$postbox.attr('id').includes('woocommerce_dashboard_status')
				    )) {
					$postbox.hide();
				}
			});
			
			// Observar mudanças no DOM para ocultar widgets adicionados dinamicamente
			var observer = new MutationObserver(function(mutations) {
				$('.postbox').each(function() {
					var $postbox = $(this);
					var title = $postbox.find('h2, h3').text().trim();
					var id = $postbox.attr('id') || '';
					
					if (title.includes('WooCommerce Setup') || 
					    title.includes('Configuração do WooCommerce') ||
					    title.includes('Status do diagnóstico') ||
					    title.includes('Diagnostic Status') ||
					    title.includes('Diagnóstico') ||
					    title.includes('Site Health Status') ||
					    title.includes('Status de saúde') ||
					    id.includes('woocommerce_dashboard_setup') ||
					    id.includes('wc_admin_dashboard_setup') ||
					    id.includes('diagnostic') ||
					    id.includes('diagnóstico') ||
					    id.includes('site_health') ||
					    id.includes('woocommerce_dashboard_status')) {
						$postbox.hide();
					}
				});
			});
			
			if (document.getElementById('dashboard-widgets')) {
				observer.observe(document.getElementById('dashboard-widgets'), {
					childList: true,
					subtree: true
				});
			}
		});
		</script>
		<?php
	}
}
add_action( 'admin_head', 'sg_hide_woocommerce_diagnostic_widget' );

/**
 * Personalizar mensagens e textos do admin
 */
function sg_customize_admin_texts( $translated_text, $text, $domain ) {
	if ( $domain === 'default' && is_admin() ) {
		switch ( $text ) {
			case 'Welcome to WordPress':
				$translated_text = 'Bem-vindo ao SG Jurídico';
				break;
			case 'Dashboard':
				$translated_text = 'Painel';
				break;
		}
	}
	return $translated_text;
}
add_filter( 'gettext', 'sg_customize_admin_texts', 20, 3 );

/**
 * Remover itens do menu admin que não são necessários
 */
function sg_remove_admin_menu_items() {
	// Remover "Ferramentas" se não necessário
	// remove_menu_page( 'tools.php' );
	
	// Remover "Comentários" se não necessário
	// remove_menu_page( 'edit-comments.php' );
	
	// Remover "Plugins" para usuários não-administradores
	if ( ! current_user_can( 'manage_options' ) ) {
		remove_menu_page( 'plugins.php' );
		remove_menu_page( 'themes.php' );
		remove_menu_page( 'tools.php' );
	}
}
add_action( 'admin_menu', 'sg_remove_admin_menu_items', 999 );

/**
 * Adicionar CSS para ocultar elementos desnecessários
 */
function sg_hide_unnecessary_admin_elements() {
	?>
	<style type="text/css">
		/* Ocultar widget de notícias do WordPress */
		#dashboard-widgets .postbox#dashboard_primary,
		#dashboard-widgets .postbox#dashboard_secondary {
			display: none !important;
		}
		
		/* Ocultar widget de atividade se não tiver comentários */
		.postbox#dashboard_activity {
			display: none !important;
		}
		
		/* Simplificar mensagens do WooCommerce */
		.woocommerce-message.is-dismissible .notice-dismiss {
			top: 0;
		}
		
		/* Ocultar avisos de configuração se já estiver configurado */
		.woocommerce-message.woocommerce-tracker,
		.wc-connect-notice {
			/* Mantém visível, apenas personaliza */
		}
	</style>
	<?php
}
add_action( 'admin_head', 'sg_hide_unnecessary_admin_elements' );

/**
 * Limpar widgets padrão do dashboard que são desnecessários
 */
function sg_clean_dashboard() {
	global $wp_meta_boxes;
	
	// Remover widgets padrão
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary'] );
	
		// Remover widgets do WooCommerce
		if ( class_exists( 'WooCommerce' ) ) {
			unset( $wp_meta_boxes['dashboard']['normal']['woocommerce']['woocommerce_dashboard_status'] );
			unset( $wp_meta_boxes['dashboard']['normal']['woocommerce']['woocommerce_dashboard_setup'] );
			unset( $wp_meta_boxes['dashboard']['normal']['woocommerce']['woocommerce_dashboard_quick_setup'] );
			unset( $wp_meta_boxes['dashboard']['normal']['woocommerce']['woocommerce_dashboard_recent_orders'] );
			unset( $wp_meta_boxes['dashboard']['normal']['woocommerce']['woocommerce_dashboard_recent_reviews'] );
			unset( $wp_meta_boxes['dashboard']['normal']['woocommerce']['wc_admin_dashboard_setup'] );
			
			// Remover de todas as prioridades possíveis
			if ( isset( $wp_meta_boxes['dashboard']['normal'] ) ) {
				foreach ( $wp_meta_boxes['dashboard']['normal'] as $context => $widgets ) {
					if ( is_array( $widgets ) ) {
						foreach ( $widgets as $id => $widget ) {
							if ( strpos( $id, 'woocommerce_dashboard_setup' ) !== false || 
							     strpos( $id, 'wc_admin_dashboard_setup' ) !== false ||
							     strpos( $id, 'woocommerce_dashboard_quick_setup' ) !== false ||
							     strpos( $id, 'diagnostic' ) !== false ||
							     strpos( $id, 'diagnóstico' ) !== false ) {
								unset( $wp_meta_boxes['dashboard']['normal'][ $context ][ $id ] );
							}
						}
					}
				}
			}
			
			// Remover também da sidebar se existir
			if ( isset( $wp_meta_boxes['dashboard']['side'] ) ) {
				foreach ( $wp_meta_boxes['dashboard']['side'] as $context => $widgets ) {
					if ( is_array( $widgets ) ) {
						foreach ( $widgets as $id => $widget ) {
							if ( strpos( $id, 'diagnostic' ) !== false || 
							     strpos( $id, 'diagnóstico' ) !== false ||
							     strpos( $id, 'woocommerce_dashboard_status' ) !== false ) {
								unset( $wp_meta_boxes['dashboard']['side'][ $context ][ $id ] );
							}
						}
					}
				}
			}
		}
}
add_action( 'wp_dashboard_setup', 'sg_clean_dashboard', 999 );

/**
 * Remover ícone do WordPress da admin bar
 */
function sg_remove_wp_logo_from_admin_bar( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'wp-logo' );
	
	// Remover badge "Ao vivo" do WooCommerce
	$wp_admin_bar->remove_node( 'woocommerce-site-visibility-badge' );
}
add_action( 'admin_bar_menu', 'sg_remove_wp_logo_from_admin_bar', 999 );

/**
 * Remover hook do WooCommerce que adiciona badge "Ao vivo"
 */
function sg_remove_woocommerce_live_badge_hook() {
	if ( class_exists( 'WooCommerce' ) ) {
		// Tentar remover hook da classe ComingSoonAdminBarBadge
		remove_action( 'admin_bar_menu', array( 'Automattic\WooCommerce\Internal\ComingSoon\ComingSoonAdminBarBadge', 'site_visibility_badge' ), 31 );
		
		// Remover via filtro de feature se possível
		add_filter( 'woocommerce_get_feature_config', function( $features ) {
			if ( isset( $features['site_visibility_badge'] ) ) {
				$features['site_visibility_badge'] = false;
			}
			return $features;
		}, 999 );
		
		// Desabilitar feature diretamente
		if ( class_exists( 'Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			// Tentar desabilitar via opção
			update_option( 'woocommerce_feature_site_visibility_badge_enabled', 'no' );
		}
	}
}
add_action( 'init', 'sg_remove_woocommerce_live_badge_hook', 5 );
add_action( 'admin_init', 'sg_remove_woocommerce_live_badge_hook', 5 );

/**
 * Remover badge "Ao vivo" diretamente no hook admin_bar_menu (antes do WooCommerce)
 */
function sg_remove_live_badge_from_admin_bar_early( $wp_admin_bar ) {
	if ( class_exists( 'WooCommerce' ) ) {
		$wp_admin_bar->remove_node( 'woocommerce-site-visibility-badge' );
	}
}
add_action( 'admin_bar_menu', 'sg_remove_live_badge_from_admin_bar_early', 30 );

/**
 * Remover badge "Ao vivo" após WooCommerce adicionar (backup)
 */
function sg_remove_live_badge_from_admin_bar_late( $wp_admin_bar ) {
	if ( class_exists( 'WooCommerce' ) ) {
		$wp_admin_bar->remove_node( 'woocommerce-site-visibility-badge' );
	}
}
add_action( 'admin_bar_menu', 'sg_remove_live_badge_from_admin_bar_late', 32 );

/**
 * Substituir logo do WordPress na admin bar pelo logo do tema
 */
function sg_replace_admin_bar_logo( $wp_admin_bar ) {
	// Logo padrão do SG Jurídico
	$logo_url = '';
	
	// PRIORIDADE 1: Verificar se há logo nas Configurações Gerais do SG Jurídico
	$sg_admin_logo_id = get_option( 'sg_admin_bar_logo_id', '' );
	if ( $sg_admin_logo_id ) {
		$logo = wp_get_attachment_image_src( $sg_admin_logo_id, 'full' );
		if ( $logo && ! empty( $logo[0] ) ) {
			$logo_url = $logo[0];
		}
	}
	
	// PRIORIDADE 2: Verificar se há logo customizado no Customizer
	if ( empty( $logo_url ) ) {
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		if ( $custom_logo_id ) {
			$logo = wp_get_attachment_image_src( $custom_logo_id, 'full' );
			if ( $logo && ! empty( $logo[0] ) ) {
				$logo_url = $logo[0];
			}
		}
	}
	
	// PRIORIDADE 3: Se não houver logo nas configurações, usar o logo padrão
	if ( empty( $logo_url ) ) {
		// Tentar diferentes caminhos possíveis
		$possible_paths = array(
			WP_CONTENT_DIR . '/uploads/2023/09/cropped-Santo-Graal-Juridico-1.png',
			ABSPATH . 'wp-content/uploads/2023/09/cropped-Santo-Graal-Juridico-1.png',
		);
		
		$found_path = false;
		foreach ( $possible_paths as $logo_path ) {
			if ( file_exists( $logo_path ) ) {
				$found_path = true;
				break;
			}
		}
		
		if ( $found_path ) {
			$logo_url = content_url( '/uploads/2023/09/cropped-Santo-Graal-Juridico-1.png' );
		}
	}
	
	// Se temos um logo, usar ele
	if ( ! empty( $logo_url ) ) {
		// Remover o nó padrão do site
		$wp_admin_bar->remove_node( 'site-name' );
		
		// Adicionar novo nó com o logo na mesma posição do wp-logo
		$wp_admin_bar->add_node( array(
			'id'    => 'site-name-logo',
			'title' => '<img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" class="sg-admin-logo" />',
			'href'  => home_url( '/' ),
			'meta'  => array(
				'title' => get_bloginfo( 'name' ),
			),
			'parent' => false,
			'group'  => false,
		) );
	} else {
		// Se não houver logo, personalizar apenas o texto
		$node = $wp_admin_bar->get_node( 'site-name' );
		if ( $node ) {
			$wp_admin_bar->remove_node( 'site-name' );
			$wp_admin_bar->add_node( array(
				'id'    => 'site-name',
				'title' => '<span class="sg-admin-site-name">' . esc_html( get_bloginfo( 'name' ) ) . '</span>',
				'href'  => home_url( '/' ),
				'meta'  => array(
					'title' => get_bloginfo( 'name' ),
				),
			) );
		}
	}
}
add_action( 'admin_bar_menu', 'sg_replace_admin_bar_logo', 50 );

/**
 * Adicionar CSS para o logo na admin bar
 */
function sg_admin_bar_logo_styles() {
	$has_logo = false;
	
	// Verificar se há logo nas Configurações Gerais do SG Jurídico
	$sg_admin_logo_id = get_option( 'sg_admin_bar_logo_id', '' );
	if ( $sg_admin_logo_id ) {
		$logo = wp_get_attachment_image_src( $sg_admin_logo_id, 'full' );
		if ( $logo && ! empty( $logo[0] ) ) {
			$has_logo = true;
		}
	}
	
	// Se não houver, verificar logo do Customizer
	if ( ! $has_logo ) {
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		if ( $custom_logo_id ) {
			$logo = wp_get_attachment_image_src( $custom_logo_id, 'full' );
			if ( $logo && ! empty( $logo[0] ) ) {
				$has_logo = true;
			}
		}
	}
	
	// Se não houver logo do Customizer, verificar se o logo padrão existe
	if ( ! $has_logo ) {
		$possible_paths = array(
			WP_CONTENT_DIR . '/uploads/2023/09/cropped-Santo-Graal-Juridico-1.png',
			ABSPATH . 'wp-content/uploads/2023/09/cropped-Santo-Graal-Juridico-1.png',
		);
		
		foreach ( $possible_paths as $logo_path ) {
			if ( file_exists( $logo_path ) ) {
				$has_logo = true;
				break;
			}
		}
	}
	
	if ( $has_logo ) {
		?>
		<style type="text/css">
			/* Ocultar ícone do WordPress completamente */
			#wpadminbar #wp-admin-bar-site-name > .ab-item:before,
			#wpadminbar #wp-admin-bar-site-name > .ab-item .ab-icon,
			#wpadminbar #wp-admin-bar-site-name > .ab-item .ab-icon:before {
				display: none !important;
				content: none !important;
			}
			
			/* Estilizar logo na admin bar */
			#wpadminbar #wp-admin-bar-site-name-logo .sg-admin-logo {
				max-height: 20px !important;
				width: auto !important;
				height: auto !important;
				vertical-align: middle !important;
				margin-right: 0 !important;
				display: block !important;
			}
			
			#wpadminbar #wp-admin-bar-site-name-logo > .ab-item {
				padding: 6px 12px !important;
				display: flex !important;
				align-items: center !important;
				justify-content: center !important;
				height: 32px !important;
				line-height: 1 !important;
			}
			
			#wpadminbar #wp-admin-bar-site-name-logo > .ab-item:before {
				display: none !important;
				content: none !important;
			}
			
			#wpadminbar #wp-admin-bar-site-name-logo:hover > .ab-item {
				background: #5CE1E6 !important;
			}
			
			/* Ocultar ícone padrão do WordPress se existir */
			#wpadminbar #wp-admin-bar-site-name-logo .ab-icon,
			#wpadminbar #wp-admin-bar-site-name-logo .ab-icon:before {
				display: none !important;
			}
		</style>
		<?php
	} else {
		// Mesmo sem logo, remover o ícone do WordPress
		?>
		<style type="text/css">
			#wpadminbar #wp-admin-bar-site-name > .ab-item:before {
				display: none !important;
				content: none !important;
			}
		</style>
		<?php
	}
}
add_action( 'admin_head', 'sg_admin_bar_logo_styles' );
add_action( 'wp_head', 'sg_admin_bar_logo_styles' );

/**
 * Remover badge "Ao vivo" do WooCommerce via JavaScript
 */
function sg_hide_woocommerce_live_badge() {
	?>
	<script type="text/javascript">
	(function() {
		function hideLiveBadge() {
			// Remover badge "Ao vivo" por ID
			var badge = document.getElementById('wp-admin-bar-woocommerce-site-visibility-badge');
			if (badge) {
				badge.style.display = 'none';
				badge.remove();
			}
			
			// Remover por classe
			var badges = document.querySelectorAll('.woocommerce-site-status-badge-live, li[class*="woocommerce-site-status"]');
			badges.forEach(function(badge) {
				badge.style.display = 'none';
				badge.remove();
			});
			
			// Remover qualquer elemento com texto "Ao vivo" na admin bar
			var allItems = document.querySelectorAll('#wpadminbar .ab-item');
			allItems.forEach(function(item) {
				if (item.textContent.trim() === 'Ao vivo' || item.textContent.trim().includes('Ao vivo')) {
					var parent = item.closest('li');
					if (parent && (parent.id.includes('woocommerce-site-visibility') || parent.classList.contains('woocommerce-site-status-badge-live'))) {
						parent.style.display = 'none';
						parent.remove();
					}
				}
			});
		}
		
		// Executar imediatamente
		hideLiveBadge();
		
		// Executar quando DOM estiver pronto
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', hideLiveBadge);
		}
		
		// Observar mudanças na admin bar
		var adminBar = document.getElementById('wpadminbar');
		if (adminBar) {
			var observer = new MutationObserver(function(mutations) {
				hideLiveBadge();
			});
			
			observer.observe(adminBar, {
				childList: true,
				subtree: true
			});
			
			// Executar após um pequeno delay para garantir
			setTimeout(hideLiveBadge, 500);
			setTimeout(hideLiveBadge, 1000);
		}
	})();
	</script>
	<?php
}
add_action( 'admin_head', 'sg_hide_woocommerce_live_badge', 999 );
add_action( 'wp_head', 'sg_hide_woocommerce_live_badge', 999 );

/**
 * ============================================
 * WIDGET: CONFIGURAÇÕES RÁPIDAS DO SITE
 * ============================================
 */

/**
 * Widget customizado: Configurações Rápidas
 * Permite acesso rápido a todas as configurações do site
 */
class SG_Quick_Settings_Widget {
	
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
	}
	
	/**
	 * Adicionar widget ao dashboard
	 */
	public function add_dashboard_widget() {
		wp_add_dashboard_widget(
			'sg_quick_settings',
			'⚙️ Configurações Rápidas - SG Jurídico',
			array( $this, 'render_widget' )
		);
	}
	
	/**
	 * Renderizar conteúdo do widget
	 */
	public function render_widget() {
		?>
		<div class="sg-quick-settings-widget">
			<style>
				.sg-quick-settings-widget {
					padding: 0;
				}
				.sg-settings-section {
					margin-bottom: 20px;
					padding-bottom: 15px;
					border-bottom: 1px solid #e0e0e0;
				}
				.sg-settings-section:last-child {
					border-bottom: none;
					margin-bottom: 0;
					padding-bottom: 0;
				}
				.sg-settings-title {
					font-size: 15px;
					font-weight: 700;
					color: #000000;
					margin: 0 0 14px 0;
					display: flex;
					align-items: center;
					gap: 8px;
					letter-spacing: -0.2px;
				}
				.sg-settings-title svg {
					width: 18px;
					height: 18px;
					color: #5CE1E6;
					stroke-width: 2.5;
				}
				.sg-settings-links {
					display: grid;
					grid-template-columns: repeat(2, 1fr);
					gap: 10px;
				}
				.sg-settings-link {
					display: flex;
					align-items: center;
					gap: 10px;
					padding: 10px 14px;
					background: #ffffff;
					border: 1px solid #666666;
					border-radius: 6px;
					text-decoration: none;
					color: #000000 !important;
					font-size: 14px;
					font-weight: 500;
					line-height: 1.4;
					transition: all 0.2s ease;
					min-height: 44px;
				}
				.sg-settings-link *:not(.status-badge) {
					color: #000000 !important;
				}
				.sg-settings-link:focus {
					outline: 2px solid #5CE1E6;
					outline-offset: 2px;
					color: #000000 !important;
				}
				.sg-settings-link:focus *:not(.status-badge) {
					color: #000000 !important;
				}
				.sg-settings-link:hover {
					background: #5CE1E6;
					color: #000000 !important;
					border-color: #5CE1E6;
					transform: translateY(-1px);
					box-shadow: 0 3px 6px rgba(0,0,0,0.12);
				}
				.sg-settings-link:hover *:not(.status-badge) {
					color: #000000 !important;
				}
				.sg-settings-link:active {
					transform: translateY(0);
					box-shadow: 0 1px 3px rgba(0,0,0,0.1);
					color: #000000 !important;
				}
				.sg-settings-link:active *:not(.status-badge) {
					color: #000000 !important;
				}
				.sg-settings-link svg {
					width: 18px;
					height: 18px;
					flex-shrink: 0;
					stroke: #000000 !important;
					stroke-width: 2;
					fill: none;
				}
				.sg-settings-link:hover svg {
					stroke: #000000 !important;
				}
				.sg-settings-link .status-badge {
					margin-left: auto;
					font-size: 11px;
					font-weight: 600;
					padding: 4px 8px;
					border-radius: 4px;
					background: #2e7d32;
					color: #ffffff;
					letter-spacing: 0.3px;
					white-space: nowrap;
				}
				.sg-settings-link .status-badge.missing {
					background: #f57c00;
					color: #ffffff;
				}
				/* Melhor contraste para textos em diferentes estados */
				.sg-settings-link:visited {
					color: #000000 !important;
				}
				.sg-settings-link:visited *:not(.status-badge) {
					color: #000000 !important;
				}
				/* Garantir que todos os textos dentro dos botões sejam pretos (exceto badges) */
				.sg-settings-link span:not(.status-badge),
				.sg-settings-link div:not(.status-badge),
				.sg-settings-link text {
					color: #000000 !important;
				}
				/* Melhorar legibilidade em telas menores */
				@media (max-width: 1200px) {
					.sg-settings-links {
						grid-template-columns: 1fr;
					}
					.sg-settings-link {
						font-size: 14px;
						padding: 12px 14px;
					}
				}
				/* Aumentar área de toque para mobile */
				@media (max-width: 782px) {
					.sg-settings-link {
						min-height: 48px;
						padding: 12px 16px;
					}
					.sg-settings-title {
						font-size: 16px;
					}
				}
			</style>
			
			<!-- Header -->
			<div class="sg-settings-section">
				<h3 class="sg-settings-title">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<rect x="3" y="3" width="18" height="18" rx="2"/>
						<path d="M9 3v18M3 9h18"/>
					</svg>
					Header
				</h3>
				<div class="sg-settings-links">
					<?php
					$custom_logo_id = get_theme_mod( 'custom_logo' );
					$logo_status = $custom_logo_id ? '✓ Criado' : '✗ Não configurado';
					$logo_status_class = $custom_logo_id ? '' : 'missing';
					?>
					<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[control]=custom_logo' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<rect x="3" y="3" width="18" height="18" rx="2"/>
							<circle cx="8.5" cy="8.5" r="1.5"/>
							<polyline points="21 15 16 10 5 21"/>
						</svg>
						Logo do Site
						<span class="status-badge <?php echo esc_attr( $logo_status_class ); ?>"><?php echo esc_html( $logo_status ); ?></span>
					</a>
					
					<a href="<?php echo esc_url( admin_url( 'nav-menus.php?action=edit&menu=0' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<line x1="8" y1="6" x2="21" y2="6"/>
							<line x1="8" y1="12" x2="21" y2="12"/>
							<line x1="8" y1="18" x2="21" y2="18"/>
							<line x1="3" y1="6" x2="3.01" y2="6"/>
							<line x1="3" y1="12" x2="3.01" y2="12"/>
							<line x1="3" y1="18" x2="3.01" y2="18"/>
						</svg>
						Menu Principal
					</a>
					
					<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=nav_menus' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<circle cx="12" cy="12" r="10"/>
							<path d="M12 6v6l4 2"/>
						</svg>
						Personalizar Menu
					</a>
					
					<a href="<?php echo esc_url( admin_url( 'widgets.php' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<rect x="3" y="3" width="7" height="7"/>
							<rect x="14" y="3" width="7" height="7"/>
							<rect x="14" y="14" width="7" height="7"/>
							<rect x="3" y="14" width="7" height="7"/>
						</svg>
						Widgets do Header
					</a>
				</div>
			</div>
			
			<!-- Footer -->
			<div class="sg-settings-section">
				<h3 class="sg-settings-title">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<polyline points="22 6 13.5 15.5 8.5 10.5 2 17"/>
						<polyline points="16 6 22 6 22 12"/>
					</svg>
					Footer
				</h3>
				<div class="sg-settings-links">
					<a href="<?php echo esc_url( admin_url( 'widgets.php#footer-1' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<rect x="3" y="3" width="7" height="7"/>
							<rect x="14" y="3" width="7" height="7"/>
							<rect x="14" y="14" width="7" height="7"/>
							<rect x="3" y="14" width="7" height="7"/>
						</svg>
						Widgets do Footer (4 colunas)
					</a>
					
					<a href="<?php echo esc_url( admin_url( 'nav-menus.php?action=locations' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<line x1="8" y1="6" x2="21" y2="6"/>
							<line x1="8" y1="12" x2="21" y2="12"/>
							<line x1="8" y1="18" x2="21" y2="18"/>
							<line x1="3" y1="6" x2="3.01" y2="6"/>
							<line x1="3" y1="12" x2="3.01" y2="12"/>
							<line x1="3" y1="18" x2="3.01" y2="18"/>
						</svg>
						Menu do Footer
					</a>
					
					<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=title_tagline' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
							<polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
							<line x1="12" y1="22.08" x2="12" y2="12"/>
						</svg>
						Informações da Empresa
					</a>
				</div>
			</div>
			
			<!-- Páginas Institucionais -->
			<div class="sg-settings-section">
				<h3 class="sg-settings-title">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
						<polyline points="14 2 14 8 20 8"/>
						<line x1="16" y1="13" x2="8" y2="13"/>
						<line x1="16" y1="17" x2="8" y2="17"/>
						<polyline points="10 9 9 9 8 9"/>
					</svg>
					Páginas Institucionais
				</h3>
				<div class="sg-settings-links">
					<?php
					// Sobre
					$sobre_page = get_page_by_path( 'sobre' );
					$sobre_url = $sobre_page ? get_edit_post_link( $sobre_page->ID ) : admin_url( 'post-new.php?post_type=page&title=Sobre&template=page-sobre.php' );
					$sobre_status = $sobre_page ? '✓ Criada' : '✗ Criar';
					$sobre_status_class = $sobre_page ? '' : 'missing';
					?>
					<a href="<?php echo esc_url( $sobre_url ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<circle cx="12" cy="12" r="10"/>
							<path d="M12 16v-4M12 8h.01"/>
						</svg>
						Sobre Nós
						<span class="status-badge <?php echo esc_attr( $sobre_status_class ); ?>"><?php echo esc_html( $sobre_status ); ?></span>
					</a>
					
					<?php
					// Contato
					$contato_page = get_page_by_path( 'contato' );
					$contato_url = $contato_page ? get_edit_post_link( $contato_page->ID ) : admin_url( 'post-new.php?post_type=page&title=Contato&template=page-contato.php' );
					$contato_status = $contato_page ? '✓ Criada' : '✗ Criar';
					$contato_status_class = $contato_page ? '' : 'missing';
					?>
					<a href="<?php echo esc_url( $contato_url ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
							<polyline points="22,6 12,13 2,6"/>
						</svg>
						Contato
						<span class="status-badge <?php echo esc_attr( $contato_status_class ); ?>"><?php echo esc_html( $contato_status ); ?></span>
					</a>
					
					<?php
					// Política de Privacidade
					$privacy_page_id = get_option( 'wp_page_for_privacy_policy' );
					$privacy_url = $privacy_page_id ? get_edit_post_link( $privacy_page_id ) : admin_url( 'options-privacy.php' );
					$privacy_status = $privacy_page_id ? '✓ Criada' : '✗ Criar';
					$privacy_status_class = $privacy_page_id ? '' : 'missing';
					?>
					<a href="<?php echo esc_url( $privacy_url ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
						</svg>
						Política de Privacidade
						<span class="status-badge <?php echo esc_attr( $privacy_status_class ); ?>"><?php echo esc_html( $privacy_status ); ?></span>
					</a>
					
					<?php
					// Termos de Uso
					$terms_page = get_page_by_path( 'termos-de-uso' );
					$terms_url = $terms_page ? get_edit_post_link( $terms_page->ID ) : admin_url( 'post-new.php?post_type=page&title=Termos+de+Uso' );
					$terms_status = $terms_page ? '✓ Criada' : '✗ Criar';
					$terms_status_class = $terms_page ? '' : 'missing';
					?>
					<a href="<?php echo esc_url( $terms_url ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
							<polyline points="14 2 14 8 20 8"/>
							<line x1="16" y1="13" x2="8" y2="13"/>
							<line x1="16" y1="17" x2="8" y2="17"/>
							<polyline points="10 9 9 9 8 9"/>
						</svg>
						Termos de Uso
						<span class="status-badge <?php echo esc_attr( $terms_status_class ); ?>"><?php echo esc_html( $terms_status ); ?></span>
					</a>
				</div>
			</div>
			
			<!-- Ações Rápidas -->
			<div class="sg-settings-section">
				<h3 class="sg-settings-title">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<circle cx="12" cy="12" r="10"/>
						<polyline points="12 6 12 12 16 14"/>
					</svg>
					Ações Rápidas
				</h3>
				<div class="sg-settings-links">
					<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=page' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<line x1="12" y1="5" x2="12" y2="19"/>
							<line x1="5" y1="12" x2="19" y2="12"/>
						</svg>
						Criar Nova Página
					</a>
					
					<a href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<line x1="12" y1="5" x2="12" y2="19"/>
							<line x1="5" y1="12" x2="19" y2="12"/>
						</svg>
						Criar Novo Post
					</a>
					
					<?php if ( class_exists( 'WooCommerce' ) ) : ?>
						<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=product' ) ); ?>" class="sg-settings-link">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<line x1="12" y1="5" x2="12" y2="19"/>
								<line x1="5" y1="12" x2="19" y2="12"/>
							</svg>
							Adicionar Produto
						</a>
					<?php endif; ?>
					
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
							<polyline points="15 3 21 3 21 9"/>
							<line x1="10" y1="14" x2="21" y2="3"/>
						</svg>
						Ver Site
					</a>
				</div>
			</div>
			
			<!-- Seção: Configurações Gerais -->
			<div class="sg-settings-section">
				<h3 class="sg-settings-title">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<circle cx="12" cy="12" r="3"/>
						<path d="M12 1v6m0 6v6M5.64 5.64l4.24 4.24m4.24 4.24l4.24 4.24M1 12h6m6 0h6M5.64 18.36l4.24-4.24m4.24-4.24l4.24-4.24"/>
					</svg>
					Configurações Gerais
				</h3>
				<div class="sg-settings-links">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=sg-juridico-settings' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
							<circle cx="9" cy="9" r="2"/>
							<path d="M21 15l-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
						</svg>
						Logo Painel
					</a>
					
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=sg-juridico-settings#sg_home_banner_images' ) ); ?>" class="sg-settings-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
							<rect x="7" y="7" width="10" height="10"/>
						</svg>
						Imagens do Banner
					</a>
				</div>
			</div>
		</div>
		<?php
	}
}

// Inicializar widget
new SG_Quick_Settings_Widget();

/**
 * ============================================
 * PÁGINA DE CONFIGURAÇÕES GERAIS DO TEMA
 * ============================================
 */

/**
 * Adicionar página de configurações no menu do admin
 */
function sg_add_theme_settings_page() {
	add_menu_page(
		'Configurações Gerais - SG Jurídico',
		'SG Jurídico',
		'manage_options',
		'sg-juridico-settings',
		'sg_render_settings_page',
		'dashicons-admin-generic',
		30
	);
}
add_action( 'admin_menu', 'sg_add_theme_settings_page' );

/**
 * Registrar configurações
 */
function sg_register_settings() {
	// Registrar opção do logo da admin bar
	register_setting( 'sg_juridico_settings', 'sg_admin_bar_logo_id', array(
		'type' => 'integer',
		'sanitize_callback' => 'absint',
		'default' => ''
	) );
	
	// Registrar opções das imagens do banner (até 3)
	register_setting( 'sg_juridico_settings', 'sg_home_banner_images', array(
		'type' => 'string',
		'sanitize_callback' => 'sg_sanitize_banner_images',
		'default' => ''
	) );
}
add_action( 'admin_init', 'sg_register_settings' );

/**
 * Sanitizar IDs das imagens do banner
 */
function sg_sanitize_banner_images( $value ) {
	if ( empty( $value ) ) {
		return '';
	}
	
	$image_ids = explode( ',', $value );
	$sanitized = array();
	
	foreach ( $image_ids as $id ) {
		$id = absint( trim( $id ) );
		if ( $id > 0 ) {
			$sanitized[] = $id;
		}
	}
	
	return implode( ',', $sanitized );
}

/**
 * Renderizar página de configurações
 */
function sg_render_settings_page() {
	// Verificar permissões
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	
	// Salvar configurações
	if ( isset( $_POST['sg_settings_submit'] ) && check_admin_referer( 'sg_settings_save', 'sg_settings_nonce' ) ) {
		$logo_id = isset( $_POST['sg_admin_bar_logo_id'] ) ? absint( $_POST['sg_admin_bar_logo_id'] ) : '';
		update_option( 'sg_admin_bar_logo_id', $logo_id );
		
		// Salvar imagens do banner
		$banner_images = isset( $_POST['sg_home_banner_images'] ) ? sanitize_text_field( $_POST['sg_home_banner_images'] ) : '';
		update_option( 'sg_home_banner_images', $banner_images );
		
		echo '<div class="notice notice-success is-dismissible"><p>Configurações salvas com sucesso!</p></div>';
	}
	
	// Obter valor atual do logo
	$current_logo_id = get_option( 'sg_admin_bar_logo_id', '' );
	$current_logo_url = '';
	if ( $current_logo_id ) {
		$logo_data = wp_get_attachment_image_src( $current_logo_id, 'full' );
		if ( $logo_data ) {
			$current_logo_url = $logo_data[0];
		}
	}
	
	// Obter imagens do banner
	$banner_images_str = get_option( 'sg_home_banner_images', '' );
	$banner_image_ids = array();
	if ( ! empty( $banner_images_str ) ) {
		$ids = explode( ',', $banner_images_str );
		foreach ( $ids as $id ) {
			$id = absint( trim( $id ) );
			if ( $id > 0 ) {
				$banner_image_ids[] = $id;
			}
		}
	}
	// Limitar a 3 imagens
	$banner_image_ids = array_slice( $banner_image_ids, 0, 3 );
	?>
	<div class="wrap">
		<h1>Configurações Gerais - SG Jurídico</h1>
		
		<form method="post" action="" id="sg-settings-form">
			<?php wp_nonce_field( 'sg_settings_save', 'sg_settings_nonce' ); ?>
			
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="sg_admin_bar_logo">Logo Painel</label>
						</th>
						<td>
							<div class="sg-logo-upload-wrapper">
								<input type="hidden" id="sg_admin_bar_logo_id" name="sg_admin_bar_logo_id" value="<?php echo esc_attr( $current_logo_id ); ?>" />
								
								<div id="sg-logo-preview" style="margin-bottom: 15px;">
									<?php if ( $current_logo_url ) : ?>
										<img src="<?php echo esc_url( $current_logo_url ); ?>" alt="Logo Preview" style="max-width: 200px; max-height: 60px; display: block; border: 1px solid #ddd; padding: 5px; background: #fff;" />
									<?php else : ?>
										<div style="width: 200px; height: 60px; border: 1px dashed #ddd; display: flex; align-items: center; justify-content: center; background: #f9f9f9; color: #666;">
											Nenhuma imagem selecionada
										</div>
									<?php endif; ?>
								</div>
								
								<button type="button" id="sg-upload-logo-btn" class="button">
									<?php echo $current_logo_id ? 'Alterar Logo' : 'Selecionar Logo da Biblioteca'; ?>
								</button>
								<?php if ( $current_logo_id ) : ?>
									<button type="button" id="sg-remove-logo-btn" class="button" style="margin-left: 10px;">
										Remover Logo
									</button>
								<?php endif; ?>
								
								<p class="description">
									Selecione o logo que aparecerá na barra superior do WordPress (admin bar) e na página de login, substituindo o logo padrão do WordPress.
								</p>
							</div>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label>Imagens do Banner Principal</label>
						</th>
						<td>
							<div class="sg-banner-images-wrapper">
								<p class="description" style="margin-bottom: 15px;">
									<strong>Recomendações de formato:</strong> Para melhor visualização, use imagens no formato <strong>horizontal (landscape)</strong> com proporção <strong>16:9</strong> ou <strong>2:1</strong>. 
									Resolução recomendada: <strong>1200x675px</strong> ou <strong>1600x900px</strong>. Formatos aceitos: JPG, PNG ou WebP.
								</p>
								
								<input type="hidden" id="sg_home_banner_images" name="sg_home_banner_images" value="<?php echo esc_attr( implode( ',', $banner_image_ids ) ); ?>" />
								
								<div id="sg-banner-images-preview" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-bottom: 15px;">
									<?php 
									for ( $i = 0; $i < 3; $i++ ) :
										$img_id = isset( $banner_image_ids[$i] ) ? $banner_image_ids[$i] : 0;
										$img_url = '';
										if ( $img_id ) {
											$img_data = wp_get_attachment_image_src( $img_id, 'medium' );
											if ( $img_data ) {
												$img_url = $img_data[0];
											}
										}
										?>
										<div class="sg-banner-image-item" data-index="<?php echo $i; ?>" data-image-id="<?php echo $img_id; ?>">
											<label style="display: block; margin-bottom: 8px; font-weight: 600;">Banner <?php echo $i + 1; ?></label>
											<div class="sg-banner-image-preview" style="width: 100%; aspect-ratio: 16/9; border: 2px dashed #ddd; border-radius: 4px; background: #f9f9f9; display: flex; align-items: center; justify-content: center; margin-bottom: 8px; overflow: hidden;">
												<?php if ( $img_url ) : ?>
													<img src="<?php echo esc_url( $img_url ); ?>" alt="Banner <?php echo $i + 1; ?>" style="width: 100%; height: 100%; object-fit: cover;" />
												<?php else : ?>
													<span style="color: #666; font-size: 14px;">Nenhuma imagem</span>
												<?php endif; ?>
											</div>
											<div style="display: flex; gap: 8px;">
												<button type="button" class="button sg-upload-banner-btn" data-index="<?php echo $i; ?>">
													<?php echo $img_id ? 'Alterar' : 'Selecionar'; ?>
												</button>
												<?php if ( $img_id ) : ?>
													<button type="button" class="button sg-remove-banner-btn" data-index="<?php echo $i; ?>">
														Remover
													</button>
												<?php endif; ?>
											</div>
										</div>
									<?php endfor; ?>
								</div>
								
								<p class="description">
									Você pode adicionar até 3 imagens que serão exibidas na página inicial (home) em substituição ao carrossel.
								</p>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			
			<?php submit_button( 'Salvar Configurações', 'primary', 'sg_settings_submit', false ); ?>
		</form>
	</div>
	
	<style>
		.sg-logo-upload-wrapper {
			max-width: 600px;
		}
		#sg-logo-preview img {
			border-radius: 4px;
		}
	</style>
	
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		var mediaUploader;
		
		// Botão de upload
		$('#sg-upload-logo-btn').on('click', function(e) {
			e.preventDefault();
			
			// Se o uploader já existe, abrir
			if (mediaUploader) {
				mediaUploader.open();
				return;
			}
			
			// Criar novo uploader
			mediaUploader = wp.media({
				title: 'Selecionar Logo Painel',
				button: {
					text: 'Usar este logo'
				},
				multiple: false,
				library: {
					type: 'image'
				}
			});
			
			// Quando uma imagem é selecionada
			mediaUploader.on('select', function() {
				var attachment = mediaUploader.state().get('selection').first().toJSON();
				
				// Atualizar campo hidden
				$('#sg_admin_bar_logo_id').val(attachment.id);
				
				// Atualizar preview
				$('#sg-logo-preview').html(
					'<img src="' + attachment.url + '" alt="Logo Preview" style="max-width: 200px; max-height: 60px; display: block; border: 1px solid #ddd; padding: 5px; background: #fff; border-radius: 4px;" />'
				);
				
				// Atualizar texto do botão
				$('#sg-upload-logo-btn').text('Alterar Logo');
				
				// Mostrar botão de remover se não existir
				if ($('#sg-remove-logo-btn').length === 0) {
					$('#sg-upload-logo-btn').after(
						'<button type="button" id="sg-remove-logo-btn" class="button" style="margin-left: 10px;">Remover Logo</button>'
					);
					
					// Adicionar evento ao botão de remover
					$('#sg-remove-logo-btn').on('click', function() {
						$('#sg_admin_bar_logo_id').val('');
						$('#sg-logo-preview').html(
							'<div style="width: 200px; height: 60px; border: 1px dashed #ddd; display: flex; align-items: center; justify-content: center; background: #f9f9f9; color: #666;">Nenhuma imagem selecionada</div>'
						);
						$('#sg-upload-logo-btn').text('Selecionar Logo da Biblioteca');
						$(this).remove();
					});
				}
			});
			
			// Abrir uploader
			mediaUploader.open();
		});
		
		// Botão de remover
		$(document).on('click', '#sg-remove-logo-btn', function(e) {
			e.preventDefault();
			$('#sg_admin_bar_logo_id').val('');
			$('#sg-logo-preview').html(
				'<div style="width: 200px; height: 60px; border: 1px dashed #ddd; display: flex; align-items: center; justify-content: center; background: #f9f9f9; color: #666;">Nenhuma imagem selecionada</div>'
			);
			$('#sg-upload-logo-btn').text('Selecionar Logo da Biblioteca');
			$(this).remove();
		});
		
		// ========== GERENCIAMENTO DE IMAGENS DO BANNER ==========
		var bannerUploaders = {};
		
		// Função para atualizar o campo hidden com todas as imagens
		function updateBannerImagesField() {
			var imageIds = [];
			$('.sg-banner-image-item').each(function() {
				var $item = $(this);
				var imgId = $item.data('image-id');
				if (imgId && imgId > 0) {
					imageIds.push(imgId);
				}
			});
			$('#sg_home_banner_images').val(imageIds.join(','));
		}
		
		// Botão de upload de banner
		$(document).on('click', '.sg-upload-banner-btn', function(e) {
			e.preventDefault();
			var index = $(this).data('index');
			var $item = $('.sg-banner-image-item[data-index="' + index + '"]');
			
			// Criar ou reutilizar uploader
			if (!bannerUploaders[index]) {
				bannerUploaders[index] = wp.media({
					title: 'Selecionar Imagem do Banner ' + (parseInt(index) + 1),
					button: {
						text: 'Usar esta imagem'
					},
					multiple: false,
					library: {
						type: 'image'
					}
				});
				
				// Quando uma imagem é selecionada
				bannerUploaders[index].on('select', function() {
					var attachment = bannerUploaders[index].state().get('selection').first().toJSON();
					
					// Salvar ID da imagem no elemento
					$item.data('image-id', attachment.id);
					
					// Atualizar preview
					var $preview = $item.find('.sg-banner-image-preview');
					$preview.html('<img src="' + attachment.url + '" alt="Banner ' + (parseInt(index) + 1) + '" style="width: 100%; height: 100%; object-fit: cover;" />');
					
					// Atualizar texto do botão
					$(this).find('.sg-upload-banner-btn').text('Alterar');
					
					// Mostrar botão de remover se não existir
					if ($item.find('.sg-remove-banner-btn').length === 0) {
						$item.find('.sg-upload-banner-btn').after(
							'<button type="button" class="button sg-remove-banner-btn" data-index="' + index + '">Remover</button>'
						);
					}
					
					// Atualizar campo hidden
					updateBannerImagesField();
				});
			}
			
			// Abrir uploader
			bannerUploaders[index].open();
		});
		
		// Botão de remover banner
		$(document).on('click', '.sg-remove-banner-btn', function(e) {
			e.preventDefault();
			var index = $(this).data('index');
			var $item = $('.sg-banner-image-item[data-index="' + index + '"]');
			
			// Limpar dados
			$item.data('image-id', '');
			
			// Atualizar preview
			$item.find('.sg-banner-image-preview').html(
				'<span style="color: #666; font-size: 14px;">Nenhuma imagem</span>'
			);
			
			// Atualizar texto do botão
			$item.find('.sg-upload-banner-btn').text('Selecionar');
			
			// Remover botão de remover
			$(this).remove();
			
			// Atualizar campo hidden
			updateBannerImagesField();
		});
		
		// Carregar IDs existentes nos elementos ao carregar a página
		<?php if ( ! empty( $banner_image_ids ) ) : ?>
			<?php foreach ( $banner_image_ids as $idx => $img_id ) : ?>
				$('.sg-banner-image-item[data-index="<?php echo $idx; ?>"]').data('image-id', <?php echo $img_id; ?>);
			<?php endforeach; ?>
		<?php endif; ?>
	});
	</script>
	<?php
}

/**
 * Enqueue scripts e estilos para a página de configurações
 */
function sg_settings_enqueue_scripts( $hook ) {
	// Carregar apenas na página de configurações
	if ( 'toplevel_page_sg-juridico-settings' !== $hook ) {
		return;
	}
	
	// Enqueue WordPress media uploader
	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'sg_settings_enqueue_scripts' );

/**
 * Obter imagens do banner configuradas ou imagens padrão da biblioteca
 */
function sg_get_home_banner_images() {
	// Obter imagens configuradas
	$banner_images_str = get_option( 'sg_home_banner_images', '' );
	$banner_image_ids = array();
	
	if ( ! empty( $banner_images_str ) ) {
		$ids = explode( ',', $banner_images_str );
		foreach ( $ids as $id ) {
			$id = absint( trim( $id ) );
			if ( $id > 0 ) {
				// Verificar se a imagem ainda existe antes de adicionar
				$image_url = wp_get_attachment_image_url( $id, 'full' );
				if ( $image_url ) {
					$banner_image_ids[] = $id;
				}
			}
		}
	}
	
	// Limitar a 3 imagens
	$banner_image_ids = array_slice( $banner_image_ids, 0, 3 );
	
	return $banner_image_ids;
}

/**
 * Converter URLs de produção para localhost quando necessário
 * Isso resolve o problema de imagens não carregarem localmente quando o banco foi migrado de produção
 */
function sg_convert_production_urls_to_localhost( $url ) {
	// Se não for uma URL completa, retornar como está
	if ( ! is_string( $url ) || empty( $url ) ) {
		return $url;
	}
	
	// Verificar se estamos em ambiente local (localhost)
	$home_url = home_url();
	$is_local = (
		strpos( $home_url, 'localhost' ) !== false ||
		strpos( $home_url, '127.0.0.1' ) !== false ||
		strpos( $home_url, 'local' ) !== false ||
		( defined( 'WP_ENVIRONMENT_TYPE' ) && WP_ENVIRONMENT_TYPE === 'local' )
	);
	
	// Se não estiver em ambiente local, retornar URL original
	if ( ! $is_local ) {
		return $url;
	}
	
	// Converter URLs de produção para localhost
	$production_domains = array(
		'https://sgjuridico.com.br',
		'http://sgjuridico.com.br',
		'sgjuridico.com.br'
	);
	
	foreach ( $production_domains as $domain ) {
		if ( strpos( $url, $domain ) !== false ) {
			// Substituir pelo domínio local
			$url = str_replace( $domain, $home_url, $url );
			break;
		}
	}
	
	return $url;
}

// Aplicar filtro em todas as URLs de attachments
add_filter( 'wp_get_attachment_url', 'sg_convert_production_urls_to_localhost', 10, 1 );
add_filter( 'attachment_link', 'sg_convert_production_urls_to_localhost', 10, 1 );

// Filtrar URLs dentro de wp_get_attachment_image_src
add_filter( 'wp_get_attachment_image_src', function( $image, $attachment_id, $size, $icon ) {
	if ( is_array( $image ) && ! empty( $image[0] ) ) {
		$image[0] = sg_convert_production_urls_to_localhost( $image[0] );
	}
	return $image;
}, 10, 4 );

// Filtrar URLs retornadas por wp_get_attachment_image_url
add_filter( 'wp_get_attachment_image_url', 'sg_convert_production_urls_to_localhost', 10, 1 );

