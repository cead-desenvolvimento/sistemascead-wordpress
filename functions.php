<?php
/*
Require para Arquivo do Customizer

Permite ao usuário editar manualmente informações como endereço e copyright
sem precisar fazer modificações no código
*/
require get_template_directory(). '/customizer.php';

function wpb_imagelink_setup() {
    $image_set = get_option( 'image_default_link_type' );

    if ($image_set !== 'none') {
        update_option('image_default_link_type', 'none');
    }
}
add_action('admin_init', 'wpb_imagelink_setup', 10);

/* ------------------------------------------------
	Custom Login Logo
------------------------------------------------ */

function custom_login_logo() {
	echo "<style type=\"text/css\">
		body.login div#login h1 a {
		background-image: url(".get_bloginfo('template_directory')."/images/logo-cead.png);
		-webkit-background-size: auto;
		background-size: auto;
		margin: 0 0 25px;
		width: 320px;
		}
		</style>";
}
add_action( 'login_enqueue_scripts', 'custom_login_logo' );

//Link na tela de login para a página inicial
function my_login_logo_url() {
    return get_bloginfo( 'url' );
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return 'Cead UFJF - Início';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );


/* ------------------------------------------------
	Theme Setup
------------------------------------------------ */

if ( ! isset( $content_width ) ) $content_width = 640;

add_action( 'after_setup_theme', 'qns_setup' );

if ( ! function_exists( 'qns_setup' ) ):

function qns_setup() {

	add_theme_support ( 'custom-logo', 
		array(
			'height' => 90,
			'width' => 516,
			'header-text' => array( 'site-title', 'site-description' )
		) );

	/*A linha abaixo adiciona a tag title no código.
	O correto é ela ser adicionada aqui e não no header.php*/
	add_theme_support('title-tag');	

	add_theme_support( 'post-thumbnails' );

	if ( function_exists( 'add_theme_support' ) ) {
		add_theme_support( 'post-thumbnails' );
	        set_post_thumbnail_size( "100", "100" );
	}

	if ( function_exists( 'add_image_size' ) ) {
		add_image_size( 'image-style1', 330, 220, true );
		// add_image_size( 'image-style2', 73, 52, true );
		// add_image_size( 'image-style3', 105, 105, true );
		// add_image_size( 'image-style4', 212, 212, true );
		// add_image_size( 'image-style5', 75, 75, true );
		add_image_size( 'image-style6', 450, 300, true );
		add_image_size( 'image-style7', 940, 450, true );
		// add_image_size( 'image-style8', 56, 56, true );
		// add_image_size( 'image-style9', 69, 69, true );
		// add_image_size( 'image-style10', 520, 265, true );
		// add_image_size( 'image-style11', 145, 145, true );
		add_image_size( 'image-style-thumb', 335, 241, true );
		add_image_size( 'image-style-card', 380, 253, false );
		add_image_size( 'image-style-post', 996, 508, true );
	}

	add_theme_support( 'automatic-feed-links' );

	load_theme_textdomain( 'qns', get_template_directory() . '/languages' );

	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	if ( is_readable( $locale_file ) ) require_once( $locale_file );

	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'qns' ),
	) );

}
endif;


/* ------------------------------------------------
	Favicon
------------------------------------------------ */

function add_favicon() {
    echo '<link rel="shortcut icon" type="image/png" href="'.get_template_directory_uri().'/img/favicon.ico" />';
}

add_action('wp_head', 'add_favicon');



/* ------------------------------------------------
	Comments Template
------------------------------------------------ */

if( ! function_exists( 'qns_comments' ) ) {
	function qns_comments($comment, $args, $depth) {
	   $path = get_template_directory_uri();
	   $GLOBALS['comment'] = $comment;
	   ?>

	<li <?php comment_class('comment-entry clearfix'); ?> id="comment-<?php comment_ID(); ?>">

		<!-- BEGIN .comment-left -->
		<div class="comment-left">
			<div class="comment-image">
				<?php echo get_avatar( $comment, 60 ); ?>
			</div>
		<!-- END .comment-left -->
		</div>

		<!-- BEGIN .comment-right -->
		<div class="comment-right">

			<p class="comment-info"><?php printf( __( '%s', 'qns' ), sprintf( '%s', get_comment_author_link() ) ); ?>
				<span><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
				<?php printf( __( '%1$s at %2$s', 'qns' ), get_comment_date(),  get_comment_time() ); ?>
				</a></span>
			</p>

			<div class="comment-text">
				<?php if ( $comment->comment_approved == '0' ) : ?>
					<p class="comment-moderation"><?php _e( 'Your comment is awaiting moderation.', 'qns' ); ?></p>
				<?php endif; ?>
				<?php comment_text(); ?>
			</div>

			<p><span class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
				<?php edit_comment_link( __( '(Edit)', 'qns' ), ' ' ); ?>
			</span></p>

		<!-- END .comment-right -->
		</div>

	<?php }
}



/* ------------------------------------------------
   Options Panel
------------------------------------------------ */

require_once ('admin/index.php');



/* ------------------------------------------------
	Register Sidebars
------------------------------------------------ */

function qns_widgets_init() {

	// Area 1
	register_sidebar( array(
		'name' => __( 'Standard Page Sidebar', 'qns' ),
		'id' => 'primary-widget-area',
		'description' => __( 'Displayed in the sidebar of all pages except the homepage', 'qns' ),
		'before_widget' => '<div class="widget content-block">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="block-title">',
		'after_title' => '</h3>',
	) );

	// Area 2
	register_sidebar( array(
		'name' => __( 'Homepage Left', 'qns' ),
		'id' => 'homepage-left-sidebar',
		'description' => __( 'Displayed on the left side of the homepage', 'qns' ),
		'before_widget' => '<div class="widget content-block">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="block-title">',
		'after_title' => '</h3>',
	) );

	// Area 3
	register_sidebar( array(
		'name' => __( 'Homepage Right', 'qns' ),
		'id' => 'homepage-right-sidebar',
		'description' => __( 'Displayed on the right side of the homepage', 'qns' ),
		'before_widget' => '<div class="widget content-block">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="block-title">',
		'after_title' => '</h3>',
	) );

	// Area 4
	register_sidebar( array(
		'name' => __( 'Homepage Center', 'qns' ),
		'id' => 'homepage-center-block',
		'description' => __( 'Displayed in the center of the homepage', 'qns' ),
		'before_widget' => '<div class="widget content-block">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="block-title">',
		'after_title' => '</h3>',
	) );

	// Area 5
	register_sidebar( array(
		'name' => __( 'Footer', 'qns' ),
		'id' => 'footer-widget-area',
		'description' => __( 'Displayed at the bottom of all pages', 'qns' ),
		'before_widget' => '<li class="col"><div class="widget">',
		'after_widget' => '</div></li>',
		'before_title' => '<div class="widget-title clearfix"><h4>',
		'after_title' => '</h4><div class="widget-title-block"></div></div>',
	) );

}

add_action( 'widgets_init', 'qns_widgets_init' );



/* ------------------------------------------------
	Register Menu
------------------------------------------------ */

if( !function_exists( 'qns_register_menu' ) ) {
	function qns_register_menu() {

		register_nav_menus(
		    array(
				'primary' => __( 'Primary Navigation','qns' ),
				'secondary' => __( 'Top Left Navigation','qns' )
		    )
		  );

	}

	add_action('init', 'qns_register_menu');
}



/* ------------------------------------------------
	Add Description Field to Menu
------------------------------------------------ */

