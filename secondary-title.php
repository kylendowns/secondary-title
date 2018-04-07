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

   /** Include plugin files */
   $include_directories = array(
      "includes"
   );

   /** Loop through the set directories */
   foreach((array)$include_directories as $include_directory) {
      $include_directory = plugin_dir_path(__FILE__) . $include_directory;

      /** Skip directory if it's not a valid directory */
      if(!is_dir($include_directory)) {
         continue;
      }

      /** Gather all .php files within the current directory */
      $include_files = glob($include_directory . "/*.php");
      foreach($include_files as $include_file) {
         /** Skip file if file is not valid */
         if(!is_file($include_file)) {
            continue;
         }

         /** Include current file */
         include_once $include_file;
      }
   }