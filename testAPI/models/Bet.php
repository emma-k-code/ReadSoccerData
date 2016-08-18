<?php

/**
 * Class Database
 * 資料庫相關方法
 */
require_once 'Database.php';

class Bet extends Database
{
    public function selectAll() {
        $sql = "SELECT `bID`, `updateTime` FROM `betting`";
        $result = $this->prepare($sql);
        $result->execute();

        $betData = $result->fetchAll(PDO::FETCH_CLASS);

        return $betData;
    }

    public function selectContent($id) {
        $sql = "SELECT * FROM `betting` WHERE `bID` = :id";
        $result = $this->prepare($sql);
        $result->bindParam('id', $id);
        $result->execute();

        $betData = $result->fetchAll(PDO::FETCH_CLASS);

        return $betData;
    }
}