class description_walker extends Walker_Nav_Menu
{
      // function start_el(&$output, $item, $depth, $args)
      function start_el(&$output, $item, $depth = 0, $args = Array(), $id = 0)
      {
           global $wp_query;
           $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

           $class_names = $value = '';

           $classes = empty( $item->classes ) ? array() : (array) $item->classes;

           $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
           $class_names = ' class="'. esc_attr( $class_names ) . '"';

           $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

           $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
           $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
           $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
           $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

           $prepend = '<strong>';
           $append = '</strong>';
           $description  = ! empty( $item->description ) ? '<span>'.esc_attr( $item->description ).'</span>' : '';

           if($depth != 0) {
				$description = $append = $prepend = "";
           }

            $item_output = $args->before;
            $item_output .= '<a'. $attributes .'>';
            $item_output .= $args->link_before .$prepend.apply_filters( 'the_title', $item->title, $item->ID ).$append;
            $item_output .= $description.$args->link_after;
            $item_output .= '</a>';
            $item_output .= $args->after;

            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
}



/* ------------------------------------------------
	Get Post Type
------------------------------------------------ */

function is_post_type($type){
    global $wp_query;
    if($type == get_post_type($wp_query->post->ID)) return true;
    return false;
}



/* ------------------------------------------------
   Register Dependant Javascript Files
------------------------------------------------ */

add_action('wp_enqueue_scripts', 'qns_load_js');

if( ! function_exists( 'qns_load_js' ) ) {
	function qns_load_js() {

		if ( is_admin() ) {

		}

		else {

			// Load JS
			wp_register_script( 'jquery_211', get_bloginfo('template_directory').'/js/jquery-2.1.1.min.js', array(), '2.1.1', true );
			wp_register_script( 'jquery_ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js', array(), '1.8', true );
			wp_register_script( 'jq_uniform', get_template_directory_uri() . '/js/jquery.uniform.js', array(), '1.4.8', true );
			wp_register_script( 'materialize', get_bloginfo('template_directory').'/js/materialize.js', array(), '1.0', true );
			wp_register_script( 'init', get_bloginfo('template_directory').'/js/init.js', array(), '1.2.12', true );
			wp_register_script( 'toggle_videos', get_bloginfo('template_directory').'/js/toggle_videos.js', array(), '1.1', true );
			wp_register_script( 'library_pagination', get_bloginfo('template_directory').'/js/library-pagination.js?v=1.0', array(), '1.0', true );
			// wp_register_script( 'tinynav', get_template_directory_uri() . '/js/tinynav.min.js', array( 'jquery' ), '1.4.8', true );
			// wp_register_script( 'superfish', get_template_directory_uri() . '/js/superfish.js', array( 'jquery' ), '1.4.8', true );
			// wp_register_script( 'prettyphoto', get_template_directory_uri() . '/js/jquery.prettyPhoto.js', array( 'jquery' ), '1.1.9', true );
			// wp_register_script( 'flexslider', get_template_directory_uri() . '/js/jquery.flexslider-min.js', array( 'jquery' ), '1.1.9', true );
			// wp_register_script( 'selectivizr', get_template_directory_uri() . '/js/selectivizr-min.js', array( 'jquery' ), '1.0.2', true );
			wp_register_script( 'custom', get_template_directory_uri() . '/js/scripts.js', array(), '1.1.1', true );
			wp_register_script( 'barra_brasil', '//barra.brasil.gov.br/barra_2.0.js', array(), '2.0', true );

			// wp_enqueue_script( array( 'jquery_ui', 'tinynav', 'uniform', 'superfish', 'prettyphoto', 'flexslider', 'custom' ) );
			wp_enqueue_script( array( 	'jquery_211',
										'jquery_ui',
										'jq_uniform',
										'materialize',
										'init',
										'toggle_videos',
										'library_pagination',
										'custom',
										'barra_brasil'
										 ) );

			global $is_IE;

			//if( $is_IE ) wp_enqueue_script( 'selectivizr' );
			if( is_single() ) wp_enqueue_script( 'comment-reply' );

			// Load CSS
			// wp_enqueue_style('parkcollege-style', get_bloginfo('stylesheet_url'));
			// wp_enqueue_style('superfish', get_template_directory_uri() .'/css/superfish.css');
			// wp_enqueue_style('prettyPhoto', get_template_directory_uri() .'/css/prettyPhoto.css');
			// wp_enqueue_style('flexslider', get_template_directory_uri() .'/css/flexslider.css');
			// wp_enqueue_style('responsive', get_template_directory_uri() .'/css/responsive.css');
			// wp_enqueue_style('colour', get_template_directory_uri() .'/css/colour.css');
			// wp_enqueue_style('superfish', get_template_directory_uri() .'/css/superfish.css');


		}
	}
}

// De-register jQuery from Contact Form 7

add_action( 'wp_print_scripts', 'deregister_contactform', 100 );
	function deregister_contactform() {
	wp_deregister_script( 'contact-form-7' );
}

if( !function_exists( 'custom_js' ) ) {

    function custom_js() {

		global $data; //fetch options stored in $data

    }

}



/* ------------------------------------------------
   Enqueue Google Fonts
------------------------------------------------ */

add_action( 'wp_enqueue_scripts', 'qns_fonts' );

function qns_fonts() {
	$protocol = is_ssl() ? 'https' : 'http';
	wp_enqueue_style( 'qns-opensans', "$protocol://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic" );

	global $data; //fetch options stored in $data

	if ( !$data['custom_font_code'] ) {
		wp_enqueue_style( 'qns-merriweather', "$protocol://fonts.googleapis.com/css?family=Merriweather:400,300,700,900' rel='stylesheet" );
	} else {
		echo $data['custom_font_code'];
	}

}



add_action('wp_footer', 'custom_js');

function admin_style() {
	wp_enqueue_style('admin-css', get_template_directory_uri().'/css/admin.css');
}

add_action("admin_head", 'admin_style');


/* ------------------------------------------------
   Load Files
------------------------------------------------ */

// Post Types
include 'functions/post-types/courses.php';
include 'functions/post-types/editais.php';
// include 'functions/post-types/events.php';
include 'functions/post-types/page.php';
include 'functions/post-types/portfolio.php';
// include 'functions/post-types/teachers.php';

// Shortcodes
include 'functions/shortcodes/accordion.php';
include 'functions/shortcodes/button.php';
include 'functions/shortcodes/columns.php';
include 'functions/shortcodes/dropcap.php';
include 'functions/shortcodes/gallery.php';
include 'functions/shortcodes/googlemap.php';
include 'functions/shortcodes/list.php';
include 'functions/shortcodes/message.php';
include 'functions/shortcodes/tabs.php';
include 'functions/shortcodes/title.php';
include 'functions/shortcodes/toggle.php';
include 'functions/shortcodes/toggle_faq.php';
include 'functions/shortcodes/toggle_videos.php';
include 'functions/shortcodes/video.php';
include 'functions/shortcodes/widget-slider.php';
include 'functions/shortcodes/arearestrita.php';
include 'functions/shortcodes/linksuteis.php';
include 'functions/shortcodes/mapasite.php';
include 'functions/shortcodes/formcontato.php';

// Widgets
// include 'functions/widgets/widget-blog-slider.php';
// include 'functions/widgets/widget-category-posts.php';
// include 'functions/widgets/widget-contact.php';
// include 'functions/widgets/widget-course-finder.php';
// include 'functions/widgets/widget-editais.php';
// include 'functions/widgets/widget-events.php';
// include 'functions/widgets/widget-flickr.php';
// include 'functions/widgets/widget-map.php';
// include 'functions/widgets/widget-recent-posts.php';
// include 'functions/widgets/widget-shortcodes.php';
// include 'functions/widgets/widget-video.php';
include 'functions/widgets/widget-banner.php';



/* ------------------------------------------------
	Custom CSS
------------------------------------------------ */

function custom_css() {

	global $data; //fetch options stored in $data

	// Set Font Family
	if ( !$data['custom_font'] ) {
		$custom_font = "'Merriweather', serif";
	} else {
		$custom_font =  $data['custom_font'];
	}

	// Output Custom CSS
	$output = '<style type="text/css">

	h1, h2, h3, h4, h5, h6, .slides .flex-caption p, .page-content blockquote, .event-full .event-info h4, .page-content table th, #cancel-comment-reply-link {
	font-family: ' . $custom_font . ' !important;}';

