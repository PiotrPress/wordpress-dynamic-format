<?php

/*
 * Plugin Name: Dynamic Format
 * Plugin URI: https://github.com/PiotrPress/wordpress-dynamic-format
 * Description: This WordPress plugin adds a dynamic format which renders an output of a selected php callback function added via a filter hook.
 * Version: 0.1.0
 * Requires at least: 6.2.2
 * Requires PHP: 7.4
 * Author: Piotr Niewiadomski
 * Author URI: https://piotr.press
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Update URI: false
 */

defined( 'ABSPATH' ) or exit;

add_action( 'init', function() {
    $asset_file = include plugin_dir_path( __FILE__ ) . 'build/index.asset.php';

    wp_register_script('dynamic-format',
        plugins_url( 'build/index.js', __FILE__ ),
        $asset_file[ 'dependencies' ],
        $asset_file[ 'version' ],
        true
    );

    wp_register_style('dynamic-format',
        plugins_url( 'build/style-index.css', __FILE__ ),
        [],
        $asset_file[ 'version' ]
    );
} );

add_action( 'enqueue_block_editor_assets', function() {
    wp_enqueue_style( 'dynamic-format' );
    wp_enqueue_script( 'dynamic-format' );
    wp_add_inline_script( 'dynamic-format',
        'const dynamicFormatCallbacks = ' . json_encode( ( function() {
            $callbacks = [ [ 'label' => __( 'None', 'dynamic-format' ), 'value' => '' ] ];
            foreach ( apply_filters( 'piotrpress/dynamic_format/callbacks', [] ) as $label => $callback )
                $callbacks[] = [ 'label' => $label, 'value' => $label ];
            return $callbacks;
        } )() ),
        'before'
    );
} );

add_filter( 'render_block', function( $block_content ) {
    if ( false !== strpos( $block_content, 'dynamic-format' ) ) {
        return preg_replace_callback('/(<span.*?class=\"dynamic-format\".*?>.*?<\\/span>)/i',
            function( $matches ) {
                return preg_replace_callback('/(?:<span.*?data-callback=\"(.*?)\".*?>)(.*?)(?:<\\/span>)/i',
                    function( $matches ) {
                        if ( $matches[ 1 ] and
                            $callbacks = apply_filters( 'piotrpress/dynamic_format/callbacks', [] ) and
                            isset( $callbacks[ $matches[ 1 ] ] ) and
                            is_callable( $callbacks[ $matches[ 1 ] ] ) )
                            return call_user_func( $callbacks[ $matches[ 1 ] ], $matches[ 2 ] );
                        return $matches[ 0 ];
                }, $matches[ 0 ] );
        }, $block_content );
    }

    return $block_content;
} );