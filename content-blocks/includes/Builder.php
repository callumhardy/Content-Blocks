<?php 
/*
 * The Builder class.
 */

  	class Builder {

	/**
	 * The Default Element Args
	 */
		public $default_element_args = array(
			'tag' => 'div',
			'id' => null,
			'classes' => null,
			'styles' => null,
			'content' => null
		);

	/**
	 * For storing the merger of default and config element args
	 */
		public $element_args;

	 /**
	  * The CLASS instance
	  */
		private static $_instance = null;


	 /**
	  * create()
	  * 
	  * Creates an instance of the CLASS using a static method
	  *
	  * @return string
	  */

		public static function create() {
			
			$obj = new static();

			return $obj;

		}

	 /**
	  * wrap_content_in()
	  * 
	  * @return string
	  */

		public static function wrap_content_in( $config_args = array() ) {
			
			// If no instance is available, create it.
			if( self::$_instance === null ) {

				self::$_instance = self::create();

			}

			$obj = self::$_instance;

			return $obj->build_element( $config_args );

		}
	
	/**
	 * Begins the process of creating the Hero Unit
	 *
	 * @return void
	 */

		public function __construct( $config_args = array() ) {

		}

	/**
	 * @return void
	 */

		public function build_element( $config_element_args = array() ) {

			//	Overwrite/Merge $default_args with the $config_args, if any are present
			$this->element_args = array_merge( $this->default_element_args, $config_element_args );
			
			//print_a($this->element_args);

			$tag 		= $this->element_args['tag'];
			$id 		= $this->get_attr_id( $this->element_args['id'] );
			$classes 	= $this->get_attr_classes( $this->element_args['classes'] );
			$styles 	= $this->get_attr_styles( $this->element_args['styles'] );
			$content 	= $this->element_args['content'];

			$this->content = "<{$tag} {$id} {$classes} {$styles}>{$content}</{$tag}>";

			return $this;

		}

	/**
	 * @return void
	 */

		public function get_attr_id( $id = null ) {

			if( is_string( $id ) )
				return "id=\"" . $id . "\"";
			else
				return false;
		}

	/**
	 * @return void
	 */

		public function get_attr_classes( $classes = null ) {

			if( is_array( $classes ) )
				return "class=\"" . implode(' ', $classes) . "\"";
			elseif( is_string( $classes ) )
				return "class=\"" . $classes . "\"";
			else
				return false;
		}

	/**
	 * @return void
	 */

		public function get_attr_styles( $styles = null ) {

			if( is_array( $styles ) ) {

				$styles_string = '';

				foreach ( $styles as $property => $value ) {

					if( is_string( $value ) && !empty($value) )
						$styles_string .= $property . ":" . $value . ";";

				}

				if( !empty( $styles_string ) )
					return "style=\"" . $styles_string . "\"";
				else
					return false;

			} elseif( is_string( $styles ) ) {

				return "style=\"" . $styles . "\"";

			} else {

				return false;

			}
		}

	/**
	 * @return void
	 */

		public function get() {

			return $this->content;

			//return htmlspecialchars( $this->content );
		}

	/**
	 * @return void
	 */

		public function store_args( $variable_name ) {

			$this->{$variable_name} = $this->element_args;

			return $this;
		}

	/**
	 * @return void
	 */

		public function print_args( $variable_name ) {

			print_a( $this->{$variable_name} );

			return $this;
		}

	}
