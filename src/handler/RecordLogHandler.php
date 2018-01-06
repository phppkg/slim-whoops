<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/1/28
 * Time: 下午9:03
 */

namespace Inhere\Whoops\Handler;

use Monolog\Logger;
use Whoops\Handler\Handler;

/**
 * Class RecordLogHandler
 * the whoops handler, for record error log.
 * ```
 * $whoops = new \Whoops\Run;
 *
 * // record log to file
 * $logHandler = new RecordLogHandler();
 * $logger = $container['logger'];
 * $logHandler->setLogger($logger);
 * $logHandler->setOptions($settings);
 * $whoops->pushHandler($logHandler);
 * ```
 * @package Inhere\Whoops\Handler
 */
class RecordLogHandler extends Handler
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var array
     */
    private $options = [
        'debug' => false,
        'logFormat' => 'simple',
    ];

    const LOG_SIMPLE = 'simple';
    const LOG_FULL = 'full';

    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
    }

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function handle()
    {
        if ($this->logger) {
            $exception = $this->getException();

            // Log the error message
            $text = $exception->getMessage() . PHP_EOL
                . $exception->getFile() . ' Line '
                . $exception->getLine();
            $text .= $exception->getTraceAsString();

            $context['request'] = [
                'HOST' => $this->getServer('HTTP_HOST'),
                'METHOD' => $this->getServer('REQUEST_METHOD'),
                'URI' => $this->getServer('REQUEST_URI'),
                'REFERER' => $this->getServer('HTTP_REFERER'),
            ];

            $this->logger->error($text, $context);
        }

        // without enable debug.
        if ( false === (bool)$this->options['debug'] ) {
            echo "An unexpected error occurred.(By whoops)!";

            return Handler::QUIT;
        }

        return Handler::DONE;
    }

    private function getServer($name, $default = '')
    {
        $name = strtoupper($name);

        return isset($_SERVER[$name]) ? $_SERVER[$name] : $default;
    }
}
