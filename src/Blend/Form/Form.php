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
use Blend\Model\Model;

/**
 * Description of Form
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Form {

    const FORM_TYPE_POST = 'POST';

    private $csrf_key;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Application
     */
    protected $submitted;

    /**
     * @var array
     */
    protected $errors;

    public function __construct($csrf_key_name, Model $model) {
        $this->submitted = false;
        $this->model = $model;
        $this->csrf_key = $csrf_key_name;
        $this->errors = [];
    }

    public function addError($message) {
        $this->errors[] = $message;
    }

    public function getErrors() {
        return $this->errors;
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
            if ($request->getMethod() !== self::FORM_TYPE_POST) {
                throw new FormException("Invalid request method. Expected {$this->method} got {$request->getMethod()}");
            } else {
                $this->model->setValues($request->request->all());
            }
        } else {
            $request->getSession()->set($this->csrf_key, md5(uniqid()));
        }
    }

    public function isSubmitted() {
        return $this->submitted;
    }

    public function validate() {
        if ($this->submitted) {
            if ($this->model->isValid()) {
                return true;
            } else {
                $this->errors = array_merge($this->errors, $this->model->getErrors());
                return false;
            }
        }
    }

}
