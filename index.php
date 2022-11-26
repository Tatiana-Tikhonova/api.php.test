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
use App\Controllers\Controller;


$err = ErrorHandler::unique();

if (0 === strlen($_SERVER['QUERY_STRING'])) {

    $errors = [
        "message" => "Incorrect endpoint"
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
    parse_str(file_get_contents("php://input"), $data);
}

$allowedEndpoints = ['posts', 'users'];

if (in_array($endpoint, $allowedEndpoints)) {
    new Controller($endpoint, $method, $id, $data);
} else {
    $errors = [
        "message" => "Incorrect endpoint",
        "field" => $endpoint
    ];

    echo \json_encode($err->handler(404, $errors));
}
