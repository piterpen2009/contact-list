<?php
/**
 * Функция логирует текстовое сообщение
 *
 * @param string $errMsg - сообщение о ошибке
 */
function logger ( string $errMsg):void
{
    file_put_contents(__DIR__ . '/app.log',"{$errMsg}\n", FILE_APPEND);
}
/**
 *
 *
 * @param string $message - сообщение о причине ошибке
 * @param int $httpCode - http code
 * @param string $status - статус ошибки
 */
function errorHandling(string $message, int $httpCode, string $status): void
{
    $result = [
        'status' => $status,
        'message' => $message
    ];
    logger($message);
    render($result, $httpCode);
}

/**
 * Функция вывода данных
 *
 * @param array $data - данные, которые хотим отобразить
 * @param int $httpCode - http code
 */
function render(array $data, int $httpCode)
{
    header('Content-Type: application/json');
    http_response_code($httpCode);
    echo json_encode($data);
    exit();
}

/**
 * Функция валидации
 *
 * @param string $paramName - имя параметра
 * @param array $param - имя запроса
 * @param string $errorMessage - сообщение об ошибке
 */
function paramTypeValidation(string $paramName, array $param, string $errorMessage)
{
    if (array_key_exists($paramName, $param) && false === is_string($param[$paramName])) {
        errorHandling($errorMessage, 500, 'fail');
    }
}

/** Функция перводит данные из json формата в php и возвращает содержимое
 *
 * @param string $sourceName - имя файла
 * @return array - содержимое json файла
 */
function loadData (string $sourceName):array
{
    $pathToFile = __DIR__ . "/rec/{$sourceName}.json";
    $content = file_get_contents($pathToFile);
    return json_decode($content, true);
}

/**
 * Функция поиска знакомых по id или full_name
 * @return array - выводит результат поиска по знакомым
 */
function findRecipient():array
{
    $recipients = loadData('recipient');
    $httpCode = 200;
    $result = [];
    logger('dispatch "recipient" url');
    paramTypeValidation('id_recipient', $_GET, 'incorrect id_recipient');
    paramTypeValidation('full_name', $_GET, 'incorrect full_name');

    $recipientIDToInfo = [];
    foreach ($recipients as $recipientInfo) {
        $recipientIDToInfo[$recipientInfo['id_recipient']] = $recipientInfo;
    }
    foreach ($recipients as $recipient) {
        if (array_key_exists('id_recipient', $_GET)) {
            $recipientMeetSearchCriteria = $_GET['id_recipient'] == (string)$recipientIDToInfo[$recipient['id_recipient']]['id_recipient'];
        } elseif (array_key_exists('full_name', $_GET)) {
            $recipientMeetSearchCriteria = $_GET['full_name'] === $recipientIDToInfo[$recipient['id_recipient']]['full_name'];
        } elseif (array_key_exists('birthday', $_GET)) {
            $recipientMeetSearchCriteria = $_GET['birthday'] === $recipientIDToInfo[$recipient['id_recipient']]['birthday'];
        } elseif (array_key_exists('profession', $_GET)) {
            $recipientMeetSearchCriteria = $_GET['profession'] === $recipientIDToInfo[$recipient['id_recipient']]['profession'];
        } else {
            $recipientMeetSearchCriteria = true;
        }
        if ($recipientMeetSearchCriteria) {
            $result[] = $recipientIDToInfo[$recipient['id_recipient']];
        }
    }
    logger('found recipients not category: '. count($result));
    return [
        'httpCode' => $httpCode,
        'result' => $result
    ];

}

/**
 * Функция поиска клиента по id или full_name
 * @return array - выводит результат поиска по клиентам
 */
function findCustomers():array
{
    $customers = loadData('customers');
    logger('dispatch "customers" url');
    $httpCode = 200;
    $result = [];

    paramTypeValidation('id_recipient', $_GET, 'incorrect id_recipient');
    paramTypeValidation('full_name', $_GET, 'incorrect full_name');

    foreach ($customers as $customer) {
        if (array_key_exists('id_recipient', $_GET)) {
            $customerMeetSearchCriteria = $_GET['id_recipient'] === (string)$customer['id_recipient'];
        } elseif (array_key_exists('full_name', $_GET)) {
            $customerMeetSearchCriteria = $_GET['full_name'] === $customer['full_name'];
        } elseif (array_key_exists('birthday', $_GET)) {
            $customerMeetSearchCriteria = $_GET['birthday'] === $customer['birthday'];
        } elseif (array_key_exists('profession', $_GET)) {
            $customerMeetSearchCriteria = $_GET['profession'] === $customer['profession'];
        } elseif (array_key_exists('contract_number', $_GET)) {
            $customerMeetSearchCriteria = $_GET['contract_number'] === $customer['contract_number'];
        } elseif (array_key_exists('average_transaction_amount', $_GET)) {
            $customerMeetSearchCriteria = $_GET['average_transaction_amount'] === (string)$customer['average_transaction_amount'];
        } elseif (array_key_exists('discount', $_GET)) {
            $customerMeetSearchCriteria = $_GET['discount'] === $customer['discount'];
        } elseif (array_key_exists('time_to_call', $_GET)) {
            $customerMeetSearchCriteria = $_GET['time_to_call'] === $customer['time_to_call'];
        } else {
            $customerMeetSearchCriteria = true;
        }
        if ($customerMeetSearchCriteria) {
            $result[] = $customer;
        }
    }
    logger('found customers not category: '. count($result) );
    return [
        'httpCode' => $httpCode,
        'result' => $result
    ];
}

/**
 * Функция поиска контакттов по категории
 * @return array - результат поиска по категориям
 */
function findContactOnCategory():array
{
    $customers = loadData('customers');
    $recipients = loadData('recipient');
    $kinsfolk = loadData('kinsfolk');
    $colleagues = loadData('colleagues');
    $httpCode = 200;
    $result = [];
    $categoryList = ['customers','kinsfolk','colleagues','recipients'];
    logger('dispatch "category" url');
    if (array_key_exists('category', $_GET)) {
        if ($_GET['category'] === 'customers') {
            logger('dispatch category "customers"');
            $result = $customers;
            logger('found customers: '. count($result));
        } elseif ($_GET['category'] === 'recipients') {
            logger('dispatch category "recipients"');
            $result = $recipients;
            logger('found customers: '. count($result));
        } elseif ($_GET['category'] === 'kinsfolk') {
            logger('dispatch category "kinsfolk"');
            $result = $kinsfolk;
            logger('found kinsfolk: '. count($result));
        } elseif ($_GET['category'] === 'colleagues') {
            logger('dispatch category "colleagues"');
            $result = $colleagues;
            logger('found colleagues: '. count($result));
        } else {
            errorHandling('dispatch category nothing', 500,'fail');
        }
    }
    return [
        'httpCode' => $httpCode,
        'result' => $result
    ];
}

/** Функция реализации веб приложения
 *
 * @return array
 */

function app ():array
{
    logger('Url request received' . $_SERVER['REQUEST_URI']);
    $pathInfo = array_key_exists('PATH_INFO', $_SERVER) && $_SERVER['PATH_INFO'] ? $_SERVER['PATH_INFO'] : '';

    if('/recipient' === $pathInfo) {
        $result = findRecipient();
    } elseif ('/customers' === $pathInfo) {
        $result = findCustomers();
    } elseif ('/category' === $pathInfo) {
        $result = findContactOnCategory();
    } else {
        errorHandling('unsupported request', 404,'fail');
    }
    return $result;
}
$resultApp = app();
render($resultApp['result'], $resultApp['httpCode']);