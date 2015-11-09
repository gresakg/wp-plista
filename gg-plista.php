<?php
/*
Plugin Name: Plista by GG
Plugin URI: http://gresak.net
Description: Basic plista integration
Author: Gregor Grešak
Version: 0.1
Author URI: http://gresak.net
*/

include_once "config.php";

$overnet_plista = new GG_Plista($plista_config);

/**
 * Hooks
 * */
add_action('wp',array($overnet_plista,'init'));
add_filter('the_content',array($overnet_plista,'bellow_article'));
add_action('wp_footer',array($overnet_plista,'print_js'));

class GG_Plista {

	public 	  $placement;
	protected $key;
	protected $post_id;
	protected $title;
	protected $excerpt;
	protected $permalink;
	protected $image;
	protected $category;
	protected $timestamp;
	protected $stop_categories;

	public function __construct(array $config) {
		$this->key = $config['pub_key'];
		$this->stop_categories = $config['stop_categories'];
	}


	public function init() {
		$this->post_id = get_the_ID();
		$this->title = get_the_title();
		$this->excerpt = get_the_excerpt();
		$this->permalink = get_the_permalink();
		$this->image = wp_get_attachment_image_src( get_post_thumbnail_id($id), 'large' )[0];
		$this->category = $this->get_single_category($id); //get_the_category()[0]->name;
		$this->timestamp = get_the_time('U');
	}

	public function bellow_article($content) {
		return $content . '<div data-widget="plista_widget_belowArticle"></div>';
	}

	public function sidebar_widget() {
		echo '<div data-widget="plista_widget_sidebar"></div>';
	}

	public function print_js() {
		if(is_single()):
		?>
		<!-- Plista begin -->
		<script type="text/javascript">
		 if (!window.PLISTA || !PLISTA.publickey) {
		     window.PLISTA = {
		        publickey: '<?php echo $this->key; ?>',
		        item: {
		            objectid: "<?php echo $this->post_id; ?>", //unique ID, alphanumeric
		            title: "<?php echo $this->title; ?>", //max 255 characters
		            text: "<?php echo $this->excerpt; ?>", //max 255 characters
		            url: "<?php echo $this->permalink; ?>", //max 1024 characters
		            img: "<?php echo $this->image; ?>", //max 255 characters
		            category: '<?php echo $this->category; ?>',
		            created_at: <?php echo $this->timestamp; ?> //UNIX timestamp
		        }
		    };
		    (function(){var n='script',d=document,s=d.createElement(n),s0=d.getElementsByTagName(n)[0];s.async='async';s.type='text/javascript';s.src=(d.location.protocol==='https:'?'https:':'http:')+'//static.plista.com/async.js';s0.parentNode.insertBefore(s,s0)}());
		}
		//if possible place this script tag just before the closing body tag
		</script>
		<!-- Plista end -->
		<?php
		endif;
	}

	protected function get_single_category($id) {

 	$categories = explode("::",strip_tags(get_the_category_list("::","single",$id)));
 	foreach($categories as $category) {
 		if(!in_array($category, $this->stop_categories)) {
 			return $category;
 		}
 	}
 }

}