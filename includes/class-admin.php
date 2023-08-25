<?php

namespace SecondaryTitle;

class Admin
{

    /**
     * Build the invisible secondary title input on edit pages
     * to let jQuery displaying it (see admin.js).
     *
     * @since 0.1.0
     */
    function init_secondary_title_admin_posts(): void {
        $title_input_position = secondary_title_get_setting(setting: 'input_field_position');

        /** Verify if Secondary Title's settings allow the input box to be displayed */
        if (!secondary_title_verify_admin_page()) {
            return;
        }

        $post_id = get_the_ID();
        $secondary_title = get_secondary_title($post_id);
        $input_title = esc_html(text: __(text: 'Enter your secondary title', domain: 'secondary-title'));
        ?>
        <input type="hidden" id="secondary-title-input-position" value="<?php echo $title_input_position; ?>"/>
        <input type="text" size="30" id="secondary-title-input" class="secondary-title-input"
               placeholder="<?php _e(text: 'Enter secondary title here', domain: 'secondary-title'); ?>"
               name="secondary_post_title" hidden value="<?php echo esc_html($secondary_title); ?>"
               title="<?php echo $input_title; ?>"/>
        <?php
    }


}

add_action(hook_name: 'admin_footer', callback: 'init_secondary_title_admin_posts');


/**
 * @param array $columns Default columns
 *
 * @return array Modified columns
 *
 * @since 1.9.3
 */
function secondary_title_register_overview_column(array $columns): array {
    /** Value of the position set on the Secondary Title admin page */
    $column_position_setting = secondary_title_get_setting('column_position');

    /** The first column used to add the offset */
    $first_key = 'cb';

    /** Row ID */
    $column_id = 'secondary_title';

    /** The row title */
    $column_title = __('Secondary Title', 'secondary-title');
    $column_title = [$column_id => $column_title];

    /** Set array keys with default columns */
    $keys = array_keys($columns);

    /** Search for the first key and modifying the position by changing the offset */
    $index = array_search($first_key, $keys, true);

    /** Default column position */
    $column_position = $index + 1;
    $column_offset = $column_position;

    /** Display the Secondary Title column on the right if set on the admin page */
    if ($column_position_setting !== 'left') {

        /** Increase offset and adding the column after the "Title" column */
        $column_offset = $column_position + 1;
    }

    /** Slice the first array */
    $column_1 = array_slice(
        $columns,
        0,
        $column_offset
    );

    /** Slice the second array */
    $column_2 = array_slice(
        $columns,
        $column_offset
    );

    /** Combine both arrays */
    $columns = array_merge(
        $column_1,
        $column_title,
        $column_2
    );

    return $columns;
}

/**
 * Initializes the overview columns.
 *
 * @since 1.4.3
 */
function secondary_title_load_overview_columns_hook(): void {
    $activated_post_types = secondary_title_get_setting('post_types');

    if (!$activated_post_types) {
        $activated_post_types = get_post_types();
    }

    $activated_post_types = apply_filters(
        'secondary_title_columns_in_post_types',
        $activated_post_types
    );

    foreach ($activated_post_types as $post_type) {
        add_action(
            "manage_{$post_type}s_custom_column",
            'secondary_title_display_overview_column',
            10,
            2
        );

        add_filter(
            "manage_{$post_type}_posts_columns",
            'secondary_title_register_overview_column'
        );
    }
}

add_action('admin_init', 'secondary_title_load_overview_columns_hook');

/**
 * Adds the secondary title to the newly created rows of the secondary title column
 *
 * @param string $column_name Name of the column we want to add the secondary title to
 * @param int $post_id ID of the post of the current row
 *
 * @since 1.4.3
 */
function secondary_title_display_overview_column(string $column_name, int $post_id): void {
    /** Loop through column names */
    switch ($column_name) {

        /** If current column name is the secondary title, then... */ case 'secondary_title':
        /** ...the actual secondary title of the post is being fetched */ $post_secondary_title = get_secondary_title($post_id);

        /** Display the secondary title of the current post within a row */
        echo $post_secondary_title;

        /** Stop the loop */
        break;
    }
}