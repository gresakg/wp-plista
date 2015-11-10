<?php 

class GG_Plista_Widget extends WP_Widget 
{

    public function __construct() {
		parent::__construct(
			'plista_widget', // Base ID
			__( 'Plista Widget', 'text_domain' ), // Name
			array( 'description' => "Plista related posts widget", ) // Args
		);
	}

    public function widget($args, $instance) { 
    	?>
        <div data-widget="plista_widget_sidebar"></div>
        <?php
    }

}