	if ( $data['main_colorrgba'] ) {
		$output .= '.slider .slides .flex-caption p {
			background: ' . $data['main_colorrgba'] . ' !important;
		}';
	}

	if ( $data['body_background'] and $data['body_background_image'] ) {

		if ( $data['background_repeat'] ) {
			$background_repeat = $data['background_repeat'];
		}
		else {
			$background_repeat = 'repeat';
		}

		$output .= 'body {
			background: url(' . $data['body_background_image'] . ') ' . $data['body_background'] . ' ' . $data['background_repeat'] . ' !important;
		}';
	}

	elseif ( $data['body_background'] ) {
		$output .= 'body {
			background: ' . $data['body_background'] . ' !important;
		}';
	}

	elseif ( $data['body_background_image'] ) {
		$output .= 'body {
			background: url(' . $data['body_background_image'] . ') fixed ' . $data['background_repeat'] . ' !important;
		}';
	}

	if ( $data['main_colour'] ) {

		$output .= '#header-top,
		.block-title,
		#reply-title,
		.page-content table th,
		.block-title,
		.event-m,
		.pagination-wrapper .selected,
		.pagination-wrapper a:hover,
		.wp-pagenavi .current,
		.wp-pagenavi a:hover,
		.teacher-4 li h3.teacher-title,
		.blog-entry .blog-info .blog-date,
		.page-title .page-title-block,
		.title-block {
			background: ' . $data['main_colour'] . ' !important;
		}';

		$output .= '#logo h1 span,
		table tr a,
		.event-info h4 a,
		.news-content h4 a,
		.blog-entry .blog-info .blog-date,
		.blog-entry .blog-content h3 a,
		.sidebar-right #twitter_update_list li a,
		.sidebar-left #twitter_update_list li a,
		#footer .widget .latest-posts-list li .lpl-content h6 span,
		.widget .latest-posts-list li .lpl-content h6 a,
		.title1 h4 a,
		.tp_recent_tweets .twitter_time,
		.tp_recent_tweets a {
			color: ' . $data['main_colour'] . ' !important;
		}';

		$output .= '#main-menu li.current_page_item,
		#main-menu li:hover,
		.slider .flex-direction-nav .flex-prev,
		.slider .flex-direction-nav .flex-next,
		.page-content blockquote,
		.ui-tabs .ui-tabs-nav li.ui-tabs-selected,
		.pagination-wrapper .selected,
		.pagination-wrapper a:hover,
		.wp-pagenavi .current,
		.wp-pagenavi a:hover,
		.page-slider .flex-direction-nav .flex-prev,
		.page-slider .flex-direction-nav .flex-next {
			border-color: ' . $data['main_colour'] . ' !important;
		}';

		$output .= '.slider .flex-direction-nav .flex-prev,
		.slider .flex-direction-nav .flex-next,
		.twitter-icon:hover,
		.facebook-icon:hover,
		.gplus-icon:hover,
		.pinterest-icon:hover,
		.flickr-icon:hover,
		.youtube-icon:hover,
		.vimeo-icon:hover,
		.skype-icon:hover,
		.rss-icon:hover,
		.course-finder-full .course-finder-icon,
		.page-slider .flex-direction-nav .flex-prev,
		.page-slider .flex-direction-nav .flex-next {
			background-color: ' . $data['main_colour'] . ' !important;
		}';

	}

	if ( $data['footer_background_colour'] ) {

		$output .= '#footer-wrapper {
			background: ' . $data['footer_background_colour'] . ' !important;
		}';

	}

	if ( $data['footer_highlight_colour'] ) {

		$output .= '#footer-bottom, #footer .tagcloud a, #footer .widget-title-block, .course-finder-full-form {
			background: ' . $data['footer_highlight_colour'] . ' !important;
		}';

	}

	if ( $data['footer_link_colour'] ) {

		$output .= '#footer #twitter_update_list li a, #footer-bottom p, #footer .tp_recent_tweets .twitter_time, #footer .tp_recent_tweets a {
			color: ' . $data['footer_link_colour'] . ' !important;
		}';

	}

	if ($data['display_homepage_blocks'] == '0') {

		$output .= '.content-wrapper {
			margin: 0 auto 0 auto !important;
		}';

	}

	if ($data['top_bar_hover_colour']) {

		$output .= '.top-left-nav li span, #header-top a:hover {
			color: ' . $data['top_bar_hover_colour'] . ' !important;
		}';

	}

	if ($data['slideshow_boxed']) {

		$output .= '.slider {
			width: 1000px;
			margin: 0 auto;
		}';

	}

	if ($data['slideshow_height']) {

		$output .= '.slider .slides li {
			max-height: ' . $data['slideshow_height'] . 'px;
		}';

		$output .= '.loading .slide-loader {
	 		min-height: ' . $data['slideshow_height'] . 'px;
		}';

	} else {

		$output .= '.loading .slide-loader {
	 		min-height: 600px;
		}';

	}

	if ($data['slideshow_header_overlay']) {

		$output .= '#header-wrapper {
	 		position: relative;
		}';

	} else {

		$output .= '#header-wrapper {
		 	position: absolute;
		}';

	}

	$output .= $data['custom_css'];
	$output .= '</style>';

	return $output;

}



/* -------------------------------------------------------
	Remove width / height attributes from gallery images
------------------------------------------------------- */

add_filter('wp_get_attachment_link', 'remove_img_width_height', 10, 1);
add_filter('wp_get_attachment_image_attributes', 'remove_img_width_height', 10, 1);

function remove_img_width_height($html) {
    $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
    return $html;
}



/* ------------------------------------------------
	Remove rel attribute from the category list
------------------------------------------------ */

function remove_category_list_rel($output)
{
  $output = str_replace(' rel="category"', '', $output);
  return $output;
}
add_filter('wp_list_categories', 'remove_category_list_rel');
add_filter('the_category', 'remove_category_list_rel');



/* -----------------------------------------------------
	Remove <p> / <br> tags from nested shortcode tags
----------------------------------------------------- */

add_filter('the_content', 'shortcode_fix');
function shortcode_fix($content)
{
    $array = array (
        '<p>[' => '[',
        ']</p>' => ']',
        ']<br />' => ']'
    );

    $content = strtr($content, $array);

	return $content;
}



