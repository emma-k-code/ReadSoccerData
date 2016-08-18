<?php

require_once 'CatchWeb.php';

ignore_user_abort(true);
set_time_limit(0);

if (function_exists('pcntl_fork')) {
    $pid = pcntl_fork();
    if ($pid === -1) {
        die('fork fail.');
    } else if (!$pid) {
        autoDo();
    }
} else {
    autoDo();
}

function autoDo() {
    $test = new catchWeb;

    while (true) {
        $test->resolveWeb();
        sleep(60);
    }
}