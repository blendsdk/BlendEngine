<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Framework\Logger;

use Blend\Framework\Logger\LoggerFactoryInterface;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

/**
 * Description of CommonLoggerFactory
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class CommonLoggerFactory implements LoggerFactoryInterface {

    protected $logFolder;
    protected $name;
    protected $maxFiles;

    public function __construct($logFolder, $name, $maxFiles) {
        $this->logFolder = $logFolder;
        $this->name = $name;
        $this->maxFiles = $maxFiles;
    }

    public function buildLogger($defaultLevel) {
        $logger = new Logger($this->name);
        $rotatingFileHandler = new RotatingFileHandler(
                $this->logFolder . '/' . $this->name . '.log'
                , $this->maxFiles, $defaultLevel);
        $logger->pushHandler($rotatingFileHandler);
        return $logger;
    }

}
