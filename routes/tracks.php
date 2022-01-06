<?php
include_once __DIR__ . '/route.php';
include_once __DIR__ . '/../models/track.php';
include_once __DIR__ . '/../utilities/helpers.php';

class TracksRoute extends Route
{

  private const COLLECTION =  'tracks';
  private const SUBCOLLECTION = '';
  private $track;

  public function __construct()
  {
    parent::__construct(false);
    $this->track = new Track();
    $this->handle_request(false);
  }

  protected function handle_get()
  {
    // tracks/
    if ($this->is_collection_request()) {
      $results = $this->track->get_all_tracks();
      echo json_encode($results);
      return;
    }

    // tracks/{id}/
    if ($this->is_resource_request()) {
      $track_id = intval($this->path_params[$this::COLLECTION]);
      $result = $this->track->get_track($track_id);

      echo json_encode($result);
      return;
    }

    // tracks?
    if ($this->is_collection_query()) {
      $ids = $this->query_params['ids'] ?? null;

      if (isset($ids)) {
        $results = $this->track->get_tracks($ids);
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
    if (!$this->has_admin_status()) return;

    $is_put_request = isset($this->body['TrackId']);

    if ($is_put_request) {
      $results = $this->track->update_track($this->body);
    } else {
      $results = $this->track->create_track($this->body);
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

    $track_id = intval($this->path_params[$this::COLLECTION]);
    $results = $this->track->delete_track($track_id);
    echo $results;
    return;
  }
}
