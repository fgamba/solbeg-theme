<?php
/**
 * solbeg functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package solbeg
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function solbeg_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on solbeg, use a find and replace
		* to change 'solbeg' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'solbeg', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'solbeg' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'solbeg_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'solbeg_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function solbeg_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'solbeg_content_width', 640 );
}
add_action( 'after_setup_theme', 'solbeg_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function solbeg_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'solbeg' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'solbeg' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'solbeg_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function solbeg_scripts() {
	wp_enqueue_style( 'solbeg-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'solbeg-style', 'rtl', 'replace' );

	wp_enqueue_script( 'solbeg-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	wp_enqueue_script( 'solbeg-utils', get_template_directory_uri() . '/js/utils.js', array(), _S_VERSION, true );

	wp_localize_script('solbeg-utils', 'utils_ajax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
    ));

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'solbeg_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

if (function_exists('add_image_size')) {
    add_image_size('custom-thumbnail', 300, 200, true);
}

function custom_post_thumbnail($size = 'custom-thumbnail', $attr = '') {
    if (has_post_thumbnail()) {
        $attr .= 'style="max-width: 300px; height: 200px;"'; // Optional: Add custom CSS styles to control the thumbnail size

        $image = wp_get_attachment_image_src(get_post_thumbnail_id(), $size);
        if ($image) {
            echo '<img src="' . esc_url($image[0]) . '" alt="' . esc_attr(get_the_title()) . '" ' . $attr . ' />';
        }
    }
}

add_action('wp_ajax_get_posts_by_category', 'get_posts_by_category');
add_action('wp_ajax_nopriv_get_posts_by_category', 'get_posts_by_category');

function get_posts_by_category () {
	$cat_id = $_POST['cat_id'];
	$args = array(
		'category' => $cat_id,
		'post_status' => 'publish'
	);
	$posts = get_posts($args);
	
	$html = '';
	if($posts) {
		foreach ($posts as $post) {
			$html .= '<article id="' . $post->ID . '" class="post type-post status-publish format-standard has-post-thumbnail hentry">';
			$html .= '<header class="entry-header"><h2 class="entry-title"><a href="'.esc_url(get_permalink($post->ID)).'" rel="bookmark">'.$post->post_title.'</a></h2>';
			$html .= '<img src="'.get_the_post_thumbnail_url($post->ID).'" style="max-width:300px; height:200px;"/></header>';
			$html .= '<div class="entry-content">'.$post->post_content.'</div>';
			$html .= '</article>';
		}
	}
	$response = array(
        'message' => 'Success!',
		'result'   => $html
    );

    // Return the response as JSON
    wp_send_json($response);
}