<?php

namespace SecondaryTitle;

/**
 * Class SecondaryTitle
 * @package SecondaryTitle
 */
class SecondaryTitle
{
    /**
     * @var SecondaryTitle $instance
     */
    private static SecondaryTitle $instance;

    /**
     * @var string
     */
    public string $plugin_dir;

    /**
     * @var string
     */
    public string $plugin_url;

    /**
     * @var string
     */
    public string $plugin_version;



    /**
     * Showit constructor.
     *
     * @param                                       $plugin_file
     */
    public function __construct($plugin_file) {
        $this->plugin_dir = plugin_dir_path(file: $plugin_file);
        $this->plugin_url = plugin_dir_url(file: $plugin_file);

        // $this->theme       = $theme;
        // $this->file_system = $file_system;

    }

    /**
     * Get instance
     *
     * @param $plugin_file
     *
     * @return SecondaryTitle
     */
    final public static function instance($plugin_file): SecondaryTitle {
        //if (!static::$instance) {
            static::$instance = new static(plugin_file: $plugin_file);
       // }

        return static::$instance;
    }

    
    public function initialize(): void {

        var_dump(value: (new Config())->get_all_secondary_title_options());

        // $path = $this->get_current_path();

        // if (Config::is_using_static_pages()) {

        //     ( new Static_Page_Loader($path) )->initialize();
        // }
        // ( new Api() )->initialize();
        // ( new Api_Generator() )->initialize();
        // ( new Plugin() )->initialize();

        // if (!get_option(Config::OPTION_DEV)) {
        //     ( new Config() )::set_default_dev_options();
        // }

        // if (!get_option(Config::OPTION_API_LAST_CHECKED)) {
        //     ( new Config() )::set_init_check_time_option();
        // } else {
        //     ( new Api_Client() )->initialize();
        // }

        // if (defined('WP_CLI') && WP_CLI) {
        //     WP_CLI::add_command('showit', 'Showit\\Core\\Cli');
        // }

        // // Check if request is for an admin page.
        // if (is_admin()) {
        //     new Admin\Admin_Base($this->theme);
        // }

        // $access_manager = new Access_Manager();
        // /**
        //  * Add a check to see if we are limiting access to only Showit admin.
        //  */
        // if ($access_manager->get_user_access_status() === 2) {
        //     $access_manager->restrict_admin_access();
        // }
        // if ($access_manager->get_user_access_status() === 3) {
        //     $access_manager->disable_blog();
        // }

        // if (function_exists('add_theme_support')) {
        //     ( new Featured_Image_Manager() )->initialize();
        // }
    }

    public function get_secondary_title( $post_id = 0, $prefix = "", $suffix = "", $use_settings = false ): string {
        /** If $post_id not set, use current post ID */
        if ( ! $post_id ) {
            $post_id = (int) get_the_ID();
        }

        /** Get the secondary title and return false if it's empty actually empty */
        $secondary_title = get_post_meta( post_id: $post_id, key: "_secondary_title", single: true );

        if ( ! $secondary_title ) {
            return "";
        }

        /** Use filters set on Secondary Title settings page */
        if ( $use_settings && ! secondary_title_validate( post_id: $post_id ) ) {
            return "";
        }

        $secondary_title = $prefix . $secondary_title . $suffix;

        $secondary_title = apply_filters("get_secondary_title", $secondary_title, $post_id, $prefix, $suffix);

        return (string) $secondary_title;
    }

    public function the_secondary_title( $post_id = 0, $prefix = "", $suffix = "", $use_settings = false ): void {

        $secondary_title = $this->get_secondary_title(post_id: $post_id, prefix: $prefix, suffix: $suffix, use_settings: $use_settings);

        $secondary_title = apply_filters("the_secondary_title", $secondary_title, $post_id, $prefix, $suffix);

        echo $secondary_title;
    }

    /**
     * Returns whether the specified post has a
     * secondary title or not.
     *
     * @param int $post_id Post ID of the post in question.
     *
     * @return bool
     *
     * @since 0.5.1
     *
     */
    public function has_secondary_title( int $post_id = 0 ): bool {
        return (bool) get_secondary_title( $post_id );
    }

    public function get_public_post_types(): array {
        /** Returns all public registered post types */
        return get_post_types(args: ["public" => true]);
    }

    /**
     * Returns all posts that have a valid
     * secondary title.
     *
     * @param array $additional_query
     *
     * @return array
     *
     * @since    0.9.2
     *
     * @internal param int $count
     *
     */
    function get_posts_with_secondary_title( array $additional_query = [] ): array {
        $query_arguments = [
            "post_type"    => "any",
            "meta_key"     => "_secondary_title",
            "meta_value"   => " ",
            "meta_compare" => "!=",
            "post_status"  => "publish"
        ];

        $query_arguments = wp_parse_args( $query_arguments, $additional_query );

        return get_posts( $query_arguments );
    }