/* ------------------------------------------------
	Excerpt Length
------------------------------------------------ */
function print_excerpt($length) {
	echo get_excerpt_($length);

	/*$content = get_the_content();
	$content = strip_shortcodes($content);
	$content = strip_tags($content);

	$dot = ".";
	// posicao do ultimo ponto antes de length
	$temp = substr($content, 0, $length);
	$ponto_antes = strrpos($temp, $dot);

	// posicao do primeiro ponto depois de length
	$temp2 = substr($content, $length);
	$ponto_depois = strpos($temp2, $dot);

	if ($ponto_antes && (($length - $ponto_antes) < ($ponto_depois - $length))) {
		$excerpt = substr($content, 0, $ponto_antes);
	} else if ($ponto_depois) {
		$ponto_depois = $ponto_depois + $length;
		$excerpt = substr($content, 0, $ponto_depois);
	} else {
		$excerpt = $content;
	}

	echo $excerpt . ". <a href=" . get_the_permalink() . ">Leia mais</a><br>";*/
}
function get_excerpt_($var) {
	if (is_int($var)) {
		$length = $var;
		$post = get_post();
	} else {
		$post = $var;
		$length = 100;
	}

	$content = $post->post_content;
	$content = strip_shortcodes($content);
	$content = strip_tags($content);

	$dot = ".";
	// posicao do ultimo ponto antes de length
	$temp = substr($content, 0, $length);
	$ponto_antes = strrpos($temp, $dot);

	// posicao do primeiro ponto depois de length
	$temp2 = substr($content, $length);
	$ponto_depois = strpos($temp2, $dot);

	if ($ponto_antes && (($length - $ponto_antes) < ($ponto_depois - $length))) {
		$excerpt = substr($content, 0, $ponto_antes);
	} else if ($ponto_depois) {
		$ponto_depois = $ponto_depois + $length;
		$excerpt = substr($content, 0, $ponto_depois);
	} else {
		$excerpt = $content;
	}

	$text = $excerpt . ". <a href=" . get_permalink($post->ID) . ">Leia mais</a><br>";
	return $text;
}
/*function reverse_strrchr($haystack, $needle, $trail) {
    return strrpos($haystack, $needle) ? substr($haystack, 0, strrpos($haystack, $needle) + $trail) : false;
}*/


function get_excerpt_new($var) {
	if (is_int($var)) {
		$length = $var;
		$post = get_post();
	} else {
		$post = $var;
		$length = 100;
	}

	$content = $post->post_content;
	$content = strip_shortcodes($content);
	$content = strip_tags($content);

	$dot = ".";
	// posicao do ultimo ponto antes de length
	$temp = substr($content, 0, $length);
	$ponto_antes = strrpos($temp, $dot);

	// posicao do primeiro ponto depois de length
	$temp2 = substr($content, $length);
	$ponto_depois = strpos($temp2, $dot);

	if ($ponto_antes && (($length - $ponto_antes) < ($ponto_depois - $length))) {
		$excerpt = substr($content, 0, $ponto_antes);
		$excerpt .= ".";
	// } else if ($ponto_depois) {
	// 	$ponto_depois = $ponto_depois + $length;
	// 	$excerpt = substr($content, 0, $ponto_depois);
	// } else {
	// 	$excerpt = $content;
	// }
	}else{
		while(strlen($content) >= $length && substr($content,$length,1) != ' '){			
		// while(	substr($content,$length,1) == '.' || substr($content,$length,1) == ',' || substr($content,$length,1) == ';' ||
		// 		substr($content,$length,1) == '-' || substr($content,$length,1) == '!' || substr($content,$length,1) == '?' ||
		// 		substr($content,$length,1) == ':' ||
		// 		(substr($content,$length,1) >= 'a' && substr($content,$length,1) <= 'z') ||
		// 	    (substr($content,$length,1) >= 'A' && substr($content,$length,1) <= 'Z') ||
		// 		(substr($content,$length,1) >= '0' && substr($content,$length,1) <= '9')){			
			$length += 1;
			// echo substr($content,$length,1);
		}		
		$excerpt = substr($content, 0, $length);
		if(strlen($content) < $length)
			$excerpt .= "...";
	}

	$text = $excerpt ;//. '<br><p class="button-link"><a href=' . get_permalink($post->ID) . '>Leia mais</a></p><br>';
	return $text;
}


/* ------------------------------------------------
	Excerpt More Link
------------------------------------------------ */

function qns_continue_reading_link() {
		return '';
}

function qns_auto_excerpt_more( $more ) {
	return qns_continue_reading_link();
}
add_filter( 'excerpt_more', 'qns_auto_excerpt_more' );



/* ------------------------------------------------
	The Title
------------------------------------------------ */

function qns_filter_wp_title( $title, $separator ) {

	if ( is_feed() )
		return $title;

	global $paged, $page;

	if ( is_search() ) {
		$title = sprintf( __( 'Search results for %s', 'qns' ), '"' . get_search_query() . '"' );
		if ( $paged >= 2 )
			$title .= " $separator " . sprintf( __( 'Page %s', 'qns' ), $paged );
		//$title .= " $separator " . home_url( 'name', 'display' );
		return $title;
	}

	$title .= get_bloginfo( 'name', 'display' );

	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $separator " . $site_description;

	if ( $paged >= 2 || $page >= 2 )
		$title .= " $separator " . sprintf( __( 'Page %s', 'qns' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'qns_filter_wp_title', 10, 2 );



/* ------------------------------------------------
	Content ID/Class
------------------------------------------------ */

function content_id_class( $position ) {

	global $data; //fetch options stored in $data

	if (array_key_exists('sidebar_position', $data)) {

		$position = $data['sidebar_position'];

		if ( $position == 'left' ) {
			$output = 'class="main-content-right page-content"';
		} elseif ( $position == 'right' ) {
			$output = 'class="main-content page-content"';
		} elseif ( $position == 'none' ) {
			$output = 'class="main-content-full page-content"';
		}

	}

	else {
		$output = 'class="main-content page-content"';
	}

	return $output;

}



/* ------------------------------------------------
	Sidebar Position
------------------------------------------------ */

function sidebar_position( $position ) {

	global $data; //fetch options stored in $data

	if ( $data['sidebar_position'] ) {

		$position = $data['sidebar_position'];

		if ( $position == 'left' ) {
			$output = '<div class="sidebar-left page-content">';
			//$output .= get_sidebar();
			$output .= '</div>';
		} elseif ( $position == 'right' ) {
			$output = '<div class="sidebar-right page-content">';
			//$output .= get_sidebar();
			$output .= '</div>';
		} elseif ( $position == 'none' ) {
			$output = '';
		}

	}

	else {
		$output = '<div class="sidebar-right page-content">';
		//$output .= get_sidebar();
		$output .= '</div>';
	}

	return $output;

}



/* ------------------------------------------------
	Main Menu Fallback
------------------------------------------------ */

function wp_page_menu_qns( $args = array() ) {
	$defaults = array('sort_column' => 'menu_order, post_title', 'menu_class' => 'menu', 'echo' => true, 'link_before' => '', 'link_after' => '');
	$args = wp_parse_args( $args, $defaults );
	$args = apply_filters( 'wp_page_menu_qns_args', $args );

	$menu = '';

	$list_args = $args;

	// Show Home in the menu
	if ( ! empty($args['show_home']) ) {
		if ( true === $args['show_home'] || '1' === $args['show_home'] || 1 === $args['show_home'] )
			$text = __('Home','qns');
		else
			$text = $args['show_home'];
		$class = '';
		if ( is_front_page() && !is_paged() )
			$class = 'class="current_page_item"';
		$menu .= '<li ' . $class . '><a href="' . home_url( '/' ) . '" title="' . esc_attr($text) . '">' . $args['link_before'] . $text . $args['link_after'] . '</a></li>';
		// If the front page is a page, add it to the exclude list
		if (get_option('show_on_front') == 'page') {
			if ( !empty( $list_args['exclude'] ) ) {
				$list_args['exclude'] .= ',';
			} else {
				$list_args['exclude'] = '';
			}
			$list_args['exclude'] .= get_option('page_on_front');
		}
	}

	$list_args['echo'] = false;
	$list_args['title_li'] = '';
	$menu .= str_replace( array( "\r", "\n", "\t" ), '', wp_list_pages($list_args) );

	if ( $menu )
		$menu = '<ul id="main-menu" class="fl">' . $menu . '</ul>';

	$menu = $menu . "\n";
	$menu = apply_filters( 'wp_page_menu_qns', $menu, $args );
	if ( $args['echo'] )
		echo $menu;
	else
		return $menu;
}



/* ------------------------------------------------
	Mobile Menu Fallback
------------------------------------------------ */

function wp_page_mobile_menu_qns( $args = array() ) {
	$defaults = array('sort_column' => 'menu_order, post_title', 'menu_class' => 'menu', 'echo' => true, 'link_before' => '', 'link_after' => '');
	$args = wp_parse_args( $args, $defaults );
	$args = apply_filters( 'wp_page_mobile_menu_qns_args', $args );

	$menu = '';

	$list_args = $args;

	// Show Home in the menu
	if ( ! empty($args['show_home']) ) {
		if ( true === $args['show_home'] || '1' === $args['show_home'] || 1 === $args['show_home'] )
			$text = __('Home','qns');
		else
			$text = $args['show_home'];
		$class = '';
		if ( is_front_page() && !is_paged() )
			$class = 'class="current_page_item"';
		$menu .= '<li ' . $class . '><a href="' . home_url( '/' ) . '" title="' . esc_attr($text) . '">' . $args['link_before'] . $text . $args['link_after'] . '</a></li>';
		// If the front page is a page, add it to the exclude list
		if (get_option('show_on_front') == 'page') {
			if ( !empty( $list_args['exclude'] ) ) {
				$list_args['exclude'] .= ',';
			} else {
				$list_args['exclude'] = '';
			}
			$list_args['exclude'] .= get_option('page_on_front');
		}
	}

	$list_args['echo'] = false;
	$list_args['title_li'] = '';
	$menu .= str_replace( array( "\r", "\n", "\t" ), '', wp_list_pages($list_args) );

	if ( $menu )
		$menu = '<ul id="mobile-menu" class="fl">' . $menu . '</ul>';

	$menu = $menu . "\n";
	$menu = apply_filters( 'wp_page_mobile_menu_qns', $menu, $args );
	if ( $args['echo'] )
		echo $menu;
	else
		return $menu;
}



/* ------------------------------------------------
	Password Protected Post Form
------------------------------------------------ */

add_filter( 'the_password_form', 'qns_password_form' );

function qns_password_form() {

	global $post;
	$label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );
	$form = '<div class="msg fail clearfix"><p class="nopassword">' . __( 'This post is password protected. To view it please enter your password below', 'qns' ) . '</p></div>
<form class="protected-post-form" action="' . get_option('siteurl') . '/wp-login.php?action=postpass" method="post"><label for="' . $label . '">' . __( 'Password', 'qns' ) . ' </label><input name="post_password" id="' . $label . '" class="text_input" type="password" size="20" /><div class="clearboth"></div><input id="submit" type="submit" value="' . esc_attr__( "Submit" ) . '" name="submit"></form>';
	return $form;

}



/* ------------------------------------------------
	Page Header
------------------------------------------------ */

function page_header( $url ) {

	global $data;

	// If custom page header is set
	if ( $url != '' ) {
		$output = 'style="background:url(' . $url . ');"';
	}

	// If default page header is set and custom header is not set
	elseif ( $data['default_header_url'] && $url == '' ) {
		$output = 'style="background:url(' . $data['default_header_url'] . ');"';
	}

	else {
		$output = '';
	}

	return $output;

}



/* ------------------------------------------------
	Email Validation
------------------------------------------------ */

function valid_email($email) {

	$result = TRUE;

	if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email)) {
    	$result = FALSE;
	}

	return $result;

}



