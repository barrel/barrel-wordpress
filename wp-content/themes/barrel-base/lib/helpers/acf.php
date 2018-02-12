<?php

/**
 * Test to see if all fields are populated
 * @param mixed $fields,... Variable number of field names can be passed in to check
 * @return bool
 */
function fields_not_empty() {
  $fields = func_get_args();

  foreach ( $fields as $field ) {
    $value = get_field( $field );
    if ( empty( $value ) ) {
      return false;
    }
  }


  return true;
}

/**
 * Test to see if all sub-fields are populated
 * @param mixed $fields,... Variable number of field names can be passed in to check
 * @return bool
 */
function subfields_not_empty() {
  $fields = func_get_args();

  foreach ( $fields as $field ) {
    $value = get_sub_field( $field );
    if ( empty( $value ) ) {
      return false;
    }
  }

  return true;
}


function get_link_type($prefix, $function) {
  return $function($prefix.'_link_type');
}

function get_link_url($prefix, $function, $type = null) {
  $link_type = $type ?: get_link_type($prefix, $function);

  if ($link_type == 'internal') {
    return $function($prefix.'_page_link');
  } elseif ($link_type == 'external') {
    return $function($prefix.'_external_link');
  }
}

function get_link_text($prefix, $function, $name = 'link') {
  $link_text = $function($prefix.'_'.$name.'_text');
}

function get_link_fields($prefix, $function, $name = 'link') {
  $type = get_link_type($prefix, $function);
  $target = ($type === 'external') ? 'target="_blank"' : '';
  $url = get_link_url($prefix, $function, $type);
  $text = get_link_text($prefix, $function, $name);

  return array(
    'link_type' => $type,
    'link_target' => $target,
    'link_url' => $url,
    'link_text' => $text,
  );
}
