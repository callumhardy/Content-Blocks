<?php

/*
 * The Content Blocks class.
 */

  	class Content_Blocks_Backend {

/*  ============================================================


	Contents:


	============================================================
    -> Variables
    ============================================================ */
	 /**
	  * The default Content Blocks ACF name
	  */   
  		public $acf_name = 'content_blocks';

	 /**
	  * The path to the current directory
	  */
  		public $path_to_dir;

	 /**
	  * The CLASS instance
	  */
		private static $_instance = null;

	 /**
	  * An array of Fields and Instructions that will overwrite existing field instructions in the Content Blocks
	  */
		public $content_blocks_instructions = array();

/*  ============================================================
    -> Methods
    ============================================================ */

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

			//	Add Options Pages
			if( function_exists('acf_add_options_sub_page') )
			{
				//acf_add_options_sub_page( 'PAGE_NAME' );
			}

			//	Import any ACF fields needed
			$this->import_acf_php_fields();

		}

	 /**
	  * enqueue_admin_scripts()
	  * 
	  * Enqueue admin scripts and style sheets.
	  *
	  * @return void
	  */

		public function enqueue_admin_scripts() {

	  		//	Content blocks - Google Maps
			wp_register_script( 'content-blocks-js', $this->path_to_dir . '/assets/js/content-blocks.js', array( 'jquery' ) );
			wp_enqueue_script( 'content-blocks-js' );

			//	Content blocks - Core
			wp_register_style( 'content-blocks-css', $this->path_to_dir . '/assets/css/content-blocks.css', '', '', 'screen' );
	        wp_enqueue_style( 'content-blocks-css' );

		}

	 /**
	  * get_acf_field_group()
	  *
	  * Checks for the existence of an ACF field Group in the database
	  *
	  * @param 	String $field_name The name of the ACF Field Group
	  * 
	  * @return Null/Object Returns the Field Group Object or NULL
	  */

		public function get_acf_field_group( $field_name = null  ) {

	  		global $wpdb;

	  		$table = $wpdb->prefix . "posts";

	  		$row = $wpdb->get_row("SELECT * FROM $table  WHERE post_title = '$field_name'");

	  		return $row;
		}

	 /**
	  * import_acf_php_fields()
	  *
	  * Imports some basic ACFs to construct the Content Blocks Toggle button in the back end
	  * 
	  * @return void
	  */

		public function import_acf_php_fields() {

	  		if(!$this->get_acf_field_group('Page Options'))
	  		{
	  			if(function_exists("register_field_group"))
	  			{
	  				register_field_group(array (
	  					'id' => 'acf_page-options',
	  					'title' => 'Page Options',
	  					'fields' => array (
	  						array (
	  							'key' => 'field_5342d205bbfee',
	  							'label' => 'Options: Content Blocks',
	  							'name' => 'toggle_content_blocks',
	  							'type' => 'checkbox',
	  							'choices' => array (
	  								'open_blocks' => 'Open all blocks',
	  								'close_blocks' => 'Close all blocks'
	  							),
	  							'default_value' => '',
	  							'layout' => 'vertical',
	  						),
	  					),
	  					'location' => array (
	  						array (
	  							array (
	  								'param' => 'post_type',
	  								'operator' => '==',
	  								'value' => 'page',
	  								'order_no' => 0,
	  								'group_no' => 0,
	  							),
	  						),
	  						array (
	  							array (
	  								'param' => 'post_type',
	  								'operator' => '==',
	  								'value' => 'post',
	  								'order_no' => 0,
	  								'group_no' => 1,
	  							),
	  						),
	  					),
	  					'options' => array (
	  						'position' => 'side',
	  						'layout' => 'no_box',
	  						'hide_on_screen' => array (
	  						),
	  					),
	  					'menu_order' => 0,
	  				));
	  			}
	  		}
		}

  	}

