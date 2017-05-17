<?php
/**
 * The included arrays work in two different ways.
 * 
 * @param $one_to_ones is written with the request URI as the key
 * and the complete URL as the value. These must be written from
 * specific to generic since the loop does a generic check using
 * the `strpos` function; therefore, URIs that are more generic 
 * should be written after the more specific ones matching a 
 * similar path.
 * @param $regex_rules is written using regular expressions with
 * the regex as the key and the full URL with regex replacements
 * as the value. If the regex value is matched, the specified
 * replacement will be will be redirected.
 * @internal note that the request URI starts with a forward slash
 * @internal note that regular expressions in PHP start and end with
 * forward slashes, so forward slashes need to be escaped. Similarly
 * other characters that are regex pattern delimiters must be escaped.
 * @see http://php.net/manual/en/regexp.reference.delimiters.php
 */

  // one-to-one redirects
  $one_to_ones = array(
    "/path/to/page" => "https://www.example.org/page/"
  );

  // regex-based redirects
  $regex_rules = array(
    "/\/festival\/(\d+)\/(\d+)\/(.*).php$/"      => "https://www.example.org/events/$3",
    "/\/your-visit\/your-visit\/(.*).php$/"      => "https://www.example.org/visit/$1",
    "/\/about\/(.*).php$/"                       => "https://www.example.org/about/$1",
    "/\/education\/school\/programs\/(.*).php$/" => "https://www.example.org/school/$1"
  );
