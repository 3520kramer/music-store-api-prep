<?php

// function get_url($index_root = __DIR__)
function get_url(bool $remove_first_element = false): array
{
  // Get url without parameters
  $url = strtok($_SERVER['REQUEST_URI'], "?");

  // Exclude the trailing slash from basedir if present
  $url = rtrim($url, '/');

  // Remove everything in the url which comes before the basedir
  // Allow the api to be deployed anywhere
  $url = substr($url, strpos($url, basename(ENV::$ROOT_DIR)));
  #$url = substr($url, strpos($url, basename($index_root)));

  // Split the array by '/'
  $url_pieces = explode('/', urldecode($url));

  if ($remove_first_element) {
    array_shift($url_pieces);
  }
    return $url_pieces;
}

/* PHP can't handle indices access array with negative index by default */
function url_get_path_element(int $position)
{
  $url_array = get_url();

  if ($position < 0) {
    return $url_array[count($url_array) - abs($position)];
  } else {
    return $url_array[$position];
  }
}

function url_get_last_element()
{
  $url = get_url();
  return end($url);
}

// PHP doesn't allow column names to be inserted into prepared statements
// This function will take care of checking if the input from the url params is valid
// Inspired by: 'Your Common Sense' @ https://stackoverflow.com/a/2543144/13799636 
function is_param_allowed($value, $allowed)
{
  $is_in_array = in_array($value, $allowed);

  if (!$is_in_array) {
    throw new InvalidArgumentException("Not allowed");
  }
}

// PHP does not handle PUT parameters explicitly. 
// For this reason, they must be read from the request body’s raw data
function get_put_body()
{
  return (array)json_decode(file_get_contents('php://input'), TRUE);
}

function has_id_field(string $id_field_name = 'id'): bool
{
  return isset($_POST[$id_field_name]) ? true : false;
}

function base64url_encode($str): string
{
  return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
}
