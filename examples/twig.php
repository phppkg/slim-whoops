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

$container = $app->getContainer();
$container['view'] = function($c) {
    $view = new \Slim\Views\Twig('./views', [
        'debug' => true,
        'cache' => './cache/views'
    ]);

    $view->addExtension(new Twig_Extension_Debug());

    return $view;
};

// Work
// $app->get('/', function($request, $response, $args) use ($app) {
//     return $this->view->render($response, 'test.html', [
//         'name' => "Tester"
//     ]);
// });

// Exception
$app->get('/', function($request, $response, $args) use ($app) {
    return $this->view->render($response, 'noExists.html');
});

$app->run();
