<?php
/*
Plugin Name: Saoshyant Custom Slider
Description: The custom slider plugin.
Author: saoshyant
*/ 
/*********************************************************************************************
Registers Custom Slider Post Type
*********************************************************************************************///
function reza_slide_post_type() {
	$labels = array(
		'name' 					=> __('Slides','reza'),
		'singular_name'			=> __('Slide','reza'),
		'add_new'				=> __('Add New','reza'),
		'add_new_item'			=>__('Add New Slide','reza'),
		'edit_item'				=> __('Edit Slide','reza'),
		'new_item'				=> __('New Slide','reza'),
		'view_item'				=> __('View Slide','reza'),
 		'all_items'				=>__('All Slides','reza'),
 		'search_items'			=> __('Search Slides','reza'),
		'not_found'				=>  __('No slides found','reza'),
		'not_found_in_trash'	=>__('No slides found in trash','reza'),
		'parent_item_colon'		=> '',
		'menu_name'				=> __('Custom Slides','reza')
	);
	
	$args = array(
		'labels'				=> $labels,
		'public'				=> true,
		'publicly_queryable'	=> true,
		'show_ui'				=> true, 
		'show_in_menu'			=> true, 
		'query_var'				=> true,
		'rewrite'				=> true,
		'capability_type'		=> 'post',
		'has_archive'			=> false, 
		'hierarchical'			=> false,
		'menu_position'			=> null,
		'supports' => array( 'title','excerpt','thumbnail' )
	); 

	register_post_type( 'reza_slide', $args );
}
add_action( 'init', 'reza_slide_post_type' );
 
 
add_action( 'init', 'reza_sliders_taxonomy', 0 );
function reza_sliders_taxonomy() {
 
   $labels = array(
    'name'							=> __( 'Sliders','reza' ),
    'singular_name'					=> __( 'Slider','reza'  ),
    'search_items'					=> __( 'Search Sliders' ,'reza' ),
    'popular_items'					=> __( 'Popular Sliders','reza'  ),
    'all_items' 					=> __( 'All Sliders' ,'reza' ),
    'parent_item'					=> __( 'Parent Slider' ,'reza' ),
    'edit_item'						=> __( 'Edit Topic','reza' ), 
    'update_item' 					=> __( 'Update Slider','reza'  ),
    'add_new_item'					=> __( 'Add New Slider','reza'  ),
    'new_item_name'			 		=> __( 'New Topic Name' ,'reza' ),
    'separate_items_with_commas'	=> __( 'Separate Sliders with commas' ,'reza' ),
    'add_or_remove_items'			=> __( 'Add or remove Sliders','reza'  ),
    'choose_from_most_used' 		=> __( 'Choose from the most used Sliders','reza'  ),
    'menu_name' 					=> __( 'Sliders' ,'reza' ),
  ); 


// Now register the taxonomy

  register_taxonomy('reza_sliders','reza_slide', array(
    'hierarchical' 					=> true,
    'labels' 						=> $labels,
    'show_ui' 						=> true,
    'show_admin_column'				=> true,
    'query_var'						=> true,
    'rewrite' 						=> array( 'slug' => 'reza_sliders' ),
  ));

}
add_filter('manage_reza_slide_posts_columns', 'reza_add_thumbnail_column', 5);

function reza_add_thumbnail_column($columns){
  $columns['new_post_excerpt'] = __('Excerpt','reza');
  $columns['new_post_thumb'] = __('Featured Image','reza');
  return $columns;
}

add_action('manage_reza_slide_posts_custom_column', 'reza_display_thumbnail_column', 5, 2);

function reza_display_thumbnail_column($column_name, $post_id){
  switch($column_name){
    case 'new_post_thumb':
      $post_thumbnail_id = get_post_thumbnail_id($post_id);
      if (!empty($post_thumbnail_id)) {
        $post_thumbnail_img = wp_get_attachment_image_src( $post_thumbnail_id, 'thumbnail' );
        echo '<img width="100" src="' . esc_url($post_thumbnail_img[0]) . '" />';
      }
      break;
    case 'new_post_excerpt':
	$the_excerpt = strip_tags(get_the_excerpt());
  	if ( strlen($the_excerpt) > 200 && 200){
 		 $content= mb_substr($the_excerpt, 0,200); $dots='...';
		 
	}else{
		$content= @$the_excerpt;
		$dots='';
	}
	  echo esc_html($content),esc_html($dots);	
      break;
	  
  }
}

 


add_action( 'add_meta_boxes', 'reza_slide_link' );
function reza_slide_link()
{
    add_meta_box( 'link-meta-box-id', 'Link', 'reza_slide_callback', 'reza_slide', 'normal', 'high' );
}

function reza_slide_callback( $post )
{
    $values = get_post_custom( $post->ID );
    $link = isset( $values['reza_slide_link'] ) ? $values['reza_slide_link'][0] : '';

    wp_nonce_field( 'my_reza_slide_nonce', 'reza_slide_nonce' );
    ?>
    <p>
 		<input type="text" name="reza_slide_link" id="reza_slide_link"  value="<?php echo esc_url($link); ?>" style="width:100%;" />

    </p>
    <p><?php echo esc_html__('Add the link of the Slide','reza');?></p>
    <?php   
}

add_action( 'save_post', 'reza_slide_link_save' );
function reza_slide_link_save( $post_id )
{
    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['reza_slide_nonce'] ) || !wp_verify_nonce( $_POST['reza_slide_nonce'], 'my_reza_slide_nonce' ) ) return;

    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post' ) ) return;

 
    // Probably a good idea to make sure your data is set

    if( isset( $_POST['reza_slide_link'] ) )
        update_post_meta( $post_id, 'reza_slide_link', $_POST['reza_slide_link'] );

}
?>
