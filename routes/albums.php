<?php
include_once __DIR__ . '/route.php';
include_once __DIR__ . '/../models/album.php';
include_once __DIR__ . '/../utilities/helpers.php';


class AlbumsRoute extends Route
{

  private const COLLECTION =  'albums';
  private const SUBCOLLECTION = 'tracks';
  private $album;

  public function __construct()
  {
    parent::__construct(true);
    $this->album = new Album();
    $this->handle_request(false);
  }

  protected function handle_get()
  {
    // albums/
    if ($this->is_collection_request()) {
      $results = $this->album->get_all_albums();
      echo json_encode($results);
      return;
    }

    // albums/{id}/
    if ($this->is_resource_request()) {
      $album_id = intval($this->path_params[$this::COLLECTION]);
      $result = $this->album->get_album($album_id);
      echo json_encode($result);
      return;
    }

    // albums?
    if ($this->is_collection_query()) {
      $artist_id = $this->query_params['artistId'] ?? null;

      if ($artist_id) {
        $results = $this->album->get_all_albums($artist_id);
        echo json_encode($results);
        return;
      } else {
        return $this->bad_request();
      }
    }
    // albums/{id}/tracks
    if($this->is_sub_collection_request()){
      $album_id = intval($this->path_params[$this::COLLECTION]);
      $result = $this->album->get_album_with_tracks($album_id);
      echo json_encode($result);
    }
    return $this->uri_not_found();
  }

  protected function handle_post()
  {
    if (!$this->has_admin_status()) return;

    $is_put_request = isset($this->body['AlbumId']);

    if ($is_put_request) {
      $results = $this->album->update_album($this->body);
    } else {
      $results = $this->album->create_album($this->body);
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
    $album_id = intval($this->path_params[$this::COLLECTION]);
    $results = $this->album->delete_album($album_id);
    echo $results;
    return;
  }
}


function albums_route()
{
  // Get the request method
  $req_method = $_SERVER['REQUEST_METHOD'];

  $album = new Album();
  define('ROUTENAME', 'albums');
  
  $last_path_element = url_get_last_element();


  switch ($req_method) {
    case 'GET':
      // get all
      if ($last_path_element === ROUTENAME) {

        // TODO: pagination stuff
        $results = $album->get_all_albums();
      } else {
        $results = $album->get_album($last_path_element);
      }

      echo json_encode($results);

      break;

    case 'POST':
      // $is_put_request = has_id_field('TrackId');

      // if($is_put_request) {
      //   $results = $track->update_track($_POST);
      // }else{
      //   $results = $track->create_track($_POST);
      // }

      // echo json_encode($results);

      break;

    case 'PUT':
      echo 'PLEASE USE POST - But include id';
      break;

    case 'DELETE':
      // $id = url_get_last_element();
      // $results = $track->delete_track($id);
      // echo $results;
      break;

    default:
      echo 'Hit default in switch - error';
  }
}
