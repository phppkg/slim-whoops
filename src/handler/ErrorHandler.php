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
        $text = $exception->getMessage() . PHP_EOL
                . $exception->getFile() . ' Line '
                . $exception->getLine();
        // $text .= $exception->getTraceAsString();
        $context['request'] = [
            'HOST' => $this->getServer('HTTP_HOST'),
            'METHOD' => $this->getServer('request_method'),
            'URI' => $this->getServer('request_uri'),
            'REFERER' => $this->getServer('HTTP_REFERER'),
        ];

        $this->logger->error($text, $context);

        // without enable debug.
        if ( false == $this->settings['debug'] ) {
            return $response
                            ->withHeader('Content-type', 'text\html')
                            ->write("An unexpected error occurred.(By whoops)!");
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

    public function getServer($name, $default = '')
    {
        $name = strtoupper($name);

        return isset($_SERVER[$name]) ? $_SERVER[$name] : $default;
    }


}
