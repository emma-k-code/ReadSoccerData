<?php

header("content-type: text/html; charset=utf-8");

/**
 * Class Database
 * 資料庫相關方法
 */
require_once 'Database.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));

if ($method != 'GET') {
    return;
}

if ($request[0] == 'GET') {
    if (is_numeric($request[1])) {
        selectContent($request[1]);
    }

    if ($request[1] == '') {
        selectAll();
    }
}

function selectAll() {
    $db = new Database;
    $sql = "SELECT `bID`, `updateTime` FROM `betting`";
    $result = $db->prepare($sql);
    $result->execute();

    $betData = $result->fetchAll(PDO::FETCH_CLASS);
    echo json_encode($betData);
}

function selectContent($id) {
    $db = new Database;
    $sql = "SELECT * FROM `betting` WHERE `bID` = :id";
    $result = $db->prepare($sql);
    $result->bindParam('id', $id);
    $result->execute();

    $betData = $result->fetchAll(PDO::FETCH_CLASS);

    foreach ($betData as $bKey=>$value) {
        foreach ($value as $vKey=>$content) {
            if (is_string($content)) {
                $content = urlencode($content);
            }
        }
    }
    echo json_encode($betData,JSON_UNESCAPED_UNICODE);
}