/* ------------------------------------------------
	Add PrettyPhoto for Attached Images
------------------------------------------------ */

add_filter( 'wp_get_attachment_link', 'sant_prettyadd');
function sant_prettyadd ($content) {
     /*$content = preg_replace("/<a/","<a
rel=\"prettyPhoto[slides]\"",$content,1);*/
     return $content;
}



/* ------------------------------------------------
	Social Icons
------------------------------------------------ */

function no_icons() {

	global $data; //fetch options stored in $data

	if( $data['social_twitter'] ) { $twitter = $data['social_twitter']; }
	else { $twitter = ''; }

	if( $data['social_facebook'] ) { $facebook = $data['social_facebook']; }
	else { $facebook = ''; }

	if( $data['social_googleplus'] ) { $googleplus = $data['social_googleplus']; }
	else { $googleplus = ''; }

	if( $data['social_pinterest'] ) { $pinterest = $data['social_pinterest']; }
	else { $pinterest = ''; }

	if( $data['social_flickr'] ) { $flickr = $data['social_flickr']; }
	else { $flickr = ''; }

	if( $data['social_youtube'] ) { $youtube = $data['social_youtube']; }
	else { $youtube = ''; }

	if( $data['social_vimeo'] ) { $vimeo = $data['social_vimeo']; }
	else { $vimeo = ''; }

	if( $data['social_skype'] ) { $skype = $data['social_skype']; }
	else { $skype = ''; }

	if( $data['social_rss'] ) { $rss = $data['social_rss']; }
	else { $rss = ''; }

	if ( $twitter == ''  && $facebook == '' && $googleplus == '' && $pinterest == '' && $flickr == '' && $youtube == '' && $vimeo == '' && $skype == '' && $rss == '') {
		return true;
	}
}

function display_social() {

	global $data; //fetch options stored in $data

	if( $data['social_twitter'] ) { $twitter = $data['social_twitter']; }
	else { $twitter = ''; }

	if( $data['social_facebook'] ) { $facebook = $data['social_facebook']; }
	else { $facebook = ''; }

	if( $data['social_googleplus'] ) { $googleplus = $data['social_googleplus']; }
	else { $googleplus = ''; }

	if( $data['social_pinterest'] ) { $pinterest = $data['social_pinterest']; }
	else { $pinterest = ''; }

	if( $data['social_flickr'] ) { $flickr = $data['social_flickr']; }
	else { $flickr = ''; }

	if( $data['social_youtube'] ) { $youtube = $data['social_youtube']; }
	else { $youtube = ''; }

	if( $data['social_vimeo'] ) { $vimeo = $data['social_vimeo']; }
	else { $vimeo = ''; }

	if( $data['social_skype'] ) { $skype = $data['social_skype']; }
	else { $skype = ''; }

	if( $data['social_rss'] ) { $rss = $data['social_rss']; }
	else { $rss = ''; }


	$output = '';

	if ( no_icons() !== true ) {
		$output .= '<ul class="social-icons clearfix">';
	}

	if( $vimeo !== '' ) {
		//$output .= '<li><a href="' . $vimeo . '" target="_blank"><span class="vimeo-icon"></span></a></li>';
		$output .= '<li><a href="javascript:void(0);"
					onClick=window.open("https://www.cead.ufjf.br/radio.html","Ratting","width=300,height=450,0,status=0,");>
					<span class="vimeo-icon"></span></a></li>';
	}

	if( $skype !== '' ) {
		$output .= '<li><a href="' . $skype . '" target="_blank"><span class="skype-icon"></span></a></li>';
	}

	if( $twitter !== '' ) {
		$output .= '<li><a href="' . $twitter . '" target="_blank"><span class="twitter-icon"></span></a></li>';
	}

	if( $facebook !== '' ) {
		$output .= '<li><a href="' . $facebook . '" target="_blank"><span class="facebook-icon"></span></a></li>';
	}

	if( $googleplus !== '' ) {
		$output .= '<li><a href="' . $googleplus . '" target="_blank"><span class="gplus-icon"></span></a></li>';
	}

	if( $pinterest !== '' ) {
		$output .= '<li><a href="' . $pinterest . '" target="_blank"><span class="pinterest-icon"></span></a></li>';
	}

	if( $flickr !== '' ) {
		$output .= '<li><a href="' . $flickr . '" target="_blank"><span class="flickr-icon"></span></a></li>';
	}

	if( $youtube !== '' ) {
		$output .= '<li><a href="' . $youtube . '" target="_blank"><span class="youtube-icon"></span></a></li>';
	}





	if( $rss !== '' ) {
		$output .= '<li><a href="' . $rss . '" target="_blank"><span class="rss-icon"></span></a></li>';
		/*$output .= '<li><a href="javascript:void(0);"
		onClick=window.open("https://www.cead.ufjf.br/radio_cead.html","Ratting","width=300,height=450,0,status=0,");>
		<span class="rss-icon"></span></a></li>';*/
	}

	if ( no_icons() !== true ) {
		$output .= '</ul>';
	}

	return $output;

}



