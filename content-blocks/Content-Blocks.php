<?php
/*
Plugin Name: Content Blocks
Plugin URI:
Description:
Version: 1.0
Author: Callum Hardy
Author URI: http://www.callumhardy.com.au
License: GPL
*/

include_once('includes/Builder.php');
include_once('includes/Content-Blocks-Backend.php');
include_once('includes/Content-Blocks-Frontend.php');

/*
 * The Content Blocks class.
 */

  	class Content_Blocks extends Content_Blocks_Frontend {

	 /**
	  * The path to the current directory
	  */
  		public $path_to_dir;

	 /**
	  * The CLASS instance
	  */
		private static $_instance = null;

	 /**
	  * -> create()
	  * 
	  * Creates an instance of the CLASS using a static method
	  *
	  * @return string
	  */

		public static function create( $config_args = array() ) {
			
			$obj = new static( $config_args );

			return $obj;

		}

	 /**
	  * -> init()
	  *
	  * Initialise the setup for the Content Blocks
	  * 
	  * @return string
	  */

		public function init() {

			//	Get the Current Path to this Directory
	  		$this->path_to_dir = substr( __DIR__, strpos( __DIR__, '/wp-content' ));

	  		if( is_admin() ) {

	  			//Content_Blocks_Backend::create()->init();

	  		} else {

	  			//Content_Blocks_Frontend::create()->init();

	  		}

	  		//	Enqueue scripts and Styles
	  		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );

	  		//	Enqueue ACF admin scripts and Styles
	  		add_action('acf/input/admin_head', array( &$this, 'enqueue_admin_scripts' ));

	  		//	Actions / Hooks
	  		do_action('content_blocks/init');

		}

  	}

  	Content_Blocks::create()->init();
