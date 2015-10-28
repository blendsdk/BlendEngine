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

use Blend\Core\FlashProvider;
use Blend\Form\FormException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Base class for all Forms
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Form extends FlashProvider {

    const FORM_TYPE_POST = 'POST';

    private $csrf_key;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var boolean
     */
    protected $submitted;

    /**
     * @var boolean
     */
    protected $valid;

    /**
     * @var string
     */
    private $sess_value_key;

    /**
     * @var array;
     */
    private $record;

    protected abstract function validate();

    public function __construct(Request $request) {
        $this->submitted = false;
        $this->csrf_key = $request->attributes->get('_csrf_key_');
        $this->errors = [];
        $this->record = [];
        $this->request = $request;
        $this->sess_value_key = md5('FORM-' . $request->getPathInfo() . date('Y'));
        $this->setFlashBagFromRequest($request);
        $this->handleRequest();
    }

    /**
     * Gets the CSRF info as an array to be used later.
     * @return array
     */
    public function getCsrfInfo() {
        return array(
            'key' => $this->csrf_key,
            'value' => $this->request->getSession()->get($this->csrf_key)
        );
    }

    /**
     * Creates an HTML hidden input tag with the CSRF value
     * @return string
     */
    public function getCsrfHTMLUserInterface() {
        $info = $this->getCsrfInfo();
        return "<input type=\"hidden\" name=\"{$info['key']}\" value=\"{$info['value']}\"/>";
    }

    /**
     * Check if the form is submitted correctly
     * @return boolean
     */
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
     */
    protected function handleRequest() {
        if ($this->checkSubmitted()) {
            if ($this->request->getMethod() !== self::FORM_TYPE_POST) {
                throw new FormException("Invalid request method. Expected {$this->method} got {$this->request->getMethod()}");
            } else {
                $this->validate();
                $this->valid = !$this->hasErrors();
                if ($this->valid === false) {
                    $this->saveFormData();
                }
            }
        } else {
            $this->request->getSession()->set($this->csrf_key, md5(uniqid()));
        }
    }

    /**
     * Saves the current form data in the session to be used later
     */
    public function saveFormData() {
        $this->request->getSession()->set($this->sess_value_key, $this->request->request->all());
    }

    /**
     * Clears the form data from the session
     */
    protected function clearSavedData() {
        $this->request->getSession()->remove($this->sess_value_key);
    }

    /**
     * Retuns an array of request paramters
     * @return string[]
     */
    public function getPreviousValues() {
        $values = $this->request->getSession()->get($this->sess_value_key, array());
        $this->clearSavedData();
        return array_replace($this->record, $values);
    }

    protected function setRecordField($key, $value) {
        $this->record[$key] = $value;
    }

    public function setRecord(array $data) {
        $this->record = $data;
    }

    public function reset() {
        $this->clearSavedData();
    }

    /**
     * Check if the vorm is valid
     * @return boolean
     */
    public function isValid() {
        return $this->valid;
    }

    /**
     * Chech if the form is submitted
     * @return boolean
     */
    public function isSubmitted() {
        return $this->submitted;
    }

    /**
     * Assert two form field to have the save value
     * @param type $key1
     * @param type $key2
     * @param type $error
     */
    protected function assertSameValue($key1, $key2, $error) {
        $value1 = $this->getField($key1);
        $value2 = $this->getField($key2);
        if ($value1 !== $value2) {
            $this->addError($error);
        }
    }

    /**
     * Assert the field value for not blank
     * @param string $key
     * @param string $error
     */
    protected function assertNotBlank($key, $error) {
        $value = $this->getField($key);
        if (empty($value)) {
            $this->addError($error);
        }
    }

    /**
     * Returns all the fields of this form
     * @return type
     */
    public function getAllFields() {
        return $this->request->request->all();
    }

    /**
     * Geta field value from this form
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getField($key, $default = null) {
        return $this->request->get($key, $default);
    }

}
