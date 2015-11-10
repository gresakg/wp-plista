<?php
/*
Plugin Name: Plista by GG
Plugin URI: http://gresak.net
Description: Basic plista integration
Author: Gregor Grešak
Version: 0.2
Author URI: http://gresak.net
*/

include_once "config.php";

if(empty($plista_config)) {
	$plista_config['no_config'] = true;
}

$overnet_plista = new GG_Plista($plista_config);

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
	protected $default_image_url;
	protected $priority = 10;

	/**
	 * Load the configuration
	 * @param array $config [description]
	 */
	public function __construct(array $config) {
		
		if(isset($config['no_config'])){
			add_action('admin_notices',array($this,'no_configuration_warning'));
		}

		if(empty($config['pub_key'])) {
			add_action('admin_notices',array($this,'no_pub_key_warning'));
		}

		$this->key = $config['pub_key'];

		if(isset($config['stop_categories']));
			$this->stop_categories = $config['stop_categories'];

		if(isset($config['default_image_url']))
			$this->default_image_url = $config['defult_image_url'];	

		if(isset($config['priority']))
			$this->priority = $config['priority'];

		$this->setup_hooks();
	}

	/**
	 * Set up the hooks
	 * @return [type] [description]
	 */
	protected function setup_hooks()
	{
		add_action('wp',array($this,'init'));
		add_filter('the_content',array($this,'bellow_article'),$this->priority);
		add_action('wp_footer',array($this,'print_js'));
	}

	/**
	 * Initialize the plista variables
	 * @return [type] [description]
	 */
	public function init() {
		$this->post_id = get_the_ID();
		$this->title = get_the_title();
		$this->excerpt = get_the_excerpt();
		$this->permalink = get_the_permalink();
		$this->image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' )[0];
		if(empty($this->image))
			$this->image = $this->default_image_url; 
		$this->category = $this->get_single_category($id); //get_the_category()[0]->name;
		$this->timestamp = get_the_time('U');
	}

	/**
	 * Print out the placeholder. This function is meant to be hooked to 
	 * the_content filter hook
	 * @param  string $content the post content
	 * @return string          altered post content
	 */
	public function bellow_article($content) {
		return $content . '<div data-widget="plista_widget_belowArticle"></div>';
	}

	/**
	 * Markup for the widgets
	 * @return string [description]
	 */
	public function sidebar_widget() {
		echo '<div data-widget="plista_widget_sidebar"></div>';
	}

	/**
	 * Pring the js for the plista api
	 * @return string
	 */
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

	/**
	 * If no plista key is provided, print a warning in the administration
	 * @return [type] [description]
	 */
	public function no_pub_key_warning(){
		echo "<div class='error'>
			<p>
				Plista public key missing. Please set it up in the config.php file in your plugins/wp-plista directory.
			</p>
		</div>";
	}

	/**
	 * If configuration file is missing, print a warning in the administration.
	 * @return [type] [description]
	 */
	public function no_configuration_warning() {
		echo "<div class='error'>
			<p>
				Configuration missing! Pleas go to your wp-content/plugins/wp-plista directory, copy the file config-dist.php
				to config.php and set the appropriate configurations.
			</p>
		</div>";
	}

	/**
	 * Helper function to extract one single categroy for plista
	 * @param  int $id post id
	 * @return string     Category name
	 */
	protected function get_single_category($id) {

	 	$categories = explode("::",strip_tags(get_the_category_list("::","single",$id)));
	 	foreach($categories as $category) {
	 		if(!in_array($category, $this->stop_categories)) {
	 			return $category;
	 		}
	 	}
 }

}