/* ------------------------------------------------
	Breadcrumbs
------------------------------------------------ */

function dimox_breadcrumbs() {

  /* === OPTIONS === */
  $text['home']     = __('Home','qns'); // text for the 'Home' link
  $text['category'] = '%s'; // text for a category page
  $text['search']   = __('Search Results','qns'); // text for a search results page
  $text['tag']      = __('Posts Tagged "%s"','qns'); // text for a tag page
  $text['author']   = __('Articles Posted by %s','qns'); // text for an author page
  $text['404']      = __('Error 404','qns'); // text for the 404 page

  $showCurrent = 0; // 1 - show current post/page title in breadcrumbs, 0 - don't show
  $showOnHome  = 1; // 1 - show breadcrumbs on the homepage, 0 - don't show
  $delimiter   = '&raquo;'; // delimiter between crumbs
  $before      = ' '; // tag before the current crumb
  $after       = ' '; // tag after the current crumb
  /* === END OF OPTIONS === */

  global $post;
  $homeLink = home_url();
  $linkBefore = ' ';
  $linkAfter = ' ';
  $linkAttr = ' rel="v:url" property="v:title"';
  $link = $linkBefore . '<a' . $linkAttr . ' href="%1$s">%2$s</a>' . $linkAfter;

  if (is_home() || is_front_page()) {

    if ($showOnHome == 1) echo '<div class="breadcrumbs clearfix"><p><a href="' . $homeLink . '">' . $text['home'] . '</a></p></div>';

  } else {

    echo '<div class="breadcrumbs clearfix"><p>' . sprintf($link, $homeLink, $text['home']) . $delimiter;

    if ( is_category() ) {
      $thisCat = get_category(get_query_var('cat'), false);
      if ($thisCat->parent != 0) {
        $cats = get_category_parents($thisCat->parent, TRUE, $delimiter);
        $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
        $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
        echo $cats;
      }
      echo $before . sprintf($text['category'], single_cat_title('', false)) . $after;

    } elseif ( is_search() ) {
      echo $before . sprintf($text['search'], get_search_query()) . $after;

    } elseif ( is_day() ) {
      echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
      echo sprintf($link, get_month_link(get_the_time('Y'),get_the_time('m')), get_the_time('F')) . $delimiter;
      echo $before . get_the_time('d') . $after;

    } elseif ( is_month() ) {
      echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
      echo $before . get_the_time('F') . $after;

    } elseif ( is_year() ) {
      echo $before . get_the_time('Y') . $after;

    } elseif ( is_single() && !is_attachment() ) {

		global $data;

		if ( get_post_type() == 'course' ) {
			$post_type = get_post_type_object(get_post_type());

			if($data['course_page_url'] != '') {
				$slug = $data['course_page_url'];
			} else {
				$slug = 'cursos';
			}

			printf($link, $homeLink . '/' . $slug . '/', $post_type->labels->name);
			if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;
		}

		elseif ( get_post_type() == 'event' ) {
			$post_type = get_post_type_object(get_post_type());

			if($data['event_page_url'] != '') {
				$slug = $data['event_page_url'];
			} else {
				$slug = 'eventos';
			}

			printf($link, $homeLink . '/' . $slug . '/', $post_type->labels->name);
			if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;
		}

		elseif ( get_post_type() == 'edital' ) {
			$post_type = get_post_type_object(get_post_type());

			if($data['edital_page_url'] != '') {
				$slug = $data['edital_page_url'];
			} else {
				$slug = 'editais';
			}

			printf($link, $homeLink . '/' . $slug . '/', $post_type->labels->name);
			if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;
		}

		elseif ( get_post_type() == 'portfolio' ) {
			$post_type = get_post_type_object(get_post_type());

			if($data['portfolio_page_url'] != '') {
				$slug = $data['portfolio_page_url'];
			} else {
				$slug = 'polos';
			}

			printf($link, $homeLink . '/' . $slug . '/', $post_type->labels->name);
			if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;
		}

		elseif ( get_post_type() == 'teacher' ) {
			$post_type = get_post_type_object(get_post_type());

			if($data['teacher_page_url'] != '') {
				$slug = $data['teacher_page_url'];
			} else {
				$slug = 'equipe';
			}

			printf($link, $homeLink . '/' . $slug . '/', $post_type->labels->name);
			if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;
		}

	/*if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object(get_post_type());
        $slug = $post_type->rewrite;
        printf($link, $homeLink . '/' . $slug['slug'] . '/', $post_type->labels->singular_name);
        if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;
      }*/ else {
        $cat = get_the_category(); $cat = $cat[0];
        $cats = get_category_parents($cat, TRUE, $delimiter);
        if ($showCurrent == 0) $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);
        $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
        $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
        echo $cats;
        if ($showCurrent == 1) echo $before . get_the_title() . $after;
      }

    } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
      $post_type = get_post_type_object(get_post_type());
      echo $before . $post_type->labels->singular_name . $after;

    } elseif ( is_attachment() ) {
      $parent = get_post($post->post_parent);
      $cat = get_the_category($parent->ID); $cat = $cat[0];
      $cats = get_category_parents($cat, TRUE, $delimiter);
      $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
      $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
      echo $cats;
      printf($link, get_permalink($parent), $parent->post_title);
      if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;

    } elseif ( is_page() && !$post->post_parent ) {
      if ($showCurrent == 1) echo $before . get_the_title() . $after;

    } elseif ( is_page() && $post->post_parent ) {
      $parent_id  = $post->post_parent;
      $breadcrumbs = array();
      while ($parent_id) {
        $page = get_page($parent_id);
        $breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
        $parent_id  = $page->post_parent;
      }
      $breadcrumbs = array_reverse($breadcrumbs);
      for ($i = 0; $i < count($breadcrumbs); $i++) {
        echo $breadcrumbs[$i];
        if ($i != count($breadcrumbs)-1) echo $delimiter;
      }
      if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;

    } elseif ( is_tag() ) {
      echo $before . sprintf($text['tag'], single_tag_title('', false)) . $after;

    } elseif ( is_author() ) {
       global $author;
      $userdata = get_userdata($author);
      echo $before . sprintf($text['author'], $userdata->display_name) . $after;

    } elseif ( is_404() ) {
      echo $before . $text['404'] . $after;
    }

    if ( get_query_var('paged') ) {
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo '';
      //echo __('Page','qns') . ' ' . get_query_var('paged');
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo '';
    }

    echo '</p></div>';

  }
} // end dimox_breadcrumbs()



/* ------------------------------------------------
	Remove width/height dimensions from <img> tags
------------------------------------------------ */

add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10 );
add_filter( 'image_send_to_editor', 'remove_thumbnail_dimensions', 10 );

function remove_thumbnail_dimensions( $html ) {
    $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
    return $html;
}

