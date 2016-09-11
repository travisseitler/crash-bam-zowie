<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Crash_Bam_Zowie {

	/**
	 * Unique Identifier
	 *
	 * @since 0.1.1
	 */
	protected $plugin_version = '0.5';

	/**
	 * Unique Identifier
	 *
	 * @since 0.1.1
	 */
	protected $plugin_slug = 'crash-bam-zowie';

	/**
	 * Initialization: Let's get it started in here
	 *
	 * @since 0.1.1
	 */
	public function init() {

		// Define custom post type on init
		add_action( 'init',                                           array( $this, 'define_posttype' ) );
		add_action( 'admin_init',                                     array( $this, 'define_posttype' ) );

		// Define custom taxonomy on init
		add_action( 'init',                                           array( $this, 'define_taxonomy' ) );
		add_action( 'admin_init',                                     array( $this, 'define_taxonomy' ) );

		// Custom rewrite rules for series, issues/chapters, and pages
		add_filter( 'post_type_link',                                 array( $this, 'page_permalinks' ), 10, 4);
		add_filter( 'rewrite_rules_array',                            array( $this, 'insert_rewrite_rules') );

		// Adjust the admin display for our Custom Post Type
		add_action( 'do_meta_boxes',                                  array( $this, 'change_featured_image_box' ) );

		// Adjust the admin display for our Custom Taxonomy
		// Thanks to CatapultThemes at https://catapultthemes.com/adding-an-image-upload-field-to-categories/
    add_action( $this->plugin_slug . '-issues_add_form_fields',   array( $this, 'add_issue_image' ), 10, 2 );
    add_action( 'created_' . $this->plugin_slug . '-issues',      array( $this, 'save_issue_image' ), 10, 2 );
    add_action( $this->plugin_slug . '-issues_edit_form_fields',  array( $this, 'update_issue_image' ), 10, 2 );
    add_action( 'edited_'.$this->plugin_slug . '-issues',         array( $this, 'updated_issue_image' ), 10, 2 );

		// Add a custom Settings Page in the Admin
		add_action( 'admin_menu',                                     array( $this, 'settings_page' ) );

		// Add CSS classes to target specific term pages in the admin
		add_filter( 'admin_body_class',                               array( $this, 'admin_body_class' ) );
    add_action( 'admin_footer',                                   array( $this, 'admin_add_script' ) );
		add_action( 'admin_enqueue_scripts',                          array( $this, 'load_wp_media_files' ) );

	}

	/**
	 * Define Custom Post Type: Comic Page
	 *
	 * @since 0.1.1
	 */
	function define_posttype() {

		/** Post Thumbnails: WE HAZ DEM. */
		add_theme_support('post-thumbnails');

		/** Define the Custom Post Type */
		$top_level_name = 'Webcomics';
		$front_name     = 'Comic Pages';
		$singular_name  = 'Comic Page';
		$plural_name    = 'Comic Pages';
		$args = array(
			'public'                => true,
			'menu_icon'             => "dashicons-layout",
			'menu_position'         => null,
			'capability_type'       => array( 'post', 'posts' ),
			'map_meta_cap'          => true,
			'hierarchical'          => false,
			'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author' ),
			'has_archive'           => true,
			'can_export'            => true,
			'rewrite'              => array(
				'slug'                       => 'comics/%crash_bam_zowie_filters%',
				'with_front'                 => false,
			),
			'labels'                => array(
				'name'                       => $front_name,
				'singular_name'              => $singular_name,
				'menu_name'                  => $top_level_name,
				'all_items'                  => 'All ' . $plural_name,
				'add_new'                    => 'Add ' . $singular_name,
				'add_new_item'               => 'Add ' . $singular_name,
				'edit_item'                  => 'Edit ' . $singular_name,
				'new_item'                   => 'New ' . $singular_name,
				'view_item'                  => 'View ' . $singular_name,
				'search_items'               => 'Search ' . $plural_name,
				'not_found'                  => 'No ' . $plural_name . ' found',
				'not_found_in_trash'         => 'No ' . $plural_name . ' found in Trash',
			),
			'publicly_queryable'    => true,
		);

		register_post_type( $this->plugin_slug, $args );

	}

	/**
	 * Define Custom Taxonomy: Comic Issues
	 *
	 * @since 0.1.1
	 */
	function define_taxonomy() {

		$singular_name = 'Series/Issue';
		$plural_name = 'Series &amp; Issues';

		$args = array(
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'rewrite'           => array( 'slug' => 'comics' ),
			'popular_items'     => null,
			'labels'            => array(
				'name'                       => $plural_name,
				'singular_name'              => $singular_name,
				'menu_name'                  => $plural_name,
				'all_items'                  => 'All ' . $plural_name,
				'edit_item'                  => 'Edit ' . $plural_name,
				'view_item'                  => 'View ' . $singular_name,
				'update_item'                => 'Update ' . $plural_name,
				'add_new_item'               => 'Add ' . $singular_name,
				'new_item_name'              => 'New ' . $singular_name,
				'search_items'               => 'Search ' . $plural_name,
				'popular_items'              => 'Popular ' . $plural_name,
				'separate_items_with_commas' => 'Separate ' . $plural_name . ' with commas',
				'add_or_remove_items'        => 'Add or remove ' . $plural_name,
				'choose_from_most_used'      => 'Choose from the most used ' . $plural_name,
				'not_found'                  => 'No ' . $plural_name . ' found',
			),
		);

		register_taxonomy( $this->plugin_slug . '-issues', $this->plugin_slug, $args );

		$args = array(
		    'sanitize_callback' => 'sanitize_my_meta_key',
		    'auth_callback' => 'authorize_my_meta_key',
		    'type' => 'string',
		    'description' => 'My registered meta key',
		    'single' => true,
		    'show_in_rest' => true,
		);
		register_meta( $this->plugin_slug . '-issues', 'cbz_cover_logo', $args );

	}

	/**
	 * Custom Rewrite Rules: Replace slug placeholder
	 *
	 * @since 0.5
	 */
	function page_permalinks( $url, $post )	{

		if ( false !== strpos( $url, '%crash_bam_zowie_filters%' ) ) {

			$the_terms = get_the_terms( $post->ID, $this->plugin_slug . '-issues' );

			$slug = 'uncategorized/item';

	    if( is_array( $the_terms ) ) {

				$term = array_pop( $the_terms );

				$slug = $term->slug;

				if( $term->parent > 0 )	{

					$parent = get_term( $term->parent, $this->plugin_slug . '-issues' );

					$slug = $parent->slug . '/' . $slug;

				}

			}

			$url = str_replace( '%crash_bam_zowie_filters%', $slug, $url );

	  }

	  return $url;

	}

	/**
	 * Custom Rewrite Rules: Insert new rules
	 *
	 * @since 0.5
	 */
	function insert_rewrite_rules( $rules ) {

		$protocol = ( !empty($_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ) ? "https://" : "http://";
    $home = $_SERVER['HTTP_HOST'] . '/';

		$newrules = array();

		/** Grab all terms, build hierarchies, then foreach that array to generate a set of rewrite rules. */
		$args=array(
			'post_type'           => $this->plugin_slug,
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'ignore_sticky_posts' => 1,
			'orderby'             => 'menu_order',
			'order'               => 'ASC',
		);

		$query = new WP_Query($args);

		if( $query->have_posts() ) {

			while( $query->have_posts() ) {

				$query->the_post();

				global $post;

				$newrules[ '^' . str_replace( $protocol . $home, '', get_post_permalink() ) . '?$' ] = 'index.php?post_type=' . $this->plugin_slug . '&name=' . $post->post_name;

			}

		}

		wp_reset_postdata();

		/** Anything that doesn't have an explicit rule should fall under a wildcard match that presumes it's content. */
		$newrules[ '^comics/(.+?)/(.+?)/(.+?)/?$' ]                                = 'index.php?' . $this->plugin_slug . '-issues=$matches[3]';
		$newrules[ '^comics/(.+?)/(.+?)/?$' ]                                      = 'index.php?' . $this->plugin_slug . '-issues=$matches[2]';
		$newrules[ '^comics/(.+?)/?$' ]                                            = 'index.php?' . $this->plugin_slug . '-issues=$matches[1]';

		return $newrules + $rules;

	}

	/**
	 * Admin: Modify the appearance of the Featured Image block
	 *
	 * @since 0.1.1
	 */
	function change_featured_image_box()	{

	    remove_meta_box( 'postimagediv', $this->plugin_slug, 'side' );

	    add_meta_box( 'postimagediv', __('Comic Art'), 'post_thumbnail_meta_box', $this->plugin_slug, 'normal', 'high' );

	}

  /**
   * Add a Cover Art form field in the New Issue/Series page
	 *
   * @since 0.1.1
   */
  public function add_issue_image( $taxonomy ) { ?>
    <div class="form-field term-group">
      <label for="issues-image-id"><?php _e( 'Cover Art' ); ?></label>
      <input type="hidden" id="issues-image-id" name="issues-image-id" class="custom_media_url" value="">
      <div id="issues-image-wrapper"></div>
      <p>
        <input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button" name="ct_tax_media_button" value="<?php _e( 'Add Image' ); ?>" />
        <input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove" name="ct_tax_media_remove" value="<?php _e( 'Remove Image' ); ?>" />
     </p>
    </div>
  <?php
  }

  /**
   * Save the Cover Art form field
	 *
   * @since 0.1.1
	 */
  public function save_issue_image( $term_id, $tt_id ) {
    if( isset( $_POST['issues-image-id'] ) && '' !== $_POST['issues-image-id'] ){
      $image = $_POST['issues-image-id'];
      add_term_meta( $term_id, 'issues-image-id', $image, true );
    }
  }

  /**
   * Edit the form field
	 *
   * @since 0.1.1
   */
  public function update_issue_image( $term, $taxonomy ) { ?>
    <tr class="form-field term-group-wrap">
      <th scope="row">
        <label for="issues-image-id"><?php _e( 'Cover Art' ); ?></label>
      </th>
      <td>
        <?php $image_id = get_term_meta ( $term -> term_id, 'issues-image-id', true ); ?>
        <input type="hidden" id="issues-image-id" name="issues-image-id" value="<?php echo $image_id; ?>">
        <div id="issues-image-wrapper">
          <?php if ( $image_id ) { ?>
            <?php echo wp_get_attachment_image ( $image_id, 'thumbnail' ); ?>
          <?php } ?>
        </div>
        <p>
          <input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button" name="ct_tax_media_button" value="<?php _e( 'Add Image' ); ?>" />
          <input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove" name="ct_tax_media_remove" value="<?php _e( 'Remove Image' ); ?>" />
        </p>
      </td>
    </tr>
  <?php
  }

	/**
	 * Update the form field value
	 *
   * @since 0.1.1
   */
	 public function updated_issue_image( $term_id, $tt_id ) {
	   if( isset( $_POST['issues-image-id'] ) && '' !== $_POST['issues-image-id'] ){
	     $image = $_POST['issues-image-id'];
	     update_term_meta( $term_id, 'issues-image-id', $image );
	   } else {
	     update_term_meta( $term_id, 'issues-image-id', '' );
	   }
	 }

  /**
   * Add a link to the Settings page in the WordPress Admin Menu
	 *
	 * @since 0.1.1
   */
  function settings_page() {

    add_submenu_page(
			'edit.php?post_type=' . $this->plugin_slug,
      'Webcomic Settings',
			'Webcomic Settings',
			'manage_options',
			'cbz_settings',
      array( $this, 'create_settings_page' )
    );

  }

  /**
   * Create a Settings page
	 *
	 * @since 0.1.1
   */
  function create_settings_page() {

		$terms = get_terms( array(
			'taxonomy'   => $this->plugin_slug . '-issues',
			'parent'     => 0,
			'hide_empty' => false,
		) );

		// Include code for admin options page
		include_once( 'crash-bam-zowie-admin.php' );

  }

	/**
	 * Admin: Add term-specific classes to body tag
	 *
	 * With extra classes, we can more cleanly target specific Admin
	 * pages when applying custom CSS rules
	 *
	 * @since 0.1.1
	 */
	function admin_body_class( $classes ) {

		$screen = get_current_screen();

		if ( 'term' != $screen->base || $this->plugin_slug . '-issues' != $screen->taxonomy ) { return $classes; }

		$term_ID = absint( $_REQUEST['tag_ID'] );
		$term    = get_term( $term_ID, $this->plugin_slug . '-issues', OBJECT, 'edit' );

		$classes = explode( ' ', $classes );

		$additional_classes = array(
			'term-id-' . $term->term_id,
			'term-' . $this->plugin_slug . '-issues' . '-' . $term->slug,
		);

		$classes = array_merge( $classes, $additional_classes );

		$classes = implode( ' ', $classes );

		return $classes;

	}

 /*
	* Add custom Javascript to the WP Admin
	* @since 1.0.0
	*/
	public function admin_add_script() { ?>
		<script>
			jQuery(document).ready( function($) {
				function ct_media_upload(button_class) {
					var _custom_media = true,
					_orig_send_attachment = wp.media.editor.send.attachment;
					$('body').on('click', button_class, function(e) {
						var button_id = '#'+$(this).attr('id');
						var send_attachment_bkp = wp.media.editor.send.attachment;
						var button = $(button_id);
						_custom_media = true;
						wp.media.editor.send.attachment = function(props, attachment){
							if ( _custom_media ) {
								$('#issues-image-id').val(attachment.id);
								$('#issues-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;float:none;" />');
								$('#issues-image-wrapper .custom_media_image').attr('src',attachment.sizes.thumbnail.url).css('display','block');
							} else {
								return _orig_send_attachment.apply( button_id, [props, attachment] );
							}
						 }
					wp.media.editor.open(button);
					return false;
				});
			}
			ct_media_upload('.ct_tax_media_button.button');
			$('body').on('click','.ct_tax_media_remove',function(){
				$('#issues-image-id').val('');
				$('#issues-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;float:none;" />');
			});
			// Thanks: http://stackoverflow.com/questions/15281995/wordpress-create-category-ajax-response
			$(document).ajaxComplete(function(event, xhr, settings) {
				var queryStringArr = settings.data.split('&');
				if( $.inArray('action=add-tag', queryStringArr) !== -1 ){
					var xml = xhr.responseXML;
					$response = $(xml).find('term_id').text();
					if($response!=""){
						// Clear the thumb image
						$('#issues-image-wrapper').html('');
					}
				}
			});
		});
	</script>
	<?php
 	}

	/**
	 * Enqueue Media Library helpers in Admin
	 */
	function load_wp_media_files() {
		wp_enqueue_media();
	}

}
