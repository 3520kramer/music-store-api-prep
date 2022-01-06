<?php

include_once __DIR__ . '/route.php';
include_once __DIR__ . '/../models/invoice.php';

class InvoiceRoute extends Route
{

  private const COLLECTION =  'invoices';
  private const SUBCOLLECTION = '';
  private $invoice;

  public function __construct()
  {
    parent::__construct(true);
    $this->invoice = new Invoice();
    $this->handle_request(true);
  }

  protected function handle_get()
  {
    // invoices/
    if ($this->is_collection_request()) {
      return $this->uri_not_found();
    }

    // invoices/{id}/
    if ($this->is_resource_request()) {
      $invoice_id = intval($this->path_params[$this::COLLECTION]);
      $result = $this->invoice->get_invoice($invoice_id, $this->auth);
      echo json_encode($result);
    }
    return $this->uri_not_found();
  }

  protected function handle_post()
  {
    $customer_id = $this->body['customerId'];
    if ($this->is_customer_allowed($customer_id)) {
      $invoice_id = $this->invoice->create_invoice($_POST);
      echo json_encode($invoice_id);
      return;
    }
    return $this->unauthorized_response();
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
