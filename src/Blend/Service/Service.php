<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Service;

use Blend\Form\Form;
use Blend\Core\FlashProvider;
use Blend\Core\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Base class for all Application level services Service
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Service extends FlashProvider {

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Application
     */
    protected $application;

    protected abstract function createForm(Request $request);

    public function __construct(Application $application) {
        $this->form = null;
        $this->application = $application;
    }

    /**
     * @return \Monolog\Logger
     */
    protected function getLogger() {
        return $this->application->getLogger();
    }

    /**
     * Wrapper funcrion for $this->application->getTranslator()->trans
     * @param string $id
     * @param mixed $params
     * @return strinf
     */
    protected function trans($id, $params = array(), $domain = null) {
        return $this->application->getTranslator()->trans($id, $params, $domain);
    }

    /**
     * Gets the main form of this service
     * @return Form;
     */
    public function getForm() {
        return $this->form;
    }

    /**
     * Prepares the validation process by creating the form and the flashbag
     * @param Request $request
     */
    protected function prepareValidation(Request $request) {
        $this->request = $request;
        $this->form = $this->createForm($request);
        $this->setFlashBagFromRequest($request);
    }

    /**
     * Validates the current request by creating the request form and
     * also setting the flash messages
     * @param Request $request
     * @return type
     */
    public function validateRequest(Request $request) {
        $this->prepareValidation($request);
        if ($this->form) {
            return $this->form->isSubmitted();
        } else {
            return false;
        }
    }

    /**
     * Adds an error to the flashbag and saved the ccurrent form data
     * @param string $message
     * @param string $category
     */
    protected function addError($message) {
        parent::addError($message);
        if ($this->form !== null) {
            $this->form->saveFormData();
        }
    }

}
