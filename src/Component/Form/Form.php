<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Form;

use Blend\Component\Exception\InvalidOperationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Base class to handle a Form.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Form
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $csrf_key;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var string
     */
    protected $formid;

    /**
     * @var array
     */
    protected $stateStorage;

    /**
     * @var bool
     */
    private $hasErrors;

    /**
     * @var array
     */
    protected $fields;

    abstract protected function validateState($submitted);

    abstract protected function doProcess($submitted, $is_valid);

    abstract protected function getDefaultValues();

    public function __construct(Request $request)
    {
        $this->hasErrors = false;
        $this->request = $request;
        $this->csrf_key = crc32($request->getPathInfo());
        $this->session = $request->getSession();
        $this->formid = '_form_'.$this->csrf_key;
        $this->fields = array_merge($request->query->all(), $request->request->all());
        $this->stateStorage = $this->session->get($this->formid, $this->createStateStorage());
    }

    /**
     * Creates a Session storage to thendle this form.
     *
     * @return type
     */
    protected function createStateStorage()
    {
        return [
            'csrf' => null,
            'messages' => [],
            'savedValues' => [],
        ];
    }

    /**
     * Returns the current values of this form. The values are
     * built based on default<-state<-request.
     *
     * @return array
     */
    protected function getCurrentValues()
    {
        return array_merge(
                $this->getDefaultValues(), $this->stateStorage['savedValues'], $this->fields
        );
    }

    public function process()
    {
        $submitted = $this->checkSubmitted();
        if (!$submitted) {
            $this->setCSRF();
        } else {
            $this->assertPOSTMethod();
        }
        $is_valid = $this->validateState($submitted);
        $result = $this->doProcess($submitted, $is_valid);
        $this->saveStorage();

        return $result;
    }

    protected function assertPOSTMethod()
    {
        if ($this->request->getMethod() !== 'POST') {
            throw new InvalidOperationException(
            'Invalid form submit method, only POST method is allowed'
            );
        }
    }

    /**
     * Getter for the hasErrors property.
     *
     * @return type
     */
    protected function hasErrors()
    {
        return $this->hasErrors;
    }

    /**
     * Check if the form is submitted correctly.
     *
     * @return bool
     */
    protected function checkSubmitted()
    {
        $csrf = $this->request->get($this->csrf_key, null);
        $check = $this->stateStorage['csrf'];
        if (!is_null($csrf) && !is_null($check) && $csrf === $check) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets the CSRF key and the code.
     *
     * @return type
     */
    protected function getCSRF()
    {
        return [$this->csrf_key, $this->stateStorage['csrf']];
    }

    /**
     * Sets a new CSRF code.
     */
    protected function setCSRF()
    {
        $this->stateStorage['csrf'] = uniqid();
    }

    /**
     * Removed the current Session storage.
     */
    protected function removeStorage()
    {
        $this->session->remove($this->formid);
    }

    /**
     * Saved the current storage in the Session.
     */
    protected function saveStorage()
    {
        $this->session->set($this->formid, $this->stateStorage);
    }

    /**
     * Adds a messages to the flash messages list.
     *
     * @param type  $type
     * @param type  $message
     * @param array $context
     */
    protected function addMessage($type, $message, array $context = [])
    {
        if (!isset($this->stateStorage['messages'][$type])) {
            $this->stateStorage['messages'][$type] = [];
        }
        $this->stateStorage['messages'][$type][] = [$message, $context];
    }

    /**
     * Adds an error message.
     *
     * @param type  $message
     * @param array $context
     */
    protected function addError($message, array $context = [])
    {
        $this->hasErrors = true;
        $this->addMessage('error', $message, $context);
    }

    /**
     * Adds a sucess message.
     *
     * @param type  $message
     * @param array $context
     */
    protected function addSuccess($message, array $context = [])
    {
        $this->addMessage('success', $message, $context);
    }

    /**
     * Adds a warning message.
     *
     * @param type  $message
     * @param array $context
     */
    protected function addWarning($message, array $context = [])
    {
        $this->addMessage('warn', $message, $context);
    }

    /**
     * Adds an infor message.
     *
     * @param type  $message
     * @param array $context
     */
    protected function addInfo($message, array $context = [])
    {
        $this->addMessage('info', $message, $context);
    }

    /**
     * Gets the messages and removes them.
     *
     * @return type
     */
    protected function getMessages()
    {
        $result = $this->stateStorage['messages'];
        $this->stateStorage['messages'] = [];

        return $result;
    }

    /**
     * Get a field from the POST or GET variables.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function getField($name, $default = null)
    {
        if (array_key_exists($name, $this->fields)) {
            return $this->fields[$name];
        } else {
            return $default;
        }
    }

    /**
     * Checks if a given field is blank and add an error to the errors list.
     *
     * @param string $field
     * @param string $message
     * @param array  $context
     */
    protected function assertNotBlank($field, $message, array $context = [])
    {
        if (empty($this->getField($field))) {
            $this->addError($message, $context);
        }
    }

    /**
     * Checks if the given fields have the same value and add an error to
     * the errors list.
     *
     * @param string $field1
     * @param string $field2
     * @param string $message
     * @param array  $context
     */
    protected function assertSameValue($field1, $field2, $message, array $context = [])
    {
        if ($this->getField($field1) !== $this->getField($field2)) {
            $this->addError($message, array_merge($context, ['field1' => $field1, 'field2' => $field2]));
        }
    }
}
