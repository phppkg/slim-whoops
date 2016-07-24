<?php
namespace inhere\whoops\handler;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Whoops\Run as WhoopsRun;
use Psr\Log\LoggerInterface as Logger;

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
     * @var Logger
     */
    private $logger;

    /**
     * @param Logger $logger
     * @param WhoopsRun|null $whoops
     */
    public function __construct(Logger $logger, WhoopsRun $whoops = null)
    {
        $this->logger = $logger;
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
        // Log the message
        $this->logger->critical($exception->getMessage());

        // without enable debug.
        if ( false == \Slim::config()->get('debug') ) {
            return $response
                            ->withHeader('Content-type', 'text\html')
                            ->write("Happend error!");
        }

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
