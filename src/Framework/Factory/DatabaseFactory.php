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

use Psr\Log\LoggerInterface;
use Blend\Component\Database\Database;
use Blend\Component\Configuration\Configuration;
use Blend\Component\DI\ObjectFactoryInterface;

/**
 * DatabaseFactory
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class DatabaseFactory implements ObjectFactoryInterface {

    /**
     * @var LoggerInterface 
     */
    protected $logger;

    /**
     * @var Configuration 
     */
    protected $config;

    public function __construct(LoggerInterface $logger, Configuration $config) {
        $this->logger = $logger;
        $this->config = $config;
    }

    public function create() {
        $appname = strtolower($this->config->get('name'));
        return new Database([
            'host' => $this->config->get('database.host', '127.0.0.1'),
            'port' => $this->config->get('database.port', 5432),
            'database' => $this->config->get('database.database', $appname),
            'username' => $this->config->get('database.username', $appname),
            'password' => $this->config->get('database.password'),
                ], $this->logger);
    }

}
