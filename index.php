<?php

$pathToRecipient = __DIR__ . '/recipients/recipient.json';
$recipientTxt = file_get_contents($pathToRecipient);
$recipients = json_decode($recipientTxt,true);


if('recipients/recipient' === $_SERVER['PATH_INFO']) {

} elseif ('/recipients/customers' === $_SERVER['PATH_INFO']) {

}