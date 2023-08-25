<?php

namespace SecondaryTitle;

use Exception;

/**
 * Class Activator
 * @package SecondaryTitle
 */
class Activator
{
    /**
     * Creates the plugin option rows in the database (if they do not exist)
     */
    public static function activate(): void
    {
        try {
            (new Config())::secondary_title_activate();
        } catch (Exception $exception) {
            deactivate_plugins(plugins: plugin_basename(file: secondary_title()->plugin_dir));
            wp_die(message: 'Secondary Title could not activate: ' . $exception);
        }
    }
}