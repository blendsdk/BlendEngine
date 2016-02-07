<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\DataModelBuilder\Command;

use Blend\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Data Access Layer generator
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class DataModelCommand extends Command {

    protected function configure() {
        $this->setName('databate:generate')
                ->setDescription('Generates a Data Model Layer from the current database')
                ->addOption('config', 'c'
                        , InputArgument::OPTIONAL
                        , 'Configuration file to specify how to generate the DataAccess Layer');
    }

}
