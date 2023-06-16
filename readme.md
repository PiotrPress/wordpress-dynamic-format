# WordPress Dynamic Format

This WordPress plugin adds a dynamic [format](https://developer.wordpress.org/block-editor/how-to-guides/format-api/) which renders an output of a selected php callback function added via a [filter hook](https://developer.wordpress.org/plugins/hooks/filters/).

## Usage

Add `piotrpress/dynamic_format/callbacks` filter.

## Example

```php
add_filter( 'piotrpress/dynamic_format/callbacks', function( $callbacks ) {
    $callbacks[ 'Current date' ] = function( $content ) { return date( 'Y-m-d H:i:s' ); };
    return $callbacks;
} );
```

## Screenshot

![Dynamic format in WordPress block editor view](screenshot-1.png)

## Requirements

PHP >= `7.4` version.

## License

[GPL v3 or later](license.txt)