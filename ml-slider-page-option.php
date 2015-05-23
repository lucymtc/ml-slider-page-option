<?php
/**
Plugin Name: Metaslider as page option
Description: Adds a dropdown to your pages to select
Version: 	 1.0
Author: 	 Lucy Tomás
Author URI:  https://wordpress.org/support/profile/lucymtc
License: 	 GPLv2
*/
 
 

 // If this file is called directly, exit.
if ( !defined( 'ABSPATH' ) ) exit;

if( !class_exists('MTSPageOption') ) {
	
	/**
	 * Main class
	 * @since   1.0
	 */
	
final class MTSPageOption {

		private static $instance = null;
		
		public $version = 1.0;
		
		/**
		 * Instance
		 * This functions returns the only one true instance of the plugin main class
		 * 
		 * @return object instance
		 * @since  1.0
		 */
		
		public static function instance (){
			
			if( self::$instance == null ){
					
				self::$instance = new MTSPageOption;
				self::$instance->constants();
				self::$instance->load_textdomain();
				
			}
			
			return self::$instance;
		}
		
		/**
		 * Class Contructor
		 * 
		 * @since 1.0
		 */

		 public function __construct () {
		 	
		 		// *** Actions 
		 		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		 		add_action( 'save_post', 	  array( $this, 'save_metadata' ) );
		 }

		/**
		 * Add Metabox
		 * 
		 * @since 1.0
		 */

		 public function add_metabox(){

		 	add_meta_box(
			        'metaslider_select', 
			        __( 'Select Metaslider', 'mtspageoption' ), 
			       array( $this, 'metabox_form'), 
			        'page', 
			        'side', 
			        'high'
    		);

		 }

		/**
		 * Metabox form
		 * 
		 * @since 1.0
		 */

		 public function metabox_form(){

		 	global $wpdb, $post;

			wp_nonce_field( 'metaslider_select', 'metaslider_select_none' );

			// get sliders list
			$sliders = $wpdb->get_results( $wpdb->prepare( 
							"
								SELECT ID, post_title 
									FROM {$wpdb->posts} 
								WHERE post_type = %s 
								AND post_status = %s
							",  
								'ml-slider',
								'publish'
						) );


			$current_slider = get_post_meta( $post->ID, '_metaslider_id', true );

			echo '<label for="metaslider">';
				_e( 'Slider', 'mtspageoption' );
			echo '</label> ';
			echo '<select id="metaslider" name="metaslider_id">';
			echo '<option value="0"></option>';
		
			foreach ( $sliders as $slider ) {

				echo '<option value="' . absint( $slider->ID )  . '" '; 
				echo  selected( absint( $slider->ID ), $current_slider, false ) ;
				echo '>' . esc_attr( $slider->post_title ) . '</option>';  
						
			}

			echo '</select>';

		 }

		/**
		 * Save metadata
		 * 
		 * @since 1.0
		 */

		 public function save_metadata( $post_id ){

			 	/* **** Security checks */	

				// Check nonce
				if ( ! isset( $_POST['metaslider_select_none'] ) ) {
					return;
				}

				// Verify nonce
				if ( ! wp_verify_nonce( $_POST['metaslider_select_none'], 'metaslider_select' ) ) {
					return;
				}

				// Return if auto save
				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
					return;
				}

				// Check permissions
				if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

					if ( ! current_user_can( 'edit_page', $post_id ) ) {
						return;
					}

				}

				/* **** Save data */	
				
				if ( ! isset( $_POST['metaslider_id'] ) ) {
					return;
				}

				$data = sanitize_text_field( $_POST['metaslider_id'] );
				
				if( $data > 0 ) {
					update_post_meta( $post_id, '_metaslider_id', $data );
				}
		 }

		/**
		 * get_slider
		 * @since 1.0
		 */

		public static function get_slider() {

			global $post;

			$slider_id = get_post_meta( $post->ID, '_metaslider_id', true );

			if( $slider_id > 0) {
			
				echo do_shortcode("[metaslider id={$slider_id}]"); 
			
			} 
		}


		/**
		 * constants
		 * @since 1.0
		 */
		  
		 private function constants() {
		  	
		  	if( !defined('MTSPageOption_PLUGIN_DIR') )  	{ define('MTSPageOption_PLUGIN_DIR', plugin_dir_path( __FILE__ )); }
			if( !defined('MTSPageOption_PLUGIN_URL') )  	{ define('MTSPageOption_PLUGIN_URL', plugin_dir_url( __FILE__ ));  }
			if( !defined('MTSPageOption_PLUGIN_FILE') ) 	{ define('MTSPageOption_PLUGIN_FILE',  __FILE__ );  }
			if( !defined('MTSPageOption_PLUGIN_VERSION') )	{ define('MTSPageOption_PLUGIN_VERSION', $this->version);  } 
			
		 }


	   /**
		* load_textdomain
		* @since 1.0
		*/

		public function load_textdomain() {
			
			load_plugin_textdomain('mtspageoption', false,  dirname( plugin_basename( MTSPageOption_PLUGIN_FILE ) ) . '/languages/' );	
	 	}


		  
	
 	
}// class
	
	
}// if !class_exists


MTSPageOption::instance();