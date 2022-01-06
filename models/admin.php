<?php

include_once __DIR__ . '/../database/database.php';

class Admin extends Database {

  function check_password(string $password){
    $query = <<<SQL
      SELECT * from `admin`;
    SQL;

    $admin = $this->get_one($query);
    
    $result = password_verify($password, $admin['Password']);

    return $result;
  }
}