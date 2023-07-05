<?php
namespace Jet_Engine\Modules\Profile_Builder;

class Frontend {

	private $template_id = null;
	private $has_access = false;
	public $menu = null;
	public $current_user_obj = false;
	public $user_page_title = null;

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		require jet_engine()->modules->modules_path( 'profile-builder/inc/menu.php' );
		require jet_engine()->modules->modules_path( 'profile-builder/inc/access.php' );

		$this->access = new Access();
		$this->menu   = new Menu();

		add_action(
			'jet-engine/profile-builder/query/setup-props',
			array( $this, 'add_template_filter' )
		);

		add_filter(
			'jet-engine/listings/dynamic-link/custom-url',
			array( $this, 'dynamic_link_url' ), 10, 2
		);

		add_filter(
			'jet-engine/listings/dynamic-image/custom-url',
			array( $this, 'dynamic_link_url' ), 10, 2
		);

		// SEO hooks.
		add_filter( 'pre_get_document_title', array( $this, 'set_document_title_on_single_user_page' ), 99 );
		add_filter( 'get_canonical_url',      array( $this, 'modify_canonical_url' ) );
	}

	/**
	 * Enqueue page template CSS
	 *
	 * @return [type] [description]
	 */
	public function enqueue_template_css() {

		if ( ! $this->get_template_id() ) {
			return;
		}

		do_action( 'jet-engine/profile-builder/template/assets', $this->get_template_id(), $this );

	}

	public function get_template_id() {
		return apply_filters( 'jet-engine/profile-builder/template-id', $this->template_id );
	}

	/**
	 * Render profile page content
	 *
	 * @return [type] [description]
	 */
	public function render_page_content() {

		$template_id = $this->get_template_id();

		if ( ! $template_id ) {
			return;
		}

		jet_engine()->admin_bar->register_post_item( $template_id );

		$settings = Module::instance()->settings->get();
		$template_mode = Module::instance()->settings->get( 'template_mode' );

		if ( 'rewrite' === $template_mode && ! empty( $settings['force_template_rewrite'] ) ) {

			global $post;

			if ( $template_id !== get_the_ID() ) {
				$template = get_post( $template_id );
				$tmp      = $post;
				$post     = $template;
			} else {
				$template = $post;
			}

			echo apply_filters( 'the_content', $template->post_content );

			if ( $template_id !== get_the_ID() ) {
				$post = $tmp;
			}

		} else {
			$template = get_post( $template_id );
			echo apply_filters( 'jet-engine/profile-builder/template/content', $template->post_content, $template_id, $this );
		}

	}

	/**
	 * Replace default content
	 * @return [type] [description]
	 */
	public function add_template_filter() {

		/**
		 * $this->template_id set up here. $this->get_template_id() not accessible earlier
		 */

		$settings   = Module::instance()->settings->get();
		$add        = false;
		$structure  = false;
		$has_access = $this->access->check_user_access();
		$subapge    = Module::instance()->query->get_subpage_data();

		if ( ! $has_access['access'] && ! empty( $has_access['template'] ) ) {
			$this->template_id = $has_access['template'];
		} else {

			$this->template_id = ! empty( $subapge['template'] ) ? $subapge['template'][0] : false;

			if ( ! $this->template_id && ! empty( $settings['force_template_rewrite'] ) ) {
				$this->template_id = get_the_ID();
			}
		}

		if ( $has_access['access'] ) {
			$this->has_access = true;
		}

		if ( $this->template_id ) {

			jet_engine()->admin_bar->register_post_item( $this->template_id );

			add_filter( 'template_include', array( $this, 'set_page_template' ), 99999 );
			add_action( 'jet-engine/profile-builder/template/main-content', array( $this, 'render_page_content' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_template_css' ) );
		}

	}

	/**
	 * Rewrite template
	 *
	 * @param [type] $template [description]
	 */
	public function set_page_template( $template ) {

		$template_mode = Module::instance()->settings->get( 'template_mode' );

		if ( 'rewrite' === $template_mode || ! $this->has_access ) {
			$current_template = get_page_template_slug();

			if ( $current_template && 'elementor_canvas' === $current_template ) {
				$template = jet_engine()->get_template( 'profile-builder/page-canvas.php' );
			} else {
				$template = jet_engine()->get_template( 'profile-builder/page.php' );
			}
		}

		return $template;
	}

	/**
	 * Dynamic link URL
	 *
	 * @param  boolean $url      [description]
	 * @param  array   $settings [description]
	 * @return [type]            [description]
	 */
	public function dynamic_link_url( $url = false, $settings = array() ) {

		$link_source = isset( $settings['dynamic_link_source'] ) ? $settings['dynamic_link_source'] : false;

		if ( ! $link_source ) {
			$link_source = isset( $settings['image_link_source'] ) ? $settings['image_link_source'] : false;
		}

		if ( $link_source && 'profile_page' === $link_source && ! empty( $settings['dynamic_link_profile_page'] ) ) {

			$context = ! empty( $settings['object_context'] ) ? $settings['object_context'] : 'default_object';

			/*
			 * Condition changed in v3.0.7 to fix https://github.com/Crocoblock/issues-tracker/issues/1855
			 */
			if ( ! in_array( $context, array( 'default_object' ) ) ) {
				$this->current_user_obj = jet_engine()->listings->data->get_object_by_context( $context );
			}

			$profile_page = $settings['dynamic_link_profile_page'];
			$profile_page = explode( '::', $profile_page );

			if ( 1 < count( $profile_page ) ) {
				$this->maybe_set_user_obj_by_context();

				$url = Module::instance()->settings->get_subpage_url( $profile_page[1], $profile_page[0] );

				$this->maybe_reset_user_obj_by_context();
			}

		}

		return $url;
	}

	public function maybe_set_user_obj_by_context() {

		if ( ! $this->current_user_obj ) {
			return;
		}

		add_filter( 'jet-engine/profile-builder/query/pre-get-queried-user', array( $this, 'set_user_obj_by_context' ) );

	}

	public function maybe_reset_user_obj_by_context() {

		if ( ! $this->current_user_obj ) {
			return;
		}

		$this->current_user_obj = null;

		remove_filter( 'jet-engine/profile-builder/query/pre-get-queried-user', array( $this, 'set_user_obj_by_context' ) );

	}

	/**
	 * Set user object by context.
	 *
	 * @param  $user
	 * @return bool|mixed
	 */
	public function set_user_obj_by_context( $user ) {

		if ( $this->current_user_obj ) {
			$user = $this->current_user_obj;
		}

		return $user;
	}


	/**
	 * Render profile menu
	 *
	 * @param  array  $settings [description]
	 * @return [type]           [description]
	 */
	public function profile_menu( $args = array(), $echo = true ) {

		$menu = $this->menu->get_profile_menu( $args );

		if ( $echo ) {
			echo $menu;
		} else {
			return $menu;
		}

	}

	public function set_document_title_on_single_user_page( $title ) {

		if ( ! Module::instance()->query->is_single_user_page() ) {
			return $title;
		}

		if ( null !== $this->user_page_title ) {
			return $this->user_page_title;
		}

		$user_page_title = Module::instance()->settings->get( 'user_page_seo_title' );

		if ( empty( $user_page_title ) ) {
			return $title;
		}

		$title_macros = $this->get_user_page_title_macros();

		$user_page_title = preg_replace_callback(
			'/%([a-z0-9_-]+)%/',
			function( $matches ) use ( $title_macros ) {

				$found = $matches[1];

				if ( ! isset( $title_macros[ $found ] ) ) {
					return $matches[0];
				}

				if ( ! isset( $title_macros[ $found ]['cb'] ) ) {
					return $matches[0];
				}

				$cb = $title_macros[ $found ]['cb'];

				if ( ! is_callable( $cb ) ) {
					return $matches[0];
				}

				$result = call_user_func( $cb );

				if ( is_array( $result ) ) {
					$result = implode( ',', $result );
				}

				return $result;

			}, $user_page_title
		);

		$user_page_title = wp_strip_all_tags( stripslashes( $user_page_title ), true );
		$user_page_title = esc_html( $user_page_title );

		$this->user_page_title = $user_page_title;

		return $this->user_page_title;
	}

	public function get_user_page_title_macros() {
		return apply_filters( 'jet-engine/profile-builder/user-page-title/macros', array(
			'username' => array(
				'label' => esc_html__( 'User Name', 'jet-engine' ),
				'cb'    => function() {
					$user = Module::instance()->query->get_queried_user();
					return $user ? $user->display_name : null;
				},
			),
			'pagetitle' => array(
				'label' => esc_html__( 'Page Title', 'jet-engine' ),
				'cb'    => function() {
					return single_post_title( '', false );
				},
			),
			'subpagetitle' => array(
				'label' => esc_html__( 'Subpage Title', 'jet-engine' ),
				'cb'    => function() {
					$subpage_data = Module::instance()->query->get_subpage_data();
					return ! empty( $subpage_data['title'] ) ? $subpage_data['title'] : '';
				},
			),
			'sep' => array(
				'label' => esc_html__( 'Separator', 'jet-engine' ),
				'cb'    => function() {
					return apply_filters( 'document_title_separator', '-' );
				},
			),
			'sitename' => array(
				'label' => esc_html__( 'Site Name', 'jet-engine' ),
				'cb'    => function() {
					return get_bloginfo( 'name', 'display' );
				},
			),
		) );
	}

	public function modify_canonical_url( $canonical_url ) {

		$page = get_query_var( Module::instance()->rewrite->page_var );

		if ( ! $page ) {
			return $canonical_url;
		}

		if ( 'single_user_page' !== $page ) {
			return $canonical_url;
		}

		$subpage = get_query_var( Module::instance()->rewrite->subpage_var );
		$user    = get_query_var( Module::instance()->rewrite->user_var );

		if ( $user ) {
			$canonical_url = $canonical_url . user_trailingslashit( $user, 'single_user' );
		}

		if ( $subpage ) {
			$canonical_url = $canonical_url . user_trailingslashit( $subpage, 'single_user' );
		}

		return $canonical_url;
	}

}
