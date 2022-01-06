<?php

include_once __DIR__ . '/route.php';
include_once __DIR__ . '/../models/customer.php';
include_once __DIR__ . '/../models/invoice.php';

class CustomerRoute extends Route
{

  private const COLLECTION =  'customers';
  private const SUBCOLLECTION = 'invoices';
  private $customer;

  public function __construct()
  {
    parent::__construct(true);
    $this->customer = new Customer();
    $this->handle_request(false);
  }

  protected function handle_get()
  {
    // customers/
    if ($this->is_collection_request()) {
      return $this->uri_not_found();
    }

    // customers/{id}/
    if ($this->is_resource_request()) {
      $customer_id = intval($this->path_params[$this::COLLECTION]);

      if ($this->is_customer_allowed($customer_id)) {
        $results = $this->customer->get_customer($customer_id);
        echo json_encode($results);
        return;
      }
      return $this->unauthorized_response();
    }

    // customers/{id}/invoices/{id}/
    if ($this->is_sub_resource_request()) {
      $invoice = new Invoice();
      $invoice_id = $this->path_params[$this::SUBCOLLECTION];
      $customer_id = intval($this->path_params[$this::COLLECTION]);

      if ($this->is_customer_allowed($customer_id)) {
        $results = $invoice->get_invoice($invoice_id, $this->auth);
        echo json_encode($results);
        return;
      }
      return $this->unauthorized_response();
    }
    return $this->uri_not_found();
  }

  protected function handle_post()
  {
    $customer_id = $this->body['CustomerId'] ?? null;
    $is_put_request = isset($customer_id);

    if ($is_put_request) {
      if ($this->is_customer_allowed($customer_id)) {
        $results = $this->customer->update_customer($this->body);
      }
    } else {
      $results = $this->customer->create_customer($this->body);
    }

    echo json_encode($results);
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
