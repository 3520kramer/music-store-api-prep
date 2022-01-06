<?php

include_once __DIR__ . '/../database/database.php';


class MediaType extends Database
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_all_mediatypes()
  {
    $query = <<< SQL
      SELECT * FROM mediatype
    SQL;

    $results = $this->get_all($query);
    return $results;
  }
}
