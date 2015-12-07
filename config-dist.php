<?php

$plista_config = array(
	/**
	 * Id for the plista API
	 */
	'domainid' => "your domain id"
	/**
	 * pub_key this is required. Get it from plista.com
	 */
	'pub_key' => "your_pub_key",
	/**
	 * Private key for communication with plista api
	 */
	'api_key' => "your private key",
	/**
	 * Append to content via the_content hook
	 */
	'the_content' => true, 
	/**
	 * Print out via action hook (i.e. wp_footer, or some custom theme hook)
	 */
	'custom_hook' => 'gg_plista_bellow_article',
	/**
	 * If you have more widgets on one hook, you can set the priority here.
	 */
	'priority' => 10, 
	/**
	 * If you have any generic categories you do not whish to add to the item 
	 * category (usually "uncategorized" or "undefined" or "default" ...)
	 */
	'stop_categories' => array(), 
	/**
	 * If featured image is missing, what image do you want to use ba default.
	 */
	'default_image_url' => "", 
	);