<?php

  include_once __DIR__ . '/../environment/my-env.php';

  class ImageUrlFetch {

    private $curl;
    private $curl_options;
    private $url_history;

    public function __construct()
    {
      $this->curl_options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER => [
          "x-rapidapi-host: " . ENV::$HOST,
          "x-rapidapi-key: " . ENV::$API_KEY//c6a784a7ef78c03a70c4791288292128" // NEEDS TO BE PRIVATE
        ],
      ];
      $this->url_history = array();
    }

    public function get_artist_img_url(string $artist, $img_size = "medium"){
      $artist = rawurlencode($artist);

      $url = "https://api.deezer.com/search?q=artist:\"$artist\"&limit=1";

      // Check if the url has already been called
      $img_url = $this->url_history[$url] ?? null;

      if($img_url) {
        return $img_url;
      }

      $res = $this->get($url);
      $img_url = $res['data'][0]['artist']["picture_$img_size"] ?? null;

      $this->url_history[$url] = $img_url;

      return $img_url;
    }

    public function get_album_art_url(string $album, $img_size = "medium"){
      $album = rawurlencode($album);
      $url = "https://api.deezer.com/search?q=album:\"$album\"&limit=1";

      // Check if the url has already been called
      $img_url = $this->url_history[$url] ?? null;

      if($img_url) {
        return $img_url;
      }

      $res = $this->get($url);
      
      $img_url = $res['data'][0]['album']["cover_$img_size"] ?? null;

      $url_history[$url] = $img_url;

      return $img_url;
    }

    private function get(string $url){
      $this->curl = curl_init($url);

      curl_setopt_array($this->curl, $this->curl_options);

      $response = curl_exec($this->curl);
      $err = curl_error($this->curl);
      curl_close($this->curl);

      if ($err) {
        echo "cURL Error #:" . $err;
      } else {
        return json_decode($response, true);
      }
    }
  }