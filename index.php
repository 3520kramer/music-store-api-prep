<?php
include_once __DIR__ . '/utilities/helpers.php';
include_once __DIR__ . '/utilities/authenticate.php';

include_once __DIR__ . '/routes/auth.php';
include_once __DIR__ . '/routes/tracks.php';
include_once __DIR__ . '/routes/albums.php';
include_once __DIR__ . '/routes/artists.php';
include_once __DIR__ . '/routes/invoices.php';
include_once __DIR__ . '/routes/customers.php';
include_once __DIR__ . '/routes/search.php';
include_once __DIR__ . '/routes/mediatypes.php';
include_once __DIR__ . '/routes/genres.php';

include_once __DIR__ . '/environment/my-env.php';

// Initializing the static class with environment variables
Env::set_env_vars(__DIR__);

$url = get_url();

// Show the API description if path is '/' i.e. only one item in array 
if (count($url) === 1) {
  echo 'Show API description';
  return;
}

// Sets the headers of the responses
header('Content-Type: application/json');
header('Accept-version: v1');
header("Access-Control-Allow-Origin: ". ENV::$HOST);

/* AUTHORISAITION */
$headers = apache_request_headers();
$auth_header = $headers['Authorization'] ?? null;

// Router
switch ($url[1]) {
  case 'search':
    new SearchRoute();
    break;
  case 'albums':
    new AlbumsRoute();
    break;
  case 'artists':
      new ArtistsRoute();
      break;
  case 'tracks':
    new TracksRoute();
    break;
  case 'invoices':
    new InvoiceRoute();
    break;
  case 'customers':
    new CustomerRoute();
    break;
  case 'auth':
    new AuthRoute();
    break;
  case 'mediatypes':
    new MediaTypesRoute();
    break;
  case 'genres':
    new GenresRoute();
    break;
  default:
    // Show bad format message if it's not the right collection
    echo 'Only employees and departments collection is accessible - BAD FORMAT',  '<br>';
}
