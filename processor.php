<?php

if (isset($GLOBALS['argv'][1]) && is_numeric($GLOBALS['argv'][1])) {
    sleepTime($GLOBALS['argv'][1]);
    echo 'finished';
}

/**
 * Busywait Loop.
 * 
 * @param  float $waitSeconds Number of seconds to wait.
 * @return none
 */
function sleepTime($waitSeconds) {
    $start = time();

    while ((time() - $start) < $waitSeconds) {
        usleep(100000);
    }
}
