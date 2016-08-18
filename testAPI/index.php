<?php

header("content-type: text/html; charset=utf-8");

require_once 'models/Bet.php';

$app = new Bet;

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    if ($_GET['search'] == 'all') {
        $betData = $app->selectAll();
    }

    if ($_GET['search'] == 'single') {
        $id = addslashes($_GET['id']);
        $betData = $app->selectContent($id);
    }

    if (!isset($_GET['search'])) {
        header("HTTP/1.0 404 Not found");
        echo "404 Not found";
        exit;
    }

    if (!$betData) {
        header("HTTP/1.0 404 Not found");
        echo "404 Not found";
        exit;
    }

    $requestContentType = $_SERVER['HTTP_ACCEPT'];

    if (strpos($requestContentType,'application/json') !== false) {
        echo json_encode($betData,JSON_UNESCAPED_UNICODE);
    }

    if (strpos($requestContentType,'text/html') !== false) {
        echo json_encode($betData,JSON_UNESCAPED_UNICODE);
    }

    if(strpos($requestContentType,'application/xml') !== false){
        $response = encodeXml($betData);
        echo $response;
    }
}

function encodeXml($responseData) {
    $xml = new SimpleXMLElement('<?xml version="1.0"?><site></site>');
    foreach($responseData as $value) {
        foreach ($value as $key=>$content) {
            $xml->addChild($key, $content);
        }
    }
    return $xml->asXML();
}
