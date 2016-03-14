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

/**
 * Route class with Blend specific functions
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Route extends RouteBase {

    const ACCESS_PUBLIC = 10;
    const ACCESS_AUTHORIZED_USER = 20;
    const ACCESS_GUEST_ONLY = 30;
    const ROLE_PUBLIC = 'ROLE_PUBLIC';
    const ROLE_ADMIN = 'ROLE_PUBLIC';
    const TYPE_WEB_ROUTE = 10;
    const TYPE_API_ROUTE = 20;

    public function __construct($path, array $defaults = array(), array $requirements = array(), array $options = array(), $host = '', $schemes = array(), $methods = array(), $condition = '') {
        parent::__construct($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
        $this->setDefault('_am', self::ACCESS_PUBLIC);
        $this->setDefault('_roles', [self::ROLE_PUBLIC]);
        $this->setDefault('_locale', null);
        $this->setDefault('_route_type', self::TYPE_WEB_ROUTE);
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
     * Sets the roles for this Route
     * @param string/array $roles Cna be a comma seperated string or an array
     * @return \Blend\Component\Routing\Route
     */
    public function setRoles($roles) {
        $r = [];
        if (is_string($roles)) {
            $roles = explode(',', $roles);
        }
        foreach ($roles as $role) {
            $r[] = trim($role);
        }
        $this->setDefault('_roles', $role);
        return $this;
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
     * Mark a Route as API route, This will trigger the Security service to
     * handle the route differently and the responses that are retuned from
     * the controller->action will be converted to a JSON response
     * @return \Blend\Component\Routing\Route
     */
    public function setAPIRoute() {
        $this->setDefault('_route_type', self::TYPE_API_ROUTE);
        $this->setDefault('_json_response', true);
        return $this;
    }

}
