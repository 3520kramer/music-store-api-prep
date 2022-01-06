<?php
include_once __DIR__ . '/../environment/my-env.php';

/*
https://phpdelusions.net/pdo#query
Another useful mode is PDO::FETCH_CLASS, which can create an object of particular class
$news = $pdo->query('SELECT * FROM news')->fetchAll(PDO::FETCH_CLASS, 'News');
*/
class Database
{
  protected $db;

  public function __construct()
  {
    $this->db = $this->connect();
  }

  public function __destruct()
  {
    $this->db = null;
  }


  public function connect()
  {
    $host = ENV::$DB_HOST;
    $port = ENV::$DB_PORT;
    $db = ENV::$DB;
    $user = ENV::$DB_USER;
    $pwd = ENV::$DB_PWD;
    $charset = ENV::$DB_CHARSET;


    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
    $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false
    ];

    try {
      $pdo = new PDO($dsn, $user, $pwd, $options);
    } catch (\PDOException $e) {
      echo $e->getMessage();
      return;
    }
    return $pdo;
  }

  public function db_disconnect()
  {
    $this->db = null;
  }

  public function get_all(string $query, array $params = null, bool $reuse_params = false)
  {
    try {
      if ($params) {
        if ($reuse_params) $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        $stmt = $this->db->prepare($query);

        $stmt->execute($params);

        $results = $stmt->fetchAll();

        return $results;
      } else {

        $stmt = $this->db->query($query);

        $results = $stmt->fetchAll();

        return $results;
      }
    } catch (Exception $e) {
      echo "ERROR fetching: \n$e";
    } finally {
      if ($reuse_params) $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
      // $this->db_disconnect();
    }
  }

  // Overloading is not possible in PHP - use if-else instead
  public function get_one(string $query, array $params = null, bool $reuse_params = false)
  {
    try {
      if ($params) {
        if ($reuse_params) $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        $stmt = $this->db->prepare($query);

        $stmt->execute($params);

        $results = $stmt->fetch();
        return $results;
      } else {

        $stmt = $this->db->query($query);

        $results = $stmt->fetch();

        return $results;
      }
    } catch (Exception $e) {
      echo "ERROR fetching: \n$e";
    } finally {
      if ($reuse_params) $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
      // $this->db_disconnect();
    }
  }

  public function create(string $query, array $params): bool
  {
    try {
      $stmt = $this->db->prepare($query);
      return $stmt->execute($params);

      // $stmt->execute($params);
      // $results = $stmt->fetch();
      // return $results;

    } catch (Exception $e) {
      echo "ERROR fetching: \n$e";
      return false;
    } finally {
      // $this->db_disconnect();
    }
  }

  public function update(string $query, array $params)
  {
    try {
      $stmt = $this->db->prepare($query);
      return $stmt->execute($params);

      //echo $stmt;
      // $stmt->execute($params);
      // $results = $stmt->fetch();
      // return $results;

    } catch (Exception $e) {
      echo "ERROR fetching: \n$e";
    } finally {
      // $this->db_disconnect();
    }
  }

  public function delete(string $query, array $params)
  {
    try {
      $stmt = $this->db->prepare($query);
      $stmt->execute($params);
      $deleted = $stmt->rowCount();
      return $deleted;
      //echo $stmt;
      // $stmt->execute($params);
      // $results = $stmt->fetch();
      // return $results;

    } catch (Exception $e) {
      echo "ERROR fetching: \n$e";
    } finally {
      // $this->db_disconnect();
    }
  }
}
