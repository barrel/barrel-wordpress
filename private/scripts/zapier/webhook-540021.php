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
  $ch = curl_init();

  curl_setopt( $ch, CURLOPT_URL, $base_url );
  curl_setopt( $ch, CURLOPT_POST, true );
  curl_setopt( $ch, CURLOPT_POSTFIELDS, $options );
  curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

  $response = curl_exec($ch);
  $body = json_decode( $response );

  curl_close ($ch);
  print_r( $body );
} catch (Exception $ex) {
  echo $ex;
}
