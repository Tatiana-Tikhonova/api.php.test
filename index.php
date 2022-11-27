<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Credentials: true');
header('Allow: GET, POST, PUT, DELETE');
header('Accept: application/json');
header('Content-Type: application/json');

require __DIR__ . '/autoload.php';

use App\Models\ErrorHandler;
use App\Controller;
use App\MyException;

$err = ErrorHandler::unique();

if (0 === strlen($_SERVER['QUERY_STRING'])) {

    $errors = [
        'message' => 'Incorrect endpoint'
    ];
    echo \json_encode($err->handler(403, $errors));
    die;
}

$params = explode('/', $_GET['query']);
$endpoint = $params[0];
$id = $params[1] ?? '';
$data = [];
$method = $_SERVER['REQUEST_METHOD'];

if ('POST' === $_SERVER['REQUEST_METHOD']) {
    $data = $_POST;
}
if ('PUT' === $_SERVER['REQUEST_METHOD']) {
    $data = \json_decode(file_get_contents('php://input'), true);
}

$allowedEndpoints = ['posts', 'users'];

if (in_array($endpoint, $allowedEndpoints)) {
    try {
        new Controller($endpoint, $method, $id, $data);
    } catch (MyException $e) {
        $msg = $e->getMessage();
        if ('Not found' == $msg) {
            $error = [
                'message' => 'Not found'
            ];
            echo \json_encode($err->handler(404, $error));
        } elseif ('User already exists' == $msg) {
            $error = [
                'message' => 'User already exists'
            ];
            echo \json_encode($err->handler(400, $error));
        } else {
            $error = [
                'message' => 'Server error'
            ];
            echo \json_encode($err->handler(500, $error));
        }
    }
} else {
    $error = [
        'message' => 'Incorrect endpoint',
        'field' => $endpoint
    ];

    echo \json_encode($err->handler(404, $error));
}
