<?php
   /**
    * Plugin Name:   Secondary Title
    * Plugin URI:    https://www.koljanolte.com/
    * Description:   Adds a secondary title to posts, pages and custom post types.
    * Version:       1.9.5
    * Author:        Kolja Nolte
    * Author URI:    http://www.koljanolte.com
    * License:       GPLv2 or later
    * License URI:   http://www.gnu.org/licenses/gpl-2.0.html
    * Text Domain:   secondary-title
    * Domain Path:   /languages
    */

   /**
    * Stop script when the file is called directly.
    */
   if(!function_exists("add_action")) {
      return false;
   }

   define("SECONDARY_TITLE_PATH", plugin_dir_path(__FILE__));
   define("SECONDARY_TITLE_URL", plugin_dir_url(__FILE__));
   define("SECONDARY_TITLE_VERSION", "1.9.4");

   define("TEXTDOMAIN", ""); // TODO: Remove

   /** Install default settings (if not set yet) */
   register_activation_hook(__FILE__, "secondary_title_install");

   function secondary_title_load_translations() {
      load_plugin_textdomain(
         "secondary-title",
         false,
         plugin_basename(
            plugin_dir_path(__FILE__)
         ) . "/languages"
      );
   }

   add_action("plugins_loaded", "secondary_title_load_translations");

   $include_files = glob(SECONDARY_TITLE_PATH . "includes/*.php");

   foreach($include_files as $include_file) {
      if(realpath($include_file)) {
         require_once $include_file;
      }
   }
