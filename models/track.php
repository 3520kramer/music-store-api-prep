<?php

include_once __DIR__ . '/../database/database.php';
include_once __DIR__ . '/../utilities/image_fetch.php';

class Track extends Database
{
  private $image_fetch;

  public function __construct()
  {
    parent::__construct();
    $this->image_fetch = new ImageUrlFetch();
  }

  public function get_all_tracks()
  {
    $query = <<< SQL
      SELECT * FROM track LIMIT 5
    SQL;

    $results = $this->get_all($query);
    return $results;
  }

  public function get_track($id)
  {
    $query = <<< SQL
      SELECT track.TrackId AS trackId, track.Name AS trackTitle, 
        track.Composer AS trackComposer, track.Milliseconds AS trackTime, 
        track.Bytes AS trackSize, track.UnitPrice AS trackPrice, 
        genre.GenreId as trackGenreId, genre.name AS trackGenre, 
        mediatype.MediaTypeId as trackMediaTypeId, mediatype.Name AS trackMediaType,
        album.AlbumId AS albumId, album.Title AS albumName, 
        artist.ArtistId AS artistId, artist.Name AS artistName
      FROM track
      JOIN album USING(AlbumId)
      JOIN artist USING(ArtistId)
      JOIN genre USING(GenreId)
      JOIN mediatype USING(MediaTypeId)
      WHERE Trackid = :id
    SQL;

    $params = ['id' => $id];
    $results = $this->get_one($query, $params);
    // var_dump($results);
    $results['imgUrl'] = $this->image_fetch->get_album_art_url($results['albumName'], 'big');

    return $results;
  }

  public function get_tracks($ids)
  {
    // only allow digits and commas
    if(!preg_match('/^[0-9]+(,[0-9]+)*$/', $ids)){
      echo "no match";
      return;
    }

  $query = <<< SQL
      SELECT track.TrackId AS trackId, track.Name AS trackTitle, 
        track.Composer AS trackComposer, track.Milliseconds AS trackTime, 
        track.Bytes AS trackSize, track.UnitPrice AS trackPrice, 
        genre.name AS trackGenre, mediatype.Name AS trackMediaType,
        album.AlbumId AS albumId, album.Title AS albumName, 
        artist.ArtistId AS artistId, artist.Name AS artistName
      FROM track
      JOIN album USING(AlbumId)
      JOIN artist USING(ArtistId)
      JOIN genre USING(GenreId)
      JOIN mediatype USING(MediaTypeId)
      WHERE Trackid in ($ids)
    SQL;

    $results = $this->get_all($query);

    $results = $this->add_image_urls($results);

    return $results;
  }


  public function create_track($track)
  {
    $query = <<< SQL
      INSERT INTO `track` (
        `Name`, `AlbumId`, `MediaTypeId`, `GenreId`, 
        `Composer`, `Milliseconds`, `Bytes`, `UnitPrice`)
      VALUES ( 
        :Name, :AlbumId, :MediaTypeId, :GenreId, 
        :Composer, :Milliseconds, :Bytes, :UnitPrice);
    SQL;
    
    $is_success = $this->create($query, $track);
    return $is_success;
  }


  public function update_track($track)
  {
    $query = <<< SQL
      UPDATE `track`
      SET `Name` = :Name, `AlbumId` = :AlbumId, `MediaTypeId` = :MediaTypeId, `GenreId` = :GenreId, 
        `Composer` = :Composer, `Milliseconds` = :Milliseconds, `Bytes` = :Bytes, `UnitPrice` = :UnitPrice
      WHERE `TrackId` = :TrackId;
    SQL;

    $is_success = $this->update($query, $track);
    return $is_success;
  }

  public function delete_track($id)
  {
    $query = <<< SQL
      DELETE FROM `track` WHERE Trackid = :id
    SQL;
    
    $params = ['id' => $id];
    $is_success = $this->delete($query, $params);
    return $is_success;
  }

  private function add_image_urls(array $array): array
  {
    $array = array_map(function ($result) use ($array){
      $result['imgUrl'] = $this->image_fetch->get_album_art_url($result['albumName']);
      return $result;
    }, $array);
    return $array;
  }
}
