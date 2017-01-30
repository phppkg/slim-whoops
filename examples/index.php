<?php
require_once dirname(dirname(__FILE__)).'/vendor/autoload.php';

use Slim\App;
use inhere\whoops\WhoopsMiddleware;

$app = new App([
    'whoops' => [
        'debug'  => true,
        'editor' => 'sublime' // Support click to open file in the editor
    ]
]);

$app->add(new WhoopsMiddleware($app));

// Throw exception, Named route does not exist for name: hello
$app->get('/', function($request, $response, $args) {
	return $this->router->pathFor('hello');
});

// $app->get('/hello', function($request, $response, $args) {
//     $response->write("Hello Slim");
//     return $response;
// })->setName('hello');

$app->run();
