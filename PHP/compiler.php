#!/usr/bin/env php
<?php

function getDirContents($dir, &$results = array()) {
    $files = scandir($dir);

    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            $results[] = $path;
        } else if ($value != "." && $value != "..") {
            getDirContents($path, $results);
            $results[] = $path;
        }
    }

    return $results;
}

$content = "";

foreach (getDirContents('./src/') as $file) {
    if (file_exists($file) && pathinfo($file)['extension'] === 'php') {
        $actual_content = file_get_contents($file);
        $actual_content = str_replace('<?php', '', $actual_content);
        $actual_content = str_replace('?>', '', $actual_content);

        preg_match_all('/namespace [a-zA−Z0-9\\;]+/s', $actual_content, $m);
        if (isset($m[0])) {
            foreach ($m[0] as $mm) {
                $actual_content = str_replace($mm, '', $actual_content);
            }
        }

        preg_match_all('/use [a-zA−Z0-9\\\;]+/', $actual_content, $m);
        if (isset($m[0])) {
            foreach ($m[0] as $mm) {
                $actual_content = str_replace($mm, '', $actual_content);
            }
        }

        $actual_content = PHP_EOL .trim($actual_content) . PHP_EOL . PHP_EOL;
        $content .= $actual_content;
    }
}

$index = file_get_contents('./index.php');
$index = str_replace("require './vendor/autoload.php';", '', $index);
$index = str_replace("use App\\Game;", '', $index);
$index = str_replace("<?php", '', $index);
$content .= PHP_EOL . $index . PHP_EOL;

$content = '<?php '. PHP_EOL . PHP_EOL . $content;
if (file_exists('./dist/compiled.php')) {
    unlink('./dist/compiled.php');
}

file_put_contents('./dist/compiled.php', $content);