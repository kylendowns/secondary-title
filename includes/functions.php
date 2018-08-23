<?php
   /**
    * (C) 2018 by Kolja Nolte
    * kolja@koljanolte.com
    * https://www.koljanolte.com
    *
    * This program is free software; you can redistribute it and/or modify
    * it under the terms of the GNU General Public License as published by
    * the Free Software Foundation; either version 2 of the License, or
    * (at your option) any later version.
    *
    * @project Secondary Title
    */

   /**
    * This file contains the main functions that can be used to return,
    * display or modify every information that is related to the plugin.
    *
    * @package    Secondary Title
    * @subpackage Global
    */

   /**
    * Stop script when the file is called directly.
    *
    * @since 0.1.0
    */
   if(!function_exists("add_action")) {
      return false;
   }

   /**
    * Sets the default settings when plugin is activated (and no settings exist).
    *
    * @since 1.0.0
    *
    * @return bool
    */
   function secondary_title_install() {
      $installed = true;

      /** Use update_option() to create the default options  */
      foreach(secondary_title_get_default_settings() as $setting => $value) {
         if(get_option($setting)) {
            continue;
         }

         if(!update_option($setting, $value)) {
            $installed = false;
         }
      }

      return $installed;
   }

   /**
    * Resets all settings back to the default values.
    *
    * @since 1.6.0
    *
    * @return bool
    */
   function secondary_title_reset_settings() {
      $default_settings = secondary_title_get_default_settings();

      foreach($default_settings as $setting => $default_value) {
         update_option($setting, $default_value);
      }

      return true;
   }

   /**
    * Returns all settings and their default values used by Secondary Title.
    *
    * @return array
    *
    * @since 0.1.0
    */
   function secondary_title_get_default_settings() {
      /** Define the default settings and their values */
      $default_settings = array(
         "secondary_title_post_types"             => array(),
         "secondary_title_categories"             => array(),
         "secondary_title_post_ids"               => array(),
         "secondary_title_auto_show"              => "on",
         "secondary_title_title_format"           => "%secondary_title%: %title%",
         "secondary_title_input_field_position"   => "above",
         "secondary_title_only_show_in_main_post" => "off",
         "secondary_title_use_in_permalinks"      => "off",
         "secondary_title_permalinks_position"    => "prepend",
         "secondary_title_column_position"        => "right",
         "secondary_title_feed_auto_show"         => "off",
         "secondary_title_feed_title_format"      => "%title%",
         "secondary_title_include_in_search"      => "on",
         "secondary_title_show_donation_notice"   => "on"
      );

      $default_settings = apply_filters("secondary_title_get_default_settings", $default_settings);

      return (array)$default_settings;
   }

   /**
    * Returns all settings generated by Secondary Title and their current values.
    *
    * @since 0.1.0
    *
    * @param bool $use_prefix
    *
    * @return array
    */
   function secondary_title_get_settings($use_prefix = true) {
      $settings = array();

      foreach(secondary_title_get_default_settings() as $setting => $default_value) {
         $option = get_option($setting);
         $value  = $default_value;

         if($option) {
            $value = $option;
         }

         if(!$use_prefix) {
            $setting = str_replace("secondary_title_", "", $setting);
         }

         $settings[$setting] = $value;
      }

      return $settings;
   }

   /**
    * Returns a specific setting for the plugin. If the selected
    * option is unset, the default value will be returned.
    *
    * @since 0.1.0
    *
    * @param $setting
    *
    * @return mixed
    */
   function secondary_title_get_setting($setting) {
      $settings = secondary_title_get_settings();

      if(isset($settings["secondary_title_$setting"])) {
         $setting = $settings["secondary_title_$setting"];
      }

      return $setting;
   }

   /**
    * Returns the IDs of the posts for which secondary title is activated.
    *
    * @since 0.1.0
    *
    * @return array Post IDs
    */
   function get_secondary_title_post_ids() {
      return (array)secondary_title_get_setting("post_ids");
   }

   /**
    * Returns the post types for which secondary title is activated.
    *
    * @since 0.1.0
    *
    * @return array Post types
    */
   function get_secondary_title_post_types() {
      return (array)secondary_title_get_setting("post_types");
   }

   /**
    * Returns the categories for which secondary title is activated.
    *
    * @since 0.1.0
    *
    * @return array Selected categories
    */
   function get_secondary_title_post_categories() {
      return (array)secondary_title_get_setting("categories");
   }

   /**
    * Get the secondary title from post ID $post_id
    *
    * @since 0.1.0
    *
    * @param int    $post_id      ID of target post.
    * @param string $prefix       To be added in front of the secondary title.
    * @param string $suffix       To be added after the secondary title.
    * @param bool   $use_settings Use filters set on Secondary Title settings page.
    *
    * @return string The secondary title
    */
   function get_secondary_title($post_id = 0, $prefix = "", $suffix = "", $use_settings = false) {
      /** If $post_id not set, use current post ID */
      if(!$post_id) {
         $post_id = (int)get_the_ID();
      }

      /** Get the secondary title and return false if it's empty actually empty */
      $secondary_title = get_post_meta($post_id, "_secondary_title", true);

      if(!$secondary_title) {
         return "";
      }

      /** Use filters set on Secondary Title settings page */
      if($use_settings && !secondary_title_validate($post_id)) {
         return "";
      }

      $secondary_title = $prefix . $secondary_title . $suffix;

      /** Apply filters to secondary title if used with Word Filter Plus plugin */
      if(class_exists("WordFilter")) {
         /** @noinspection PhpUndefinedClassInspection */
         $word_filter = new WordFilter;
         /** @noinspection PhpUndefinedMethodInspection */
         $secondary_title = $word_filter->filter_title($secondary_title);
      }

      $secondary_title = apply_filters("get_secondary_title", $secondary_title, $post_id, $prefix, $suffix);

      return (string)$secondary_title;
   }

   /**
    * Prints the secondary title and adds an optional suffix.
    *
    * @since 0.1.0
    *
    * @param int    $post_id      ID of target post.
    * @param string $prefix       To be added in front of the secondary title.
    * @param string $suffix       To be added after the secondary title.
    * @param bool   $use_settings Use filters set on Secondary Title settings page.
    */
   function the_secondary_title($post_id = 0, $prefix = "", $suffix = "", $use_settings = false) {
      $secondary_title = get_secondary_title(
         $post_id,
         $prefix,
         $suffix,
         $use_settings
      );

      $secondary_title = apply_filters(
         "the_secondary_title",
         $secondary_title,
         $post_id,
         $prefix,
         $suffix
      );

      echo $secondary_title;
   }

   /**
    * Returns whether the specified post has a
    * secondary title or not.
    *
    * @since 0.5.1
    *
    * @param int $post_id Post ID of the post in question.
    *
    * @return bool
    */
   function has_secondary_title($post_id = 0) {
      $secondary_title = get_secondary_title($post_id);
      $has             = false;

      if($secondary_title) {
         $has = true;
      }

      return $has;
   }

   /**
    * Returns all available post types except pages, attachments,
    * revision ans nav_menu_items.
    *
    * @since 0.1.0
    *
    * @return array
    */
   function get_secondary_title_filtered_post_types() {
      /** Returns all registered post types */
      $post_types = get_post_types(
         array(
            "public" => true, // Only show post types that are publicly accessible in the front end
         )
      );

      return $post_types;
   }

   /**
    * Returns all posts that have a valid
    * secondary title.
    *
    * @param array $additional_query
    *
    * @internal param int $count
    *
    * @since    0.9.2
    *
    * @return array
    */
   function get_posts_with_secondary_title(array $additional_query = array()) {
      $query_arguments = array(
         "post_type"    => "any",
         "meta_key"     => "_secondary_title",
         "meta_value"   => " ",
         "meta_compare" => "!=",
         "post_status"  => "publish"
      );

      $query_arguments = wp_parse_args($query_arguments, $additional_query);

      return get_posts($query_arguments);
   }

   /**
    * Returns a random post that has a valid
    * secondary title.
    *
    * @since 0.9.2
    *
    * @return bool|WP_Post
    */
   function get_random_post_with_secondary_title() {
      $post = get_posts_with_secondary_title(
         array(
            "showposts" => 1,
            "orderby"   => "rand"
         )
      );

      if(!$post) {
         return false;
      }

      return $post[0];
   }

   /**
    * @param array $new_settings
    *
    * @since 1.4.0
    *
    * @return bool
    */
   function secondary_title_update_settings(array $new_settings = array()) {
      $saved  = false;
      $arrays = array(
         "post_types",
         "categories"
      );

      foreach(secondary_title_get_default_settings() as $full_setting_name => $default_value) {
         $setting_name = str_replace("secondary_title_", "", $full_setting_name);
         $value        = "";

         if(isset($new_settings[$setting_name])) {
            $value = $new_settings[$setting_name];

            if($setting_name === "post_ids") {
               $value = preg_replace("'[^0-9,]'", "", $value);

               if(!is_array($value)) {
                  $value = explode(",", $value);
               }
            }

            if($setting_name === "post_ids" && (!$new_settings[$setting_name] || $value[0] === "")) {
               $value = array();
            }
         }
         elseif(in_array($setting_name, $arrays, true)) {
            $value = array();
         }
         if(update_option($full_setting_name, $value)) {
            $saved = true;
         }
      }

      return $saved;
   }

   /**
    * Checks whether the secondary title is allowed
    * to be displayed according to the settings set
    * on Secondary Title's settings page.
    *
    * @since 1.4.0
    *
    * @param $post_id
    *
    * @return bool
    */
   function secondary_title_validate($post_id) {
      $allowed_post_types = get_secondary_title_post_types();
      $allowed_categories = get_secondary_title_post_categories();
      $allowed_post_ids   = get_secondary_title_post_ids();
      $post_categories    = wp_get_post_categories($post_id);

      /** Check if post type is among the allowed post types */
      if(count($allowed_post_types) && !in_array(get_post_type($post_id), $allowed_post_types, false)) {
         return false;
      }

      /** Check if post's categories are among the allowed categories */
      $in_categories = false;
      foreach($post_categories as $category_id) {
         if(in_array($category_id, $allowed_categories, false)) {
            $in_categories = true;
         }
      }
      if(!$in_categories && count($allowed_categories)) {
         return false;
      }

      return !in_array($post_id, $allowed_post_ids, false);
   }

   /**
    * Verifies whether plugin settings allow secondary title
    * input box to be displayed.
    *
    * @since 1.7.0
    *
    * @return bool
    */
   function secondary_title_verify_admin_page() {
      global $post;

      $category_taxonomy  = get_taxonomy("category");
      $allowed_post_ids   = secondary_title_get_setting("post_ids");
      $allowed_post_types = secondary_title_get_setting("post_types");
      $allowed_categories = secondary_title_get_setting("categories");

      /** Check if post is not among allowed post types */
      /** @noinspection UnSafeIsSetOverArrayInspection */
      if(isset($post->post_type) && count($allowed_post_types) && !in_array($post->post_type, $allowed_post_types, false)) {
         return false;
      }

      if(!isset($_GET["post"])) {
         return true;
      }

      /** Don't do anything if the post is not a valid, well, post */
      if(!$post->ID) {
         return false;
      }

      if(!get_the_title($post->ID)) {
         return true;
      }

      /** Check if post is not among allowed post IDs */
      if(count($allowed_post_ids) && !in_array($post->ID, $allowed_post_ids, false)) {
         return false;
      }

      /** Check if post is not among allowed post categories */
      if(count($allowed_categories) && in_array($post->post_type, $category_taxonomy->object_type, false)) {
         $in_category = false;
         foreach((array)wp_get_post_categories($post->ID) as $category) {
            if(!$in_category && in_array($category, $allowed_categories, false)) {
               $in_category = true;
            }
         }

         if(!$in_category) {
            return false;
         }
      }

      /** Yup, we're good */
      return true;
   }

   /**
    * Turns the "show donation notification" setting back on
    * when the plugin is deactivated. Please don't kill me!
    *
    * @since 1.9.7
    *
    * @return bool
    */
   function secondary_title_reset_donation_notice() {
      return update_option("secondary_title_show_donation_notice", "on");
   }

   /**
    * @param $anchor
    */
   function secondary_title_print_html_info_circle($anchor) {
      $info_url = "https://thaikolja.gitbooks.io/secondary-title/quick-start/settings.html";
      ?>
      <a href="<?php echo $info_url . "#" . $anchor; ?>" target="_blank" title="<?php _e("Click here to learn more about this setting", "secondary-title"); ?>" class="info-circle right">
         <i class="fa fa-info-circle"></i>
      </a>
      <?php
   }