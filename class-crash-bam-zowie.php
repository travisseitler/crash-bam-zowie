<?php

class Crash_Bam_Zowie {

	/**
	 * Unique Identifier
	 *
	 * @since 0.1.1
	 */
	protected $plugin_version = '0.1.1';

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
		add_action( 'init',                     array( $this, 'define_posttype' ) );
		add_action( 'admin_init',               array( $this, 'define_posttype' ) );

		// Define custom taxonomy on init
		add_action( 'init',                     array( $this, 'define_taxonomy' ) );
		add_action( 'admin_init',               array( $this, 'define_taxonomy' ) );

		// Adjust the admin display for
		add_action( 'do_meta_boxes',            array( $this, 'change_image_box' ) );

		// Add a custom Settings Page in the Admin
		add_action( 'admin_menu',               array( $this, 'settings_page' ) );

		// Add the ability to target specific term pages in the admin for CSS/JS
		add_filter( 'admin_body_class',         array( $this, 'admin_body_class' ) );

	}

	/**
	 * Define Custom Post Type: Comic Page
	 *
	 * @since 0.1.1
	 */
	function define_posttype() {

		/**
		 * Post Thumbnails: WE HAZ DEM.
		 */
		add_theme_support('post-thumbnails');

		/**
		 * Define the Custom Post Type
		 */
		$top_level_name = 'Comic Pages';
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

		$singular_name = 'Comic Issue';
		$plural_name = 'Comic Issues';

		$args = array(
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
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

	}

  /**
   * Add a link to the Settings page in the WordPress Admin Menu
	 *
	 * @since 0.1.1
   */
  function settings_page() {

    add_submenu_page(
			$this->plugin_slug . '-pages',
      'CRASH! BAM! ZOWIE! Webcomic Management Settings',
			'CBZ Settings',
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

		$title = 'CRASH! BAM! ZOWIE! Webcomic Management Settings';

		print '<div class="wrap">';

		print '<h1>' . $title . '</h1>';

		print '</div>'; // .wrap

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

		if ( 'term' != $screen->base || $this->plugin_slug != $screen->taxonomy ) { return $classes; }

		$term_ID = absint( $_REQUEST['tag_ID'] );
		$term    = get_term( $term_ID, $this->plugin_slug, OBJECT, 'edit' );

		$classes = explode( ' ', $classes );

		$additional_classes = array(
			'term-id-' . $term->term_id,
			'term-' . $this->plugin_slug . '-' . $term->slug,
		);

		$classes = array_merge( $classes, $additional_classes );

		$classes = implode( ' ', $classes );

		return $classes;

	}

	/**
	 * Admin: Modify the appearance of the Featured Image block
	 *
	 * @since 0.1.1
	 */
	function change_image_box()	{

	    remove_meta_box( 'postimagediv', $this->plugin_slug, 'side' );


	}

}
