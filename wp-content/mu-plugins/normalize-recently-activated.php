<?php
/**
 * Normalize the `recently_activated` option to avoid PHP type errors.
 */

function sg_normalize_recently_activated_list( $value ) {
	if ( empty( $value ) || ! is_array( $value ) ) {
		return array();
	}

	$normalized = array();

	foreach ( $value as $plugin => $timestamp ) {
		if ( empty( $plugin ) ) {
			continue;
		}

		$normalized[ $plugin ] = is_numeric( $timestamp ) ? (int) $timestamp : time();
	}

	return $normalized;
}

add_filter( 'option_recently_activated', 'sg_normalize_recently_activated_list' );
add_filter( 'pre_update_option_recently_activated', 'sg_normalize_recently_activated_list' );
add_filter( 'option_woocommerce_task_list_hidden', 'sg_normalize_recently_activated_list' );
add_filter( 'pre_update_option_woocommerce_task_list_hidden', 'sg_normalize_recently_activated_list' );
add_filter( 'pre_option_woocommerce_task_list_hidden', function( $value ) {
    if ( empty( $value ) || ! is_array( $value ) ) {
        return array();
    }
    return $value;
} );

add_filter( 'option_woocommerce_task_list_hidden_lists', 'sg_normalize_recently_activated_list' );
add_filter( 'pre_update_option_woocommerce_task_list_hidden_lists', 'sg_normalize_recently_activated_list' );
add_filter( 'pre_option_woocommerce_task_list_hidden_lists', function( $value ) {
    if ( empty( $value ) || ! is_array( $value ) ) {
        return array();
    }
    return $value;
} );


