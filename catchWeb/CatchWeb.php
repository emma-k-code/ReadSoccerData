<?php

header("content-type: text/html; charset=utf-8");

/**
 * Class Database
 * 資料庫相關方法
 */
require_once 'Database.php';

class catchWeb extends Database
{
    /**
     * 解析網頁資料
     */
    public function resolveWeb()
    {

        shell_exec('phantomjs getWeb.js');

        $contents = file_get_contents('web/1.html');
        unlink('web/1.html');
        $today = explode('top.today_gmt = ',$contents);
        $today = substr($today[1],1,10);

        // 解析資料
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $test = $doc->loadHTML($contents);

        $xpath = new DOMXPath($doc);

        $entries = $xpath->query('//*[@id="game_table"]/tbody/tr');

        $i = 0;
        foreach ($entries as $key=>$entry) {
            $td = $xpath->query('./td', $entry);

            foreach ($td as $value) {
                $output[$i][] = trim($xpath->query(".", $value)->item(0)->nodeValue);
            }

            if (($key != 0) && (($key % 4) == 0)) {
                $i++;
            }
        }

        foreach ($output as $key=> $value) {
            if (mb_substr($value[1], 5, 2, 'utf-8') == '滚球') {
                $time = mb_substr($value[1], 0, 5, 'utf-8');
                $running = '滚球';
            } else {
                $time = $value[1];
                $running = '';
            }

            $datetime = $today . ' ' . $time;

            $event = explode(' ', $value[2]);
            $event = trim($event[0]) . '-' . trim(end($event));

            date_default_timezone_set('Asia/Taipei');
            $updateTime = date("Y-m-d H:i:s");

            $id = $this->checkBet($datetime, $running, $event, $value);

            if (is_numeric($id)) {
                $this->updateBet($id, $updateTime, $datetime, $running, $event, $value);
            } else {
                $this->insertBet($updateTime, $datetime, $running, $event, $value);
            }
        }
    }

    /**
     * 確認是否已有該賭盤
     *
     * @param string $datetime 賽事時間
     * @param string $running 是否為滾球
     * @param string $event 比賽隊伍
     * @param string $value 賭盤資料
     */
    private function checkBet($datetime, $running, $event, $value)
    {
        $sql = "SELECT * FROM `betting` " .
        "WHERE `datetime` = :date AND `league` = :league AND " .
        "`leagueEvents` = :event AND `runningBall` = :runinng";
        $result = $this->prepare($sql);
        $result->bindParam('date', $datetime);
        $result->bindParam('league', $value[0]);
        $result->bindParam('event', $event);
        $result->bindParam('runinng', $running);
        $result->execute();

        $betData = $result->fetchAll();
        foreach ($betData as $data) {

            if (is_numeric($value[3]) && is_numeric($data['allSingleA'])) {
                return $data['bID'];
            }

            if ((!(is_numeric($value[3]))) && (!(is_numeric($data['allSingleA'])))) {
                if (($value[5] != '') && ($data['allOverUnderA'] != '') && ($value[9] == '') && ($data['halfOverUnderA'] == '')) {
                    return $data['bID'];
                }

                if (($value[5] == '') && ($data['allOverUnderA'] == '') && ($value[9] != '') && ($data['halfOverUnderA'] != '')) {
                    return $data['bID'];
                }

                if (($value[5] != '') && ($data['allOverUnderA'] != '') && ($value[9] != '') && ($data['halfOverUnderA'] != '')) {
                    return $data['bID'];
                }

                if (($value[5] == '') && ($data['allOverUnderA'] == '') && ($value[9] == '') && ($data['halfOverUnderA'] == '')) {
                    return $data['bID'];
                }
            }
        }
    }