/**
 * Chooses the search-courses template when searching for courses
 *
 * @global WP_Query $wp_query The main query
 * @param string $template The template file name
 * @return string The filtered template file name
 */
function template_chooser($template) {
	global $wp_query;
	$get = filter_input_array(INPUT_GET);
	if ($wp_query->is_search) {
		if (isset($get['keyword-type'])) {
				return locate_template('search-courses.php');
		}
	}
	return $template;
}
add_filter('template_include', 'template_chooser');

/**
 * Filters the query when searching for courses
 *
 * @global array $data The SMOF options
 * @param WP_Query $query The main query to be filtered
 * @return WP_Query The filtered query
 */
function search_courses($query) {
	$get = filter_input_array(INPUT_GET);
	if (!isset($get['keyword-type'])) {
		return $query;
	}
	global $data;
	$posts_per_page = !empty($data['course_items_pp']) ? $data['course_items_pp'] : 10;
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$keyword = stripslashes($get['s']);
	$keyword_type = '';

	switch($get['keyword-type']) {
		//case 'course_id': $keyword_type = 'qns_course_id'; break;
		case 'course_name': $keyword_type = 'qns_course_name'; break;
		case 'program_type': $keyword_type = 'qns_program_type'; break;
		case 'course_length': $keyword_type = 'qns_course_length'; break;
		default: $keyword_type = 'qns_course_name';
	}

	/*if (empty($keyword)) {
		$query->is_search = TRUE;
		$query->is_home = FALSE;
	}*/
	if ($query->is_search && isset($get['keyword-type'])) {
		$query->set('s', '');
		$query->set('post_type', 'course');
		$query->set('posts_per_page', $posts_per_page);
		$query->set('paged', $paged);
		$query->set('order', 'ASC');
		$query->set('meta_query', array(array(
			'key' => $keyword_type,
			'value' => $keyword,
			'compare' => 'LIKE'
		)));
	}
	return $query;
}
add_filter('pre_get_posts', 'search_courses');

function change_search_url_rewrite() {
	$get = filter_input_array(INPUT_GET);

	if ( is_search() && ! empty( $_GET['s'] ) && ! (isset($get['keyword-type'])) ) {
		wp_redirect( home_url( "/search/" ) . urlencode( get_query_var( 's' ) ) );
		exit();
	}
}
add_action( 'template_redirect', 'change_search_url_rewrite' );

if(!is_admin()){
    add_action('init', 'search_query_fix');
    function search_query_fix(){
        if(isset($_GET['s']) && $_GET['s']==''){
            $_GET['s']=' ';
        }
    }
}

//Função corretor ortográfico pt-br
function fb_mce_external_languages($initArray){
    $initArray['spellchecker_languages'] = '+Portuguese=pt, English=en';

    return $initArray;
}
add_filter('tiny_mce_before_init', 'fb_mce_external_languages');

// adicionar posts automaticamente
function adicionar_polo($title) {
//function adicionar_polo($title, $polo_apresentacao, $polo_informacoes) {  DESENVOLVIMENTO!!!
	$author_id = 1;
	$args = array(
		'comment_status'=> 'closed',
		'ping_status'   => 'closed',
		'post_author'   => $author_id,
		'post_title'    => $title,
		'post_status'   => 'publish',
		'post_type'		=> 'portfolio',
	);

	$post_id = wp_insert_post( $args );

	//update_post_meta($post_id, 'qns_polo_apresentacao', $polo_apresentacao, '');
	//update_post_meta($post_id, 'qns_polo_informacoes', $polo_informacoes, '');
}

// function adicionar_curso($title, $course_length, $course_length, $course_grade, $course_polos) {
// //function adicionar_curso($title, $program_type, $course_length, $course_description, $course_perfilegresso, $course_grade, $course_polos, $course_informacoes) {
// 	$author_id = 1;
// 	$args = array(
// 		'comment_status'=> 'closed',
// 		'ping_status'   => 'closed',
// 		'post_author'   => $author_id,
// 		'post_title'    => $title,
// 		'post_status'   => 'publish',
// 		'post_type'		=> 'portfolio',
// 	);

// 	$post_id = wp_insert_post( $args );

// 	update_post_meta($post_id, 'qns_course_name', $title, '');
// 	update_post_meta($post_id, 'qns_program_type', $program_type, '');  //graduacao, pos, etc
// 	update_post_meta($post_id, 'qns_course_length', $course_length, '');
// 	//update_post_meta($post_id, 'qns_course_description', $course_description, '');  //apresentacao
// 	//update_post_meta($post_id, 'qns_course_perfilegresso', $course_perfilegresso, '');  //perfil egresso/area atuacao
// 	update_post_meta($post_id, 'qns_course_grade', $course_grade, '');
// 	update_post_meta($post_id, 'qns_course_polos', $course_polos, '');
// 	//update_post_meta($post_id, 'qns_course_informacoes', $course_informacoes, '');  //informacoes curso
// }

function strProper($str) {
	$noUp = array('um','uma','o','a','de','do','da','em','e','dos','das','no','na');
	$str = trim($str);
	$str = strtoupper($str[0]) . strtolower(substr($str, 1));
	for($i=1; $i<strlen($str)-1; ++$i) {
		if($str[$i]==' ') {
			for($j=$i+1; $j<strlen($str) && $str[$j]!=' '; ++$j); //find next space
			$size = $j-$i-1;
			$shortWord = false;
			if($size<=3) {
				$theWord = substr($str,$i+1,$size);
				for($j=0; $j<count($noUp) && !$shortWord; ++$j)
					if($theWord==$noUp[$j])
						$shortWord = true;
			}
			if( !$shortWord )
				$str = substr($str, 0, $i+1) . strtoupper($str[$i+1]) . substr($str, $i+2);
		}
		$i+=$size;
	}
	return $str;
}

function wp_get_attachment_image2($href, $attachment_id, $size = 'thumbnail', $icon = false, $attr = '') {

	$html = '';
	$image = wp_get_attachment_image_src($attachment_id, $size, $icon);
	if ( $image ) {
		list($src, $width, $height) = $image;
		$hwstring = image_hwstring($width, $height);
		if ( is_array($size) )
			$size = join('x', $size);
		$attachment = get_post($attachment_id);
		$default_attr = array(
			'src'	=> $src,
			'class'	=> "attachment-$size",
			'alt'	=> trim(strip_tags( get_post_meta($attachment_id, '_wp_attachment_image_alt', true) )), // Use Alt field first
		);
		if ( empty($default_attr['alt']) )
			$default_attr['alt'] = trim(strip_tags( $attachment->post_excerpt )); // If not, Use the Caption
		if ( empty($default_attr['alt']) )
			$default_attr['alt'] = trim(strip_tags( $attachment->post_title )); // Finally, use the title

		$attr = wp_parse_args($attr, $default_attr);

		/**
		 * Filter the list of attachment image attributes.
		 *
		 * @since 2.8.0
		 *
		 * @param mixed $attr          Attributes for the image markup.
		 * @param int   $attachment_id Image attachment ID.
		 */
		$attr = apply_filters( 'wp_get_attachment_image_attributes', $attr, $attachment );
		$attr = array_map( 'esc_attr', $attr );
		$html = rtrim("<a href=\" $href \"><img ");
		foreach ( $attr as $name => $value ) {
			$html .= " $name=" . '"' . $value . '"';
		}
		$html .= ' />';
	}
	else
	{
		$html = "<a href=".$href."><img src='https://www.cead.ufjf.br/wp-content/uploads/2014/11/logo_default.png'></a>";
	}

	return $html;
}


// function wpdev_custom_excerpt_length( $length ) {
// 	return 20;
// }
// add_filter( 'excerpt_length', 'wpdev_custom_excerpt_length');

