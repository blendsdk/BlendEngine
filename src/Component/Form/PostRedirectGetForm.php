<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Component\Form;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Base class to handle a PostRedirectGetForm form.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class PostRedirectGetForm extends Form
{
    const STATE_INITIAL = 0;
    const STATE_DIRTY = 1;
    const STATE_COMPLETE = 2;

    abstract public function processFrom($submitted, $is_valid);

    protected function createStateStorage()
    {
        /*
         * We override this method to add the form state
         */
        return array_merge(parent::createStateStorage(), array(
            'formState' => self::STATE_INITIAL,
        ));
    }

    /**
     * Gets the current state.
     *
     * @return type
     */
    protected function getState()
    {
        return $this->stateStorage['formState'];
    }

    /**
     * Sets the current state.
     *
     * @param type $state
     */
    protected function setState($state)
    {
        $this->stateStorage['formState'] = $state;
    }

    protected function getCSRF()
    {
        /*
         * We override to get the CSRF as an HTML input
         */
        list($key, $value) = parent::getCSRF();

        return "<input type=\"hidden\" name=\"{$key}\" value=\"{$value}\"/>";
    }

    /**
     * Create a "render" context.
     *
     * @param array $input
     *
     * @return type
     */
    protected function createContext(array $input = array())
    {
        $messages = $this->getMessages();

        return array_merge($input, $messages, array(
            'request' => $this->request,
            'url' => $this->request->getPathInfo(),
            'csrf' => $this->getCSRF(),
            'form_state' => $this->getState(),
            'allmessages' => $messages,
                ), $this->getCurrentValues());
    }

    protected function doProcess($submitted, $is_valid)
    {
        $result = $this->processFrom($submitted, $is_valid);
        if ($this->getState() === self::STATE_COMPLETE) {
            $this->removeStorage();
        }

        return $result;
    }

    /**
     * Redirect the form to itself.
     *
     * @return RedirectResponse
     */
    protected function getRedirectSelf()
    {
        return new RedirectResponse($this->request->getPathInfo());
    }
}
