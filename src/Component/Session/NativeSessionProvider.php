<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Session;

use Blend\Component\Session\SessionProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

/**
 * Description of NativeSessionProvider
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class NativeSessionProvider implements SessionProviderInterface {

    /**
     *
     * @var Session
     */
    protected $session;
    protected $save_path;

    public function __construct($save_path) {
        $this->save_path = $save_path;
    }

    public function initializeSession(Request $request) {
        $this->session = new Session(
                new NativeSessionStorage(array()
                , new NativeFileSessionHandler($this->save_path)
                )
        );
        $request->setSession($this->session);
    }

    /**
     * @return Session
     */
    public function getSession() {
        return $this->session;
    }

}
