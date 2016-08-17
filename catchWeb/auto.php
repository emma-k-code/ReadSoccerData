<?php

require_once 'CatchWeb.php';

ignore_user_abort(true);
set_time_limit(0);

$test = new catchWeb;

while (true) {
    $test->resolveWeb();
    sleep(60);
}