<?php
$pathToRecipient = __DIR__ . '/rec/recipient.json';
$recipientTxt = file_get_contents($pathToRecipient);
$recipients = json_decode($recipientTxt,true);

$pathToCustomer = __DIR__ . '/rec/customers.json';
$customerTxt = file_get_contents($pathToCustomer);
$customers = json_decode($customerTxt,true);

$pathToKinsfolk = __DIR__ . '/rec/kinsfolk.json';
$kinsfolkTxt = file_get_contents($pathToKinsfolk);
$kinsfolk = json_decode($kinsfolkTxt,true);

$pathToColleagues = __DIR__ . '/rec/colleagues.json.json';
$colleaguesTxt = file_get_contents($pathToColleagues);
$colleagues = json_decode($colleaguesTxt,true);

if('/recipient' === $_SERVER['PATH_INFO']) {
    $httpCode = 200;
    $result = [];

    $recipientIDToInfo = [];
    foreach ($recipients as $recipientInfo) {
        $recipientIDToInfo[$recipientInfo['id_recipient']] = $recipientInfo;
    }
    foreach ($recipients as $recipient) {
        if(array_key_exists('id_recipient', $_GET)) {
            $recipientMeetSearchCriteria = $_GET['id_recipient'] == $recipientIDToInfo[$recipient['id_recipient']]['id_recipient'];
        } elseif (array_key_exists('full_name', $_GET)) {
            $recipientMeetSearchCriteria = $_GET['full_name'] == $recipientIDToInfo[$recipient['id_recipient']]['full_name'];
        } elseif (array_key_exists('birthday', $_GET)) {
            $recipientMeetSearchCriteria = $_GET['birthday'] == $recipientIDToInfo[$recipient['id_recipient']]['birthday'];
        } elseif (array_key_exists('profession', $_GET)) {
            $recipientMeetSearchCriteria = $_GET['profession'] == $recipientIDToInfo[$recipient['id_recipient']]['profession'];
        } else {
            $recipientMeetSearchCriteria = true;
        }

        if ($recipientMeetSearchCriteria) {
            $result[] = $recipientIDToInfo[$recipient['id_recipient']];
        }
    }
} elseif ('/customers' === $_SERVER['PATH_INFO']) {
    $httpCode = 200;
    $result = [];

    foreach ($customers as $customer) {
        if(array_key_exists('id_recipient', $_GET)) {
            $customerMeetSearchCriteria = $_GET['id_recipient'] == $customer['id_recipient'];
        } elseif (array_key_exists('full_name', $_GET)) {
            $customerMeetSearchCriteria = $_GET['full_name'] == $customer['full_name'];
        } elseif (array_key_exists('birthday', $_GET)) {
            $customerMeetSearchCriteria = $_GET['birthday'] == $customer['birthday'];
        } elseif (array_key_exists('profession', $_GET)) {
            $customerMeetSearchCriteria = $_GET['profession'] == $customer['profession'];
        } elseif (array_key_exists('contract_number', $_GET)) {
            $customerMeetSearchCriteria = $_GET['contract_number'] == $customer['contract_number'];
        } elseif (array_key_exists('average_transaction_amount', $_GET)) {
            $customerMeetSearchCriteria = $_GET['average_transaction_amount'] == $customer['average_transaction_amount'];
        } elseif (array_key_exists('discount', $_GET)) {
            $customerMeetSearchCriteria = $_GET['discount'] == $customer['discount'];
        } elseif (array_key_exists('time_to_call', $_GET)) {
            $customerMeetSearchCriteria = $_GET['time_to_call'] == $customer['time_to_call'];
        } else {
            $customerMeetSearchCriteria = true;
        }
        if ($customerMeetSearchCriteria) {
            $result[] = $customer;
        }
    }

} elseif ('/category' === $_SERVER['PATH_INFO']) {
    $httpCode = 200;
    $result = [];
    if (array_key_exists('category', $_GET)) {
        if ($_GET['category'] === 'customers') {
            $result = $customers;
        } elseif ($_GET['category'] === 'recipients') {
            $result = $recipients;
        } elseif ($_GET['category'] === 'kinsfolk') {
            $result = $kinsfolk;
        } elseif ($_GET['category'] === 'colleagues') {
            $result = $colleagues;
        }
    }

}
header('Content-Type: application/json');
http_response_code($httpCode);
echo json_encode($result);
