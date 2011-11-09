<?php
require_once '../ProgressBar.php';

$size = 100;

echo ProgressBar::start($size, "Starting in about 5 seconds");
usleep(5000000);

ProgressBar::setMessage('Go!');
for ($i = 1; $i <= $size; $i++) {
    if ($i % 20) {
        echo ProgressBar::next();
    } else {
        echo ProgressBar::next(1, "made it to $i yay!");
    }
    usleep(100000);
}
    
echo ProgressBar::finish();
