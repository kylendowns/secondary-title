# Filters

WordPress' comprehensive [Plugin API](https://codex.wordpress.org/Plugin_API) comes with a set of filters that allow you to programmatically overwrite a section of a code fragment without altering the function itself. Just like action, filters usually go into a file of your theme, often `functions.php`. To learn more about WordPress filters, [click here](https://codex.wordpress.org/Plugin_API/Filter_Reference).

Secondary Title makes use of the Plugin API by adding custom filters. Here is the complete list:

## Available Filters

* _string_ **`get_secondary_title`** Applies to all displays of the secondary post title
    * _string_ `$secondary_title` Post's secondary title (if exists)
    * _int_ `$post_id` Current post ID
    * _string_ `$default_title` Unprocessed post title (same as `$post->post_title`)

## Examples

These are a few examples that demonstrate how to use Secondary Title's filters correctly. Don't forget that you **must** remove the `<?php` and `?>` PHP opening/closing tags if you paste the code below into your `functions.php`.

### 1. Deactivate Secondary Title on archive pages

To prevent Secondary Title from being displayed on archive pages, we can use the `get_secondary_title` filter:

```php
<?php
    add_filters('get_secondary_title', function($secondary_title, $post_id, $default_title) {
        $post = get_post($post_id);
        
        if(is_archive()) {
            return $post->title;
        }        
    });
```

**Last updated on:** {docsify-updated}