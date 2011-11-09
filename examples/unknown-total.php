<?php
require_once '../ProgressBar.php';

echo ProgressBar::start(0, "One moment please");
usleep(10000000);

$size = 200; // Fixed here, this would be the result of some slow logic/query/api-call
ProgressBar::setTotal(200);

for ($i = 1; $i <= $size; $i++) {
    echo ProgressBar::next();
    usleep(100000);
}
    
echo ProgressBar::finish();
