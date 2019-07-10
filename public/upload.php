<?php
require __DIR__.'/../vendor/autoload.php';
$config = require __DIR__.'/../src/config.php';

if (empty($_FILES)) {
    http_response_code(400);
    exit;
}

$file = array_pop($_FILES);
$handler = new App\Qiniu($config['qiniu']);
$url = $handler->put($file);
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'url' => $url,
]);
