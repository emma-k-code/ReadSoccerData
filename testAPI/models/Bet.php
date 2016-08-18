<?php

/**
 * Class Database
 * 資料庫相關方法
 */
require_once 'Database.php';

class Bet
{
    public function selectAll() {
        $db = new Database;
        $sql = "SELECT `bID`, `updateTime` FROM `betting`";
        $result = $db->prepare($sql);
        $result->execute();

        $betData = $result->fetchAll(PDO::FETCH_CLASS);

        return $betData;
    }

    public function selectContent($id) {
        $db = new Database;
        $sql = "SELECT * FROM `betting` WHERE `bID` = :id";
        $result = $db->prepare($sql);
        $result->bindParam('id', $id);
        $result->execute();

        $betData = $result->fetchAll(PDO::FETCH_CLASS);

        return $betData;
    }
}