function new_excerpt_more( $more ) {
	return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');

function wp_nav_menu_no_ul()
{
    $options = array(
        'echo' => false,
        'container' => false,
        // 'theme_location' => 'primary',
        // 'fallback_cb'=> 'default_page_menu'
    );

    $menu = wp_nav_menu($options);
    echo preg_replace(array(
        '#^<ul[^>]*>#',
        '#</ul>$#'
    ), '', $menu);

}

function default_page_menu() {
   wp_list_pages('title_li=');
} 

function formataNomePolo( $nomePolo ) {
	/*Parte usada para capitalizar nome dos polos com acentuação correta*/
	$encoding = mb_internal_encoding();
	$nomePolo = mb_convert_case($nomePolo, MB_CASE_TITLE, $encoding);

	$palavrasNomePolo = explode(" ", $nomePolo);
	$tamanhoNomeCidade = count($palavrasNomePolo);

	//tratando caso do hífen (letra após o hífen maiúscula)
	for($i = 0;$i<$tamanhoNomeCidade;$i++){
		$palavrasNomePolo[$i] = implode('-', array_map('ucfirst', explode('-', $palavrasNomePolo[$i])));
	}

	$nomePolo = implode(" ",$palavrasNomePolo);

	//tratando outros casos
	if($tamanhoNomeCidade > 1){
		$nomePolo = str_replace(" Da ", " da ", $nomePolo);
		$nomePolo = str_replace(" De ", " de ", $nomePolo);
		$nomePolo = str_replace(" Do ", " do ", $nomePolo);
		$nomePolo = str_replace(" Das ", " das ", $nomePolo);
		$nomePolo = str_replace(" Des ", " des ", $nomePolo);
		$nomePolo = str_replace(" E ", " e ", $nomePolo);
		$nomePolo = str_replace(" D'", " d'", $nomePolo);
		$nomePolo = str_replace(" Del", " del", $nomePolo);
	}

	return $nomePolo;
}

function custom_posts_pagination($settings = array('count_prev' => 1,'count_next' => 2,'prev_text' => '<','next_text' => '>')){

	global $wp_query;
	$current = max( 1, get_query_var( 'paged' ) );
	$total = $wp_query->max_num_pages;
	$links = array();

	if($total < 2)
		return '';

	if( $total - $current < $settings['count_next'] ) {
		$settings['count_prev'] = $settings['count_prev'] + $settings['count_next'] - ($total - $current);
	}
	if( $current <= $settings['count_prev'] ) {
		$settings['count_next'] = $settings['count_next'] + $settings['count_prev'] - ($current - 1);
	}

	// Previous
	if( $current > 1 ) {				
		$link_prev = custom_navigation_link( $current - 1, '', $settings['prev_text'], "Anterior" );
	}
	else {
		$link_prev = '<span class="current">'.$settings['prev_text'].'</span>';
	}

	// Previous Pages
	for( $i = $settings['count_prev']; $i > 0; $i-- ) {
		$page = $current - $i;
		if( $page > 0 ) {
			$links[] = custom_navigation_link( $page, 'page-numbers' );
		}
	}

	// Current
	$links[] = '<span aria-current="page" class="page-numbers current">'.$current.'</span>';

	// Next Pages
	for( $i = 1; $i <= $settings['count_next']; $i++ ) {
		$page = $current + $i;
		if( $page <= $total ) {
			$links[] = custom_navigation_link( $page, 'page-numbers' );
		}
	}

	// Next
	if( $current < $total ) {			
		$link_next = custom_navigation_link( $current + 1, '', $settings['next_text'], "Próximo" );
	}
	else
	{
		$link_next = '<span class="current">'.$settings['next_text'].'</span>';
	}

	echo '<div id="navigation-post">';
		echo '<div id="link-previous">'.$link_prev.'</div>';
		echo '<div id="link-pag">';
			echo '<nav class="navigation pagination" role="navigation">';
	    		echo '<div class="nav-links">' . join( '', $links ) . '</div>';
			echo '</nav>';
		echo '</div>';
		echo '<div id="link-next">'.$link_next.'</div>';
	echo '</div>';
}

add_action( 'tha_content_while_after', 'custom_posts_navigation' );

function custom_navigation_link( $page = false, $class = '', $label = '', $title = '' ) {

	if( ! $page )
		return;

	$classes = array( 'page-numbers' );
	if( !empty( $class ) )
		$classes[] = $class;
	$classes = array_map( 'sanitize_html_class', $classes );

	$label = $label ? $label : $page;
	$link = esc_url_raw( get_pagenum_link( $page ) );

	return '<a class="' . join ( ' ', $classes ) . '" href="' . $link . '" title="'.$title.'">' . $label . '</a>';

}

function get_default_og_image($post_id)
{
	$post_date = strtotime(get_the_time('Y-m-d', $post_id));
	if($post_date >= strtotime("2019-09-09"))
		return get_template_directory_uri().'/img/cover_default/ale-'.($post_id%23+1).'.jpg';
	return get_template_directory_uri().'/img/institucional-banner.jpg';
}

function get_default_cover($post_id)
{
	$post_date = strtotime(get_the_time('Y-m-d', $post_id));
	if($post_date >= strtotime("2019-09-09"))
		return get_template_directory_uri().'/img/cover_default/ale-'.($post_id%23+1).'.jpg';
	// return get_template_directory_uri().'/img/posts_cover.jpg';
	return '';
}

function get_default_thumbnail($post_id)
{
	$post_date = strtotime(get_the_time('Y-m-d', $post_id));
	if($post_date >= strtotime("2019-09-09"))
		return get_template_directory_uri().'/img/cover_default/ale-thumb-'.($post_id%23+1).'.jpg';
	return get_template_directory_uri().'/img/posts-old-thumb.jpg';
}

function get_default_card_thumb($post_id)
{
	$post_date = strtotime(get_the_time('Y-m-d', $post_id));
	if($post_date >= strtotime("2019-09-09"))
		return get_template_directory_uri().'/img/cover_default/ale-card-'.($post_id%23+1).'.jpg';
	return get_template_directory_uri().'/img/posts_cover-card.jpg';
}

/**
 * Filter the except length to 20 words.
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 */
function wpdocs_custom_excerpt_length( $length ) {
    return 20;
}
add_filter( 'excerpt_length', 'wpdocs_custom_excerpt_length', 999 );



add_action( 'wp_footer', 'contactform_sent_event' );
 
function contactform_sent_event() {
?>
<script type="text/javascript">
document.addEventListener( 'wpcf7mailsent', function( event ) {
    ga( 'send', 'event', 'Contact Form', 'submit' );
    document.getElementById('wpcf7-f17427-p17344-o1').style.display = 'none';
}, false );
</script>
<?php
}


/* ------------------------------------------------
	Add Google Analytics script
------------------------------------------------ */

add_action('wp_head','my_analytics', 20);function my_analytics() {

	if( strpos( site_url(), 'cead.ufjf.br') !== false )
	{
?>

		<!-- Global site tag (gtag.js) - Google Analytics -->
		<!--
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-156121632-1"></script>
		-->
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());

			gtag('config', 'UA-156121632-1');
		</script>
<?php
	}
}

function cead_enqueue_scripts() {
    // Remove o jQuery antigo embutido (caso ainda exista)
    wp_deregister_script('jquery');

    // Registra e adiciona a versão que o WordPress já traz (normalmente 3.x)
    wp_enqueue_script('jquery');

    // Se precisar do jQueryUI também:
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-autocomplete');
}
add_action('wp_enqueue_scripts', 'cead_enqueue_scripts');