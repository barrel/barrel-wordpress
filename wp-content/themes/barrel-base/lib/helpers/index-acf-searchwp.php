<?php

add_filter( 'searchwp_custom_fields', function ( $custom_field_value, $custom_field_name, $the_post ) {
    $post_id = $the_post->ID;
    $has_redirect = get_post_meta($post_id, 'redirect_event_page', true);
    $redirect_type = get_post_meta($post_id, 'event_redirect_link_type', true);
    $link_id = (int) get_post_meta($post_id, 'event_page_link', true);

    // index the custom field value if this is not an event, or if it doesn't have an event_page_link field
    if ( $custom_field_name !== 'event_page_link' || !$has_redirect || $redirect_type !== 'internal' || !$link_id ) {
      return $custom_field_value;
    }

    // index the title and content of the linked post
    $linked_post = get_post( $link_id );
        $content_to_index = $linked_post->post_title . ' ' . $linked_post->post_content;

    return $content_to_index;
  }
);
