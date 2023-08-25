<?php

namespace SecondaryTitle;

use Exception;

/**
 * Class Config
 * @package SecondaryTitle
 */
class Config
{
    /**
     * WordPress option name for determining if Secondary Tile will automatically display
     */
    public const OPTION_AUTO_SHOW = 'secondary_title_auto_show';

    /**
     * WordPress option name for Secondary Title format
     */
    public const OPTION_TITLE_FORMAT = 'secondary_title_title_format';

    /**
     * WordPress option name for an array of post types allowed for Secondary Title
     */
    public const OPTION_POST_TYPES = 'secondary_title_post_types';

    /**
     * WordPress option name for an array of categories allowed for Secondary Title
     */
    public const OPTION_CATEGORIES = 'secondary_title_categories';

    /**
     * WordPress option name for an array of post IDs allowed for Secondary Title
     */
    public const OPTION_POST_IDS = 'secondary_title_post_ids';

    /**
     * WordPress option name for an input field position with the Classic Editor
     */
    public const OPTION_INPUT_FIELD_POSITION = 'secondary_title_input_field_position';

    /**
     * WordPress option name for displaying Secondary Title on Single Post/Page only
     */
    public const OPTION_SHOW_ONLY_IN_SINGLE = 'secondary_title_only_show_in_main_post';

    /**
     * WordPress option name for determining if Secondary Title should be added to permalinks
     */
    public const OPTION_USE_IN_PERMALINKS = 'secondary_title_use_in_permalinks';

    /**
     * WordPress option name for determining where Secondary Title should be positioned in permalinks
     */
    public const OPTION_PERMALINK_POSITION = 'secondary_title_permalinks_position';


    /**
     * WordPress option name for Secondary Title column position in post editor
     */
    public const OPTION_COLUMN_POSITION = 'secondary_title_column_position';


    /**
     * WordPress option name for determining if Secondary Title should exist in RSS feed
     */
    public const OPTION_RSS_FEED_SHOW = 'secondary_title_feed_auto_show';

    /**
     * WordPress option name for determining Secondary Title format in RSS feed
     */
    public const OPTION_RSS_TITLE_FORMAT = 'secondary_title_feed_title_format';

    /**
     * WordPress option name for determining if Secondary Title should be included in search results
     */
    public const OPTION_INCLUDE_IN_SEARCH = 'secondary_title_include_in_search';

    /**
     * WordPress option name determining if donation notice should display
     */
    public const OPTION_SHOW_DONATION_NOTICE = 'secondary_title_show_donation_notice';

    /**
     * Array of all plugin options
     */
    private const DEFAULT_SECONDARY_TITLE_OPTIONS = [
        self::OPTION_AUTO_SHOW              => true,
        self::OPTION_TITLE_FORMAT           => '%secondary_title%: %title%',
        self::OPTION_POST_TYPES             => [],
        self::OPTION_POST_IDS               => [],
        self::OPTION_CATEGORIES             => [],
        self::OPTION_COLUMN_POSITION        => 'above',
        self::OPTION_PERMALINK_POSITION     => 'prepend',
        self::OPTION_INPUT_FIELD_POSITION   => 'above',
        self::OPTION_RSS_FEED_SHOW          => false,
        self::OPTION_RSS_TITLE_FORMAT       => '%title%',
        self::OPTION_SHOW_ONLY_IN_SINGLE    => false,
        self::OPTION_INCLUDE_IN_SEARCH      => true,
        self::OPTION_USE_IN_PERMALINKS      => false,
        self::OPTION_SHOW_DONATION_NOTICE   => true
    ];

    /**
     * Returns all settings and their default values used by Secondary Title.
     *
     * @return array
     *
     * @since 3.0.0
     */
    public function secondary_title_get_default_options(): array {

        /** Define the default settings and their values */
        $default_settings = self::DEFAULT_SECONDARY_TITLE_OPTIONS;

        $default_settings = apply_filters("secondary_title_get_default_settings", value: $default_settings);

        return (array) $default_settings;
    }

    /**
     * Sets the default settings when plugin is activated (and no settings exist).
     *
     * @throws Exception
     * @since 3.0.0
     */
    public static function secondary_title_activate(): void {

        /** Use update_option() to create the default options  */
        foreach (self::DEFAULT_SECONDARY_TITLE_OPTIONS as $option => $value) {
            if (get_option($option)) {
                continue;
            }
            if (!update_option($option, value: $value)) {
                throw new Exception();
            }
        }
    }

    /**
     * Resets all settings back to the default values.
     *
     * @return bool
     *
     * @since 3.0.0
     *
     */
    public static function secondary_title_reset_settings(): bool {

        $default_settings = self::DEFAULT_SECONDARY_TITLE_OPTIONS;

        foreach ( $default_settings as $option => $default_value ) {
            update_option( $option, value: $default_value );
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function secondary_title_get_option(string $option ) {

        if(array_key_exists(key: $option, array: self::DEFAULT_SECONDARY_TITLE_OPTIONS )){
            return get_option($option);
        }
        else{
            throw new Exception(message: 'That option does not exist in secondary title');
        }
    }


    /**
     * Returns all settings generated by Secondary Title and their current values
     *
     * @return array
     *
     * @since 0.1.0
     *
     */
    public function get_all_secondary_title_options(): array {
        $options = [];

        foreach (self::DEFAULT_SECONDARY_TITLE_OPTIONS as $option => $value) {
            $options[$option] = get_option($option);
        }

        return $options;
    }



}