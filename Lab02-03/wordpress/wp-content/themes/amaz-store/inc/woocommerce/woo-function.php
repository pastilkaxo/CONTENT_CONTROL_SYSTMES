<?php 
/**
 * Perform all main WooCommerce configurations for this theme
 *
 * @package  Amaz Store WordPress theme
 */
// If plugin - 'WooCommerce' not exist then return.
if ( ! class_exists( 'WooCommerce' ) ){
	   return;
}

if ( ! function_exists( 'is_plugin_active' ) ){
  require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

/***********************************************/
//Sort section Woocommerce category filter show
/***********************************************/
function amaz_store_add_to_cart_url($product){
     $args = array();
     if ( $product ){
      $defaults = array(
        'quantity'   => 1,
        'class'      => implode(
          ' ',
          array_filter(
            array(
              'button th-button',
              'product_type_' . $product->get_type(),
              $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
              $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
            )
          )
        ),
        'attributes' => array(
          'data-product_id'  => $product->get_id(),
          'data-product_sku' => $product->get_sku(),
          'aria-label'       => $product->add_to_cart_description(),
          'rel'              => 'nofollow',
        ),
      );

      $args = apply_filters( 'woocommerce_loop_add_to_cart_args', wp_parse_args( $args, $defaults ), $product );

      if ( isset( $args['attributes']['aria-label'] ) ){
        $args['attributes']['aria-label'] = wp_strip_all_tags( $args['attributes']['aria-label'] );
      }

      wc_get_template( 'loop/add-to-cart.php', $args );
    }
}
/**********************************/
//Shop Product Markup
/**********************************/
if ( ! function_exists( 'amaz_store_product_meta_start' ) ){
  /**
   * Thumbnail wrap start.
   */
  function amaz_store_product_meta_start(){
    echo '<div class="thunk-product-wrap"><div class="thunk-product">';
  }
}
if ( ! function_exists( 'amaz_store_product_meta_end' ) ){

  /**
   * Thumbnail wrap start.
   */
  function amaz_store_product_meta_end(){

    echo '</div></div>';
  }
}
/**********************************/
//Shop Product Image Markup
/**********************************/
if ( ! function_exists( 'amaz_store_product_image_start' ) ){
  /**
   * Thumbnail wrap start.
   */
  function amaz_store_product_image_start(){
    echo '<div class="thunk-product-image">';
    
  }
}
if ( ! function_exists( 'amaz_store_product_image_end' ) ){

  /**
   * Thumbnail wrap start.
   */
    function amaz_store_product_image_end(){
      global $product;
      $pid = $product->get_id();
  echo '<div class="thunk-icons-wrap">';
  if (function_exists('amaz_store_whish_list')) {
      amaz_store_whish_list($pid);
    }
    if (function_exists('amaz_store_add_to_compare_fltr')) {
      amaz_store_add_to_compare_fltr($pid);
    }
    amaz_store_quickview();
    
    echo '</div> </div>';
  }
}

/**
   * add to cart start.
   */
 if ( ! function_exists( 'amaz_store_add_to_cart' ) ){
  function amaz_store_add_to_cart(){

    echo'<div class="th-add-to-cart">';
    echo woocommerce_template_loop_add_to_cart();

    echo '</div>';
      
  }
}

if ( ! function_exists( 'amaz_store_product_content_start' ) ){
  /**
   * Thumbnail wrap start.
   */
  function amaz_store_product_content_start(){
    echo '<div class="thunk-product-content">';
  }
}
if ( ! function_exists( 'amaz_store_product_content_end' ) ){

  /**
   * Thumbnail wrap start.
   */
  function amaz_store_product_content_end(){
    amaz_store_show_stock_shop();
    amaz_store_display_specific_shipping_class();
    echo '</div>';
  }
}
 /**
   * Thunk-product-hover start.
   */
 if ( ! function_exists( 'amaz_store_product_hover_start' ) ){
  function amaz_store_product_hover_start(){

    echo'<div class="thunk-product-hover">';
    amaz_store_add_to_cart();
      
  }
}
if ( ! function_exists( 'amaz_store_product_hover_end' ) ){

  /**
   * Thumbnail wrap start.
   */
  function amaz_store_product_hover_end(){
    
    echo '</div>';
  }
}

if ( ! function_exists( 'amaz_store_shop_content_start' ) ){

  /**
   * Thumbnail wrap start.
   */
  function amaz_store_shop_content_start(){
    $viewshow = get_theme_mod('amaz_store_prd_view','grid-view');
    if($viewshow == 'grid-view'){
    echo '<div id="shop-product-wrap" class="thunk-grid-view">';
    }else{
    echo '<div id="shop-product-wrap" class="thunk-list-view">';
    }
  }
}

if ( ! function_exists( 'amaz_store_shop_content_end' ) ){

  /**
   * Thumbnail wrap start.
   */
  function amaz_store_shop_content_end(){
    
    echo '</div>';
  }
}

function amaz_store_quickview(){
do_action('quickview');
}
/**
* Shop customization.
*
* @return void
*/
add_action( 'woocommerce_before_shop_loop_item', 'amaz_store_product_meta_start', 10);
add_action( 'woocommerce_after_shop_loop_item', 'amaz_store_product_meta_end', 12 );
add_action( 'woocommerce_before_shop_loop_item_title', 'amaz_store_product_content_start',20);
add_action( 'woocommerce_after_shop_loop_item_title', 'amaz_store_product_content_end', 20 );
add_action( 'woocommerce_after_shop_loop_item_title', 'amaz_store_product_hover_start',50);
add_action( 'woocommerce_after_shop_loop_item', 'amaz_store_product_hover_end',20);
add_action( 'woocommerce_before_shop_loop_item_title','woocommerce_template_loop_product_link_open',5);
add_action( 'woocommerce_before_shop_loop_item_title','woocommerce_template_loop_product_link_close',10);
add_action( 'woocommerce_shop_loop_item_title','woocommerce_template_loop_product_link_open',0);
add_action( 'woocommerce_shop_loop_item_title','woocommerce_template_loop_product_link_close',10);
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 0);
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price',10);
add_action( 'woocommerce_before_shop_loop_item_title', 'amaz_store_product_image_start', 0);
add_action( 'woocommerce_before_shop_loop_item_title', 'amaz_store_product_image_end',10 );

remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

add_action( 'woocommerce_before_shop_loop', 'amaz_store_shop_content_start',1);
add_action( 'woocommerce_after_shop_loop', 'amaz_store_shop_content_end',1);

remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open');
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
// remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );


/***************/
// single page
/***************/
if ( ! function_exists( 'amaz_store_single_summary_start' ) ){

  /**
   * Thumbnail wrap start.
   */
  function amaz_store_single_summary_start(){
    
    echo '<div class="thunk-single-product-summary-wrap single">';
  }
}
if( ! function_exists( 'amaz_store_single_summary_end' ) ){

  /**
   * Thumbnail wrap start.
   */
  function amaz_store_single_summary_end(){
    
    echo '</div>';
  }
}
add_action( 'woocommerce_before_single_product_summary', 'amaz_store_single_summary_start',0);
add_action( 'woocommerce_after_single_product_summary', 'amaz_store_single_summary_end',0);
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs',40 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
add_filter( 'woocommerce_product_tabs', 'amaz_store_woocommerce_custom_product_tabs', 40 );
add_action('woocommerce_single_product_summary', 'amaz_store_display_specific_shipping_class', 15 );
function amaz_store_woocommerce_custom_product_tabs( $tabs ) {
     $tabs['delivery_information'] = array(
        'title'     => __( 'Meta Information', 'amaz-store' ),
        'priority'  => 10,
        'callback'  => 'woocommerce_product_meta_tab'
    );
   return $tabs;
}
function woocommerce_product_meta_tab(){// this is where you indicate what appears in the description tab
wc_get_template( 'single-product/meta.php' ); // The meta content first
}

/**
 * Add next/prev buttons @ WooCommerce Single Product Page
 */
 
add_action( 'woocommerce_single_product_summary', 'amaz_store_prev_next_product',0 );
 
// and if you also want them at the bottom...
add_action( 'woocommerce_single_product_summary', 'amaz_store_prev_next_product',0 );
 
