<?php
namespace inhere\whoops\handler;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Whoops\Run as WhoopsRun;

/**
 * Class ErrorHandler
 * @package inhere\whoops\handler
 */
class ErrorHandler
{
    /**
     * @var WhoopsRun
     */
    private $whoops;

    /**
     * @param WhoopsRun $whoops
     */
    public function __construct(WhoopsRun $whoops)
    {
        $this->whoops = $whoops;
    }

    /**
     * @param ServerRequestInterface $request
     * @param Response $response
     * @param Exception $exception
     * @return Response
     */
    public function __invoke(ServerRequestInterface $request, Response $response, Exception $exception)
    {
        $handler = WhoopsRun::EXCEPTION_HANDLER;

        ob_start();

        $this->whoops->$handler($exception);

        $content = ob_get_clean();
        $code    = $exception instanceof \HttpException ? $exception->getStatusCode() : 500;

        return $response
                ->withStatus($code)
                ->withHeader('Content-type', 'text\html')
                ->write($content);
    }

    private function renderException(Exception $exception)
    {

    }

}
