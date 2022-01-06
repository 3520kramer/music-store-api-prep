<?php
include_once __DIR__ . '/route.php';
include_once __DIR__ . '/../models/search.php';

class SearchRoute extends Route
{

  private const COLLECTION =  'search';
  private const SUBCOLLECTION = '';
  private $search;

  public function __construct()
  {
    parent::__construct(true);
    $this->search = new Search();
    $this->handle_request(false);
  }

  protected function handle_get()
  { 
    // search?
    if ($this->is_collection_query()) {
      $search_value = $this->query_params['value'] ?? null;

      if ($search_value) {
        $results = $this->search->search($search_value);
        echo json_encode($results);
        return;
      } else {
        return $this->bad_request();
      }
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
