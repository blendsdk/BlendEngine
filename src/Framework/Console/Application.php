<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Framework\Console;

use Symfony\Component\Console\Application as ApplicationBase;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Application.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Application extends ApplicationBase
{
    private $projectFolder;

    /**
     * Constructor.
     *
     * @param type $projectFolder This should always be the __DIR__ global variable
     * @param type $name
     * @param type $version
     */
    public function __construct($projectFolder, $name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);
        $this->projectFolder = realpath($projectFolder);
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $output->getFormatter()->setStyle('warn', new OutputFormatterStyle('black', 'yellow'));
        parent::doRun($input, $output);
    }

    /**
     * Returns the folder where this project is located. The location
     * is calculated by returning the parent folder of the OS script
     * where this class is instantiated from.
     *
     * @return string The project folder or null
     */
    public function getProjectFolder()
    {
        return $this->projectFolder;
    }
}
