<?php
header('Content-Type: application/json');

// Your Flutterwave SECRET key
$secret_key = "FLWSECK_TEST-xxxxxxxxxxxxxxxxxx-X";

if (!isset($_POST['transaction_id'])) {
    echo json_encode(["status" => "error", "message" => "No transaction ID"]);
    exit;
}

$transaction_id = $_POST['transaction_id'];

// Verify payment via Flutterwave API
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/$transaction_id/verify",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer $secret_key",
    "Content-Type: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
  echo json_encode(["status" => "error", "message" => "cURL Error: $err"]);
} else {
  $result = json_decode($response, true);

  if ($result['status'] === "success" && $result['data']['status'] === "successful") {
    echo json_encode([
      "status" => "success",
      "message" => "Payment verified successfully!",
      "amount" => $result['data']['amount'],
      "customer_email" => $result['data']['customer']['email']
    ]);
  } else {
    echo json_encode(["status" => "error", "message" => "Payment not successful"]);
  }
}
