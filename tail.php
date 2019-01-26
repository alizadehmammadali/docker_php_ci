<?php
$logFile        = getcwd() . "/log.txt";
$log=shell_exec("tail -n 50 $logFile 2>&1");
echo $log;
