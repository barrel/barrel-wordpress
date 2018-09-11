<?php
/**
 * Initiates Zapier Catch Hook Webhook Trigger
 */

$base_url = "https://hooks.zapier.com/hooks/catch/540021/q8dsq1/";
$headers = array(
  'Cache-Control' => 'no-cache'
);
// load variables and redirect them to zapier
$options = $_POST + $_SERVER;

try {
  // make request
  $query = http_build_query( $options );
  $response = Requests::post($base_url, $headers, $options);
  print_r( $response->body );
} catch (Exception $ex) {
  echo $ex;
}
