<?php

namespace Acme;

use Blend\Framework\Support\Runtime\RuntimeProviderInterface;

/**
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class AcmeController {

    /**
     * @var RuntimeProviderInterface
     */
    private $runtime;

    public function __construct(RuntimeProviderInterface $runtime) {
        $this->runtime = $runtime;
    }

    public function index() {
        return "Welcome to {$this->runtime->getApplicationName()} running " .
                "from {$this->runtime->getAppRootFolder()}";
    }

}
