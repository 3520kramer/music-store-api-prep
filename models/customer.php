<?php

include_once __DIR__ . '/../database/database.php';

class Customer extends Database
{
  public function __construct()
  {
    parent::__construct();
  }

  function get_customer(int $id){
    $query = <<< SQL
      SELECT CustomerId, FirstName, LastName, Company, Address, City, 
        State, Country, PostalCode, Phone, Fax, Email
      FROM customer
      WHERE CustomerId = :id
    SQL;

    $params = ['id' => $id];
    $results = $this->get_one($query, $params);
    return $results;
  }

  public function create_customer($customer)
  {
    $customer['Password'] = password_hash($customer['Password'], PASSWORD_DEFAULT);
    $query = <<< SQL
      INSERT INTO `customer` (
        `FirstName`, `LastName`, `Password`, `Company`, `Address`, `City`,
        `State`, `Country`, `PostalCode`, `Phone`, `Fax`, `Email`)
      VALUES ( 
        :FirstName, :LastName, :Password, :Company, :Address, :City,
        :State, :Country, :PostalCode, :Phone, :Fax, :Email);
    SQL;
    
    $is_success = $this->create($query, $customer);
    return $is_success;
  }

  public function update_customer($customer)
  {
    $query = <<< SQL
      UPDATE `customer`
      SET
      `FirstName` = :FirstName,
      `LastName` = :LastName,
      `Company` = :Company,
      `Address` = :Address,
      `City` = :City,
      `State` = :State,
      `Country` = :Country,
      `PostalCode` = :PostalCode,
      `Phone` = :Phone,
      `Fax` = :Fax,
      `Email` = :Email
      WHERE `CustomerId` = :CustomerId;
    SQL;

    $is_success = $this->update($query, $customer);
    return $is_success;
  }

  function check_password($email, $password)
  {
    $query = <<<SQL
      SELECT CustomerId, Password, FirstName, LastName FROM customer WHERE `Email` = :email;
    SQL;

    $params = ['email' => $email];
    $customer = $this->get_one($query, $params);

    if (!$customer) {
      echo 'No customer';
      return;
    }

    $result = password_verify($password, $customer['Password']);

    return $result ? $customer : null;
  }
}