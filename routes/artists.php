<?php
include_once __DIR__ . '/route.php';
include_once __DIR__ . '/../models/artist.php';
include_once __DIR__ . '/../utilities/helpers.php';


class ArtistsRoute extends Route
{

  private const COLLECTION =  'artists';
  private const SUBCOLLECTION = 'albums';
  private $artist;

  public function __construct()
  {
    parent::__construct(true);
    $this->artist = new Artist();
    $this->handle_request(false);
  }

  protected function handle_get()
  {
    // artists/
    if ($this->is_collection_request()) {
      $results = $this->artist->get_all_artists();
      echo json_encode($results);
      return;
    }

    // artists/{id}/
    if ($this->is_resource_request()) {
      $artist_id = intval($this->path_params[$this::COLLECTION]);
      $result = $this->artist->get_artist($artist_id);
      echo json_encode($result);
      return;
    }

    // if($this->is_sub_collection_request()){
    //   $artist_id = intval($this->path_params[$this::COLLECTION]);
    //   $result = $this->artist->get_artists_abums($artist_id);
    //   echo json_encode($result);
    //   return;
    // }
    return $this->uri_not_found();
  }

  protected function handle_post()
  {
    if (!$this->has_admin_status()) return;

    $is_put_request = isset($this->body['ArtistId']);

    if ($is_put_request) {
      $results = $this->artist->update_artist($this->body);
    } else {
      $results = $this->artist->create_artist($this->body);
    }

    echo json_encode($results);
    return;
  }

  protected function handle_put()
  {
    return $this->method_not_allowed();
  }

  protected function handle_delete()
  {
    if (!$this->has_admin_status()) return;

    $album_id = intval($this->path_params[$this::COLLECTION]);
    $results = $this->artist->delete_artist($album_id);
    echo $results;
    return;
  }
}
