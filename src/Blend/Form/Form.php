<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Form;

use Blend\Core\Application;
use Symfony\Component\HttpFoundation\Request;
use Blend\Form\FormException;
use Blend\Form\FieldSet;
use Blend\Form\Field;
use Blend\Form\ErrorProvider;

/**
 * Description of Form
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Form extends ErrorProvider {

    const FORM_TYPE_POST = 'POST';
    const FORM_TYPE_GET = 'GET';
    const DEFAULT_FIELDSET = false;

    private $csrf_key;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Application
     */
    protected $method;
    protected $submitted;
    protected $name;

    /**
     * @var FieldSet[]
     */
    protected $fieldsets;

    public function __construct($name, $csrf_key_name, $method = Form::FORM_TYPE_POST) {
        parent::__construct();
        $this->method = $method;
        $this->submitted = false;
        $this->csrf_key = $csrf_key_name;
        $this->name = $name;
        $this->fieldsets = array();
        $this->addFieldSet(new FieldSet($name));
    }

    public function addFieldSet(FieldSet $fieldset) {
        $this->fieldsets[$fieldset->getName()] = $fieldset;
    }

    public function addField($id, Field $field) {
        $this->fieldsets[$this->name]->addField($id, $field);
    }

    public function getCSRF() {

        return array(
            'key' => $this->csrf_key,
            'value' => $this->request->getSession()->get($this->csrf_key)
        );
    }

    protected function checkSubmitted() {
        $csrf = $this->request->get($this->csrf_key, null);
        $check = $this->request->getSession()->get($this->csrf_key);
        if (!is_null($csrf) && !is_null($check) && $csrf === $check) {
            return ($this->submitted = true);
        } else {
            return ($this->submitted = false);
        }
    }

    /**
     *
     * @param Request $request
     * @throws FormException
     * @return boolean true if the form can be processed further
     */
    public function handleRequest(Request $request) {
        $this->request = $request;
        if ($this->checkSubmitted()) {
            if ($request->getMethod() !== $this->method) {
                throw new FormException("Invalid request method. Expected {$this->method} got {$request->getMethod()}");
            } else {
                $this->setDataInternal($request);
            }
        } else {
            $request->getSession()->set($this->csrf_key, md5(uniqid()));
        }
    }

    public function getData($fielsetName = Form::DEFAULT_FIELDSET) {
        $name = $fielsetName === Form::DEFAULT_FIELDSET ? $this->name : $fielsetName;
        if (isset($this->fieldsets[$name])) {
            return $this->fieldsets[$name]->getData();
        } else if (is_null($fielsetName)) {
            $result = array();
            foreach ($this->fieldsets as $name => $fieldset) {
                $result[$name] = $fieldset->getData();
            }
            return $result;
        } else {
            return null;
        }
    }

    public function setData($data = array(), $fieldsets = Form::DEFAULT_FIELDSET) {
        $fset = $fieldsets === Form::DEFAULT_FIELDSET ? $this->name : $fieldsets;
        if (isset($this->fieldsets[$set])) {
            $this->fieldsets[$set]->setData($data);
        } else {
            foreach ($this->fieldsets as $fieldset) {
                $fieldset->setData($data);
            }
        }
    }

    protected function setDataInternal(Request $request) {
        foreach ($this->fieldsets as $fieldset) {
            $name = $fieldset->getName();
            $fieldset->setData($request->get($name, null));
        }
    }

    protected function validate() {
        foreach ($this->fieldsets as $fieldset) {
            if ($fieldset->validate() === false) {
                $this->errors = array_merge($this->errors, $fieldset->getErrors());
            }
        }
    }

    public function isValid() {
        if ($this->submitted) {
            $this->validate();
            return parent::isValid();
        } else {
            return false;
        }
    }

}
