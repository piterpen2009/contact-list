<?php
/**
 * Функция логирует текстовое сообщение
 *
 * @param string $errMsg - сообщение о ошибке
 */
function loggerInFile ( string $errMsg):void
{
    file_put_contents(__DIR__ . '/app.log',"{$errMsg}\n", FILE_APPEND);
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
 * @param array $validateParameters - валидируемые параметры, ключ имя параметра, а значение это текст сообщения о ошибке
 * @param array $params - все множество параметров
 * @return array - сообщение о ошибках
 */
function paramTypeValidation(array $validateParameters, array $params):?array
{
    $result = null;
    foreach ($validateParameters as $paramName => $errorMessage) {
        if (array_key_exists($paramName, $params) && false === is_string($params[$paramName])) {
            $result = [
                'httpCode' => 500,
                'result' => [
                    'status' => 'fail',
                    'message'=> $errorMessage
                ]
            ];
            break;
        }
    }
    return $result;
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
 * @param $request array - параметры которые передаёт пользователь
 * @param $logger callable - параметр инкапсулирующий логгирование
 * @return array - возвращает результат поиска по знакомым
 */
function findRecipient(array $request, callable $logger):array
{
    $recipients = loadData('recipient');
    $logger('dispatch "recipient" url');

    $paramValidations = [
        'id_recipient' => 'incorrect id_recipient',
        'full_name' =>'incorrect full_name',
        'birthday' => 'incorrect birthday',
        'profession' => 'incorrect profession'
    ];

    if(null === ($result = paramTypeValidation($paramValidations, $request))) {
        $foundRecipients = [];
        $recipientIDToInfo = [];
        foreach ($recipients as $recipientInfo) {
            $recipientIDToInfo[$recipientInfo['id_recipient']] = $recipientInfo;
        }
        foreach ($recipients as $recipient) {
            if (array_key_exists('id_recipient', $request)) {
                $recipientMeetSearchCriteria = $request['id_recipient'] == (string)$recipientIDToInfo[$recipient['id_recipient']]['id_recipient'];
            } elseif (array_key_exists('full_name', $request)) {
                $recipientMeetSearchCriteria = $request['full_name'] === $recipientIDToInfo[$recipient['id_recipient']]['full_name'];
            } elseif (array_key_exists('birthday', $request)) {
                $recipientMeetSearchCriteria = $request['birthday'] === $recipientIDToInfo[$recipient['id_recipient']]['birthday'];
            } elseif (array_key_exists('profession', $request)) {
                $recipientMeetSearchCriteria = $request['profession'] === $recipientIDToInfo[$recipient['id_recipient']]['profession'];
            } else {
                $recipientMeetSearchCriteria = true;
            }
            if ($recipientMeetSearchCriteria) {
                $foundRecipients[] = $recipientIDToInfo[$recipient['id_recipient']];
            }
        }
        $logger('found recipients not category: ' . count($foundRecipients));
        return [
            'httpCode' => 200,
            'result' => $foundRecipients
        ];
    }
    return $result;
}

/**
 * Функция поиска клиента по id или full_name
 * @param $request array - параметры которые передаёт пользователь
 * @param $logger callable - параметр инкапсулирующий логгирование
 * @return array - возвращает результат поиска по авторам
 */

function findCustomers(array $request, callable $logger):array
{
    $customers = loadData('customers');
    $logger('dispatch "customers" url');
    $paramValidations = [
        'id_recipient' => 'incorrect id_recipient',
        'full_name' =>'incorrect full_name',
        'birthday' => 'incorrect birthday',
        'profession' => 'incorrect profession',
        'contract_number' => ' incorrect contract_number',
        'average_transaction_amount' => 'incorrect average_transaction_amount',
        'discount' => 'incorrect discount',
        'time_to_call' => 'incorrect time_to_call'
    ];

    if(null === ($result = paramTypeValidation($paramValidations, $request))) {
        $foundCustomers =[];
        foreach ($customers as $customer) {
            if (array_key_exists('id_recipient', $request)) {
                $customerMeetSearchCriteria = $request['id_recipient'] === (string)$customer['id_recipient'];
            } elseif (array_key_exists('full_name', $request)) {
                $customerMeetSearchCriteria = $request['full_name'] === $customer['full_name'];
            } elseif (array_key_exists('birthday', $request)) {
                $customerMeetSearchCriteria = $request['birthday'] === $customer['birthday'];
            } elseif (array_key_exists('profession', $request)) {
                $customerMeetSearchCriteria = $request['profession'] === $customer['profession'];
            } elseif (array_key_exists('contract_number', $request)) {
                $customerMeetSearchCriteria = $request['contract_number'] === $customer['contract_number'];
            } elseif (array_key_exists('average_transaction_amount', $request)) {
                $customerMeetSearchCriteria = $request['average_transaction_amount'] === (string)$customer['average_transaction_amount'];
            } elseif (array_key_exists('discount', $request)) {
                $customerMeetSearchCriteria = $request['discount'] === $customer['discount'];
            } elseif (array_key_exists('time_to_call', $request)) {
                $customerMeetSearchCriteria = $request['time_to_call'] === $customer['time_to_call'];
            } else {
                $customerMeetSearchCriteria = true;
            }
            if ($customerMeetSearchCriteria) {
                $foundCustomers = $customer;
            }
        }
        $logger('found customers not category: ' . count($foundCustomers));
        return [
            'httpCode' => 200,
            'result' => $foundCustomers
        ];
    }
    return $result;
}

/**
 * Функция поиска контакттов по категории
 * @param $request array - параметры которые передаёт пользователь
 * @param $logger callable - параметр инкапсулирующий логгирование
 * @return array - возвращает результат поиска по категориям
 */
function findContactOnCategory(array $request, callable $logger):array
{
    $customers = loadData('customers');
    $recipients = loadData('recipient');
    $kinsfolk = loadData('kinsfolk');
    $colleagues = loadData('colleagues');

    $logger('dispatch "category" url');

    if (!array_key_exists('category', $request)) {
        return [
            'httpCode' => 500,
            'result' => [
                'status' => 'fail',
                'message' => 'empty category'
            ]
        ];
    }
        if ($request['category'] === 'customers') {
            $logger('dispatch category "customers"');
            $foundRecipientsOnCategory = $customers;
            $logger('found customers: '. count($foundRecipientsOnCategory));
        } elseif ($request['category'] === 'recipients') {
            $logger('dispatch category "recipients"');
            $foundRecipientsOnCategory = $recipients;
            $logger('found customers: '. count($foundRecipientsOnCategory));
        } elseif ($request['category'] === 'kinsfolk') {
            $logger('dispatch category "kinsfolk"');
            $foundRecipientsOnCategory = $kinsfolk;
            $logger('found kinsfolk: '. count($foundRecipientsOnCategory));
        } elseif ($request['category'] === 'colleagues') {
            $logger('dispatch category "colleagues"');
            $foundRecipientsOnCategory = $colleagues;
            $logger('found colleagues: '. count($foundRecipientsOnCategory));
        } else {
            return [
                'httpCode' => 500,
                'result' => [
                    'status' => 'fail',
                    'message' => 'dispatch category nothing'
                ]
            ];
        }
        return [
            'httpCode' => 200,
            'result' => $foundRecipientsOnCategory
        ];
}

/** Функция реализации веб приложения
 *
 * @param $requestUri string - URI запроса
 * @param $request array - параметры которые передаёт пользователь
 * @param $logger callable - параметр инкапсулирующий логгирование
 * @return array
 */
function app (string $requestUri, array $request, callable $logger):array
{
    $logger('Url request received' . $requestUri);
    $urlPath = parse_url($requestUri, PHP_URL_PATH);

    if('/recipient' === $urlPath) {
        $result = findRecipient($request, $logger);
    } elseif ('/customers' === $urlPath) {
        $result = findCustomers($request, $logger);
    } elseif ('/category' === $urlPath) {
        $result = findContactOnCategory($request, $logger);
    } else {
        $result = [
            'httpCode' => 404,
            'result' => [
                'status' => 'fail',
                'message' => 'unsupported request'
            ]
        ];
        $logger($result['result']['message']);
    }
    return $result;
}
$resultApp = app
(
    $_SERVER['REQUEST_URI'],
    $_GET,
    'loggerInFile'
);
render($resultApp['result'], $resultApp['httpCode']);