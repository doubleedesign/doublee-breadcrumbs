# A WordPress Breadcrumbs plugin
Allows developers to easily add breadcrumb trails to theme templates in classic themes. (Not developed for or tested with block themes.)

## Installation

1. Upload `breadcrumbs-doublee` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place `<?php do_action('doublee_breadcrumbs'); ?>` in your templates where you want to show breadcrumbs.

## Options

* Global settings for how breadcrumb trails are built can be found in Settings > Breadcrumbs.
* You can override the title shown in the breadcrumb trail at the post level, in the Breadcrumb Settings metabox.
* Filters are provided so developers can modify which post types and taxonomies have breadcrumbs, and modify the HTML output. Details can be found in the settings screen. 