function amaz_store_prev_next_product(){
 
echo '<div class="prev_next_buttons">';
 
   // 'product_cat' will make sure to return next/prev from current category
   $previous = next_post_link('%link', '&larr;', TRUE, ' ', 'product_cat');
   $next = previous_post_link('%link', '&rarr;', TRUE, ' ', 'product_cat');
 
   echo $previous;
   echo $next;
    
echo '</div>';
         
}
/****************/
// add to compare
/****************/
if (!function_exists('amaz_store_add_to_compare_fltr')) {
function amaz_store_add_to_compare_fltr($pid = ''){
  global $product;
      $product_id = $pid;
       if(class_exists('th_product_compare') || class_exists('Tpcp_product_compare')){
    echo '<div class="thunk-compare"><span class="compare-list"><div class="woocommerce product compare-button">
          <a class="th-product-compare-btn compare button" data-th-product-id="'.$pid.'"></a>
          </div></span></div>';

           }
        }

       
}
/**********************/
/** wishlist **/
/**********************/
if (!function_exists('amaz_store_whish_list')) {
function amaz_store_whish_list($pid = ''){
       if( shortcode_exists( 'yith_wcwl_add_to_wishlist' )){
       echo '<div class="thunk-wishlist">
       <span class="thunk-wishlist-inner">'.do_shortcode('[yith_wcwl_add_to_wishlist product_id='.$pid.' icon="th-icon th-icon-heart1" label='.__('wishlist','amaz-store').' already_in_wishslist_text='.__('Already','amaz-store').' browse_wishlist_text='.__('Added','amaz-store').']' ).'</span></div>';
       }
 } 
}
if (!function_exists('amaz_store_whishlist_url')){
function amaz_store_whishlist_url(){
  if( class_exists( 'YITH_WCWL' )){
$wishlist_page_id =  get_option( 'yith_wcwl_wishlist_page_id' );
$wishlist_permalink = get_the_permalink( $wishlist_page_id ); ?>
<a class="whishlist" href="<?php echo esc_url( $wishlist_permalink ); ?>">
        <span class="th-icon th-icon-heartline"></span><span class="tooltiptext"><?php echo esc_html('Wishlist','amaz-store');?></span></a>
<?php } 
}
}
// shop open
/** My Account Menu **/
if (!function_exists('amaz_store_account')) {
function amaz_store_account(){
 if ( is_user_logged_in() ){
  $return = '<a class="account" href="'.get_permalink( get_option('woocommerce_myaccount_page_id') ).'"><span class="th-icon th-icon-user"></span><span class="tooltiptext">'.__('Account','amaz-store').'</span></a>';
  } 
 else {
  $return = '<a class="account" href="'.get_permalink( get_option('woocommerce_myaccount_page_id') ).'"><span class="th-icon th-icon-lock1"></span><span class="tooltiptext">'.__('Register','amaz-store').'</span></a>';
}
 echo $return;
 }
}

 // Plus Minus Quantity Buttons @ WooCommerce Single Product Page
add_action( 'woocommerce_before_add_to_cart_quantity', 'amaz_store_display_quantity_minus',10,2 );
function amaz_store_display_quantity_minus(){
      global $product;
    // Get the product ID
    $product_id = $product->get_id();

    // Check if stock management is enabled
    $manage_stock = get_post_meta( $product_id, '_manage_stock', true );

    // Check if the product has stock management and the quantity is greater than 1
    if ( ( $manage_stock === 'no' ) || ( $manage_stock === 'yes' && $product->get_stock_quantity() > 1 ) ) {
    echo '<div class="amaz-store-quantity"><button type="button" class="minus" >-</button>';
 }
  
}
add_action( 'woocommerce_after_add_to_cart_quantity', 'amaz_store_display_quantity_plus',10,2 );
function amaz_store_display_quantity_plus(){
       global $product;
    // Get the product ID
    $product_id = $product->get_id();

    // Check if stock management is enabled
    $manage_stock = get_post_meta( $product_id, '_manage_stock', true );

    // Check if the product has stock management and the quantity is greater than 1
    if ( ( $manage_stock === 'no' ) || ( $manage_stock === 'yes' && $product->get_stock_quantity() > 1 ) ) {
    echo '<button type="button" class="plus" >+</button></div>';
  }
}

//Woocommerce: How to remove page-title at the home/shop page but not category pages
add_filter( 'woocommerce_show_page_title', 'amaz_store_not_a_shop_page' );
function amaz_store_not_a_shop_page() {
    return boolval(!is_shop());
}

