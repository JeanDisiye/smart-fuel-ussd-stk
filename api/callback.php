<?php
header("Content-Type: application/json");

$stkCallback = file_get_contents('php://input');
$log = fopen("stk_callback_log.txt", "a");
fwrite($log, $stkCallback . "\n");
fclose($log);

$data = json_decode($stkCallback, true);

if (isset($data['Body']['stkCallback'])) {
    $resultCode = $data['Body']['stkCallback']['ResultCode'];
    $phone = $data['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'] ?? '';
    $amount = $data['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'] ?? '';

    if ($resultCode == 0) {
        // Payment successful, store in database or update balance
        file_put_contents("success_log.txt", "Payment of $amount from $phone\n", FILE_APPEND);
    }
}
?>