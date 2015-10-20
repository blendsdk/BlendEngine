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
     * @return Form;
     */
    public function getForm() {
        return $this->form;
    }

    public function validateRequest(Request $request) {
        $this->form = $this->createForm($request);
        $this->setFlashBagFromRequest($request);
        return $this->form->isSubmitted();
    }

    protected function addError($message, $category = null) {
        parent::addError($message, $category);
        if($this->form !== null) {
            $this->form->saveFormData();
        }
    }

}
