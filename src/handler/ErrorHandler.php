<?php

namespace Inhere\Whoops\Handler;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Whoops\Run as WhoopsRun;

/**
 * Class ErrorHandler
 *
 * handle the slim app runtime error.
 *
 * @package Inhere\Whoops\Handler
 */
class ErrorHandler
{
    /**
     * @var WhoopsRun
     */
    private $whoops;

    /**
     * @var RecordLogHandler
     */
    private $logHandler;

    /**
     * @param RecordLogHandler $logHandler
     * @param WhoopsRun|null   $whoops
     */
    public function __construct(RecordLogHandler $logHandler, WhoopsRun $whoops = null)
    {
        $this->logHandler = $logHandler;
        $this->whoops = $whoops;
    }

    /**
     * @param ServerRequestInterface $request
     * @param Response               $response
     * @param \Throwable             $throwable
     * @return Response
     */
    public function __invoke(ServerRequestInterface $request, Response $response, $throwable)
    {
        // record exception log
        $this->logHandler->setException($throwable);

        // show error
        $handler = WhoopsRun::EXCEPTION_HANDLER;

        ob_start();
        $this->whoops->$handler($throwable);
        $content = ob_get_clean();

//        var_dump($content);
        return $response
            ->withStatus(200)
            ->withHeader('Content-type', 'text/html')
            ->write($content);
    }
}
