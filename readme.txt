=== Dynamic Format ===
Contributors: PiotrPress
Tags: format, format-api, formatting-toolbar-api, dynamic-format, dynamic-block, dynamic-blocks, gutenberg, block, blocks, php-block, php-blocks, fse, full-site-editing
Requires at least: 6.2.2
Tested up to: 6.2.2
Stable tag: trunk
Requires PHP: 7.4
License: GPL v3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

This WordPress plugin adds a dynamic format which renders an output of a selected php callback function added via a filter hook.

== Description ==

This WordPress plugin adds a dynamic [format](https://developer.wordpress.org/block-editor/how-to-guides/format-api/) which renders an output of a selected php callback function added via a [filter hook](https://developer.wordpress.org/plugins/hooks/filters/).

= Usage =

Add `piotrpress/dynamic_format/callbacks` filter.

= Example =

`add_filter( 'piotrpress/dynamic_format/callbacks', function( $callbacks ) {
     $callbacks[ 'Current date' ] = function( $content ) { return date( 'Y-m-d H:i:s' ); };
     return $callbacks;
 } );`

== Screenshots ==

1. Dynamic format in WordPress block editor view