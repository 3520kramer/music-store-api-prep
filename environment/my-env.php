<?php
// include local vars file if it exists = Development mode
// else config vars will be set when deployed
$local_vars_file_path = __DIR__ . '/local_vars.php';
if (file_exists($local_vars_file_path)) {
  include_once $local_vars_file_path;
}

class Env
{
  public static $HOST;
  public static $API_KEY;
  public static $DB_HOST;
  public static $DB_PORT;
  public static $DB;
  public static $DB_USER;
  public static $DB_PWD;
  public static $DB_CHARSET;
  public static $ROOT_DIR;

  public static function set_env_vars(string $root_dir)
  {
    static::$HOST = getenv('HOST');
    static::$API_KEY = getenv('API_KEY');
    static::$DB_HOST = getenv('DB_HOST');
    static::$DB_PORT = getenv('DB_PORT');
    static::$DB = getenv('DB');
    static::$DB_USER = getenv('DB_USER');
    static::$DB_PWD = getenv('DB_PWD');
    static::$DB_CHARSET = getenv('DB_CHARSET');
    static::$ROOT_DIR = $root_dir;
  }
}
