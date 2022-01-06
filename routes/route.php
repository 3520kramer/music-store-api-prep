<?php

abstract class Route
{
  protected $auth;
  protected $url_array;
  protected $req_method;
  protected $query_params;
  protected $path_params;
  protected $body;

  protected const GET = 'GET';
  protected const POST = 'POST';
  protected const PUT = 'PUT';
  protected const DELETE = 'DELETE';

  abstract protected function handle_get();
  abstract protected function handle_post();
  abstract protected function handle_put();
  abstract protected function handle_delete();

  public function __construct(bool $validate_route)
  {
    $this->auth = new Authenticator($validate_route);
    $this->url_array = get_url(true);
    $this->req_method = $_SERVER['REQUEST_METHOD'];
    $this->query_params = $_GET;
    $this->path_params = $this->get_path_params();
    $this->body = $_POST;
  }

  protected function handle_request(bool $check_auth)
  {
    // Validate token
    if ($check_auth && !$this->auth->is_valid) {
      return $this->unauthorized_response();
    }

    switch ($this->req_method) {
      case $this::GET:
        $this->handle_get();
        break;

      case $this::POST:
        $this->handle_post();
        break;

      case $this::PUT:
        echo 'PLEASE USE POST - But include id';
        $this->handle_put();
        break;

      case $this::DELETE:
        $this->handle_delete();
        break;

      default:
        $this->method_not_allowed();
    }
  }

  // Get the params as key-value pair
  protected function get_path_params()
  {
    $params = [];
    $path_param_key = "";

    foreach ($this->url_array as $index => $path_element) {
      if ($index % 2 === 0 || $index === 0) {
        $path_param_key = $path_element;
        $params[$path_param_key] = null;
      } else {
        $params[$path_param_key] = $path_element;
      }
    }
    return $params;
  }

  // Only customer with certain id or admin is allowed 
  // note: admin is allowed on all routes
  protected function is_customer_allowed($id)
  {
    if ($this->auth->is_valid && (
          $this->auth->customer_id === intval($id) || $this->auth->is_admin)){
      return true;
    } else {
      $this->unauthorized_response();
      return false;
    }
  }

  // used to restrict other than admins access on certain routes
  protected function has_admin_status()
  {
    if ($this->auth->is_admin) {
      return true;
    } else {
      $this->unauthorized_response();
      return false;
    }
  }

  protected function is_collection_request()
  {
    return count($this->url_array) === 1 && empty($this->query_params);
  }

  protected function is_collection_query()
  {
    return count($this->url_array) === 1 && !empty($this->query_params);
  }

  protected function is_resource_request()
  {
    return count($this->url_array) === 2;
  }

  protected function is_sub_collection_request()
  {
    return count($this->url_array) === 3;
  }

  protected function is_sub_resource_request()
  {
    return count($this->url_array) === 4;
  }

  protected function bad_request()
  {
    return http_response_code(400);
  }

  protected function unauthorized_response()
  {
    return http_response_code(401);
  }

  protected function uri_not_found()
  {
    return http_response_code(404);
  }

  protected function method_not_allowed()
  {
    return http_response_code(405);
  }

  /* OLD */
  private function get_path_params2(string $route, string $subroute = null)
  {
    $params = [
      $route => $this->url_array[1] ?? null,
      $subroute => $this->url_array[3] ?? null
    ];
    return $params;
  }

  protected function has_path_params()
  {

    $path_array = $this->array_flatten($this->path_params);

    var_dump($path_array);

    // echo '<br>' . count($path_array);
    // if (count($this->path_params) > 1) {
    //   return true;
    // } else {
    //   return false;
    // }
    return count($path_array) > 1 ? true : false;
  }

  private function array_flatten($arr)
  {
    foreach ($arr as $arrkey => $arrval) {
      $arr_new[] = $arrkey;
      $arr_new[] = $arrval;
    }
    return $arr_new;
  }
}
