<?php

include_once __DIR__ . '/../database/database.php';
include_once __DIR__ . '/../utilities/image_fetch.php';

class Search extends Database
{
  public function search(string $search)
  {
    $query = <<< SQL
      SELECT track.TrackId AS 'id', track.Name AS 'trackName', artist.Name AS 'artistName', album.Title AS 'albumName', 'track' AS type 
      FROM track
      JOIN album USING(AlbumId)
      JOIN artist USING(ArtistId) 
      WHERE track.Name LIKE :search
      UNION
      SELECT artist.ArtistId AS 'id', null AS 'trackName', artist.Name AS 'artistName', null AS 'albumName', 'artist' AS type 
      FROM artist 
      WHERE artist.Name LIKE :search
      UNION
      SELECT album.AlbumId AS 'id', null AS 'trackName', artist.Name AS 'artistName', album.Title AS 'albumName', 'album' AS type 
      FROM album 
      JOIN artist USING(ArtistId)
      WHERE album.Title LIKE :search
    SQL;

    $search = "$search%";
    $params = ['search' => $search];

    $results = $this->get_all($query, $params, true);
    
    $results = $this->format_results($results);
    
    $results = $this->add_image_urls($results);

    return $results;
  }

  private function format_results(array $query_results): array
  {
    $formatted_results = array(
      'tracks' => array(),
      'artists' => array(),
      'albums' => array()
    );

    foreach ($query_results as $result) {
      if ($result['type'] === 'track') {
        array_push($formatted_results['tracks'], $result);
      } else if ($result['type'] === 'artist') {
        array_push($formatted_results['artists'], $result);
      } else {
        array_push($formatted_results['albums'], $result);
      }
    }

    return $formatted_results;
  }

  private function add_image_urls(array $search_results)
  {
    $image_fetch = new ImageUrlFetch();
    
    if (!empty($search_results['tracks'])) {
      $search_results['tracks'] = array_map(function ($result) use ($image_fetch) {
        $result['imgUrl'] = $image_fetch->get_album_art_url($result['albumName']);
        return $result;
      }, $search_results['tracks']);
    }

    if (!empty($search_results['artists'])) {
      $search_results['artists'] = array_map(function ($result) use ($image_fetch) {
        $result['imgUrl'] = $image_fetch->get_artist_img_url($result['artistName']);
        return $result;
      }, $search_results['artists']);
    }

    if (!empty($search_results['albums'])) {
      $search_results['albums'] = array_map(function ($result) use ($image_fetch) {
        $result['imgUrl'] = $image_fetch->get_album_art_url($result['albumName']);
        return $result;
      }, $search_results['albums']);
    }

    return $search_results;
    
  }
}
