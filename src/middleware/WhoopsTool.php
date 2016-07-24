<?php

namespace inhere\whoops\middleware;

use Whoops\Run as WhoopsRun;
use Whoops\Util\Misc;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;
use inhere\whoops\handler\ErrorHandler;
use Slim\Http\Environment;
use Slim\Http\Request;

/**
 * Class WhoopsTool
 * @package inhere\whoops\middleware
 */
class WhoopsTool
{

    /**
     * @param Request $request
     * @param $response
     * @param $next
     * @return mixed
     */
    public function __invoke(Request $request, $response, $next)
    {
        $app         = \Slim::$app;
        /** @var \Slim\Container $container */
        $container   = $app->getContainer();
        $settings    = $container['settings'];
        /** @var Request $request */
        // $request     = $container['request'];

        if (isset($settings['debug']) === true && $settings['debug'] === true) {
            /** @var Environment $environment */
            $environment = $container['environment'];

            // Enable PrettyPageHandler with editor options
            $prettyPageHandler = new PrettyPageHandler();

            if (empty($settings['whoops.editor']) === false) {
                $prettyPageHandler->setEditor($settings['whoops.editor']);
            }

            // Add more information to the PrettyPageHandler
            $prettyPageHandler->addDataTable('Slim Application', [
                'Application Class' => get_class($app),
                'Script Name'       => $environment->get('SCRIPT_NAME'),
                'Request URI'       => $environment->get('PATH_INFO') ?: '<none>',
            ]);

            $prettyPageHandler->addDataTable('Slim Application (Request)', array(
                'Accept Charset'  => $request->getHeader('ACCEPT_CHARSET') ?: '<none>',
                'Content Charset' => $request->getContentCharset() ?: '<none>',
                'Path'            => $request->getUri()->getPath(),
                'Query String'    => $request->getUri()->getQuery() ?: '<none>',
                'HTTP Method'     => $request->getMethod(),
                'Base URL'        => (string) $request->getUri(),
                'Scheme'          => $request->getUri()->getScheme(),
                'Port'            => $request->getUri()->getPort(),
                'Host'            => $request->getUri()->getHost(),
            ));

            // Set Whoops to default exception handler
            $whoops = new WhoopsRun;
            $whoops->pushHandler($prettyPageHandler);

            // Enable JsonResponseHandler when request is AJAX
            if (Misc::isAjaxRequest()){
                $whoops->pushHandler(new JsonResponseHandler());
            }

            $whoops->register();

            $container['errorHandler'] = function($c) use ($whoops) {
                $logger = isset($c['errLogger']) ? $c['errLogger'] : $c['logger'];

                return new ErrorHandler($logger, $whoops);
            };

            $container['whoops'] = $whoops;
        } else {
            $container['errorHandler'] = function($c) {
                $logger = isset($c['errLogger']) ? $c['errLogger'] : $c['logger'];

                return new ErrorHandler($logger);
            };
        }

        return $next($request, $response);
    }

}
