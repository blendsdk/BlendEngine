<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\PDF;

use Blend\Core\Application;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * PDFPrinter for creating PDF files
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class PDFPrinterService extends Pdf {

    protected $application;
    private $jobQueue;

    public function __construct(Application $application) {
        $this->application = $application;
        $nativePrinter = $application->getRootFolder() . '/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64';
        if (!file_exists($nativePrinter)) {
            throw new FileNotFoundException("{$nativePrinter} does not exists!", 500);
        }
        $this->jobQueue = array();
        parent::__construct($nativePrinter);
        $this->application->getDispatcher()->addListener(KernelEvents::TERMINATE, array($this, 'flushQueue'));
    }

    public function generateQueued($input, $output, array $options = array(), $overwrite = false) {
        $this->prepareOutput($output, $overwrite);
        $this->jobQueue[] = array($output, $this->getCommand($input, $output, $this->handleOptions($options)));
    }

    public function flushQueue() {
        foreach ($this->jobQueue as $job) {
            list($output, $command) = $job;
            list($status, $stdout, $stderr) = $this->executeCommand($command);
            $this->checkProcessStatus($status, $stdout, $stderr, $command);
            $this->checkOutput($output, $command);
        }
    }

}
