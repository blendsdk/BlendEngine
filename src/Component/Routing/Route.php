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
use Blend\Component\Security\Security;
use Blend\Component\Routing\RouteAttribute;

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

    public function __construct($path, array $defaults = array(), array $requirements = array(), array $options = array(), $host = '', $schemes = array(), $methods = array(), $condition = '') {
        parent::__construct($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
        $this->setDefault('_am', Security::ACCESS_PUBLIC);
        $this->setDefault(RouteAttribute::LOCALE, null);
        /**
         * We make this by default to LOGIN bu the security handle will only
         * act if the access method is not publc
         */
        $this->setDefault('_security_type', Security::SECURITY_TYPE_LOGIN);
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
        $this->setDefault(RouteAttribute::CONTROLLER, [$controller, $action]);
        return $this;
    }

    /**
     * Mark a Route as API route, the responses that are retuned from
     * the controller->action will be converted to a JSON response
     * @return \Blend\Component\Routing\Route
     */
    public function setAPIRoute() {
        $this->setDefault(RouteAttribute::JSON_RESPONSE, true);
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
        return $this->getDefault('_security_type', Security::SECURITY_TYPE_LOGIN);
    }

    /**
     * Mark this Route as publicly accessible
     */
    public function accessPublic() {
        $this->setAccessMethod(Security::ACCESS_PUBLIC);
    }

    /**
     * Mark this Rout only accessible by an authorized user
     */
    public function accessAuthorized() {
        $this->setAccessMethod(Security::ACCESS_AUTHORIZED_USER);
    }

    /**
     * Mark this Route only accessible if the used is not authorized
     */
    public function accessGuestOnly() {
        $this->setAccessMethod(Security::ACCESS_GUEST_ONLY);
    }

}
