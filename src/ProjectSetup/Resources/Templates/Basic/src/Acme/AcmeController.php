<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Acme;

use Blend\Framework\Support\Runtime\RuntimeProviderInterface;

/**
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class AcmeController
{
    /**
     * @var RuntimeProviderInterface
     */
    private $runtime;

    public function __construct(RuntimeProviderInterface $runtime)
    {
        $this->runtime = $runtime;
    }

    public function index()
    {
        return "Welcome to {$this->runtime->getApplicationName()} running ".
                "from {$this->runtime->getAppRootFolder()}";
    }
}
