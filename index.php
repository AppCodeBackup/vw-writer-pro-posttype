<?php 
/*
 Plugin Name: VW Writer Pro Posttype
 lugin URI: https://www.vwthemes.com/
 Description: Creating new post type for VW Writer Pro Theme.
 Author: VW Themes
 Version: 1.1
 Author URI: https://www.vwthemes.com/
Domain Path: /languages/
*/

define( 'VW_WRITER_PRO_POSTTYPE_VERSION', '1.0' );
add_action( 'init', 'vw_writer_pro_posttype_create_post_type' );

function vw_writer_pro_posttype_create_post_type() {
  register_post_type( 'testimonials',
    array(
  		'labels' => array(
  			'name' => __( 'Testimonials','vw-writer-pro-posttype' ),
  			'singular_name' => __( 'Testimonials','vw-writer-pro-posttype' )
  		),
  		'capability_type' => 'post',
  		'menu_icon'  => 'dashicons-businessman',
  		'public' => true,
  		'supports' => array(
  			'title',
  			'editor',
  			'thumbnail'
  		)
		)
	);
}
/*----------------------Testimonial section ----------------------*/
/* Adds a meta box to the Testimonial editing screen */
function vw_writer_pro_posttype_bn_testimonial_meta_box() {
	add_meta_box( 'vw-writer-pro-posttype-testimonial-meta', __( 'Enter Details', 'vw-writer-pro-posttype' ), 'vw_writer_pro_posttype_bn_testimonial_meta_callback', 'testimonials', 'normal', 'high' );
}
// Hook things in for admin
if (is_admin()){
    add_action('admin_menu', 'vw_writer_pro_posttype_bn_testimonial_meta_box');
}
/* Adds a meta box for custom post */
function vw_writer_pro_posttype_bn_testimonial_meta_callback( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'vw_writer_pro_posttype_posttype_testimonial_meta_nonce' );
  $bn_stored_meta = get_post_meta( $post->ID );
  if(!empty($bn_stored_meta['vw_writer_pro_posttype_testimonial_desigstory'][0]))
      $bn_vw_writer_pro_posttype_testimonial_desigstory = $bn_stored_meta['vw_writer_pro_posttype_testimonial_desigstory'][0];
    else
      $bn_vw_writer_pro_posttype_testimonial_desigstory = '';
	?>
	<div id="testimonials_custom_stuff">
		<table id="list">
			<tbody id="the-list" data-wp-lists="list:meta">
				<tr id="meta-1">
					<td class="left">
						<?php _e( 'Designation', 'vw-writer-pro-posttype' )?>
					</td>
					<td class="left" >
						<input type="text" name="vw_writer_pro_posttype_testimonial_desigstory" id="vw_writer_pro_posttype_testimonial_desigstory" value="<?php echo esc_attr( $bn_vw_writer_pro_posttype_testimonial_desigstory ); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
}
/* Saves the custom meta input */
function vw_writer_pro_posttype_bn_metadesig_save( $post_id ) {
	if (!isset($_POST['vw_writer_pro_posttype_posttype_testimonial_meta_nonce']) || !wp_verify_nonce($_POST['vw_writer_pro_posttype_posttype_testimonial_meta_nonce'], basename(__FILE__))) {
		return;
	}
	if (!current_user_can('edit_post', $post_id)) {
		return;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	// Save desig.
	if( isset( $_POST[ 'vw_writer_pro_posttype_testimonial_desigstory' ] ) ) {
		update_post_meta( $post_id, 'vw_writer_pro_posttype_testimonial_desigstory', sanitize_text_field($_POST[ 'vw_writer_pro_posttype_testimonial_desigstory']) );
	}
}
add_action( 'save_post', 'vw_writer_pro_posttype_bn_metadesig_save' );
/*------------------- Testimonial Shortcode -------------------------*/
function vw_writer_pro_posttype_testimonials_func( $atts ) {
    $testimonial = ''; 
    $testimonial = '<div id="testimonials"><div class="row inner-test-bg">';
      $new = new WP_Query( array( 'post_type' => 'testimonials') );
      if ( $new->have_posts() ) :
        $k=1;
        while ($new->have_posts()) : $new->the_post();
          $post_id = get_the_ID();
          $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'medium' );
          $url = $thumb['0'];
          $excerpt = vw_portfolio_pro_string_limit_words(get_the_excerpt(),20);
          $designation = get_post_meta($post_id,'vw_writer_pro_posttype_testimonial_desigstory',true);

          $testimonial .= '<div class="col-md-12 mb-4">
                <div class="testimonial_box mb-3 col-md-10 offset-md-1" >
                  <div class="content_box w-100">
                    <div class="short_text pb-3"><blockquote>'.$excerpt.'</blockquote></div>
                  </div>                  
                </div>
                <ul class="testimonial_auther">
                  <li class="textimonial-img">';
                    if (has_post_thumbnail()){
                    $testimonial.= '<img src="'.esc_url($url).'">';
                    }
                  $testimonial .= '</li>
                  <li class="testimonial-box">
                    <h4 class="testimonial_name"><a href="'.get_the_permalink().'">'.get_the_title().'</a> <cite>'.esc_html($designation).'</cite></h4>
                  </li>
                </ul>
              </div>
              <div class="clearfix"></div>';
          $k++;         
        endwhile; 
        wp_reset_postdata();
      else :
        $testimonial = '<div id="testimonial" class="testimonial_wrap col-md-3 mt-3 mb-4"><h2 class="center">'.__('Not Found','vw-writer-pro-posttype').'</h2></div>';
      endif;
    $testimonial .= '</div></div>';
    return $testimonial;
}
add_shortcode( 'vw-writer-pro-testimonials', 'vw_writer_pro_posttype_testimonials_func' );

add_action( 'vw_writer_pro_posttype_plugins_loaded', 'vw_writer_pro_posttype_load_textdomain' );
/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function vw_writer_pro_posttype_load_textdomain() {
  load_plugin_textdomain( 'vw-writer-pro-posttype', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
}