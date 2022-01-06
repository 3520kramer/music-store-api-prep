<?php
include_once __DIR__ . '/../database/database.php';

class Invoice extends Database
{
  public function __construct()
  {
    parent::__construct();
  }

  /* 
  Once:
  CustomerId, db adress or from form?, total,

  Multiple:
  trackid, price, quantity(1)
  */

  function get_invoice(string $invoice_id, Authenticator $auth)
  {    
    $query = <<<SQL
      SELECT invoice.InvoiceId, invoice.InvoiceDate, invoice.BillingAddress, invoice.BillingCity, 
        invoice.BillingState, invoice.BillingCountry, invoice.BillingPostalCode, invoice.Total,
          CONCAT(customer.FirstName, ' ', customer.LastName) AS CustomerName, track.Name AS TrackName, track.UnitPrice
      FROM invoice
      JOIN invoiceline USING(InvoiceId)
      JOIN track USING(TrackId)
      JOIN customer USING(CustomerId)
      WHERE InvoiceId = :id
      SQL;

    $params = ['id' => $invoice_id];

    if(!$auth->is_admin){
      $query .= ' AND customer.CustomerId = :customer_id';
      $params['customer_id'] = $auth->customer_id;
    }    

    $results = $this->get_all($query, $params);

    if(count($results) < 1) {
      echo "No results";
      return;
    };

    $invoice_info = $results[0];
    unset($invoice_info['TrackName'], $invoice_info['UnitPrice']);

    $tracks = array_map(function ($result) {
      return [
        'TrackName' => $result['TrackName'],
        'UnitPrice' => $result['UnitPrice']
      ];
    }, $results);

    return ['invoiceInfo' => $invoice_info, 'tracks' => $tracks];
  }

  function create_invoice(array $data): mixed
  {
    // Frontend sends as array
    // Postman sends as string
    $ids = is_string($data['trackIds']) ? $data['trackIds'] : implode(',', $data['trackIds']);

    if (!preg_match('/^[0-9]+(,[0-9]+)*$/', $ids)) {
      echo "Id's not good";
      return null;
    }

    try {
      $this->db->beginTransaction();

      $sum_query = <<< SQL
        SELECT SUM(UnitPrice) as sum FROM track WHERE TrackId IN($ids)
      SQL;

      $tracks_sum = $this->get_one($sum_query)['sum'];

      $invoice_query = <<< SQL
        INSERT INTO invoice
          (CustomerId, InvoiceDate, BillingAddress, BillingCity,
            BillingState, BillingCountry, BillingPostalCode, Total)
        VALUES
        (:customer_id, NOW(), :address, :city, :state, :country, :postal_code, :total);
        SQL;

      $params = [
        'customer_id' => $data['customerId'],
        'address' => $data['address'],
        'city' => $data['city'],
        'state' => $data['state'],
        'country' => $data['country'],
        'postal_code' => $data['postalCode'],
        'total' => $tracks_sum
      ];

      $this->create($invoice_query, $params);

      $invoice_id = $this->db->lastInsertId();

      $invoice_line_query = <<<SQL
        INSERT INTO invoiceline (InvoiceId, TrackId, UnitPrice, Quantity)
        SELECT :invoice_id, track.trackId, track.UnitPrice, 1
        FROM track
        WHERE track.TrackId in ($ids);
        SQL;

      $params = ['invoice_id' => $invoice_id];

      $this->create($invoice_line_query, $params);
      $this->db->commit();
    } catch (Exception $e) {
      return $this->db->rollBack();
    }
    return ['invoice_id' => $invoice_id];
  }
}
