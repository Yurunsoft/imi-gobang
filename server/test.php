<?php
$n = new Swoole\Atomic;
if (pcntl_fork() > 0) {
    echo "master start\n";
    var_dump($n->wait(1.5));
    $n->wakeup();
    echo "master end\n";
    var_dump($n->wait(1.5));
    echo "master end\n";
} else {
    echo "child start\n";
    sleep(1);
    $n->wakeup();
    echo "child end\n";
}