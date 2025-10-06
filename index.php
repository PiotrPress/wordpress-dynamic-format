<?php declare( strict_types = 1 );

/**
 * Plugin Name: Dynamic Format
 * Plugin URI: https://github.com/PiotrPress/wordpress-dynamic-format
 * Description: This WordPress plugin adds a dynamic format which renders an output of a selected php callback function added via a filter hook.
 * Version: 0.2.0
 * Requires at least: 6.2.2
 * Requires PHP: 7.4
 * Author: Piotr Niewiadomski
 * Author URI: https://piotr.press
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: dynamic-format
 * Domain Path: /languages
 * Update URI: false
 */

defined( 'ABSPATH' ) or exit;

add_action( 'init', function() : void {
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

add_action( 'enqueue_block_editor_assets', function() : void {
    wp_enqueue_style( 'dynamic-format' );
    wp_enqueue_script( 'dynamic-format' );
    wp_add_inline_script( 'dynamic-format',
        'const dynamicFormatCallbacks = ' . json_encode( ( function() : array {
            $callbacks = [ [ 'label' => __( 'None', 'dynamic-format' ), 'value' => '' ] ];
            foreach( apply_filters( 'piotrpress/dynamic_format/callbacks', [] ) as $key => $callback )
                $callbacks[] = [
                    'label' => (string)( is_array( $callback ) and isset( $callback[ 'label' ] ) ) ? $callback[ 'label' ] : $key,
                    'value' => $key
                ];
            return $callbacks;
        } )() ),
        'before'
    );
} );

add_filter( 'render_block', function( string $block_content ) : string {
    if( false !== strpos( $block_content, 'dynamic-format' ) ) {
        return preg_replace_callback('/(<span.*?class=\"dynamic-format\".*?>.*?<\\/span>)/i',
            function( $matches ) {
                return preg_replace_callback('/(?:<span.*?data-callback=\"(.*?)\".*?>)(.*?)(?:<\\/span>)/i',
                    function( $matches ) {
                        $callbacks = apply_filters( 'piotrpress/dynamic_format/callbacks', [] );
                        $key = $matches[ 1 ] ?? '';
                        $content = $matches[ 2 ] ?? '';
                        $callback = $callbacks[ $key ] ?? '';
                        $value = ( is_array( $callback ) and isset( $callback[ 'value' ] ) ) ? $callback[ 'value' ] : $callback;
                        return is_callable( $value ) ? (string)call_user_func( $value, $content ) : $matches[ 0 ];
                }, $matches[ 0 ] );
        }, $block_content );
    }
    return $block_content;
} );