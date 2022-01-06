<?php

include_once __DIR__ . '/../database/database.php';
include_once __DIR__ . '/../utilities/image_fetch.php';


class Artist extends Database
{
  private $image_fetch;

  public function __construct()
  {
    parent::__construct();
    $this->image_fetch = new ImageUrlFetch();
  }

  public function get_all_artists()
  {
    $query = <<< SQL
      SELECT * FROM artist
    SQL;

    $results = $this->get_all($query);
    return $results;
  }

  public function get_artist($id)
  {
    $query = <<< SQL
      SELECT * FROM artist WHERE ArtistId = :id
    SQL;
    $params = ['id' => $id];

    $result = $this->get_one($query, $params);

    return $result;
  }

  public function create_artist($artist)
  {
    $query = <<< SQL
      INSERT INTO `artist` (`Name`)
      VALUES (:Name)
    SQL;

    $is_success = $this->create($query, $artist);
    return $is_success;
  }

  public function update_artist($artist)
  {
    $query = <<< SQL
      UPDATE `artist`
      SET `Name` = :Name
      WHERE `ArtistId` = :ArtistId
    SQL;

    $is_success = $this->update($query, $artist);
    return $is_success;
  }

  public function delete_artist($id)
  {
    $query = <<< SQL
      DELETE FROM `artist` WHERE `ArtistId` = :id
    SQL;

    $params = ['id' => $id];
    $is_success = $this->delete($query, $params);
    return $is_success;
  }
}
