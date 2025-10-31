<?php
/**
 * My Account page (SG Jurídico override)
 *
 * Wraps navigation and content side-by-side in a grid layout.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="sg-my-account-layout">
    <aside class="sg-my-account-sidebar">
        <?php
        /**
         * My Account navigation.
         * @since 2.6.0
         */
        do_action( 'woocommerce_account_navigation' );
        ?>
    </aside>

    <div class="woocommerce-MyAccount-content sg-my-account-content">
        <?php
        /**
         * My Account content.
         * @since 2.6.0
         */
        do_action( 'woocommerce_account_content' );
        ?>
    </div>
</div>


