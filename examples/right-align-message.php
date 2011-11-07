<?php
require_once '../ProgressBar.php';

$size = 100;

echo ProgressBar::start(
    $size, 
    '',
    array(
        'format' => "\r [%6\$s] %.01f%% %2\$d/%3\$d ETC: %4\$s. Elapsed: %5\$s:padding::message:",
    )
);

for ($i = 1; $i <= $size; $i++) {
    echo ProgressBar::next();
    usleep(100000);
}
    
echo ProgressBar::finish();
