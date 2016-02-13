<?php

/**
 * Empire Tri storefront child theme functions.php file.
 *
 * @package empire-storefront-child
 */

// Replace main nav text on mobile
add_filter( 'storefront_menu_toggle_text', function($text) { return 'Menu'; } );

add_action( 'after_setup_theme', function() {
  remove_action( 'storefront_header', 'storefront_product_search', 40 ); // remove search bar
});

// Establish relationship between parent and child themes
function theme_enqueue_styles() {
  wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

// Add carousel script
function load_javascript_files() {
  if(is_front_page()) { // conditionally enqueue carousel script (homepage only)
    wp_enqueue_script('jquery');
    wp_enqueue_script('carousel_script', get_stylesheet_directory_uri() . '/js/carousel.js', true );
    wp_enqueue_script('owl_carousel_script', get_stylesheet_directory_uri() . '/owl-carousel/owl.carousel.js', true );
  }
}

add_action( 'wp_enqueue_scripts', 'load_javascript_files' );


// Load stylesheets for Owl carousel
function load_stylesheets() {
  if(is_front_page()) {
    wp_enqueue_style('owl_carousel_styles', get_stylesheet_directory_uri() . '/owl-carousel/owl.carousel.css');
    wp_enqueue_style('owl_theme_styles', get_stylesheet_directory_uri() . '/owl-carousel/owl.theme.css');
  }
}

add_action( 'wp_enqueue_scripts', 'load_stylesheets' );


// Disable text header and enable custom logo
function storefront_custom_logo() {
  remove_action( 'storefront_header', 'storefront_site_branding', 20 );
  add_action( 'storefront_header', 'storefront_display_custom_logo', 20 );
}

// Add custom logo
function storefront_display_custom_logo() {
?>
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo-link" rel="home">
    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/logo.svg" alt="<?php echo get_bloginfo( 'name' ); ?>" />
    <?php if ( '' != get_bloginfo( 'description' ) ) { ?>
      <p class="site-description"><?php echo bloginfo( 'description' ); ?></p>
    <?php } ?>
  </a>
<?php
}

add_action( 'init', 'storefront_custom_logo' );

// Sponsors
function etc_sponsors() { ?>
  <div id='sponsors'>
    <h5>Our Partners and Sponsors:</h5>
    <ul id='sponsors-banner'>
      <?php

      $args = array(
        'post_type'   => 'etc_sponsors'
      );

      $posts_array = get_posts( $args );

      foreach ( $posts_array as $key => $post ) {

        setup_postdata( $post );
        $post_thumbnail = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );

        ?>
        <li><a href="<?php echo $post->post_excerpt ?>" target="_blank"><img src='<?php echo $post_thumbnail ?>' alt='<?php echo $post->post_title ?>'/></a></li>

      <?php } ?>
    </ul>
  </div>
<?php }

add_action('storefront_before_footer', 'etc_sponsors');

// Register custom post types
function create_post_type() {

  // Blog
  register_post_type( 'etc_blog',
    array(
      'labels'          => array(
        'name'          => __( 'Blog' ),
        'singular_name' => __( 'Blog' )
      ),
      'public'      => true,
      'has_archive' => 'about-the-club/blog',
      'rewrite'     => true
    )
  );

  // In The News
  register_post_type( 'etc_news',
    array(
      'labels'          => array(
        'name'          => __( 'In The News' ),
        'singular_name' => __( 'In The News' )
      ),
      'public'      => true,
      'has_archive' => 'about-the-club/news',
      'rewrite'     => true,
      'supports'    => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
    )
  );

  // Sponsors
  register_post_type( 'etc_sponsors',
    array(
      'labels'          => array(
        'name'          => __( 'Sponsors' ),
        'singular_name' => __( 'Sponsor' )
      ),
      'public'      => true,
      'has_archive' => false,
      'rewrite'     => array( 'slug' => 'sponsors' ),
      'supports'    => array( 'title', 'editor', 'thumbnail', 'excerpt' )
    )
  );
}
add_action( 'init', 'create_post_type' );


// Set post types per page on news archive
function set_posts_per_page_etc_news( $query ) {
  if ( !is_admin() && $query->is_main_query() && is_post_type_archive( 'etc_news' ) ) {
    $query->set( 'posts_per_page', '10' );
  }
}
add_action( 'pre_get_posts', 'set_posts_per_page_etc_news' );


// Custom Homepage
// Get featured content for the homepage
function empire_homepage_featured() {

  $args = array(
    'category_name'    => 'featured',
    'post_type'   => 'post'
  );

  $posts_array = get_posts( $args );

  foreach ( $posts_array as $key => $post ) {

    setup_postdata( $post );
    $background_image_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );

    ?>

    <div class="carousel-item" style="background-image: url('<?php echo $background_image_url ?>')">
      <div class="carousel-item-content">

            <a href="<?php echo get_permalink($post->ID) ?>">
              <h1><?php echo $post->post_title ?></h1>
              <p><?php echo $post->post_excerpt ?></p>
            </a>

      </div>
    </div>

<?php

  }

  wp_reset_postdata();

}

add_action( 'empire_homepage_featured', 'empire_homepage_featured');

// Homepage 'buckets'

function empire_homepage_buckets() {

  ?>

  <ul class='products'>

  <?php

  $args = array(
    'category_name'    => 'bucket',
    'post_type'   => 'post'
  );

  $posts_array = get_posts( $args );

  foreach ( $posts_array as $key => $post ) {

    setup_postdata( $post );
    $post_thumbnail = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );

    ?>

    <li class='product'>
      <div class="bucket">
        <img src="<?php echo $post_thumbnail ?>" alt="<?php echo $post->post_title ?>" />
        <h1><?php echo $post->post_title ?></h1>
        <?php the_content() ?>
      <div>
    </li>

  <?php } ?>

  </ul>

  <?php wp_reset_postdata();

}
add_action( 'empire_homepage_buckets', 'empire_homepage_buckets' );
