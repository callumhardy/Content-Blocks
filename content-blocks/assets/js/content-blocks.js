(function( $, window, document, undefined ) {

	/**
	 * Fall back for older browsers that don't support Object.create
	 */
	if( typeof Object.create !== 'function' ) {

		Object.create = function( object ) {

			function Obj(){}
			Obj.prototype = object;
			return new Obj();
		};
	}

	$document = $(document);
	$window = $(window);

	$document.ready(function($) {

		/**
		 * Handles the setup of the content blocks in the back end
		 * 
		 * @return this
		 */
		ContentBlocks = {

			init: function(){

				var self = this;
					
				self.headingFieldName = 'column_heading';

				//	Find and store the Content Blocks jQuery elements
				self.setElements();

				//	Close certain WP meta boxes
				self.closeMetaBoxes();

				//	Close all ACF Layouts
				self.$layoutHandles.click();

				//	Initially set the Layout Headings
				self.updateLayoutHeadings();

				//	Bind events to the DOM
				self.bindEvents();

				//	Create the Content Block Toggle Buttons
				$('#acf-toggle_content_blocks').find('input').each( function() {

					$this = $(this);
					$label = $this.closest('label');
					var labelText = $label.text();

					$label.replaceWith( '<button class="acf-button content-block-toggle" type="button" value="' + $this.val() + '">' + labelText + '</button>' );
				});

				return self;
				
			},

			/**
			 * Stores or Updates Content Blocks jQuery elements
			 *
			 * @return this
			 */
			setElements: function(){

				var self = this;

				self.$contentBlocks = $('#acf-content_blocks, #acf-content_blocks_repeater');
				self.$layouts = self.$contentBlocks.find('.layout');
				self.$layoutHandles = self.$layouts.find('.acf-fc-layout-handle');

				return self;

			},

			/**
			 * Bind events to the DOM
			 * 
			 * @return this
			 */
			bindEvents: function(){

				var self = this;
				
				//	When a Layout's Heading field's text is altered, make sure the Layout's Heading reflects this change
				$document.on( 'change', '[data-field_name="'+self.headingFieldName+'"]', function() {
					self.updateLayoutHeadings();
				});

				//	Content block Option Buttons
				$document.on('click', '.content-block-toggle', function() {

					$this = $(this);

					//	Open the Content Blocks
					if( $this.val() === 'open_blocks' ) {

						self.openContentBlocks();
					
					//	Close the Content Blocks
					} else if( $this.val() === 'close_blocks' ) {

						self.closeContentBlocks();
					}
				});

				//	refresh the ACF google maps when opening the layouts
				$document.on('click', '.acf-fc-layout-handle', function( e ){

					$layout = $(this).closest('.layout');

					if( $layout.length > 0 ) {

						$maps = $layout.find('.acf-google-map');

						if( $maps.length > 0 ) {

							$maps.each( function() {

								$map = $(this);

								acf.fields.google_map.set({
									$el : $map
								}).refresh();

							});
						}
					}
				});
				return self;
			},

			/**
			 * Open all Closed Content Blocks
			 * 
			 * @return this
			 */
			openContentBlocks: function(){

				var self = this;

				self.$layouts.each(function() {

					$layout = $(this);
					$layoutHandle = $layout.find('.acf-fc-layout-handle');

					var layoutToggle = $layout.attr('data-toggle');

					if( layoutToggle === 'closed' ) {

						$layoutHandle.click();

					}
				});

				return self;
				
			},

			/**
			 * Close all open Content Blocks
			 * 
			 * @return this
			 */
			closeContentBlocks: function(){

				var self = this;

				self.$layouts.each(function() {

					$layout = $(this);
					$layoutHandle = $layout.find('.acf-fc-layout-handle');

					var layoutToggle = $layout.attr('data-toggle');

					if( layoutToggle === 'open' ) {

						$layoutHandle.click();
						
					}
				});

				return self;
	
			},

			/**
			 * Closes specific WP meta boxes
			 * 
			 * @return this
			 */
			closeMetaBoxes: function(){

				$postMetaBoxes = $('.postbox');

				$postMetaBoxes.each(function()
				{
					$this = $(this);

					if( !$this.hasClass('closed') && $this.attr('id') !== 'submitdiv' && $this.attr('id') !== undefined ) $this.addClass('closed');
				});

				return self;
			},

			/**
			 * Search all layouts for valid Heading Text and update the Layout handle to include the Heading Text
			 * 
			 * @return this
			 */
			updateLayoutHeadings: function(){

				var self = this;

				self.setElements();

				self.$layouts.each( function(){

					$layout = $(this);
					$handle = $layout.find('.acf-fc-layout-handle');
					$heading = $layout.find('[data-field_name="'+self.headingFieldName+'"]');
					var headingValue = $heading.find('input').eq(0).val();

					$handleHeading = $handle.find('.handle-heading');
					if( $handleHeading.length > 0 ) $handleHeading.remove();

					$handleHeading = $( '<strong>' );
					$handleHeading.addClass( 'handle-heading' );

					if( headingValue !== '' )
						$handleHeading.text( '- ' + headingValue );

					$handle.append( $handleHeading );

				});

				return self;
			}
		};

		//	Create the Content Block Object
		var contentBlocks = Object.create( ContentBlocks );

		//	Initialise it
		contentBlocks.init();

	});

})( jQuery, window, document );