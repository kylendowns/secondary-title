<?php
/**
 * (C) Copyright 2021 by Kolja Nolte
 * kolja.nolte@gmail.com
 * https://www.kolja-nolte.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * @see    https://wordpress.org/plugins/secondary-title/
 * @author Kolja Nolte <kolja.nolte@gmail.com>
 */

/**
 * Plugin Name:   Secondary Title
 * Plugin URI:    https://www.kolja-nolte.com/wordpress/plugins/secondary-title/
 * Description:   Adds a secondary title to posts, pages and custom post types.
 * Version:       3.0.0
 * Author:        Kolja Nolte
 * Author URI:    https://www.kolja-nolte.com
 * License:       GPLv2 or later
 * License URI:   http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:   secondary-title
 */

// If this file is called directly, abort.
if (! defined(constant_name: 'ABSPATH')) {
    exit;
}

/**
 * Add our autoloader for getting classes out of the 'includes' directory
 */
require_once plugin_dir_path(file: __FILE__) . 'autoload.php';

/**
 * Set up plugin and confirm default options are set.
 */
//register_activation_hook(__FILE__, array( 'SecondaryTitle\\Activator', 'activate' ));

/**
 * Return a single instance of the Secondary_Title class.
 *
 * @return SecondaryTitle\SecondaryTitle
 */
function secondary_title(): \SecondaryTitle\SecondaryTitle
{
    return SecondaryTitle\SecondaryTitle::instance(plugin_file: __FILE__);
}

/**
 * Kick off the plugin!
 */
function secondary_title_initialize(): void {
    secondary_title()->initialize();
}

add_action(hook_name: 'plugins_loaded', callback: 'secondary_title_initialize');
