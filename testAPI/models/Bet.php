<?php

/**
 * Class Database
 * 資料庫相關方法
 */
require_once 'Database.php';

class Bet extends Database
{
    /**
     * 取得資料庫中所的資料其id與更新時間
     *
     * @return array
     */
    public function selectAll() {
        $sql = "SELECT `bID`, `updateTime` FROM `betting`";
        $result = $this->prepare($sql);
        $result->execute();

        $betData = $result->fetchAll(PDO::FETCH_CLASS);

        return $betData;
    }

    /**
     * 取得資料庫中編號$id的資料
     *
     * @param string $id 資料id
     * @return array
     */
    public function selectContent($id) {
        $sql = "SELECT * FROM `betting` WHERE `bID` = :id";
        $result = $this->prepare($sql);
        $result->bindParam('id', $id);
        $result->execute();

        $betData = $result->fetchAll(PDO::FETCH_CLASS);

        return $betData;
    }
}
