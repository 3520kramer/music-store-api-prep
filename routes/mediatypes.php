<?php
include_once __DIR__ . '/route.php';
include_once __DIR__ . '/../models/mediatype.php';

class MediaTypesRoute extends Route
{

  private const COLLECTION =  'mediatypes';
  private const SUBCOLLECTION = '';
  private $mediatype;

  public function __construct()
  {
    parent::__construct(true);
    $this->mediatype = new MediaType();
    $this->handle_request(false);
  }

  protected function handle_get()
  {
    // mediatypes/
    if ($this->is_collection_request()) {

      $results = $this->mediatype->get_all_mediatypes();
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
