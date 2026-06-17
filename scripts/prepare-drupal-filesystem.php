<?php

$projectRoot = dirname(__DIR__);

$directories = [
    $projectRoot . '/web/sites/default/files',
    $projectRoot . '/config/sync',
];

foreach ($directories as $directory) {
    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        fwrite(STDERR, sprintf("Failed to create directory: %s\n", $directory));
        exit(1);
    }

    chmod($directory, 0775);
}