    /**
     * 更新賭盤
     *
     * @param string $id 資料庫中賭盤的ID
     * @param string $datetime 賽事時間
     * @param string $running 是否為滾球
     * @param string $event 比賽隊伍
     * @param string $value 賭盤資料
     */
    private function updateBet($id, $updateTime, $datetime, $running, $event, $value)
    {
        $sql = "UPDATE `betting` SET `updateTime` = :update, " .
        "`datetime` = :date, `league` = :league, " .
        "`leagueEvents` = :event, `runningBall` = :runinng, " .
        "`allSingleA` = :allSingleA, `allHandicapA` = :allHandicapA, " .
        "`allOverUnderA` = :allOverUnderA, `allOddEvenA` = :allOddEvenA, " .
        "`halfSingleA` = :halfSingleA, `halfHandicapA` = :halfHandicapA, " .
        "`halfOverUnderA` = :halfOverUnderA, `allSingleB` = :allSingleB, " .
        "`allHandicapB` = :allHandicapB, `allOverUnderB` = :allOverUnderB, " .
        "`allOddEvenB` = :allOddEvenB, `halfSingleB` = :halfSingleB, " .
        "`halfHandicapB` = :halfHandicapB, " .
        "`halfOverUnderB` = :halfOverUnderB, `allSingleD` = :allSingleD, " .
        "`halfSingleD` = :halfSingleD WHERE `bID` = :id";
        $sth = $this->prepare($sql);
        $sth->bindParam('id', $id);
        $sth->bindParam('update', $updateTime);
        $sth->bindParam('date', $datetime);
        $sth->bindParam('league', $value[0]);
        $sth->bindParam('event', $event);
        $sth->bindParam('runinng', $running);
        $sth->bindParam('allSingleA', $value[3]);
        $sth->bindParam('allHandicapA', $value[4]);
        $sth->bindParam('allOverUnderA', $value[5]);
        $sth->bindParam('allOddEvenA', $value[6]);
        $sth->bindParam('halfSingleA', $value[7]);
        $sth->bindParam('halfHandicapA', $value[8]);
        $sth->bindParam('halfOverUnderA', $value[9]);
        $sth->bindParam('allSingleB', $value[10]);
        $sth->bindParam('allHandicapB', $value[11]);
        $sth->bindParam('allOverUnderB', $value[12]);
        $sth->bindParam('allOddEvenB', $value[13]);
        $sth->bindParam('halfSingleB', $value[14]);
        $sth->bindParam('halfHandicapB', $value[15]);
        $sth->bindParam('halfOverUnderB', $value[16]);
        $sth->bindParam('allSingleD', $value[18]);
        $sth->bindParam('halfSingleD', $value[20]);
        $s = $sth->execute();
    }

    /**
     * 將賭盤資料寫入資料庫
     *
     * @param string $datetime 賽事時間
     * @param string $running 是否為滾球
     * @param string $event 比賽隊伍
     * @param string $value 賭盤資料
     */
    private function insertBet($updateTime, $datetime, $running, $event, $value)
    {
        $sql = "INSERT INTO `betting`(`updateTime`, `datetime`, `league`, " .
        "`leagueEvents`, `runningBall`, `allSingleA`, " .
        "`allHandicapA`, `allOverUnderA`, `allOddEvenA`, " .
        "`halfSingleA`, `halfHandicapA`, `halfOverUnderA`, " .
        "`allSingleB`, `allHandicapB`, `allOverUnderB`, " .
        "`allOddEvenB`, `halfSingleB`, `halfHandicapB`, `halfOverUnderB`, " .
        "`allSingleD`, `halfSingleD`) VALUES (:update, :date, :league, " .
        ":event, :runinng , :allSingleA, :allHandicapA, " .
        ":allOverUnderA, :allOddEvenA, :halfSingleA, :halfHandicapA, " .
        ":halfOverUnderA, :allSingleB, :allHandicapB, :allOverUnderB, " .
        ":allOddEvenB, :halfSingleB, :halfHandicapB, " .
        ":halfOverUnderB, :allSingleD, :halfSingleD)";
        $sth = $this->prepare($sql);
        $sth->bindParam('update', $updateTime);
        $sth->bindParam('date', $datetime);
        $sth->bindParam('league', $value[0]);
        $sth->bindParam('event', $event);
        $sth->bindParam('runinng', $running);
        $sth->bindParam('allSingleA', $value[3]);
        $sth->bindParam('allHandicapA', $value[4]);
        $sth->bindParam('allOverUnderA', $value[5]);
        $sth->bindParam('allOddEvenA', $value[6]);
        $sth->bindParam('halfSingleA', $value[7]);
        $sth->bindParam('halfHandicapA', $value[8]);
        $sth->bindParam('halfOverUnderA', $value[9]);
        $sth->bindParam('allSingleB', $value[10]);
        $sth->bindParam('allHandicapB', $value[11]);
        $sth->bindParam('allOverUnderB', $value[12]);
        $sth->bindParam('allOddEvenB', $value[13]);
        $sth->bindParam('halfSingleB', $value[14]);
        $sth->bindParam('halfHandicapB', $value[15]);
        $sth->bindParam('halfOverUnderB', $value[16]);
        $sth->bindParam('allSingleD', $value[18]);
        $sth->bindParam('halfSingleD', $value[20]);
        $sth->execute();
    }

}
