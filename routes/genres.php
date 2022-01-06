<?php
include_once __DIR__ . '/route.php';
include_once __DIR__ . '/../models/genre.php';

function test(){
  echo "in test function";
}
class GenresRoute extends Route
{

  private const COLLECTION =  'genres';
  private const SUBCOLLECTION = '';
  private $genres;

  public function __construct()
  {
    parent::__construct(true);
    $this->genre = new Genre();
    $this->handle_request(false);
  }

  protected function handle_get()
  {
    // genres/
    if ($this->is_collection_request()) {

      $results = $this->genre->get_all_genres();
      echo json_encode($results);
      return;
    }
    return $this->uri_not_found();
  }

  protected function handle_post()
  {
    return $this->method_not_allowed();
  }

  protected function handle_put()
  {
    return $this->method_not_allowed();
  }

  protected function handle_delete()
  {
    return $this->method_not_allowed();
  }
}
