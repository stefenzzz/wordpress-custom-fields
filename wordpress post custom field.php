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
    add_meta_box(
        'custom_event_until_date_metabox',       // $id
        'Event Until Date',                  // $title
        'show_custom_event_until_meta_box',  // $callback
        'event',                 // $page
        'side',                  // location of the input box
        'low'                     // $priority
    );
    add_meta_box(
        'custom_event_location_metabox',       // $id
        'Event Location',                  // $title
        'show_custom_location_meta_box',  // $callback
        'event',                 // $page
        'side',                  // location of the input box
        'low'                     // $priority
    );

    // add meta box for youtube id
    add_meta_box(
        'custom_youtube_id_metabox',       // $id
        'Youtube ID',                  // $title
        'show_custom_youtube_id_meta_box',  // $callback
        'video',                 // $page
        'side',                  // location of the input box
        'low'                     // $priority
    );
 }
 add_action('add_meta_boxes', 'rm_add_custom_meta_box');


 function show_custom_meta_box() {
    global $post;

    // Use nonce for verification to secure data sending
    wp_nonce_field( basename( __FILE__ ), 'readersmagnet_eventDate_nonce' );


    $custom = get_post_custom( $post->ID );
    $rm_post_event_date = $custom[ "_event_date" ][ 0 ] ?? '';

  
    ?>
    
    <!-- my custom value input -->
    <input type="date" name="_event_date" value="<?=$rm_post_event_date?>" placeholder= "Event Date">

    <?php
}
function show_custom_event_until_meta_box(){
    global $post;
    
    // Use nonce for verification to secure data sending
    wp_nonce_field( basename( __FILE__ ), 'readersmagnet_eventUntil_nonce' );


    $custom = get_post_custom( $post->ID );
    $rm_post_event_until_date = $custom[ "_event_until_date" ][0] ?? '';

  
    ?>
    
    <!-- my custom value input -->
    <input type="date" name="_event_until_date" value="<?=$rm_post_event_until_date?>" placeholder= "Event ID">

    <?php
    
}
function show_custom_location_meta_box(){
    global $post;
    
    // Use nonce for verification to secure data sending
    wp_nonce_field( basename( __FILE__ ), 'readersmagnet_eventLocation_nonce' );


    $custom = get_post_custom( $post->ID );
    $rm_post_location_date = $custom[ "_event_location" ][0] ?? '';

  
    ?>
    
    <!-- my custom value input -->
    <input type="text" name="_event_location" value="<?=$rm_post_location_date?>" placeholder= "Event Location">

    <?php

}
function show_custom_youtube_id_meta_box(){
    global $post;

    // Use nonce for verification to secure data sending
    wp_nonce_field( basename( __FILE__ ), 'readersmagnet_youtubeID_nonce' );


    $custom = get_post_custom( $post->ID );
    $rm_post_location_date = $custom[ "_rm_youtube_id" ][0] ?? '';

  
    ?>
    
    <!-- my custom value input -->
    <input type="text" name="_rm_youtube_id" value="<?=$rm_post_location_date?>" placeholder= "Youtube ID">

    <?php
}


 // save field value
function save_post_meta_boxes(){
    global $post;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if (isset($_POST['readersmagnet_eventDate_nonce']) ){

        
        if(wp_verify_nonce($_POST['readersmagnet_eventDate_nonce'], basename(__FILE__)))
        {
            if($post)
            {
                update_post_meta( $post->ID, "_event_date", sanitize_text_field( $_POST[ "_event_date" ] ) );
            }
        }

    }
    if (isset($_POST['readersmagnet_eventUntil_nonce'])){

        if(wp_verify_nonce($_POST['readersmagnet_eventUntil_nonce'], basename(__FILE__))){
            if($post)
            {
                update_post_meta( $post->ID, "_event_until_date", sanitize_text_field( $_POST[ "_event_until_date" ] ) );
            }
        }

    }
    if (isset($_POST['readersmagnet_eventLocation_nonce'])){
        if(wp_verify_nonce($_POST['readersmagnet_eventLocation_nonce'], basename(__FILE__))){
            if($post)
            {
                update_post_meta( $post->ID, "_event_location", sanitize_text_field( $_POST[ "_event_location" ] ) );
            }
        }
      
    }
    if (isset($_POST['readersmagnet_youtubeID_nonce'])){
        if(wp_verify_nonce($_POST['readersmagnet_youtubeID_nonce'], basename(__FILE__))){
            if($post)
            {
                update_post_meta( $post->ID, "_rm_youtube_id", sanitize_text_field( $_POST[ "_rm_youtube_id" ] ) );
            }
        }
      
    }
   
  
    
}


add_action( 'save_post', 'save_post_meta_boxes' );



add_shortcode('events-list', 'rm_upcoming_events');

function rm_upcoming_events(){
    global $post;

    $args = array(
        'post_type'=>'event', 
        'post_status'=>'publish', 
        'posts_per_page'=>3, 
        'orderby'=>'meta_value',
        'meta_key' => '_event_date',
        'order'=>'ASC'
    );

    $query = new WP_Query($args);
    $content = '<div class="events-container">';

    if($query->have_posts()):
		while($query->have_posts()): $query->the_post();
        $even_date = date_format(date_create(get_post_meta($post->ID, '_event_date', true)), 'F d, Y');
        $even_until_date = date_format(date_create(get_post_meta($post->ID, '_event_until_date', true)), 'F d, Y');
        $date = $even_date.' - '.$even_until_date;
        if($even_date === $even_until_date ){
            $date = $even_date;
        }
       
        
        $content .= '<div class="event-post-container">
        <div class="event-image-container">
        <img src="'.get_the_post_thumbnail_url().'">
        </div>
        <div class="event-content-container">
          <h3><a class="event-post-title" href="'.get_the_permalink().'">'.get_the_title().'</a></h3>
          <span class="event-date">Date: '.$date.'</span>
           <span class="event-Location">Location: '.get_post_meta($post->ID, '_event_location', true).'</span>
                  <div class="button-wrapper">
              <a href="'.get_the_permalink().'" class="event-view-more">View More</a>
              </div>  
        </div>
      </div>';
    endwhile;
    wp_reset_postdata();
    else: 
        _e('Sorry, nothing to display.', 'RMevents');
    endif;
    $content .= '</div>';
  return $content;



  
}

<?php



function rmvideos_post_types(){
    register_post_type('video',[
        'public' => true,
        'labels' =>[
            'name' => 'Videos',
            'add_new_item' => 'Add New Video',
            'edit_item' => 'Edit Video',
            'all_items' => 'All Videos',
            'singular_name' => 'Video'
        ],
        'menu_icon' => 'dashicons-videos',
        'supports' => ['title','editor','thumbnail','revisions','custom-fields'],
        'has_archive' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'can_export' => true,
        'has_archive' => true
    ]);
}


add_action('init','rmvideos_post_types');

