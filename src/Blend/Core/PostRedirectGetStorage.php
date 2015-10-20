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

/**
 * PostRedirectGetBag
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class PostRedirectGetStorage {

    /**
     * @var string
     */
    private $storageKey;

    /**
     * @var array
     */
    private $values;

    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request, $storageKey) {
        $this->storageKey = md5(__CLASS__ . $storageKey);
        $this->request = $request;
        $this->loadStorage();
    }

    private function loadStorage() {
        $this->values = $this->request->getSession()->get($this->storageKey, array());
    }

    private function updateStorage() {
        $this->request->getSession()->set($this->storageKey, $this->values);
    }

    public function setValues(array $values) {
        $this->values = $values;
        $this->updateStorage();
        return $this;
    }

    public function setValue($key, $value) {
        $this->values[$key] = $value;
        $this->updateStorage();
        return $this;
    }

    public function getValues() {
        $values = $this->values;
        $this->clear();
        return $values;
    }

    public function getValue($key, $default = null) {
        if (isset($this->values[$key])) {
            $value = $this->values[$key];
            unset($this->values[$key]);
            $this->updateStorage();
            return $value;
        } else {
            return $default;
        }
    }

    public function clear() {
        $this->values = array();
        $this->request->getSession()->remove($this->storageKey);
    }

}
