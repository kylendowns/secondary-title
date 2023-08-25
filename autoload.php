<?php
/**
 * autoload.php
 * @package SecondaryTitle
 */

spl_autoload_register(
	callback: function ($class) {
		$prefix   = 'SecondaryTitle\\';
		$base_dir = __DIR__ . '/includes/';
		$len      = strlen( string: $prefix );

		if ( strncmp( string1: $prefix, string2: $class, length: $len ) !== 0 ) {
			return;
		}

		$relative_class = substr( string: $class, offset: $len );
		$relative_class = str_replace( search: '_', replace: '-', subject: $relative_class );
		$relative_class = strtolower( string: preg_replace( pattern: '/([^\\\\]+)$/i', replacement: '$2class-$1', subject: $relative_class ) );
		$file           = $base_dir . str_replace( search: '\\', replace: '/', subject: $relative_class ) . '.php';

		if ( file_exists( filename: $file ) ) {
			require $file;
		}
	}
);