//***********************/
// product category list
//************************/
function amaz_store_product_list_categories( $args = '' ){
$term = get_theme_mod('amaz_store_exclde_category');
if(!empty($term[0])){
  $exclude_id = $term;
  }else{
  $exclude_id = '';
 }
$defaults = array(
        'child_of'            => 0,
        'current_category'    => 0,
        'depth'               => 5,
        'echo'                => 0,
        'exclude'             => $exclude_id,
        'exclude_tree'        => '',
        'feed'                => '',
        'feed_image'          => '',
        'feed_type'           => '',
        'hide_empty'          => 1,
        'hide_title_if_empty' => false,
        'hierarchical'        => true,
        'order'               => 'ASC',
        'orderby'             => 'menu_order',
        'separator'           => '<br />',
        'show_count'          => 0,
        'show_option_all'     => '',
        'show_option_none'    => __( 'No categories','amaz-store' ),
        'style'               => 'list',
        'taxonomy'            => 'product_cat',
        'title_li'            => '',
        'use_desc_for_title'  => 0,
    );
 $html = wp_list_categories($defaults);
        echo '<ul class="product-cat-list thunk-product-cat-list" data-menu-style="vertical">'.$html.'</ul>';
  }
function amaz_store_product_list_categories_mobile( $args = '' ){
  $term = get_theme_mod('amaz_store_exclde_category');
if(!empty($term[0])){
  $exclude_id = $term;
  }else{
  $exclude_id = '';
 }
    $defaults = array(
        'child_of'            => 0,
        'current_category'    => 0,
        'depth'               => 5,
        'echo'                => 0,
        'exclude'             => $exclude_id,
        'exclude_tree'        => '',
        'feed'                => '',
        'feed_image'          => '',
        'feed_type'           => '',
        'hide_empty'          => 1,
        'hide_title_if_empty' => false,
        'hierarchical'        => true,
        'order'               => 'ASC',
        'orderby'             => 'menu_order',
        'separator'           => '<br />',
        'show_count'          => 0,
        'show_option_all'     => '',
        'show_option_none'    => __( 'No categories','amaz-store' ),
        'style'               => 'list',
        'taxonomy'            => 'product_cat',
        'title_li'            => '',
        'use_desc_for_title'  => 0,
    );
 $html = wp_list_categories($defaults);
        echo '<ul class="thunk-product-cat-list mobile" data-menu-style="accordion">'.$html.'</ul>';
  }

  add_filter( 'woocommerce_sale_flash', 'amaz_store_add_percentage_to_sale_badge', 20, 3 );
function amaz_store_add_percentage_to_sale_badge( $html, $post, $product ) {

  if( $product->is_type('variable')){
      $percentages = array();

      // Get all variation prices
      $prices = $product->get_variation_prices();

      // Loop through variation prices
      foreach( $prices['price'] as $key => $price ){
          // Only on sale variations
          if( $prices['regular_price'][$key] !== $price ){
              // Calculate and set in the array the percentage for each variation on sale
              $percentages[] = round( 100 - ( floatval($prices['sale_price'][$key]) / floatval($prices['regular_price'][$key]) * 100 ) );
          }
      }
      // We keep the highest value
      $percentage = max($percentages) . '%';

  } elseif( $product->is_type('grouped') ){
      $percentages = array();

      // Get all variation prices
      $children_ids = $product->get_children();

      // Loop through variation prices
      foreach( $children_ids as $child_id ){
          $child_product = wc_get_product($child_id);

          $regular_price = (float) $child_product->get_regular_price();
          $sale_price    = (float) $child_product->get_sale_price();

          if ( $sale_price != 0 || ! empty($sale_price) ) {
              // Calculate and set in the array the percentage for each child on sale
              $percentages[] = round(100 - ($sale_price / $regular_price * 100));
          }
      }
      // We keep the highest value
      $percentage = max($percentages) . '%';

  } else {
      $regular_price = (float) $product->get_regular_price();
      $sale_price    = (float) $product->get_sale_price();

      if ( $sale_price != 0 || ! empty($sale_price) ) {
          $percentage    = round(100 - ($sale_price / $regular_price * 100)) . '%';
      } else {
          return $html;
      }
  }
  return '<span class="onsale">' . esc_html__( 'SALE', 'amaz-store' ) . ' ' . $percentage . '</span>';
}
//show stock quantity for products  
function amaz_store_show_stock_shop() {
   global $product;
   echo wc_get_stock_html( $product );
}

//SHOW shipping class 
function amaz_store_display_specific_shipping_class(){
    global $product;
    $shipping_class_id   = $product->get_shipping_class_id();
$shipping_class_term = get_term($shipping_class_id, 'product_shipping_class');

if( ! is_wp_error($shipping_class_term) && is_a($shipping_class_term, 'WP_Term') ) {
    $shipping_class_name  = $shipping_class_term->name;
    echo '<p class="product-shipping-class">'.esc_html($shipping_class_name).'</p>';
}

}

