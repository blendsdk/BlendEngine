<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Routing;

use Symfony\Component\Routing\Route as RouteBase;
use Blend\Component\Security\SecurityAccessMethod;

/**
 * Route class with Blend specific functions
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Route extends RouteBase {

    const ROLE_PUBLIC = 'ROLE_PUBLIC';
    const ROLE_ADMIN = 'ROLE_';
    const TYPE_WEB_ROUTE = 10;
    const TYPE_API_ROUTE = 20;
    const SECURITY_TYPE_API = 10;
    const SECURITY_TYPE_LOGIN = 20;

    public function __construct($path, array $defaults = array(), array $requirements = array(), array $options = array(), $host = '', $schemes = array(), $methods = array(), $condition = '') {
        parent::__construct($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
        $this->setDefault('_am', SecurityAccessMethod::ACCESS_PUBLIC);
        $this->setDefault('_locale', null);
        /**
         * We make this by default to LOGIN bu the security handle will only
         * act if the access method is not publc
         */
        $this->setDefault('_security_type', self::SECURITY_TYPE_LOGIN);
    }

    public function compile() {
        $key = crc32($this->getPath() . serialize($this->getDefaults()));
        $this->setDefault('_csrf_key', $key);
        return parent::compile();
    }

    /**
     * Sets an Access Method for this route
     * @param type $method
     * @return \Blend\Component\Routing\Route
     */
    public function setAccessMethod($method) {
        $this->setDefault('_am', $method);
        return $this;
    }

    /**
     * Gets the access method for this route
     * @return type
     */
    public function getAccessMethod() {
        return $this->getDefault('_am');
    }

    /**
     * Seth the controller and the action for this route
     * @param type $controller
     * @param type $action
     * @return \Blend\Component\Routing\Route
     */
    public function setControllerAction($controller, $action) {
        $this->setDefault('_controller', [$controller, $action]);
        return $this;
    }

    /**
     * Mark a Route as API route, the responses that are retuned from
     * the controller->action will be converted to a JSON response
     * @return \Blend\Component\Routing\Route
     */
    public function setAPIRoute() {
        $this->setDefault('_json_response', true);
        return $this;
    }

    /**
     * Sets the security type for this route
     * @param type $type
     * @return \Blend\Component\Routing\Route
     */
    public function setSecurityType($type) {
        $this->setDefault('_security_type', $type);
        return $this;
    }

    /**
     * Thes the security type for this route
     * @return type
     */
    public function getSecurityType() {
        return $this->getDefault('_security_type', self::SECURITY_TYPE_LOGIN);
    }

}
