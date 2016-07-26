<?php
namespace inhere\whoops\handler;

use Exception;
use Slim\Container;
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
     * @var Logger
     */
    private $settings = [
        'debug' => false,
        'logFormat' => 'simple',
    ];

    const LOG_SIMPLE = 'simple';
    const LOG_FULL = 'full';

    /**
     * @param Container $container
     * @param WhoopsRun|null $whoops
     */
    public function __construct(Container $container, WhoopsRun $whoops = null)
    {
        $this->logger = isset($container['errLogger']) ? $container['errLogger'] : $container['logger'];
        $this->whoops = $whoops;

        $settings     = $container['settings'];
        $format = isset($settings['whoops.errLog']) ? $settings['whoops.errLog'] : self::LOG_SIMPLE;

        $this->settings = [
            'debug'     => isset($settings['debug']) ? (bool)$settings['debug'] : false,
            'logFormat' => in_array($format, [self::LOG_FULL, self::LOG_SIMPLE]) ? $format : self::LOG_SIMPLE,
        ];
    }

    /**
     * @param ServerRequestInterface $request
     * @param Response $response
     * @param Exception $exception
     * @return Response
     */
    public function __invoke(ServerRequestInterface $request, Response $response, Exception $exception)
    {
        // Log the error message
        if ( $this->settings['logFormat'] === self::LOG_FULL ) {
            de($exception);
            $text = $exception->getTraceAsString():;

            $this->logger->error($exception->getMessage());
        } else {
            $this->logger->error($exception->getMessage());
        }

        // without enable debug.
        if ( false == $this->settings['debug'] ) {
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
