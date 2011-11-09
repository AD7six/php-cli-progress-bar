<?php
require_once '../ProgressBar.php';

$size = 10;

echo ProgressBar::start($size);

for ($i = 1; $i <= $size * 10; $i++) {
    if ($i === ($size + 1)) {
        ProgressBar::setMessage('Ooops, looks like there\'s more work to do');
    }
    echo ProgressBar::next();
    usleep(100000);
}
    
echo ProgressBar::finish();
