<?php

include_once __DIR__ . '/../database/database.php';


class Genre extends Database
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_all_genres()
  {
    $query = <<< SQL
      SELECT * FROM genre
    SQL;

    $results = $this->get_all($query);
    return $results;
  }
}
