<?php
/**
 * Template Name: Minha Conta (Sidebar)
 * Description: Página Minha Conta com menu lateral fixo e conteúdo ao lado.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main">
	<div class="container">
    <?php
        if ( ! function_exists( 'wc_get_template' ) ) {
            echo '<p>' . esc_html__( 'WooCommerce não está ativo.', 'sg-juridico' ) . '</p>';
        } else {
            // Respeitar fluxo de login do WooCommerce
            if ( ! is_user_logged_in() ) {
                wc_get_template( 'myaccount/form-login.php' );
            } else {
                ?>
                <div class="sg-my-account-layout">
                    <aside class="sg-my-account-sidebar">
                        <?php do_action( 'woocommerce_account_navigation' ); ?>
                    </aside>
                    <div class="woocommerce-MyAccount-content sg-my-account-content">
                        <?php do_action( 'woocommerce_account_content' ); ?>
                    </div>
                </div>
                <?php
            }
        }
        ?>
	</div>
</main>

<?php get_footer();


