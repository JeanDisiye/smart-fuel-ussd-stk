<?php
$phone = $_POST['phone']; // e.g., 254712345678
$amount = $_POST['amount']; // e.g., 100

$BusinessShortCode = '174379'; // Your Paybill or BuyGoods number
$Passkey = 'bfb279f9aa9bdbcf15e97dd71a467cd2';
$ConsumerKey = '10t2pb38KaKLltr5Cd0J6b9hPfitKIecyJxRx9MIUOVOvADe';
$ConsumerSecret = 'OwOz0DJAhAKzfnxDXyWah5LSGivZ5Kw5vfku7Nv8bzjEeAc4pCz5lLfCKrT5oq3f';
$PartyA = $phone;
$AccountReference = 'FuelTopUp';
$TransactionDesc = 'Fuel Purchase';
$CallBackURL = 'https://your-localtunnel-url.loca.lt/ussd/callback.php'; // replace with your LT URL

// 1. Get access token
$credentials = base64_encode("$ConsumerKey:$ConsumerSecret");
$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
$token = json_decode($response)->access_token;
curl_close($curl);

// 2. Prepare STK push
$timestamp = date('YmdHis');
$password = base64_encode($BusinessShortCode.$Passkey.$timestamp);

$stkpush_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $stkpush_url);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
  'Content-Type: application/json',
  'Authorization: Bearer ' . $token
]);

$data = [
  'BusinessShortCode' => $BusinessShortCode,
  'Password' => $password,
  'Timestamp' => $timestamp,
  'TransactionType' => 'CustomerPayBillOnline',
  'Amount' => $amount,
  'PartyA' => $PartyA,
  'PartyB' => $BusinessShortCode,
  'PhoneNumber' => $PartyA,
  'CallBackURL' => $CallBackURL,
  'AccountReference' => $AccountReference,
  'TransactionDesc' => $TransactionDesc
];

curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
curl_close($curl);

echo "STK Push Sent! Response: " . $response;
?>