    /**
     * Returns a random post that has a valid
     * secondary title.
     *
     * @return bool|WP_Post
     *
     * @since 0.9.2
     *
     */
    function get_random_post_with_secondary_title() {
        $post = get_posts_with_secondary_title(
            [
                "showposts" => 1,
                "orderby"   => "rand"
            ]
        );

        if ( ! $post ) {
            return false;
        }

        return $post[0];
    }

    /**
     * @param array $new_settings
     *
     * @return bool
     *
     * @since 1.4.0
     *
     */
    function secondary_title_update_settings( array $new_settings = [] ): bool {
        $saved  = false;
        $arrays = [
            "post_types",
            "categories"
        ];

        foreach ( secondary_title_get_default_settings() as $full_setting_name => $default_value ) {
            $setting_name = str_replace( "secondary_title_", "", $full_setting_name );
            $value        = "";

            if ( $setting_name === "show_donation_notice" ) {
                continue;
            }

            if ( isset( $new_settings[$setting_name] ) ) {
                $value = $new_settings[$setting_name];

                if ( $setting_name === "post_ids" ) {
                    $value = preg_replace( "'[^0-9,]'", "", $value );

                    if ( ! is_array( $value ) ) {
                        $value = explode( ",", $value );
                    }
                }

                if ( $setting_name === "post_ids" && ( ! $new_settings[$setting_name] || $value[0] === "" ) ) {
                    $value = [];
                }
            } elseif ( in_array( $setting_name, $arrays, true ) ) {
                $value = [];
            }
            if ( update_option( $full_setting_name, $value ) ) {
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
     * @param int $post_id
     *
     * @return bool
     *
     * @since 1.4.0
     *
     */
    function secondary_title_validate( int $post_id ): bool {
        $allowed_post_types = get_secondary_title_post_types();
        $allowed_categories = get_secondary_title_post_categories();
        $allowed_post_ids   = get_secondary_title_post_ids();
        $post_categories    = wp_get_post_categories( $post_id );

        /** Check if post type is among the allowed post types */
        if ( count( $allowed_post_types ) && ! in_array( get_post_type( $post_id ), $allowed_post_types, false ) ) {
            return false;
        }

        /** Check if post's categories are among the allowed categories */
        $in_categories = false;
        foreach ( $post_categories as $category_id ) {
            if ( in_array( $category_id, $allowed_categories, false ) ) {
                $in_categories = true;
            }
        }
        if ( ! $in_categories && count( $allowed_categories ) ) {
            return false;
        }

        return ! in_array( $post_id, $allowed_post_ids, false );
    }

    /**
     * Verifies whether plugin settings allow secondary title
     * input box to be displayed.
     *
     * @return bool
     *
     * @since 1.7.0
     *
     */
    function secondary_title_verify_admin_page(): bool {
        global $post;

        $category_taxonomy  = get_taxonomy( "category" );
        $allowed_post_ids   = secondary_title_get_setting( "post_ids" );
        $allowed_post_types = secondary_title_get_setting( "post_types" );
        $allowed_categories = secondary_title_get_setting( "categories" );

        /** Check if post is not among allowed post types */
        if ( isset( $post->post_type ) && count( $allowed_post_types ) && ! in_array( $post->post_type, $allowed_post_types, false ) ) {
            return false;
        }

        if ( ! isset( $_GET["post"] ) ) {
            return true;
        }

        /** Don't do anything if the post is not a valid, well, post */
        if ( ! $post->ID ) {
            return false;
        }

        if ( ! isset( $post->ID ) || ! get_the_title( $post->ID ) ) {
            return true;
        }

        /** Check if post is not among allowed post IDs */
        if ( count( $allowed_post_ids ) && ! in_array( $post->ID, $allowed_post_ids, false ) ) {
            return false;
        }

        /** Check if post is not among allowed post categories */
        if ( count( $allowed_categories ) && in_array( $post->post_type, $category_taxonomy->object_type, false ) ) {
            $in_category = false;
            foreach ( (array) wp_get_post_categories( $post->ID ) as $category ) {
                if ( ! $in_category && in_array( $category, $allowed_categories, false ) ) {
                    $in_category = true;
                }
            }

            if ( ! $in_category ) {
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
     * @return bool
     * @since 1.9.7
     *
     */
    function secondary_title_reset_donation_notice(): bool {
        return update_option( "secondary_title_show_donation_notice", value: true );
    }

    /**
     * Displays a Font Awesome info icon with a link
     * pointing to the relevant section in Secondary Title's
     * documentation on gitbooks.io.
     *
     * @param string $anchor
     *
     * @since 2.0.0
     */
    function secondary_title_print_html_info_circle( string $anchor ): void {
        $info_url = "https://thaikolja.gitbooks.io/secondary-title/quick-start/settings.html";
        ?>
        <a href="<?php echo $info_url . "#" . $anchor; ?>" target="_blank" title="<?php _e( "Click here to learn more about this setting", "secondary-title" ); ?>" class="info-circle right">
            <i class="fa fa-info-circle"></i>
        </a>
        <?php
    }

}
