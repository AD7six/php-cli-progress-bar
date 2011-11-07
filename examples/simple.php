<?php
require_once '../ProgressBar.php';

$size = 100;

echo ProgressBar::start($size);

for ($i = 1; $i <= $size; $i++) {
    echo ProgressBar::next();
    usleep(100000);
}
    
echo ProgressBar::finish();
