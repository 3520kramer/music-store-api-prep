<?php

include_once __DIR__ . '/helpers.php';
/* 
  CREDITS: https://roytuts.com/how-to-generate-and-validate-jwt-using-php-without-using-third-party-api/
*/
class Authenticator
{
	// const ADMIN = 'admin';
	// const USER = 'user';
	// const USER_WITH_ID = 'user_with_id';

	// private $role;
	protected $is_valid;
	protected $customer_id;
	protected $is_admin;
	// private $is_customer;

	public function __construct(bool $validate_now)
	{
		if ($validate_now) {
			$this->validate_token();
		}
	}

	public function __get($property)
	{
		switch ($property) {
			case 'is_valid':
				return boolval($this->is_valid);
			case 'customer_id':
				return $this->customer_id;
			case 'is_admin':
				return boolval($this->is_admin);
			default:
				echo "error";
		}
	}

	public function __set($property, $value)
	{
		switch ($property) {
			case 'is_valid':
				return $this->is_valid = $value;
			case 'customer_id':
				return $this->customer_id = $value;
			case 'is_admin':
				return $this->is_admin = $value;
			default:
				echo "error";
		}
	}

	public static function generate_jwt(array $payload, string $secret = 'secret', int $expire_in = 600000): string
	{
		$headers = array('alg' => 'HS256', 'typ' => 'JWT');
		$headers_encoded = base64url_encode(json_encode($headers));

		$payload['exp'] = (time() + $expire_in);
		$payload_encoded = base64url_encode(json_encode($payload));

		$signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
		$signature_encoded = base64url_encode($signature);

		$jwt = "$headers_encoded.$payload_encoded.$signature_encoded";

		return $jwt;
	}

	// public function is_allowed_access(string $role, int $customer_id_allowed = null)
	// {
	// 	if ($role === $this->USER_WITH_ID) {
	// 	}

	// 	if ($role === $this->USER) {
	// 	}

	// 	if ($role === $this->ADMIN) {
	// 	}
	// }

	public function validate_token()
	{
		$headers = apache_request_headers();
		$auth_header = $headers['Authorization'] ?? null;
		
		if (is_null($auth_header) || !$this->is_jwt_valid($auth_header)) {
			$this->is_valid = false;
			return;
		}

		$payload = $this->get_jwt_payload($auth_header);

		$this->is_valid = true;
		$this->is_admin = $payload['is_admin'];
		$this->customer_id = $payload['customer_id'];
	}

	// split the jwt and decode payload
	private function get_jwt_payload(string $jwt = null)
	{
		$token_parts = explode('.', $jwt);
		$payload = base64_decode($token_parts[1]);
		return json_decode($payload, true);
	}

	private function is_jwt_valid(string $jwt, string $secret = 'secret'): bool
	{
		// doesn't accept authorize header without Bearer
		if (!str_starts_with($jwt, 'Bearer ')) return false;

		// remove Bearer from the jwt
		$jwt = substr($jwt, 7);

		// split the jwt and decode
		$token_parts = explode('.', $jwt);
		$header = base64_decode($token_parts[0]);
		$payload = base64_decode($token_parts[1]);
		$signature_provided = $token_parts[2];

		// check the expiration time - note: this will cause an error if there is no 'exp' claim in the jwt
		$expiration = json_decode($payload)->exp;
		$is_token_expired = ($expiration - time()) < 0;

		// build a signature based on the header and payload using the secret
		$base64_url_header = base64url_encode($header);
		$base64_url_payload = base64url_encode($payload);
		$signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, $secret, true);
		$base64_url_signature = base64url_encode($signature);

		// verify it matches the signature provided in the jwt
		$is_signature_valid = ($base64_url_signature === $signature_provided);

		if ($is_token_expired || !$is_signature_valid) {
			return false;
		} else {
			return true;
		}
	}
}

/* ***** *********************************/
/* ***** *********************************/
/* ***** *********************************/
/* ***** *********************************/
/* ***** *********************************/

// function base64url_encode($str)
// {
// 	return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
// }

// // Default secret and expiration (in seconds)
// function generate_jwt(array $payload, string $secret = 'secret', int $expire_in = 600000): string
// {
// 	$headers = array('alg' => 'HS256', 'typ' => 'JWT');
// 	$headers_encoded = base64url_encode(json_encode($headers));

// 	$payload['exp'] = (time() + $expire_in);
// 	$payload_encoded = base64url_encode(json_encode($payload));

// 	$signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
// 	$signature_encoded = base64url_encode($signature);

// 	$jwt = "$headers_encoded.$payload_encoded.$signature_encoded";

// 	return $jwt;
// }

// function validate_token(string $jwt, string $secret = 'secret'): bool
// {
// 	// doesn't accept authorize header without Bearer
// 	if (!str_starts_with($jwt, 'Bearer ')) return false;

// 	// remove Bearer from the jwt
// 	$jwt = substr($jwt, 7);

// 	// split the jwt and decode
// 	$token_parts = explode('.', $jwt);
// 	$header = base64_decode($token_parts[0]);
// 	$payload = base64_decode($token_parts[1]);
// 	$signature_provided = $token_parts[2];

// 	// check the expiration time - note: this will cause an error if there is no 'exp' claim in the jwt
// 	$expiration = json_decode($payload)->exp;
// 	$is_token_expired = ($expiration - time()) < 0;

// 	// build a signature based on the header and payload using the secret
// 	$base64_url_header = base64url_encode($header);
// 	$base64_url_payload = base64url_encode($payload);
// 	$signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, $secret, true);
// 	$base64_url_signature = base64url_encode($signature);

// 	// verify it matches the signature provided in the jwt
// 	$is_signature_valid = ($base64_url_signature === $signature_provided);

// 	if ($is_token_expired || !$is_signature_valid) {
// 		return false;
// 	} else {
// 		return true;
// 	}
// }

// // split the jwt and decode payload
// function get_jwt_payload(string $jwt = null)
// {
// 	if (is_null($jwt)) {
// 	}
// 	$token_parts = explode('.', $jwt);
// 	$payload = base64_decode($token_parts[1]);
// 	return json_decode($payload, true);
// }

// /* Check if the token is not expired or has wrong signature */
// /* Optional: if given a customer id, the jwt will be valid i.e. return true for routes 
// for that particular customer and user with is_admin true
// /* Optional: check if token is valid for an admin */
// function is_jwt_valid(int $customer_id = null, bool $check_for_admin_status = false): bool
// {
// 	$headers = apache_request_headers();
// 	$auth_header = $headers['Authorization'] ?? null;

// 	if (is_null($auth_header) || !validate_token($auth_header)) {
// 		unauthorized_response();
// 		return false;
// 	}

// 	$payload = get_jwt_payload($auth_header);

// 	// will allow any jwt with admin status access
// 	if ($payload['is_admin']) {
// 		return true;
// 	}

// 	// will restrict access to anyone not an admin - if check for admin status is true
// 	if ($check_for_admin_status && !$payload['is_admin']) {
// 		unauthorized_response();
// 		return false;
// 	}

// 	// will restrict access if customer_id doesn't match
// 	if (!is_null($customer_id) && $customer_id !== $payload['customer_id']) {
// 		unauthorized_response();
// 		return false;
// 	}

// 	return true;
// }

// function unauthorized_response()
// {
// 	http_response_code(401);
// 	echo 'Not authorized';
// }
