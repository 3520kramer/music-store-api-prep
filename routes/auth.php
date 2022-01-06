<?php
include_once __DIR__ . '/route.php';

include_once __DIR__ . '/../utilities/authenticate.php';
include_once __DIR__ . '/../utilities/helpers.php';
include_once __DIR__ . '/../models/admin.php';
include_once __DIR__ . '/../models/customer.php';

class AuthRoute extends Route
{

  private const COLLECTION =  'auth';
  private const SUBCOLLECTION = '';

  private const CUSTOMER = 'customer';
  private const ADMIN = 'admin';


  public function __construct()
  {
    parent::__construct(true);
    $this->handle_request(false);
  }

  protected function handle_get()
  {
    return $this->method_not_allowed();
  }

  protected function handle_post()
  {
    $email = $this->body['username'] ?? null;
    $pass = $this->body['password'] ?? null;

    if (!$email || !$pass) return $this->bad_request();

    $jwt_payload = [];

    if ($this->path_params[$this::COLLECTION] === $this::CUSTOMER) {
      $customer = new Customer();
      $customer_info = $customer->check_password($email, $pass);

      if (!$customer_info) return $this->unauthorized_response();

      $jwt_payload['is_admin'] = false; // might not be needed
      $jwt_payload['customer_id'] = $customer_info['CustomerId'];
      $jwt_payload['first_name'] = $customer_info['FirstName'];
      $jwt_payload['last_name'] = $customer_info['LastName'];
    }
    if ($this->path_params[$this::COLLECTION] === $this::ADMIN) {
      $admin = new Admin();
      $is_admin = $admin->check_password($pass);

      if (!$is_admin) return $this->unauthorized_response();

      $jwt_payload['customer_id'] = 0;
      $jwt_payload['is_admin'] = true;
      $jwt_payload['first_name'] = $this::ADMIN;
    }
    $jwt = Authenticator::generate_jwt($jwt_payload);
    $json = json_encode(['token' => $jwt]);
    echo $json;
    return;
  }

  protected function handle_put()
  {
    return $this->method_not_allowed();
  }

  protected function handle_delete()
  {
    return $this->method_not_allowed();
  }
}

function authentication_route()
{

  $req_method = $_SERVER['REQUEST_METHOD'];

  // auth/
  switch ($req_method) {
    case 'GET':
      break;
    case 'POST':
      $email = $_POST['username'] ?? null;
      $pass = $_POST['password'] ?? null;

      if (!$email || !$pass) {
        http_response_code(400);
        echo json_encode('Error');
        return;
      }

      $jwt_payload = [];

      // check if admin is in url
      if (url_get_last_element() === 'user') {
        $customer = new Customer();
        $customer_info = $customer->check_password($email, $pass);

        if (!$customer_info) {
          echo 'invalid password';
          return;
        }

        $jwt_payload['is_admin'] = false; // might not be needed
        $jwt_payload['customer_id'] = $customer_info['CustomerId'];
        $jwt_payload['first_name'] = $customer_info['FirstName'];
        $jwt_payload['last_name'] = $customer_info['LastName'];
      } else {
        $admin = new Admin();
        $is_admin = $admin->check_password($pass);

        if (!$is_admin) {
          echo 'invalid password';
          return;
        }
        $jwt_payload['customer_id'] = 0;
        $jwt_payload['is_admin'] = true;
      };

      $jwt = Authenticator::generate_jwt($jwt_payload);
      $json = json_encode(['token' => $jwt]);
      echo $json;

      break;
    case 'DELETE':
      break;
    default:
      echo 'ERROR';
  }
  if ($req_method !== 'POST') {
    echo 'ERROR';
    return;
  }

  // $user = $_POST['username'] ?? null;
  // $pass = $_POST['password'] ?? null;

  // #echo $user . $pass . '<br>';

  // $jwt = generate_jwt(array($user, $pass));
  // echo $jwt;

}
