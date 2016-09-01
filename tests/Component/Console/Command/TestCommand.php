<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Console\Command;

use Blend\Framework\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Blend\Component\Configuration\Configuration;
use Psr\Log\LoggerInterface;

/**
 * Description of TestCommand
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class TestCommand extends Command {

    protected function configure() {
        parent::configure();
        $this->setName('test:testcommand');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        /* @var $config Configuration */
        $config = $this->container->get(Configuration::class);

        /* @var $log LoggerInterface */
        $log = $this->container->get(LoggerInterface::class);
        $log->error('some error', ['date' => date('c')]);
    }

}
