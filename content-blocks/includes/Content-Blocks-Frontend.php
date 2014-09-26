<?php

/*
 * The Content Blocks class.
 */

  	class Content_Blocks_Frontend extends Content_Blocks_Backend {

/*  ============================================================


	Contents:

    -> Variables
    
    -> Methods
    -->> Get Blocks
    -->> Wrap with Column
    -->> Wrap with Row
    -->> Wrap with Container
    -->> Return GET vars string
    -->> Get Page URL

    -->> Blocks
    --->>> Text
    --->>> Post Feed
    --->>> Gallery
    --->>> Google Map


	============================================================
    -> Variables
    ============================================================ */
     /**
	  * The current Content Blocks HTML array
	  */ 
  		public $content_blocks_HTML = array();

     /**
	  * Block Variables
	  */ 
  		public $block_count = 1;
  		public $blocks_total;
  		public $end_strip = true;

     /**
	  * Columns Variables
	  */ 
  		public $container_args = array();

     /**
	  * Columns Variables
	  */ 
  		public $row_args = array();

     /**
	  * Columns Variables
	  */
		public $column_args = array();
  		public $columns_total = 0;
  		public $columns_modulus = 0;

	 /**
	  * The Default args
	  */
  		public $default_args = array(
			'acf_name' 			=> 'content_blocks',
			'container' 		=> true,
			'container_tag'		=> 'div',
			'container_classes'	=> null,
			'container_styles' 	=> null,
			'row' 				=> true,
			'row_tag'			=> 'div',
			'row_classes'		=> null,
			'row_styles' 		=> null,
			'column' 			=> true,
			'column_tag'		=> 'div',
			'column_classes'	=> null,
			'column_styles' 	=> null,
			'auto_clearing'		=> true,
			'page_id'			=> null,
			'echo' 				=> false
		);

	 /**
	  * For storing the merger of default and config args
	  */
		public $args;

	 /**
	  * Columns that determine their width by the grids maximum width
	  */
  		public $constant_columns = array(
  			1 => 'one',
  			2 => 'two',
  			3 => 'three',
  			4 => 'four',
  			5 => 'five',
  			6 => 'six',
  			7 => 'seven',
  			8 => 'eight',
  			9 => 'nine',
  			10 => 'ten',
  			11 => 'eleven',
  			12 => 'twelve',
  			13 => 'thirteen',
  			14 => 'fourteen',
  			15 => 'fifteen',
  			16 => 'sixteen',
  			17 => 'seventeen',
  			18 => 'eighteen',
  			19 => 'nineteen',
  			20 => 'twenty',
  			21 => 'twenty-one',
  			22 => 'twenty-two',
  			23 => 'twenty-three',
  			24 => 'twenty-four'
  		);

	 /**
	  * Columns that determine their width by the width of their parent HTML element
	  */
  		public $relative_columns = array(
  			3 => 'quarter',
  			4 => 'third',
  			6 => 'half',
  			8 => 'two-thirds',
  			9 => 'three-quarters',
  			12 => 'full'
  		);

/*  ============================================================
    -> Methods
    ============================================================ */

	 /**
	  * Sets up the initial state of the CLASS
	  *
	  * @return void
	  */

		public function __construct( $config_args = array() ) {

			//	Overwrite/Merge $default_args with the $config_args, if any are present
			$this->args = array_merge( $this->default_args, $config_args );

			//	Make sure we have the id of a page to work with
			if( $this->args['page_id'] == null )
				$this->args['page_id'] = get_the_ID();

			$this->acf_name = $this->args['acf_name'];
			
		}

	 /**
	  * -> enqueue_scripts()
	  * 
	  * Enqueue scripts and style sheets.
	  *
	  * @return void
	  */

		public function enqueue_scripts() {

	  		//	Google Maps
	  		wp_register_script( '_google-maps-js', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false', array( 'jquery' ) );
	  		wp_enqueue_script( '_google-maps-js' );

	  		//	Content blocks - Google Maps
			wp_register_script( 'content-blocks-google-maps-js', $this->path_to_dir . '/assets/js/google-maps.js', array( 'jquery' ) );
			wp_enqueue_script( 'content-blocks-google-maps-js' );

			//	Content blocks - Core
			wp_register_style( 'content-blocks-google-maps-css', $this->path_to_dir . '/assets/css/google-maps.css', '', '', 'screen' );
	        wp_enqueue_style( 'content-blocks-google-maps-css' );

		}

/*  ============================================================
    -->> Get Blocks
    ============================================================ */
	 /**
	  * Construct and print the Content Blocks HTML to the screen
	  *
	  * @return void
	  */

		public function get_blocks() {

			//	Block Variables
			$args = $this->args;

			//	Are there any Content Blocks?
			if( have_rows( $args['acf_name'], $args['page_id'] ) ):
			 
			    // loop through the Content Blocks
			    while ( have_rows( $args['acf_name'], $args['page_id'] ) ) : the_row();

					//	Create/Reset the blocks HTML variable
					$block_HTML = '';

			    	//	Block ACF fields
			    	$block_heading = get_sub_field('block_heading');
			    	$block_settings = get_sub_field('block_settings');
			    	$block_background_image = get_sub_field('block_background_image');
			    	$block_background_colour = get_sub_field('block_background_colour');

			    	//	Are there any Columns
			    	if( have_rows( 'columns', $args['page_id'] ) ):

			    		// loop through the Columns
			    		while ( have_rows( 'columns', $args['page_id'] ) ) : the_row();

					 		//	Create the name of the method that would run for this row layout
					        $row_layout_method = get_row_layout().'_block';

					        //	Check that a method exists in this CLASS with the same name as the row layout method variable
					        if ( method_exists( 'Content_Blocks', $row_layout_method ) )
					        {
					        	//	Create/Reset the Default Arguments for the Column element
					        	$this->column_args = array(
					        		'tag' => $args['column_tag'],
					        		'id' => null,
					        		'classes' => array('columns'),
					        		'styles' => $args['column_styles']
					        	);

					        	//	Run the method using a variable function name
					        	$block_HTML .= $this->$row_layout_method();

					        	//	Increment Block Count
					        	$this->block_count++;
					        }

			    		endwhile;

			    	else:

			    		//	No columns
						return false;

			    	endif;

				    //	Does this block need to be wrapped in a row?
				    if( $args['row'] ) {

				    	//	Create/Reset the Default Arguments for the Row element
				    	$this->row_args = array(
				    		'tag' => $args['row_tag'],
				    		'id' => null,
				    		'classes' => array('row'),
				    		'styles' => $args['row_styles']
				    	);

	    	    		//	Build the row
	    				$block_HTML = $this->wrap_with_row( $block_HTML );

				    }

				    //	Does this block need to be wrapped in a container?
				    if( $args['container'] ) {
	    				
				    	//	Create/Reset the Default Arguments for the Container element
				    	$this->container_args = array(
				    		'tag' => $args['container_tag'],
				    		'id' => null,
				    		'classes' => array('container', 'content-block'),
				    		'styles' => $args['container_styles']
				    	);

	    	    		//	Build the container
	    				$block_HTML = $this->wrap_with_container( $block_HTML );

				    }

				    //	Add this block to the Content Blocks HTML array before starting on the next block
				    array_push( $this->content_blocks_HTML, $block_HTML);
			 
			    endwhile;

			    //	Turn the Content Blocks HTML array into a string
			    $blocks_HTML = implode(' ', $this->content_blocks_HTML);

		        if( $this->end_strip ) {

		        	$blocks_HTML .= "<div class=\"container\">";
		        		$blocks_HTML .= "<div class=\"row\">";
		    		    	$blocks_HTML .= "<div class=\"twelve columns\">";

	    		    		if( is_single() ) {

    			        		$prev_post_permalink = get_permalink(get_adjacent_post(false,'',false));
    			        		$next_post_permalink = get_permalink(get_adjacent_post(false,'',true));

    			        		$blocks_HTML .= "<div class=\"pagination\">";
    				        		$blocks_HTML .= "<ul>";
    					        		$blocks_HTML .= "<li class=\"to-prev arrow-button\">";
    					        			$blocks_HTML .= "<a href=\"{$prev_post_permalink}\"></a>";
    					        		$blocks_HTML .= "</li>";
    					        		$blocks_HTML .= "<li class=\"to-next arrow-button\">";
    					        			$blocks_HTML .= "<a href=\"{$next_post_permalink}\"></a>";
    					        		$blocks_HTML .= "</li>";
    				        		$blocks_HTML .= "</ul>";
    			        		$blocks_HTML .= "</div>";

	    		    		} else {

	    		    			$blocks_HTML .= "<div id=\"bottom-border\"></div>";

	    		    		}

		    		    	$blocks_HTML .= "</div>";				    			
		        		$blocks_HTML .= "</div>";
		        	$blocks_HTML .= "</div>";

		        }

			    //	Do we need to echo the Content Blocks automatically
			    if( $args['echo'] )
			   		echo $blocks_HTML;

			   	//	Return the Content Blocks to the document
				return $blocks_HTML;

			else:

				//	No Content Blocks
				return false;

			endif;

		}

/*  ============================================================
    -->> Wrap with Column
    ============================================================ */
	 /**
	  * Wraps a content in a Column
	  *
	  * @return void
	  */

		public function wrap_with_column( $content_html = null ) {

			// Variable to hold the Column Elements HTML
			$column_html = '';

			//	Check for an array of classes to merge with the current Column Args class array
			if( is_array( $this->args['column_classes'] ) )
				$this->column_args['classes'] = array_merge( $this->column_args['classes'], $this->args['column_classes'] );

			// If there's no class array to merge check if its a string
			elseif( is_string( $this->args['column_classes'] ) )
				array_push( $this->column_args['classes'], $this->args['column_classes'] );

			if( $this->args['auto_clearing'] ) {
				if ( $this->columns_total % 12 == 0 || ( $this->columns_total + $this->column_args['columns'] ) % 12 < $this->columns_modulus && ( $this->columns_total + $this->column_args['columns'] ) % 12 !== 0 ) {
					array_push( $this->column_args['classes'], 'clear' );	
				}			
			}

			//	The content that will be wrapped
			$this->column_args['content'] = $content_html;

			//	Build and get the Column HTML
			$column_html = Builder::wrap_content_in( $this->column_args )->get();

			//	Increment/Calculate the new Column count and Column Modulus
			//	Now the next column is ready to work out if it needs to have its float cleared
			$this->columns_modulus = $this->columns_total % 12; 
			$this->columns_total += $this->column_args['columns'];

			//	Serve up the resulting HTML
			return $column_html;			

		}

/*  ============================================================
    -->> Wrap with Row
    ============================================================ */
	 /**
	  * Wraps a content in a row
	  *
	  * @return void
	  */

		public function wrap_with_row( $content_html = null ) {

			// Variable to hold the Row Elements HTML
			$row_html = '';

			//	Check for an array of classes to merge with the current Row Args class array
			if( is_array( $this->args['row_classes'] ) )
				$this->row_args['classes'] = array_merge( $this->row_args['classes'], $this->args['row_classes'] );

			// If there's no class array to merge check if its a string
			elseif( is_string( $this->args['row_classes'] ) )
				array_push( $this->row_args['classes'], $this->args['row_classes'] );

			//	The content that will be wrapped
			$this->row_args['content'] = $content_html;

			//	Build and get the Row HTML
			$row_html = Builder::wrap_content_in( $this->row_args )->get();

			//	Serve up the resulting HTML
			return $row_html;

		}

/*  ============================================================
    -->> Wrap with Container
    ============================================================ */
	 /**
	  * Wraps a content in a container
	  *
	  * @return void
	  */

		public function wrap_with_container( $content_html = null ) {

			// Variable to hold the Container Elements HTML
			$container_html = '';

			//	Check for an array of classes to merge with the current Container Args class array
			if( is_array( $this->args['container_classes'] ) )
				$this->container_args['classes'] = array_merge( $this->container_args['classes'], $this->args['container_classes'] );

			// If there's no class array to merge check if its a string
			elseif( is_string( $this->args['container_classes'] ) )
				array_push( $this->container_args['classes'], $this->args['container_classes'] );

			//	The content that will be wrapped
			$this->container_args['content'] = $content_html;

			//	Build and get the Container HTML
			$container_html = Builder::wrap_content_in( $this->container_args )->get();

			//	Serve up the resulting HTML
			return $container_html;

		}

/*  ============================================================
    -->> Get Page URL
    ============================================================ */

	 /**
	  * get_page_url()
	  * 
	  * Get the current page url. 
	  *
	  * @return String
	  */

		public function get_page_url() {

			//	Check for front page or search page
			if( is_front_page() || is_search() ):
				return home_url();

			//	Check for blog page
			elseif ( is_home() ):
				return get_permalink( get_option('page_for_posts' ) );

			//	Category Page
			elseif(is_category()):
				$category = get_query_var('cat');
				$category = get_category($category);
				return get_category_link( $category->term_id );

			//	We are on a normal page
			else:
				return get_permalink();
			endif;
		}

/*  ============================================================
    -->> Return GET vars string
    ============================================================ */

	 /**
	  * return_get_vars_string()
	  * 
	  * Turns the current GET vars into a string. Handels prepending the correct concatenation symbols
	  * 
	  * @param  Array  $config 	Configurable args for the method
	  *                         - `remove` (array): An array of GET vars to exclude from the returned string
	  *                         - `add` (array): An assiative array of GET vars to add to the string. eg `array('foo'=>'bar')` will add `?foo=bar` OR `&foo=bar` to the string depending on if it is the first of only variable.
	  * 
	  * 
	  * @return String
	  * Returns a string of GET vars
	  */
		public function return_get_vars_string( $config = array() ) {

			$get_vars = null;
			$divider = '?';

			if (!empty($_GET)) {

				foreach ( $_GET as $key => $value) {

					if( !isset($config['remove']) || !in_array( $key, $config['remove'] ) ) {

						$get_vars .= $divider.$key.'='.$value;
						if( $divider == '?' ) $divider = '&';
						
					}
				}
			}

			if( isset($config['add']) ) {

				foreach ( $config['add'] as $key => $value) {

					$get_vars .= $divider.$key.'='.$value;
					if( $divider == '?' )$divider = '&';
				}
			}

			return $get_vars;
		}


/*  ============================================================
    -> Blocks 
    ============================================================ */

/*  ============================================================
    -->> Page Title 
    ============================================================ */

	 /**
	  * Method for the Text Content Block
	  *
	  * @return void
	  */
	
		public function page_title_block() {

			// Local Variables
			$row_layout 		= get_row_layout();
			$column_content 	= '';
			$column_HTML 		= '';
			$heading_classes 	= array('columns');

			//	ACF field data
			$column_heading 	= get_sub_field('column_heading');
			$column_settings 	= get_sub_field('column_settings');
			$column_width 		= get_sub_field('width');
			$custom_heading 	= get_sub_field('custom_heading');
			$sub_heading 		= get_sub_field('sub_heading');

			//	Add the Column Width Class
			array_push( $this->column_args['classes'], $this->constant_columns[ $column_width ] );
			array_push( $this->column_args['classes'], $row_layout );

			//	Store how many column this column takes up
			$this->column_args['columns'] = $column_width;

			$page_title = ( in_array( 'custom_heading', $column_settings ) && $custom_heading )
				? $custom_heading
				: get_the_title();

			$page_sub_title = ( in_array( 'add_sub_heading', $column_settings ) && $sub_heading )
				? $sub_heading
				: null;

			$column_content .= "<h1 class=\"article-heading\">{$page_title}</h1>";
			
			if( $page_sub_title )
				$column_content .= "<h2 class=\"article-subheading\">{$page_sub_title}</h2>";
			 

			//	Set up the Arguments to create a panel
			$panel_args = array(
				'element' => 'div',
				'classes' => array( 'panel' ),
				'content' => $column_content
 			);

			//	Wrap the content in a div.panel
			$column_content = Builder::wrap_content_in( $panel_args )->get();

			//	Wrap the Column Content in a Column
			$column_HTML .= $this->wrap_with_column( $column_content );

			$this->end_strip = true;

			//	Return the Content
			return $column_HTML;

		}

/*  ============================================================
    -->> Category Links
    ============================================================ */

	 /**
	  * Method for the Text Content Block
	  *
	  * @return void
	  */
	
		public function category_links_block() {

			// Local Variables
			$row_layout 		= get_row_layout();
			$column_content 	= '';
			$column_HTML 		= '';
			$heading_classes 	= array('columns');

			//	ACF field data
			$column_heading 	= get_sub_field('column_heading');
			$column_settings 	= get_sub_field('column_settings');
			$column_width 		= get_sub_field('width');
			$categories 		= get_sub_field('pick_categories');

			//	Add the Column Width Class
			array_push( $this->column_args['classes'], $this->constant_columns[ $column_width ] );

			array_push( $this->column_args['classes'], $row_layout );

			//	Store how many column this column takes up
			$this->column_args['columns'] = $column_width;

 			if( !$categories && !in_array( 'pick_categories', $column_settings ) )
 				$categories = wp_get_post_categories( get_the_ID() );

 			if( $categories ) {

 				//	BEGIN .categories-list
 				$column_content .= "<div class=\"categories-list\">";

 				$filter_count = 1;

 				foreach ( $categories as $category ) {

 					if ( $filter_count++ > 1 )
 						$column_content .= " / ";

 					$get_args = array(
 						'remove'=>array(),
 						'add' => array(
 							'cat_id' => $category
 						)
 					);

 					$filter_class = ( $post_categories === $category ) ? 'active-filter' : 'inactive-filter'  ;

 					$column_content .= "<a href=\"".get_category_link( $category )."\">".get_cat_name( $category )."</a>";
 				}
 				//	END .categories-list
 				$column_content .= "</div>";				
 			}

			//	Set up the Arguments to create a panel
			$panel_args = array(
				'element' => 'div',
				'classes' => array( 'panel' ),
				'content' => $column_content
 			);

			//	Wrap the content in a div.panel
			$column_content = Builder::wrap_content_in( $panel_args )->get();

			//	Wrap the Column Content in a Column
			$column_HTML .= $this->wrap_with_column( $column_content );

			$this->end_strip = true;

			//	Return the Content
			return $column_HTML;

		}

/*  ============================================================
    -->> Text 
    ============================================================ */

	 /**
	  * Method for the Text Content Block
	  *
	  * @return void
	  */
	
		public function text_block() {

			// Local Variables
			$row_layout 		= get_row_layout();
			$column_content 	= '';
			$column_HTML 		= '';
			$heading_classes 	= array('columns');
			$heading_width 		= 12;

			//	ACF field data
			$column_heading 	= get_sub_field('column_heading');
			$column_settings 	= get_sub_field('column_settings');
			$column_width 		= get_sub_field('width');
			$column_wysiwyg 	= get_sub_field('content');
			$extra_padding 		= get_sub_field('extra_padding');

			//	Add the Column Width Class
			array_push( $this->column_args['classes'], $this->constant_columns[ $column_width ] );

			array_push( $this->column_args['classes'], $row_layout );

			//	Store how many column this column takes up
			$this->column_args['columns'] = $column_width;

			if( $heading_width - $column_width > 0 )
				$heading_width -= $column_width;

			array_push( $heading_classes, $this->constant_columns[ $heading_width ] );

			$heading_classes = implode( ' ', $heading_classes );

			//	The Column Heading
			if( in_array( 'display_heading', $column_settings ) && $column_heading ) {

				$column_HTML .= "<div class=\"{$heading_classes}\">";

					$column_HTML .= "<div class=\"\">";

						$column_HTML .= "<h2 class=\"title\">".$column_heading."</h2>";

					$column_HTML .= "</div>";

				$column_HTML .= "</div>";

				//	Increment/Calculate the new Column count and Column Modulus
				//	Now the next column is ready to work out if it needs to have its float cleared
				$this->columns_modulus = $this->columns_total % 12; 
				$this->columns_total += $heading_width;

			} 

			$column_content .= $column_wysiwyg;

			//	Set up the Arguments to create a panel
			$panel_args = array(
				'element' => 'div',
				'classes' => array( 'panel' ),
				'content' => $column_content
 			);

			//	Wrap the content in a div.panel
			$column_content = Builder::wrap_content_in( $panel_args )->get();

			//	Wrap the Column Content in a Column
			$column_HTML .= $this->wrap_with_column( $column_content );

			$this->end_strip = true;

			//	Return the Content
			return $column_HTML;

		}

/*  ============================================================
    --->>> Post Feed 
    ============================================================ */

	 /**
	  * Method for the Post Feed Content Block
	  *
	  * @return void
	  */

		public function post_feed_block() {

			//	Post Feed ACF field data
			$column_heading 	= get_sub_field('column_heading');
			$column_settings 	= get_sub_field('column_settings');
			$column_width 		= get_sub_field('width');
			$feed_columns 		= 4;
			$selection_type 	= get_sub_field('selection_type');
			$include_post_type 	= ( $selection_type == 'post_type' ) ? get_sub_field('include_post_type') : 'any';
			$include_selection 	= ( $selection_type == 'selection' ) ? get_sub_field('include_selection') : null;
			$posts_per_page 	= get_sub_field('posts_per_page');
			$taxonomy_field 	= get_sub_field('filter_links');

			//	Post Feed Variables
			$column_content 	= '';
			$post_width 		= $this->relative_columns[ $feed_columns ];
			$post_count 		= 0;
			$post_feed_classes 	= array();
			$post_categories	= ( isset($_GET['cat_id']) ) ? $_GET['cat_id'] : null ;

			//	Pushing Classes to the Post Feed
			array_push( $post_feed_classes, 'clearfix' );

			//	Get Post feed Class string
			$post_feed_classes = implode(' ', $post_feed_classes );

			$paged = ( get_query_var('page') ) ? get_query_var('page') : 1;

			// Required Custom Query Arguments
			// 1. The number of posts per page must be set
			// 2. The current page number must be passed into the Query
			$custom_query_args = array(
			    "posts_per_page" => $posts_per_page,
			    //"posts_per_page" => 3,
			    "paged" => $paged, 
			    "post_type" => $include_post_type,
			    "post__in" => $include_selection,
			    //"post_parent__in" => $include_children,
			    "category__in" => $post_categories,
			    //'meta_key'        => 'date',
			    //'orderby'       => 'meta_value_num',
			    'order'         => 'DESC'
			);
			
			if( $taxonomy_field ) {

				//	BEGIN .categories-list
				$column_content .= "<div class=\"categories-list\">";

				$filter_count = 1;

				foreach ( $taxonomy_field as $taxonomy ) {

					if ( $filter_count++ > 1 )
						$column_content .= " / ";

					$get_args = array(
						'remove'=>array( 'cat_id' ),
						'add' => array(
							'cat_id' => $taxonomy
						)
					);

					$filter_class = ( $post_categories === $taxonomy ) ? 'active-filter' : 'inactive-filter'  ;

					//$column_content .= "<a href=\"".get_category_link( $taxonomy )."\">".get_cat_name( $taxonomy )."</a>";
					$column_content .= "<a class=\"{$filter_class}\" href=\"".get_the_permalink().$this->return_get_vars_string( $get_args )."\">".get_cat_name( $taxonomy )."</a>";
				}
				//	END .categories-list
				$column_content .= "</div>";				
			}

			//	BEGIN Column
			$column_content .= "<div class=\"post-feed {$post_feed_classes}\">";
			
			// Custom Query
			$custom_query = new WP_Query( $custom_query_args );

			if ( $custom_query->have_posts() ) while ( $custom_query->have_posts() ): $custom_query->the_post();

				//	Post Variables
				$post_classes 			= array( 'post', 'columns', 'feature-wrapper' );
				$featured_img 			= get_field('featured_image', get_the_ID() );
				$featured_img_sizes 	= $featured_img['sizes'];
				$sub_heading 			= get_field('sub_heading');

				$post_width = $this->constant_columns[ $feed_columns ];
				array_push( $post_classes, $post_width );
				$excerpt_length = 150;

				$post_count++;

				$post_classes = implode(' ', $post_classes );

				//	Setup the Excerpt
				$excerpt_args = array(
					//	Search for text ACF's with the following names
					'content' => array('content'),
					'echo' => false,
					'container_class' => 'excerpt',
					'length' => $excerpt_length
				);

				//	BEGIN .post
				$column_content .= "<div class=\"{$post_classes}\">";

					//	BEGIN Link
					$column_content .= "<a href=\"".get_the_permalink()."\">";

						//	BEGIN .thumbnail
						$column_content .= "<div class=\"\">";

							//	Add Feature Image Thumbnail
							//$column_content .= get_the_post_thumbnail( get_the_ID() );
							$column_content .= "<img src=\"{$featured_img_sizes['medium']}\" alt=\"{$featured_img['caption']}\" id=\"\" class=\"\" />";

						//	END .thumbnail
						$column_content .= "</div>";

						//	BEGIN .panel
						$column_content .= "<div class=\"panel\">";

							//	Add Heading
							$column_content .= "<h3>".get_the_title()."</h3>";

							$sub_heading_args = array(
								'content' => array('sub_heading'),
								'length' => 999,
								'echo' => false,
								'container' => false

							);

							$sub_heading = Excerpt::get( $sub_heading_args );

							if( $sub_heading )
								$column_content .= "<h4>{$sub_heading}</h4>";

							//	Add Arrow Box
							$column_content .= "<div class=\"arrow-box\"></div>";

						//	END .panel
						$column_content .= "</div>";

					//	END Link
					$column_content .= "</a>";

				//	END .post
				$column_content .= "</div>";

			endwhile;

			//	END Column
			$column_content .= "</div>";

			$pagination_args = array(
				'padding' 		=> 3,
				'next_text' 	=> '',
				'prev_text' 	=> '',
				'first' => false,
				'last' => false,
				'numbered' => false
			);

			wp_reset_query();

			$this->end_strip = true;

			if( in_array( 'pagination', $column_settings ) ) {

				$pagination = Pagination::create()->get_pagination( $custom_query, $pagination_args );

				$column_content .= $pagination;

				if( $pagination )
					$this->end_strip = false;
			}

			//	Add the Column Width Class
			array_push( $this->column_args['classes'], $this->constant_columns[ $column_width ] );
			array_push( $this->column_args['classes'], 'nest' );

			//	Set the number of columns
			$this->column_args['columns'] = $column_width;

			//	Wrap the Column Content in a Column
			$column_HTML = $this->wrap_with_column( $column_content );

			return $column_HTML;

		}

/*  ============================================================
    --->>> Gallery
    ============================================================ */

	 /**
	  * Method for the Slider Content Block
	  *
	  * @return void
	  */

		public function gallery_block() {

			//	ACF field data
			$column_heading 	= get_sub_field('column_heading');
			$column_settings 	= get_sub_field('column_settings');
			$column_width 		= get_sub_field('width');
			$images 			= get_sub_field('images');
			$image 				= get_sub_field('image');
			$gallery_type 		= get_sub_field('type');
			$captions 			= get_sub_field('captions');
			$image_count		= 0;

			$gallery_type_classes = array( $gallery_type );

			if( in_array( 'fullscreen_gallery', $column_settings) )
				array_push( $gallery_type_classes, 'has-fullscreen-gallery');

			$gallery_type_classes = implode( ' ', $gallery_type_classes );

			//	Local Variables
			$column_content = '';

			//	BEGIN .gallery
			$column_content .= "<div class=\"gallery\">";

				//	BEGIN {$gallery_type}
				$column_content .= "<div class=\"{$gallery_type_classes}\">";

					//	BEGIN .images
					$column_content .= "<div class=\"images clearfix\">";

					//	BEGIN Slider
					if( $images && $gallery_type === 'slider' ) {

						foreach ( $images as $key => $image ) {

							$external_link = get_field('external_link',$image['id']);
							$internal_link = get_field('internal_link',$image['id']);

							if( $internal_link && !in_array( 'fullscreen_gallery', $column_settings) )
								$slide_link = $internal_link;
							elseif( $external_link && !in_array( 'fullscreen_gallery', $column_settings) )
								$slide_link = $external_link;
							else
								$slide_link = false;

							$image_sizes = $image['sizes'];

							$column_content .= "<div class=\"slide\">";

								if( $slide_link )
									$column_content .= "<a href=\"{$slide_link}\" >";

								//	Add Image
								$column_content .= "<img src=\"{$image_sizes['large']}\" alt=\"{$image['caption']}\" id=\"\" class=\"lazyOwl\" />";

								if( $slide_link )
									$column_content .= "</a>";

							$column_content .= "</div>";
						}

					//	END Slider
					}
					//	BEGIN Image Roll
					elseif( $images && $gallery_type === 'roll' ) foreach ( $images as $key => $image ) {
						
						$image_sizes = $image['sizes'];

						$column_content .= "<img src=\"{$image_sizes['large']}\" alt=\"{$image['caption']}\" id=\"\" class=\"\" />";
						

					//	END Image Roll
					} 
					//	BEGIN Single Image
					elseif( $image && $gallery_type === 'single' ) {

						$image_sizes = $image['sizes'];

						//	Add Image
						$column_content .= "<img src=\"{$image_sizes['large']}\" id=\"\" class=\"\" />";

					//	END Single Image
					}

					//	END .images
					$column_content .= "</div>";

					if( in_array( 'fullscreen_gallery', $column_settings) ) {

						//$column_content .= "<div class=\"fullscreen gallery\">";

							$column_content .= "<div class=\"fullscreen slider\">";

								$column_content .= "<div class=\"close\"></div>";

								$column_content .= "<div class=\"images\">";

								foreach ( $images as $key => $image ) {

									$image_sizes = $image['sizes'];

										$column_content .= "<div class=\"slide\">";

											//	Add Image
											$column_content .= "<img data-src=\"{$image_sizes['max_display']}\" alt=\"{$image['caption']}\" id=\"\" class=\"lazyOwl\" />";

											if( $slide_link )
												$column_content .= "</a>";

										$column_content .= "</div>";

								}

								$column_content .= "</div>";

							$column_content .= "</div>";
							
						//$column_content .= "</div>";
					}

				//	END .{type}
				$column_content .= "</div>";

				if( $captions === 'below' ) {

					$column_content .= "<div class=\"gallery-caption\">";
						$column_content .= "<p>Caption</p>";
					$column_content .= "</div>";
					
				}

			//	END .gallery
			$column_content .= "</div>";

			//	Add the Column Width Class
			array_push( $this->column_args['classes'], $this->constant_columns[ $column_width ] );
			//array_push( $this->column_args['classes'], 'nest' );

			//	Set the number of columns
			$this->column_args['columns'] = $column_width;

			//	Wrap the Column Content in a Column
			$column_HTML = $this->wrap_with_column( $column_content );

			return $column_HTML;

		}

/*  ============================================================
    --->>> Google Map
    ============================================================ */

	 /**
	  * Method for the Post Feed Content Block
	  *
	  * @return void
	  */

		public function google_map_block() {

			// Local Variables
			$row_layout = get_row_layout();
			$column_content = '';

			//	ACF field data
			$column_heading 	= get_sub_field('column_heading');
			$column_settings 	= get_sub_field('column_settings');
			$column_width 		= get_sub_field('width');
			$address = get_sub_field('address');

			//	Add the Column Width Class
			array_push( $this->column_args['classes'], $this->constant_columns[ $column_width ] );

			//	Set the number of columns
			$this->column_args['columns'] = $column_width;

			//	The Column Heading
			if( in_array( 'display_heading', $column_settings ) && $column_heading ) {

				$column_content .= "<h2 class=\"\">".$column_heading."</h2>";

			}

			$marker_icon = get_sub_field('marker_icon');
			$marker_icon = $marker_icon['sizes'];
			$marker_icon = $marker_icon['thumbnail'];

			//	The Google Map HTML
			$column_content .= "<div class=\"acf-map\">";

				$column_content .= "<div class=\"marker\" data-icon=\"{$marker_icon}\" data-lat=\"{$address['lat']}\" data-lng=\"{$address['lng']}\"></div>";

			$column_content .= "</div>";

			//	Wrap the Column Content in a Column
			$column_HTML = $this->wrap_with_column( $column_content );

			//	Return the Content
			return $column_HTML;

		}

  	}


