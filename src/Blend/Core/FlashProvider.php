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
    private $error_category;

    /**
     * @var string
     */
    private $message_category;

    protected function setFlashBagFromRequest(Request $request) {
        $this->flashBag = $request->getSession()->getFlashBag();
        $key = $request->attributes->get('_csrf_key_');
        $this->error_category = 'error_' . $key;
        $this->message_category = 'message_' . $key;
    }

    public function hasMessages() {
        $data = $this->flashBag->peek($this->message_category);
        return count($data) !== 0;
    }

    protected function addMessage($message) {
        $this->flashBag->add($this->message_category, $message);
    }

    /**
     * Retrives the list of error is the flashbag
     * @return mixed
     */
    public function getMessages() {
        return $this->flashBag->get($this->message_category);
    }

    public function hasErrors() {
        $data = $this->flashBag->peek($this->error_category);
        return count($data) !== 0;
    }

    protected function addError($message) {
        $this->flashBag->add($this->error_category, $message);
    }

    /**
     * Retrives the list of error is the flashbag
     * @return mixed
     */
    public function getErrors() {
        return $this->flashBag->get($this->error_category);
    }

}
