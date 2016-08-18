<?php

header("content-type: text/html; charset=utf-8");

require_once 'models/Bet.php';

$app = new Bet;

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));

if ($method == 'GET') {
    if (is_numeric($request[0])) {
        $betData = $app->selectContent($request[0]);
    } elseif ($request[0] == '') {
        $betData = $app->selectAll();
    } else {
        header("HTTP/1.0 404 Not found");
        echo "404 Not found";
        exit;
    }

    if (!$betData) {
        header("HTTP/1.0 404 Not found");
        echo "404 Not found";
        exit;
    }

    echo json_encode($betData,JSON_UNESCAPED_UNICODE);
}
