<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Base class for all classes that need to set flash messages
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class FlashProvider {

    /**
     * @var FlashBagInterface
     */
    protected $flashBag;

    /**
     * @var string
     */
    private $flashCategory;

    /**
     * @var number
     */
    private $errorCount;

    public function hasErrors() {
        return $this->errorCount !== 0;
    }

    protected function setFlashBagFromRequest(Request $request) {
        $this->flashBag = $request->getSession()->getFlashBag();
        $this->flashCategory = $request->attributes->get('_csrf_key_');
        $this->errorCount = 0;
    }

    protected function addError($message, $category = null) {
        $this->flashBag->add(is_null($category) ? $this->flashCategory : $category, $message);
        $this->errorCount++;
    }

    /**
     * Retrives the list of error is the flashbag
     * @return mixed
     */
    public function getErrors($category = null) {
        $this->errorCount = 0;
        return $this->flashBag->get(is_null($category) ? $this->flashCategory : $category);
    }

}
