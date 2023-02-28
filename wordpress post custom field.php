<?php

// add rm events
function rmevents_post_types(){
    register_post_type('event',[
        'public' => true,
        'labels' =>[
            'name' => 'Events',
            'add_new_item' => 'Add New Event',
            'edit_item' => 'Edit Event',
            'all_items' => 'All Events',
            'singular_name' => 'Event'
        ],
        'menu_icon' => 'dashicons-calendar',
        'supports' => ['title','editor','thumbnail','revisions','custom-fields'],
        'has_archive' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'can_export' => true,
        'has_archive' => true
    ]);
}
add_action('init','rmevents_post_types');


function rm_add_custom_meta_box() {
    add_meta_box(
        'custom_event_date_metabox',       // $id
        'Event Date',                  // $title
        'show_custom_meta_box',  // $callback
        'event',                 // $page
        'side',                  // location of the input box
        'low'                     // $priority
    );
 }
 add_action('add_meta_boxes', 'rm_add_custom_meta_box');


 function show_custom_meta_box() {
    global $post;

    // Use nonce for verification to secure data sending
    wp_nonce_field( basename( __FILE__ ), 'readersmagnet_our_nonce' );


    $custom = get_post_custom( $post->ID );
    $rm_post_event_date = $custom[ "_event_date" ][ 0 ] ?? '';

  
    ?>
    
    <!-- my custom value input -->
    <input type="date" name="_event_date" value="<?=$rm_post_event_date?>" placeholder= "Event Date">

    <?php
}


 // save field value
function save_post_meta_boxes(){
    global $post;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if (!isset($_POST['readersmagnet_our_nonce']) || !wp_verify_nonce($_POST['readersmagnet_our_nonce'], basename(__FILE__))){
        return 'nonce not verified';
    }
   
    if($post)
    {
        update_post_meta( $post->ID, "_event_date", sanitize_text_field( $_POST[ "_event_date" ] ) );
    }
    
}


add_action( 'save_post', 'save_post_meta_boxes' );



add_shortcode('events-list', 'rm_events');

function rm_events(){
    global $post;

    $args = array(
        'post_type'=>'event', 
        'post_status'=>'publish', 
        'posts_per_page'=>10, 
        'orderby'=>'meta_value',
        'meta_key' => '_event_date',
        'order'=>'DESC'
    );

    $query = new WP_Query($args);
    $content = '<ul>';

    if($query->have_posts()):
		while($query->have_posts()): $query->the_post();
        $content .= '<li><a href="'.get_the_permalink().'">'. get_the_title() .'</a> - '.date_format(date_create(get_post_meta($post->ID, '_event_date', true)), 'jS F').'</li>';
    endwhile;
    else: 
        _e('Sorry, nothing to display.', 'vicodemedia');
    endif;
    $content .= '</ul>';
  return $content;



  
}
