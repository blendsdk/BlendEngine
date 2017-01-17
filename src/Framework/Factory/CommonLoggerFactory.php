<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Factory;

use Blend\Component\DI\ObjectFactoryInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;

/**
 * Description of CommonLoggerFactory.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class CommonLoggerFactory implements ObjectFactoryInterface
{
    protected $logFolder;
    protected $name;
    protected $maxFiles;
    protected $level;

    public function __construct($logFolder, $logName, $logMaxFiles = 10, $logLevel = LogLevel::WARNING)
    {
        $this->logFolder = $logFolder;
        $this->name = $logName;
        $this->maxFiles = $logMaxFiles;
        $this->level = $logLevel;
    }

    public function create()
    {
        $logger = new Logger($this->name);
        $rotatingFileHandler = new RotatingFileHandler(
                $this->logFolder.'/'.$this->name.'.log', $this->maxFiles, $this->level);
        $logger->pushHandler($rotatingFileHandler);

        return $logger;
    }
}
